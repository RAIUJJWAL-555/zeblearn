<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Standard activity window in days for student engagement.
     */
    public const ACTIVITY_WINDOW_DAYS = 30;

    public function __construct(
        protected LessonProgressService $progressService
    ) {}

    /**
     * Parse date range filters with robust defaults.
     *
     * @param  array<string, mixed>  $filters
     * @return array{start: ?Carbon, end: ?Carbon, preset: string}
     */
    public function parseDateRange(array $filters): array
    {
        $preset = $filters['date_range'] ?? 'all_time';
        $now = now();

        switch ($preset) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'preset' => 'today',
                ];
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->startOfDay(),
                    'end' => $now->copy()->subDay()->endOfDay(),
                    'preset' => 'yesterday',
                ];
            case 'last_7_days':
                return [
                    'start' => $now->copy()->subDays(6)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'preset' => 'last_7_days',
                ];
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(29)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'preset' => 'last_30_days',
                ];
            case 'last_90_days':
                return [
                    'start' => $now->copy()->subDays(89)->startOfDay(),
                    'end' => $now->copy()->endOfDay(),
                    'preset' => 'last_90_days',
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth(),
                    'preset' => 'this_month',
                ];
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth(),
                    'end' => $now->copy()->subMonth()->endOfMonth(),
                    'preset' => 'last_month',
                ];
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear(),
                    'preset' => 'this_year',
                ];
            case 'custom':
                $start = ! empty($filters['start_date']) ? Carbon::parse($filters['start_date'])->startOfDay() : null;
                $end = ! empty($filters['end_date']) ? Carbon::parse($filters['end_date'])->endOfDay() : null;

                return [
                    'start' => $start,
                    'end' => $end,
                    'preset' => 'custom',
                ];
            case 'all_time':
            default:
                return [
                    'start' => null,
                    'end' => null,
                    'preset' => 'all_time',
                ];
        }
    }

    /**
     * Database-driver-aware month formatting string.
     */
    protected function getMonthGroupSql(string $column): string
    {
        $driver = DB::connection()->getDriverName();

        return $driver === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }

    /**
     * Database-driver-aware day formatting string.
     */
    protected function getDayGroupSql(string $column): string
    {
        $driver = DB::connection()->getDriverName();

        return $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', {$column})"
            : "DATE({$column})";
    }

    /**
     * Get platform-wide overview KPI metrics.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getPlatformOverview(array $filters = []): array
    {
        $dateRange = $this->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // 1. User counts
        $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
        $totalInstructors = User::where('role', User::ROLE_INSTRUCTOR)->count();
        $totalAdmins = User::where('role', User::ROLE_ADMIN)->count();

        // 2. Course counts
        $totalCourses = Course::count();
        $publishedCourses = Course::published()->count();
        $draftCourses = Course::where('status', '!=', 'available')->orWhereNull('published_at')->count();

        // 3. Enrollment totals (filtered by date if specified)
        $enrollmentsQuery = Enrollment::query();
        if ($start) {
            $enrollmentsQuery->where('requested_at', '>=', $start);
        }
        if ($end) {
            $enrollmentsQuery->where('requested_at', '<=', $end);
        }
        if (! empty($filters['course_id'])) {
            $enrollmentsQuery->where('course_id', $filters['course_id']);
        }

        $totalEnrollments = (clone $enrollmentsQuery)->count();
        $pendingEnrollments = (clone $enrollmentsQuery)->where('status', Enrollment::STATUS_PENDING)->count();
        $approvedEnrollments = (clone $enrollmentsQuery)->where('status', Enrollment::STATUS_APPROVED)->count();

        // 4. Course completion rate
        // Definition: Approved enrollments that have completed all lessons or received an active certificate
        $approvedWithCertificates = (clone $enrollmentsQuery)
            ->where('status', Enrollment::STATUS_APPROVED)
            ->whereHas('certificate', fn ($q) => $q->whereNull('revoked_at'))
            ->count();

        $courseCompletionRate = $approvedEnrollments > 0
            ? (int) round(($approvedWithCertificates / $approvedEnrollments) * 100)
            : 0;

        // 5. Active users definition
        // Active students within the activity window (default 30 days)
        $activityCutoff = now()->subDays(self::ACTIVITY_WINDOW_DAYS);
        $activeStudentIds = $this->getActiveStudentIds($activityCutoff);
        $activeStudentsCount = count($activeStudentIds);
        $inactiveStudentsCount = max(0, $totalStudents - $activeStudentsCount);

        // 6. Collected revenue (paid only, in INR rupees)
        $financialOverview = $this->getFinancialAnalytics($filters);

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudentsCount,
            'inactive_students' => $inactiveStudentsCount,
            'total_instructors' => $totalInstructors,
            'total_admins' => $totalAdmins,
            'total_courses' => $totalCourses,
            'published_courses' => $publishedCourses,
            'draft_courses' => $draftCourses,
            'total_enrollments' => $totalEnrollments,
            'pending_enrollments' => $pendingEnrollments,
            'approved_enrollments' => $approvedEnrollments,
            'course_completion_rate' => $courseCompletionRate,
            'total_revenue' => $financialOverview['total_collected_revenue'],
            'formatted_total_revenue' => '₹'.number_format($financialOverview['total_collected_revenue'], 2),
            'activity_window_days' => self::ACTIVITY_WINDOW_DAYS,
            'date_preset' => $dateRange['preset'],
        ];
    }

    /**
     * Identify IDs of students active since a given timestamp.
     * Activity is defined based on authentic timestamps:
     * - Lesson accessed/completed
     * - Quiz attempt started/submitted
     * - Assignment submitted
     * - Enrollment requested
     *
     * @return array<int>
     */
    public function getActiveStudentIds(Carbon $since): array
    {
        $lessonStudentIds = LessonProgress::where(function ($q) use ($since) {
            $q->where('last_accessed_at', '>=', $since)
                ->orWhere('completed_at', '>=', $since);
        })->distinct()->pluck('user_id')->all();

        $quizStudentIds = QuizAttempt::where('started_at', '>=', $since)
            ->distinct()->pluck('user_id')->all();

        $assignmentStudentIds = AssignmentSubmission::where('submitted_at', '>=', $since)
            ->distinct()->pluck('user_id')->all();

        $enrollmentStudentIds = Enrollment::where('requested_at', '>=', $since)
            ->distinct()->pluck('user_id')->all();

        $merged = array_unique(array_merge(
            $lessonStudentIds,
            $quizStudentIds,
            $assignmentStudentIds,
            $enrollmentStudentIds
        ));

        // Ensure only students are counted
        return User::where('role', User::ROLE_STUDENT)
            ->whereIn('id', $merged)
            ->pluck('id')
            ->all();
    }

    /**
     * Get comprehensive financial analytics.
     * Strictly distinguishes collected (paid) revenue from pending, failed, or refunded amounts.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getFinancialAnalytics(array $filters = []): array
    {
        $dateRange = $this->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $baseQuery = Payment::query();

        if ($start) {
            $baseQuery->where(function ($q) use ($start) {
                $q->where('paid_at', '>=', $start)
                    ->orWhere(function ($q2) use ($start) {
                        $q2->whereNull('paid_at')->where('created_at', '>=', $start);
                    });
            });
        }
        if ($end) {
            $baseQuery->where(function ($q) use ($end) {
                $q->where('paid_at', '<=', $end)
                    ->orWhere(function ($q2) use ($end) {
                        $q2->whereNull('paid_at')->where('created_at', '<=', $end);
                    });
            });
        }
        if (! empty($filters['course_id'])) {
            $baseQuery->where('course_id', $filters['course_id']);
        }
        if (! empty($filters['payment_status'])) {
            $baseQuery->where('status', $filters['payment_status']);
        }

        // Status counts and amounts (amounts in DB are in paise; / 100 for rupees)
        $statusBreakdown = (clone $baseQuery)
            ->selectRaw('status, count(*) as count, sum(amount) as total_amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $paidCount = (int) ($statusBreakdown[Payment::STATUS_PAID]->count ?? 0);
        $paidAmountRupees = ((int) ($statusBreakdown[Payment::STATUS_PAID]->total_amount ?? 0)) / 100;

        $pendingCount = (int) ($statusBreakdown[Payment::STATUS_PENDING]->count ?? 0);
        $pendingAmountRupees = ((int) ($statusBreakdown[Payment::STATUS_PENDING]->total_amount ?? 0)) / 100;

        $failedCount = (int) ($statusBreakdown[Payment::STATUS_FAILED]->count ?? 0);
        $failedAmountRupees = ((int) ($statusBreakdown[Payment::STATUS_FAILED]->total_amount ?? 0)) / 100;

        $refundedCount = (int) ($statusBreakdown[Payment::STATUS_REFUNDED]->count ?? 0);
        $refundedAmountRupees = ((int) ($statusBreakdown[Payment::STATUS_REFUNDED]->total_amount ?? 0)) / 100;

        $totalTransactions = $paidCount + $pendingCount + $failedCount + $refundedCount;
        $successRate = $totalTransactions > 0 ? (int) round(($paidCount / $totalTransactions) * 100) : 0;

        // Daily / Monthly Revenue trends (Paid transactions only)
        $monthSql = $this->getMonthGroupSql('paid_at');
        $monthlyRevenueQuery = Payment::where('status', Payment::STATUS_PAID)
            ->whereNotNull('paid_at');

        if ($start) {
            $monthlyRevenueQuery->where('paid_at', '>=', $start);
        } else {
            // Default to past 12 months for trend if all time
            $monthlyRevenueQuery->where('paid_at', '>=', now()->subMonths(11)->startOfMonth());
        }
        if ($end) {
            $monthlyRevenueQuery->where('paid_at', '<=', $end);
        }
        if (! empty($filters['course_id'])) {
            $monthlyRevenueQuery->where('course_id', $filters['course_id']);
        }

        $monthlyTrend = $monthlyRevenueQuery
            ->selectRaw("{$monthSql} as period, count(*) as tx_count, sum(amount) as total_amount")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                $amt = ((int) $row->total_amount) / 100;
                $date = Carbon::createFromFormat('Y-m', $row->period);

                return [
                    'period' => $row->period,
                    'label' => $date ? $date->format('M Y') : $row->period,
                    'short_month' => $date ? $date->format('M') : $row->period,
                    'transactions' => (int) $row->tx_count,
                    'amount' => $amt,
                    'formatted_amount' => '₹'.number_format($amt, 2),
                ];
            });

        // Course-wise Revenue (Paid transactions only)
        $courseRevenue = Payment::where('payments.status', Payment::STATUS_PAID)
            ->join('courses', 'payments.course_id', '=', 'courses.id')
            ->when($start, fn ($q) => $q->where('payments.paid_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('payments.paid_at', '<=', $end))
            ->when(! empty($filters['course_id']), fn ($q) => $q->where('payments.course_id', $filters['course_id']))
            ->selectRaw('courses.id as course_id, courses.name as course_title, courses.slug as course_slug, count(payments.id) as paid_count, sum(payments.amount) as total_amount')
            ->groupBy('courses.id', 'courses.name', 'courses.slug')
            ->orderByDesc('total_amount')
            ->get()
            ->map(function ($row) use ($paidAmountRupees) {
                $amt = ((int) $row->total_amount) / 100;
                $pct = $paidAmountRupees > 0 ? (int) round(($amt / $paidAmountRupees) * 100) : 0;

                return [
                    'course_id' => $row->course_id,
                    'title' => $row->course_title,
                    'slug' => $row->course_slug,
                    'paid_count' => (int) $row->paid_count,
                    'amount' => $amt,
                    'formatted_amount' => '₹'.number_format($amt, 2),
                    'percentage' => $pct,
                ];
            });

        return [
            'total_collected_revenue' => $paidAmountRupees,
            'formatted_collected_revenue' => '₹'.number_format($paidAmountRupees, 2),
            'pending_amount' => $pendingAmountRupees,
            'formatted_pending_amount' => '₹'.number_format($pendingAmountRupees, 2),
            'failed_amount' => $failedAmountRupees,
            'formatted_failed_amount' => '₹'.number_format($failedAmountRupees, 2),
            'refunded_amount' => $refundedAmountRupees,
            'formatted_refunded_amount' => '₹'.number_format($refundedAmountRupees, 2),
            'paid_count' => $paidCount,
            'pending_count' => $pendingCount,
            'failed_count' => $failedCount,
            'refunded_count' => $refundedCount,
            'total_transactions' => $totalTransactions,
            'success_rate' => $successRate,
            'monthly_trend' => $monthlyTrend,
            'course_revenue' => $courseRevenue,
            'max_monthly_revenue' => max($monthlyTrend->max('amount') ?? 0, 1),
        ];
    }

    /**
     * Get detailed enrollment analytics and status distributions.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getEnrollmentAnalytics(array $filters = []): array
    {
        $dateRange = $this->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $baseQuery = Enrollment::query();

        if ($start) {
            $baseQuery->where('requested_at', '>=', $start);
        }
        if ($end) {
            $baseQuery->where('requested_at', '<=', $end);
        }
        if (! empty($filters['course_id'])) {
            $baseQuery->where('course_id', $filters['course_id']);
        }
        if (! empty($filters['status'])) {
            $baseQuery->where('status', $filters['status']);
        }

        // Status distribution
        $statusCounts = (clone $baseQuery)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $approved = (int) ($statusCounts[Enrollment::STATUS_APPROVED] ?? 0);
        $pending = (int) ($statusCounts[Enrollment::STATUS_PENDING] ?? 0);
        $rejected = (int) ($statusCounts[Enrollment::STATUS_REJECTED] ?? 0);
        $cancelled = (int) ($statusCounts[Enrollment::STATUS_CANCELLED] ?? 0);
        $total = $approved + $pending + $rejected + $cancelled;

        $approvalRate = $total > 0 ? (int) round(($approved / $total) * 100) : 0;

        // Monthly trends
        $monthSql = $this->getMonthGroupSql('requested_at');
        $monthlyEnrollmentsQuery = Enrollment::query();
        if ($start) {
            $monthlyEnrollmentsQuery->where('requested_at', '>=', $start);
        } else {
            $monthlyEnrollmentsQuery->where('requested_at', '>=', now()->subMonths(11)->startOfMonth());
        }
        if ($end) {
            $monthlyEnrollmentsQuery->where('requested_at', '<=', $end);
        }
        if (! empty($filters['course_id'])) {
            $monthlyEnrollmentsQuery->where('course_id', $filters['course_id']);
        }

        $monthlyTrend = $monthlyEnrollmentsQuery
            ->selectRaw("{$monthSql} as period, count(*) as total_count, sum(case when status = 'approved' then 1 else 0 end) as approved_count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                $date = Carbon::createFromFormat('Y-m', $row->period);

                return [
                    'period' => $row->period,
                    'label' => $date ? $date->format('M Y') : $row->period,
                    'short_month' => $date ? $date->format('M') : $row->period,
                    'total' => (int) $row->total_count,
                    'approved' => (int) $row->approved_count,
                ];
            });

        // Course-wise enrollment performance
        $courseEnrollments = Enrollment::join('courses', 'enrollments.course_id', '=', 'courses.id')
            ->when($start, fn ($q) => $q->where('enrollments.requested_at', '>=', $start))
            ->when($end, fn ($q) => $q->where('enrollments.requested_at', '<=', $end))
            ->when(! empty($filters['course_id']), fn ($q) => $q->where('enrollments.course_id', $filters['course_id']))
            ->selectRaw("
                courses.id as course_id,
                courses.name as course_title,
                count(enrollments.id) as total_enrollments,
                sum(case when enrollments.status = 'approved' then 1 else 0 end) as approved_count,
                sum(case when enrollments.status = 'pending' then 1 else 0 end) as pending_count,
                sum(case when enrollments.status = 'rejected' then 1 else 0 end) as rejected_count
            ")
            ->groupBy('courses.id', 'courses.name')
            ->orderByDesc('total_enrollments')
            ->get()
            ->map(function ($row) {
                $total = (int) $row->total_enrollments;
                $approved = (int) $row->approved_count;
                $rate = $total > 0 ? (int) round(($approved / $total) * 100) : 0;

                return [
                    'course_id' => $row->course_id,
                    'title' => $row->course_title,
                    'total_enrollments' => $total,
                    'approved_count' => $approved,
                    'pending_count' => (int) $row->pending_count,
                    'rejected_count' => (int) $row->rejected_count,
                    'approval_rate' => $rate,
                ];
            });

        return [
            'total_enrollments' => $total,
            'approved_count' => $approved,
            'pending_count' => $pending,
            'rejected_count' => $rejected,
            'cancelled_count' => $cancelled,
            'approval_rate' => $approvalRate,
            'monthly_trend' => $monthlyTrend,
            'max_monthly_enrollment' => max($monthlyTrend->max('total') ?? 0, 1),
            'course_enrollments' => $courseEnrollments,
        ];
    }

    /**
     * Get course performance reports across all or filtered courses.
     * Computes completion rates, average student progress, quiz performance, and assignment stats.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getCoursePerformanceReports(array $filters = []): array
    {
        $coursesQuery = Course::withCount([
            'enrollments as total_enrollments',
            'enrollments as approved_enrollments' => fn ($q) => $q->where('status', Enrollment::STATUS_APPROVED),
            'enrollments as pending_enrollments' => fn ($q) => $q->where('status', Enrollment::STATUS_PENDING),
            'certificates as certificates_issued' => fn ($q) => $q->whereNull('revoked_at'),
            'quizzes as total_quizzes',
            'assignments as total_assignments',
        ]);

        if (! empty($filters['course_id'])) {
            $coursesQuery->where('id', $filters['course_id']);
        }
        if (! empty($filters['category'])) {
            $coursesQuery->where('category', $filters['category']);
        }
        if (! empty($filters['course_status'])) {
            $coursesQuery->where('status', $filters['course_status']);
        }

        $courses = $coursesQuery->orderBy('name')->get();
        $courseIds = $courses->pluck('id')->all();

        // Total published lessons per course
        $lessonsCountPerCourse = DB::table('lessons')
            ->join('course_modules', 'lessons.course_module_id', '=', 'course_modules.id')
            ->whereIn('course_modules.course_id', $courseIds)
            ->where('course_modules.status', 'published')
            ->where('lessons.status', 'published')
            ->groupBy('course_modules.course_id')
            ->select('course_modules.course_id', DB::raw('count(lessons.id) as published_lessons_count'))
            ->pluck('published_lessons_count', 'course_id')
            ->all();

        // Quiz metrics per course: average score, pass rate, total attempts
        $quizMetricsPerCourse = DB::table('quiz_attempts')
            ->join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
            ->whereIn('quizzes.course_id', $courseIds)
            ->where('quiz_attempts.status', QuizAttempt::STATUS_SUBMITTED)
            ->groupBy('quizzes.course_id')
            ->selectRaw('
                quizzes.course_id,
                count(quiz_attempts.id) as attempts_count,
                avg(quiz_attempts.percentage) as avg_score,
                sum(case when quiz_attempts.is_passed = 1 then 1 else 0 end) as passed_count
            ')
            ->get()
            ->keyBy('course_id');

        // Assignment metrics per course: submissions, graded, average marks
        $assignmentMetricsPerCourse = DB::table('assignment_submissions')
            ->join('assignments', 'assignment_submissions.assignment_id', '=', 'assignments.id')
            ->whereIn('assignments.course_id', $courseIds)
            ->groupBy('assignments.course_id')
            ->selectRaw('
                assignments.course_id,
                count(assignment_submissions.id) as submissions_count,
                sum(case when assignment_submissions.status = "graded" then 1 else 0 end) as graded_count,
                sum(case when assignment_submissions.status in ("submitted", "under_review") then 1 else 0 end) as pending_grading_count,
                avg(case when assignment_submissions.status = "graded" then assignment_submissions.marks_awarded else null end) as avg_marks
            ')
            ->get()
            ->keyBy('course_id');

        // Course reports mapping
        $reports = $courses->map(function (Course $course) use ($lessonsCountPerCourse, $quizMetricsPerCourse, $assignmentMetricsPerCourse) {
            $totalEnrollments = (int) $course->total_enrollments;
            $approvedEnrollments = (int) $course->approved_enrollments;
            $completedStudents = (int) $course->certificates_issued;
            $inProgressStudents = max(0, $approvedEnrollments - $completedStudents);
            $completionRate = $approvedEnrollments > 0
                ? (int) round(($completedStudents / $approvedEnrollments) * 100)
                : 0;

            $totalPublishedLessons = (int) ($lessonsCountPerCourse[$course->id] ?? 0);

            // Compute average progress for approved students in this course
            $avgProgress = 0;
            if ($approvedEnrollments > 0 && $totalPublishedLessons > 0) {
                $completedLessonEvents = DB::table('lesson_progress')
                    ->join('lessons', 'lesson_progress.lesson_id', '=', 'lessons.id')
                    ->join('course_modules', 'lessons.course_module_id', '=', 'course_modules.id')
                    ->where('course_modules.course_id', $course->id)
                    ->where('course_modules.status', 'published')
                    ->where('lessons.status', 'published')
                    ->where('lesson_progress.status', LessonProgress::STATUS_COMPLETED)
                    ->count();

                $potentialTotal = $approvedEnrollments * $totalPublishedLessons;
                $avgProgress = $potentialTotal > 0
                    ? min(100, (int) round(($completedLessonEvents / $potentialTotal) * 100))
                    : 0;
            }

            // Quiz data
            $quizData = $quizMetricsPerCourse->get($course->id);
            $quizAttempts = (int) ($quizData->attempts_count ?? 0);
            $quizAvgScore = $quizData ? (int) round((float) $quizData->avg_score) : null;
            $quizPassRate = $quizAttempts > 0
                ? (int) round(((int) $quizData->passed_count / $quizAttempts) * 100)
                : null;

            // Assignment data
            $assignmentData = $assignmentMetricsPerCourse->get($course->id);
            $assignmentSubmissions = (int) ($assignmentData->submissions_count ?? 0);
            $assignmentGraded = (int) ($assignmentData->graded_count ?? 0);
            $assignmentPending = (int) ($assignmentData->pending_grading_count ?? 0);
            $assignmentAvgMarks = $assignmentData && $assignmentData->avg_marks !== null
                ? round((float) $assignmentData->avg_marks, 1)
                : null;

            return [
                'course_id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'status' => $course->status,
                'category' => $course->category,
                'total_enrolled' => $totalEnrollments,
                'approved_enrolled' => $approvedEnrollments,
                'pending_enrolled' => (int) $course->pending_enrollments,
                'completed_students' => $completedStudents,
                'in_progress_students' => $inProgressStudents,
                'completion_rate' => $completionRate,
                'average_progress' => $avgProgress,
                'published_lessons' => $totalPublishedLessons,
                'quiz_stats' => [
                    'total_quizzes' => (int) $course->total_quizzes,
                    'total_attempts' => $quizAttempts,
                    'avg_score' => $quizAvgScore,
                    'pass_rate' => $quizPassRate,
                ],
                'assignment_stats' => [
                    'total_assignments' => (int) $course->total_assignments,
                    'submissions' => $assignmentSubmissions,
                    'graded' => $assignmentGraded,
                    'pending_grading' => $assignmentPending,
                    'avg_marks' => $assignmentAvgMarks,
                ],
            ];
        });

        return [
            'reports' => $reports,
            'total_courses_analyzed' => $reports->count(),
            'avg_platform_completion_rate' => $reports->count() > 0 ? (int) round($reports->avg('completion_rate')) : 0,
        ];
    }

    /**
     * Get student engagement analytics.
     * Computes active/inactive student ratio, lesson completion trends,
     * progress distributions, quiz participation, and assignment activity.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getStudentEngagementAnalytics(array $filters = []): array
    {
        $dateRange = $this->parseDateRange($filters);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        $totalStudents = User::where('role', User::ROLE_STUDENT)->count();

        // 1. Activity in window
        $activityCutoff = now()->subDays(self::ACTIVITY_WINDOW_DAYS);
        $activeStudentIds = $this->getActiveStudentIds($activityCutoff);
        $activeStudentsCount = count($activeStudentIds);
        $inactiveStudentsCount = max(0, $totalStudents - $activeStudentsCount);
        $activityRate = $totalStudents > 0 ? (int) round(($activeStudentsCount / $totalStudents) * 100) : 0;

        // 2. Lesson completion trends over time
        $monthSql = $this->getMonthGroupSql('completed_at');
        $lessonTrendQuery = LessonProgress::where('status', LessonProgress::STATUS_COMPLETED)
            ->whereNotNull('completed_at');

        if ($start) {
            $lessonTrendQuery->where('completed_at', '>=', $start);
        } else {
            $lessonTrendQuery->where('completed_at', '>=', now()->subMonths(11)->startOfMonth());
        }
        if ($end) {
            $lessonTrendQuery->where('completed_at', '<=', $end);
        }

        $lessonCompletionTrend = $lessonTrendQuery
            ->selectRaw("{$monthSql} as period, count(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                $date = Carbon::createFromFormat('Y-m', $row->period);

                return [
                    'period' => $row->period,
                    'label' => $date ? $date->format('M Y') : $row->period,
                    'count' => (int) $row->count,
                ];
            });

        // 3. Progress distribution across approved enrollments
        // Buckets: 0%, 1-25%, 26-50%, 51-75%, 76-99%, 100%
        $distributionBuckets = [
            '0%' => 0,
            '1-25%' => 0,
            '26-50%' => 0,
            '51-75%' => 0,
            '76-99%' => 0,
            '100%' => 0,
        ];

        $approvedEnrollments = Enrollment::where('status', Enrollment::STATUS_APPROVED)
            ->with(['user', 'course'])
            ->get();

        foreach ($approvedEnrollments as $enrollment) {
            if (! $enrollment->user || ! $enrollment->course) {
                continue;
            }

            $prog = $this->progressService->getCourseProgress($enrollment->user, $enrollment->course);
            $pct = $prog['percentage'];

            if ($pct === 0) {
                $distributionBuckets['0%']++;
            } elseif ($pct <= 25) {
                $distributionBuckets['1-25%']++;
            } elseif ($pct <= 50) {
                $distributionBuckets['26-50%']++;
            } elseif ($pct <= 75) {
                $distributionBuckets['51-75%']++;
            } elseif ($pct < 100) {
                $distributionBuckets['76-99%']++;
            } else {
                $distributionBuckets['100%']++;
            }
        }

        // 4. Quiz Participation & Scores
        $quizAttemptsQuery = QuizAttempt::where('status', QuizAttempt::STATUS_SUBMITTED);
        if ($start) {
            $quizAttemptsQuery->where('submitted_at', '>=', $start);
        }
        if ($end) {
            $quizAttemptsQuery->where('submitted_at', '<=', $end);
        }

        $totalQuizAttempts = (clone $quizAttemptsQuery)->count();
        $passedQuizAttempts = (clone $quizAttemptsQuery)->where('is_passed', true)->count();
        $failedQuizAttempts = $totalQuizAttempts - $passedQuizAttempts;
        $avgQuizScore = $totalQuizAttempts > 0 ? (int) round((float) (clone $quizAttemptsQuery)->avg('percentage')) : 0;

        // 5. Assignment submission activity
        $assignmentQuery = AssignmentSubmission::query();
        if ($start) {
            $assignmentQuery->where('submitted_at', '>=', $start);
        }
        if ($end) {
            $assignmentQuery->where('submitted_at', '<=', $end);
        }

        $totalSubmissions = (clone $assignmentQuery)->count();
        $onTimeSubmissions = (clone $assignmentQuery)->where('is_late', false)->count();
        $lateSubmissions = (clone $assignmentQuery)->where('is_late', true)->count();
        $gradedSubmissions = (clone $assignmentQuery)->where('status', AssignmentSubmission::STATUS_GRADED)->count();

        // 6. Course completion (certificate issuance) trends
        $certMonthSql = $this->getMonthGroupSql('issued_at');
        $certTrend = Certificate::whereNull('revoked_at')
            ->when($start, fn ($q) => $q->where('issued_at', '>=', $start))
            ->when(! $start, fn ($q) => $q->where('issued_at', '>=', now()->subMonths(11)->startOfMonth()))
            ->when($end, fn ($q) => $q->where('issued_at', '<=', $end))
            ->selectRaw("{$certMonthSql} as period, count(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                $date = Carbon::createFromFormat('Y-m', $row->period);

                return [
                    'period' => $row->period,
                    'label' => $date ? $date->format('M Y') : $row->period,
                    'count' => (int) $row->count,
                ];
            });

        return [
            'total_students' => $totalStudents,
            'active_students' => $activeStudentsCount,
            'inactive_students' => $inactiveStudentsCount,
            'activity_rate' => $activityRate,
            'activity_window_days' => self::ACTIVITY_WINDOW_DAYS,
            'lesson_completion_trend' => $lessonCompletionTrend,
            'max_lesson_completion' => max($lessonCompletionTrend->max('count') ?? 0, 1),
            'progress_distribution' => $distributionBuckets,
            'total_approved_enrollments' => $approvedEnrollments->count(),
            'quiz_participation' => [
                'total_attempts' => $totalQuizAttempts,
                'passed' => $passedQuizAttempts,
                'failed' => $failedQuizAttempts,
                'avg_score' => $avgQuizScore,
                'pass_rate' => $totalQuizAttempts > 0 ? (int) round(($passedQuizAttempts / $totalQuizAttempts) * 100) : 0,
            ],
            'assignment_activity' => [
                'total_submissions' => $totalSubmissions,
                'on_time' => $onTimeSubmissions,
                'late' => $lateSubmissions,
                'graded' => $gradedSubmissions,
            ],
            'course_completion_trend' => $certTrend,
        ];
    }

    /**
     * Get performance analytics for instructors.
     * Can return all instructors (for admin) or strictly scoped to one instructor.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getInstructorPerformanceAnalytics(?User $specificInstructor = null, array $filters = []): array
    {
        $instructorsQuery = User::where('role', User::ROLE_INSTRUCTOR)
            ->with(['assignedCourses']);

        if ($specificInstructor) {
            $instructorsQuery->where('id', $specificInstructor->id);
        } elseif (! empty($filters['instructor_id'])) {
            $instructorsQuery->where('id', $filters['instructor_id']);
        }

        $instructors = $instructorsQuery->orderBy('name')->get();

        $dossiers = $instructors->map(function (User $instructor) {
            $courseIds = $instructor->assignedCourses->pluck('id')->all();
            $assignedCoursesCount = count($courseIds);

            // Distinct enrolled students across assigned courses
            $totalStudents = 0;
            $approvedStudents = 0;
            $completedStudents = 0;

            if ($assignedCoursesCount > 0) {
                $totalStudents = Enrollment::whereIn('course_id', $courseIds)
                    ->distinct('user_id')
                    ->count('user_id');

                $approvedStudents = Enrollment::whereIn('course_id', $courseIds)
                    ->where('status', Enrollment::STATUS_APPROVED)
                    ->distinct('user_id')
                    ->count('user_id');

                $completedStudents = Certificate::whereIn('course_id', $courseIds)
                    ->whereNull('revoked_at')
                    ->distinct('user_id')
                    ->count('user_id');
            }

            // Grading statistics
            $submissionsGradedByInstructor = AssignmentSubmission::where('grader_id', $instructor->id)
                ->where('status', AssignmentSubmission::STATUS_GRADED)
                ->count();

            // Pending workload in assigned courses
            $pendingWorkload = $assignedCoursesCount > 0
                ? AssignmentSubmission::whereHas('assignment', fn ($q) => $q->whereIn('course_id', $courseIds))
                    ->whereIn('status', [AssignmentSubmission::STATUS_SUBMITTED, AssignmentSubmission::STATUS_UNDER_REVIEW])
                    ->count()
                : 0;

            // Turnaround time: average hours between submitted_at and graded_at
            $gradedWithTimestamps = AssignmentSubmission::where('grader_id', $instructor->id)
                ->where('status', AssignmentSubmission::STATUS_GRADED)
                ->whereNotNull('submitted_at')
                ->whereNotNull('graded_at')
                ->get(['submitted_at', 'graded_at']);

            $avgTurnaroundHours = null;
            if ($gradedWithTimestamps->isNotEmpty()) {
                $totalHours = 0;
                foreach ($gradedWithTimestamps as $sub) {
                    $totalHours += abs($sub->submitted_at->diffInHours($sub->graded_at));
                }
                $avgTurnaroundHours = round($totalHours / $gradedWithTimestamps->count(), 1);
            }

            return [
                'instructor_id' => $instructor->id,
                'name' => $instructor->name,
                'email' => $instructor->email,
                'designation' => $instructor->designation ?? 'Faculty',
                'specialization' => $instructor->specialization ?? 'General',
                'assigned_courses_count' => $assignedCoursesCount,
                'assigned_courses' => $instructor->assignedCourses->map(fn ($c) => [
                    'id' => $c->id,
                    'title' => $c->title,
                    'slug' => $c->slug,
                ]),
                'total_students' => $totalStudents,
                'approved_students' => $approvedStudents,
                'completed_students' => $completedStudents,
                'submissions_graded' => $submissionsGradedByInstructor,
                'pending_workload' => $pendingWorkload,
                'avg_turnaround_hours' => $avgTurnaroundHours,
            ];
        });

        return [
            'instructors' => $dossiers,
            'total_instructors_analyzed' => $dossiers->count(),
            'total_assigned_courses' => $dossiers->sum('assigned_courses_count'),
            'total_graded_reviews' => $dossiers->sum('submissions_graded'),
            'total_pending_workload' => $dossiers->sum('pending_workload'),
        ];
    }

    /**
     * Get personal learning analytics for a student.
     * Strictly scopes to the specified student user.
     *
     * @return array<string, mixed>
     */
    public function getStudentPersonalAnalytics(User $student): array
    {
        // 1. Enrolled courses overview
        $enrollments = Enrollment::where('user_id', $student->id)
            ->with(['course.modules.lessons'])
            ->get();

        $totalEnrolled = $enrollments->count();
        $approvedEnrollments = $enrollments->where('status', Enrollment::STATUS_APPROVED);
        $approvedCount = $approvedEnrollments->count();

        // 2. Course-by-course progress
        $courseAnalytics = [];
        $totalLessonsAcrossCourses = 0;
        $totalCompletedLessons = 0;
        $completedCoursesCount = 0;

        foreach ($approvedEnrollments as $enr) {
            $course = $enr->course;
            if (! $course) {
                continue;
            }

            $prog = $this->progressService->getCourseProgress($student, $course);
            $totalLessonsAcrossCourses += $prog['total_lessons'];
            $totalCompletedLessons += $prog['completed_lessons'];

            $hasCertificate = Certificate::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->whereNull('revoked_at')
                ->exists();

            if ($prog['is_completed'] || $hasCertificate) {
                $completedCoursesCount++;
            }

            $courseAnalytics[] = [
                'course' => $course,
                'progress' => $prog,
                'has_certificate' => $hasCertificate,
            ];
        }

        $overallProgressPercentage = $totalLessonsAcrossCourses > 0
            ? (int) round(($totalCompletedLessons / $totalLessonsAcrossCourses) * 100)
            : 0;

        // 3. Quiz performance
        $quizAttempts = QuizAttempt::where('user_id', $student->id)
            ->where('status', QuizAttempt::STATUS_SUBMITTED)
            ->with(['quiz.course'])
            ->latest('submitted_at')
            ->get();

        $totalQuizAttempts = $quizAttempts->count();
        $passedQuizAttempts = $quizAttempts->where('is_passed', true)->count();
        $avgQuizScore = $totalQuizAttempts > 0 ? (int) round($quizAttempts->avg('percentage')) : 0;

        // 4. Assignment performance
        $assignmentSubmissions = AssignmentSubmission::where('user_id', $student->id)
            ->with(['assignment.course'])
            ->latest('submitted_at')
            ->get();

        $totalSubmissions = $assignmentSubmissions->count();
        $gradedSubmissions = $assignmentSubmissions->where('status', AssignmentSubmission::STATUS_GRADED);
        $gradedCount = $gradedSubmissions->count();
        $avgMarks = $gradedCount > 0 ? round($gradedSubmissions->avg('marks_awarded'), 1) : null;

        // 5. Certificates earned
        $certificates = Certificate::where('user_id', $student->id)
            ->whereNull('revoked_at')
            ->with('course')
            ->latest('issued_at')
            ->get();

        // 6. Recent learning activity timeline (last 6 months)
        $monthSql = $this->getMonthGroupSql('completed_at');
        $activityTimeline = LessonProgress::where('user_id', $student->id)
            ->where('status', LessonProgress::STATUS_COMPLETED)
            ->where('completed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("{$monthSql} as period, count(*) as count")
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(function ($row) {
                $date = Carbon::createFromFormat('Y-m', $row->period);

                return [
                    'period' => $row->period,
                    'label' => $date ? $date->format('M Y') : $row->period,
                    'count' => (int) $row->count,
                ];
            });

        return [
            'total_enrolled' => $totalEnrolled,
            'approved_count' => $approvedCount,
            'completed_courses_count' => $completedCoursesCount,
            'active_courses_count' => max(0, $approvedCount - $completedCoursesCount),
            'total_lessons' => $totalLessonsAcrossCourses,
            'completed_lessons' => $totalCompletedLessons,
            'overall_progress_percentage' => $overallProgressPercentage,
            'course_analytics' => $courseAnalytics,
            'quiz_stats' => [
                'total_attempts' => $totalQuizAttempts,
                'passed' => $passedQuizAttempts,
                'failed' => max(0, $totalQuizAttempts - $passedQuizAttempts),
                'avg_score' => $avgQuizScore,
                'recent_attempts' => $quizAttempts->take(5),
            ],
            'assignment_stats' => [
                'total_submissions' => $totalSubmissions,
                'graded_count' => $gradedCount,
                'avg_marks' => $avgMarks,
                'recent_submissions' => $assignmentSubmissions->take(5),
            ],
            'certificates_count' => $certificates->count(),
            'certificates' => $certificates,
            'activity_timeline' => $activityTimeline,
        ];
    }
}
