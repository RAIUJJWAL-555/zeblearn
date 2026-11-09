<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'category',
        'status',
        'short_description',
        'description',
        'curriculum',
        'duration',
        'delivery_mode',
        'fee',
        'featured',
        'learner_count',
        'badge_label',
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
        'learner_count' => 'integer',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

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
     * Get user-facing formatted fee.
     */
    public function getFormattedFeeAttribute(): ?string
    {
        if ($this->fee === null) {
            return null;
        }

        return '₹'.number_format($this->fee).' + GST';
    }

    /**
     * Get human-readable category label.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'functional' => 'SAP Functional',
            'technical' => 'SAP Technical',
            'cloud_specialized' => 'SAP Cloud & Specialized',
            default => 'SAP Academy',
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

        return route('book-demo', ['course' => $this->slug]);
    }
}
