<?php

namespace App\Services;

use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\AssignmentGradedNotification;
use App\Notifications\AssignmentResubmissionRequestedNotification;
use App\Notifications\CertificateIssuedNotification;
use App\Notifications\CourseAssignedNotification;
use App\Notifications\EnrollmentApprovedNotification;
use App\Notifications\EnrollmentRejectedNotification;
use App\Notifications\NewAssignmentSubmissionNotification;
use App\Notifications\NewEnrollmentRequestNotification;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\PaymentReceivedAdminNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify student that their course enrollment was approved.
     */
    public function notifyStudentEnrollmentApproved(Enrollment $enrollment): void
    {
        $this->safeDispatch(function () use ($enrollment) {
            $student = $enrollment->user;
            if (! $student || ! $student->isActive()) {
                return;
            }

            if ($this->hasRecentNotification($student, EnrollmentApprovedNotification::class, ['enrollment_id' => $enrollment->id])) {
                return;
            }

            $student->notify(new EnrollmentApprovedNotification($enrollment));
        });
    }

    /**
     * Notify student that their course enrollment was rejected.
     */
    public function notifyStudentEnrollmentRejected(Enrollment $enrollment, ?string $reason = null): void
    {
        $this->safeDispatch(function () use ($enrollment, $reason) {
            $student = $enrollment->user;
            if (! $student || ! $student->isActive()) {
                return;
            }

            $student->notify(new EnrollmentRejectedNotification($enrollment, $reason));
        });
    }

    /**
     * Notify administrators of a new course enrollment request.
     */
    public function notifyAdminsNewEnrollment(Enrollment $enrollment): void
    {
        $this->safeDispatch(function () use ($enrollment) {
            $admins = User::where('role', User::ROLE_ADMIN)
                ->where('status', User::STATUS_ACTIVE)
                ->get();

            foreach ($admins as $admin) {
                if ($this->hasRecentNotification($admin, NewEnrollmentRequestNotification::class, ['enrollment_id' => $enrollment->id])) {
                    continue;
                }

                $admin->notify(new NewEnrollmentRequestNotification($enrollment));
            }
        });
    }

    /**
     * Notify student and administrators when a payment is successful.
     */
    public function notifyPaymentSuccess(Payment $payment): void
    {
        $this->safeDispatch(function () use ($payment) {
            $student = $payment->user;

            if ($student && $student->isActive()) {
                if (! $this->hasRecentNotification($student, PaymentConfirmedNotification::class, ['payment_id' => $payment->id])) {
                    $student->notify(new PaymentConfirmedNotification($payment));
                }
            }

            // Alert active administrators
            $admins = User::where('role', User::ROLE_ADMIN)
                ->where('status', User::STATUS_ACTIVE)
                ->get();

            foreach ($admins as $admin) {
                if ($this->hasRecentNotification($admin, PaymentReceivedAdminNotification::class, ['payment_id' => $payment->id])) {
                    continue;
                }

                $admin->notify(new PaymentReceivedAdminNotification($payment));
            }
        });
    }

    /**
     * Notify student when a payment attempt fails.
     */
    public function notifyPaymentFailed(Payment $payment, ?string $reason = null): void
    {
        $this->safeDispatch(function () use ($payment, $reason) {
            $student = $payment->user;
            if (! $student || ! $student->isActive()) {
                return;
            }

            $student->notify(new PaymentFailedNotification($payment, $reason));
        });
    }

    /**
     * Notify student when their certificate of completion is issued.
     */
    public function notifyCertificateIssued(Certificate $certificate): void
    {
        $this->safeDispatch(function () use ($certificate) {
            $student = $certificate->user;
            if (! $student || ! $student->isActive()) {
                return;
            }

            if ($this->hasRecentNotification($student, CertificateIssuedNotification::class, ['certificate_id' => $certificate->id])) {
                return;
            }

            $student->notify(new CertificateIssuedNotification($certificate));
        });
    }

    /**
     * Notify student that their assignment submission was graded.
     */
    public function notifyAssignmentGraded(AssignmentSubmission $submission): void
    {
        $this->safeDispatch(function () use ($submission) {
            $student = $submission->user;
            if (! $student || ! $student->isActive()) {
                return;
            }

            $student->notify(new AssignmentGradedNotification($submission));
        });
    }

    /**
     * Notify student that resubmission was requested.
     */
    public function notifyAssignmentResubmissionRequested(AssignmentSubmission $submission): void
    {
        $this->safeDispatch(function () use ($submission) {
            $student = $submission->user;
            if (! $student || ! $student->isActive()) {
                return;
            }

            $student->notify(new AssignmentResubmissionRequestedNotification($submission));
        });
    }

    /**
     * Notify assigned instructors and admins when a new assignment submission is received.
     */
    public function notifyInstructorsNewSubmission(AssignmentSubmission $submission): void
    {
        $this->safeDispatch(function () use ($submission) {
            $course = $submission->assignment?->course;
            if (! $course) {
                return;
            }

            $instructors = $course->instructors()
                ->where('users.status', User::STATUS_ACTIVE)
                ->get();

            // Notify assigned instructors
            foreach ($instructors as $instructor) {
                $instructor->notify(new NewAssignmentSubmissionNotification($submission));
            }

            // If no instructors assigned to this course, fallback to alerting admins
            if ($instructors->isEmpty()) {
                $admins = User::where('role', User::ROLE_ADMIN)
                    ->where('status', User::STATUS_ACTIVE)
                    ->get();

                foreach ($admins as $admin) {
                    $admin->notify(new NewAssignmentSubmissionNotification($submission));
                }
            }
        });
    }

    /**
     * Notify an instructor when assigned to a course.
     */
    public function notifyInstructorCourseAssigned(Course $course, User $instructor, ?string $roleOrTitle = null): void
    {
        $this->safeDispatch(function () use ($course, $instructor, $roleOrTitle) {
            if (! $instructor->isActive() || ! $instructor->isInstructor()) {
                return;
            }

            if ($this->hasRecentNotification($instructor, CourseAssignedNotification::class, ['course_id' => $course->id])) {
                return;
            }

            $instructor->notify(new CourseAssignedNotification($course, $roleOrTitle));
        });
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Get recent notifications preview for dropdown.
     */
    public function getRecentNotifications(User $user, int $limit = 5): Collection
    {
        return $user->notifications()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Mark a specific notification as read, ensuring strict ownership.
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        /** @var DatabaseNotification|null $notification */
        $notification = $user->notifications()->where('id', $notificationId)->first();

        if (! $notification) {
            return false;
        }

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return true;
    }

    /**
     * Mark all notifications of a user as read.
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Check if a recent identical notification already exists within 5 minutes.
     *
     * @param  array<string, mixed>  $metadataKeys
     */
    protected function hasRecentNotification(User $user, string $notificationClass, array $metadataKeys = []): bool
    {
        $query = $user->notifications()
            ->where('type', $notificationClass)
            ->where('created_at', '>=', now()->subMinutes(5));

        foreach ($metadataKeys as $key => $val) {
            $query->where("data->metadata->{$key}", $val);
        }

        return $query->exists();
    }

    /**
     * Fail-safe execution wrapper to prevent notification exceptions from breaking core transactions.
     *
     * @param  callable(): void  $callback
     */
    protected function safeDispatch(callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            Log::warning('LMS Notification dispatch failed: '.$e->getMessage(), [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
