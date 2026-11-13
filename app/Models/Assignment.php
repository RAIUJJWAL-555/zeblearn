<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
    ];

    /**
     * Map of common file extensions to accepted MIME types.
     *
     * @var array<string, list<string>>
     */
    public const EXTENSION_MIME_MAP = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'txt' => ['text/plain'],
        'zip' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip'],
        'rar' => ['application/x-rar-compressed', 'application/vnd.rar', 'application/octet-stream'],
        'png' => ['image/png'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'csv' => ['text/csv', 'text/plain', 'application/vnd.ms-excel'],
        'py' => ['text/x-python', 'text/plain'],
        'sql' => ['application/sql', 'text/x-sql', 'text/plain'],
        'json' => ['application/json', 'text/plain'],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'course_id',
        'course_module_id',
        'lesson_id',
        'created_by',
        'title',
        'slug',
        'description',
        'instructions',
        'max_marks',
        'passing_marks',
        'start_at',
        'due_at',
        'allow_late_submissions',
        'late_penalty_percent',
        'max_attempts',
        'allow_text_submission',
        'allow_file_submission',
        'allowed_file_types',
        'max_file_size_mb',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_marks' => 'integer',
        'passing_marks' => 'integer',
        'start_at' => 'datetime',
        'due_at' => 'datetime',
        'allow_late_submissions' => 'boolean',
        'late_penalty_percent' => 'integer',
        'max_attempts' => 'integer',
        'allow_text_submission' => 'boolean',
        'allow_file_submission' => 'boolean',
        'max_file_size_mb' => 'integer',
    ];

    /**
     * Get the course this assignment belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the module this assignment belongs to, if module or lesson level.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    /**
     * Get the lesson this assignment belongs to, if lesson level.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the administrator or instructor who authored the assignment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all submissions across students for this assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Scope a query to only published assignments.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_PUBLISHED);
    }

    /**
     * Scope a query to only draft assignments.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_DRAFT);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isCourseLevel(): bool
    {
        return $this->course_module_id === null && $this->lesson_id === null;
    }

    public function isModuleLevel(): bool
    {
        return $this->course_module_id !== null && $this->lesson_id === null;
    }

    public function isLessonLevel(): bool
    {
        return $this->lesson_id !== null;
    }

    public function getContextTypeAttribute(): string
    {
        if ($this->isLessonLevel()) {
            return 'lesson';
        }

        if ($this->isModuleLevel()) {
            return 'module';
        }

        return 'course';
    }

    public function getContextLabelAttribute(): string
    {
        if ($this->isLessonLevel()) {
            return 'Lesson Assignment';
        }

        if ($this->isModuleLevel()) {
            return 'Module Assignment';
        }

        return 'Course Final Project';
    }

    /**
     * Determine if this assignment has historical submissions.
     */
    public function hasSubmissions(): bool
    {
        return $this->submissions()->exists();
    }

    /**
     * Determine if submissions are currently open.
     */
    public function isSubmissionOpen(): bool
    {
        if (! $this->isPublished()) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_at && $now->isBefore($this->start_at)) {
            return false;
        }

        if ($this->due_at && $now->isAfter($this->due_at) && ! $this->allow_late_submissions) {
            return false;
        }

        return true;
    }

    /**
     * Check if assignment is past due deadline.
     */
    public function isPastDue(): bool
    {
        if ($this->due_at === null) {
            return false;
        }

        return Carbon::now()->isAfter($this->due_at);
    }

    /**
     * Check if assignment has not started yet.
     */
    public function isBeforeStart(): bool
    {
        if ($this->start_at === null) {
            return false;
        }

        return Carbon::now()->isBefore($this->start_at);
    }

    /**
     * Parse allowed file extensions array.
     *
     * @return list<string>
     */
    public function getAllowedFileExtensions(): array
    {
        if (empty($this->allowed_file_types)) {
            return ['pdf', 'doc', 'docx', 'zip', 'txt'];
        }

        return array_values(array_filter(array_map(
            fn ($ext) => strtolower(trim(ltrim($ext, '.'))),
            explode(',', $this->allowed_file_types)
        )));
    }

    /**
     * Check if a specific student can submit to this assignment.
     */
    public function canUserSubmit(User $user): bool
    {
        if (! $user->isStudent() || ! $user->isActive()) {
            return false;
        }

        if (! $this->isPublished()) {
            return false;
        }

        if ($this->isBeforeStart()) {
            return false;
        }

        if ($this->isPastDue() && ! $this->allow_late_submissions) {
            return false;
        }

        $attemptsCount = $this->submissions()
            ->where('user_id', $user->id)
            ->count();

        return $attemptsCount < $this->max_attempts;
    }

    /**
     * Calculate remaining submission attempts for a student.
     */
    public function remainingAttemptsForUser(User $user): int
    {
        $used = $this->submissions()
            ->where('user_id', $user->id)
            ->count();

        return max(0, $this->max_attempts - $used);
    }

    /**
     * Get the latest submission for a student.
     */
    public function latestSubmissionForUser(User $user): ?AssignmentSubmission
    {
        return $this->submissions()
            ->where('user_id', $user->id)
            ->latest('attempt_number')
            ->first();
    }
}
