<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\User;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __construct(
        protected LessonProgressService $progressService
    ) {}

    /**
     * Display the student learning dashboard with active enrollments and status.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $enrollments = $user->enrollments()
            ->with([
                'course' => function ($query) {
                    $query->with([
                        'modules' => function ($mQuery) {
                            $mQuery->published()->with([
                                'lessons' => function ($lQuery) {
                                    $lQuery->published();
                                },
                            ]);
                        },
                    ]);
                },
            ])
            ->latest('requested_at')
            ->get();

        $approvedEnrollments = $enrollments->where('status', Enrollment::STATUS_APPROVED);
        $pendingEnrollments = $enrollments->where('status', Enrollment::STATUS_PENDING);
        $declinedEnrollments = $enrollments->whereIn('status', [Enrollment::STATUS_REJECTED, Enrollment::STATUS_CANCELLED]);

        $approvedCourses = $approvedEnrollments->pluck('course')->filter();
        $courseProgressMap = $this->progressService->getMultipleCoursesProgress($user, $approvedCourses);

        // Accurate learning metrics from verified database progress
        $totalCompletedLessons = (int) collect($courseProgressMap)->sum('completed');
        $inProgressCoursesCount = collect($courseProgressMap)->filter(fn ($p) => ! $p['is_completed'] && $p['completed'] > 0)->count();
        $completedCoursesCount = collect($courseProgressMap)->filter(fn ($p) => $p['is_completed'])->count();

        // Continue Learning / Resume lesson resolution
        $resumeCourse = null;
        $resumeLesson = null;
        $resumeProgress = null;

        if ($approvedCourses->isNotEmpty()) {
            $recentProgress = $user->lessonProgress()
                ->whereHas('lesson', function ($lQuery) use ($approvedCourses) {
                    $lQuery->where('status', 'published')
                        ->whereHas('module', function ($mQuery) use ($approvedCourses) {
                            $mQuery->where('status', 'published')
                                ->whereIn('course_id', $approvedCourses->pluck('id'));
                        });
                })
                ->with(['lesson.module.course'])
                ->latest('last_accessed_at')
                ->first();

            if ($recentProgress && $recentProgress->lesson && $recentProgress->lesson->module && $recentProgress->lesson->module->course) {
                $resumeCourse = $recentProgress->lesson->module->course;
                $resumeLesson = $recentProgress->lesson;
                $resumeProgress = $courseProgressMap[$resumeCourse->id] ?? null;
            } else {
                $resumeCourse = $approvedCourses->first();
                $resumeLesson = $resumeCourse ? $this->progressService->getResumeLesson($user, $resumeCourse) : null;
                $resumeProgress = $resumeCourse ? ($courseProgressMap[$resumeCourse->id] ?? null) : null;
            }
        }

        // Quiz assessment metrics and overview
        $quizAttempts = $user->quizAttempts()
            ->with(['quiz.course'])
            ->latest('created_at')
            ->take(4)
            ->get();

        $totalQuizAttempts = $user->quizAttempts()->count();
        $passedQuizAttempts = $user->quizAttempts()->where('is_passed', true)->count();

        $availableQuizzes = collect();
        if ($approvedCourses->isNotEmpty()) {
            $availableQuizzes = Quiz::published()
                ->whereIn('course_id', $approvedCourses->pluck('id'))
                ->with(['course', 'questions'])
                ->latest()
                ->take(4)
                ->get();
        }

        // Assignment and coursework metrics
        $upcomingAssignments = collect();
        if ($approvedCourses->isNotEmpty()) {
            $upcomingAssignments = Assignment::published()
                ->whereIn('course_id', $approvedCourses->pluck('id'))
                ->where(function ($q) {
                    $q->whereNull('due_at')->orWhere('due_at', '>=', now());
                })
                ->with(['course', 'module'])
                ->orderBy('due_at', 'asc')
                ->take(4)
                ->get();
        }

        $studentSubmissions = $user->assignmentSubmissions()
            ->with(['assignment.course'])
            ->latest('submitted_at')
            ->get();

        $pendingSubmissions = $studentSubmissions->whereIn('status', [AssignmentSubmission::STATUS_SUBMITTED, AssignmentSubmission::STATUS_UNDER_REVIEW])->take(4);
        $recentlyGradedAssignments = $studentSubmissions->where('status', AssignmentSubmission::STATUS_GRADED)->take(4);

        // Chronological activity records (compiled purely from authentic database events)
        $activities = collect();

        foreach ($studentSubmissions->take(3) as $asgnSub) {
            if ($asgnSub->assignment && $asgnSub->assignment->course) {
                if ($asgnSub->isGraded()) {
                    $activities->push([
                        'type' => 'assignment_graded',
                        'title' => 'Assignment Graded: '.$asgnSub->assignment->title.($asgnSub->marks_awarded !== null ? " ({$asgnSub->marks_awarded}/{$asgnSub->assignment->max_marks})" : ''),
                        'subtitle' => $asgnSub->assignment->course->name.' • Score: '.$asgnSub->percentage.'%',
                        'timestamp' => $asgnSub->graded_at ?? $asgnSub->updated_at,
                        'url' => route('student.assignments.submissions.show', [$asgnSub->assignment->course, $asgnSub->assignment, $asgnSub]),
                        'badge' => 'Assignment',
                        'badge_variant' => 'success',
                        'icon' => 'document-check',
                    ]);
                } else {
                    $activities->push([
                        'type' => 'assignment_submitted',
                        'title' => 'Turned in Assignment: '.$asgnSub->assignment->title,
                        'subtitle' => $asgnSub->assignment->course->name.' • Attempt #'.$asgnSub->attempt_number,
                        'timestamp' => $asgnSub->submitted_at,
                        'url' => route('student.assignments.show', [$asgnSub->assignment->course, $asgnSub->assignment]),
                        'badge' => 'Assignment',
                        'badge_variant' => 'info',
                        'icon' => 'document-text',
                    ]);
                }
            }
        }

        $recentCompletedLessons = $user->lessonProgress()
            ->where('status', LessonProgress::STATUS_COMPLETED)
            ->with(['lesson.module.course'])
            ->latest('completed_at')
            ->take(4)
            ->get();

        foreach ($recentCompletedLessons as $lp) {
            if ($lp->lesson && $lp->lesson->module && $lp->lesson->module->course) {
                $activities->push([
                    'type' => 'lesson_completed',
                    'title' => 'Completed Lesson: '.$lp->lesson->title,
                    'subtitle' => $lp->lesson->module->course->name,
                    'timestamp' => $lp->completed_at ?? $lp->updated_at,
                    'url' => route('student.courses.lesson', [$lp->lesson->module->course, $lp->lesson]),
                    'badge' => 'Lesson',
                    'badge_variant' => 'success',
                    'icon' => 'check-circle',
                ]);
            }
        }

        foreach ($quizAttempts as $qa) {
            if ($qa->quiz && $qa->quiz->course) {
                $activities->push([
                    'type' => 'quiz_submitted',
                    'title' => 'Quiz: '.$qa->quiz->title.($qa->percentage !== null ? " ({$qa->percentage}%)" : ''),
                    'subtitle' => $qa->quiz->course->name.($qa->is_passed ? ' • Passed' : ' • Completed'),
                    'timestamp' => $qa->submitted_at ?? $qa->created_at,
                    'url' => route('student.quizzes.result', [$qa->quiz->course, $qa->quiz, $qa]),
                    'badge' => 'Quiz',
                    'badge_variant' => $qa->is_passed ? 'success' : 'warning',
                    'icon' => 'academic-cap',
                ]);
            }
        }

        foreach ($enrollments->take(3) as $en) {
            if ($en->course) {
                $activities->push([
                    'type' => 'enrollment_'.$en->status,
                    'title' => 'Enrollment: '.$en->course->name,
                    'subtitle' => 'Status: '.$en->status_label,
                    'timestamp' => $en->approved_at ?? $en->rejected_at ?? $en->requested_at ?? $en->created_at,
                    'url' => $en->isApproved() ? route('student.courses.show', $en->course) : null,
                    'badge' => 'Enrollment',
                    'badge_variant' => $en->isApproved() ? 'success' : ($en->isPending() ? 'warning' : 'neutral'),
                    'icon' => 'document-text',
                ]);
            }
        }

        $recentActivities = $activities->sortByDesc('timestamp')->take(5)->values();

        return view('student.dashboard', [
            'user' => $user,
            'approvedEnrollments' => $approvedEnrollments,
            'pendingEnrollments' => $pendingEnrollments,
            'declinedEnrollments' => $declinedEnrollments,
            'totalEnrolled' => $approvedEnrollments->count(),
            'totalPending' => $pendingEnrollments->count(),
            'courseProgressMap' => $courseProgressMap,
            'totalCompletedLessons' => $totalCompletedLessons,
            'inProgressCoursesCount' => $inProgressCoursesCount,
            'completedCoursesCount' => $completedCoursesCount,
            'resumeCourse' => $resumeCourse,
            'resumeLesson' => $resumeLesson,
            'resumeProgress' => $resumeProgress,
            'quizAttempts' => $quizAttempts,
            'totalQuizAttempts' => $totalQuizAttempts,
            'passedQuizAttempts' => $passedQuizAttempts,
            'availableQuizzes' => $availableQuizzes,
            'recentActivities' => $recentActivities,
            'upcomingAssignments' => $upcomingAssignments,
            'pendingSubmissions' => $pendingSubmissions,
            'recentlyGradedAssignments' => $recentlyGradedAssignments,
        ]);
    }
}
