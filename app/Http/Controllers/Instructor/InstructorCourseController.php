<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\InstructorService;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstructorCourseController extends Controller
{
    public function __construct(
        protected InstructorService $instructorService,
        protected LessonProgressService $progressService
    ) {}

    /**
     * Display a listing of courses assigned exclusively to the authenticated instructor.
     */
    public function index(Request $request): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        $courses = $instructor->assignedCourses()
            ->withCount(['modules', 'assignments', 'quizzes'])
            ->with([
                'enrollments' => function ($q) {
                    $q->where('status', Enrollment::STATUS_APPROVED);
                },
            ])
            ->latest('course_instructors.assigned_at')
            ->paginate(9);

        return view('instructor.courses.index', [
            'instructor' => $instructor,
            'courses' => $courses,
        ]);
    }

    /**
     * Display details, curriculum structure, quizzes and assignments for an assigned course.
     */
    public function show(Request $request, Course $course): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        if (! $this->instructorService->canInstructorAccessCourse($instructor, $course)) {
            abort(403, 'Access denied. You are not assigned as an instructor for this course.');
        }

        $course->load([
            'modules' => function ($q) {
                $q->orderBy('sort_order', 'asc')->with([
                    'lessons' => function ($lQuery) {
                        $lQuery->orderBy('sort_order', 'asc');
                    },
                ]);
            },
            'quizzes' => function ($q) {
                $q->withCount('questions');
            },
            'assignments' => function ($q) {
                $q->withCount('submissions');
            },
        ]);

        $enrolledCount = $course->enrollments()->where('status', Enrollment::STATUS_APPROVED)->count();

        return view('instructor.courses.show', [
            'instructor' => $instructor,
            'course' => $course,
            'enrolledCount' => $enrolledCount,
        ]);
    }

    /**
     * Display the enrolled students roster and progress for an assigned course.
     */
    public function students(Request $request, Course $course): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        if (! $this->instructorService->canInstructorAccessCourse($instructor, $course)) {
            abort(403, 'Access denied. You are not assigned as an instructor for this course.');
        }

        $enrollments = $course->enrollments()
            ->where('status', Enrollment::STATUS_APPROVED)
            ->with(['user'])
            ->latest('approved_at')
            ->paginate(15);

        // Calculate progress for each student
        $studentsWithProgress = collect($enrollments->items())->map(function ($enrollment) use ($course) {
            $student = $enrollment->user;
            $progress = $student ? $this->progressService->getCourseProgress($student, $course) : null;

            return [
                'enrollment' => $enrollment,
                'student' => $student,
                'progress' => $progress,
            ];
        });

        return view('instructor.courses.students', [
            'instructor' => $instructor,
            'course' => $course,
            'enrollments' => $enrollments,
            'studentsWithProgress' => $studentsWithProgress,
        ]);
    }
}
