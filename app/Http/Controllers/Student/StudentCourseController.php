<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\LessonProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentCourseController extends Controller
{
    public function __construct(
        protected LessonProgressService $progressService
    ) {}

    /**
     * Display the enrolled course learning area syllabus and modules.
     */
    public function show(Request $request, Course $course): View
    {
        $user = $request->user();

        // 1. Verify active student status
        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Only active students can access course learning content.');
        }

        // 2. Verify approved enrollment exists
        if (! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment to access this course.');
        }

        // 3. Verify course availability
        if (! $course->isAvailableForEnrollment()) {
            abort(404, 'This course is currently not published or available.');
        }

        // 4. Eager load only published modules with published lessons & quizzes in deterministic sort order
        $course->load([
            'quizzes' => function ($q) {
                $q->published()->whereNull('course_module_id');
            },
            'modules' => function ($query) {
                $query->published()->ordered()->with([
                    'quizzes' => function ($q) {
                        $q->published()->whereNull('lesson_id');
                    },
                    'lessons' => function ($lQuery) {
                        $lQuery->published()->ordered()->with([
                            'quizzes' => function ($q) {
                                $q->published();
                            },
                        ]);
                    },
                ]);
            },
        ]);

        $courseProgress = $this->progressService->getCourseProgress($user, $course);
        $resumeLesson = $this->progressService->getResumeLesson($user, $course);
        $progressMap = $this->progressService->getCourseProgressMap($user, $course);

        return view('student.courses.show', [
            'course' => $course,
            'modules' => $course->modules,
            'courseProgress' => $courseProgress,
            'progress' => $courseProgress,
            'resumeLesson' => $resumeLesson,
            'progressMap' => $progressMap,
        ]);
    }

    /**
     * Display a specific lesson within an approved course and track access.
     */
    public function lesson(Request $request, Course $course, Lesson $lesson): View
    {
        $user = $request->user();

        // 1. Verify active student status
        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Only active students can access course learning content.');
        }

        // 2. Verify approved enrollment exists
        if (! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment to access this course.');
        }

        // 3. Verify course availability
        if (! $course->isAvailableForEnrollment()) {
            abort(404, 'This course is currently not open or available.');
        }

        // 4. Verify lesson belongs to a module of this course
        $module = $lesson->module;
        if (! $module || (int) $module->course_id !== (int) $course->id) {
            abort(404, 'Lesson does not belong to this course.');
        }

        // 5. Verify module and lesson are published (strict draft protection)
        if (! $module->isPublished() || ! $lesson->isPublished()) {
            abort(404, 'This lesson is currently not published.');
        }

        // 6. Record student access (creates in_progress record or updates last_accessed_at)
        $progress = $this->progressService->recordLessonAccess($user, $lesson);

        // 7. Calculate course progress & lesson navigation
        $courseProgress = $this->progressService->getCourseProgress($user, $course);
        $navigation = $this->progressService->getLessonNavigation($course, $lesson);
        $progressMap = $this->progressService->getCourseProgressMap($user, $course);

        // Load published quizzes attached to this lesson
        $lesson->load([
            'quizzes' => function ($query) {
                $query->published()->withCount('questions');
            },
        ]);

        // Load all course published modules for course syllabus player sidebar
        $modules = $course->modules()->published()->ordered()->with([
            'lessons' => function ($query) {
                $query->published()->ordered()->with(['quizzes' => fn ($zq) => $zq->published()]);
            },
            'quizzes' => function ($query) {
                $query->published()->withCount('questions');
            },
        ])->get();

        return view('student.courses.lesson', [
            'course' => $course,
            'module' => $module,
            'lesson' => $lesson,
            'modules' => $modules,
            'progress' => $progress,
            'currentProgress' => $progress,
            'courseProgress' => $courseProgress,
            'navigation' => $navigation,
            'prevLesson' => $navigation['previous_lesson'] ?? $navigation['prev'] ?? null,
            'nextLesson' => $navigation['next_lesson'] ?? $navigation['next'] ?? null,
            'progressMap' => $progressMap,
        ]);
    }

    /**
     * Mark a lesson complete.
     */
    public function completeLesson(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->progressService->validateLessonEligibility($request->user(), $course, $lesson);
        $this->progressService->markLessonComplete($request->user(), $course, $lesson);

        return redirect()->back()->with('success', "Completed: {$lesson->title}");
    }

    /**
     * Mark a completed lesson as incomplete (in progress).
     */
    public function incompleteLesson(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        $this->progressService->validateLessonEligibility($request->user(), $course, $lesson);
        $this->progressService->markLessonIncomplete($request->user(), $course, $lesson);

        return redirect()->back()->with('info', "Marked '{$lesson->title}' as in progress.");
    }

    /**
     * Resume course learning from the last accessed incomplete lesson.
     */
    public function resume(Request $request, Course $course): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Only active students can access course learning content.');
        }

        if (! $user->hasApprovedEnrollment($course)) {
            abort(403, 'You must have an approved enrollment to resume this course.');
        }

        if (! $course->isAvailableForEnrollment()) {
            abort(404, 'This course is currently not published or open.');
        }

        $resumeLesson = $this->progressService->getResumeLesson($user, $course);

        if ($resumeLesson) {
            return redirect()->route('student.courses.lesson', [$course, $resumeLesson]);
        }

        return redirect()->route('student.courses.show', $course)->with('info', 'No published lessons found for this course.');
    }
}
