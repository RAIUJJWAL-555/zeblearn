<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

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
        'badge_label',
        'tag_badge',
        'highlights',
        'target_audience',
        'seo_title',
        'seo_description',
        'sort_order',
        'published_at',
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
        'featured' => 'boolean',
        'fee' => 'integer',
        'discount_percent' => 'integer',
        'original_fee' => 'integer',
        'modules_count' => 'integer',
        'learner_count' => 'integer',
        'ratings_count' => 'integer',
        'rating' => 'float',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category that owns the course.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
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
        return $this->hasManyThrough(Lesson::class, CourseModule::class, 'course_id', 'course_module_id')->orderBy('sort_order', 'asc');
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
     * Get destination URL for course.
     */
    public function getUrlAttribute(): string
    {
        if ($this->slug === 'sap-fico-training') {
            return route('courses.fico');
        }

        return route('courses.show', $this->slug);
    }
}
