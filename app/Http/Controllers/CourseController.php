<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display the course academy catalog.
     */
    public function index(Request $request): View
    {
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');

        $query = Course::query()->published()->orderBy('sort_order', 'asc');

        if ($category && $category !== 'all') {
            $query->byCategory($category);
        }

        if ($status && $status !== 'all') {
            $query->byStatus($status);
        }

        $courses = $query->get();

        // Also fetch all published courses for overview statistics and instant client filtering
        $allCourses = Course::query()->published()->orderBy('sort_order', 'asc')->get();

        $categoryMeta = Course::CATEGORIES[$category] ?? Course::CATEGORIES['sap-erp'];

        $userEnrollments = $request->user()
            ? $request->user()->enrollments()->get()->keyBy('course_id')
            : collect();

        return view('pages.courses.index', [
            'courses' => $courses,
            'allCourses' => $allCourses,
            'categories' => Course::CATEGORIES,
            'categoryMeta' => $categoryMeta,
            'availableCount' => $allCourses->where('status', 'available')->count(),
            'upcomingCount' => $allCourses->where('status', 'upcoming')->count(),
            'plannedCount' => $allCourses->where('status', 'planned')->count(),
            'activeCategory' => $category,
            'activeStatus' => $status,
            'userEnrollments' => $userEnrollments,
        ]);
    }

    /**
     * Display the flagship SAP FICO course landing & LMS overview.
     */
    public function fico(Request $request): View
    {
        $course = Course::where('slug', 'sap-fico-training')->first();
        $user = $request->user();
        $enrollment = ($user && $course)
            ? $user->enrollments()->where('course_id', $course->id)->first()
            : null;

        return view('pages.courses.sap-fico', [
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Display a published course LMS detail and overview page.
     */
    public function show(Request $request, Course $course): View
    {
        if ($course->slug === 'sap-fico-training') {
            return $this->fico($request);
        }

        $user = $request->user();
        $enrollment = $user
            ? $user->enrollments()->where('course_id', $course->id)->first()
            : null;

        $course->load([
            'modules' => function ($query) {
                $query->published()->ordered()->withCount('lessons');
            },
        ]);

        return view('pages.courses.show', [
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }
}
