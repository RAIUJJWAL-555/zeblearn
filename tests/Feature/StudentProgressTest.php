<?php

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Payment;
use App\Models\User;
use App\Services\LessonProgressService;

function createTestCourseWithCurriculum(string $courseSlug = 'sap-fico-masterclass', bool $isCoursePublished = true): array
{
    $course = Course::create([
        'name' => 'SAP FICO Masterclass',
        'slug' => $courseSlug,
        'category' => 'sap-erp',
        'status' => $isCoursePublished ? 'available' : 'draft',
        'short_description' => 'Complete enterprise accounting workflow.',
        'fee' => 35000,
        'published_at' => $isCoursePublished ? now() : null,
    ]);

    $module1 = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: General Ledger',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson1 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'Lesson 1.1: Chart of Accounts',
        'content_type' => 'text',
        'content' => 'Overview of Chart of Accounts.',
        'duration_minutes' => 15,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson2 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'Lesson 1.2: Journal Posting',
        'content_type' => 'text',
        'content' => 'Journal posting best practices.',
        'duration_minutes' => 20,
        'sort_order' => 2,
        'status' => 'published',
    ]);

    $module2 = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 2: Accounts Payable',
        'sort_order' => 2,
        'status' => 'published',
    ]);

    $lesson3 = Lesson::create([
        'course_module_id' => $module2->id,
        'title' => 'Lesson 2.1: Vendor Master Data',
        'content_type' => 'text',
        'content' => 'Vendor master data walkthrough.',
        'duration_minutes' => 25,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return [
        'course' => $course,
        'module1' => $module1,
        'lesson1' => $lesson1,
        'lesson2' => $lesson2,
        'module2' => $module2,
        'lesson3' => $lesson3,
    ];
}

function createApprovedStudentForCourse(Course $course): User
{
    $student = User::factory()->student()->active()->create();

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDay(),
        'approved_at' => now(),
    ]);

    return $student;
}

test('1. approved student can open eligible published lesson', function () {
    $curriculum = createTestCourseWithCurriculum('student-open-lesson-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    $response = $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $response->assertOk();
    $response->assertSee($curriculum['lesson1']->title);
    $response->assertSee('Chart of Accounts');
});

test('2. opening lesson creates in-progress record', function () {
    $curriculum = createTestCourseWithCurriculum('progress-creation-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    expect(LessonProgress::count())->toBe(0);

    $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $progress = LessonProgress::where('user_id', $student->id)
        ->where('lesson_id', $curriculum['lesson1']->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->status)->toBe(LessonProgress::STATUS_IN_PROGRESS);
    expect($progress->first_started_at)->not->toBeNull();
    expect($progress->last_accessed_at)->not->toBeNull();
    expect($progress->completed_at)->toBeNull();
});

test('3. opening lesson updates last_accessed_at', function () {
    $curriculum = createTestCourseWithCurriculum('last-accessed-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    $earlierTime = now()->subHours(5);

    $progress = LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_IN_PROGRESS,
        'first_started_at' => $earlierTime,
        'last_accessed_at' => $earlierTime,
    ]);

    $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $progress->refresh();
    expect($progress->last_accessed_at->isAfter($earlierTime))->toBeTrue();
    expect($progress->first_started_at->toDateTimeString())->toBe($earlierTime->toDateTimeString());
});

test('4. reopening completed lesson does not reset status', function () {
    $curriculum = createTestCourseWithCurriculum('completed-not-reset-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    $completedTime = now()->subHours(2);
    $progress = LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'first_started_at' => now()->subDay(),
        'completed_at' => $completedTime,
        'last_accessed_at' => $completedTime,
    ]);

    $this->actingAs($student)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $progress->refresh();
    expect($progress->status)->toBe(LessonProgress::STATUS_COMPLETED);
    expect($progress->completed_at->toDateTimeString())->toBe($completedTime->toDateTimeString());
    expect($progress->isCompleted())->toBeTrue();
});

test('5. student can mark own lesson completed', function () {
    $curriculum = createTestCourseWithCurriculum('mark-complete-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    $response = $this->actingAs($student)->post(route('student.courses.lesson.complete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $progress = LessonProgress::where('user_id', $student->id)
        ->where('lesson_id', $curriculum['lesson1']->id)
        ->first();

    expect($progress)->not->toBeNull();
    expect($progress->status)->toBe(LessonProgress::STATUS_COMPLETED);
    expect($progress->completed_at)->not->toBeNull();
});

test('6. student can mark completed lesson incomplete', function () {
    $curriculum = createTestCourseWithCurriculum('mark-incomplete-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'first_started_at' => now()->subDay(),
        'completed_at' => now()->subHour(),
        'last_accessed_at' => now()->subHour(),
    ]);

    $response = $this->actingAs($student)->post(route('student.courses.lesson.incomplete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $response->assertRedirect();
    $response->assertSessionHas('info');

    $progress = LessonProgress::where('user_id', $student->id)
        ->where('lesson_id', $curriculum['lesson1']->id)
        ->first();

    expect($progress->status)->toBe(LessonProgress::STATUS_IN_PROGRESS);
    expect($progress->completed_at)->toBeNull();
    expect($progress->first_started_at)->not->toBeNull();
});

test('7. repeated completion requests do not create duplicate records', function () {
    $curriculum = createTestCourseWithCurriculum('idempotent-complete-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    $this->actingAs($student)->post(route('student.courses.lesson.complete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $this->actingAs($student)->post(route('student.courses.lesson.complete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $records = LessonProgress::where('user_id', $student->id)
        ->where('lesson_id', $curriculum['lesson1']->id)
        ->get();

    expect($records->count())->toBe(1);
    expect($records->first()->status)->toBe(LessonProgress::STATUS_COMPLETED);
});

test('8. student cannot access another student\'s progress or view another student\'s personal state', function () {
    $curriculum = createTestCourseWithCurriculum('progress-isolation-test');
    $studentA = createApprovedStudentForCourse($curriculum['course']);
    $studentB = createApprovedStudentForCourse($curriculum['course']);

    // Student A completes lesson 1
    LessonProgress::create([
        'user_id' => $studentA->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Student B visits lesson 1
    $response = $this->actingAs($studentB)->get(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $response->assertOk();
    // Student B should see lesson as In Progress, not Completed
    $response->assertSee('Mark as Complete');
    $response->assertDontSee('Lesson marked as completed');

    $bProgress = LessonProgress::where('user_id', $studentB->id)
        ->where('lesson_id', $curriculum['lesson1']->id)
        ->first();

    expect($bProgress->status)->toBe(LessonProgress::STATUS_IN_PROGRESS);
});

test('9. student cannot modify another student\'s progress', function () {
    $curriculum = createTestCourseWithCurriculum('modify-progress-security-test');
    $studentA = createApprovedStudentForCourse($curriculum['course']);
    $studentB = createApprovedStudentForCourse($curriculum['course']);

    $progressA = LessonProgress::create([
        'user_id' => $studentA->id,
        'lesson_id' => $curriculum['lesson1']->id,
        'status' => LessonProgress::STATUS_IN_PROGRESS,
        'first_started_at' => now(),
    ]);

    // Student B performs complete on lesson1 - this completes Student B's progress, not Student A's
    $this->actingAs($studentB)->post(route('student.courses.lesson.complete', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));

    $progressA->refresh();
    expect($progressA->status)->toBe(LessonProgress::STATUS_IN_PROGRESS);
    expect($progressA->completed_at)->toBeNull();

    $progressB = LessonProgress::where('user_id', $studentB->id)
        ->where('lesson_id', $curriculum['lesson1']->id)
        ->first();
    expect($progressB->status)->toBe(LessonProgress::STATUS_COMPLETED);
});

test('10. student without approved enrollment cannot access lessons or progress endpoints', function () {
    $curriculum = createTestCourseWithCurriculum('no-enrollment-access-test');
    $unapprovedStudent = User::factory()->student()->active()->create();

    // Guest / unapproved cannot view lesson
    $this->actingAs($unapprovedStudent)
        ->get(route('student.courses.lesson', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertForbidden();

    // Cannot complete lesson
    $this->actingAs($unapprovedStudent)
        ->post(route('student.courses.lesson.complete', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertForbidden();

    // Cannot resume
    $this->actingAs($unapprovedStudent)
        ->get(route('student.courses.resume', [
            'course' => $curriculum['course'],
        ]))
        ->assertForbidden();
});

test('11. suspended student cannot use progress endpoints', function () {
    $curriculum = createTestCourseWithCurriculum('suspended-student-test');
    $student = createApprovedStudentForCourse($curriculum['course']);
    $student->forceFill(['status' => User::STATUS_SUSPENDED])->save();

    $this->actingAs($student)
        ->get(route('student.courses.lesson', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertForbidden();

    $this->actingAs($student)
        ->post(route('student.courses.lesson.complete', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertForbidden();
});

test('12. draft course cannot be accessed', function () {
    $curriculum = createTestCourseWithCurriculum('draft-course-test', isCoursePublished: false);
    $student = createApprovedStudentForCourse($curriculum['course']);

    $this->actingAs($student)
        ->get(route('student.courses.lesson', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertNotFound();

    $this->actingAs($student)
        ->post(route('student.courses.lesson.complete', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertNotFound();
});

test('13. draft module cannot be accessed', function () {
    $curriculum = createTestCourseWithCurriculum('draft-module-test');
    $curriculum['module1']->update(['status' => 'draft']);
    $student = createApprovedStudentForCourse($curriculum['course']);

    $this->actingAs($student)
        ->get(route('student.courses.lesson', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertNotFound();

    $this->actingAs($student)
        ->post(route('student.courses.lesson.complete', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertNotFound();
});

test('14. draft lesson cannot be accessed or completed', function () {
    $curriculum = createTestCourseWithCurriculum('draft-lesson-test');
    $curriculum['lesson1']->update(['status' => 'draft']);
    $student = createApprovedStudentForCourse($curriculum['course']);

    $this->actingAs($student)
        ->get(route('student.courses.lesson', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertNotFound();

    $this->actingAs($student)
        ->post(route('student.courses.lesson.complete', [
            'course' => $curriculum['course'],
            'lesson' => $curriculum['lesson1'],
        ]))
        ->assertNotFound();
});

test('15. lesson belonging to another course is rejected', function () {
    $curriculumA = createTestCourseWithCurriculum('course-a-test');
    $curriculumB = createTestCourseWithCurriculum('course-b-test');
    $studentA = createApprovedStudentForCourse($curriculumA['course']);

    // Attempting to access course B's lesson via course A's URL route
    $this->actingAs($studentA)
        ->get(route('student.courses.lesson', [
            'course' => $curriculumA['course'],
            'lesson' => $curriculumB['lesson1'],
        ]))
        ->assertNotFound();

    $this->actingAs($studentA)
        ->post(route('student.courses.lesson.complete', [
            'course' => $curriculumA['course'],
            'lesson' => $curriculumB['lesson1'],
        ]))
        ->assertNotFound();
});

test('16. course percentage is calculated correctly', function () {
    $curriculum = createTestCourseWithCurriculum('percentage-calc-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    $service = app(LessonProgressService::class);

    // Initial 0%
    $initial = $service->getCourseProgress($student, $curriculum['course']);
    expect($initial['total'])->toBe(3);
    expect($initial['completed'])->toBe(0);
    expect($initial['percentage'])->toBe(0);
    expect($initial['is_completed'])->toBeFalse();

    // 1 completed of 3 = 33%
    $service->markLessonComplete($student, $curriculum['course'], $curriculum['lesson1']);
    $oneDone = $service->getCourseProgress($student, $curriculum['course']);
    expect($oneDone['completed'])->toBe(1);
    expect($oneDone['percentage'])->toBe(33);
    expect($oneDone['is_completed'])->toBeFalse();

    // 2 completed of 3 = 67%
    $service->markLessonComplete($student, $curriculum['course'], $curriculum['lesson2']);
    $twoDone = $service->getCourseProgress($student, $curriculum['course']);
    expect($twoDone['completed'])->toBe(2);
    expect($twoDone['percentage'])->toBe(67);
    expect($twoDone['is_completed'])->toBeFalse();

    // 3 completed of 3 = 100%
    $service->markLessonComplete($student, $curriculum['course'], $curriculum['lesson3']);
    $allDone = $service->getCourseProgress($student, $curriculum['course']);
    expect($allDone['completed'])->toBe(3);
    expect($allDone['percentage'])->toBe(100);
    expect($allDone['is_completed'])->toBeTrue();
});

test('17. draft and unpublished lessons are excluded from percentage', function () {
    $curriculum = createTestCourseWithCurriculum('unpublished-excluded-test');
    $student = createApprovedStudentForCourse($curriculum['course']);

    // Add a draft lesson to module 1
    Lesson::create([
        'course_module_id' => $curriculum['module1']->id,
        'title' => 'Draft Lesson Ineligible',
        'content_type' => 'text',
        'content' => 'Draft content',
        'sort_order' => 99,
        'status' => 'draft',
    ]);

    // Add an entire draft module with a lesson
    $draftModule = CourseModule::create([
        'course_id' => $curriculum['course']->id,
        'title' => 'Draft Module',
        'sort_order' => 99,
        'status' => 'draft',
    ]);
    Lesson::create([
        'course_module_id' => $draftModule->id,
        'title' => 'Draft Module Lesson',
        'content_type' => 'text',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $service = app(LessonProgressService::class);
    $progress = $service->getCourseProgress($student, $curriculum['course']);

    // Only the 3 published lessons in published modules should be counted
    expect($progress['total'])->toBe(3);
});

test('18. zero eligible lessons return 0% safely', function () {
    $emptyCourse = Course::create([
        'name' => 'Empty Enterprise Course',
        'slug' => 'empty-enterprise-course',
        'category' => 'salesforce',
        'status' => 'available',
        'short_description' => 'Course in preparation.',
        'fee' => 20000,
        'published_at' => now(),
    ]);
    $student = createApprovedStudentForCourse($emptyCourse);

    $service = app(LessonProgressService::class);
    $progress = $service->getCourseProgress($student, $emptyCourse);

    expect($progress['total'])->toBe(0);
    expect($progress['completed'])->toBe(0);
    expect($progress['percentage'])->toBe(0);
    expect($progress['is_completed'])->toBeFalse();
});

test('19. resume learning returns last eligible accessed incomplete lesson or first lesson', function () {
    $curriculum = createTestCourseWithCurriculum('resume-learning-test');
    $student = createApprovedStudentForCourse($curriculum['course']);
    $service = app(LessonProgressService::class);

    // Initial state: should return first published lesson (lesson1)
    $firstResume = $service->getResumeLesson($student, $curriculum['course']);
    expect($firstResume->id)->toBe($curriculum['lesson1']->id);

    // Student accesses lesson 2
    $service->recordLessonAccess($student, $curriculum['course'], $curriculum['lesson2']);
    $afterAccess = $service->getResumeLesson($student, $curriculum['course']);
    expect($afterAccess->id)->toBe($curriculum['lesson2']->id);

    // Student completes lesson 2, next incomplete accessed or uncompleted is resolved
    $service->markLessonComplete($student, $curriculum['course'], $curriculum['lesson2']);
    $afterComplete = $service->getResumeLesson($student, $curriculum['course']);
    // Since lesson 1 is incomplete, it returns lesson 1 (or next incomplete)
    expect($afterComplete->id)->toBe($curriculum['lesson1']->id);

    // Test controller resume redirect
    $response = $this->actingAs($student)->get(route('student.courses.resume', $curriculum['course']));
    $response->assertRedirect(route('student.courses.lesson', [
        'course' => $curriculum['course'],
        'lesson' => $curriculum['lesson1'],
    ]));
});

test('20. resume learning ignores unpublished and ineligible lessons', function () {
    $curriculum = createTestCourseWithCurriculum('resume-ignore-unpublished-test');
    $student = createApprovedStudentForCourse($curriculum['course']);
    $service = app(LessonProgressService::class);

    // Create a draft lesson and set its last accessed to recent
    $draftLesson = Lesson::create([
        'course_module_id' => $curriculum['module1']->id,
        'title' => 'Draft Ineligible Lesson',
        'content_type' => 'text',
        'content' => 'Ineligible draft',
        'sort_order' => 10,
        'status' => 'draft',
    ]);

    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $draftLesson->id,
        'status' => LessonProgress::STATUS_IN_PROGRESS,
        'last_accessed_at' => now(),
    ]);

    $resume = $service->getResumeLesson($student, $curriculum['course']);
    // Must NOT be the draft lesson
    expect($resume->id)->not->toBe($draftLesson->id);
    expect($resume->id)->toBe($curriculum['lesson1']->id);
});

test('21. previous and next navigation respects module and lesson ordering across module boundaries', function () {
    $curriculum = createTestCourseWithCurriculum('prev-next-nav-test');
    $student = createApprovedStudentForCourse($curriculum['course']);
    $service = app(LessonProgressService::class);

    // Lesson 1.1: Prev = null, Next = Lesson 1.2
    $nav1 = $service->getLessonNavigation($curriculum['course'], $curriculum['lesson1']);
    expect($nav1['prev'])->toBeNull();
    expect($nav1['next']->id)->toBe($curriculum['lesson2']->id);

    // Lesson 1.2 (End of Module 1): Next crosses boundary to Lesson 2.1 (Start of Module 2)
    $nav2 = $service->getLessonNavigation($curriculum['course'], $curriculum['lesson2']);
    expect($nav2['prev']->id)->toBe($curriculum['lesson1']->id);
    expect($nav2['next']->id)->toBe($curriculum['lesson3']->id);

    // Lesson 2.1 (End of Module 2 / Course): Prev = Lesson 1.2, Next = null
    $nav3 = $service->getLessonNavigation($curriculum['course'], $curriculum['lesson3']);
    expect($nav3['prev']->id)->toBe($curriculum['lesson2']->id);
    expect($nav3['next'])->toBeNull();
});

test('22. existing manual enrollment and Razorpay-approved enrollment continue to work', function () {
    $curriculum = createTestCourseWithCurriculum('existing-enrollment-continuity-test');
    $student = User::factory()->student()->active()->create();

    // 1. Manual enrollment workflow
    $manualEnrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    expect($manualEnrollment->isApproved())->toBeFalse();

    // Approve enrollment
    $manualEnrollment->update([
        'status' => Enrollment::STATUS_APPROVED,
        'approved_at' => now(),
    ]);
    expect($manualEnrollment->fresh()->isApproved())->toBeTrue();

    // 2. Razorpay payment workflow
    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $curriculum['course']->id,
        'enrollment_id' => $manualEnrollment->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_prog_test_123',
        'gateway_payment_id' => 'pay_prog_test_456',
        'status' => Payment::STATUS_PAID,
        'currency' => 'INR',
        'amount' => 3500000,
        'paid_at' => now(),
        'verified_at' => now(),
    ]);

    expect($payment->isPaid())->toBeTrue();

    // Student now accesses dashboard and has progress loaded cleanly
    $response = $this->actingAs($student)->get(route('student.dashboard'));
    $response->assertOk();
    $response->assertSee($curriculum['course']->name);
    $response->assertSee('0% Complete');
    $response->assertSee('Start Learning');
});
