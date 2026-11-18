<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_CONTACTED = 'contacted';

    public const STATUS_QUALIFIED = 'qualified';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUSES = [
        self::STATUS_NEW => 'New',
        self::STATUS_CONTACTED => 'Contacted',
        self::STATUS_QUALIFIED => 'Qualified',
        self::STATUS_CONVERTED => 'Converted',
        self::STATUS_REJECTED => 'Rejected',
    ];

    public const FORM_TYPES = [
        'contact' => 'Contact Us',
        'book_demo' => 'Book Free Demo',
        'hero_demo' => 'Hero Demo',
        'course_enquiry' => 'Fee Calculator',
        'callback' => 'Callback Request',
        'brochure' => 'Brochure Download',
        'placement' => 'Placement Help',
        'newsletter' => 'Newsletter',
        'lead' => 'Inbound Lead',
        'course_review' => 'Student Review',
    ];

    protected $fillable = [
        'form_type',
        'form_name',
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'course_id',
        'course_name',
        'company_name',
        'message',
        'additional_data',
        'page_url',
        'page_title',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Associated course if matched.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope query to search submissions across name, email, phone, message, course.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")
                ->orWhere('whatsapp_number', 'like', "%{$term}%")
                ->orWhere('course_name', 'like', "%{$term}%")
                ->orWhere('company_name', 'like', "%{$term}%")
                ->orWhere('message', 'like', "%{$term}%")
                ->orWhere('page_url', 'like', "%{$term}%")
                ->orWhere('utm_source', 'like', "%{$term}%")
                ->orWhere('utm_campaign', 'like', "%{$term}%");
        });
    }

    /**
     * Filter by form type.
     */
    public function scopeFormType(Builder $query, ?string $type): Builder
    {
        if (! empty($type) && $type !== 'all') {
            return $query->where('form_type', $type);
        }

        return $query;
    }

    /**
     * Filter by submission status.
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! empty($status) && $status !== 'all') {
            return $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Filter by associated course.
     */
    public function scopeCourseFilter(Builder $query, $courseIdOrName): Builder
    {
        if (empty($courseIdOrName) || $courseIdOrName === 'all') {
            return $query;
        }

        if (is_numeric($courseIdOrName)) {
            return $query->where('course_id', (int) $courseIdOrName);
        }

        return $query->where('course_name', 'like', "%{$courseIdOrName}%");
    }

    /**
     * Filter by submission date range.
     */
    public function scopeDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if (! empty($from)) {
            $query->whereDate('submitted_at', '>=', $from);
        }

        if (! empty($to)) {
            $query->whereDate('submitted_at', '<=', $to);
        }

        return $query;
    }

    /**
     * Scope for newest submissions first.
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy('submitted_at', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Get human-friendly badge color class for status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NEW => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::STATUS_CONTACTED => 'bg-blue-50 text-blue-700 border-blue-200',
            self::STATUS_QUALIFIED => 'bg-amber-50 text-amber-700 border-amber-200',
            self::STATUS_CONVERTED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::STATUS_REJECTED => 'bg-rose-50 text-rose-700 border-rose-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    /**
     * Get human-readable form type label.
     */
    public function getFormTypeLabelAttribute(): string
    {
        return self::FORM_TYPES[$this->form_type] ?? ucwords(str_replace('_', ' ', $this->form_type));
    }

    /**
     * Get badge color styling for form type.
     */
    public function getFormTypeBadgeClassAttribute(): string
    {
        return match ($this->form_type) {
            'hero_demo', 'book_demo' => 'bg-orange-50 text-[#FB6221] border-orange-200',
            'contact' => 'bg-blue-50 text-blue-700 border-blue-200',
            'course_enquiry' => 'bg-purple-50 text-purple-700 border-purple-200',
            'callback' => 'bg-amber-50 text-amber-700 border-amber-200',
            'brochure' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'placement' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'newsletter' => 'bg-teal-50 text-teal-700 border-teal-200',
            'course_review' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
