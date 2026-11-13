<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    /**
     * Supported lesson content types.
     */
    public const CONTENT_TYPES = [
        'text' => 'Text Lesson',
        'video' => 'Video Lesson',
        'resource' => 'Resource Link',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_module_id',
        'title',
        'description',
        'content_type',
        'content',
        'video_url',
        'resource_url',
        'duration_minutes',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the module that owns the lesson.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    /**
     * Get all student progress records for this lesson.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get the progress record for a specific user.
     */
    public function progressForUser(User $user): ?LessonProgress
    {
        return $this->progress()->where('user_id', $user->id)->first();
    }

    /**
     * Get the quizzes associated with this specific lesson.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the assignments associated directly with this lesson.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Scope a query to only include published lessons.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), 'published');
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /**
     * Check if lesson is text based.
     */
    public function isText(): bool
    {
        return $this->content_type === 'text';
    }

    /**
     * Check if lesson is video based.
     */
    public function isVideo(): bool
    {
        return $this->content_type === 'video';
    }

    /**
     * Check if lesson is an external resource.
     */
    public function isResource(): bool
    {
        return $this->content_type === 'resource';
    }

    /**
     * Check if lesson is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if lesson is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Get human-readable formatted duration.
     */
    public function getFormattedDurationAttribute(): ?string
    {
        if ($this->duration_minutes === null) {
            return null;
        }

        if ($this->duration_minutes < 60) {
            return $this->duration_minutes.' min';
        }

        $hours = floor($this->duration_minutes / 60);
        $remainder = $this->duration_minutes % 60;

        if ($remainder === 0) {
            return $hours.' hr';
        }

        return $hours.' hr '.$remainder.' min';
    }
}
