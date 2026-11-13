<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin overview dashboard with real-time analytics.
     */
    public function index(Request $request): View
    {
        // 1. Database-driven KPI Metrics
        $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
        $activeStudents = User::where('role', User::ROLE_STUDENT)->where('status', User::STATUS_ACTIVE)->count();
        $totalInstructors = User::where('role', User::ROLE_INSTRUCTOR)->count();
        $totalCourses = Course::count();
        $publishedCourses = Course::published()->count();
        $totalCategories = Schema::hasTable('categories')
            ? Category::count()
            : count(Course::CATEGORIES);

        // 2. Course Status Distribution
        $statusCounts = Course::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $courseStatusBreakdown = [
            'available' => $statusCounts['available'] ?? 0,
            'upcoming' => $statusCounts['upcoming'] ?? 0,
            'planned' => $statusCounts['planned'] ?? 0,
        ];

        // 3. Recent 5 Students
        $recentStudents = User::where('role', User::ROLE_STUDENT)
            ->latest()
            ->take(5)
            ->get();

        // 4. Recent 5 Courses
        $recentCourses = Course::latest()
            ->take(5)
            ->get();

        // 5. Course Distribution by Category
        $categoryDistribution = Course::query()
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->take(6)
            ->get()
            ->map(function ($row) use ($totalCourses) {
                $percentage = $totalCourses > 0 ? (int) round(($row->count / $totalCourses) * 100) : 0;

                return [
                    'name' => Course::CATEGORIES[$row->category]['name'] ?? ucfirst(str_replace('-', ' ', (string) $row->category)),
                    'slug' => $row->category,
                    'count' => (int) $row->count,
                    'percentage' => $percentage,
                ];
            });

        // 6. Student Registration Trend (Past 6 Months)
        $monthlyCounts = User::where('role', User::ROLE_STUDENT)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['created_at'])
            ->groupBy(fn ($u) => $u->created_at?->format('Y-m') ?? now()->format('Y-m'))
            ->map->count();

        $registrationTrend = collect(range(5, 0))->map(function ($monthsAgo) use ($monthlyCounts) {
            $date = now()->subMonths($monthsAgo);
            $key = $date->format('Y-m');

            return [
                'month_label' => $date->format('M Y'),
                'short_month' => $date->format('M'),
                'count' => $monthlyCounts->get($key, 0),
            ];
        })->values();

        $maxMonthlyRegistration = max($registrationTrend->max('count') ?? 0, 1);

        // 7. Real Assignment & Grading Metrics
        $totalAssignments = Assignment::count();
        $pendingSubmissions = AssignmentSubmission::pendingGrading()->count();
        $gradedSubmissions = AssignmentSubmission::graded()->count();
        $recentGradedSubmissions = AssignmentSubmission::graded()
            ->with(['assignment', 'user', 'grader'])
            ->latest('graded_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'admin' => $request->user(),
            'kpis' => [
                'total_students' => $totalStudents,
                'active_students' => $activeStudents,
                'total_instructors' => $totalInstructors,
                'total_courses' => $totalCourses,
                'published_courses' => $publishedCourses,
                'total_categories' => $totalCategories,
                'total_assignments' => $totalAssignments,
                'pending_grading' => $pendingSubmissions,
            ],
            'courseStatusBreakdown' => $courseStatusBreakdown,
            'recentStudents' => $recentStudents,
            'recentCourses' => $recentCourses,
            'categoryDistribution' => $categoryDistribution,
            'registrationTrend' => $registrationTrend,
            'maxMonthlyRegistration' => $maxMonthlyRegistration,
            'assignmentMetrics' => [
                'total_assignments' => $totalAssignments,
                'pending_grading' => $pendingSubmissions,
                'graded_submissions' => $gradedSubmissions,
                'recent_graded' => $recentGradedSubmissions,
            ],
        ]);
    }
}
