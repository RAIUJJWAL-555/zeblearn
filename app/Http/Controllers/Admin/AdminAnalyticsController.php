<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService,
        protected ReportExportService $exportService
    ) {}

    /**
     * Display the main Admin Analytics & Reporting Hub.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['date_range', 'start_date', 'end_date', 'course_id', 'status']);

        $overview = $this->analyticsService->getPlatformOverview($filters);
        $financials = $this->analyticsService->getFinancialAnalytics($filters);
        $enrollments = $this->analyticsService->getEnrollmentAnalytics($filters);
        $courses = Course::orderBy('name')->get(['id', 'name', 'slug']);

        return view('admin.analytics.index', [
            'overview' => $overview,
            'financials' => $financials,
            'enrollments' => $enrollments,
            'courses' => $courses,
            'filters' => $filters,
        ]);
    }

    /**
     * Display detailed Revenue & Financial Analytics.
     */
    public function revenue(Request $request): View
    {
        $filters = $request->only(['date_range', 'start_date', 'end_date', 'course_id', 'payment_status']);

        $financials = $this->analyticsService->getFinancialAnalytics($filters);
        $courses = Course::orderBy('name')->get(['id', 'name', 'slug']);
        $paymentStatuses = Payment::STATUSES;

        return view('admin.analytics.revenue', [
            'financials' => $financials,
            'courses' => $courses,
            'paymentStatuses' => $paymentStatuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Display detailed Enrollment Analytics.
     */
    public function enrollments(Request $request): View
    {
        $filters = $request->only(['date_range', 'start_date', 'end_date', 'course_id', 'status']);

        $enrollments = $this->analyticsService->getEnrollmentAnalytics($filters);
        $courses = Course::orderBy('name')->get(['id', 'name', 'slug']);
        $enrollmentStatuses = Enrollment::STATUSES;

        return view('admin.analytics.enrollments', [
            'enrollments' => $enrollments,
            'courses' => $courses,
            'enrollmentStatuses' => $enrollmentStatuses,
            'filters' => $filters,
        ]);
    }

    /**
     * Display Course Performance Reports.
     */
    public function courses(Request $request): View
    {
        $filters = $request->only(['course_id', 'category', 'course_status']);

        $performance = $this->analyticsService->getCoursePerformanceReports($filters);
        $courses = Course::orderBy('name')->get(['id', 'name', 'slug']);
        $categories = Course::CATEGORIES;

        return view('admin.analytics.courses', [
            'reports' => $performance['reports'],
            'totalCoursesAnalyzed' => $performance['total_courses_analyzed'],
            'avgPlatformCompletion' => $performance['avg_platform_completion_rate'],
            'courses' => $courses,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Display Student Engagement Analytics.
     */
    public function students(Request $request): View
    {
        $filters = $request->only(['date_range', 'start_date', 'end_date']);

        $engagement = $this->analyticsService->getStudentEngagementAnalytics($filters);

        return view('admin.analytics.students', [
            'engagement' => $engagement,
            'filters' => $filters,
        ]);
    }

    /**
     * Display Instructor Performance Analytics.
     */
    public function instructors(Request $request): View
    {
        $filters = $request->only(['instructor_id']);

        $analytics = $this->analyticsService->getInstructorPerformanceAnalytics(null, $filters);
        $instructorsList = User::where('role', User::ROLE_INSTRUCTOR)->orderBy('name')->get(['id', 'name']);

        return view('admin.analytics.instructors', [
            'instructors' => $analytics['instructors'],
            'summary' => $analytics,
            'instructorsList' => $instructorsList,
            'filters' => $filters,
        ]);
    }

    /**
     * Handle CSV and PDF Report Exports.
     */
    public function export(Request $request, string $type): Response|StreamedResponse
    {
        $validTypes = ['enrollments', 'revenue', 'courses', 'students', 'instructors'];
        abort_unless(in_array($type, $validTypes, true), 404, 'Invalid report type requested.');

        $format = $request->query('format', 'csv');
        $filters = $request->only(['date_range', 'start_date', 'end_date', 'course_id', 'status', 'payment_status', 'instructor_id', 'category', 'course_status']);

        if ($format === 'pdf') {
            return match ($type) {
                'enrollments' => $this->exportService->exportEnrollmentsPdf($filters),
                'revenue' => $this->exportService->exportRevenuePdf($filters),
                'courses' => $this->exportService->exportCoursesPdf($filters),
                'students' => $this->exportService->exportStudentsPdf($filters),
                'instructors' => $this->exportService->exportInstructorsPdf($filters),
            };
        }

        return match ($type) {
            'enrollments' => $this->exportService->exportEnrollmentsCsv($filters),
            'revenue' => $this->exportService->exportRevenueCsv($filters),
            'courses' => $this->exportService->exportCoursesCsv($filters),
            'students' => $this->exportService->exportStudentsCsv($filters),
            'instructors' => $this->exportService->exportInstructorsCsv($filters),
        };
    }
}
