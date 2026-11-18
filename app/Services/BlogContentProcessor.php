<?php

namespace App\Services;

use Illuminate\Support\Str;

class BlogContentProcessor
{
    /**
     * Process blog content (Markdown, HTML, or mixed) for public rendering:
     * - Detects and converts Markdown headings (#, ##, ###), lists, tables, blockquotes, code blocks, bold/italic
     * - Preserves valid semantic HTML without double escaping or corruption
     * - Prevents duplicate article title if content starts with an H1 identical to the blog title
     * - Normalizes all article body H1s to H2 to guarantee a strict single-H1 SEO hierarchy
     * - Injects unique IDs and tabindex into H2 and H3 tags for smooth anchor scrolling
     * - Extracts a hierarchical Table of Contents
     * - Wraps data tables in responsive containers with accessibility attributes
     * - Injects contextual high-relevance internal links
     * - Safely sanitizes output to eliminate XSS while preserving legitimate markup
     * - Calculates accurate reading time based on word count
     *
     * @param  string|null  $content  Raw database article content (Markdown or HTML)
     * @param  array<string, string>  $anchorMap  Map of anchor texts to destination URLs
     * @param  string|null  $blogTitle  Article title for duplicate H1 prevention
     * @return array{
     *     content: string,
     *     toc: array<int, array{id: string, title: string, level: int}>,
     *     reading_time: int,
     *     word_count: int,
     *     headings_count: int
     * }
     */
    public function process(?string $content, array $anchorMap = [], ?string $blogTitle = null): array
    {
        if (empty($content)) {
            return [
                'content' => '',
                'toc' => [],
                'reading_time' => 3,
                'word_count' => 0,
                'headings_count' => 0,
            ];
        }

        // 1. Strip raw script and style blocks completely before Markdown/HTML parsing
        $processed = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
        $processed = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $processed);

        // 2. Prevent duplicate article title if content starts with a matching H1 or Markdown # heading
        $processed = $this->removeDuplicateTitleHeading($processed, $blogTitle);

        // 3. Preprocess paragraph-wrapped markdown (from rich text / WYSIWYG editors)
        $processed = $this->normalizeMarkdownInHtml($processed);

        // 4. Render Markdown into semantic HTML (preserves existing valid HTML tags)
        $html = Str::markdown($processed, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);

        // 5. Demote any remaining <h1> in the article body to <h2> to maintain single-H1 SEO rule
        $html = preg_replace('/<h1(\b[^>]*)>/i', '<h2$1>', $html);
        $html = preg_replace('/<\/h1>/i', '</h2>', $html);

        // 6. Sanitize HTML for security (strip dangerous scripts, styles, event handlers)
        $sanitizer = app(BlogSanitizerService::class);
        $html = $sanitizer->sanitize($html);

        // 7. Process H2 and H3 tags for TOC and Anchor Jump Navigation
        $usedIds = [];
        $toc = [];
        $pattern = '/<h([23])([^>]*)>(.*?)<\/h\1>/is';

        $processedHtml = preg_replace_callback($pattern, function ($matches) use (&$usedIds, &$toc) {
            $level = (int) $matches[1];
            $attrs = $matches[2];
            $innerHtml = $matches[3];
            $plainText = trim(strip_tags(html_entity_decode($innerHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

            // Skip empty headings or standalone generic words like "Answer"
            if (empty($plainText) || in_array(strtolower($plainText), ['answer', 'ans', 'ans:', 'answers', 'explanation'])) {
                return $matches[0];
            }

            // Generate clean URL slug from heading text
            $baseSlug = Str::slug($plainText);
            if (empty($baseSlug)) {
                $baseSlug = 'section-'.($level === 2 ? 'topic' : 'item');
            }

            // Limit slug length for clean URLs
            if (strlen($baseSlug) > 60) {
                $baseSlug = substr($baseSlug, 0, 60);
                $baseSlug = rtrim($baseSlug, '-');
            }

            // Ensure unique ID
            $uniqueId = $baseSlug;
            $counter = 1;
            while (isset($usedIds[$uniqueId])) {
                $uniqueId = $baseSlug.'-'.$counter;
                $counter++;
            }
            $usedIds[$uniqueId] = true;

            $toc[] = [
                'id' => $uniqueId,
                'title' => $plainText,
                'level' => $level,
            ];

            // If an ID already exists in attributes, replace or append
            if (preg_match('/id=[\'"][^\'"]*[\'"]/i', $attrs)) {
                $attrs = preg_replace('/id=[\'"][^\'"]*[\'"]/i', 'id="'.$uniqueId.'" tabindex="-1"', $attrs);
            } else {
                $attrs .= ' id="'.$uniqueId.'" tabindex="-1"';
            }

            return "<h{$level}{$attrs}>{$innerHtml}</h{$level}>";
        }, $html);

        // 7. Wrap tables in responsive, accessible containers (prevent double-wrapping)
        $processedHtml = preg_replace_callback('/(?:<div class="table-wrapper[^>]*>\s*)?<table(.*?)>(.*?)<\/table>(?:\s*<\/div>)?/is', function ($matches) {
            return '<div class="table-wrapper overflow-x-auto my-6 rounded-2xl border border-slate-200/90 shadow-2xs bg-white custom-scrollbar" role="region" aria-label="Article Data Table" tabindex="0"><table'.$matches[1].'>'.$matches[2].'</table></div>';
        }, $processedHtml);

        // 8. Inject Contextual High-Relevance Inline Links (Score >= 85)
        if (! empty($anchorMap)) {
            $processedHtml = app(InternalLinkingService::class)->injectContextualLinks($processedHtml, $anchorMap);
        }

        // 9. Calculate word count and estimated reading time
        $plainTextBody = strip_tags(html_entity_decode($processedHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $wordCount = str_word_count($plainTextBody);
        $readingTime = max(2, (int) ceil($wordCount / 200));

        return [
            'content' => $processedHtml,
            'toc' => $toc,
            'reading_time' => $readingTime,
            'word_count' => $wordCount,
            'headings_count' => count($toc),
        ];
    }

    /**
     * Remove leading H1 or Markdown # heading that duplicates the article title.
     */
    protected function removeDuplicateTitleHeading(string $content, ?string $blogTitle): string
    {
        if (empty($blogTitle)) {
            return $content;
        }

        $normalizedTitle = Str::slug($blogTitle);
        if (empty($normalizedTitle)) {
            return $content;
        }

        // 1. Check for leading Markdown heading: # Title
        if (preg_match('/^\s*(?:<p>\s*)?#\s+([^\r\n<]+)(?:<\/p>)?(\r?\n)*/i', $content, $matches)) {
            $headingText = trim(strip_tags($matches[1]));
            $normalizedHeading = Str::slug($headingText);

            if ($normalizedHeading === $normalizedTitle || Str::contains($normalizedHeading, $normalizedTitle) || Str::contains($normalizedTitle, $normalizedHeading)) {
                $content = substr($content, strlen($matches[0]));
            }
        }

        // 2. Check for leading HTML <h1> Title </h1>
        if (preg_match('/^\s*<h1\b[^>]*>(.*?)<\/h1>(\r?\n)*/is', $content, $matches)) {
            $headingText = trim(strip_tags($matches[1]));
            $normalizedHeading = Str::slug($headingText);

            if ($normalizedHeading === $normalizedTitle || Str::contains($normalizedHeading, $normalizedTitle) || Str::contains($normalizedTitle, $normalizedHeading)) {
                $content = substr($content, strlen($matches[0]));
            }
        }

        return trim($content);
    }

    /**
     * Normalize Markdown syntax that may be encapsulated inside HTML paragraphs by WYSIWYG editors.
     */
    protected function normalizeMarkdownInHtml(string $content): string
    {
        // 1. Unwrap Markdown headings trapped inside <p> tags
        $content = preg_replace('/<p>\s*(#{1,6}\s+.*?)\s*<\/p>/i', "\n\n$1\n\n", $content);

        // 2. Unwrap Markdown lists trapped inside <p> tags
        $content = preg_replace('/<p>\s*([*+-]|\d+\.)\s+(.*?)\s*<\/p>/i', "\n\n$1 $2\n\n", $content);

        // 3. Unwrap Markdown blockquotes trapped inside <p> tags
        $content = preg_replace('/<p>\s*(>.*?)\s*<\/p>/i', "\n\n$1\n\n", $content);

        // 4. Unwrap Markdown code blocks trapped inside <p> tags
        $content = preg_replace('/<p>\s*(```.*?)\s*<\/p>/i', "\n\n$1\n\n", $content);

        // 5. Unwrap Markdown table rows trapped inside <p> tags
        $content = preg_replace('/<p>\s*(\|.*?\|)\s*<\/p>/i', "\n$1\n", $content);

        // 6. Convert <br> or <br/> within markdown lists or tables into clean newlines
        $content = preg_replace('/(\|.*?\|)<br\s*\/?>/i', "$1\n", $content);
        $content = preg_replace('/([*+-]\s+.*?)<br\s*\/?>/i', "$1\n", $content);

        return $content;
    }
}
