<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    /**
     * Generate the XML sitemap string.
     */
    public function generateXml(): string
    {
        return Cache::remember('zeblearn_sitemap_xml', 86400, function () {
            $courses = Course::published()
                ->where(function ($query) {
                    $query->whereNull('robots_directive')
                        ->orWhere('robots_directive', 'not like', '%noindex%');
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            // Static high-priority pages
            $staticPages = [
                ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'daily'],
                ['loc' => route('courses.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
                ['loc' => route('about'), 'priority' => '0.7', 'changefreq' => 'monthly'],
                ['loc' => route('contact'), 'priority' => '0.7', 'changefreq' => 'monthly'],
                ['loc' => route('blogs'), 'priority' => '0.8', 'changefreq' => 'weekly'],
                ['loc' => route('book-demo'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ];

            foreach ($staticPages as $page) {
                $xml .= "  <url>\n";
                $xml .= '    <loc>'.htmlspecialchars($page['loc']).'</loc>'."\n";
                $xml .= '    <lastmod>'.now()->toAtomString().'</lastmod>'."\n";
                $xml .= '    <changefreq>'.$page['changefreq'].'</changefreq>'."\n";
                $xml .= '    <priority>'.$page['priority'].'</priority>'."\n";
                $xml .= "  </url>\n";
            }

            // Dynamic course landing pages
            foreach ($courses as $course) {
                $url = $course->slug === 'sap-fico-training' ? route('courses.fico') : route('courses.show', $course->slug);
                $lastmod = ($course->updated_at ?? $course->published_at ?? now())->toAtomString();

                $xml .= "  <url>\n";
                $xml .= '    <loc>'.htmlspecialchars($url).'</loc>'."\n";
                $xml .= '    <lastmod>'.$lastmod.'</lastmod>'."\n";
                $xml .= "    <changefreq>weekly</changefreq>\n";
                $xml .= "    <priority>0.8</priority>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';

            return $xml;
        });
    }

    /**
     * Clear sitemap cache.
     */
    public function clearCache(): void
    {
        Cache::forget('zeblearn_sitemap_xml');
    }
}
