<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrustItem extends Model
{
    use HasFactory;

    public const TYPE_PLACEMENT = 'placement';

    public const TYPE_TESTIMONIAL = 'testimonial';

    public const TYPE_CERTIFICATION = 'certification';

    public const TYPE_TRAINER = 'trainer';

    public const TYPE_PARTNER = 'partner';

    public const TYPE_PROJECT = 'project';

    public const TYPE_VIDEO_STORY = 'video_story';

    public const TYPE_AWARD = 'award';

    public const TYPES = [
        self::TYPE_PLACEMENT => 'Student Placements',
        self::TYPE_TESTIMONIAL => 'Student Testimonials',
        self::TYPE_CERTIFICATION => 'Certification Records',
        self::TYPE_TRAINER => 'Expert Trainers',
        self::TYPE_PARTNER => 'Partner Organizations',
        self::TYPE_PROJECT => 'Industry Projects',
        self::TYPE_VIDEO_STORY => 'Student Video Stories',
        self::TYPE_AWARD => 'Awards & Recognition',
    ];

    public const SCOPE_GLOBAL = 'global';

    public const SCOPE_ALL = 'all';

    public const SCOPE_CATEGORY = 'category';

    public const SCOPE_SELECTED_COURSES = 'selected_courses';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trust_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'title',
        'subtitle',
        'description',
        'image_path',
        'media_url',
        'content_category',
        'scope_type',
        'scope_category',
        'metadata',
        'is_verified',
        'is_published',
        'display_priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_published' => 'boolean',
        'display_priority' => 'integer',
    ];

    /**
     * Associated courses mapped to this trust record.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'trust_item_courses')
            ->withPivot(['sort_order'])
            ->withTimestamps()
            ->orderByPivot('sort_order', 'asc');
    }

    /**
     * Scope to published records.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to verified records.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to active records eligible for public display (published & verified).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('is_verified', true);
    }

    /**
     * Scope query by content type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Helper to read metadata key with fallback.
     */
    public function meta(string $key, mixed $default = null): mixed
    {
        return $this->metadata[$key] ?? $default;
    }

    /**
     * Human-readable type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? ucfirst(str_replace('_', ' ', $this->type));
    }

    /**
     * Resolved display image.
     */
    public function getDisplayImageAttribute(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
