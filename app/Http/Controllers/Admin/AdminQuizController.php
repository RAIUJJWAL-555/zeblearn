<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminQuizController extends Controller
{
    /**
     * Display a paginated listing of quizzes with filtering and analytics summary.
     */
    public function index(Request $request): View
    {
        $query = Quiz::with(['course', 'module', 'lesson'])->withCount(['questions', 'attempts']);

        // 1. Search by title
        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // 2. Filter by course
        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        // 3. Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // 4. Filter by context
        if ($context = $request->input('context')) {
            if ($context === 'course') {
                $query->whereNull('course_module_id')->whereNull('lesson_id');
            } elseif ($context === 'module') {
                $query->whereNotNull('course_module_id')->whereNull('lesson_id');
            } elseif ($context === 'lesson') {
                $query->whereNotNull('lesson_id');
            }
        }

        $quizzes = $query->latest('id')->paginate(15)->withQueryString();

        // High-level analytics
        $totalQuizzes = Quiz::count();
        $publishedQuizzes = Quiz::where('status', Quiz::STATUS_PUBLISHED)->count();
        $totalAttempts = QuizAttempt::whereIn('status', [QuizAttempt::STATUS_SUBMITTED, QuizAttempt::STATUS_EXPIRED])->count();

        $avgScore = $totalAttempts > 0
            ? (int) round(QuizAttempt::whereIn('status', [QuizAttempt::STATUS_SUBMITTED, QuizAttempt::STATUS_EXPIRED])->avg('percentage'))
            : 0;

        $passedAttempts = QuizAttempt::where('is_passed', true)->count();
        $passRate = $totalAttempts > 0
            ? (int) round(($passedAttempts / $totalAttempts) * 100)
            : 0;

        $courses = Course::orderBy('name')->get(['id', 'name']);

        return view('admin.quizzes.index', [
            'quizzes' => $quizzes,
            'courses' => $courses,
            'totalQuizzes' => $totalQuizzes,
            'publishedQuizzes' => $publishedQuizzes,
            'totalAttempts' => $totalAttempts,
            'avgScore' => $avgScore,
            'passRate' => $passRate,
        ]);
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create(Request $request): View
    {
        $courses = Course::with([
            'modules' => fn ($q) => $q->orderBy('sort_order')->with([
                'lessons' => fn ($lq) => $lq->orderBy('sort_order'),
            ]),
        ])->orderBy('name')->get();

        return view('admin.quizzes.create', [
            'courses' => $courses,
            'preselectedCourseId' => $request->input('course_id'),
            'preselectedModuleId' => $request->input('module_id'),
            'preselectedLessonId' => $request->input('lesson_id'),
        ]);
    }

    /**
     * Store a newly created quiz in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateQuiz($request);

        $course = Course::findOrFail($validated['course_id']);

        // Assessment context validation
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
        while (Quiz::where('course_id', $course->id)->where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-".$counter++;
        }

        $passingPercentage = (int) ($validated['passing_percentage'] ?? $validated['pass_percentage'] ?? 70);
        $attemptsAllowed = (int) ($validated['attempts_allowed'] ?? $validated['allowed_attempts'] ?? 1);

        $quiz = Quiz::create([
            'course_id' => $course->id,
            'course_module_id' => $moduleId,
            'lesson_id' => $lessonId,
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'time_limit_minutes' => ! empty($validated['time_limit_minutes']) ? (int) $validated['time_limit_minutes'] : null,
            'passing_percentage' => $passingPercentage,
            'attempts_allowed' => $attemptsAllowed,
            'shuffle_questions' => $request->boolean('shuffle_questions'),
            'shuffle_options' => $request->boolean('shuffle_options'),
            'show_correct_answers' => $request->boolean('show_correct_answers', true),
            'status' => Quiz::STATUS_DRAFT, // Initially draft until questions are added
        ]);

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', "Quiz '{$quiz->title}' created. Now add questions to build your assessment.");
    }

    /**
     * Display the specified quiz console with questions and attempts summary.
     */
    public function show(Quiz $quiz): View
    {
        $quiz->load([
            'course',
            'module',
            'lesson',
            'questions.options',
            'attempts' => fn ($q) => $q->with('user')->latest('id')->limit(20),
        ]);

        $publishingErrors = $quiz->getPublishingValidationErrors();

        return view('admin.quizzes.show', [
            'quiz' => $quiz,
            'publishingErrors' => $publishingErrors,
        ]);
    }

    /**
     * Show the form for editing quiz configurations.
     */
    public function edit(Quiz $quiz): View
    {
        $courses = Course::with([
            'modules' => fn ($q) => $q->orderBy('sort_order')->with([
                'lessons' => fn ($lq) => $lq->orderBy('sort_order'),
            ]),
        ])->orderBy('name')->get();

        return view('admin.quizzes.edit', [
            'quiz' => $quiz,
            'courses' => $courses,
            'hasAttempts' => $quiz->attempts()->exists(),
        ]);
    }

    /**
     * Update quiz settings.
     */
    public function update(Request $request, Quiz $quiz): RedirectResponse|JsonResponse
    {
        $validated = $this->validateQuiz($request, $quiz->id);

        // If attempts exist in ANY state, freeze all configuration settings
        if ($quiz->hasAttempts()) {
            $frozenErrors = [];

            if ($request->has('course_id') && (int) $request->input('course_id') !== (int) $quiz->course_id) {
                $frozenErrors['course_id'] = 'Course cannot be modified because student attempts already exist for this quiz.';
            }

            if ($request->has('course_module_id')) {
                $submittedModuleId = ! empty($request->input('course_module_id')) ? (int) $request->input('course_module_id') : null;
                if ($submittedModuleId !== $quiz->course_module_id) {
                    $frozenErrors['course_module_id'] = 'Module cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('lesson_id')) {
                $submittedLessonId = ! empty($request->input('lesson_id')) ? (int) $request->input('lesson_id') : null;
                if ($submittedLessonId !== $quiz->lesson_id) {
                    $frozenErrors['lesson_id'] = 'Lesson cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('passing_percentage') || $request->has('pass_percentage')) {
                $submittedPass = (int) ($request->input('passing_percentage') ?? $request->input('pass_percentage'));
                if ($submittedPass !== (int) $quiz->passing_percentage) {
                    $key = $request->has('passing_percentage') ? 'passing_percentage' : 'pass_percentage';
                    $frozenErrors[$key] = 'Passing percentage cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('attempts_allowed') || $request->has('allowed_attempts')) {
                $submittedAttempts = (int) ($request->input('attempts_allowed') ?? $request->input('allowed_attempts'));
                if ($submittedAttempts !== (int) $quiz->attempts_allowed) {
                    $key = $request->has('attempts_allowed') ? 'attempts_allowed' : 'allowed_attempts';
                    $frozenErrors[$key] = 'Allowed attempts cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('time_limit_minutes')) {
                $submittedTime = ! empty($request->input('time_limit_minutes')) ? (int) $request->input('time_limit_minutes') : null;
                if ($submittedTime !== $quiz->time_limit_minutes) {
                    $frozenErrors['time_limit_minutes'] = 'Time limit cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('shuffle_questions')) {
                $submittedShuffleQ = $request->boolean('shuffle_questions');
                if ($submittedShuffleQ !== (bool) $quiz->shuffle_questions) {
                    $frozenErrors['shuffle_questions'] = 'Question shuffling setting cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('shuffle_options')) {
                $submittedShuffleO = $request->boolean('shuffle_options');
                if ($submittedShuffleO !== (bool) $quiz->shuffle_options) {
                    $frozenErrors['shuffle_options'] = 'Option shuffling setting cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('show_correct_answers')) {
                $submittedShowCorrect = $request->boolean('show_correct_answers');
                if ($submittedShowCorrect !== (bool) $quiz->show_correct_answers) {
                    $frozenErrors['show_correct_answers'] = 'Show correct answers setting cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if ($request->has('total_marks')) {
                $submittedTotalMarks = (int) $request->input('total_marks');
                if ($submittedTotalMarks !== (int) $quiz->total_marks) {
                    $frozenErrors['total_marks'] = 'Total marks cannot be modified because student attempts already exist for this quiz.';
                }
            }

            if (! empty($frozenErrors)) {
                throw ValidationException::withMessages($frozenErrors);
            }
        }

        $courseId = (int) ($validated['course_id'] ?? $quiz->course_id);
        $course = Course::findOrFail($courseId);

        $moduleId = array_key_exists('course_module_id', $validated)
            ? (! empty($validated['course_module_id']) ? (int) $validated['course_module_id'] : null)
            : $quiz->course_module_id;

        $lessonId = array_key_exists('lesson_id', $validated)
            ? (! empty($validated['lesson_id']) ? (int) $validated['lesson_id'] : null)
            : $quiz->lesson_id;

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

        if (array_key_exists('title', $validated)) {
            $updateData['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $updateData['description'] = $validated['description'];
        }
        if (array_key_exists('instructions', $validated)) {
            $updateData['instructions'] = $validated['instructions'];
        }

        // Only update configuration fields if no attempts exist
        if (! $quiz->hasAttempts()) {
            $updateData['course_id'] = $course->id;
            $updateData['course_module_id'] = $moduleId;
            $updateData['lesson_id'] = $lessonId;

            if ($request->has('time_limit_minutes')) {
                $updateData['time_limit_minutes'] = ! empty($validated['time_limit_minutes']) ? (int) $validated['time_limit_minutes'] : null;
            }

            if ($request->has('passing_percentage') || $request->has('pass_percentage')) {
                $updateData['passing_percentage'] = (int) ($validated['passing_percentage'] ?? $validated['pass_percentage'] ?? 70);
            }

            if ($request->has('attempts_allowed') || $request->has('allowed_attempts')) {
                $updateData['attempts_allowed'] = (int) ($validated['attempts_allowed'] ?? $validated['allowed_attempts'] ?? 1);
            }

            if ($request->has('shuffle_questions')) {
                $updateData['shuffle_questions'] = $request->boolean('shuffle_questions');
            }

            if ($request->has('shuffle_options')) {
                $updateData['shuffle_options'] = $request->boolean('shuffle_options');
            }

            if ($request->has('show_correct_answers')) {
                $updateData['show_correct_answers'] = $request->boolean('show_correct_answers');
            }
        }

        $quiz->update($updateData);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Quiz settings updated successfully.',
                'quiz' => $quiz->fresh(),
            ]);
        }

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz settings updated successfully.');
    }

    /**
     * Toggle status between draft and published.
     */
    public function toggleStatus(Quiz $quiz): RedirectResponse
    {
        if ($quiz->isDraft()) {
            $errors = $quiz->getPublishingValidationErrors();
            if (! empty($errors)) {
                return back()->with('error', 'Cannot publish quiz: '.implode(' ', $errors));
            }

            $quiz->update(['status' => Quiz::STATUS_PUBLISHED]);

            return back()->with('success', "Quiz '{$quiz->title}' is now published and available to enrolled students.");
        }

        $quiz->update(['status' => Quiz::STATUS_DRAFT]);

        return back()->with('success', "Quiz '{$quiz->title}' has been moved to draft.");
    }

    /**
     * Delete a quiz only if no historical attempts exist.
     */
    public function destroy(Quiz $quiz): RedirectResponse
    {
        if ($quiz->attempts()->exists()) {
            return back()->with('error', "Cannot delete quiz '{$quiz->title}' because historical student attempts exist for it.");
        }

        // Clean up questions & options
        foreach ($quiz->questions as $q) {
            $q->options()->delete();
        }
        $quiz->questions()->delete();
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', "Quiz '{$quiz->title}' deleted successfully.");
    }

    /**
     * View individual student attempt for grading and auditing.
     */
    public function attemptShow(Quiz $quiz, QuizAttempt $attempt): View
    {
        abort_if($attempt->quiz_id !== $quiz->id, 404);

        $attempt->load(['user', 'answers.question.options', 'answers.option']);

        return view('admin.quizzes.attempt-show', [
            'quiz' => $quiz,
            'attempt' => $attempt,
        ]);
    }

    protected function validateQuiz(Request $request, ?int $quizId = null): array
    {
        $isUpdate = $quizId !== null;

        return $request->validate([
            'course_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:courses,id'],
            'course_module_id' => ['nullable', 'integer'],
            'lesson_id' => ['nullable', 'integer'],
            'title' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'instructions' => ['nullable', 'string', 'max:3000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:360'],
            'passing_percentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'pass_percentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'attempts_allowed' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'allowed_attempts' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'total_marks' => ['sometimes', 'integer', 'min:1'],
            'shuffle_questions' => ['nullable', 'boolean'],
            'shuffle_options' => ['nullable', 'boolean'],
            'show_correct_answers' => ['nullable', 'boolean'],
        ]);
    }
}
