<?php

namespace App\Services;

use Illuminate\Support\Str;

class MarkdownSanitizerService
{
    /**
     * Convert markdown string into safe, sanitized HTML.
     */
    public function toHtml(?string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // 1. Convert Markdown to HTML using CommonMark with strict options
        $html = Str::markdown($markdown, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        // 2. Normalize heading levels: convert any <h1> to <h2> to preserve strict single-H1 SEO structure
        $html = preg_replace('/<h1(\b[^>]*)>/i', '<h2$1>', $html);
        $html = preg_replace('/<\/h1>/i', '</h2>', $html);

        // 3. Remove any residual inline event handlers (on\w+=...)
        $html = preg_replace('/\s*on\w+\s*=\s*(["\']).*?\1/i', '', $html);

        // 4. Ensure links have security attributes (target="_blank" rel="noopener noreferrer")
        $html = preg_replace_callback('/<a\s+([^>]*?)href=(["\'])(https?:\/\/[^"\']+)\2([^>]*)>/i', function ($matches) {
            $before = trim($matches[1]);
            $quote = $matches[2];
            $url = $matches[3];
            $after = trim($matches[4]);

            return '<a '.$before.' href='.$quote.htmlspecialchars($url, ENT_QUOTES, 'UTF-8').$quote.' '.$after.' target="_blank" rel="noopener noreferrer">';
        }, $html);

        // 5. Final whitelist tag filtering
        $allowedTags = '<h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><ul><ol><li><a><blockquote><code><pre><hr><table><thead><tbody><tr><th><td>';
        $sanitized = strip_tags($html, $allowedTags);

        return trim($sanitized);
    }

    /**
     * Extract a clean plain-text excerpt from markdown without heading or list symbols.
     */
    public function extractExcerpt(?string $markdown, int $limit = 280): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Split into paragraphs
        $lines = preg_split('/\r\n|\r|\n/', trim($markdown));
        $cleanParagraphs = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (empty($trimmed)) {
                continue;
            }
            // Skip markdown headings
            if (str_starts_with($trimmed, '#')) {
                continue;
            }
            // Remove markdown bullets / numbers
            $cleanLine = preg_replace('/^[-*+]\s+/', '', $trimmed);
            $cleanLine = preg_replace('/^\d+\.\s+/', '', $cleanLine);
            // Remove bold/italic markers
            $cleanLine = preg_replace('/[*_]{1,3}([^*_]+)[*_]{1,3}/', '$1', $cleanLine);
            // Remove link syntax [text](url) -> text
            $cleanLine = preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $cleanLine);

            $cleanParagraphs[] = $cleanLine;
            if (strlen(implode(' ', $cleanParagraphs)) >= $limit) {
                break;
            }
        }

        $fullText = implode(' ', $cleanParagraphs);

        return Str::limit($fullText, $limit);
    }

    /**
     * Convert course overview/curriculum markdown into rich, theme-styled HTML
     * with branded headings, comfortable typography, and theme-based checkmark bullet items.
     */
    public function toCourseOverviewHtml(?string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // 1. Remove editorial scraper/draft note sections
        $cleaned = preg_replace('/##\s+Source and content status.*$/is', '', $markdown);

        // 2. Clean editorial drafting tags in headings
        $cleaned = str_replace(
            ['Suggested learning roadmap (AI-generated draft)', 'Course plan pricing shown on the source page', '## Course overview'],
            ['Comprehensive Learning Roadmap & Module Scope', 'Flexible Training Tracks & Certification Plans', '## Executive Course Overview & Objectives'],
            $cleaned
        );

        // 3. Convert markdown to sanitized base HTML
        $html = $this->toHtml($cleaned);

        // 4. Transform headings into theme-styled modern headings without raw ## tokens
        $html = preg_replace_callback('/<h([2-6])([^>]*)>(.*?)<\/h\1>/is', function ($matches) {
            $headingText = trim($matches[3]);

            return '<h3 class="font-heading text-lg sm:text-xl font-black uppercase tracking-tight text-[#0A0A0A] mt-8 mb-4 flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221] shrink-0"></span>
                <span>'.$headingText.'</span>
            </h3>';
        }, $html);

        // 5. Transform paragraphs into comfortable reading typography
        $html = preg_replace('/<p(\b[^>]*)>/i', '<p class="font-sans text-sm sm:text-base text-slate-600 leading-relaxed mb-4">', $html);

        // 6. Transform <ul> into styled list container
        $html = preg_replace('/<ul(\b[^>]*)>/i', '<ul class="space-y-3.5 my-5">', $html);

        // 7. Transform <li> into theme-based right tick (checkmark) items
        $tickSvg = '<span class="w-5 h-5 rounded-full bg-orange-50 border border-orange-200/90 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5 shadow-2xs group-hover:bg-[#FB6221] group-hover:text-white transition-all"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></span>';

        $html = preg_replace_callback('/<li(\b[^>]*)>(.*?)<\/li>/is', function ($matches) use ($tickSvg) {
            $content = trim($matches[2]);
            // If the item starts with "Topic Name:", format the topic label with bold text
            if (preg_match('/^([^:]+:\s*)(.*)$/s', $content, $subMatches)) {
                $content = '<strong class="font-bold text-[#0A0A0A] mr-1">'.$subMatches[1].'</strong><span>'.$subMatches[2].'</span>';
            }

            return '<li class="flex items-start gap-3 text-slate-700 font-sans text-sm sm:text-base leading-relaxed py-1 group">'.$tickSvg.'<div class="flex-1">'.$content.'</div></li>';
        }, $html);

        return trim($html);
    }
}
