<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Export enrollments report as streaming CSV.
     */
    public function exportEnrollmentsCsv(array $filters = []): StreamedResponse
    {
        $dateRange = $this->analyticsService->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $query = Enrollment::with(['user', 'course', 'reviewer'])
            ->when($start, fn ($q) => $q->where('requested_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('requested_at', '<=', $end))
            ->when(! empty($filters['course_id']), fn ($q) => $q->where('course_id', $filters['course_id']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('requested_at');

        $filename = 'enrollments-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Enrollment ID',
                'Student Name',
                'Student Email',
                'Student Phone',
                'Course Code',
                'Course Title',
                'Status',
                'Enrollment Source',
                'Requested At',
                'Approved At',
                'Reviewed By',
            ]);

            $query->chunk(250, function ($enrollments) use ($handle) {
                foreach ($enrollments as $enr) {
                    fputcsv($handle, [
                        $enr->id,
                        $enr->user?->name ?? 'N/A',
                        $enr->user?->email ?? 'N/A',
                        $enr->user?->phone ?? 'N/A',
                        $enr->course?->code ?? $enr->course?->id,
                        $enr->course?->title ?? 'N/A',
                        ucfirst($enr->status),
                        $enr->enrollment_source ?? 'web',
                        $enr->requested_at?->format('Y-m-d H:i:s') ?? '',
                        $enr->approved_at?->format('Y-m-d H:i:s') ?? '',
                        $enr->reviewer?->name ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export enrollments report as PDF.
     */
    public function exportEnrollmentsPdf(array $filters = []): Response
    {
        $dateRange = $this->analyticsService->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $enrollments = Enrollment::with(['user', 'course'])
            ->when($start, fn ($q) => $q->where('requested_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('requested_at', '<=', $end))
            ->when(! empty($filters['course_id']), fn ($q) => $q->where('course_id', $filters['course_id']))
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->latest('requested_at')
            ->limit(500)
            ->get();

        $pdf = Pdf::loadView('admin.analytics.exports.enrollments-pdf', [
            'enrollments' => $enrollments,
            'filters' => $filters,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('enrollments-report-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export revenue report as streaming CSV.
     */
    public function exportRevenueCsv(array $filters = []): StreamedResponse
    {
        $dateRange = $this->analyticsService->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $query = Payment::with(['user', 'course'])
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('created_at', '<=', $end))
            ->when(! empty($filters['course_id']), fn ($q) => $q->where('course_id', $filters['course_id']))
            ->when(! empty($filters['payment_status']), fn ($q) => $q->where('status', $filters['payment_status']))
            ->latest('created_at');

        $filename = 'revenue-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Transaction ID',
                'Student Name',
                'Student Email',
                'Course Title',
                'Gateway Order ID',
                'Amount (INR)',
                'Currency',
                'Status',
                'Payment Method',
                'Paid At',
                'Created At',
            ]);

            $query->chunk(250, function ($payments) use ($handle) {
                foreach ($payments as $payment) {
                    fputcsv($handle, [
                        $payment->id,
                        $payment->user?->name ?? 'N/A',
                        $payment->user?->email ?? 'N/A',
                        $payment->course?->title ?? 'N/A',
                        $payment->gateway_order_id ?? 'N/A',
                        number_format($payment->amount / 100, 2, '.', ''),
                        $payment->currency ?? 'INR',
                        ucfirst($payment->status),
                        $payment->payment_method ?? 'Razorpay',
                        $payment->paid_at?->format('Y-m-d H:i:s') ?? '',
                        $payment->created_at?->format('Y-m-d H:i:s') ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export revenue report as PDF.
     */
    public function exportRevenuePdf(array $filters = []): Response
    {
        $dateRange = $this->analyticsService->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $payments = Payment::with(['user', 'course'])
            ->when($start, fn ($q) => $q->where('created_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('created_at', '<=', $end))
            ->when(! empty($filters['course_id']), fn ($q) => $q->where('course_id', $filters['course_id']))
            ->when(! empty($filters['payment_status']), fn ($q) => $q->where('status', $filters['payment_status']))
            ->latest('created_at')
            ->limit(500)
            ->get();

        $financials = $this->analyticsService->getFinancialAnalytics($filters);

        $pdf = Pdf::loadView('admin.analytics.exports.revenue-pdf', [
            'payments' => $payments,
            'financials' => $financials,
            'filters' => $filters,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('revenue-report-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export course performance report as CSV.
     */
    public function exportCoursesCsv(array $filters = []): StreamedResponse
    {
        $courseData = $this->analyticsService->getCoursePerformanceReports($filters);
        $reports = $courseData['reports'];
        $filename = 'course-performance-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Course ID',
                'Title',
                'Category',
                'Status',
                'Total Enrolled',
                'Approved Enrolled',
                'In Progress',
                'Completed Students',
                'Completion Rate (%)',
                'Average Progress (%)',
                'Published Lessons',
                'Quiz Attempts',
                'Quiz Avg Score (%)',
                'Quiz Pass Rate (%)',
                'Assignment Submissions',
                'Assignments Graded',
                'Assignments Pending',
                'Assignments Avg Marks',
            ]);

            foreach ($reports as $item) {
                fputcsv($handle, [
                    $item['course_id'],
                    $item['title'],
                    $item['category'],
                    $item['status'],
                    $item['total_enrolled'],
                    $item['approved_enrolled'],
                    $item['in_progress_students'],
                    $item['completed_students'],
                    $item['completion_rate'],
                    $item['average_progress'],
                    $item['published_lessons'],
                    $item['quiz_stats']['total_attempts'],
                    $item['quiz_stats']['avg_score'] ?? 'N/A',
                    $item['quiz_stats']['pass_rate'] ?? 'N/A',
                    $item['assignment_stats']['submissions'],
                    $item['assignment_stats']['graded'],
                    $item['assignment_stats']['pending_grading'],
                    $item['assignment_stats']['avg_marks'] ?? 'N/A',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export course performance report as PDF.
     */
    public function exportCoursesPdf(array $filters = []): Response
    {
        $courseData = $this->analyticsService->getCoursePerformanceReports($filters);

        $pdf = Pdf::loadView('admin.analytics.exports.courses-pdf', [
            'reports' => $courseData['reports'],
            'avg_completion' => $courseData['avg_platform_completion_rate'],
            'filters' => $filters,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('courses-performance-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export student engagement / progress report as CSV.
     */
    public function exportStudentsCsv(array $filters = []): StreamedResponse
    {
        $students = User::where('role', User::ROLE_STUDENT)
            ->withCount([
                'enrollments as total_enrollments',
                'enrollments as approved_enrollments' => fn ($q) => $q->where('status', Enrollment::STATUS_APPROVED),
                'quizAttempts as quiz_attempts_count',
                'assignmentSubmissions as assignments_count',
                'certificates as certificates_count' => fn ($q) => $q->whereNull('revoked_at'),
            ])
            ->orderBy('name');

        $filename = 'student-engagement-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Student ID',
                'Name',
                'Email',
                'Phone',
                'Account Status',
                'Total Enrollments',
                'Approved Enrollments',
                'Quiz Attempts',
                'Assignments Submitted',
                'Certificates Earned',
                'Joined At',
            ]);

            $students->chunk(250, function ($rows) use ($handle) {
                foreach ($rows as $st) {
                    fputcsv($handle, [
                        $st->id,
                        $st->name,
                        $st->email,
                        $st->phone ?? 'N/A',
                        ucfirst($st->status),
                        $st->total_enrollments,
                        $st->approved_enrollments,
                        $st->quiz_attempts_count,
                        $st->assignments_count,
                        $st->certificates_count,
                        $st->created_at?->format('Y-m-d') ?? '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export student engagement report as PDF.
     */
    public function exportStudentsPdf(array $filters = []): Response
    {
        $engagement = $this->analyticsService->getStudentEngagementAnalytics($filters);

        $students = User::where('role', User::ROLE_STUDENT)
            ->withCount([
                'enrollments as approved_enrollments' => fn ($q) => $q->where('status', Enrollment::STATUS_APPROVED),
                'quizAttempts as quiz_attempts_count',
                'assignmentSubmissions as assignments_count',
                'certificates as certificates_count' => fn ($q) => $q->whereNull('revoked_at'),
            ])
            ->latest()
            ->limit(100)
            ->get();

        $pdf = Pdf::loadView('admin.analytics.exports.students-pdf', [
            'engagement' => $engagement,
            'students' => $students,
            'filters' => $filters,
            'generated_at' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('student-engagement-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Export instructor performance report as CSV.
     */
    public function exportInstructorsCsv(array $filters = [], ?User $specificInstructor = null): StreamedResponse
    {
        $analytics = $this->analyticsService->getInstructorPerformanceAnalytics($specificInstructor, $filters);
        $instructors = $analytics['instructors'];
        $filename = 'instructor-performance-'.now()->format('Y-m-d-His').'.csv';

        return response()->stream(function () use ($instructors) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Instructor ID',
                'Name',
                'Email',
                'Designation',
                'Specialization',
                'Assigned Courses Count',
                'Total Students',
                'Approved Students',
                'Completed Students',
                'Submissions Graded',
                'Pending Workload',
                'Avg Turnaround (Hours)',
            ]);

            foreach ($instructors as $inst) {
                fputcsv($handle, [
                    $inst['instructor_id'],
                    $inst['name'],
                    $inst['email'],
                    $inst['designation'],
                    $inst['specialization'],
                    $inst['assigned_courses_count'],
                    $inst['total_students'],
                    $inst['approved_students'],
                    $inst['completed_students'],
                    $inst['submissions_graded'],
                    $inst['pending_workload'],
                    $inst['avg_turnaround_hours'] ?? 'N/A',
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export instructor performance report as PDF.
     */
    public function exportInstructorsPdf(array $filters = [], ?User $specificInstructor = null): Response
    {
        $analytics = $this->analyticsService->getInstructorPerformanceAnalytics($specificInstructor, $filters);

        $pdf = Pdf::loadView('admin.analytics.exports.instructors-pdf', [
            'instructors' => $analytics['instructors'],
            'summary' => $analytics,
            'filters' => $filters,
            'generated_at' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('instructor-performance-'.now()->format('Y-m-d').'.pdf');
    }
}
