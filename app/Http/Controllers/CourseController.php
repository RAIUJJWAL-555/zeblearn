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

        return view('pages.courses.index', [
            'courses' => $courses,
            'allCourses' => $allCourses,
            'availableCount' => $allCourses->where('status', 'available')->count(),
            'upcomingCount' => $allCourses->where('status', 'upcoming')->count(),
            'plannedCount' => $allCourses->where('status', 'planned')->count(),
            'activeCategory' => $category,
            'activeStatus' => $status,
        ]);
    }
}
