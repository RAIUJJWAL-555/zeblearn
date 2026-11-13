<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use App\Models\Course;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentAssignmentController extends Controller
{
    public function __construct(
        protected AssignmentService $assignmentService
    ) {}

    /**
     * Display assignments available within the enrolled course.
     */
    public function index(Request $request, Course $course): View
    {
        $user = $request->user();

        if (! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment in this course to view assignments.');
        }

        $assignments = Assignment::published()
            ->where('course_id', $course->id)
            ->with(['module', 'lesson'])
            ->withCount(['submissions as user_submissions_count' => fn ($q) => $q->where('user_id', $user->id)])
            ->latest('id')
            ->get();

        // Map student submissions for each assignment
        $userSubmissions = AssignmentSubmission::where('user_id', $user->id)
            ->whereIn('assignment_id', $assignments->pluck('id'))
            ->latest('attempt_number')
            ->get()
            ->groupBy('assignment_id');

        return view('student.assignments.index', [
            'course' => $course,
            'assignments' => $assignments,
            'userSubmissions' => $userSubmissions,
        ]);
    }

    /**
     * Display assignment instructions, rules, and submission console.
     */
    public function show(Request $request, Course $course, Assignment $assignment): View
    {
        abort_if((int) $assignment->course_id !== (int) $course->id, 404);

        $user = $request->user();
        $this->assignmentService->validateStudentAccess($user, $assignment, true);

        $submissions = $assignment->submissions()
            ->where('user_id', $user->id)
            ->with(['files'])
            ->latest('attempt_number')
            ->get();

        $latestSubmission = $submissions->first();
        $canSubmit = $assignment->canUserSubmit($user);
        $remainingAttempts = $assignment->remainingAttemptsForUser($user);

        return view('student.assignments.show', [
            'course' => $course,
            'assignment' => $assignment,
            'submissions' => $submissions,
            'latestSubmission' => $latestSubmission,
            'canSubmit' => $canSubmit,
            'remainingAttempts' => $remainingAttempts,
        ]);
    }

    /**
     * Submit an assignment response.
     */
    public function submit(Request $request, Course $course, Assignment $assignment): RedirectResponse|JsonResponse
    {
        abort_if((int) $assignment->course_id !== (int) $course->id, 404);

        $user = $request->user();

        $request->validate([
            'submission_text' => ['nullable', 'string', 'max:50000'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'max:'.($assignment->max_file_size_mb * 1024)],
        ]);

        $submissionText = $request->input('submission_text');
        $files = $request->file('files') ?? [];

        $submission = $this->assignmentService->submitAssignment(
            $user,
            $assignment,
            $submissionText,
            is_array($files) ? $files : [$files]
        );

        $statusMsg = $submission->is_late
            ? 'Assignment submitted successfully (marked late).'
            : 'Assignment submitted successfully.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $statusMsg,
                'submission' => $submission,
            ]);
        }

        return redirect()->route('student.assignments.show', [$course, $assignment])
            ->with('success', $statusMsg);
    }

    /**
     * View an individual submission and grading result.
     */
    public function submissionShow(Request $request, Course $course, Assignment $assignment, AssignmentSubmission $submission): View
    {
        abort_if((int) $assignment->course_id !== (int) $course->id, 404);
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);
        abort_if((int) $submission->user_id !== (int) $request->user()->id, 403, 'Unauthorized access to submission.');

        $submission->load(['files']);

        return view('student.assignments.submission-show', [
            'course' => $course,
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    /**
     * Securely download an uploaded submission file.
     */
    public function downloadFile(
        Request $request,
        Course $course,
        Assignment $assignment,
        AssignmentSubmission $submission,
        AssignmentSubmissionFile $file
    ): StreamedResponse {
        abort_if((int) $assignment->course_id !== (int) $course->id, 404);
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);
        abort_if((int) $file->assignment_submission_id !== (int) $submission->id, 404);
        abort_if((int) $submission->user_id !== (int) $request->user()->id, 403, 'Unauthorized file download.');

        return $this->assignmentService->downloadSubmissionFile($request->user(), $file);
    }
}
