<?php

namespace App\Services;

class BlogSanitizerService
{
    /**
     * Whitelist of safe HTML tags.
     */
    protected array $allowedTags = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'p', 'br', 'hr',
        'strong', 'b', 'em', 'i', 'u', 's', 'strike',
        'ul', 'ol', 'li',
        'blockquote', 'pre', 'code',
        'a', 'img', 'figure', 'figcaption',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td',
        'div', 'span',
        'iframe', // for embedded videos (safely filtered)
    ];

    /**
     * Sanitize HTML content.
     */
    public function sanitize(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // 1. Strip raw script and style blocks completely with regex first
        $cleaned = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $cleaned = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $cleaned);

        // 2. Strip inline event handlers (onclick, onload, onerror, etc.)
        $cleaned = preg_replace('/(\s+on\w+\s*=\s*(["\'])?[^"\' >]*(\2)?)/i', '', $cleaned);
        $cleaned = preg_replace('/(\s+on\w+\s*=\s*[^ >]+)/i', '', $cleaned);

        // 3. Strip javascript: URLs in href/src safely with matched quotes
        $cleaned = preg_replace('/href\s*=\s*(["\'])?\s*javascript:[^>]*?\1/i', 'href="#"', $cleaned);
        $cleaned = preg_replace('/src\s*=\s*(["\'])?\s*javascript:[^>]*?\1/i', 'src=""', $cleaned);

        // 4. Strip dangerous iframes (only allow YouTube / Vimeo)
        $cleaned = preg_replace_callback('/<iframe(.*?)>(.*?)<\/iframe>/is', function ($matches) {
            $attrs = $matches[1];
            if (preg_match('/src=["\'](https?:\/\/(www\.)?(youtube\.com|youtube-nocookie\.com|player\.vimeo\.com)[^"\']*)["\']/i', $attrs, $srcMatch)) {
                $src = htmlspecialchars($srcMatch[1], ENT_QUOTES, 'UTF-8');

                return '<iframe src="'.$src.'" class="w-full aspect-video rounded-xl my-4 border-0" allowfullscreen loading="lazy"></iframe>';
            }

            return '';
        }, $cleaned);

        // 5. Upgrade insecure http internal asset URLs to https to prevent mixed content
        $cleaned = str_replace(
            ['http://sap.zeblearn.com', 'http://zeblearn.com', 'http://www.zeblearn.com'],
            ['https://sap.zeblearn.com', 'https://zeblearn.com', 'https://www.zeblearn.com'],
            $cleaned
        );

        // 6. Use strip_tags with allowed list
        $tagString = '<'.implode('><', $this->allowedTags).'>';
        $cleaned = strip_tags($cleaned, $tagString);

        return trim($cleaned);
    }
}
