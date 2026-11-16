<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentFile;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\User;
use App\Services\AssignmentService;
use App\Services\InstructorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class InstructorAssignmentController extends Controller
{
    public function __construct(
        protected InstructorService $instructorService,
        protected AssignmentService $assignmentService
    ) {}

    /**
     * Display a listing of assignment submissions for the instructor's assigned courses.
     */
    public function index(Request $request): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        $assignedCourseIds = $instructor->assignedCourses()->pluck('courses.id');

        $submissionsQuery = AssignmentSubmission::whereHas('assignment', function ($q) use ($assignedCourseIds) {
            $q->whereIn('course_id', $assignedCourseIds);
        })
            ->with(['assignment.course', 'student', 'grader'])
            ->latest('submitted_at');

        // Filter by course
        if ($courseId = $request->input('course_id')) {
            if ($assignedCourseIds->contains($courseId)) {
                $submissionsQuery->whereHas('assignment', function ($q) use ($courseId) {
                    $q->where('course_id', $courseId);
                });
            }
        }

        // Filter by grading status
        if ($status = $request->input('status')) {
            if (in_array($status, [
                AssignmentSubmission::STATUS_SUBMITTED,
                AssignmentSubmission::STATUS_UNDER_REVIEW,
                AssignmentSubmission::STATUS_GRADED,
                AssignmentSubmission::STATUS_RESUBMISSION_REQUESTED,
            ], true)) {
                $submissionsQuery->where('status', $status);
            }
        }

        // Search by student name
        if ($studentSearch = trim((string) $request->input('student'))) {
            $submissionsQuery->whereHas('student', function ($q) use ($studentSearch) {
                $q->where('name', 'like', "%{$studentSearch}%")
                    ->orWhere('email', 'like', "%{$studentSearch}%");
            });
        }

        $submissions = $submissionsQuery->paginate(15)->withQueryString();

        // Metrics for filter tabs
        $totalSubmissions = $assignedCourseIds->isNotEmpty()
            ? AssignmentSubmission::whereHas('assignment', fn ($q) => $q->whereIn('course_id', $assignedCourseIds))->count()
            : 0;

        $pendingSubmissions = $assignedCourseIds->isNotEmpty()
            ? AssignmentSubmission::whereHas('assignment', fn ($q) => $q->whereIn('course_id', $assignedCourseIds))
                ->whereIn('status', [AssignmentSubmission::STATUS_SUBMITTED, AssignmentSubmission::STATUS_UNDER_REVIEW])
                ->count()
            : 0;

        $gradedSubmissions = $assignedCourseIds->isNotEmpty()
            ? AssignmentSubmission::whereHas('assignment', fn ($q) => $q->whereIn('course_id', $assignedCourseIds))
                ->where('status', AssignmentSubmission::STATUS_GRADED)
                ->count()
            : 0;

        $assignedCourses = $instructor->assignedCourses()->orderBy('name')->get();

        return view('instructor.assignments.index', [
            'instructor' => $instructor,
            'submissions' => $submissions,
            'assignedCourses' => $assignedCourses,
            'totalSubmissions' => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'gradedSubmissions' => $gradedSubmissions,
            'currentCourse' => $request->input('course_id'),
            'currentStatus' => $request->input('status'),
            'currentSearch' => $request->input('student'),
        ]);
    }

    /**
     * Show a specific student submission for review and grading.
     */
    public function submissionShow(Request $request, Assignment $assignment, AssignmentSubmission $submission): View
    {
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);

        /** @var User $instructor */
        $instructor = $request->user();

        if (! $this->instructorService->canInstructorGradeSubmission($instructor, $submission)) {
            abort(403, 'Unauthorized access. You are not assigned to the course for this assignment.');
        }

        $submission->load(['user', 'grader', 'files', 'assignment.course']);

        return view('instructor.assignments.show', [
            'instructor' => $instructor,
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    /**
     * Grade or request resubmission for a student submission.
     */
    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission): RedirectResponse
    {
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);

        /** @var User $instructor */
        $instructor = $request->user();

        if (! $this->instructorService->canInstructorGradeSubmission($instructor, $submission)) {
            abort(403, 'Unauthorized. You cannot grade submissions for a course not assigned to you.');
        }

        $validated = $request->validate([
            'marks_awarded' => ['required', 'integer', 'min:0', "max:{$assignment->max_marks}"],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'private_notes' => ['nullable', 'string', 'max:5000'],
            'action' => ['nullable', 'string', 'in:grade,resubmission'],
        ]);

        if (($validated['action'] ?? 'grade') === 'resubmission') {
            $this->assignmentService->requestResubmission(
                $instructor,
                $submission,
                $validated['feedback'] ?? null,
                $validated['private_notes'] ?? null
            );

            return redirect()->route('instructor.assignments.submissions.show', [$assignment, $submission])
                ->with('info', 'Resubmission requested from student.');
        }

        $this->assignmentService->gradeSubmission(
            $instructor,
            $submission,
            (int) $validated['marks_awarded'],
            $validated['feedback'] ?? null,
            $validated['private_notes'] ?? null
        );

        return redirect()->route('instructor.assignments.submissions.show', [$assignment, $submission])
            ->with('success', "Submission successfully graded with {$validated['marks_awarded']}/{$assignment->max_marks} marks.");
    }

    /**
     * Securely download a student submission file.
     */
    public function downloadFile(
        Request $request,
        Assignment $assignment,
        AssignmentSubmission $submission,
        AssignmentFile $file
    ): Response {
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);
        abort_if((int) $file->assignment_submission_id !== (int) $submission->id, 404);

        /** @var User $instructor */
        $instructor = $request->user();

        if (! $this->instructorService->canInstructorGradeSubmission($instructor, $submission)) {
            abort(403, 'Unauthorized download.');
        }

        return $this->assignmentService->downloadFile($file, $instructor);
    }
}
