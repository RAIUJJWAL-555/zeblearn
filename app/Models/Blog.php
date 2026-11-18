<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('blog_active_categories_with_count');
            Cache::forget('search_default_top_blogs');
        });
        static::deleted(function () {
            Cache::forget('blog_active_categories_with_count');
            Cache::forget('search_default_top_blogs');
        });
    }

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_INCOMPLETE = 'incomplete';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_SCHEDULED => 'Scheduled',
        self::STATUS_INCOMPLETE => 'Incomplete (Recovered)',
    ];

    public const RECOVERY_NONE = 'none';

    public const RECOVERY_FULLY_RECOVERED = 'fully_recovered';

    public const RECOVERY_INCOMPLETE = 'incomplete';

    public const RECOVERY_FAILED = 'failed';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'featured_image_original_url',
        'author_id',
        'author_name',
        'status',
        'published_at',
        'scheduled_at',
        'legacy_source_url',
        'legacy_id',
        'recovery_status',
        'recovery_notes',
        'seo_title',
        'meta_description',
        'focus_keyword',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots_index',
        'robots_follow',
        'schema_type',
        'view_count',
        'reading_time',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'robots_index' => 'boolean',
        'robots_follow' => 'boolean',
        'view_count' => 'integer',
        'reading_time' => 'integer',
        'legacy_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (Blog $blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }

            // Calculate reading time based on content words
            if (! empty($blog->content)) {
                $wordCount = str_word_count(strip_tags($blog->content));
                $blog->reading_time = max(1, (int) ceil($wordCount / 200));
            }

            // If publishing now and published_at is not set
            if ($blog->status === self::STATUS_PUBLISHED && empty($blog->published_at)) {
                $blog->published_at = now();
            }
        });
    }

    /**
     * Categories assigned to this blog.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_blog_category');
    }

    /**
     * Tags assigned to this blog.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_blog_tag');
    }

    /**
     * Author user account if linked.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope to published blogs ready for public consumption.
     * Excludes drafts, scheduled (future published_at), unpublished, and deleted articles.
     * Allows created_at fallback only when status is published and published_at is null.
     */
    public function scopePublished(Builder $query): Builder
    {
        $statusCol = $this->qualifyColumn('status');
        $publishedAtCol = $this->qualifyColumn('published_at');
        $createdAtCol = $this->qualifyColumn('created_at');

        return $query->where($statusCol, self::STATUS_PUBLISHED)
            ->where(function (Builder $q) use ($publishedAtCol, $createdAtCol) {
                $q->where(function (Builder $sub) use ($publishedAtCol) {
                    $sub->whereNotNull($publishedAtCol)
                        ->where($publishedAtCol, '<=', now()->addHours(14));
                })->orWhere(function (Builder $sub) use ($publishedAtCol, $createdAtCol) {
                    $sub->whereNull($publishedAtCol)
                        ->whereNotNull($createdAtCol)
                        ->where($createdAtCol, '<=', now()->addHours(14));
                });
            });
    }

    /**
     * Scope query to published blogs sorted chronologically newest first at database level.
     * Orders by published_at DESC with created_at fallback, and id DESC for deterministic pagination.
     */
    public function scopeNewestPublished(Builder $query): Builder
    {
        $publishedAtCol = $this->qualifyColumn('published_at');
        $createdAtCol = $this->qualifyColumn('created_at');
        $idCol = $this->qualifyColumn('id');

        return $query->published()
            ->orderByRaw("COALESCE({$publishedAtCol}, {$createdAtCol}) DESC")
            ->orderBy($idCol, 'desc');
    }

    /**
     * Check if blog is actively published and live.
     */
    public function isPublished(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        if ($this->published_at !== null) {
            return $this->published_at <= now()->addHours(14);
        }

        return $this->created_at !== null && $this->created_at <= now()->addHours(14);
    }

    /**
     * Scope to draft blogs.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope to scheduled blogs.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope to incomplete recovered blogs.
     */
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_INCOMPLETE);
    }

    /**
     * Scope to search blogs by query.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%")
                ->orWhere('author_name', 'like', "%{$term}%");
        });
    }

    /**
     * Accessor for author name display.
     */
    public function getEffectiveAuthorNameAttribute(): string
    {
        return $this->author?->name ?? $this->author_name ?? 'ZebLearn Editorial Team';
    }

    /**
     * Accessor for SEO Title.
     */
    public function getEffectiveSeoTitleAttribute(): string
    {
        return $this->seo_title ?: ($this->title.' | ZebLearn');
    }

    /**
     * Accessor for Meta Description.
     */
    public function getEffectiveMetaDescriptionAttribute(): string
    {
        if (! empty($this->meta_description)) {
            return $this->meta_description;
        }

        if (! empty($this->excerpt)) {
            return Str::limit(trim(strip_tags($this->excerpt)), 160);
        }

        return Str::limit(trim(strip_tags($this->content ?? '')), 160);
    }

    /**
     * Accessor for Meta Keywords.
     */
    public function getEffectiveMetaKeywordsAttribute(): ?string
    {
        if (! empty($this->meta_keywords)) {
            return $this->meta_keywords;
        }

        if ($this->relationLoaded('tags') && $this->tags->isNotEmpty()) {
            return $this->tags->pluck('name')->implode(', ');
        }

        if (! empty($this->focus_keyword)) {
            return $this->focus_keyword;
        }

        return null;
    }

    /**
     * Accessor for Canonical URL.
     */
    public function getEffectiveCanonicalUrlAttribute(): string
    {
        if (! empty($this->canonical_url)) {
            $parsedHost = parse_url($this->canonical_url, PHP_URL_HOST);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            if ($parsedHost && ! in_array($parsedHost, ['sap.zeblearn.com', $appHost, 'localhost', '127.0.0.1'])) {
                return url('/'.ltrim($this->slug, '/'));
            }

            return $this->canonical_url;
        }

        // Return root legacy path url if preserved, or standard blog url
        return url('/'.ltrim($this->slug, '/'));
    }

    /**
     * Accessor for OG image.
     */
    public function getEffectiveOgImageAttribute(): string
    {
        if (! empty($this->og_image)) {
            return $this->og_image;
        }

        if (! empty($this->featured_image)) {
            return Str::startsWith($this->featured_image, ['http://', 'https://'])
                ? $this->featured_image
                : asset($this->featured_image);
        }

        return asset('logo/logo2-400.webp');
    }

    /**
     * Accessor for primary category.
     */
    public function getPrimaryCategoryAttribute(): ?BlogCategory
    {
        return $this->categories->first();
    }
}
