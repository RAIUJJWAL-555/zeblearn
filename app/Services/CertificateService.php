<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateService
{
    /**
     * Check if a student is eligible for a course completion certificate.
     */
    public function isEligible(User $user, Course $course, ?Enrollment $enrollment = null): bool
    {
        // 1. Student must be active
        if (! $user->isStudent() || ! $user->isActive()) {
            return false;
        }

        // 2. Must have an approved enrollment
        $activeEnrollment = $enrollment ?? Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->first();

        if (! $activeEnrollment || $activeEnrollment->status !== Enrollment::STATUS_APPROVED) {
            return false;
        }

        // 3. Must not already have a certificate for this enrollment
        if (Certificate::where('enrollment_id', $activeEnrollment->id)->exists()) {
            return false;
        }

        // 4. Course must have at least 1 published lesson (cannot issue for empty courses)
        $totalPublishedLessons = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('status', 'published');
        })->where('status', 'published')->count();

        if ($totalPublishedLessons === 0) {
            return false;
        }

        // 5. All published lessons must be completed
        $completedLessonsCount = LessonProgress::where('user_id', $user->id)
            ->where('status', LessonProgress::STATUS_COMPLETED)
            ->whereHas('lesson', function ($lQ) use ($course) {
                $lQ->where('status', 'published')
                    ->whereHas('module', function ($mQ) use ($course) {
                        $mQ->where('course_id', $course->id)->where('status', 'published');
                    });
            })
            ->count();

        return $completedLessonsCount >= $totalPublishedLessons;
    }

    /**
     * Calculate published lesson completion stats for a course.
     *
     * @return array{
     *     total_lessons: int,
     *     completed_lessons: int,
     *     percentage: int,
     *     is_completed: bool
     * }
     */
    public function getCompletionStats(User $user, Course $course): array
    {
        $totalPublishedLessons = Lesson::whereHas('module', function ($q) use ($course) {
            $q->where('course_id', $course->id)->where('status', 'published');
        })->where('status', 'published')->count();

        if ($totalPublishedLessons === 0) {
            return [
                'total_lessons' => 0,
                'completed_lessons' => 0,
                'percentage' => 0,
                'is_completed' => false,
            ];
        }

        $completedLessonsCount = LessonProgress::where('user_id', $user->id)
            ->where('status', LessonProgress::STATUS_COMPLETED)
            ->whereHas('lesson', function ($lQ) use ($course) {
                $lQ->where('status', 'published')
                    ->whereHas('module', function ($mQ) use ($course) {
                        $mQ->where('course_id', $course->id)->where('status', 'published');
                    });
            })
            ->count();

        $percentage = (int) round(($completedLessonsCount / $totalPublishedLessons) * 100);

        return [
            'total_lessons' => $totalPublishedLessons,
            'completed_lessons' => $completedLessonsCount,
            'percentage' => min(100, $percentage),
            'is_completed' => $completedLessonsCount >= $totalPublishedLessons,
        ];
    }

    /**
     * Generate a unique certificate identifier.
     * Format: ZEB-YYYY-XXXXXXXX
     */
    public function generateCertificateNumber(): string
    {
        $year = date('Y');

        do {
            $random = Str::upper(Str::random(8));
            $certificateNumber = "ZEB-{$year}-{$random}";
        } while (Certificate::where('certificate_number', $certificateNumber)->exists());

        return $certificateNumber;
    }

    /**
     * Issue a completion certificate idempotently and thread-safely.
     */
    public function issueCertificate(User $user, Course $course, ?Enrollment $enrollment = null): ?Certificate
    {
        $targetEnrollment = $enrollment ?? Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->first();

        if (! $targetEnrollment || $targetEnrollment->status !== Enrollment::STATUS_APPROVED) {
            return null;
        }

        $wasNewlyIssued = false;

        try {
            $certificate = DB::transaction(function () use ($user, $course, $targetEnrollment, &$wasNewlyIssued) {
                // Pessimistic lock on enrollment record
                $lockedEnrollment = Enrollment::where('id', $targetEnrollment->id)
                    ->lockForUpdate()
                    ->first();

                if (! $lockedEnrollment || $lockedEnrollment->status !== Enrollment::STATUS_APPROVED) {
                    return null;
                }

                // If certificate already exists, return existing certificate (idempotent)
                $existing = Certificate::where('enrollment_id', $lockedEnrollment->id)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                // Verify completion eligibility under transaction lock
                if (! $this->isEligible($user, $course, $lockedEnrollment)) {
                    return null;
                }

                $certificateNumber = $this->generateCertificateNumber();
                $wasNewlyIssued = true;

                return Certificate::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'enrollment_id' => $lockedEnrollment->id,
                    'certificate_number' => $certificateNumber,
                    'issued_at' => now(),
                ]);
            });

            if ($certificate && $wasNewlyIssued) {
                app(NotificationService::class)->notifyCertificateIssued($certificate);
            }

            return $certificate;
        } catch (QueryException $e) {
            // Handle duplicate entry race condition gracefully
            $existing = Certificate::where('enrollment_id', $targetEnrollment->id)->first();
            if ($existing) {
                return $existing;
            }

            Log::error('Certificate issuance failed: '.$e->getMessage(), [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);

            return null;
        }
    }

    /**
     * Revoke an issued certificate with a mandatory reason.
     */
    public function revokeCertificate(Certificate $certificate, string $reason, ?User $admin = null): Certificate
    {
        $cleanReason = trim($reason);

        if (empty($cleanReason)) {
            throw new InvalidArgumentException('A reason is mandatory to revoke a certificate.');
        }

        $certificate->update([
            'revoked_at' => now(),
            'revocation_reason' => $cleanReason,
        ]);

        Log::info("Certificate {$certificate->certificate_number} was revoked.", [
            'certificate_id' => $certificate->id,
            'revoked_by' => $admin?->id,
            'reason' => $cleanReason,
        ]);

        return $certificate->fresh();
    }

    /**
     * Non-blocking automated issuance hook invoked upon lesson completion.
     */
    public function checkAndIssueOnLessonCompletion(User $user, Course $course): ?Certificate
    {
        try {
            if ($this->isEligible($user, $course)) {
                return $this->issueCertificate($user, $course);
            }
        } catch (\Throwable $e) {
            // Fail-safe: log without interrupting lesson completion workflow
            Log::error('Automated certificate check on lesson completion encountered an exception: '.$e->getMessage(), [
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
        }

        return null;
    }

    /**
     * Retroactively scan and issue certificates for all eligible enrollments.
     */
    public function issueAllEligibleCertificates(): int
    {
        $approvedEnrollments = Enrollment::where('status', Enrollment::STATUS_APPROVED)
            ->whereDoesntHave('certificate')
            ->with(['user', 'course'])
            ->get();

        $issuedCount = 0;

        foreach ($approvedEnrollments as $enrollment) {
            if (! $enrollment->user || ! $enrollment->course) {
                continue;
            }

            $certificate = $this->issueCertificate($enrollment->user, $enrollment->course, $enrollment);
            if ($certificate) {
                $issuedCount++;
            }
        }

        return $issuedCount;
    }

    /**
     * Render the landscape PDF certificate instance.
     */
    public function generatePdf(Certificate $certificate): \Barryvdh\DomPDF\PDF
    {
        $certificate->loadMissing(['user', 'course']);

        $verificationUrl = $certificate->verificationUrl();

        // Suppress deprecation warnings from nested QR code library in PHP 8.4
        $oldErrorReporting = error_reporting(error_reporting() & ~E_DEPRECATED);
        try {
            $qrSvg = (string) QrCode::format('svg')
                ->size(110)
                ->errorCorrection('M')
                ->generate($verificationUrl);
            $qrCodeBase64 = 'data:image/svg+xml;base64,'.base64_encode($qrSvg);
        } catch (\Throwable $e) {
            Log::warning('QR code generation failed, using fallback: '.$e->getMessage());
            $qrCodeBase64 = null;
        } finally {
            error_reporting($oldErrorReporting);
        }

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'student' => $certificate->user,
            'course' => $certificate->course,
            'verificationUrl' => $verificationUrl,
            'qrCodeBase64' => $qrCodeBase64,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
        $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);

        return $pdf;
    }
}
