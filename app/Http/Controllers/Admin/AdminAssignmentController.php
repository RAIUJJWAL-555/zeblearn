<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionFile;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Services\AssignmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAssignmentController extends Controller
{
    public function __construct(
        protected AssignmentService $assignmentService
    ) {}

    /**
     * Display a listing of assignments with filtering and analytics.
     */
    public function index(Request $request): View
    {
        $query = Assignment::with(['course', 'module', 'lesson', 'creator'])
            ->withCount('submissions');

        // Search by title
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter by course
        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by context
        if ($context = $request->input('context')) {
            if ($context === 'course') {
                $query->whereNull('course_module_id')->whereNull('lesson_id');
            } elseif ($context === 'module') {
                $query->whereNotNull('course_module_id')->whereNull('lesson_id');
            } elseif ($context === 'lesson') {
                $query->whereNotNull('lesson_id');
            }
        }

        $assignments = $query->latest('id')->paginate(15)->withQueryString();

        // High-level metrics
        $totalAssignments = Assignment::count();
        $publishedAssignments = Assignment::where('status', Assignment::STATUS_PUBLISHED)->count();
        $totalSubmissions = AssignmentSubmission::count();
        $pendingSubmissions = AssignmentSubmission::pendingGrading()->count();
        $gradedSubmissions = AssignmentSubmission::graded()->count();

        $courses = Course::orderBy('name')->get(['id', 'name']);

        return view('admin.assignments.index', [
            'assignments' => $assignments,
            'courses' => $courses,
            'totalAssignments' => $totalAssignments,
            'publishedAssignments' => $publishedAssignments,
            'totalSubmissions' => $totalSubmissions,
            'pendingSubmissions' => $pendingSubmissions,
            'gradedSubmissions' => $gradedSubmissions,
        ]);
    }

    /**
     * Show the form for creating a new assignment.
     */
    public function create(Request $request): View
    {
        $courses = Course::with([
            'modules' => fn ($q) => $q->orderBy('sort_order')->with([
                'lessons' => fn ($lq) => $lq->orderBy('sort_order'),
            ]),
        ])->orderBy('name')->get();

        return view('admin.assignments.create', [
            'courses' => $courses,
            'preselectedCourseId' => $request->input('course_id'),
            'preselectedModuleId' => $request->input('module_id'),
            'preselectedLessonId' => $request->input('lesson_id'),
        ]);
    }

    /**
     * Store a newly created assignment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAssignment($request);

        $course = Course::findOrFail($validated['course_id']);

        $moduleId = ! empty($validated['course_module_id']) ? (int) $validated['course_module_id'] : null;
        $lessonId = ! empty($validated['lesson_id']) ? (int) $validated['lesson_id'] : null;

        if ($moduleId) {
            $module = CourseModule::where('id', $moduleId)->where('course_id', $course->id)->first();
            if (! $module) {
                return back()->withInput()->withErrors(['course_module_id' => 'Selected module does not belong to the selected course.']);
            }
        }

        if ($lessonId) {
            if (! $moduleId) {
                return back()->withInput()->withErrors(['lesson_id' => 'A module must be selected when selecting a lesson.']);
            }

            $lesson = Lesson::where('id', $lessonId)->where('course_module_id', $moduleId)->first();
            if (! $lesson) {
                return back()->withInput()->withErrors(['lesson_id' => 'Selected lesson does not belong to the selected module.']);
            }
        }

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Assignment::where('course_id', $course->id)->where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-".$counter++;
        }

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'course_module_id' => $moduleId,
            'lesson_id' => $lessonId,
            'created_by' => $request->user()?->id,
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'max_marks' => (int) ($validated['max_marks'] ?? 100),
            'passing_marks' => ! empty($validated['passing_marks']) ? (int) $validated['passing_marks'] : null,
            'start_at' => ! empty($validated['start_at']) ? Carbon::parse($validated['start_at']) : null,
            'due_at' => ! empty($validated['due_at']) ? Carbon::parse($validated['due_at']) : null,
            'allow_late_submissions' => $request->boolean('allow_late_submissions'),
            'late_penalty_percent' => (int) ($validated['late_penalty_percent'] ?? 0),
            'max_attempts' => (int) ($validated['max_attempts'] ?? 1),
            'allow_text_submission' => $request->boolean('allow_text_submission', true),
            'allow_file_submission' => $request->boolean('allow_file_submission', true),
            'allowed_file_types' => $validated['allowed_file_types'] ?? 'pdf,doc,docx,zip,txt',
            'max_file_size_mb' => (int) ($validated['max_file_size_mb'] ?? 10),
            'status' => Assignment::STATUS_DRAFT,
        ]);

        return redirect()->route('admin.assignments.show', $assignment)
            ->with('success', "Assignment '{$assignment->title}' created successfully.");
    }

    /**
     * Display assignment console with student submissions.
     */
    public function show(Request $request, Assignment $assignment): View
    {
        $assignment->load(['course', 'module', 'lesson', 'creator']);

        $submissionsQuery = $assignment->submissions()
            ->with(['user', 'grader', 'files'])
            ->latest('submitted_at');

        if ($status = $request->input('status')) {
            $submissionsQuery->where('status', $status);
        }

        if ($search = $request->input('student')) {
            $submissionsQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $submissions = $submissionsQuery->paginate(20)->withQueryString();

        $stats = [
            'total' => $assignment->submissions()->count(),
            'pending' => $assignment->submissions()->pendingGrading()->count(),
            'graded' => $assignment->submissions()->graded()->count(),
            'avgScore' => $assignment->submissions()->graded()->avg('marks_awarded') ?? 0,
        ];

        return view('admin.assignments.show', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing an assignment.
     */
    public function edit(Assignment $assignment): View
    {
        $courses = Course::with([
            'modules' => fn ($q) => $q->orderBy('sort_order')->with([
                'lessons' => fn ($lq) => $lq->orderBy('sort_order'),
            ]),
        ])->orderBy('name')->get();

        return view('admin.assignments.edit', [
            'assignment' => $assignment,
            'courses' => $courses,
            'hasSubmissions' => $assignment->hasSubmissions(),
        ]);
    }

    /**
     * Update an assignment, strictly freezing structural configuration when submissions exist.
     */
    public function update(Request $request, Assignment $assignment): RedirectResponse|JsonResponse
    {
        $validated = $this->validateAssignment($request, $assignment->id);

        // If submissions exist, enforce strict structural freeze
        if ($assignment->hasSubmissions()) {
            $frozenErrors = [];

            if ($request->has('course_id') && (int) $request->input('course_id') !== (int) $assignment->course_id) {
                $frozenErrors['course_id'] = 'Course cannot be modified because student submissions already exist for this assignment.';
            }

            if ($request->has('course_module_id')) {
                $submittedModuleId = ! empty($request->input('course_module_id')) ? (int) $request->input('course_module_id') : null;
                if ($submittedModuleId !== $assignment->course_module_id) {
                    $frozenErrors['course_module_id'] = 'Module context cannot be modified because student submissions already exist.';
                }
            }

            if ($request->has('lesson_id')) {
                $submittedLessonId = ! empty($request->input('lesson_id')) ? (int) $request->input('lesson_id') : null;
                if ($submittedLessonId !== $assignment->lesson_id) {
                    $frozenErrors['lesson_id'] = 'Lesson context cannot be modified because student submissions already exist.';
                }
            }

            if ($request->has('max_marks') && (int) $request->input('max_marks') !== (int) $assignment->max_marks) {
                $frozenErrors['max_marks'] = 'Maximum marks cannot be modified because student submissions already exist.';
            }

            if ($request->has('due_at')) {
                $newDue = ! empty($request->input('due_at')) ? Carbon::parse($request->input('due_at'))->toDateTimeString() : null;
                $oldDue = $assignment->due_at?->toDateTimeString();
                if ($newDue !== $oldDue) {
                    $frozenErrors['due_at'] = 'Due date cannot be modified because student submissions already exist.';
                }
            }

            if ($request->has('max_attempts') && (int) $request->input('max_attempts') !== (int) $assignment->max_attempts) {
                $frozenErrors['max_attempts'] = 'Maximum attempts cannot be modified because student submissions already exist.';
            }

            if ($request->has('allow_late_submissions') && $request->boolean('allow_late_submissions') !== (bool) $assignment->allow_late_submissions) {
                $frozenErrors['allow_late_submissions'] = 'Late submission policy cannot be modified because student submissions already exist.';
            }

            if ($request->has('allow_text_submission') && $request->boolean('allow_text_submission') !== (bool) $assignment->allow_text_submission) {
                $frozenErrors['allow_text_submission'] = 'Text submission policy cannot be modified because student submissions already exist.';
            }

            if ($request->has('allow_file_submission') && $request->boolean('allow_file_submission') !== (bool) $assignment->allow_file_submission) {
                $frozenErrors['allow_file_submission'] = 'File upload policy cannot be modified because student submissions already exist.';
            }

            if ($request->has('allowed_file_types') && trim((string) $request->input('allowed_file_types')) !== trim((string) $assignment->allowed_file_types)) {
                $frozenErrors['allowed_file_types'] = 'Allowed file types cannot be modified because student submissions already exist.';
            }

            if ($request->has('max_file_size_mb') && (int) $request->input('max_file_size_mb') !== (int) $assignment->max_file_size_mb) {
                $frozenErrors['max_file_size_mb'] = 'Maximum file size cannot be modified because student submissions already exist.';
            }

            if (! empty($frozenErrors)) {
                throw ValidationException::withMessages($frozenErrors);
            }
        }

        $courseId = (int) ($validated['course_id'] ?? $assignment->course_id);
        $course = Course::findOrFail($courseId);

        $moduleId = array_key_exists('course_module_id', $validated)
            ? (! empty($validated['course_module_id']) ? (int) $validated['course_module_id'] : null)
            : $assignment->course_module_id;

        $lessonId = array_key_exists('lesson_id', $validated)
            ? (! empty($validated['lesson_id']) ? (int) $validated['lesson_id'] : null)
            : $assignment->lesson_id;

        if ($moduleId) {
            $module = CourseModule::where('id', $moduleId)->where('course_id', $course->id)->first();
            if (! $module) {
                throw ValidationException::withMessages(['course_module_id' => 'Selected module does not belong to the selected course.']);
            }
        }

        if ($lessonId) {
            if (! $moduleId) {
                throw ValidationException::withMessages(['lesson_id' => 'A module must be selected when selecting a lesson.']);
            }

            $lesson = Lesson::where('id', $lessonId)->where('course_module_id', $moduleId)->first();
            if (! $lesson) {
                throw ValidationException::withMessages(['lesson_id' => 'Selected lesson does not belong to the selected module.']);
            }
        }

        $updateData = [];

        // Editable metadata
        if (array_key_exists('title', $validated)) {
            $updateData['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $updateData['description'] = $validated['description'];
        }
        if (array_key_exists('instructions', $validated)) {
            $updateData['instructions'] = $validated['instructions'];
        }
        if (array_key_exists('passing_marks', $validated)) {
            $updateData['passing_marks'] = ! empty($validated['passing_marks']) ? (int) $validated['passing_marks'] : null;
        }
        if (array_key_exists('start_at', $validated)) {
            $updateData['start_at'] = ! empty($validated['start_at']) ? Carbon::parse($validated['start_at']) : null;
        }
        if (array_key_exists('late_penalty_percent', $validated)) {
            $updateData['late_penalty_percent'] = (int) ($validated['late_penalty_percent'] ?? 0);
        }

        // Structural fields editable only when NO submissions exist
        if (! $assignment->hasSubmissions()) {
            $updateData['course_id'] = $course->id;
            $updateData['course_module_id'] = $moduleId;
            $updateData['lesson_id'] = $lessonId;

            if ($request->has('max_marks')) {
                $updateData['max_marks'] = (int) $validated['max_marks'];
            }
            if ($request->has('due_at')) {
                $updateData['due_at'] = ! empty($validated['due_at']) ? Carbon::parse($validated['due_at']) : null;
            }
            if ($request->has('allow_late_submissions')) {
                $updateData['allow_late_submissions'] = $request->boolean('allow_late_submissions');
            }
            if ($request->has('max_attempts')) {
                $updateData['max_attempts'] = (int) $validated['max_attempts'];
            }
            if ($request->has('allow_text_submission')) {
                $updateData['allow_text_submission'] = $request->boolean('allow_text_submission');
            }
            if ($request->has('allow_file_submission')) {
                $updateData['allow_file_submission'] = $request->boolean('allow_file_submission');
            }
            if ($request->has('allowed_file_types')) {
                $updateData['allowed_file_types'] = $validated['allowed_file_types'];
            }
            if ($request->has('max_file_size_mb')) {
                $updateData['max_file_size_mb'] = (int) $validated['max_file_size_mb'];
            }
        }

        $assignment->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Assignment settings updated successfully.',
                'assignment' => $assignment->fresh(),
            ]);
        }

        return redirect()->route('admin.assignments.show', $assignment)
            ->with('success', 'Assignment settings updated successfully.');
    }

    /**
     * Toggle status between draft and published.
     */
    public function toggleStatus(Assignment $assignment): RedirectResponse
    {
        if ($assignment->isDraft()) {
            $assignment->update(['status' => Assignment::STATUS_PUBLISHED]);

            return back()->with('success', "Assignment '{$assignment->title}' is now published.");
        }

        $assignment->update(['status' => Assignment::STATUS_DRAFT]);

        return back()->with('success', "Assignment '{$assignment->title}' moved to draft.");
    }

    /**
     * Delete an assignment only when safe (no submissions exist).
     */
    public function destroy(Assignment $assignment): RedirectResponse
    {
        if ($assignment->hasSubmissions()) {
            return back()->with('error', "Cannot delete assignment '{$assignment->title}' because student submissions exist for it.");
        }

        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', "Assignment '{$assignment->title}' deleted successfully.");
    }

    /**
     * View an individual student submission for grading.
     */
    public function submissionShow(Assignment $assignment, AssignmentSubmission $submission): View
    {
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);

        $submission->load(['user', 'grader', 'files']);

        return view('admin.assignments.submission-show', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    /**
     * Grade a student submission.
     */
    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission): RedirectResponse|JsonResponse
    {
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);

        $validated = $request->validate([
            'marks_awarded' => ['required', 'integer', 'min:0', "max:{$assignment->max_marks}"],
            'feedback' => ['nullable', 'string', 'max:5000'],
            'private_notes' => ['nullable', 'string', 'max:5000'],
            'action' => ['nullable', 'string', 'in:grade,resubmission'],
        ]);

        if (($validated['action'] ?? 'grade') === 'resubmission') {
            $this->assignmentService->requestResubmission(
                $request->user(),
                $submission,
                $validated['feedback'] ?? null,
                $validated['private_notes'] ?? null
            );

            $msg = 'Submission returned for student resubmission.';
        } else {
            $this->assignmentService->gradeSubmission(
                $request->user(),
                $submission,
                (int) $validated['marks_awarded'],
                $validated['feedback'] ?? null,
                $validated['private_notes'] ?? null,
                AssignmentSubmission::STATUS_GRADED
            );

            $msg = "Submission graded successfully. Marks: {$validated['marks_awarded']} / {$assignment->max_marks}.";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'submission' => $submission->fresh(),
            ]);
        }

        return redirect()->route('admin.assignments.submissions.show', [$assignment, $submission])
            ->with('success', $msg);
    }

    /**
     * Securely download an assignment file.
     */
    public function downloadFile(Assignment $assignment, AssignmentSubmission $submission, AssignmentSubmissionFile $file): StreamedResponse
    {
        abort_if((int) $submission->assignment_id !== (int) $assignment->id, 404);
        abort_if((int) $file->assignment_submission_id !== (int) $submission->id, 404);

        return $this->assignmentService->downloadSubmissionFile(request()->user(), $file);
    }

    protected function validateAssignment(Request $request, ?int $assignmentId = null): array
    {
        $isUpdate = $assignmentId !== null;

        return $request->validate([
            'course_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:courses,id'],
            'course_module_id' => ['nullable', 'integer'],
            'lesson_id' => ['nullable', 'integer'],
            'title' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'max_marks' => ['sometimes', 'integer', 'min:1', 'max:1000'],
            'passing_marks' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'start_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date'],
            'allow_late_submissions' => ['nullable', 'boolean'],
            'late_penalty_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_attempts' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'allow_text_submission' => ['nullable', 'boolean'],
            'allow_file_submission' => ['nullable', 'boolean'],
            'allowed_file_types' => ['nullable', 'string', 'max:255'],
            'max_file_size_mb' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
    }
}
