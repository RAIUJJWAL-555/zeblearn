<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'avatar_path',
    'bio',
    'preferences',
    'designation',
    'specialization',
    'experience_years',
    'qualification',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Supported user roles.
     */
    public const ROLE_ADMIN = 'admin';

    public const ROLE_INSTRUCTOR = 'instructor';

    public const ROLE_STUDENT = 'student';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_INSTRUCTOR,
        self::ROLE_STUDENT,
    ];

    /**
     * Supported account statuses.
     */
    public const STATUS_ACTIVE = 'active';

    public const STATUS_SUSPENDED = 'suspended';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_SUSPENDED,
    ];

    /**
     * The model's default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_STUDENT,
        'status' => self::STATUS_ACTIVE,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    /**
     * Get a specific user preference with an optional fallback default.
     */
    public function getPreference(string $key, mixed $default = null): mixed
    {
        return data_get($this->preferences, $key, $default);
    }

    /**
     * Set a specific user preference.
     */
    public function setPreference(string $key, mixed $value): void
    {
        $prefs = $this->preferences ?? [];
        data_set($prefs, $key, $value);
        $this->preferences = $prefs;
    }

    /**
     * Determine if a user account can be safely deleted or if historical records prevent deletion.
     *
     * @return array{can_delete: bool, reason: ?string}
     */
    public function canDelete(): array
    {
        if ($this->enrollments()->exists()) {
            $count = $this->enrollments()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete user associated with {$count} course enrollment record(s). Please suspend the account instead to preserve enrollment history.",
            ];
        }

        if ($this->payments()->exists()) {
            $count = $this->payments()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete user associated with {$count} financial transaction record(s). Accounting integrity requires preserving payment history.",
            ];
        }

        if ($this->certificates()->exists()) {
            $count = $this->certificates()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete user holding {$count} verified course completion certificate(s). Credential audit trails must be retained.",
            ];
        }

        if ($this->quizAttempts()->exists()) {
            $count = $this->quizAttempts()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete user who has completed {$count} assessment attempt(s). Academic records must be preserved.",
            ];
        }

        if ($this->assignmentSubmissions()->exists()) {
            $count = $this->assignmentSubmissions()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete user with {$count} assignment coursework submission(s).",
            ];
        }

        if ($this->isInstructor() && $this->assignedCourses()->exists()) {
            $count = $this->assignedCourses()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete instructor while assigned to {$count} course(s). Unassign courses first or suspend the account.",
            ];
        }

        if (AssignmentSubmission::where('grader_id', $this->id)->exists()) {
            $count = AssignmentSubmission::where('grader_id', $this->id)->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete faculty member who has graded {$count} assignment submission(s). Grading audit history must be retained.",
            ];
        }

        if (Enrollment::where('reviewed_by', $this->id)->exists()) {
            $count = Enrollment::where('reviewed_by', $this->id)->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete administrator who reviewed {$count} student enrollment application(s).",
            ];
        }

        return [
            'can_delete' => true,
            'reason' => null,
        ];
    }

    /**
     * Check if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCourseEditor(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if the user is an instructor.
     */
    public function isInstructor(): bool
    {
        return $this->role === self::ROLE_INSTRUCTOR;
    }

    /**
     * Check if the user is a student.
     */
    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    /**
     * Check if the user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Get the default dashboard route URL based on role.
     */
    public function dashboardUrl(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => route('admin.dashboard'),
            self::ROLE_INSTRUCTOR => route('instructor.dashboard'),
            default => route('student.dashboard'),
        };
    }

    /**
     * Get the student's enrollments.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the student's payment transactions.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the student's lesson progress tracking records.
     */
    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Get the student's assignment submissions.
     */
    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    /**
     * Get the student's earned certificates.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the student's enrolled courses.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withPivot(['status', 'requested_at', 'approved_at', 'rejected_at', 'admin_note'])
            ->withTimestamps();
    }

    /**
     * Check if the user has an approved enrollment for a given course.
     */
    public function hasApprovedEnrollment(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->exists();
    }

    /**
     * Check if the user has a pending enrollment request for a given course.
     */
    public function hasPendingEnrollment(Course $course): bool
    {
        return $this->enrollments()
            ->where('course_id', $course->id)
            ->where('status', Enrollment::STATUS_PENDING)
            ->exists();
    }

    /**
     * Get the user's quiz attempts.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the courses assigned to this instructor.
     */
    public function assignedCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_instructors')
            ->withPivot(['role_or_title', 'assigned_at'])
            ->withTimestamps();
    }

    /**
     * Check if this instructor is assigned to a specific course.
     */
    public function isAssignedToCourse(Course $course): bool
    {
        return $this->assignedCourses()
            ->where('courses.id', $course->id)
            ->exists();
    }

    /**
     * Scope query to instructors only.
     */
    public function scopeInstructors(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_INSTRUCTOR);
    }

    /**
     * Scope query to active users only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get the resolved avatar URL or return null.
     */
    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        if (str_starts_with($this->avatar_path, 'http://') || str_starts_with($this->avatar_path, 'https://')) {
            return $this->avatar_path;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }
}
