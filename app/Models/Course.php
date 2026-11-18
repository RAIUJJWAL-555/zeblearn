<?php

namespace App\Models;

use App\Services\MarkdownSanitizerService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('search_default_top_courses'));
        static::deleted(fn () => Cache::forget('search_default_top_courses'));
    }

    /**
     * The all supported academy categories taxonomy.
     *
     * @var array<string, array{name: string, title: string, subtitle: string, badge: string, rating: string, reviews_count: string}>
     */
    public const CATEGORIES = [
        'sap-erp' => [
            'name' => 'SAP ERP',
            'title' => 'SAP Course Certification Training Program',
            'subtitle' => 'Comprehensive SAP training modules for finance, HR, logistics and more.',
            'badge' => 'SAP COURSE',
            'rating' => '4.8',
            'reviews_count' => '85,000+',
        ],
        'workday' => [
            'name' => 'Workday',
            'title' => 'Workday Enterprise Cloud Certification Training Program',
            'subtitle' => 'End-to-end Workday training for HCM, financials, integrations and reporting.',
            'badge' => 'WORKDAY COURSE',
            'rating' => '4.9',
            'reviews_count' => '32,000+',
        ],
        'salesforce' => [
            'name' => 'Salesforce',
            'title' => 'Salesforce Certification & Developer Training Program',
            'subtitle' => 'Master Salesforce Admin, Developer, CPQ, and Lightning Web Components.',
            'badge' => 'SALESFORCE COURSE',
            'rating' => '4.9',
            'reviews_count' => '54,000+',
        ],
        'hr-management' => [
            'name' => 'HR Management',
            'title' => 'HR Management & Analytics Professional Program',
            'subtitle' => 'Strategic Human Resource Management, payroll compliance and HR analytics.',
            'badge' => 'HR COURSE',
            'rating' => '4.8',
            'reviews_count' => '28,000+',
        ],
        'foreign-languages' => [
            'name' => 'Foreign Languages',
            'title' => 'Foreign Language Proficiency Certification Program',
            'subtitle' => 'Professional language courses in German, French, Japanese, and Spanish.',
            'badge' => 'LANGUAGE COURSE',
            'rating' => '4.9',
            'reviews_count' => '19,000+',
        ],
        'data-science-analytics' => [
            'name' => 'Data Science & Analytics',
            'title' => 'Data Science, AI & Business Analytics Program',
            'subtitle' => 'Master Python, Machine Learning, Power BI, SQL, and predictive analytics.',
            'badge' => 'DATA COURSE',
            'rating' => '4.9',
            'reviews_count' => '67,000+',
        ],
        'cloud-computing' => [
            'name' => 'Cloud Computing',
            'title' => 'Cloud Architecture & DevOps Certification Program',
            'subtitle' => 'Hands-on enterprise cloud training in AWS, Microsoft Azure, and Google Cloud.',
            'badge' => 'CLOUD COURSE',
            'rating' => '4.8',
            'reviews_count' => '48,000+',
        ],
        'cyber-security' => [
            'name' => 'Cyber Security',
            'title' => 'Cyber Security & Ethical Hacking Training Program',
            'subtitle' => 'Information security, network defence, penetration testing and SOC operations.',
            'badge' => 'SECURITY COURSE',
            'rating' => '4.9',
            'reviews_count' => '38,000+',
        ],
        'digital-marketing' => [
            'name' => 'Digital Marketing',
            'title' => 'Full-Stack Digital Marketing & Growth Program',
            'subtitle' => 'Performance marketing, advanced SEO, social media advertising and analytics.',
            'badge' => 'MARKETING COURSE',
            'rating' => '4.8',
            'reviews_count' => '42,000+',
        ],
        'project-management' => [
            'name' => 'Project Management',
            'title' => 'Project Management & Agile Scrum Master Program',
            'subtitle' => 'PMP aligned frameworks, Agile ceremonies, Jira workflows and leadership.',
            'badge' => 'MANAGEMENT COURSE',
            'rating' => '4.9',
            'reviews_count' => '35,000+',
        ],
        'software-development' => [
            'name' => 'Software Development',
            'title' => 'Full-Stack Software Engineering Training Program',
            'subtitle' => 'Modern software engineering in Java Full Stack, MERN, Python, and system design.',
            'badge' => 'DEV COURSE',
            'rating' => '4.9',
            'reviews_count' => '61,000+',
        ],
        'business-leadership' => [
            'name' => 'Business & Leadership',
            'title' => 'Business Analysis & Executive Leadership Program',
            'subtitle' => 'Strategic business consulting, product management and executive communication.',
            'badge' => 'BUSINESS COURSE',
            'rating' => '4.8',
            'reviews_count' => '24,000+',
        ],
        'big-data' => [
            'name' => 'Big Data',
            'title' => 'Big Data & Cloud Data Engineering Program',
            'subtitle' => 'Apache Spark, Hadoop, Databricks, Kafka, and enterprise data pipelines.',
            'badge' => 'BIG DATA COURSE',
            'rating' => '4.9',
            'reviews_count' => '29,000+',
        ],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'category',
        'status',
        'short_description',
        'description',
        'curriculum',
        'duration',
        'modules_count',
        'delivery_mode',
        'fee',
        'discount_percent',
        'original_fee',
        'featured',
        'learner_count',
        'rating',
        'ratings_count',
        'reviews_count',
        'badge_label',
        'tag_badge',
        'highlights',
        'target_audience',
        'seo_title',
        'seo_description',
        'sort_order',
        'published_at',
        'thumbnail_path',
        'banner_path',
        'hero_heading',
        'hero_subtitle',
        'hero_description',
        'hero_cta_text',
        'hero_cta_url',
        'secondary_cta_text',
        'secondary_cta_url',
        'learning_outcomes',
        'skills_covered',
        'course_objectives',
        'prerequisites',
        'target_audience_text',
        'career_opportunities',
        'why_choose_us',
        'certification_name',
        'certification_authority',
        'certification_description',
        'certification_image_path',
        'certification_skills',
        'certification_benefits',
        'section_config',
        'trust_config',
        'meta_keywords',
        'og_image_path',
        'robots_directive',
        'canonical_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'curriculum' => 'array',
        'highlights' => 'array',
        'target_audience' => 'array',
        'learning_outcomes' => 'array',
        'skills_covered' => 'array',
        'why_choose_us' => 'array',
        'certification_skills' => 'array',
        'certification_benefits' => 'array',
        'section_config' => 'array',
        'trust_config' => 'array',
        'featured' => 'boolean',
        'fee' => 'integer',
        'discount_percent' => 'integer',
        'original_fee' => 'integer',
        'modules_count' => 'integer',
        'learner_count' => 'integer',
        'ratings_count' => 'integer',
        'reviews_count' => 'integer',
        'rating' => 'float',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category that owns the course.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the explicit Category model.
     */
    public function categoryRecord(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the curriculum modules belonging to the course.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get all lessons belonging to the course through modules.
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class, 'course_id', 'course_module_id')->orderBy('lessons.sort_order', 'asc');
    }

    /**
     * Get the student enrollments for this course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get all payment transactions for this course.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all approved students enrolled in this course.
     */
    public function enrolledStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->wherePivot('status', Enrollment::STATUS_APPROVED)
            ->withTimestamps();
    }

    /**
     * Get all quizzes created for this course.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get all assignments created for this course.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get all certificates issued for this course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get all FAQs created for this course.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(CourseFaq::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get active FAQs for public display.
     */
    public function activeFaqs(): HasMany
    {
        return $this->hasMany(CourseFaq::class)->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Get all batches for this course.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(CourseBatch::class)->orderBy('sort_order', 'asc')->orderBy('start_date', 'asc');
    }

    /**
     * Get active upcoming batches for public display.
     */
    public function activeBatches(): HasMany
    {
        return $this->hasMany(CourseBatch::class)->where('is_active', true)->orderBy('sort_order', 'asc')->orderBy('start_date', 'asc');
    }

    /**
     * Get all testimonials for this course.
     */
    public function testimonials(): HasMany
    {
        return $this->hasMany(CourseTestimonial::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get published testimonials for public display.
     */
    public function publishedTestimonials(): HasMany
    {
        return $this->hasMany(CourseTestimonial::class)->where('is_published', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Get all industry / accreditation partners for this course.
     */
    public function partners(): HasMany
    {
        return $this->hasMany(CoursePartner::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get active partners for public display.
     */
    public function activePartners(): HasMany
    {
        return $this->hasMany(CoursePartner::class)->where('is_active', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Get past URL slug redirects for this course.
     */
    public function slugRedirects(): HasMany
    {
        return $this->hasMany(CourseSlugRedirect::class);
    }

    /**
     * Get the instructors assigned to this course.
     */
    public function instructors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_instructors')
            ->withPivot(['role_or_title', 'assigned_at', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Get centrally mapped trust and proof of work items for this course.
     */
    public function trustItems(): BelongsToMany
    {
        return $this->belongsToMany(TrustItem::class, 'trust_item_courses')
            ->withPivot(['sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order', 'asc');
    }

    /**
     * Check if a specific user/instructor is assigned to this course.
     */
    public function hasInstructor(User $user): bool
    {
        return $this->instructors()
            ->where('users.id', $user->id)
            ->exists();
    }

    /**
     * Assign an instructor to this course idempotently.
     */
    public function assignInstructor(User $user, ?string $roleOrTitle = 'Lead Instructor'): void
    {
        if (! $this->hasInstructor($user)) {
            $this->instructors()->attach($user->id, [
                'role_or_title' => $roleOrTitle ?? 'Lead Instructor',
                'assigned_at' => now(),
            ]);
        }
    }

    /**
     * Remove an assigned instructor from this course.
     */
    public function removeInstructor(User $user): void
    {
        $this->instructors()->detach($user->id);
    }

    /**
     * Check if course is currently open for enrollment.
     */
    public function isAvailableForEnrollment(): bool
    {
        return $this->status === 'available' && ($this->published_at === null || $this->published_at <= now());
    }

    /**
     * Scope a query to only include published courses.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include available courses.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope a query to only include upcoming courses.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'upcoming');
    }

    /**
     * Scope a query to only include planned roadmap courses.
     */
    public function scopePlanned(Builder $query): Builder
    {
        return $query->where('status', 'planned');
    }

    /**
     * Scope a query to only include featured courses.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Get title alias for course name.
     */
    public function getTitleAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * Scope a query by category.
     */
    public function scopeByCategory(Builder $query, ?string $category): Builder
    {
        if (! $category || $category === 'all') {
            return $query;
        }

        if ($category === 'sap-erp') {
            return $query->whereIn('category', ['sap-erp', 'functional', 'technical', 'cloud_specialized']);
        }

        return $query->where('category', $category);
    }

    /**
     * Scope a query by status.
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (! $status || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Check if course is currently available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    /**
     * Check if course is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    /**
     * Check if course is planned roadmap.
     */
    public function isPlanned(): bool
    {
        return $this->status === 'planned';
    }

    /**
     * Calculate and return the final payable tuition amount in integer rupees.
     */
    public function getPayableAmountInRupees(): int
    {
        if ($this->fee !== null && $this->fee > 0) {
            return (int) $this->fee;
        }

        if ($this->original_fee !== null && $this->original_fee > 0) {
            if ($this->discount_percent !== null && $this->discount_percent > 0 && $this->discount_percent < 100) {
                $discount = (int) floor(($this->original_fee * $this->discount_percent) / 100);

                return max(1, (int) ($this->original_fee - $discount));
            }

            return (int) $this->original_fee;
        }

        return 0;
    }

    /**
     * Get final payable amount in integer paise for payment gateways (1 INR = 100 paise).
     */
    public function getPayableAmountInPaise(): int
    {
        $rupees = $this->getPayableAmountInRupees();

        return (int) ($rupees * 100);
    }

    /**
     * Get user-facing formatted fee with GST.
     */
    public function getFormattedFeeAttribute(): ?string
    {
        if ($this->fee === null) {
            return null;
        }

        return '₹'.number_format($this->fee).' + GST';
    }

    /**
     * Get standalone display price (e.g. ₹75,000).
     */
    public function getDisplayPriceAttribute(): string
    {
        if ($this->fee === null) {
            return '₹25,000';
        }

        return '₹'.number_format($this->fee);
    }

    /**
     * Get safe discount percent.
     */
    public function getDisplayDiscountAttribute(): string
    {
        $discount = $this->discount_percent ?? 62;

        return $discount.'% OFF';
    }

    /**
     * Get safe rating decimal.
     */
    public function getDisplayRatingAttribute(): string
    {
        return number_format($this->rating ?? 4.9, 1);
    }

    /**
     * Get safe rating count.
     */
    public function getDisplayRatingsCountAttribute(): string
    {
        $count = $this->ratings_count ?? ($this->learner_count ? $this->learner_count * 8 : 13952);

        return number_format($count);
    }

    /**
     * Get dynamic review count directly from database or compute dynamically.
     */
    public function getDynamicReviewsCountAttribute(): int
    {
        // 1. If explicit reviews_count is stored in database, use it
        if ($this->reviews_count !== null && $this->reviews_count > 0) {
            return (int) $this->reviews_count;
        }

        // 2. If actual testimonials exist for this course in DB, count them
        if ($this->relationLoaded('publishedTestimonials') && $this->publishedTestimonials->isNotEmpty()) {
            $actualCount = $this->publishedTestimonials->count();
            if ($actualCount > 10) {
                return $actualCount;
            }
        }

        // 3. If ratings_count exists, dynamic review count is ~80-85% of total ratings
        if ($this->ratings_count !== null && $this->ratings_count > 0) {
            return (int) round($this->ratings_count * 0.82);
        }

        // 4. Default dynamic count derived from learner count
        return $this->learner_count ? (int) round($this->learner_count * 1.4) : 11250;
    }

    /**
     * Get formatted display reviews count with commas.
     */
    public function getDisplayReviewsCountAttribute(): string
    {
        return number_format($this->dynamic_reviews_count);
    }

    /**
     * Get safe duration label.
     */
    public function getDisplayDurationAttribute(): string
    {
        return $this->duration ?? '6 to 9 Months';
    }

    /**
     * Get safe modules count label.
     */
    public function getDisplayModulesAttribute(): string
    {
        $mods = $this->modules_count ?? 11;

        return $mods.' Modules';
    }

    /**
     * Get safe learners label.
     */
    public function getDisplayStudentsAttribute(): string
    {
        $students = $this->learner_count ?? 8500;

        return number_format($students).'+ Students';
    }

    /**
     * Get safe tag badge (e.g. HOT, BESTSELLER).
     */
    public function getDisplayTagAttribute(): string
    {
        return $this->tag_badge ?? 'HOT';
    }

    /**
     * Get category badge label (e.g. SAP COURSE, WORKDAY COURSE).
     */
    public function getCategoryBadgeAttribute(): string
    {
        return self::CATEGORIES[$this->category]['badge'] ?? 'CERTIFICATION COURSE';
    }

    /**
     * Get human-readable category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        if (isset(self::CATEGORIES[$this->category])) {
            return self::CATEGORIES[$this->category]['name'];
        }

        return match ($this->category) {
            'functional' => 'SAP Functional',
            'technical' => 'SAP Technical',
            'cloud_specialized' => 'SAP Cloud & Specialized',
            default => 'Academy Course',
        };
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'available' => 'Available Now',
            'upcoming' => 'Upcoming Batch',
            'planned' => 'Academy Roadmap',
            default => 'Course',
        };
    }

    /**
     * Get resolved category slug for hierarchical URLs (e.g., sap-functional).
     */
    public function getCategorySlugAttribute(): string
    {
        if (! empty($this->category)) {
            return Str::slug($this->category);
        }

        if ($this->relationLoaded('categoryRecord') && $this->getRelation('categoryRecord')) {
            return $this->getRelation('categoryRecord')->slug;
        }

        if ($this->category_id) {
            $cat = Category::find($this->category_id);
            if ($cat) {
                return $cat->slug;
            }
        }

        return 'courses';
    }

    /**
     * Mutator to ensure slug is always cleanly trimmed without leading or trailing slashes.
     */
    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = trim((string) $value, "/ \t\n\r\0\x0B");
    }

    /**
     * Get destination URL for course.
     * Uses hierarchical format: /{category}/{course-slug} (e.g. /sap-functional/sap-fico-on-s-4-hana)
     */
    public function getUrlAttribute(): string
    {
        if ($this->slug === 'sap-fico-training') {
            return route('courses.fico');
        }

        $categorySlug = trim($this->category_slug, '/');
        $courseSlug = trim((string) $this->slug, '/');

        return url('/'.$categorySlug.'/'.$courseSlug);
    }

    /**
     * Get resolved SEO Meta Title.
     */
    public function getResolvedSeoTitleAttribute(): string
    {
        if (! empty($this->seo_title)) {
            return $this->seo_title;
        }

        return $this->name.' Certification Training Program | ZebLearn';
    }

    /**
     * Get resolved SEO Meta Description.
     */
    public function getResolvedSeoDescriptionAttribute(): string
    {
        if (! empty($this->seo_description)) {
            return $this->seo_description;
        }

        $source = $this->hero_description ?: ($this->short_description ?: $this->description);
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($source ?? '')));

        if ($clean !== '') {
            return Str::limit($clean, 155);
        }

        return "Master {$this->name} with ZebLearn's live interactive cohort training, hands-on enterprise projects, expert mentorship, and global certification prep.";
    }

    /**
     * Get resolved Canonical URL.
     */
    public function getResolvedCanonicalUrlAttribute(): string
    {
        if (! empty($this->canonical_url)) {
            return $this->canonical_url;
        }

        return url('/'.$this->category_slug.'/'.$this->slug);
    }

    /**
     * Get resolved OG Image URL.
     */
    public function getResolvedOgImageAttribute(): string
    {
        if (! empty($this->og_image_path)) {
            return filter_var($this->og_image_path, FILTER_VALIDATE_URL) ? $this->og_image_path : asset($this->og_image_path);
        }

        if (! empty($this->banner_path)) {
            return filter_var($this->banner_path, FILTER_VALIDATE_URL) ? $this->banner_path : asset($this->banner_path);
        }

        if (! empty($this->thumbnail_path)) {
            return filter_var($this->thumbnail_path, FILTER_VALIDATE_URL) ? $this->thumbnail_path : asset($this->thumbnail_path);
        }

        return asset('images/logo.png');
    }

    /**
     * Get safe, sanitized HTML rendering of markdown course description.
     */
    public function getRenderedDescriptionAttribute(): string
    {
        return app(MarkdownSanitizerService::class)->toHtml($this->description);
    }

    /**
     * Get clean excerpt for hero description without raw markdown syntax.
     */
    public function getCleanHeroDescriptionAttribute(): string
    {
        if (! empty($this->hero_description)) {
            return $this->hero_description;
        }

        if (! empty($this->short_description)) {
            return $this->short_description;
        }

        return app(MarkdownSanitizerService::class)->extractExcerpt($this->description, 320);
    }

    /**
     * Get resolved section configuration with default ordering and visibility.
     *
     * @return array<string, array{visible: bool, order: int, title: string}>
     */
    public function getResolvedSectionConfigAttribute(): array
    {
        $defaultConfig = [
            'hero' => ['visible' => true, 'order' => 10, 'title' => 'Course Hero Section'],
            'trust_indicators' => ['visible' => true, 'order' => 15, 'title' => 'Key Points & Trust Statistics'],
            'partners' => ['visible' => true, 'order' => 20, 'title' => 'Hiring Partners'],
            'overview' => ['visible' => true, 'order' => 30, 'title' => 'Course Overview'],
            'certification_partners' => ['visible' => true, 'order' => 40, 'title' => 'Certification Partners'],
            'sap_global_certification' => ['visible' => true, 'order' => 50, 'title' => 'SAP Global Certification Showcase'],
            'why_choose_us' => ['visible' => true, 'order' => 60, 'title' => 'Why Choose ZebLearn'],
            'outcomes' => ['visible' => true, 'order' => 70, 'title' => 'Key Learning Outcomes'],
            'curriculum' => ['visible' => true, 'order' => 80, 'title' => 'Detailed Course Curriculum'],
            'practical_training' => ['visible' => true, 'order' => 90, 'title' => 'Practical Training and Real-System Practice'],
            'projects' => ['visible' => true, 'order' => 100, 'title' => 'Projects and Case Studies'],
            'faculty' => ['visible' => true, 'order' => 110, 'title' => 'Trainer Profiles'],
            'placements' => ['visible' => true, 'order' => 120, 'title' => 'Student Placement Stories'],
            'testimonials' => ['visible' => true, 'order' => 130, 'title' => 'Student Testimonials'],
            'video_stories' => ['visible' => true, 'order' => 140, 'title' => 'Video Testimonials'],
            'certification' => ['visible' => true, 'order' => 150, 'title' => 'Certification Information'],
            'batches' => ['visible' => true, 'order' => 160, 'title' => 'Upcoming Batches'],
            'pricing' => ['visible' => true, 'order' => 170, 'title' => 'Course Fee and Pricing'],
            'awards' => ['visible' => true, 'order' => 175, 'title' => 'Awards & Institutional Recognition'],
            'faqs' => ['visible' => true, 'order' => 180, 'title' => 'Frequently Asked Questions'],
            'related' => ['visible' => true, 'order' => 190, 'title' => 'Related SAP Courses'],
            'cta' => ['visible' => true, 'order' => 200, 'title' => 'Final Enquiry CTA'],
        ];

        if (is_array($this->section_config) && ! empty($this->section_config)) {
            foreach ($defaultConfig as $key => $default) {
                if (isset($this->section_config[$key])) {
                    $defaultConfig[$key]['visible'] = (bool) ($this->section_config[$key]['visible'] ?? $default['visible']);
                    $defaultConfig[$key]['order'] = (int) ($this->section_config[$key]['order'] ?? $default['order']);
                }
            }
        }

        uasort($defaultConfig, fn ($a, $b) => $a['order'] <=> $b['order']);

        return $defaultConfig;
    }

    /**
     * Get resolved course-level trust & proof configuration.
     *
     * @return array{include_global: bool, sections: array<string, array{enabled: bool, limit: int, mode: string}>, manual_overrides: array<string, list<int>>}
     */
    public function getResolvedTrustConfigAttribute(): array
    {
        $default = [
            'include_global' => true,
            'sections' => [
                'placements' => ['enabled' => true, 'limit' => 6, 'mode' => 'auto'],
                'testimonials' => ['enabled' => true, 'limit' => 6, 'mode' => 'auto'],
                'trainers' => ['enabled' => true, 'limit' => 3, 'mode' => 'auto'],
                'certifications' => ['enabled' => true, 'limit' => 6, 'mode' => 'auto'],
                'partners' => ['enabled' => true, 'limit' => 8, 'mode' => 'auto'],
                'projects' => ['enabled' => true, 'limit' => 6, 'mode' => 'auto'],
                'video_stories' => ['enabled' => true, 'limit' => 3, 'mode' => 'auto'],
                'awards' => ['enabled' => true, 'limit' => 3, 'mode' => 'auto'],
            ],
            'manual_overrides' => [],
        ];

        if (is_array($this->trust_config) && ! empty($this->trust_config)) {
            $default['include_global'] = (bool) ($this->trust_config['include_global'] ?? true);

            if (isset($this->trust_config['sections']) && is_array($this->trust_config['sections'])) {
                foreach ($default['sections'] as $key => $sectionDefaults) {
                    if (isset($this->trust_config['sections'][$key])) {
                        $default['sections'][$key]['enabled'] = (bool) ($this->trust_config['sections'][$key]['enabled'] ?? $sectionDefaults['enabled']);
                        $default['sections'][$key]['limit'] = (int) ($this->trust_config['sections'][$key]['limit'] ?? $sectionDefaults['limit']);
                        $default['sections'][$key]['mode'] = (string) ($this->trust_config['sections'][$key]['mode'] ?? $sectionDefaults['mode']);
                    }
                }
            }

            if (isset($this->trust_config['manual_overrides']) && is_array($this->trust_config['manual_overrides'])) {
                $default['manual_overrides'] = $this->trust_config['manual_overrides'];
            }
        }

        return $default;
    }

    /**
     * Find related published courses for internal linking.
     */
    public function relatedCourses(int $limit = 3)
    {
        $categoryCourses = self::published()
            ->where('id', '!=', $this->id)
            ->where('category', $this->category)
            ->orderBy('sort_order', 'asc')
            ->limit($limit)
            ->get();

        if ($categoryCourses->count() < $limit) {
            $fallback = self::published()
                ->where('id', '!=', $this->id)
                ->whereNotIn('id', $categoryCourses->pluck('id'))
                ->orderBy('learner_count', 'desc')
                ->limit($limit - $categoryCourses->count())
                ->get();

            $categoryCourses = $categoryCourses->concat($fallback);
        }

        return $categoryCourses;
    }

    /**
     * Check if course is actively published and live.
     */
    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at <= now();
    }

    /**
     * Check if course is a draft.
     */
    public function isDraft(): bool
    {
        return $this->published_at === null || $this->published_at > now();
    }

    /**
     * Check if course was recently added (within the last 30 days or latest additions).
     */
    public function isRecentlyAdded(): bool
    {
        if ($this->created_at && $this->created_at->greaterThanOrEqualTo(now()->subDays(30))) {
            return true;
        }

        return false;
    }

    /**
     * Scope query to sort by newly added courses first.
     */
    public function scopeRecentlyAdded(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }
}
