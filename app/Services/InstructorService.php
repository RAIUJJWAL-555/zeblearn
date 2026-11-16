<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class InstructorService
{
    /**
     * Assign an instructor to a course safely, preventing duplicates.
     */
    public function assignInstructor(Course $course, User $instructor, ?string $roleOrTitle = 'Lead Instructor'): bool
    {
        if (! $instructor->isInstructor()) {
            return false;
        }

        if ($course->hasInstructor($instructor)) {
            return true;
        }

        $course->instructors()->attach($instructor->id, [
            'role_or_title' => $roleOrTitle ?: 'Lead Instructor',
            'assigned_at' => now(),
        ]);

        app(NotificationService::class)->notifyInstructorCourseAssigned($course, $instructor, $roleOrTitle);

        return true;
    }

    /**
     * Remove an instructor from a course.
     */
    public function unassignInstructor(Course $course, User $instructor): bool
    {
        $course->instructors()->detach($instructor->id);

        return true;
    }

    /**
     * Check if a user (instructor or administrator) has access to a given course.
     */
    public function canInstructorAccessCourse(User $user, Course $course): bool
    {
        if ($user->isAdmin() && $user->isActive()) {
            return true;
        }

        if ($user->isInstructor() && $user->isActive()) {
            return $user->isAssignedToCourse($course);
        }

        return false;
    }

    /**
     * Check if an instructor can review and grade a specific assignment submission.
     */
    public function canInstructorGradeSubmission(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->isAdmin() && $user->isActive()) {
            return true;
        }

        if ($user->isInstructor() && $user->isActive()) {
            $course = $submission->assignment?->course;

            return $course && $user->isAssignedToCourse($course);
        }

        return false;
    }

    /**
     * Determine if an instructor account can be safely deleted without breaking data integrity.
     *
     * @return array{can_delete: bool, reason: ?string}
     */
    public function canDeleteInstructor(User $instructor): array
    {
        if ($instructor->assignedCourses()->exists()) {
            $count = $instructor->assignedCourses()->count();

            return [
                'can_delete' => false,
                'reason' => "Cannot delete instructor while assigned to {$count} course(s). Please unassign the courses first or suspend the account.",
            ];
        }

        $gradedSubmissionsCount = AssignmentSubmission::where('grader_id', $instructor->id)->count();
        if ($gradedSubmissionsCount > 0) {
            return [
                'can_delete' => false,
                'reason' => "Cannot delete instructor who has graded {$gradedSubmissionsCount} assignment submission(s). Preserving academic integrity requires keeping historical grader records. Please suspend the account instead.",
            ];
        }

        return [
            'can_delete' => true,
            'reason' => null,
        ];
    }

    /**
     * Compile authentic operational metrics for an instructor dashboard.
     *
     * @return array<string, mixed>
     */
    public function getInstructorMetrics(User $instructor): array
    {
        $assignedCourses = $instructor->assignedCourses()
            ->with(['modules.lessons'])
            ->get();

        $courseIds = $assignedCourses->pluck('id');

        $totalCourses = $assignedCourses->count();
        $publishedCourses = $assignedCourses->filter(fn ($c) => $c->isAvailable() && $c->published_at !== null)->count();
        $draftCourses = $totalCourses - $publishedCourses;

        // Distinct approved students across assigned courses
        $totalEnrolledStudents = 0;
        $recentEnrollments = collect();

        if ($courseIds->isNotEmpty()) {
            $totalEnrolledStudents = Enrollment::whereIn('course_id', $courseIds)
                ->where('status', Enrollment::STATUS_APPROVED)
                ->distinct('user_id')
                ->count('user_id');

            $recentEnrollments = Enrollment::whereIn('course_id', $courseIds)
                ->with(['user', 'course'])
                ->latest('requested_at')
                ->take(5)
                ->get();
        }

        // Pending assignment submissions requiring review
        $pendingSubmissionsQuery = AssignmentSubmission::whereHas('assignment', function ($q) use ($courseIds) {
            $q->whereIn('course_id', $courseIds);
        })->whereIn('status', [
            AssignmentSubmission::STATUS_SUBMITTED,
            AssignmentSubmission::STATUS_UNDER_REVIEW,
        ]);

        $pendingAssignmentsCount = $courseIds->isNotEmpty() ? $pendingSubmissionsQuery->count() : 0;

        $recentSubmissions = $courseIds->isNotEmpty()
            ? AssignmentSubmission::whereHas('assignment', function ($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })
                ->with(['assignment.course', 'student'])
                ->latest('submitted_at')
                ->take(6)
                ->get()
            : collect();

        return [
            'assignedCourses' => $assignedCourses,
            'courseIds' => $courseIds,
            'totalCourses' => $totalCourses,
            'publishedCourses' => $publishedCourses,
            'draftCourses' => $draftCourses,
            'totalEnrolledStudents' => $totalEnrolledStudents,
            'pendingAssignmentsCount' => $pendingAssignmentsCount,
            'recentSubmissions' => $recentSubmissions,
            'recentEnrollments' => $recentEnrollments,
        ];
    }
}
