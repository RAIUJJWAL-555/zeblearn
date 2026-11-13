<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use App\Models\Enrollment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentService
{
    /**
     * Validate student eligibility and enrollment for an assignment.
     */
    public function validateStudentAccess(User $user, Assignment $assignment, bool $requirePublished = true): void
    {
        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Only active students can access assignments.');
        }

        $course = $assignment->course;

        if (! $course || ! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment in this course to access this assignment.');
        }

        if (! $course->isAvailableForEnrollment()) {
            abort(404, 'This course is currently not published or available.');
        }

        if ($requirePublished && ! $assignment->isPublished()) {
            abort(404, 'This assignment is currently not published.');
        }

        if ($assignment->course_module_id) {
            $module = $assignment->module;
            if (! $module || (int) $module->course_id !== (int) $course->id || ($requirePublished && ! $module->isPublished())) {
                abort(404, 'The module associated with this assignment is not available.');
            }
        }

        if ($assignment->lesson_id) {
            $lesson = $assignment->lesson;
            if (! $lesson || (int) $lesson->course_module_id !== (int) $assignment->course_module_id || ($requirePublished && ! $lesson->isPublished())) {
                abort(404, 'The lesson associated with this assignment is not available.');
            }
        }
    }

    /**
     * Submit an assignment response with optional files under strict transaction locking.
     *
     * @param  list<UploadedFile>  $uploadedFiles
     */
    public function submitAssignment(User $user, Assignment $assignment, ?string $submissionText = null, array $uploadedFiles = []): AssignmentSubmission
    {
        $this->validateStudentAccess($user, $assignment, true);

        // Pre-validate deadline policies
        if ($assignment->isBeforeStart()) {
            abort(422, 'Submissions for this assignment are not open yet.');
        }

        if ($assignment->isPastDue() && ! $assignment->allow_late_submissions) {
            abort(422, 'The due date for this assignment has passed and late submissions are not allowed.');
        }

        // Validate submission payload content
        $hasText = ! empty(trim((string) $submissionText));
        $hasFiles = ! empty($uploadedFiles);

        if (! $hasText && ! $hasFiles) {
            abort(422, 'You must provide either a written text response or upload at least one file.');
        }

        if (! $assignment->allow_text_submission && $hasText && ! $hasFiles) {
            abort(422, 'Text submissions are disabled for this assignment.');
        }

        if (! $assignment->allow_file_submission && $hasFiles) {
            abort(422, 'File uploads are disabled for this assignment.');
        }

        // Validate uploaded files before transaction
        $allowedExtensions = $assignment->getAllowedFileExtensions();
        $maxSizeBytes = $assignment->max_file_size_mb * 1024 * 1024;

        foreach ($uploadedFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                abort(422, 'One or more uploaded files are invalid or corrupted.');
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, $allowedExtensions, true)) {
                abort(422, "File type '.{$ext}' is not permitted. Allowed extensions: ".implode(', ', $allowedExtensions));
            }

            if ($file->getSize() > $maxSizeBytes) {
                abort(422, "File '{$file->getClientOriginalName()}' exceeds the maximum allowed size of {$assignment->max_file_size_mb} MB.");
            }
        }

        return DB::transaction(function () use ($user, $assignment, $submissionText, $uploadedFiles) {
            // Row-level lock on student's enrollment to serialize concurrent submission attempts
            Enrollment::where('user_id', $user->id)
                ->where('course_id', $assignment->course_id)
                ->lockForUpdate()
                ->first();

            // Count attempts under lock
            $usedAttempts = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->count();

            if ($usedAttempts >= $assignment->max_attempts) {
                abort(403, "You have reached the maximum allowed attempts ({$assignment->max_attempts}) for this assignment.");
            }

            $now = Carbon::now();
            $isLate = $assignment->isPastDue();

            $submission = AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'user_id' => $user->id,
                'attempt_number' => $usedAttempts + 1,
                'status' => AssignmentSubmission::STATUS_SUBMITTED,
                'submission_text' => $submissionText ? trim($submissionText) : null,
                'submitted_at' => $now,
                'is_late' => $isLate,
            ]);

            // Store files on private disk outside public directory
            foreach ($uploadedFiles as $file) {
                $originalName = $file->getClientOriginalName();
                $cleanExtension = strtolower($file->getClientOriginalExtension());
                $randomFilename = Str::random(40).'.'.$cleanExtension;
                $storageDir = "assignments/{$assignment->id}/{$submission->id}";

                $storedPath = $file->storeAs($storageDir, $randomFilename, 'local');

                AssignmentSubmissionFile::create([
                    'assignment_submission_id' => $submission->id,
                    'original_filename' => $originalName,
                    'stored_path' => $storedPath,
                    'mime_type' => $file->getClientMimeType() ?: 'application/octet-stream',
                    'file_size' => $file->getSize(),
                    'disk' => 'local',
                ]);
            }

            return $submission->load('files');
        });
    }

    /**
     * Grade a student submission.
     */
    public function gradeSubmission(
        User $grader,
        AssignmentSubmission $submission,
        int $marksAwarded,
        ?string $feedback = null,
        ?string $privateNotes = null,
        string $status = AssignmentSubmission::STATUS_GRADED
    ): AssignmentSubmission {
        if (! $grader->isAdmin() && ! $grader->isInstructor()) {
            abort(403, 'Only administrators or instructors can grade submissions.');
        }

        $assignment = $submission->assignment;

        if ($marksAwarded < 0 || $marksAwarded > $assignment->max_marks) {
            abort(422, "Marks awarded must be between 0 and {$assignment->max_marks}.");
        }

        $submission->update([
            'marks_awarded' => $marksAwarded,
            'grader_id' => $grader->id,
            'graded_at' => Carbon::now(),
            'feedback' => $feedback ? trim($feedback) : null,
            'private_notes' => $privateNotes ? trim($privateNotes) : null,
            'status' => $status,
        ]);

        return $submission->fresh(['assignment', 'user', 'grader', 'files']);
    }

    /**
     * Request resubmission if assignment max attempts allows another attempt.
     */
    public function requestResubmission(
        User $grader,
        AssignmentSubmission $submission,
        ?string $feedback = null,
        ?string $privateNotes = null
    ): AssignmentSubmission {
        if (! $grader->isAdmin() && ! $grader->isInstructor()) {
            abort(403, 'Only administrators or instructors can request resubmission.');
        }

        $submission->update([
            'status' => AssignmentSubmission::STATUS_RESUBMISSION_REQUIRED,
            'grader_id' => $grader->id,
            'graded_at' => Carbon::now(),
            'feedback' => $feedback ? trim($feedback) : null,
            'private_notes' => $privateNotes ? trim($privateNotes) : null,
        ]);

        return $submission->fresh(['assignment', 'user', 'grader', 'files']);
    }

    /**
     * Securely download an assignment submission file with strict authorization.
     */
    public function downloadSubmissionFile(User $user, AssignmentSubmissionFile $file): StreamedResponse
    {
        $submission = $file->submission;

        if (! $submission) {
            abort(404, 'Submission not found.');
        }

        // Authorization check: User must be admin/instructor, or the student who owns the submission
        $isOwner = (int) $submission->user_id === (int) $user->id;
        $isStaff = $user->isAdmin() || $user->isInstructor();

        if (! $isOwner && ! $isStaff) {
            abort(403, 'Unauthorized access to assignment submission file.');
        }

        if (! Storage::disk($file->disk)->exists($file->stored_path)) {
            abort(404, 'File not found on storage server.');
        }

        return Storage::disk($file->disk)->download($file->stored_path, $file->original_filename);
    }
}
