<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogMigrationLog;
use App\Models\BlogTag;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogRecoveryService
{
    public const API_BASE = 'https://sap.zeblearn.com/wp-json/wp/v2';

    public const CATEGORY_ARCHIVE_URL = 'https://sap.zeblearn.com/category/sap-blogs/';

    public const SITEMAP_URL = 'https://sap.zeblearn.com/post-sitemap.xml';

    public function __construct(
        protected BlogSanitizerService $sanitizer
    ) {}

    /**
     * Get aggregate statistics on recovered blogs and migration logs.
     *
     * @return array<string, int>
     */
    public function getStats(): array
    {
        return [
            'total_blogs' => Blog::count(),
            'published_blogs' => Blog::where('status', Blog::STATUS_PUBLISHED)->count(),
            'draft_blogs' => Blog::where('status', Blog::STATUS_DRAFT)->count(),
            'scheduled_blogs' => Blog::where('status', Blog::STATUS_SCHEDULED)->count(),
            'incomplete_blogs' => Blog::where('status', Blog::STATUS_INCOMPLETE)->count(),
            'fully_recovered_blogs' => Blog::where('recovery_status', Blog::RECOVERY_FULLY_RECOVERED)->count(),
            'discovered_logs' => BlogMigrationLog::where('status', 'discovered')->count(),
            'imported_logs' => BlogMigrationLog::where('status', 'imported')->count(),
            'failed_logs' => BlogMigrationLog::where('status', 'failed')->count(),
            'duplicate_logs' => BlogMigrationLog::where('status', 'duplicate')->count(),
        ];
    }

    /**
     * Run the full recovery crawler.
     *
     * @return array{success: bool, imported: int, incomplete: int, duplicates: int, failed: int, total_processed: int, message: string}
     */
    public function runMigration(?int $limit = null, bool $force = false): array
    {
        $batchId = 'batch_'.date('Ymd_His').'_'.Str::random(4);
        $imported = 0;
        $incomplete = 0;
        $duplicates = 0;
        $failed = 0;
        $totalProcessed = 0;

        // Try Source 1: WordPress REST API
        $posts = $this->fetchFromRestApi($limit);

        // Fallback to Source 2: Category Archive HTML Scraping if API returns empty
        if (empty($posts)) {
            Log::info('WordPress REST API unavailable or empty. Falling back to category archive crawler.');
            $posts = $this->fetchFromCategoryArchive($limit);
        }

        // Fallback to Source 3: Yoast Sitemap XML if still empty
        if (empty($posts)) {
            Log::info('Archive crawler returned no posts. Falling back to Yoast Sitemap crawler.');
            $posts = $this->fetchFromSitemap($limit);
        }

        foreach ($posts as $postData) {
            $totalProcessed++;
            $result = $this->processAndStorePost($postData, $batchId, $force);

            if ($result['status'] === 'imported') {
                $imported++;
            } elseif ($result['status'] === 'incomplete') {
                $incomplete++;
            } elseif ($result['status'] === 'duplicate') {
                $duplicates++;
            } elseif ($result['status'] === 'failed') {
                $failed++;
            }

            if ($limit && $totalProcessed >= $limit) {
                break;
            }
        }

        return [
            'success' => true,
            'batch_id' => $batchId,
            'imported' => $imported,
            'incomplete' => $incomplete,
            'duplicates' => $duplicates,
            'failed' => $failed,
            'total_processed' => $totalProcessed,
            'message' => "Migration finished. Processed {$totalProcessed} posts (Imported: {$imported}, Incomplete: {$incomplete}, Duplicates: {$duplicates}, Failed: {$failed}).",
        ];
    }

    /**
     * Fetch posts via WordPress REST API.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchFromRestApi(?int $limit = null): array
    {
        $allPosts = [];
        $page = 1;
        $perPage = 100;

        try {
            while (true) {
                $response = Http::timeout(25)
                    ->withHeaders(['User-Agent' => 'ZebLearnBlogCrawler/1.0'])
                    ->get(self::API_BASE.'/posts', [
                        'categories' => 11,
                        'per_page' => $perPage,
                        'page' => $page,
                        '_embed' => 1,
                    ]);

                if (! $response->successful()) {
                    break;
                }

                $posts = $response->json();
                if (! is_array($posts) || empty($posts)) {
                    break;
                }

                foreach ($posts as $post) {
                    $allPosts[] = $post;
                    if ($limit && count($allPosts) >= $limit) {
                        return $allPosts;
                    }
                }

                $totalPages = (int) $response->header('X-WP-TotalPages', 1);
                if ($page >= $totalPages) {
                    break;
                }

                $page++;
                usleep(50000); // 50ms politeness pause
            }
        } catch (\Throwable $e) {
            Log::error('Error fetching from WordPress REST API: '.$e->getMessage());
        }

        return $allPosts;
    }

    /**
     * Fallback Source 2: Fetch articles from category archive pages.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchFromCategoryArchive(?int $limit = null): array
    {
        $articles = [];
        $page = 1;

        try {
            while ($page <= 24) {
                $url = $page === 1 ? self::CATEGORY_ARCHIVE_URL : self::CATEGORY_ARCHIVE_URL.'page/'.$page.'/';
                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => 'ZebLearnBlogCrawler/1.0'])
                    ->get($url);

                if (! $response->successful()) {
                    break;
                }

                $html = $response->body();
                // Match article links inside the archive
                preg_match_all('/<a\s+[^>]*href=["\'](https:\/\/sap\.zeblearn\.com\/([^"\'\/]+)\/?)["\'][^>]*>(.*?)<\/a>/si', $html, $matches, PREG_SET_ORDER);

                $foundOnPage = 0;
                foreach ($matches as $match) {
                    $fullUrl = $match[1];
                    $slug = trim($match[2]);

                    // Skip category, author, tag, wp-json, and base URLs
                    if (in_array($slug, ['category', 'tag', 'author', 'page', 'wp-json', ''])) {
                        continue;
                    }

                    if (! isset($articles[$slug])) {
                        // Fetch individual article page to get full content
                        $articleData = $this->scrapeIndividualArticle($fullUrl, $slug);
                        if ($articleData) {
                            $articles[$slug] = $articleData;
                            $foundOnPage++;

                            if ($limit && count($articles) >= $limit) {
                                return array_values($articles);
                            }
                        }
                    }
                }

                if ($foundOnPage === 0) {
                    break;
                }

                $page++;
                usleep(100000); // 100ms pause
            }
        } catch (\Throwable $e) {
            Log::error('Error fetching from category archive: '.$e->getMessage());
        }

        return array_values($articles);
    }

    /**
     * Fallback Source 3: Fetch articles from Yoast Post Sitemap.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchFromSitemap(?int $limit = null): array
    {
        $articles = [];

        try {
            $response = Http::timeout(25)
                ->withHeaders(['User-Agent' => 'ZebLearnBlogCrawler/1.0'])
                ->get(self::SITEMAP_URL);

            if (! $response->successful()) {
                return [];
            }

            $xml = simplexml_load_string($response->body());
            if (! $xml || ! isset($xml->url)) {
                return [];
            }

            foreach ($xml->url as $urlElement) {
                $loc = (string) $urlElement->loc;
                $slug = trim(parse_url($loc, PHP_URL_PATH), '/');

                if (empty($slug)) {
                    continue;
                }

                $articleData = $this->scrapeIndividualArticle($loc, $slug);
                if ($articleData) {
                    $articles[] = $articleData;
                    if ($limit && count($articles) >= $limit) {
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error fetching from sitemap: '.$e->getMessage());
        }

        return $articles;
    }

    /**
     * Scrape an individual article page directly if using fallback.
     *
     * @return array<string, mixed>|null
     */
    protected function scrapeIndividualArticle(string $url, string $slug): ?array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'ZebLearnBlogCrawler/1.0'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $html = $response->body();

            // Extract title
            $title = '';
            if (preg_match('/<h1[^>]*class=["\'][^"\']*entry-title[^"\']*["\'][^>]*>(.*?)<\/h1>/si', $html, $m)) {
                $title = trim(strip_tags($m[1]));
            } elseif (preg_match('/<title>(.*?)<\/title>/si', $html, $m)) {
                $title = trim(explode(' - ', explode(' | ', $m[1])[0])[0]);
            }

            // Extract content
            $content = '';
            if (preg_match('/<div[^>]*class=["\'][^"\']*entry-content[^"\']*["\'][^>]*>(.*?)<\/div>\s*<!-- \.entry-content -->/si', $html, $m)) {
                $content = $m[1];
            } elseif (preg_match('/<article[^>]*>(.*?)<\/article>/si', $html, $m)) {
                $content = $m[1];
            }

            // Extract meta description
            $metaDescription = '';
            if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/si', $html, $m)) {
                $metaDescription = html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
            }

            // Extract canonical
            $canonical = $url;
            if (preg_match('/<link[^>]*rel=["\']canonical["\'][^>]*href=["\'](.*?)["\']/si', $html, $m)) {
                $canonical = $m[1];
            }

            return [
                'id' => null,
                'slug' => $slug,
                'link' => $url,
                'title' => ['rendered' => $title ?: Str::headline($slug)],
                'content' => ['rendered' => $content],
                'excerpt' => ['rendered' => Str::limit(strip_tags($content), 200)],
                'date' => now()->toIso8601String(),
                'yoast_head_json' => [
                    'title' => $title,
                    'description' => $metaDescription,
                    'canonical' => $canonical,
                ],
                '_embedded' => [],
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Process and store a post into the database.
     *
     * @param  array<string, mixed>  $post
     * @return array{status: string, message: string}
     */
    public function processAndStorePost(array $post, ?string $batchId = null, bool $force = false): array
    {
        $rawTitle = $post['title']['rendered'] ?? '';
        $title = trim(html_entity_decode(strip_tags($rawTitle), ENT_QUOTES, 'UTF-8'));
        $slug = trim($post['slug'] ?? Str::slug($title));
        $sourceUrl = $post['link'] ?? (self::CATEGORY_ARCHIVE_URL.$slug.'/');
        $postId = $post['id'] ?? null;

        if (empty($title) || empty($slug)) {
            BlogMigrationLog::create([
                'batch_id' => $batchId,
                'source_url' => $sourceUrl,
                'post_id' => $postId,
                'slug' => $slug,
                'title' => $title,
                'status' => 'failed',
                'reason' => 'Missing title or slug',
                'raw_data' => $post,
            ]);

            return ['status' => 'failed', 'message' => 'Missing title or slug'];
        }

        // Duplicate check
        $existing = Blog::where('slug', $slug)
            ->orWhere('legacy_source_url', $sourceUrl)
            ->first();

        if ($existing && ! $force) {
            BlogMigrationLog::create([
                'batch_id' => $batchId,
                'source_url' => $sourceUrl,
                'post_id' => $postId,
                'slug' => $slug,
                'title' => $title,
                'status' => 'duplicate',
                'reason' => "Article already exists (ID: {$existing->id})",
            ]);

            return ['status' => 'duplicate', 'message' => "Duplicate skipped: {$slug}"];
        }

        // Extract content and excerpt
        $rawContent = $post['content']['rendered'] ?? '';
        $cleanContent = $this->sanitizer->sanitize($rawContent);
        $rawExcerpt = $post['excerpt']['rendered'] ?? '';
        $cleanExcerpt = trim(html_entity_decode(strip_tags($rawExcerpt), ENT_QUOTES, 'UTF-8'));

        // Quality rule: if content is empty or only excerpt is available, mark as incomplete
        $isIncomplete = empty($cleanContent) || strlen(strip_tags($cleanContent)) < 120;
        $status = $isIncomplete ? Blog::STATUS_INCOMPLETE : Blog::STATUS_DRAFT;
        $recoveryStatus = $isIncomplete ? Blog::RECOVERY_INCOMPLETE : Blog::RECOVERY_FULLY_RECOVERED;

        // Author extraction
        $authorName = 'ZebLearn Editorial Team';
        if (! empty($post['_embedded']['author'][0]['name'])) {
            $authorName = trim($post['_embedded']['author'][0]['name']);
        }

        // Dates
        $publishedAt = ! empty($post['date']) ? date('Y-m-d H:i:s', strtotime($post['date'])) : now();

        // Featured Image extraction & local download
        $featuredImageUrl = null;
        $featuredImageAlt = null;

        if (! empty($post['_embedded']['wp:featuredmedia'][0]['source_url'])) {
            $featuredImageUrl = $post['_embedded']['wp:featuredmedia'][0]['source_url'];
            $featuredImageAlt = $post['_embedded']['wp:featuredmedia'][0]['alt_text'] ?? $title;
        } elseif (! empty($post['yoast_head_json']['og_image'][0]['url'])) {
            $featuredImageUrl = $post['yoast_head_json']['og_image'][0]['url'];
            $featuredImageAlt = $title;
        }

        $localImagePath = null;
        if ($featuredImageUrl) {
            $localImagePath = $this->downloadAndStoreImage($featuredImageUrl, $slug);
        }

        // Yoast SEO extraction
        $yoast = $post['yoast_head_json'] ?? [];
        $seoTitle = $yoast['title'] ?? null;
        $metaDescription = $yoast['description'] ?? null;
        $canonicalUrl = $yoast['canonical'] ?? $sourceUrl;
        $ogTitle = $yoast['og_title'] ?? $title;
        $ogDescription = $yoast['og_description'] ?? $metaDescription;
        $ogImage = $localImagePath ? asset($localImagePath) : ($featuredImageUrl ?? null);
        $schemaType = 'BlogPosting';

        $blogData = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $cleanExcerpt ?: Str::limit(strip_tags($cleanContent), 160),
            'content' => $cleanContent,
            'featured_image' => $localImagePath,
            'featured_image_alt' => $featuredImageAlt ?: $title,
            'featured_image_original_url' => $featuredImageUrl,
            'author_name' => $authorName,
            'status' => $status,
            'published_at' => $publishedAt,
            'legacy_source_url' => $sourceUrl,
            'legacy_id' => $postId,
            'recovery_status' => $recoveryStatus,
            'recovery_notes' => $isIncomplete ? 'Only excerpt or short content recovered from archive.' : 'Successfully recovered with complete content.',
            'seo_title' => $seoTitle,
            'meta_description' => $metaDescription,
            'canonical_url' => $canonicalUrl,
            'og_title' => $ogTitle,
            'og_description' => $ogDescription,
            'og_image' => $ogImage,
            'robots_index' => true,
            'robots_follow' => true,
            'schema_type' => $schemaType,
        ];

        if ($existing) {
            // Backup snapshot before overwrite for complete audit safety
            BlogMigrationLog::create([
                'batch_id' => $batchId,
                'source_url' => $sourceUrl,
                'post_id' => $postId,
                'slug' => $slug,
                'title' => $existing->title,
                'status' => 'overwrite_backup',
                'reason' => "Pre-overwrite snapshot backup for existing article ID {$existing->id}",
                'raw_data' => $existing->toArray(),
            ]);

            // Safeguard published articles and custom assigned authors from accidental regressions
            if ($existing->status === Blog::STATUS_PUBLISHED) {
                $blogData['status'] = Blog::STATUS_PUBLISHED;
            }
            if ($existing->author_id !== null) {
                unset($blogData['author_name']);
            }

            $existing->update($blogData);
            $blog = $existing;
        } else {
            $blog = Blog::create($blogData);
        }

        // Attach Categories from _embedded terms
        $categoryIds = [];
        if (! empty($post['_embedded']['wp:term'])) {
            foreach ($post['_embedded']['wp:term'] as $taxonomyGroup) {
                foreach ($taxonomyGroup as $term) {
                    if (($term['taxonomy'] ?? '') === 'category') {
                        $catSlug = Str::slug($term['slug'] ?? $term['name']);
                        $catName = html_entity_decode($term['name'], ENT_QUOTES, 'UTF-8');
                        $category = BlogCategory::firstOrCreate(
                            ['slug' => $catSlug],
                            ['name' => $catName, 'is_active' => true]
                        );
                        $categoryIds[] = $category->id;
                    }
                }
            }
        }

        // Ensure default 'SAP Blogs' category if empty
        if (empty($categoryIds)) {
            $defaultCat = BlogCategory::firstOrCreate(
                ['slug' => 'sap-blogs'],
                ['name' => 'SAP Blogs', 'description' => 'Comprehensive SAP guides, tutorials and interview notes.', 'is_active' => true]
            );
            $categoryIds[] = $defaultCat->id;
        }

        $blog->categories()->sync($categoryIds);

        // Attach Tags from _embedded terms
        $tagIds = [];
        if (! empty($post['_embedded']['wp:term'])) {
            foreach ($post['_embedded']['wp:term'] as $taxonomyGroup) {
                foreach ($taxonomyGroup as $term) {
                    if (($term['taxonomy'] ?? '') === 'post_tag') {
                        $tagSlug = Str::slug($term['slug'] ?? $term['name']);
                        $tagName = html_entity_decode($term['name'], ENT_QUOTES, 'UTF-8');
                        $tag = BlogTag::firstOrCreate(
                            ['slug' => $tagSlug],
                            ['name' => $tagName]
                        );
                        $tagIds[] = $tag->id;
                    }
                }
            }
        }
        if (! empty($tagIds)) {
            $blog->tags()->sync($tagIds);
        }

        // Log successful import
        BlogMigrationLog::create([
            'batch_id' => $batchId,
            'source_url' => $sourceUrl,
            'post_id' => $postId,
            'slug' => $slug,
            'title' => $title,
            'status' => $isIncomplete ? 'incomplete' : 'imported',
            'reason' => $isIncomplete ? 'Marked incomplete (content body missing or insufficient)' : 'Successfully recovered and stored in draft status.',
        ]);

        return [
            'status' => $isIncomplete ? 'incomplete' : 'imported',
            'message' => "Recovered: {$title}",
            'blog_id' => $blog->id,
        ];
    }

    /**
     * Download and store an external image locally in storage/app/public/blogs/images/.
     */
    protected function downloadAndStoreImage(string $url, string $slug): ?string
    {
        try {
            $dir = storage_path('app/public/blogs/images');
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }

            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (! in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                $extension = 'webp';
            }

            $filename = Str::slug($slug).'-'.substr(md5($url), 0, 8).'.'.$extension;
            $localFullPath = $dir.'/'.$filename;
            $publicRelativePath = 'storage/blogs/images/'.$filename;

            // If already downloaded, reuse
            if (File::exists($localFullPath) && File::size($localFullPath) > 0) {
                return $publicRelativePath;
            }

            $response = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'ZebLearnImageCrawler/1.0'])
                ->get($url);

            if ($response->successful() && strlen($response->body()) > 100) {
                File::put($localFullPath, $response->body());

                return $publicRelativePath;
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to download image {$url}: ".$e->getMessage());
        }

        return null;
    }
}
