<?php

namespace App\Models;

use App\Services\QuizService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PUBLISHED,
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
        'title',
        'slug',
        'description',
        'instructions',
        'time_limit_minutes',
        'total_marks',
        'passing_percentage',
        'pass_percentage',
        'attempts_allowed',
        'allowed_attempts',
        'shuffle_questions',
        'shuffle_options',
        'show_correct_answers',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'time_limit_minutes' => 'integer',
        'total_marks' => 'integer',
        'passing_percentage' => 'integer',
        'attempts_allowed' => 'integer',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_correct_answers' => 'boolean',
    ];

    public function getPassingPercentageAttribute(): int
    {
        return (int) ($this->attributes['passing_percentage'] ?? 70);
    }

    public function getPassPercentageAttribute(): int
    {
        return (int) ($this->attributes['passing_percentage'] ?? 70);
    }

    public function getAllowedAttemptsAttribute(): int
    {
        return (int) ($this->attributes['attempts_allowed'] ?? 1);
    }

    public function setPassPercentageAttribute($value): void
    {
        $this->attributes['passing_percentage'] = (int) $value;
    }

    public function setAllowedAttemptsAttribute($value): void
    {
        $this->attributes['attempts_allowed'] = (int) $value;
    }

    /**
     * Get the course this quiz belongs to.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the module this quiz is scoped to, if module or lesson level.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    /**
     * Get the lesson this quiz is scoped to, if lesson level.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get the ordered questions belonging to this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Get all attempts submitted by students for this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Scope a query to only published quizzes.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_PUBLISHED);
    }

    /**
     * Scope a query to only draft quizzes.
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
            return 'Lesson Assessment';
        }

        if ($this->isModuleLevel()) {
            return 'Module Assessment';
        }

        return 'Course Final Assessment';
    }

    /**
     * Check if this quiz has any student attempts in any state.
     */
    public function hasAttempts(): bool
    {
        return $this->attempts()->exists();
    }

    /**
     * Recalculate total marks sum from all questions and save to database.
     */
    public function recalculateTotalMarks(): int
    {
        if ($this->hasAttempts()) {
            return (int) $this->total_marks;
        }

        $sum = (int) $this->questions()->sum('marks');
        $this->update(['total_marks' => $sum]);

        return $sum;
    }

    /**
     * Validate whether quiz meets all structural requirements for publishing.
     *
     * @return list<string> Validation error messages (empty if valid)
     */
    public function getPublishingValidationErrors(): array
    {
        $errors = [];

        $questions = $this->questions()->with('options')->get();

        if ($questions->isEmpty()) {
            $errors[] = 'Quiz must have at least one question before it can be published.';

            return $errors;
        }

        foreach ($questions as $index => $q) {
            $num = $index + 1;

            if ($q->options->count() < 2) {
                $errors[] = "Question #{$num} must have at least 2 answer options.";
            }

            $correctCount = $q->options->where('is_correct', true)->count();
            if ($correctCount !== 1) {
                $errors[] = "Question #{$num} must have exactly 1 correct answer option (found {$correctCount}).";
            }

            if ($q->marks < 1) {
                $errors[] = "Question #{$num} marks must be a positive integer.";
            }
        }

        if ($this->passing_percentage < 0 || $this->passing_percentage > 100) {
            $errors[] = 'Passing percentage must be between 0% and 100%.';
        }

        if ($this->attempts_allowed < 1) {
            $errors[] = 'Allowed attempts must be at least 1.';
        }

        return $errors;
    }

    public function canBePublished(): bool
    {
        return empty($this->getPublishingValidationErrors());
    }

    /**
     * Check if a specific student can start or resume an attempt.
     */
    public function canUserAttempt(User $user): bool
    {
        if (! $user->isStudent() || ! $user->isActive()) {
            return false;
        }

        if (! $this->isPublished()) {
            return false;
        }

        $activeAttempt = $this->activeAttemptForUser($user);
        if ($activeAttempt) {
            if (! $activeAttempt->hasExpired()) {
                return true;
            }

            // Finalize expired in-progress attempt
            app(QuizService::class)->submitAttempt($activeAttempt);
        }

        $totalAttempts = $this->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                QuizAttempt::STATUS_IN_PROGRESS,
                QuizAttempt::STATUS_SUBMITTED,
                QuizAttempt::STATUS_EXPIRED,
            ])
            ->count();

        return $totalAttempts < $this->attempts_allowed;
    }

    /**
     * Get remaining attempts for a student across all statuses.
     */
    public function remainingAttemptsForUser(User $user): int
    {
        $activeAttempt = $this->activeAttemptForUser($user);
        if ($activeAttempt && $activeAttempt->hasExpired()) {
            app(QuizService::class)->submitAttempt($activeAttempt);
        }

        $used = $this->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', [
                QuizAttempt::STATUS_IN_PROGRESS,
                QuizAttempt::STATUS_SUBMITTED,
                QuizAttempt::STATUS_EXPIRED,
            ])
            ->count();

        return max(0, $this->attempts_allowed - $used);
    }

    /**
     * Get latest attempt of a user.
     */
    public function latestAttemptForUser(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->latest('id')
            ->first();
    }

    /**
     * Get active in-progress attempt for a user if one exists.
     */
    public function activeAttemptForUser(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', QuizAttempt::STATUS_IN_PROGRESS)
            ->first();
    }
}
