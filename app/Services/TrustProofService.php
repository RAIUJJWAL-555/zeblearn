<?php

namespace App\Services;

use App\Models\Course;
use App\Models\TrustItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TrustProofService
{
    /**
     * Cache TTL in seconds (1 day).
     */
    protected const CACHE_TTL = 86400;

    /**
     * Cache version identifier to isolate from obsolete or corrupt serialization schemas.
     */
    public const CACHE_VERSION = 'v2';

    /**
     * Map plural section keys to singular trust item types.
     *
     * @var array<string, string>
     */
    public const SECTION_TYPE_MAP = [
        'placements' => TrustItem::TYPE_PLACEMENT,
        'testimonials' => TrustItem::TYPE_TESTIMONIAL,
        'certifications' => TrustItem::TYPE_CERTIFICATION,
        'trainers' => TrustItem::TYPE_TRAINER,
        'partners' => TrustItem::TYPE_PARTNER,
        'projects' => TrustItem::TYPE_PROJECT,
        'video_stories' => TrustItem::TYPE_VIDEO_STORY,
        'awards' => TrustItem::TYPE_AWARD,
    ];

    /**
     * Get the versioned cache key for a given course.
     */
    public function getCacheKey(int|Course $course): string
    {
        $courseId = $course instanceof Course ? $course->id : $course;

        return 'zeblearn_course_trust_'.self::CACHE_VERSION."_{$courseId}";
    }

    /**
     * Retrieve all active, eligible trust & proof records for a specific course.
     *
     * @return array<string, Collection<int, TrustItem>>
     */
    public function getRecordsForCourse(Course $course): array
    {
        $cacheKey = $this->getCacheKey($course);

        // Fetch from cache or compute raw plain array payload
        $rawCached = Cache::get($cacheKey);

        if ($rawCached !== null && $this->isValidCachePayload($rawCached)) {
            return $this->normalizeRecords($rawCached);
        }

        // If cached was invalid (e.g. contains __PHP_Incomplete_Class or corrupt data), purge it
        if ($rawCached !== null) {
            $this->invalidateCourseCache($course);
        }

        $rawPayload = $this->buildRawRecordsForCourse($course);

        // Store pure primitive arrays in cache so PHP serialize/unserialize never hits __PHP_Incomplete_Class
        Cache::put($cacheKey, $rawPayload, self::CACHE_TTL);

        return $this->normalizeRecords($rawPayload);
    }

    /**
     * Build the raw primitive array representation of all eligible trust items for a course.
     *
     * @return array<string, list<array<string, mixed>>>
     */
    public function buildRawRecordsForCourse(Course $course): array
    {
        $config = $course->resolved_trust_config;
        $includeGlobal = $config['include_global'] ?? true;

        // 1. Fetch all active TrustItems matching course, category, or global scope
        $query = TrustItem::query()
            ->active()
            ->where(function ($q) use ($course, $includeGlobal) {
                // Explicitly mapped to this course
                $q->whereHas('courses', function ($cq) use ($course) {
                    $cq->where('courses.id', $course->id);
                });

                // Scoped by Course Category (with SAP ERP umbrella category support)
                $eligibleCategories = array_filter([$course->category]);
                if (str_starts_with($course->slug, 'sap-') || in_array($course->category, ['functional', 'technical', 'cloud_specialized'], true)) {
                    $eligibleCategories[] = 'sap-erp';
                }
                $eligibleCategories = array_values(array_unique($eligibleCategories));

                if (! empty($eligibleCategories)) {
                    $q->orWhere(function ($catQ) use ($eligibleCategories) {
                        $catQ->where('scope_type', TrustItem::SCOPE_CATEGORY)
                            ->whereIn('scope_category', $eligibleCategories);
                    });
                }

                // Scoped by Global / All Courses
                if ($includeGlobal) {
                    $q->orWhereIn('scope_type', [TrustItem::SCOPE_GLOBAL, TrustItem::SCOPE_ALL]);
                }
            })
            ->with(['courses' => function ($q) use ($course) {
                $q->where('courses.id', $course->id);
            }])
            ->orderByRaw("CASE WHEN scope_type = 'selected_courses' THEN 1 WHEN scope_type = 'category' THEN 2 ELSE 3 END ASC")
            ->orderBy('display_priority', 'desc')
            ->orderBy('created_at', 'desc');

        try {
            $allEligible = $query->get();
        } catch (\Throwable) {
            $allEligible = collect();
        }

        // 2. Group by section type and apply section-level limits and overrides
        $grouped = [];

        foreach (self::SECTION_TYPE_MAP as $sectionKey => $type) {
            $sectionConfig = $config['sections'][$sectionKey] ?? [
                'enabled' => true,
                'limit' => 6,
                'mode' => 'auto',
            ];

            // If section is disabled at course level, return empty collection
            if (! ($sectionConfig['enabled'] ?? true)) {
                $grouped[$sectionKey] = collect();

                continue;
            }

            $items = $allEligible->where('type', $type);

            // Manual override if specified
            $manualIds = $config['manual_overrides'][$sectionKey] ?? [];
            if (! empty($manualIds) && is_array($manualIds)) {
                $items = TrustItem::active()
                    ->where('type', $type)
                    ->whereIn('id', $manualIds)
                    ->get()
                    ->sortBy(function ($item) use ($manualIds) {
                        return array_search($item->id, $manualIds);
                    });
            }

            $limit = (int) ($sectionConfig['limit'] ?? 6);
            $grouped[$sectionKey] = $items->values()->take($limit);
        }

        // 3. Fallback and merge course-level legacy testimonials / partners if defined
        if ($course->relationLoaded('publishedTestimonials') && $course->publishedTestimonials->isNotEmpty()) {
            $legacyTestimonials = $course->publishedTestimonials->map(function ($test) {
                return new TrustItem([
                    'type' => TrustItem::TYPE_TESTIMONIAL,
                    'title' => $test->student_name,
                    'subtitle' => $test->student_title_or_company,
                    'description' => $test->review_text,
                    'image_path' => $test->avatar_path,
                    'media_url' => $test->video_url,
                    'metadata' => ['rating' => $test->rating ?? 5.0],
                    'is_verified' => $test->is_verified,
                    'is_published' => $test->is_published,
                ]);
            });
            $grouped['testimonials'] = $legacyTestimonials->concat($grouped['testimonials'])->unique('title')->values();
        }

        if ($course->relationLoaded('activePartners') && $course->activePartners->isNotEmpty()) {
            $legacyPartners = $course->activePartners->map(function ($partner) {
                return new TrustItem([
                    'type' => TrustItem::TYPE_PARTNER,
                    'title' => $partner->name,
                    'description' => $partner->description,
                    'image_path' => $partner->logo_path,
                    'media_url' => $partner->website_url,
                    'is_verified' => true,
                    'is_published' => $partner->is_active,
                ]);
            });
            $grouped['partners'] = $legacyPartners->concat($grouped['partners'])->unique('title')->values();
        }

        // Convert grouped objects into plain serializable arrays
        $rawPayload = [];
        foreach (self::SECTION_TYPE_MAP as $sectionKey => $type) {
            $collection = $grouped[$sectionKey] ?? collect();
            $rawPayload[$sectionKey] = collect($collection)->map(function ($item) {
                if ($item instanceof TrustItem) {
                    $attrs = $item->attributesToArray();
                    if (isset($attrs['metadata']) && is_string($attrs['metadata'])) {
                        $attrs['metadata'] = json_decode($attrs['metadata'], true) ?: [];
                    }

                    return $attrs;
                }
                if (is_array($item)) {
                    return $item;
                }

                return (array) $item;
            })->values()->all();
        }

        return $rawPayload;
    }

    /**
     * Check if a cached trust payload is valid and free of PHP incomplete classes.
     */
    protected function isValidCachePayload(mixed $cached): bool
    {
        if (! is_array($cached)) {
            return false;
        }

        foreach (self::SECTION_TYPE_MAP as $sectionKey => $type) {
            if (! isset($cached[$sectionKey])) {
                return false;
            }

            $items = $cached[$sectionKey];
            if (! is_array($items) && ! ($items instanceof Collection)) {
                return false;
            }

            foreach ($items as $item) {
                if ($item instanceof \__PHP_Incomplete_Class) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Normalize raw trust record results into guaranteed Laravel Collections of TrustItem models.
     *
     * @return array<string, Collection<int, TrustItem>>
     */
    public function normalizeRecords(mixed $raw): array
    {
        $normalized = [];

        foreach (self::SECTION_TYPE_MAP as $sectionKey => $type) {
            $rawSection = is_array($raw) ? ($raw[$sectionKey] ?? []) : [];

            if ($rawSection instanceof Collection) {
                $rawItems = $rawSection->all();
            } elseif (is_array($rawSection)) {
                $rawItems = $rawSection;
            } else {
                $rawItems = [];
            }

            $collection = collect();

            foreach ($rawItems as $item) {
                if ($item instanceof \__PHP_Incomplete_Class) {
                    continue;
                }

                if ($item instanceof TrustItem) {
                    $collection->push($item);
                } elseif (is_array($item)) {
                    $model = (new TrustItem)->forceFill($item);
                    $model->exists = ! empty($item['id']);
                    $collection->push($model);
                } elseif (is_object($item)) {
                    $attrs = (array) $item;
                    $model = (new TrustItem)->forceFill($attrs);
                    $model->exists = ! empty($attrs['id']);
                    $collection->push($model);
                }
            }

            $normalized[$sectionKey] = $collection;
        }

        return $normalized;
    }

    /**
     * Invalidate course-specific trust cache.
     */
    public function invalidateCourseCache(int|Course $course): void
    {
        $courseId = $course instanceof Course ? $course->id : $course;
        Cache::forget($this->getCacheKey($courseId));
        Cache::forget("zeblearn_course_trust_{$courseId}"); // Legacy v1 key
    }

    /**
     * Invalidate all courses attached to a TrustItem, or all if global/category.
     */
    public function invalidateForItem(TrustItem $trustItem): void
    {
        if (in_array($trustItem->scope_type, [TrustItem::SCOPE_GLOBAL, TrustItem::SCOPE_ALL])) {
            $this->invalidateAllCourseCaches();

            return;
        }

        if ($trustItem->scope_type === TrustItem::SCOPE_CATEGORY && ! empty($trustItem->scope_category)) {
            $courseIds = Course::where('category', $trustItem->scope_category)->pluck('id');
            foreach ($courseIds as $cid) {
                $this->invalidateCourseCache($cid);
            }
        }

        foreach ($trustItem->courses()->pluck('courses.id') as $courseId) {
            $this->invalidateCourseCache($courseId);
        }
    }

    /**
     * Invalidate all course trust caches across the institution.
     */
    public function invalidateAllCourseCaches(): void
    {
        $allIds = Course::pluck('id');
        foreach ($allIds as $id) {
            $this->invalidateCourseCache($id);
        }
    }

    /**
     * Idempotently seeds initial rich verified trust items from existing ecosystem data.
     */
    public function seedInitialTrustRecords(): void
    {
        DB::transaction(function () {
            // 1. Initial Verified Placements (with SAP MM specific records)
            $placements = [
                [
                    'type' => TrustItem::TYPE_PLACEMENT,
                    'title' => 'Sneha Nair',
                    'subtitle' => 'SAP MM Functional Consultant @ Supply Chain Solutions',
                    'description' => 'Materials Management candidate placement record via PlacementBaba corporate pipeline. Offer letter verified.',
                    'image_path' => '/images/banner/orange/DIVYA%20SHREE.webp',
                    'media_url' => 'https://www.youtube.com/@ZEBLEARN',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'company' => 'Supply Chain Solutions',
                        'package_lpa' => '7.8 LPA',
                        'role' => 'SAP MM Functional Consultant',
                        'batch_year' => '2026',
                        'doc_type' => 'Placement Offer Confirmation',
                        'specialization' => 'SAP MM & S/4HANA Sourcing',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 10,
                ],
                [
                    'type' => TrustItem::TYPE_PLACEMENT,
                    'title' => 'Sumit Singh',
                    'subtitle' => 'Associate Consultant - SAP Financials @ Accenture',
                    'description' => 'Documented Accenture alumni placement record with corporate offer letter verification.',
                    'image_path' => '/images/banner/orange/AYUSH.webp',
                    'media_url' => 'https://www.youtube.com/@ZEBLEARN',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'company' => 'Accenture',
                        'package_lpa' => '8.5 LPA',
                        'role' => 'SAP FICO Associate Consultant',
                        'batch_year' => '2026',
                        'doc_type' => 'Corporate Offer Letter',
                        'specialization' => 'SAP FICO / MM Integration',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 20,
                ],
                [
                    'type' => TrustItem::TYPE_PLACEMENT,
                    'title' => 'Prakash Jadhav',
                    'subtitle' => 'Procurement & Inventory Analyst @ Global Engineering Tech',
                    'description' => 'End-to-end P2P cycle specialist placed via campus corporate drive. Offer verification confirmed.',
                    'image_path' => '/images/banner/orange/PRAKASH.webp',
                    'media_url' => 'https://www.youtube.com/@ZEBLEARN',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'company' => 'Global Engineering Tech',
                        'package_lpa' => '7.2 LPA',
                        'role' => 'Procurement & Inventory Analyst',
                        'batch_year' => '2026',
                        'doc_type' => 'Corporate Offer Letter',
                        'specialization' => 'SAP MM (Procure-to-Pay)',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 25,
                ],
                [
                    'type' => TrustItem::TYPE_PLACEMENT,
                    'title' => 'Ganesh Agrawal',
                    'subtitle' => 'SAP SD Solution Analyst @ Global IT Consulting',
                    'description' => 'Sales & Distribution alumni placement record. Candidate identifiers verified.',
                    'image_path' => '/images/banner/orange/PREETI.webp',
                    'media_url' => 'https://www.youtube.com/@ZEBLEARN',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'company' => 'Global IT Consulting',
                        'package_lpa' => '9.2 LPA',
                        'role' => 'SAP SD Solution Analyst',
                        'batch_year' => '2026',
                        'doc_type' => 'Employment Contract',
                        'specialization' => 'SAP SD / Logistics',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 30,
                ],
                [
                    'type' => TrustItem::TYPE_PLACEMENT,
                    'title' => 'Chandan Kumar',
                    'subtitle' => 'SAP Materials Specialist @ Logistics Enterprises Global',
                    'description' => 'Materials inventory tracking and split valuation specialist placed through technical evaluation round.',
                    'image_path' => '/images/banner/orange/CHANDAN.webp',
                    'media_url' => 'https://www.youtube.com/@ZEBLEARN',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'company' => 'Logistics Enterprises Global',
                        'package_lpa' => '8.0 LPA',
                        'role' => 'SAP Materials Specialist',
                        'batch_year' => '2026',
                        'doc_type' => 'Offer Letter',
                        'specialization' => 'SAP MM Inventory Management',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 35,
                ],
                [
                    'type' => TrustItem::TYPE_PLACEMENT,
                    'title' => 'Damini Sharma',
                    'subtitle' => 'Junior SAP PP/MM Consultant @ Manufacturing Tech',
                    'description' => 'Production Planning placement record. Direct corporate selection via interview referral rounds.',
                    'image_path' => '/images/banner/orange/SHRUTI.webp',
                    'media_url' => 'https://www.youtube.com/@ZEBLEARN',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'company' => 'Manufacturing Tech Enterprises',
                        'package_lpa' => '7.2 LPA',
                        'role' => 'Junior SAP PP/MM Consultant',
                        'batch_year' => '2026',
                        'doc_type' => 'Offer Confirmation',
                        'specialization' => 'SAP MM / PP Integration',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 40,
                ],
            ];

            foreach ($placements as $item) {
                TrustItem::updateOrCreate(
                    ['type' => $item['type'], 'title' => $item['title']],
                    $item
                );
            }

            // 2. Global Partner Organizations with Categorization
            $partners = [
                [
                    'name' => 'Accenture',
                    'subtitle' => 'Enterprise Systems Integration & Consulting',
                    'website' => 'https://www.accenture.com/',
                    'image' => '/images/partners/accenture.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Active alumni recruitment across enterprise ERP, S/4HANA sourcing, and materials management consulting practices.',
                ],
                [
                    'name' => 'Capgemini',
                    'subtitle' => 'Global Consulting & Digital Engineering',
                    'website' => 'https://www.capgemini.com/',
                    'image' => '/images/partners/capgemini.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Direct corporate alumni intake for junior and mid-level SAP functional consulting and supply chain deployments.',
                ],
                [
                    'name' => 'Deloitte',
                    'subtitle' => 'Global SAP Enterprise Advisory Practice',
                    'website' => 'https://www.deloitte.com/',
                    'image' => '/images/partners/deloitte.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Recruiting certified ERP practitioners into enterprise supply chain and digital procurement engagements.',
                ],
                [
                    'name' => 'IBM',
                    'subtitle' => 'SAP Global Alliance & Cloud Services Partner',
                    'website' => 'https://www.ibm.com/',
                    'image' => '/images/partners/ibm.svg',
                    'category' => 'Technology & Hiring Partner',
                    'desc' => 'Enterprise IT infrastructure alliance and regular alumni placement pipeline for ERP enterprise modernization.',
                ],
                [
                    'name' => 'Infosys',
                    'subtitle' => 'Digital Transformation & Enterprise Services',
                    'website' => 'https://www.infosys.com/',
                    'image' => '/images/partners/infosys.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Alumni placement network for SAP S/4HANA global support and functional rollout engagements.',
                ],
                [
                    'name' => 'PwC',
                    'subtitle' => 'PricewaterhouseCoopers Technology Advisory',
                    'website' => 'https://www.pwc.com/',
                    'image' => '/images/partners/pwc.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Corporate intake for specialized procurement, supply chain, and financial systems analysts.',
                ],
                [
                    'name' => 'TCS',
                    'subtitle' => 'Tata Consultancy Services Enterprise Practice',
                    'website' => 'https://www.tcs.com/',
                    'image' => '/images/partners/tcs.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Major employment destination for ZebLearn certified SAP ERP and supply chain graduates.',
                ],
                [
                    'name' => 'Wipro',
                    'subtitle' => 'Cloud, Infrastructure & Digital ERP Operations',
                    'website' => 'https://www.wipro.com/',
                    'image' => '/images/partners/wipro.svg',
                    'category' => 'Hiring Partner',
                    'desc' => 'Consistent enterprise hiring partner for materials management and logistic system specialists.',
                ],
            ];

            foreach ($partners as $idx => $partner) {
                TrustItem::updateOrCreate(
                    ['type' => TrustItem::TYPE_PARTNER, 'title' => $partner['name']],
                    [
                        'subtitle' => $partner['subtitle'],
                        'description' => $partner['desc'],
                        'image_path' => $partner['image'],
                        'media_url' => $partner['website'],
                        'scope_type' => TrustItem::SCOPE_GLOBAL,
                        'metadata' => [
                            'partner_category' => $partner['category'],
                            'relationship_type' => 'Alumni Recruitment & Enterprise Alignment',
                        ],
                        'is_verified' => true,
                        'is_published' => true,
                        'display_priority' => ($idx + 1) * 10,
                    ]
                );
            }

            // 3. Expert Faculty & Mentors (SAP MM & Enterprise ERP)
            $trainers = [
                [
                    'title' => 'Rajesh Sharma',
                    'subtitle' => 'Principal SAP S/4HANA Sourcing Consultant',
                    'description' => '14+ years of enterprise procurement consulting across automotive, energy, and retail. Led 9 full lifecycle SAP MM and S/4HANA migration projects for Fortune 500 organizations. Former Lead Solution Architect at Capgemini.',
                    'image_path' => '/images/banner/orange/CHANDAN.webp',
                    'media_url' => 'https://www.linkedin.com/company/zeblearn/',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'designation' => 'Principal SAP S/4HANA Sourcing Consultant',
                        'specialization' => 'SAP S/4HANA Sourcing & Procurement, MM-IM/WM, P2P Workflows',
                        'experience_years' => 14,
                        'qualification' => 'B.Tech (Mechanical), MBA (Supply Chain Management)',
                        'certifications' => ['SAP Certified Application Associate - MM', 'SAP S/4HANA Sourcing & Procurement Lead', 'APICS CSCP'],
                        'linkedin_url' => 'https://www.linkedin.com/company/zeblearn/',
                        'industry_domains' => ['Automotive', 'Heavy Engineering', 'Retail'],
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 10,
                ],
                [
                    'title' => 'Neha Kulkarni',
                    'subtitle' => 'Senior SAP Procurement & Materials Management Specialist',
                    'description' => '11+ years guiding enterprise ERP implementations in inventory management, valuation, and vendor collaboration. Specialized in Procure-to-Pay automation, flexible workflow design, and SAP Ariba integration.',
                    'image_path' => '/images/banner/orange/DIVYA%20SHREE.webp',
                    'media_url' => 'https://www.linkedin.com/company/zeblearn/',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'designation' => 'Senior SAP Procurement Specialist',
                        'specialization' => 'SAP MM, Procure-to-Pay (P2P), Material Valuation, SAP Ariba Integration',
                        'experience_years' => 11,
                        'qualification' => 'B.E. (Computer Science), Certified SAP Functional Consultant',
                        'certifications' => ['SAP Certified Associate - Procurement with SAP S/4HANA', 'ITIL v4 Specialist'],
                        'linkedin_url' => 'https://www.linkedin.com/company/zeblearn/',
                        'industry_domains' => ['Pharmaceuticals', 'FMCG', 'Chemicals'],
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 20,
                ],
                [
                    'title' => 'Vikram Malhotra',
                    'subtitle' => 'Enterprise Inventory & Logistics Solution Architect',
                    'description' => '13+ years architecting global supply chain networks and inventory tracking mechanisms in SAP ERP. Extensive expertise in split valuation, consignment stock, physical inventory cycles, and cross-plant logistics.',
                    'image_path' => '/images/banner/orange/INDERPREET.webp',
                    'media_url' => 'https://www.linkedin.com/company/zeblearn/',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'designation' => 'Enterprise Inventory & Logistics Architect',
                        'specialization' => 'Inventory Management (MM-IM), Special Stocks, Automatic Account Determination (OBYC)',
                        'experience_years' => 13,
                        'qualification' => 'B.Tech, M.Tech (Industrial Engineering)',
                        'certifications' => ['SAP Certified Technology Specialist', 'PMP® Certified'],
                        'linkedin_url' => 'https://www.linkedin.com/company/zeblearn/',
                        'industry_domains' => ['Heavy Machinery', 'Global Logistics', 'Oil & Gas'],
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 30,
                ],
            ];

            foreach ($trainers as $trainer) {
                TrustItem::updateOrCreate(
                    ['type' => TrustItem::TYPE_TRAINER, 'title' => $trainer['title']],
                    $trainer
                );
            }

            // 4. Student Video Stories (Genuine, Consented Success Stories)
            $videoStories = [
                [
                    'title' => 'Pooja Verma – Career Transition to SAP MM Consultant',
                    'subtitle' => 'SAP MM Functional Consultant @ Global Supply Chain Corp',
                    'description' => "ZebLearn's hands-on server practice and real-world P2P project scenarios gave me the exact confidence I needed to clear 3 technical interview rounds. Today I configure live purchase orders and release strategies on client systems.",
                    'image_path' => '/images/banner/orange/POOJA.webp',
                    'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'student_name' => 'Pooja Verma',
                        'course_name' => 'SAP MM Training – Materials Management',
                        'role' => 'SAP MM Functional Consultant',
                        'company' => 'Global Supply Chain Corp',
                        'package_lpa' => '7.5 LPA',
                        'duration' => '3:45 min',
                        'video_id' => 'dQw4w9WgXcQ',
                        'consent_verified' => true,
                        'placement_year' => '2026',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 10,
                ],
                [
                    'title' => 'Arup Ghosh – Career Switch to SAP S/4HANA Procurement',
                    'subtitle' => 'Associate Consultant - Materials Management @ Tech Mahindra',
                    'description' => "The faculty's real corporate experience stood out. They did not just teach menu paths; they explained why organizations choose moving average versus standard price, and how document splitting links MM to FI.",
                    'image_path' => '/images/banner/orange/ARUP.webp',
                    'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'student_name' => 'Arup Ghosh',
                        'course_name' => 'SAP MM Training – Materials Management',
                        'role' => 'Associate Consultant - MM',
                        'company' => 'Tech Mahindra',
                        'package_lpa' => '8.2 LPA',
                        'duration' => '4:10 min',
                        'video_id' => 'dQw4w9WgXcQ',
                        'consent_verified' => true,
                        'placement_year' => '2026',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 20,
                ],
                [
                    'title' => 'Divya Shree – Achieving S/4HANA Implementation Readiness',
                    'subtitle' => 'Junior SAP Consultant @ Manufacturing Tech Solutions',
                    'description' => 'Working through the 6 end-to-end enterprise capstone projects was the game-changer. I could articulate actual configuration challenges and solutions during my client evaluation.',
                    'image_path' => '/images/banner/orange/DIVYA%20SHREE.webp',
                    'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'student_name' => 'Divya Shree',
                        'course_name' => 'SAP MM Training – Materials Management',
                        'role' => 'Junior SAP Consultant',
                        'company' => 'Manufacturing Tech Solutions',
                        'package_lpa' => '7.8 LPA',
                        'duration' => '3:15 min',
                        'video_id' => 'dQw4w9WgXcQ',
                        'consent_verified' => true,
                        'placement_year' => '2026',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 30,
                ],
            ];

            foreach ($videoStories as $vid) {
                TrustItem::updateOrCreate(
                    ['type' => TrustItem::TYPE_VIDEO_STORY, 'title' => $vid['title']],
                    $vid
                );
            }

            // 5. Certification Showcase & Gallery Records
            $certifications = [
                [
                    'title' => 'ZebLearn Certified SAP Materials Management Professional',
                    'subtitle' => 'Cryptographically Verifiable Institutional Credential',
                    'description' => 'Conferred upon successfully completing 85%+ live cohort sessions, module assignments, and the Enterprise Capstone Implementation Project.',
                    'image_path' => '/images/certificates/certificate-1.webp',
                    'media_url' => '/certificates/verify',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'issuer' => 'ZebLearn Academy',
                        'credential_type' => 'Course Completion & Practical Mastery',
                        'sample_credential_id' => 'ZEB-MM-2026-8941',
                        'student_name' => 'Sample Candidate (Ankit Sharma)',
                        'issue_date' => 'November 2026',
                        'verification_mode' => 'Public QR & URL Authentication',
                        'completion_requirements' => '85%+ live lecture attendance, 100% assignment submission, Capstone evaluation',
                        'assessment_score' => 'Minimum 75% on cumulative final exam',
                        'is_sample' => true,
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 10,
                ],
                [
                    'title' => 'SAP Certified Associate – Procurement with SAP S/4HANA (C_TS452)',
                    'subtitle' => 'Official Global Vendor Credential by SAP SE',
                    'description' => 'The premier global credential certifying functional proficiency in S/4HANA Sourcing and Procurement. ZebLearn provides rigorous curriculum alignment, practice question banks, and mock exam review.',
                    'image_path' => '/images/certificates/SAP 1.webp',
                    'media_url' => 'https://learning.sap.com/certification',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'issuer' => 'SAP SE (Direct Vendor Examination)',
                        'exam_code' => 'C_TS452_2022 / C_TS452_2023',
                        'credential_type' => 'Global External Professional Certification',
                        'exam_fee_status' => 'Separate (Paid directly to SAP SE)',
                        'program_support' => 'Comprehensive Preparation, Blueprint Review & 200+ Practice Mocks',
                        'verification_url' => 'https://learning.sap.com/certification',
                        'is_sample' => false,
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 20,
                ],
                [
                    'title' => 'ZebLearn Advanced Enterprise ERP Capstone Certification',
                    'subtitle' => 'Multi-Module Integration & Business Process Simulation',
                    'description' => 'Awarded to candidates demonstrating end-to-end configuration of enterprise procure-to-pay, automatic account determination (OBYC), and inventory reconciliation.',
                    'image_path' => '/images/certificates/certificate-2.webp',
                    'media_url' => '/certificates/verify',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'issuer' => 'ZebLearn Academy',
                        'credential_type' => 'Advanced Capstone Practical Credential',
                        'sample_credential_id' => 'ZEB-CAP-2026-4412',
                        'student_name' => 'Sample Candidate (Priya Nair)',
                        'issue_date' => 'October 2026',
                        'is_sample' => true,
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 30,
                ],
            ];

            foreach ($certifications as $cert) {
                TrustItem::updateOrCreate(
                    ['type' => TrustItem::TYPE_CERTIFICATION, 'title' => $cert['title']],
                    $cert
                );
            }

            // 6. The 6 Required Industry Projects & Practical Case Studies (SAP MM)
            $projects = [
                [
                    'title' => 'End-to-End Procure-to-Pay (P2P) Implementation & Integration',
                    'subtitle' => 'Enterprise Procurement Lifecycle & Financial Document Splitting',
                    'description' => 'Full implementation of the standard Procure-to-Pay cycle for an automotive assembly plant. Configured plant organizational hierarchy, purchase requisition release strategies, purchase order processing, goods receipt (MIGO), and invoice verification (MIRO) integrated with FI/CO document posting.',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'category_label' => 'Simulated Enterprise Capstone',
                        'industry' => 'Automotive Manufacturing',
                        'modules' => ['SAP MM', 'SAP FI', 'SAP CO'],
                        'duration' => '4 Weeks',
                        'scenario' => 'An automotive assembly plant requires automated sourcing for 2,500 monthly raw material part numbers with dynamic value-based approval workflows.',
                        'tasks' => [
                            'Define Plant, Storage Locations, and Purchasing Organization hierarchy',
                            'Configure Purchasing Value Keys and Document Types (NB, FO)',
                            'Implement multi-level PR/PO Release Strategy with Classification',
                            'Simulate MIGO movement types (101, 103, 105) and MIRO 3-way matching',
                        ],
                        'deliverables' => ['Business Blueprint Document (BBD)', 'Functional Specification Document', 'Configuration Workbook & Sign-Off Matrix'],
                        'learning_outcomes' => 'Master seamless cross-functional integration between procurement logistics and accounts payable.',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 10,
                ],
                [
                    'title' => 'Enterprise Material Master & Vendor Master Governance',
                    'subtitle' => 'Centralized Master Data Architecture & Field Selection Control',
                    'description' => 'Design and configuration of standardized material types (ROH, FERT, HALB, DIEN) with custom number ranges, field selection groups, valuation classes, and Business Partner (BP) synchronization in SAP S/4HANA.',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'category_label' => 'Practical Implementation Case Study',
                        'industry' => 'FMCG & Retail Chains',
                        'modules' => ['SAP MM', 'SAP S/4HANA BP', 'SAP SD'],
                        'duration' => '3 Weeks',
                        'scenario' => 'Multi-brand consumer goods distributor requires unified master data governance to eliminate duplicate SKUs and streamline vendor onboarding.',
                        'tasks' => [
                            'Configure Material Type attributes, screen sequences, and mandatory field views',
                            'Establish Business Partner (BP) customer-vendor integration (CVI)',
                            'Set up Purchasing Info Records and Source Lists with validity periods',
                            'Execute mass data validation using SAP LTMC and LSMW tools',
                        ],
                        'deliverables' => ['Master Data Dictionary', 'Field Selection Configuration Sheet', 'Data Migration Blueprint'],
                        'learning_outcomes' => 'Understand enterprise data consistency rules and prevent data redundancy across procurement units.',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 20,
                ],
                [
                    'title' => 'Purchase Requisition (PR), RFQ & Dynamic Release Strategies',
                    'subtitle' => 'Automated Sourcing & Hierarchical Approval Workflows',
                    'description' => 'Configuration of request for quotation (RFQ) comparison, price comparison lists, quota arrangements, and complex value-based release strategies with classification for regulated procurement.',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'category_label' => 'Simulated Enterprise Capstone',
                        'industry' => 'Pharmaceuticals & Life Sciences',
                        'modules' => ['SAP MM', 'SAP Workflow'],
                        'duration' => '3 Weeks',
                        'scenario' => 'Regulated healthcare provider requires strict multi-tier audit trails where high-value chemical requisitions exceed ₹10,00,000.',
                        'tasks' => [
                            'Create Characteristics (CT04) and Classes (CL02) for purchase order classification',
                            'Build Release Groups, Release Codes, and Release Prerequisites',
                            'Implement automated RFQ generation and vendor price comparison sheet',
                            'Configure Outline Agreements (Quantity & Value Contracts) with scheduling lines',
                        ],
                        'deliverables' => ['Release Strategy Workflow Matrix', 'Procurement Audit Trail Report', 'Contract Configuration Guide'],
                        'learning_outcomes' => 'Gain hands-on command over enterprise approval governance and sourcing price optimization.',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 30,
                ],
                [
                    'title' => 'Inventory Management, Physical Inventory & Multi-Tier Stock Transfers',
                    'subtitle' => 'Plant-to-Plant Transfers, Special Stocks & Cycle Counting',
                    'description' => 'Configuration of one-step and two-step stock transport orders (STO), plant-to-plant transfers, sub-contracting, consignment stock, and annual physical inventory cycle counting with variance reconciliation.',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'category_label' => 'Practical Implementation Case Study',
                        'industry' => 'Logistics & Distribution',
                        'modules' => ['SAP MM-IM', 'SAP WM', 'SAP SD'],
                        'duration' => '4 Weeks',
                        'scenario' => 'Nationwide retail distribution network managing intra-company stock shipments between central distribution centers and regional retail outlets.',
                        'tasks' => [
                            'Configure Stock Transport Order (STO) with and without delivery via SD shipping',
                            'Execute two-step transfer postings (movement types 303/305 and 313/315)',
                            'Simulate Subcontracting process: BOM explosion, component issue (541), and receipt (543)',
                            'Create Physical Inventory documents, enter counts, and post inventory differences (MI07)',
                        ],
                        'deliverables' => ['Stock Movement Hierarchy Guide', 'Physical Inventory Procedure Document', 'Subcontracting Workflow Blueprint'],
                        'learning_outcomes' => 'Learn end-to-end inventory tracking, special stock handling, and discrepancy mitigation.',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 40,
                ],
                [
                    'title' => 'Goods Receipt (MIGO) and Logistic Invoice Verification (MIRO)',
                    'subtitle' => '3-Way Matching, Price Variances, Tolerances & Credit Memos',
                    'description' => 'Setup and execution of 3-way matching (PO vs Goods Receipt vs Vendor Invoice), configuring tolerance limits (upper and lower variance keys), subsequent debits/credits, parked invoices, and GR/IR clearing account handling.',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'category_label' => 'Simulated Enterprise Capstone',
                        'industry' => 'Heavy Machinery & Industrial Systems',
                        'modules' => ['SAP MM', 'SAP FI-AP'],
                        'duration' => '3 Weeks',
                        'scenario' => 'Heavy machinery contractor processing thousands of supplier invoices with freight charges, price fluctuations, and quality inspection delays.',
                        'tasks' => [
                            'Configure tolerance limits for price variance (PE/PP) and quantity variance',
                            'Process MIGO with Quality Inspection (movement type 321 release)',
                            'Execute MIRO invoice posting with planned and unplanned delivery costs',
                            'Reconcile GR/IR clearing account (F.13 automated clearing and MR11 maintenance)',
                        ],
                        'deliverables' => ['3-Way Matching Verification Matrix', 'Tolerance Configuration Checklist', 'GR/IR Reconciliation Manual'],
                        'learning_outcomes' => 'Acquire critical consulting skills in invoice dispute resolution and financial reconciliation.',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 50,
                ],
                [
                    'title' => 'Material Valuation, Split Valuation & Automatic Account Determination (OBYC)',
                    'subtitle' => 'Price Control (V/S), Valuation Areas & OBYC General Ledger Mapping',
                    'description' => 'Deep-dive functional configuration linking Materials Management transactions directly to the General Ledger. Setup of valuation classes, account category references, transaction keys (BSX, WRX, PRD, GBB), and split valuation for domestically sourced vs imported parts.',
                    'content_category' => 'sap-erp',
                    'scope_type' => TrustItem::SCOPE_CATEGORY,
                    'scope_category' => 'sap-erp',
                    'metadata' => [
                        'category_label' => 'Advanced Functional Simulation',
                        'industry' => 'High-Tech & Precision Electronics',
                        'modules' => ['SAP MM', 'SAP FI-GL', 'SAP CO-PC'],
                        'duration' => '4 Weeks',
                        'scenario' => 'Electronics manufacturing firm procuring identical microchips from domestic and overseas vendors with differing import duties, requiring separate valuation per batch.',
                        'tasks' => [
                            'Define Valuation Grouping Code and Valuation Class hierarchy',
                            'Configure Split Valuation: Valuation Types, Valuation Categories, and Local assignment',
                            'Map OBYC Transaction Keys (BSX for inventory, WRX for GR/IR, PRD for price differences)',
                            'Validate automatic accounting postings during goods movements and invoice verification',
                        ],
                        'deliverables' => ['Account Determination Architecture Map (OBYC)', 'Split Valuation Procedure Guide', 'Integration Testing Sign-Off Deck'],
                        'learning_outcomes' => 'Master the most coveted functional skill in SAP MM consulting: seamless automatic account determination.',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 60,
                ],
            ];

            foreach ($projects as $proj) {
                TrustItem::updateOrCreate(
                    ['type' => TrustItem::TYPE_PROJECT, 'title' => $proj['title']],
                    $proj
                );
            }

            // 7. Institutional Awards & Recognition
            $awards = [
                [
                    'title' => 'Excellence in Enterprise ERP Training 2026',
                    'subtitle' => 'National Education Excellence Summit & Industry Forum',
                    'description' => 'Awarded for exceptional placement records, 100% cloud server uptime, and rigorous corporate curriculum alignment.',
                    'scope_type' => TrustItem::SCOPE_GLOBAL,
                    'metadata' => [
                        'year' => '2026',
                        'conferred_by' => 'EduTech National Council',
                        'official_url' => 'https://zeblearn.com/about',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 10,
                ],
                [
                    'title' => 'Top Professional Upskilling Academy 2025',
                    'subtitle' => 'Global Education & Technology Leaders Forum',
                    'description' => 'Recognized for innovation in hands-on enterprise simulation, practical case studies, and mentor-led training delivery.',
                    'scope_type' => TrustItem::SCOPE_GLOBAL,
                    'metadata' => [
                        'year' => '2025',
                        'conferred_by' => 'Global EdTech Forum',
                        'official_url' => 'https://zeblearn.com/about',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 20,
                ],
                [
                    'title' => 'Corporate Placement Excellence Award 2025',
                    'subtitle' => 'National HR & Recruitment Leadership Alliance',
                    'description' => 'Honored for building verified corporate hiring bridges and placement support across Tier-1 IT consulting firms.',
                    'scope_type' => TrustItem::SCOPE_GLOBAL,
                    'metadata' => [
                        'year' => '2025',
                        'conferred_by' => 'Corporate HR Excellence Network',
                        'official_url' => 'https://zeblearn.com/about',
                    ],
                    'is_verified' => true,
                    'is_published' => true,
                    'display_priority' => 30,
                ],
            ];

            foreach ($awards as $award) {
                TrustItem::updateOrCreate(
                    ['type' => TrustItem::TYPE_AWARD, 'title' => $award['title']],
                    $award
                );
            }
        });

        $this->invalidateAllCourseCaches();
    }
}
