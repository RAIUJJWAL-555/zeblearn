<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentQuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService
    ) {}

    /**
     * Display quiz overview, rules, and student attempt status.
     */
    public function show(Request $request, Course $course, Quiz $quiz): View
    {
        abort_if($quiz->course_id !== $course->id, 404);

        $user = $request->user();
        $this->quizService->validateStudentAccess($user, $quiz);

        $attempts = $quiz->attempts()
            ->where('user_id', $user->id)
            ->latest('attempt_number')
            ->get();

        $activeAttempt = $attempts->firstWhere('status', QuizAttempt::STATUS_IN_PROGRESS);
        $completedAttempts = $attempts->whereIn('status', [QuizAttempt::STATUS_SUBMITTED, QuizAttempt::STATUS_EXPIRED]);

        $bestAttempt = $completedAttempts->sortByDesc('percentage')->first();
        $canAttempt = $quiz->canUserAttempt($user);
        $remainingAttempts = $quiz->remainingAttemptsForUser($user);

        return view('student.quizzes.show', [
            'course' => $course,
            'quiz' => $quiz,
            'attempts' => $attempts,
            'activeAttempt' => $activeAttempt,
            'bestAttempt' => $bestAttempt,
            'canAttempt' => $canAttempt,
            'remainingAttempts' => $remainingAttempts,
        ]);
    }

    /**
     * Start a new attempt or resume an active one.
     */
    public function start(Request $request, Course $course, Quiz $quiz): RedirectResponse
    {
        abort_if($quiz->course_id !== $course->id, 404);

        if (! $quiz->canUserAttempt($request->user())) {
            return redirect()->route('student.quizzes.show', [$course, $quiz])
                ->with('error', "You have reached the maximum allowed attempts ({$quiz->attempts_allowed}) for this assessment.");
        }

        $attempt = $this->quizService->startOrResumeAttempt($request->user(), $quiz);

        return redirect()->route('student.quizzes.take', [$course, $quiz, $attempt]);
    }

    /**
     * Active quiz-taking screen with questions, timer, and option selection.
     */
    public function take(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt): View|RedirectResponse
    {
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== $request->user()->id, 403, 'Unauthorized access to another student attempt.');

        // If already submitted or expired, redirect straight to result
        if (! $attempt->isInProgress()) {
            return redirect()->route('student.quizzes.result', [$course, $quiz, $attempt]);
        }

        // Check server-authoritative timer expiry
        if ($attempt->hasExpired()) {
            $this->quizService->submitAttempt($attempt);

            return redirect()->route('student.quizzes.result', [$course, $quiz, $attempt])
                ->with('info', 'Time expired. Your attempt has been automatically submitted.');
        }

        $viewData = $this->quizService->getAttemptTakingData($attempt);

        return view('student.quizzes.take', $viewData);
    }

    /**
     * Save an individual question answer during the attempt.
     */
    public function saveAnswer(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt): JsonResponse|RedirectResponse
    {
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'question_id' => ['required', 'integer'],
            'option_id' => ['nullable', 'integer'],
        ]);

        $this->quizService->saveAnswer(
            $attempt,
            (int) $validated['question_id'],
            $validated['option_id'] ? (int) $validated['option_id'] : null
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Answer saved.',
            ]);
        }

        return back()->with('success', 'Answer saved.');
    }

    /**
     * Final submission of student attempt.
     */
    public function submit(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt): RedirectResponse
    {
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== $request->user()->id, 403);

        $finalized = $this->quizService->submitAttempt($attempt);

        $statusMessage = $finalized->is_passed
            ? "Congratulations! You passed with {$finalized->percentage}%."
            : "Attempt submitted. You scored {$finalized->percentage}%.";

        return redirect()->route('student.quizzes.result', [$course, $quiz, $finalized])
            ->with('success', $statusMessage);
    }

    /**
     * Display attempt score, percentage, pass/fail result, and answer review if allowed.
     */
    public function result(Request $request, Course $course, Quiz $quiz, QuizAttempt $attempt): View
    {
        abort_if($quiz->course_id !== $course->id, 404);
        abort_if($attempt->quiz_id !== $quiz->id, 404);
        abort_if($attempt->user_id !== $request->user()->id, 403);

        $resultData = $this->quizService->getAttemptResultData($attempt);

        return view('student.quizzes.result', $resultData);
    }

    /**
     * Full attempt history for a student on this quiz.
     */
    public function history(Request $request, Course $course, Quiz $quiz): View
    {
        abort_if($quiz->course_id !== $course->id, 404);

        $user = $request->user();
        $this->quizService->validateStudentAccess($user, $quiz, false);

        $attempts = $quiz->attempts()
            ->where('user_id', $user->id)
            ->whereIn('status', [QuizAttempt::STATUS_SUBMITTED, QuizAttempt::STATUS_EXPIRED])
            ->orderBy('attempt_number', 'desc')
            ->get();

        return view('student.quizzes.history', [
            'course' => $course,
            'quiz' => $quiz,
            'attempts' => $attempts,
        ]);
    }
}
