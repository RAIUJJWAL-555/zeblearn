<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_EXPIRED = 'expired';

    public const STATUSES = [
        self::STATUS_IN_PROGRESS,
        self::STATUS_SUBMITTED,
        self::STATUS_EXPIRED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'status',
        'started_at',
        'submitted_at',
        'expires_at',
        'score',
        'earned_marks',
        'total_marks',
        'percentage',
        'is_passed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attempt_number' => 'integer',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'expires_at' => 'datetime',
        'score' => 'integer',
        'total_marks' => 'integer',
        'percentage' => 'integer',
        'is_passed' => 'boolean',
    ];

    public function getEarnedMarksAttribute(): ?int
    {
        return $this->score !== null ? (int) $this->score : null;
    }

    public function setEarnedMarksAttribute($value): void
    {
        $this->attributes['score'] = $value !== null ? (int) $value : null;
    }

    /**
     * Get the quiz being attempted.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the student who took this attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get student's recorded answers for this attempt.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAttemptAnswer::class);
    }

    /**
     * Get the persisted question layout for this attempt.
     */
    public function attemptQuestions(): HasMany
    {
        return $this->hasMany(QuizAttemptQuestion::class)->orderBy('sort_order', 'asc');
    }

    /**
     * Scope for in-progress attempts.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_IN_PROGRESS);
    }

    /**
     * Scope for completed attempts (submitted or expired).
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereIn($this->qualifyColumn('status'), [self::STATUS_SUBMITTED, self::STATUS_EXPIRED]);
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isSubmitted(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    /**
     * Determine if time limit has elapsed based on server authority.
     */
    public function hasExpired(): bool
    {
        if ($this->isExpired()) {
            return true;
        }

        if ($this->expires_at === null) {
            return false;
        }

        return Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Seconds remaining before attempt expires (0 if expired or no timer).
     */
    public function getTimeRemainingSecondsAttribute(): ?int
    {
        if ($this->expires_at === null) {
            return null;
        }

        if ($this->hasExpired()) {
            return 0;
        }

        return max(0, Carbon::now()->diffInSeconds($this->expires_at, false));
    }
}
