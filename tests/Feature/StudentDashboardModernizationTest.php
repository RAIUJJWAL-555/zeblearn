<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;

function createModernizedDashboardCurriculum(string $slug = 'sap-fico-modern-dashboard'): array
{
    $course = Course::create([
        'name' => 'SAP FICO Modern Learning',
        'slug' => $slug,
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive financial accounting workflow and master data.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Financial Fundamentals',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson1 = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'General Ledger Configuration',
        'content_type' => 'text',
        'content' => 'Overview of GL setup.',
        'duration_minutes' => 20,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson2 = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Document Splitting Principles',
        'content_type' => 'text',
        'content' => 'Deep dive into splitting rules.',
        'duration_minutes' => 25,
        'sort_order' => 2,
        'status' => 'published',
    ]);

    return compact('course', 'module', 'lesson1', 'lesson2');
}

test('student dashboard renders cleanly for authenticated student with no enrollments', function () {
    $student = User::factory()->student()->active()->create([
        'name' => 'Aditi Sharma',
        'email' => 'aditi.sharma@example.com',
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertSee('Welcome, Aditi Sharma');
    $response->assertSee('Student Console');
    $response->assertSee('Enrolled Courses');
    $response->assertSee('Completed Lessons');
    $response->assertSee('No Active Course Access Yet');
    $response->assertSee('Explore Course Catalog');
    $response->assertSee('Browse Courses');
    $response->assertSee('My Payments');
    $response->assertDontSee('Certificates');
});

test('student dashboard displays approved course with accurate 0 percent progress initially', function () {
    $student = User::factory()->student()->active()->create(['name' => 'Rohan Sen']);
    $data = createModernizedDashboardCurriculum('sap-fico-rohan');

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $data['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertSee('SAP FICO Modern Learning');
    $response->assertSee('0% Complete');
    $response->assertSee('0/2 Lessons');
    $response->assertSee('Start Learning');
    $response->assertSee('View Syllabus');
});

test('student dashboard updates progress metrics and shows continue learning after completing a lesson', function () {
    $student = User::factory()->student()->active()->create(['name' => 'Meera Nair']);
    $data = createModernizedDashboardCurriculum('sap-fico-meera');

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $data['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDays(2),
        'approved_at' => now()->subDay(),
    ]);

    // Student completed lesson 1
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $data['lesson1']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'first_started_at' => now()->subHours(3),
        'last_accessed_at' => now()->subHours(2),
        'completed_at' => now()->subHours(2),
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertSee('50% Complete');
    $response->assertSee('1/2 Lessons');
    $response->assertSee('Continue Learning');
    $response->assertSee('UP NEXT / RESUME LEARNING');
    $response->assertSee('Recent Learning Activity');
    $response->assertSee('Completed Lesson: General Ledger Configuration');
});

test('student dashboard separates pending and declined admissions correctly', function () {
    $student = User::factory()->student()->active()->create(['name' => 'Kabir Mehta']);

    $coursePending = Course::create([
        'name' => 'SAP MM Supply Chain Academy',
        'slug' => 'sap-mm-supply-chain-pending',
        'category' => 'sap-logistics',
        'status' => 'available',
        'short_description' => 'Procurement and inventory management.',
        'fee' => 32000,
        'published_at' => now(),
    ]);

    $courseDeclined = Course::create([
        'name' => 'SAP SD Sales and Distribution',
        'slug' => 'sap-sd-sales-declined',
        'category' => 'sap-logistics',
        'status' => 'available',
        'short_description' => 'Order to cash workflow.',
        'fee' => 31000,
        'published_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $coursePending->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now()->subHour(),
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $courseDeclined->id,
        'status' => Enrollment::STATUS_REJECTED,
        'requested_at' => now()->subDays(3),
        'rejected_at' => now()->subDay(),
        'admin_note' => 'Prerequisites not verified for this batch.',
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertSee('Pending Admissions Applications (1)');
    $response->assertSee('SAP MM Supply Chain Academy');
    $response->assertSee('Under Review');
    $response->assertSee('Application History &amp; Declined Requests', false);
    $response->assertSee('SAP SD Sales and Distribution');
    $response->assertSee('Prerequisites not verified for this batch.');
    $response->assertSee('Re-apply for Enrollment');
});

test('student dashboard displays quiz scores and attempts in assessments card', function () {
    $student = User::factory()->student()->active()->create(['name' => 'Pooja Varma']);
    $data = createModernizedDashboardCurriculum('sap-fico-pooja');

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $data['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    $quiz = Quiz::create([
        'course_id' => $data['course']->id,
        'title' => 'GL Configuration Assessment',
        'slug' => 'gl-configuration-assessment-pooja',
        'description' => 'Test your knowledge on general ledger accounts.',
        'time_limit_minutes' => 15,
        'total_marks' => 20,
        'passing_percentage' => 70,
        'allowed_attempts' => 3,
        'status' => Quiz::STATUS_PUBLISHED,
    ]);

    QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => QuizAttempt::STATUS_SUBMITTED,
        'started_at' => now()->subMinutes(20),
        'submitted_at' => now()->subMinutes(5),
        'score' => 18,
        'total_marks' => 20,
        'percentage' => 90,
        'is_passed' => true,
    ]);

    $response = $this->actingAs($student)->get(route('student.dashboard'));

    $response->assertOk();
    $response->assertSee('Quiz Assessments');
    $response->assertSee('GL Configuration Assessment');
    $response->assertSee('90% Passed');
    $response->assertSee('Result');
    $response->assertSee('1 Passed');
});

test('unauthenticated visitors cannot access student dashboard and are redirected to login', function () {
    $response = $this->get(route('student.dashboard'));
    $response->assertRedirect(route('login'));
});
