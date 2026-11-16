<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InstructorAnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected ReportExportService $exportService
    ) {}

    /**
     * Display personal faculty analytics for assigned courses only.
     */
    public function index(Request $request): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        $filters = $request->only(['course_id']);

        // Validate that if course_id is passed, it belongs to this instructor
        if (! empty($filters['course_id'])) {
            $isAssigned = $instructor->assignedCourses()->where('courses.id', $filters['course_id'])->exists();
            if (! $isAssigned) {
                abort(403, 'Unauthorized access to unassigned course analytics.');
            }
        }

        $analytics = $this->analyticsService->getInstructorPerformanceAnalytics($instructor, $filters);
        $instructorDossier = $analytics['instructors']->first() ?? [
            'instructor_id' => $instructor->id,
            'name' => $instructor->name,
            'email' => $instructor->email,
            'designation' => $instructor->designation ?? 'Faculty',
            'specialization' => $instructor->specialization ?? 'General',
            'assigned_courses_count' => 0,
            'assigned_courses' => collect(),
            'total_students' => 0,
            'approved_students' => 0,
            'completed_students' => 0,
            'submissions_graded' => 0,
            'pending_workload' => 0,
            'avg_turnaround_hours' => null,
        ];

        // Also fetch course performance for assigned courses only
        $assignedCourseIds = $instructor->assignedCourses->pluck('id')->all();
        $courseReports = collect();

        if (! empty($assignedCourseIds)) {
            $courseFilters = ! empty($filters['course_id'])
                ? ['course_id' => $filters['course_id']]
                : [];
            $allReports = $this->analyticsService->getCoursePerformanceReports($courseFilters)['reports'];
            $courseReports = $allReports->whereIn('course_id', $assignedCourseIds);
        }

        return view('instructor.analytics.index', [
            'instructor' => $instructor,
            'dossier' => $instructorDossier,
            'courseReports' => $courseReports,
            'assignedCourses' => $instructor->assignedCourses,
            'filters' => $filters,
        ]);
    }

    /**
     * Export instructor's performance and assigned course report.
     */
    public function export(Request $request): Response|StreamedResponse
    {
        /** @var User $instructor */
        $instructor = $request->user();
        $format = $request->query('format', 'csv');

        if ($format === 'pdf') {
            return $this->exportService->exportInstructorsPdf([], $instructor);
        }

        return $this->exportService->exportInstructorsCsv([], $instructor);
    }
}
