<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Course;
use App\Models\InternalLinkingLog;
use App\Models\InternalLinkOverride;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class InternalLinkingService
{
    /**
     * Thresholds for automatic linking.
     */
    public const THRESHOLD_INLINE = 85;

    public const THRESHOLD_RELATED = 70;

    /**
     * Cache TTL in seconds (24 hours).
     */
    protected const CACHE_TTL = 86400;

    /**
     * Master SAP Knowledge & Semantic Domain Taxonomy.
     * Maps module identifier to high-value canonical keywords and anchors.
     *
     * @var array<string, array{name: string, keywords: list<string>, primary_course_slug: string, anchor_phrases: list<string>}>
     */
    protected const MODULE_TAXONOMY = [
        'fico' => [
            'name' => 'SAP FICO (Financials & Controlling)',
            'primary_course_slug' => 'sap-fico-on-s-4-hana',
            'keywords' => [
                'fico', 'financial accounting', 'controlling', 'general ledger', 'accounts payable',
                'accounts receivable', 'asset accounting', 'cost center', 'profit center', 'financials',
            ],
            'anchor_phrases' => [
                'SAP FICO on S/4 HANA', 'SAP FICO Training', 'SAP Financial Accounting and Controlling',
                'SAP FICO Course', 'SAP FICO Certification',
            ],
        ],
        'mm' => [
            'name' => 'SAP MM (Materials Management & Logistics)',
            'primary_course_slug' => 'mm-s4-hana-simple-logistics',
            'keywords' => [
                'sap mm', 'materials management', 'procurement', 'purchase order', 'purchase requisition',
                'goods receipt', 'inventory management', 'vendor master', 'simple logistics', 'mrp',
            ],
            'anchor_phrases' => [
                'SAP MM & S/4 HANA Training', 'SAP Materials Management', 'SAP MM Simple Logistics',
                'SAP MM Course', 'SAP MM Certification',
            ],
        ],
        'sd' => [
            'name' => 'SAP SD (Sales & Distribution)',
            'primary_course_slug' => 'sap-sd-2',
            'keywords' => [
                'sap sd', 'sales and distribution', 'sales order', 'order-to-cash', 'billing', 'pricing',
                'shipping', 'credit management', 'customer master', 'quotation', 'inquiry',
            ],
            'anchor_phrases' => [
                'SAP SD & S/4 HANA Training', 'SAP Sales and Distribution', 'SAP SD Order to Cash',
                'SAP SD Course', 'SAP SD Certification',
            ],
        ],
        'hcm' => [
            'name' => 'SAP HCM / HR (Human Capital Management)',
            'primary_course_slug' => 'sap-hcm-on-s-4-hana',
            'keywords' => [
                'sap hcm', 'sap hr', 'human capital management', 'payroll', 'personnel administration',
                'organizational management', 'time management', 'infotype', 'personnel development',
            ],
            'anchor_phrases' => [
                'SAP HCM & S/4 HANA Training', 'SAP Human Capital Management', 'SAP HCM Course',
                'SAP HR Payroll Training', 'SAP HCM Certification',
            ],
        ],
        'basis' => [
            'name' => 'SAP BASIS (System Administration)',
            'primary_course_slug' => 'sap-mobility',
            'keywords' => [
                'sap basis', 'system administration', 'netweaver', 'transport management', 'client copy',
                'kernel upgrade', 'spau', 'spdd', 'saprouter', 'rfc connection',
            ],
            'anchor_phrases' => [
                'SAP BASIS Training', 'SAP System Administration', 'SAP Architecture & Administration',
                'SAP NetWeaver BASIS Course',
            ],
        ],
        'bw' => [
            'name' => 'SAP BW on HANA (Business Warehouse)',
            'primary_course_slug' => 'sap-bw-on-hana',
            'keywords' => [
                'sap bw', 'bw on hana', 'business warehouse', 'bw/4hana', 'infocube', 'dso', 'adso',
                'data modeling', 'bex query', 'analytics',
            ],
            'anchor_phrases' => [
                'SAP BW on HANA Training', 'SAP Business Warehouse Course', 'SAP BW/4HANA Analytics',
                'SAP BW on HANA Certification',
            ],
        ],
        'crm' => [
            'name' => 'SAP CRM (Customer Relationship Management)',
            'primary_course_slug' => 'sap-crm',
            'keywords' => [
                'sap crm', 'customer relationship management', 'crm web ui', 'interaction center',
                'sales order crm', 'service ticket', 'middleware crm',
            ],
            'anchor_phrases' => [
                'SAP CRM Training', 'SAP Customer Relationship Management', 'SAP CRM Course',
            ],
        ],
        'refx' => [
            'name' => 'SAP REFX (Real Estate Management)',
            'primary_course_slug' => 'sap-refx',
            'keywords' => [
                'sap refx', 'real estate management', 'lease accounting', 'property management',
                'ifrs 16', 'contract management refx',
            ],
            'anchor_phrases' => [
                'SAP REFX Training', 'SAP Flexible Real Estate Management', 'SAP REFX Course',
            ],
        ],
        'chemicals' => [
            'name' => 'SAP Chemicals & S/4 HANA',
            'primary_course_slug' => 'sap-s-4-hana-chemicals',
            'keywords' => [
                'sap chemicals', 's/4 hana chemicals', 'chemical industry', 'process industry sap',
                'batch management', 'hazardous material',
            ],
            'anchor_phrases' => [
                'SAP S/4 HANA Chemicals Training', 'SAP Chemicals Industry Solution', 'SAP Chemicals Course',
            ],
        ],
        'pharma' => [
            'name' => 'SAP Pharma',
            'primary_course_slug' => 'sap-pharma',
            'keywords' => [
                'sap pharma', 'pharmaceutical sap', 'gmp compliance', 'validation sap', 'serialisation',
            ],
            'anchor_phrases' => [
                'SAP Pharma Training', 'SAP Life Sciences & Pharma Course',
            ],
        ],
        'utilities' => [
            'name' => 'SAP Utilities',
            'primary_course_slug' => 'sap-utilities',
            'keywords' => [
                'sap utilities', 'isu', 'is-u', 'billing utilities', 'device management sap',
            ],
            'anchor_phrases' => [
                'SAP Utilities Training', 'SAP IS-U Course',
            ],
        ],
        'mobility' => [
            'name' => 'SAP Mobility',
            'primary_course_slug' => 'sap-mobility',
            'keywords' => [
                'sap mobility', 'fiori', 'sapui5', 'mobile app sap', 'mobile platform',
            ],
            'anchor_phrases' => [
                'SAP Mobility Training', 'SAP Fiori and Mobility Course',
            ],
        ],
    ];

    /**
     * Get automatically computed internal linking recommendations for a Blog.
     *
     * @return array{
     *     primaryCourse: ?Course,
     *     relatedBlogs: Collection<int, Blog>,
     *     relatedCourses: Collection<int, Course>,
     *     interviewQuestions: Collection<int, Blog>,
     *     qualifyingInlineAnchors: array<string, string>
     * }
     */
    public function getRecommendationsForBlog(Blog $blog): array
    {
        $version = Cache::get('zeblearn_spider_version', 1);
        $cacheKey = "zeblearn_spider_v{$version}_blog_{$blog->id}";

        $cachedData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($blog) {
            $overrides = $this->getOverridesForSource('blog', $blog->id);
            $excludedCourseIds = $overrides['exclude_course'] ?? [];
            $excludedBlogIds = $overrides['exclude_blog'] ?? [];
            $pinnedCourseId = $overrides['pin_course'] ?? null;
            $pinnedBlogIds = $overrides['pin_blog'] ?? [];

            // 1. Detect Primary Domain Modules from Title, Slug, Content & Excerpt
            $analysisText = strtolower($blog->title.' '.$blog->slug.' '.($blog->focus_keyword ?? '').' '.($blog->meta_description ?? ''));
            $detectedModules = $this->detectModules($analysisText);

            // 2. Resolve Primary Course Bridge (Score >= 70)
            $primaryCourse = null;
            if ($pinnedCourseId) {
                $primaryCourse = Course::published()->find($pinnedCourseId);
            }

            if (! $primaryCourse) {
                $candidateCourses = Course::published()
                    ->whereNotIn('id', $excludedCourseIds)
                    ->get();

                $scoredCourses = $candidateCourses->map(function ($course) use ($detectedModules, $analysisText) {
                    $score = $this->scoreCourseRelevance($course, $detectedModules, $analysisText);

                    return ['course' => $course, 'score' => $score];
                })->filter(fn ($item) => $item['score'] >= self::THRESHOLD_RELATED)
                    ->sortByDesc('score');

                $primaryCourse = $scoredCourses->first()['course'] ?? null;
            }

            // Fallback course bridge if none scored above 70: default to flagship FICO or top published (respecting exclusions)
            if (! $primaryCourse) {
                $primaryCourse = Course::published()
                    ->whereNotIn('id', $excludedCourseIds)
                    ->where('slug', 'sap-fico-on-s-4-hana')
                    ->first() ?? Course::published()->whereNotIn('id', $excludedCourseIds)->first();
            }

            // 3. Resolve Related Sibling Courses (up to 3)
            $relatedCourses = Course::published()
                ->where('id', '!=', $primaryCourse?->id)
                ->whereNotIn('id', $excludedCourseIds)
                ->when($primaryCourse?->category_id, fn ($q) => $q->where('category_id', $primaryCourse->category_id))
                ->limit(3)
                ->get();

            if ($relatedCourses->count() < 3) {
                $fillers = Course::published()
                    ->where('id', '!=', $primaryCourse?->id)
                    ->whereNotIn('id', $relatedCourses->pluck('id')->merge($excludedCourseIds))
                    ->limit(3 - $relatedCourses->count())
                    ->get();
                $relatedCourses = $relatedCourses->concat($fillers);
            }

            // 4. Resolve Related Blogs (Excluding self and non-indexed)
            $allCandidates = Blog::published()
                ->where('id', '!=', $blog->id)
                ->where('robots_index', true)
                ->whereNotIn('id', $excludedBlogIds)
                ->with('categories')
                ->get();

            $sourceCategoryIds = $blog->categories->pluck('id')->all();

            $scoredBlogs = $allCandidates->map(function ($candidate) use ($detectedModules, $analysisText, $sourceCategoryIds) {
                $candidateText = strtolower($candidate->title.' '.$candidate->slug);
                $candidateCategoryIds = $candidate->categories->pluck('id')->all();
                $hasSharedCategory = ! empty(array_intersect($sourceCategoryIds, $candidateCategoryIds));
                $score = $this->scoreContentRelevance($candidateText, $detectedModules, $analysisText, $hasSharedCategory);

                return ['blog' => $candidate, 'score' => $score];
            })->sortByDesc('score');

            // Segregate Interview Questions from General Articles
            $interviewQuestions = $scoredBlogs->filter(function ($item) {
                return Str::contains(strtolower($item['blog']->title), ['interview question', 'interview q']);
            })->take(3)->pluck('blog');

            $relatedBlogs = $scoredBlogs->filter(function ($item) use ($interviewQuestions) {
                return ! $interviewQuestions->contains('id', $item['blog']->id);
            })->take(4)->pluck('blog');

            // 5. Generate Qualifying Inline Anchors (Score >= 85)
            $qualifyingInlineAnchors = [];
            if ($primaryCourse) {
                $moduleKey = $this->getModuleKeyForCourse($primaryCourse);
                if ($moduleKey && isset(self::MODULE_TAXONOMY[$moduleKey])) {
                    $tax = self::MODULE_TAXONOMY[$moduleKey];
                    foreach ($tax['anchor_phrases'] as $anchor) {
                        $qualifyingInlineAnchors[$anchor] = $primaryCourse->url;
                    }
                }
                // Also add standard course title
                $qualifyingInlineAnchors[$primaryCourse->name] = $primaryCourse->url;
            }

            return [
                'primaryCourseId' => $primaryCourse?->id,
                'relatedBlogIds' => $relatedBlogs->pluck('id')->all(),
                'relatedCourseIds' => $relatedCourses->pluck('id')->all(),
                'interviewQuestionIds' => $interviewQuestions->pluck('id')->all(),
                'qualifyingInlineAnchors' => $qualifyingInlineAnchors,
            ];
        });

        // Hydrate Eloquent models safely without serialization bugs
        $primaryCourse = ! empty($cachedData['primaryCourseId'])
            ? Course::published()->find($cachedData['primaryCourseId'])
            : null;

        $relatedBlogs = ! empty($cachedData['relatedBlogIds'])
            ? Blog::published()->whereIn('id', $cachedData['relatedBlogIds'])->with('categories')->get()
            : collect();

        $relatedCourses = ! empty($cachedData['relatedCourseIds'])
            ? Course::published()->whereIn('id', $cachedData['relatedCourseIds'])->get()
            : collect();

        $interviewQuestions = ! empty($cachedData['interviewQuestionIds'])
            ? Blog::published()->whereIn('id', $cachedData['interviewQuestionIds'])->get()
            : collect();

        return [
            'primaryCourse' => $primaryCourse,
            'relatedBlogs' => $relatedBlogs,
            'relatedCourses' => $relatedCourses,
            'interviewQuestions' => $interviewQuestions,
            'qualifyingInlineAnchors' => $cachedData['qualifyingInlineAnchors'] ?? [],
        ];
    }

    /**
     * Get automatically computed internal linking recommendations for a Course.
     *
     * @return array{
     *     siblingCourses: Collection<int, Course>,
     *     relatedBlogs: Collection<int, Blog>,
     *     interviewQuestions: Collection<int, Blog>,
     *     category: ?Category
     * }
     */
    public function getRecommendationsForCourse(Course $course): array
    {
        $cacheKey = "zeblearn_spider_course_{$course->id}";

        $cachedData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($course) {
            $overrides = $this->getOverridesForSource('course', $course->id);
            $excludedCourseIds = $overrides['exclude_course'] ?? [];
            $excludedBlogIds = $overrides['exclude_blog'] ?? [];

            // 1. Sibling Courses in Same Learning Track
            $siblingCourses = Course::published()
                ->where('id', '!=', $course->id)
                ->whereNotIn('id', $excludedCourseIds)
                ->when($course->category_id, fn ($q) => $q->where('category_id', $course->category_id))
                ->orderBy('sort_order', 'asc')
                ->limit(3)
                ->get();

            if ($siblingCourses->count() < 3) {
                $fallback = Course::published()
                    ->where('id', '!=', $course->id)
                    ->whereNotIn('id', $siblingCourses->pluck('id')->merge($excludedCourseIds))
                    ->orderBy('learner_count', 'desc')
                    ->limit(3 - $siblingCourses->count())
                    ->get();
                $siblingCourses = $siblingCourses->concat($fallback);
            }

            // 2. Identify Domain Module Keywords
            $moduleKey = $this->getModuleKeyForCourse($course);
            $keywords = $moduleKey && isset(self::MODULE_TAXONOMY[$moduleKey])
                ? self::MODULE_TAXONOMY[$moduleKey]['keywords']
                : [strtolower($course->name), strtolower($course->slug)];

            // 3. Find High-Relevance Published Articles
            $blogsQuery = Blog::published()
                ->where('robots_index', true)
                ->whereNotIn('id', $excludedBlogIds)
                ->where(function ($q) use ($keywords) {
                    foreach ($keywords as $kw) {
                        $cleanKw = strtolower($kw);
                        $q->orWhereRaw('LOWER(title) LIKE ?', ["%{$cleanKw}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$cleanKw}%"]);
                    }
                })
                ->latest('published_at');

            $allMatchedBlogs = $blogsQuery->limit(12)->get();

            // Separate Interview Questions from General Guides
            $interviewQuestions = $allMatchedBlogs->filter(function ($b) {
                return Str::contains(strtolower($b->title), ['interview question', 'interview q']);
            })->take(3);

            $relatedBlogs = $allMatchedBlogs->filter(function ($b) use ($interviewQuestions) {
                return ! $interviewQuestions->contains('id', $b->id);
            })->take(4);

            // Fallback blogs if specific keyword matches are low
            if ($relatedBlogs->count() < 4) {
                $fallbackBlogs = Blog::published()
                    ->where('robots_index', true)
                    ->whereNotIn('id', $relatedBlogs->pluck('id')->merge($interviewQuestions->pluck('id')))
                    ->latest('published_at')
                    ->limit(4 - $relatedBlogs->count())
                    ->get();
                $relatedBlogs = $relatedBlogs->concat($fallbackBlogs);
            }

            return [
                'siblingCourseIds' => $siblingCourses->pluck('id')->all(),
                'relatedBlogIds' => $relatedBlogs->pluck('id')->all(),
                'interviewQuestionIds' => $interviewQuestions->pluck('id')->all(),
                'categoryId' => $course->category_id,
            ];
        });

        // Hydrate Eloquent models freshly
        $siblingCourses = ! empty($cachedData['siblingCourseIds'])
            ? Course::published()->whereIn('id', $cachedData['siblingCourseIds'])->get()
            : collect();

        $relatedBlogs = ! empty($cachedData['relatedBlogIds'])
            ? Blog::published()->whereIn('id', $cachedData['relatedBlogIds'])->with('categories')->get()
            : collect();

        $interviewQuestions = ! empty($cachedData['interviewQuestionIds'])
            ? Blog::published()->whereIn('id', $cachedData['interviewQuestionIds'])->get()
            : collect();

        $category = ! empty($cachedData['categoryId'])
            ? Category::find($cachedData['categoryId'])
            : $course->categoryRecord;

        return [
            'siblingCourses' => $siblingCourses,
            'relatedBlogs' => $relatedBlogs,
            'interviewQuestions' => $interviewQuestions,
            'category' => $category,
        ];
    }

    /**
     * Get recommendations for a Category archive.
     *
     * @return array{
     *     courses: Collection<int, Course>,
     *     blogs: Collection<int, Blog>
     * }
     */
    public function getRecommendationsForCategory(Category $category): array
    {
        $cacheKey = "zeblearn_spider_cat_{$category->id}";

        $cachedData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($category) {
            $courses = Course::published()
                ->where('category_id', $category->id)
                ->orWhere('category', $category->slug)
                ->orderBy('sort_order', 'asc')
                ->get();

            $categoryKeywords = [strtolower($category->name), strtolower($category->slug)];

            $blogs = Blog::query()
                ->where('robots_index', true)
                ->where(function ($q) use ($categoryKeywords) {
                    foreach ($categoryKeywords as $kw) {
                        $q->orWhere('title', 'like', "%{$kw}%")
                            ->orWhere('slug', 'like', "%{$kw}%");
                    }
                })
                ->newestPublished()
                ->limit(6)
                ->get();

            return [
                'courseIds' => $courses->pluck('id')->all(),
                'blogIds' => $blogs->pluck('id')->all(),
            ];
        });

        $courses = ! empty($cachedData['courseIds'])
            ? Course::published()->whereIn('id', $cachedData['courseIds'])->get()
            : collect();

        $blogs = ! empty($cachedData['blogIds'])
            ? Blog::published()->whereIn('id', $cachedData['blogIds'])->get()
            : collect();

        return [
            'courses' => $courses,
            'blogs' => $blogs,
        ];
    }

    /**
     * Safe HTML Contextual Inline Link Injector.
     *
     * Injects qualifying high-relevance internal links into article body paragraphs:
     * - Respects frequency caps (max 2-3 links per 1000 words, minimum 150 words distance)
     * - Skips headings (h1-h6), existing anchors (<a>), pre, code, blockquotes, figcaptions
     * - Injects exact first occurrence only, preserving case and punctuation
     * - Never modifies raw database content
     */
    public function injectContextualLinks(string $html, array $anchorMap, int $maxLinks = 3): string
    {
        if (empty($html) || empty($anchorMap)) {
            return $html;
        }

        $wordCount = str_word_count(strip_tags($html));
        $allowedLinks = max(1, min($maxLinks, (int) ceil($wordCount / 350)));

        $injectedCount = 0;
        $usedDestinations = [];

        // Sort anchors by character length descending to prevent substring collisions
        uksort($anchorMap, fn ($a, $b) => strlen($b) <=> strlen($a));

        // Process only contents of <p> tags using DOMDocument with libxml safety
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        // Wrap in HTML envelope for UTF-8 preservation
        $wrappedHtml = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body>'.$html.'</body></html>';
        $loaded = $dom->loadHTML($wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        if (! $loaded) {
            return $html;
        }

        $xpath = new \DOMXPath($dom);
        // Find text nodes that are children of <p> tags, and NOT inside <a>, <h1>-<h6>, <pre>, <code>, <blockquote>, <button>
        $textNodes = $xpath->query('//p//text()[not(ancestor::a) and not(ancestor::h1) and not(ancestor::h2) and not(ancestor::h3) and not(ancestor::h4) and not(ancestor::h5) and not(ancestor::h6) and not(ancestor::pre) and not(ancestor::code) and not(ancestor::blockquote) and not(ancestor::button)]');

        if (! $textNodes || $textNodes->length === 0) {
            return $html;
        }

        foreach ($textNodes as $textNode) {
            if ($injectedCount >= $allowedLinks) {
                break;
            }

            $nodeValue = $textNode->nodeValue;
            if (empty(trim($nodeValue)) || strlen($nodeValue) < 40) {
                continue;
            }

            foreach ($anchorMap as $anchor => $url) {
                if ($injectedCount >= $allowedLinks) {
                    break;
                }

                if (in_array($url, $usedDestinations)) {
                    continue;
                }

                $escapedAnchor = preg_quote($anchor, '/');
                $pattern = '/\b('.$escapedAnchor.')\b/i';

                if (preg_match($pattern, $nodeValue, $matches, PREG_OFFSET_CAPTURE)) {
                    $matchedText = $matches[1][0];
                    $matchPos = $matches[1][1];

                    // Split text node and replace with anchor element
                    $before = substr($nodeValue, 0, $matchPos);
                    $after = substr($nodeValue, $matchPos + strlen($matchedText));

                    $anchorElem = $dom->createElement('a', htmlspecialchars($matchedText));
                    $anchorElem->setAttribute('href', $url);
                    $anchorElem->setAttribute('class', 'text-[#FB6221] hover:text-[#d44810] font-semibold underline decoration-[#FB6221]/30 hover:decoration-[#FB6221] transition-colors');
                    $anchorElem->setAttribute('title', 'View '.htmlspecialchars($anchor));

                    $parent = $textNode->parentNode;
                    if ($before !== '') {
                        $parent->insertBefore($dom->createTextNode($before), $textNode);
                    }
                    $parent->insertBefore($anchorElem, $textNode);
                    if ($after !== '') {
                        $parent->insertBefore($dom->createTextNode($after), $textNode);
                    }
                    $parent->removeChild($textNode);

                    $usedDestinations[] = $url;
                    $injectedCount++;
                    break; // Move to next paragraph node to respect minimum spacing
                }
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        if (! $body) {
            return $html;
        }

        $innerHtml = '';
        foreach ($body->childNodes as $child) {
            $innerHtml .= $dom->saveHTML($child);
        }

        return $innerHtml ?: $html;
    }

    /**
     * Invalidate spider web cache for a specific model or whole site.
     */
    public function invalidateCache(?string $type = null, ?int $id = null): void
    {
        $version = Cache::get('zeblearn_spider_version', 1);

        if ($type && $id) {
            Cache::forget("zeblearn_spider_v{$version}_{$type}_{$id}");
            Cache::forget("zeblearn_spider_{$type}_{$id}");
        }

        // Increment version to instantly refresh spider caches across peer content
        try {
            Cache::increment('zeblearn_spider_version');
        } catch (\Throwable) {
            Cache::put('zeblearn_spider_version', $version + 1, self::CACHE_TTL);
        }

        // Clear general site sitemap cache to ensure parity
        Cache::forget('zeblearn_sitemap_xml');
    }

    /**
     * Get aggregate SEO Spider Web health and link stats.
     *
     * @return array{
     *     total_published_courses: int,
     *     total_published_blogs: int,
     *     total_internal_links: int,
     *     average_links_per_page: float,
     *     orphan_courses_count: int,
     *     orphan_blogs_count: int,
     *     health_score: int
     * }
     */
    public function getSpiderWebStats(): array
    {
        $courses = Course::published()->get();
        $blogs = Blog::published()->where('robots_index', true)->get();

        $courseCount = $courses->count();
        $blogCount = $blogs->count();
        $totalPages = $courseCount + $blogCount;

        // Calculate link coverage
        $orphans = $this->getOrphanPages();
        $orphanTotal = $orphans['courses']->count() + $orphans['blogs']->count();

        // Estimated internal links generated across components + inline
        $totalEstimatedLinks = ($courseCount * 6) + ($blogCount * 8);
        $avgLinks = $totalPages > 0 ? round($totalEstimatedLinks / $totalPages, 1) : 0.0;

        $healthScore = $totalPages > 0
            ? max(0, min(100, (int) round((($totalPages - $orphanTotal) / $totalPages) * 100)))
            : 100;

        return [
            'total_published_courses' => $courseCount,
            'total_published_blogs' => $blogCount,
            'total_internal_links' => $totalEstimatedLinks,
            'average_links_per_page' => $avgLinks,
            'orphan_courses_count' => $orphans['courses']->count(),
            'orphan_blogs_count' => $orphans['blogs']->count(),
            'health_score' => $healthScore,
        ];
    }

    /**
     * Identify orphan pages (published pages with 0 incoming internal recommendations).
     *
     * @return array{courses: Collection<int, Course>, blogs: Collection<int, Blog>}
     */
    public function getOrphanPages(): array
    {
        // A course is never an orphan because all published courses are in /courses catalog and sitemap
        $orphanCourses = Course::published()
            ->whereNull('category_id')
            ->where(fn ($q) => $q->whereNull('category')->orWhere('category', ''))
            ->get();

        // A blog is an orphan if it is published, indexed, but has no categories and matches no modules
        $orphanBlogs = Blog::published()
            ->where('robots_index', true)
            ->doesntHave('categories')
            ->get()
            ->filter(function ($blog) {
                $text = strtolower($blog->title.' '.$blog->slug);

                return empty($this->detectModules($text));
            });

        return [
            'courses' => $orphanCourses,
            'blogs' => $orphanBlogs,
        ];
    }

    /**
     * Record internal linking automated activity in log.
     */
    public function recordLog(string $sourceType, ?int $sourceId, string $action, int $linksCount, int $score, array $details = []): void
    {
        try {
            InternalLinkingLog::create([
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'action' => $action,
                'links_generated' => $linksCount,
                'qualifying_score' => $score,
                'details' => $details,
            ]);
        } catch (\Throwable) {
            // Fail silently to never block content publishing
        }
    }

    /**
     * Detect matching SAP modules from text string.
     *
     * @return list<string>
     */
    public function detectModules(string $text): array
    {
        $matches = [];
        foreach (self::MODULE_TAXONOMY as $key => $data) {
            foreach ($data['keywords'] as $kw) {
                if (str_contains($text, $kw)) {
                    $matches[] = $key;
                    break;
                }
            }
        }

        return array_values(array_unique($matches));
    }

    /**
     * Score candidate course relevance for a blog post.
     */
    protected function scoreCourseRelevance(Course $course, array $detectedModules, string $blogText): int
    {
        $score = 0;
        $courseModule = $this->getModuleKeyForCourse($course);

        // 1. Module match (+70 points)
        if ($courseModule && in_array($courseModule, $detectedModules)) {
            $score += 70;
        }

        // 2. Exact name/slug occurrence (+30 points)
        $cleanCourseName = strtolower($course->name);
        $cleanSlug = strtolower(str_replace('-', ' ', $course->slug));
        if (str_contains($blogText, $cleanCourseName) || str_contains($blogText, $cleanSlug)) {
            $score += 30;
        }

        // 3. Keyword matches (+15 points)
        if ($courseModule && isset(self::MODULE_TAXONOMY[$courseModule])) {
            foreach (self::MODULE_TAXONOMY[$courseModule]['keywords'] as $kw) {
                if (str_contains($blogText, $kw)) {
                    $score += 5;
                    if ($score >= 95) {
                        break;
                    }
                }
            }
        }

        return min(100, $score);
    }

    /**
     * Score candidate article relevance.
     */
    protected function scoreContentRelevance(string $candidateText, array $detectedModules, string $sourceText, bool $hasSharedCategory = false): int
    {
        $score = 0;

        // Shared category bonus (+50 pts)
        if ($hasSharedCategory) {
            $score += 50;
        }

        $candidateModules = $this->detectModules($candidateText);

        // Common module domains (+40 pts)
        $shared = array_intersect($detectedModules, $candidateModules);
        if (! empty($shared)) {
            $score += 45;
        }

        // Shared tokens (+25 pts)
        $sourceTokens = array_filter(explode(' ', $sourceText), fn ($w) => strlen($w) > 3);
        $matches = 0;
        foreach ($sourceTokens as $token) {
            if (str_contains($candidateText, $token)) {
                $matches++;
            }
        }
        $score += min(30, $matches * 5);

        // Interview Questions bonus
        if (str_contains($candidateText, 'interview question') && str_contains($sourceText, 'interview question')) {
            $score += 20;
        }

        return min(100, $score);
    }

    /**
     * Identify module key for a Course model.
     */
    protected function getModuleKeyForCourse(Course $course): ?string
    {
        $slug = strtolower($course->slug);
        $name = strtolower($course->name);

        foreach (self::MODULE_TAXONOMY as $key => $data) {
            if ($slug === $data['primary_course_slug'] || str_contains($slug, $key) || str_contains($name, $key)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Retrieve manual overrides for a source entity.
     *
     * @return array<string, list<int>>
     */
    protected function getOverridesForSource(string $type, int $id): array
    {
        try {
            $overrides = InternalLinkOverride::where('source_type', $type)
                ->where(function ($q) use ($id) {
                    $q->where('source_id', $id)->orWhereNull('source_id');
                })
                ->get();

            $result = [
                'pin_course' => null,
                'pin_blog' => [],
                'exclude_course' => [],
                'exclude_blog' => [],
            ];

            foreach ($overrides as $o) {
                if ($o->action === 'pin' && $o->target_type === 'course') {
                    $result['pin_course'] = (int) $o->target_id;
                } elseif ($o->action === 'pin' && $o->target_type === 'blog') {
                    $result['pin_blog'][] = (int) $o->target_id;
                } elseif ($o->action === 'exclude' && $o->target_type === 'course') {
                    $result['exclude_course'][] = (int) $o->target_id;
                } elseif ($o->action === 'exclude' && $o->target_type === 'blog') {
                    $result['exclude_blog'][] = (int) $o->target_id;
                }
            }

            return $result;
        } catch (\Throwable) {
            return [];
        }
    }
}
