<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => User::STATUS_ACTIVE,
    ]);

    $this->instructor = User::factory()->create([
        'role' => User::ROLE_INSTRUCTOR,
        'status' => User::STATUS_ACTIVE,
        'designation' => 'Lead Faculty',
        'specialization' => 'Cloud Architecture',
    ]);

    $this->student = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'status' => User::STATUS_ACTIVE,
    ]);
});

test('admin can access analytics hub overview with valid platform kpis', function () {
    // Create test data
    $course = Course::factory()->create(['status' => 'available', 'published_at' => now()]);
    Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
        'approved_at' => now(),
    ]);

    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_123',
        'amount' => 500000, // 5,000 INR
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.analytics.index'));

    $response->assertOk();
    $response->assertViewIs('admin.analytics.index');
    $response->assertSee('LMS Operational Analytics');
    $response->assertSee('₹5,000.00');
    $response->assertViewHas('overview');
    $response->assertViewHas('financials');
    $response->assertViewHas('enrollments');
});

test('financial analytics strictly distinguishes paid collected revenue from pending, failed, and refunded amounts', function () {
    $course = Course::factory()->create(['status' => 'available', 'published_at' => now()]);

    // 1. Paid payment: 10,000 INR (1,000,000 paise)
    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_paid_1',
        'amount' => 1000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
    ]);

    // 2. Pending payment: 4,000 INR
    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_pend_1',
        'amount' => 400000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PENDING,
    ]);

    // 3. Failed payment: 3,000 INR
    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_fail_1',
        'amount' => 300000,
        'currency' => 'INR',
        'status' => Payment::STATUS_FAILED,
    ]);

    // 4. Refunded payment: 2,000 INR
    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_ref_1',
        'amount' => 200000,
        'currency' => 'INR',
        'status' => Payment::STATUS_REFUNDED,
    ]);

    /** @var AnalyticsService $service */
    $service = app(AnalyticsService::class);
    $financials = $service->getFinancialAnalytics();

    expect($financials['total_collected_revenue'])->toEqual(10000.0)
        ->and($financials['pending_amount'])->toEqual(4000.0)
        ->and($financials['failed_amount'])->toEqual(3000.0)
        ->and($financials['refunded_amount'])->toEqual(2000.0)
        ->and($financials['paid_count'])->toBe(1)
        ->and($financials['pending_count'])->toBe(1)
        ->and($financials['failed_count'])->toBe(1)
        ->and($financials['refunded_count'])->toBe(1);

    $response = $this->actingAs($this->admin)->get(route('admin.analytics.revenue'));
    $response->assertOk();
    $response->assertSee('₹10,000.00');
    $response->assertSee('₹4,000.00');
});

test('enrollment analytics accurately aggregates statuses and approval ratios', function () {
    $course = Course::factory()->create();

    // 2 approved
    Enrollment::create(['user_id' => $this->student->id, 'course_id' => $course->id, 'status' => Enrollment::STATUS_APPROVED, 'requested_at' => now(), 'approved_at' => now()]);
    $student2 = User::factory()->create(['role' => User::ROLE_STUDENT]);
    Enrollment::create(['user_id' => $student2->id, 'course_id' => $course->id, 'status' => Enrollment::STATUS_APPROVED, 'requested_at' => now(), 'approved_at' => now()]);

    // 1 pending
    $student3 = User::factory()->create(['role' => User::ROLE_STUDENT]);
    Enrollment::create(['user_id' => $student3->id, 'course_id' => $course->id, 'status' => Enrollment::STATUS_PENDING, 'requested_at' => now()]);

    // 1 rejected
    $student4 = User::factory()->create(['role' => User::ROLE_STUDENT]);
    Enrollment::create(['user_id' => $student4->id, 'course_id' => $course->id, 'status' => Enrollment::STATUS_REJECTED, 'requested_at' => now()]);

    /** @var AnalyticsService $service */
    $service = app(AnalyticsService::class);
    $enrollmentData = $service->getEnrollmentAnalytics();

    expect($enrollmentData['total_enrollments'])->toBe(4)
        ->and($enrollmentData['approved_count'])->toBe(2)
        ->and($enrollmentData['pending_count'])->toBe(1)
        ->and($enrollmentData['rejected_count'])->toBe(1)
        ->and($enrollmentData['approval_rate'])->toBe(50); // 2 of 4 = 50%

    $response = $this->actingAs($this->admin)->get(route('admin.analytics.enrollments'));
    $response->assertOk();
    $response->assertSee('50%');
});

test('course performance reports compute authentic progress, quiz metrics, and assignment evaluation stats', function () {
    $course = Course::factory()->create(['status' => 'available', 'published_at' => now()]);
    $module = CourseModule::create(['course_id' => $course->id, 'title' => 'Module 1', 'status' => 'published', 'order_index' => 1]);
    $lesson1 = Lesson::create(['course_module_id' => $module->id, 'title' => 'Lesson 1', 'status' => 'published', 'order_index' => 1]);
    $lesson2 = Lesson::create(['course_module_id' => $module->id, 'title' => 'Lesson 2', 'status' => 'published', 'order_index' => 2]);

    $enrollment = Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
        'approved_at' => now(),
    ]);

    // Lesson 1 completed
    LessonProgress::create([
        'user_id' => $this->student->id,
        'lesson_id' => $lesson1->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Quiz evaluation
    $quiz = Quiz::create([
        'course_id' => $course->id,
        'title' => 'Course Quiz',
        'slug' => 'course-quiz',
        'status' => 'published',
        'pass_percentage' => 70,
        'total_marks' => 100,
    ]);

    QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $this->student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'score' => 80,
        'total_marks' => 100,
        'percentage' => 80,
        'is_passed' => true,
        'started_at' => now()->subMinutes(30),
        'submitted_at' => now(),
    ]);

    // Assignment evaluation
    $assignment = Assignment::create([
        'course_id' => $course->id,
        'title' => 'Project 1',
        'slug' => 'project-1',
        'status' => 'published',
        'total_marks' => 100,
        'pass_marks' => 50,
    ]);

    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $this->student->id,
        'attempt_number' => 1,
        'status' => AssignmentSubmission::STATUS_GRADED,
        'marks_awarded' => 90,
        'submitted_at' => now()->subHours(5),
        'graded_at' => now()->subHours(1),
        'grader_id' => $this->instructor->id,
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.analytics.courses'));
    $response->assertOk();
    $response->assertSee($course->title);
    $response->assertSee('50%'); // 1 of 2 lessons = 50% avg progress
});

test('student engagement analytics accurately evaluates 30-day activity window and progress buckets', function () {
    $course = Course::factory()->create(['status' => 'available', 'published_at' => now()]);
    $module = CourseModule::create(['course_id' => $course->id, 'title' => 'Mod', 'status' => 'published', 'order_index' => 1]);
    $lesson = Lesson::create(['course_module_id' => $module->id, 'title' => 'Les', 'status' => 'published', 'order_index' => 1]);

    // Active student: has accessed lesson today
    LessonProgress::create([
        'user_id' => $this->student->id,
        'lesson_id' => $lesson->id,
        'status' => LessonProgress::STATUS_IN_PROGRESS,
        'last_accessed_at' => now(),
    ]);

    // Inactive student: created 60 days ago with zero recent actions
    $inactiveStudent = User::factory()->create([
        'role' => User::ROLE_STUDENT,
        'status' => User::STATUS_ACTIVE,
        'created_at' => now()->subDays(60),
    ]);

    /** @var AnalyticsService $service */
    $service = app(AnalyticsService::class);
    $engagement = $service->getStudentEngagementAnalytics();

    expect($engagement['active_students'])->toBe(1)
        ->and($engagement['inactive_students'])->toBe(1)
        ->and($engagement['activity_rate'])->toBe(50); // 1 of 2 students active

    $response = $this->actingAs($this->admin)->get(route('admin.analytics.students'));
    $response->assertOk();
    $response->assertSee('Last 30 Days');
});

test('admin can view instructor performance analytics with grading workload and turnaround hours', function () {
    $course = Course::factory()->create();
    $this->instructor->assignedCourses()->attach($course->id, ['assigned_at' => now()]);

    $assignment = Assignment::create([
        'course_id' => $course->id,
        'title' => 'Homework',
        'slug' => 'homework',
        'status' => 'published',
        'total_marks' => 100,
        'pass_marks' => 50,
    ]);

    // 1 Graded submission: submitted 10 hours ago, graded 2 hours ago => 8 hours turnaround
    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $this->student->id,
        'attempt_number' => 1,
        'status' => AssignmentSubmission::STATUS_GRADED,
        'marks_awarded' => 95,
        'submitted_at' => now()->subHours(10),
        'graded_at' => now()->subHours(2),
        'grader_id' => $this->instructor->id,
    ]);

    // 1 Pending submission in assigned course
    $student2 = User::factory()->create(['role' => User::ROLE_STUDENT]);
    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $student2->id,
        'attempt_number' => 1,
        'status' => AssignmentSubmission::STATUS_SUBMITTED,
        'submitted_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($this->admin)->get(route('admin.analytics.instructors'));
    $response->assertOk();
    $response->assertSee($this->instructor->name);
    $response->assertSee('8 hrs');
});

test('instructor can access their personal analytics page strictly scoped to their assigned courses', function () {
    $course = Course::factory()->create(['name' => 'Instructor Assigned Course']);
    $this->instructor->assignedCourses()->attach($course->id, ['assigned_at' => now()]);

    $otherCourse = Course::factory()->create(['name' => 'Unassigned Mystery Course']);

    $response = $this->actingAs($this->instructor)->get(route('instructor.analytics.index'));
    $response->assertOk();
    $response->assertSee('Instructor Assigned Course');
    $response->assertDontSee('Unassigned Mystery Course');
});

test('instructor is forbidden from accessing analytics of an unassigned course via filter', function () {
    $unassignedCourse = Course::factory()->create();

    $response = $this->actingAs($this->instructor)->get(route('instructor.analytics.index', ['course_id' => $unassignedCourse->id]));
    $response->assertForbidden();
});

test('instructor cannot access admin analytics endpoints', function () {
    $this->actingAs($this->instructor)->get(route('admin.analytics.index'))->assertForbidden();
    $this->actingAs($this->instructor)->get(route('admin.analytics.revenue'))->assertForbidden();
    $this->actingAs($this->instructor)->get(route('admin.analytics.enrollments'))->assertForbidden();
});

test('student can view their personal learning analytics and cannot view admin or instructor analytics', function () {
    $course = Course::factory()->create(['name' => 'Enrolled Student Track']);
    Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($this->student)->get(route('student.analytics.index'));
    $response->assertOk();
    $response->assertSee('Enrolled Student Track');
    $response->assertSee('My Learning Analytics');

    // Forbidden from admin and instructor routes
    $this->actingAs($this->student)->get(route('admin.analytics.index'))->assertForbidden();
    $this->actingAs($this->student)->get(route('instructor.analytics.index'))->assertForbidden();
});

test('admin can stream download CSV reports for enrollments, revenue, courses, students, and instructors', function () {
    $course = Course::factory()->create();
    Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
    ]);

    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_csv_1',
        'amount' => 500000,
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
    ]);

    // 1. Enrollments CSV
    $respEnr = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'enrollments', 'format' => 'csv']));
    $respEnr->assertOk();
    $respEnr->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    // 2. Revenue CSV
    $respRev = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'revenue', 'format' => 'csv']));
    $respRev->assertOk();
    $respRev->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    // 3. Courses CSV
    $respCrs = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'courses', 'format' => 'csv']));
    $respCrs->assertOk();

    // 4. Students CSV
    $respStd = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'students', 'format' => 'csv']));
    $respStd->assertOk();

    // 5. Instructors CSV
    $respInst = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'instructors', 'format' => 'csv']));
    $respInst->assertOk();
});

test('admin can download PDF reports for all analytics domains', function () {
    $course = Course::factory()->create();
    Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
    ]);

    // 1. Enrollments PDF
    $pdfEnr = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'enrollments', 'format' => 'pdf']));
    $pdfEnr->assertOk();
    $pdfEnr->assertHeader('Content-Type', 'application/pdf');

    // 2. Revenue PDF
    $pdfRev = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'revenue', 'format' => 'pdf']));
    $pdfRev->assertOk();
    $pdfRev->assertHeader('Content-Type', 'application/pdf');

    // 3. Courses PDF
    $pdfCrs = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'courses', 'format' => 'pdf']));
    $pdfCrs->assertOk();
    $pdfCrs->assertHeader('Content-Type', 'application/pdf');

    // 4. Students PDF
    $pdfStd = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'students', 'format' => 'pdf']));
    $pdfStd->assertOk();
    $pdfStd->assertHeader('Content-Type', 'application/pdf');

    // 5. Instructors PDF
    $pdfInst = $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'instructors', 'format' => 'pdf']));
    $pdfInst->assertOk();
    $pdfInst->assertHeader('Content-Type', 'application/pdf');
});

test('instructor can download CSV and PDF of their assigned course performance', function () {
    $csv = $this->actingAs($this->instructor)->get(route('instructor.analytics.export', ['format' => 'csv']));
    $csv->assertOk();
    $csv->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $pdf = $this->actingAs($this->instructor)->get(route('instructor.analytics.export', ['format' => 'pdf']));
    $pdf->assertOk();
    $pdf->assertHeader('Content-Type', 'application/pdf');
});

test('empty database yields zero metrics without divisions by zero or SQL exceptions', function () {
    /** @var AnalyticsService $service */
    $service = app(AnalyticsService::class);

    $overview = $service->getPlatformOverview();
    expect($overview['total_students'])->toBe(1) // from beforeEach
        ->and($overview['total_courses'])->toBe(0)
        ->and($overview['course_completion_rate'])->toBe(0)
        ->and($overview['total_revenue'])->toEqual(0.0);

    $financials = $service->getFinancialAnalytics();
    expect($financials['total_collected_revenue'])->toEqual(0.0)
        ->and($financials['success_rate'])->toBe(0);

    $enrollments = $service->getEnrollmentAnalytics();
    expect($enrollments['total_enrollments'])->toBe(0)
        ->and($enrollments['approval_rate'])->toBe(0);

    $courses = $service->getCoursePerformanceReports();
    expect($courses['reports'])->toBeEmpty();

    $engagement = $service->getStudentEngagementAnalytics();
    expect($engagement['quiz_participation']['pass_rate'])->toBe(0);

    $studentAnalytics = $service->getStudentPersonalAnalytics($this->student);
    expect($studentAnalytics['total_enrolled'])->toBe(0)
        ->and($studentAnalytics['overall_progress_percentage'])->toBe(0);
});

test('date presets and custom date range filters properly bound query results', function () {
    $course = Course::factory()->create();

    // Payment today
    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_today',
        'amount' => 100000,
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
        'created_at' => now(),
    ]);

    // Payment 60 days ago
    Payment::create([
        'user_id' => $this->student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_60_days',
        'amount' => 200000,
        'status' => Payment::STATUS_PAID,
        'paid_at' => now()->subDays(60),
        'created_at' => now()->subDays(60),
    ]);

    /** @var AnalyticsService $service */
    $service = app(AnalyticsService::class);

    // Filter 'last_7_days' should only find 1 payment (1,000 INR)
    $filtered = $service->getFinancialAnalytics(['date_range' => 'last_7_days']);
    expect($filtered['paid_count'])->toBe(1)
        ->and($filtered['total_collected_revenue'])->toEqual(1000.0);

    // Filter 'all_time' should find both (3,000 INR)
    $allTime = $service->getFinancialAnalytics(['date_range' => 'all_time']);
    expect($allTime['paid_count'])->toBe(2)
        ->and($allTime['total_collected_revenue'])->toEqual(3000.0);
});

test('course filter and status filter properly isolate query results', function () {
    $course1 = Course::factory()->create(['name' => 'Track Alpha']);
    $course2 = Course::factory()->create(['name' => 'Track Beta']);

    // Enrollment for course 1 approved
    Enrollment::create([
        'user_id' => $this->student->id,
        'course_id' => $course1->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
    ]);

    // Enrollment for course 2 pending
    $student2 = User::factory()->create(['role' => User::ROLE_STUDENT]);
    Enrollment::create([
        'user_id' => $student2->id,
        'course_id' => $course2->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    /** @var AnalyticsService $service */
    $service = app(AnalyticsService::class);

    // Filter by course 1
    $course1Analytics = $service->getEnrollmentAnalytics(['course_id' => $course1->id]);
    expect($course1Analytics['total_enrollments'])->toBe(1)
        ->and($course1Analytics['approved_count'])->toBe(1)
        ->and($course1Analytics['pending_count'])->toBe(0);

    // Filter by status 'pending'
    $pendingAnalytics = $service->getEnrollmentAnalytics(['status' => Enrollment::STATUS_PENDING]);
    expect($pendingAnalytics['total_enrollments'])->toBe(1)
        ->and($pendingAnalytics['pending_count'])->toBe(1)
        ->and($pendingAnalytics['approved_count'])->toBe(0);
});

test('unauthenticated users are redirected to login from all analytics routes', function () {
    $this->get(route('admin.analytics.index'))->assertRedirect(route('login'));
    $this->get(route('instructor.analytics.index'))->assertRedirect(route('login'));
    $this->get(route('student.analytics.index'))->assertRedirect(route('login'));
});

test('invalid export type responds with 404', function () {
    $this->actingAs($this->admin)->get(route('admin.analytics.export', ['type' => 'invalid-type-name']))
        ->assertNotFound();
});

test('students and instructors cannot access admin export downloads', function () {
    $this->actingAs($this->student)->get(route('admin.analytics.export', ['type' => 'revenue']))
        ->assertForbidden();

    $this->actingAs($this->instructor)->get(route('admin.analytics.export', ['type' => 'revenue']))
        ->assertForbidden();
});
