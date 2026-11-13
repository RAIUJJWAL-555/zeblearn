<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentSubmission extends Model
{
    use HasFactory;

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_GRADED = 'graded';

    public const STATUS_RESUBMISSION_REQUIRED = 'resubmission_required';

    public const STATUSES = [
        self::STATUS_SUBMITTED,
        self::STATUS_UNDER_REVIEW,
        self::STATUS_GRADED,
        self::STATUS_RESUBMISSION_REQUIRED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'assignment_id',
        'user_id',
        'attempt_number',
        'status',
        'submission_text',
        'submitted_at',
        'is_late',
        'marks_awarded',
        'grader_id',
        'graded_at',
        'feedback',
        'private_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attempt_number' => 'integer',
        'submitted_at' => 'datetime',
        'is_late' => 'boolean',
        'marks_awarded' => 'integer',
        'graded_at' => 'datetime',
    ];

    /**
     * Get the assignment this submission belongs to.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the student who submitted.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instructor or administrator who graded this submission.
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'grader_id');
    }

    /**
     * Get all uploaded files attached to this submission.
     */
    public function files(): HasMany
    {
        return $this->hasMany(AssignmentSubmissionFile::class);
    }

    /**
     * Scope for graded submissions.
     */
    public function scopeGraded(Builder $query): Builder
    {
        return $query->where($this->qualifyColumn('status'), self::STATUS_GRADED);
    }

    /**
     * Scope for submissions awaiting grading.
     */
    public function scopePendingGrading(Builder $query): Builder
    {
        return $query->whereIn($this->qualifyColumn('status'), [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW]);
    }

    public function isGraded(): bool
    {
        return $this->status === self::STATUS_GRADED;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW]);
    }

    public function isResubmissionRequired(): bool
    {
        return $this->status === self::STATUS_RESUBMISSION_REQUIRED;
    }

    /**
     * Calculate score percentage.
     */
    public function getPercentageAttribute(): ?int
    {
        if ($this->marks_awarded === null || ! $this->assignment) {
            return null;
        }

        $max = $this->assignment->max_marks;
        if ($max <= 0) {
            return 0;
        }

        return (int) round(($this->marks_awarded / $max) * 100);
    }

    /**
     * Check if passed passing threshold if defined on assignment.
     */
    public function getIsPassedAttribute(): ?bool
    {
        if ($this->marks_awarded === null || ! $this->assignment) {
            return null;
        }

        if ($this->assignment->passing_marks === null) {
            return true;
        }

        return $this->marks_awarded >= $this->assignment->passing_marks;
    }

    /**
     * Clean status label for presentation.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_GRADED => 'Graded',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_RESUBMISSION_REQUIRED => 'Resubmission Requested',
            default => 'Submitted',
        };
    }
}
