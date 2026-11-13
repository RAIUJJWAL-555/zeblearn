<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the course that owns the module.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lessons belonging to this module.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'course_module_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Get the quizzes associated directly with this module.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'course_module_id');
    }

    /**
     * Get the assignments associated directly with this module.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'course_module_id');
    }

    /**
     * Scope a query to only include published modules.
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
     * Check if module is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if module is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
