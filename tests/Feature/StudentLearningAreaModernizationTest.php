<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\User;

function setupLearningAreaTestCurriculum(string $slug = 'sap-fico-learning-area-test'): array
{
    $course = Course::create([
        'name' => 'SAP FICO Learning Area Suite',
        'slug' => $slug,
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive financial accounting workflow from gl to reporting.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $module1 = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'General Ledger Mastery',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson1 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'GL Accounts Architecture',
        'content_type' => 'text',
        'content' => 'Deep architectural study of Chart of Accounts and GL accounts.',
        'duration_minutes' => 20,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson2 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'Posting Rules Video Walkthrough',
        'content_type' => 'video',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'description' => 'Step by step walkthrough of journal entries.',
        'duration_minutes' => 35,
        'sort_order' => 2,
        'status' => 'published',
    ]);

    $module2 = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Accounts Payable & Procurement',
        'sort_order' => 2,
        'status' => 'published',
    ]);

    $lesson3 = Lesson::create([
        'course_module_id' => $module2->id,
        'title' => 'Vendor Master External Resource',
        'content_type' => 'resource',
        'resource_url' => 'https://cloud.sap.com/sandbox/vendor-master',
        'description' => 'Access the live sandbox vendor database blueprint.',
        'duration_minutes' => 15,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('course', 'module1', 'lesson1', 'lesson2', 'module2', 'lesson3');
}

test('student course syllabus overview renders curriculum, module accordions, and progress', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-syllabus-test');
    $student = User::factory()->student()->active()->create(['name' => 'Aarav Patel']);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.show', $curriculum['course']));

    $response->assertOk();
    $response->assertSee('SAP FICO Learning Area Suite');
    $response->assertSee('Enrolled &amp; Active', false);
    $response->assertSee('General Ledger Mastery');
    $response->assertSee('GL Accounts Architecture');
    $response->assertSee('Posting Rules Video Walkthrough');
    $response->assertSee('Accounts Payable &amp; Procurement', false);
    $response->assertSee('Vendor Master External Resource');
    $response->assertSee('Course Progress: 0%');
    $response->assertSee('0 of 3 lessons completed');
    $response->assertSee('Start Course');
});

test('unapproved student or guest cannot access student course syllabus', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-unapproved-test');
    $student = User::factory()->student()->active()->create();

    // Guest redirected to login
    $this->get(route('student.courses.show', $curriculum['course']))->assertRedirect(route('login'));

    // Student without approved enrollment receives 403
    $this->actingAs($student)->get(route('student.courses.show', $curriculum['course']))->assertForbidden();
});

test('video lesson renders responsive player and secure embed URL', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-video-player-test');
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson2'],
    ]));

    $response->assertOk();
    $response->assertSee('Posting Rules Video Walkthrough');
    $response->assertSee('VIDEO LESSON');
    $response->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
    $response->assertSee('Open Video Stream in new tab');
    $response->assertSee('Mark as Complete');
    $response->assertSee('Previous Lesson');
    $response->assertSee('Next Lesson');
});

test('reading lesson renders text content, module sidebar, and completion controls', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-reading-test');
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $response->assertOk();
    $response->assertSee('GL Accounts Architecture');
    $response->assertSee('TEXT LESSON');
    $response->assertSee('Deep architectural study of Chart of Accounts and GL accounts.');
    $response->assertSee('First Lesson');
    $response->assertSee('Next Lesson');
    $response->assertSee('Course Syllabus');
    $response->assertSee('Mark as Complete');
});

test('resource lesson renders external resource access link securely', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-resource-test');
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson3'],
    ]));

    $response->assertOk();
    $response->assertSee('Vendor Master External Resource');
    $response->assertSee('RESOURCE LESSON');
    $response->assertSee('https://cloud.sap.com/sandbox/vendor-master');
    $response->assertSee('Access Resource URL');
    $response->assertSee('Course Completed');
});

test('student can toggle lesson complete and incomplete via authoritative server routes', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-completion-toggle-test');
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    // 1. Mark as complete
    $completeResponse = $this->actingAs($student)->post(route('student.courses.lesson.complete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $completeResponse->assertRedirect();
    $completeResponse->assertSessionHas('success');

    $this->assertDatabaseHas('lesson_progress', [
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
    ]);

    // Lesson view now shows "Completed" and "Mark as Incomplete" button
    $viewResponse = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));
    $viewResponse->assertOk();
    $viewResponse->assertSee('Lesson marked as completed');
    $viewResponse->assertSee('Mark as Incomplete');

    // 2. Mark as incomplete
    $incompleteResponse = $this->actingAs($student)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $incompleteResponse->assertRedirect();
    $incompleteResponse->assertSessionHas('info');

    $this->assertDatabaseHas('lesson_progress', [
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_IN_PROGRESS,
    ]);
});

test('lesson player surfaces attached quiz assessment banner when present', function () {
    $curriculum = setupLearningAreaTestCurriculum('sap-fico-quiz-banner-test');
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    Quiz::create([
        'course_id' => $curriculum['course']->id,
        'course_module_id' => $curriculum['module1']->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'title' => 'GL Architecture Knowledge Check',
        'slug' => 'gl-architecture-knowledge-check',
        'description' => 'Test your understanding of GL master setup.',
        'time_limit_minutes' => 10,
        'total_marks' => 10,
        'passing_percentage' => 80,
        'status' => Quiz::STATUS_PUBLISHED,
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $response->assertOk();
    $response->assertSee('Lesson Assessment');
    $response->assertSee('GL Architecture Knowledge Check');
    $response->assertSee('Take Quiz');
});
