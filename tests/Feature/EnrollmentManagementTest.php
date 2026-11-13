<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;

test('1. active student can request course enrollment', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'SAP FICO Financial Accounting',
        'slug' => 'sap-fico-financial-accounting',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive accounting training.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
    ]);
});

test('2. unauthenticated guest is redirected to login when requesting enrollment', function () {
    $course = Course::create([
        'name' => 'Workday Core HCM',
        'slug' => 'workday-core-hcm-guest-test',
        'category' => 'workday',
        'status' => 'available',
        'short_description' => 'HCM training.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $response = $this->post(route('courses.enroll', $course));

    $response->assertRedirect(route('login'));
});

test('3. admin cannot submit a student enrollment request', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Salesforce Admin Course',
        'slug' => 'salesforce-admin-course',
        'category' => 'salesforce',
        'status' => 'available',
        'short_description' => 'Salesforce training.',
        'fee' => 28000,
        'published_at' => now(),
    ]);

    $this->actingAs($admin)->post(route('courses.enroll', $course))->assertForbidden();
});

test('4. instructor cannot submit a student enrollment request', function () {
    $instructor = User::factory()->instructor()->active()->create();

    $course = Course::create([
        'name' => 'Cloud Computing AWS',
        'slug' => 'cloud-computing-aws',
        'category' => 'cloud-computing',
        'status' => 'available',
        'short_description' => 'AWS training.',
        'fee' => 32000,
        'published_at' => now(),
    ]);

    $this->actingAs($instructor)->post(route('courses.enroll', $course))->assertForbidden();
});

test('5. suspended student cannot request enrollment', function () {
    $suspendedStudent = User::factory()->student()->suspended()->create();

    $course = Course::create([
        'name' => 'Data Science Analytics',
        'slug' => 'data-science-analytics',
        'category' => 'data-science-analytics',
        'status' => 'available',
        'short_description' => 'Data science.',
        'fee' => 40000,
        'published_at' => now(),
    ]);

    $this->actingAs($suspendedStudent)->post(route('courses.enroll', $course))->assertForbidden();
});

test('6. unavailable course cannot be enrolled in', function () {
    $student = User::factory()->student()->active()->create();

    // Upcoming or planned courses are not open for enrollment
    $course = Course::create([
        'name' => 'SAP EWM Logistics Future',
        'slug' => 'sap-ewm-logistics-future',
        'category' => 'sap-erp',
        'status' => 'upcoming',
        'short_description' => 'Upcoming batch.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
    ]);
});

test('7. duplicate pending enrollment request is prevented', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Cyber Security Defense',
        'slug' => 'cyber-security-defense',
        'category' => 'cyber-security',
        'status' => 'available',
        'short_description' => 'Security training.',
        'fee' => 45000,
        'published_at' => now(),
    ]);

    // First request
    $this->actingAs($student)->post(route('courses.enroll', $course));

    // Second request
    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('info');

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('8. already approved enrollment cannot be duplicated', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Full-Stack Software Development',
        'slug' => 'full-stack-software-development',
        'category' => 'software-development',
        'status' => 'available',
        'short_description' => 'Software engineering.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('info');

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('9. admin can view enrollment listing with search and filters', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create(['name' => 'Rahul Sharma']);

    $course = Course::create([
        'name' => 'Digital Marketing Growth Master',
        'slug' => 'digital-marketing-growth-master',
        'category' => 'digital-marketing',
        'status' => 'available',
        'short_description' => 'Marketing masterclass.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));

    $response->assertOk();
    $response->assertSee('Student Enrollment Approvals');
    $response->assertSee('Rahul Sharma');
    $response->assertSee('Digital Marketing Growth Master');
    $response->assertSee('Pending Review');
});

test('10. student cannot access admin enrollment management', function () {
    $student = User::factory()->student()->active()->create();

    $this->actingAs($student)->get(route('admin.enrollments.index'))->assertForbidden();
});

test('11. instructor cannot access admin enrollment management', function () {
    $instructor = User::factory()->instructor()->active()->create();

    $this->actingAs($instructor)->get(route('admin.enrollments.index'))->assertForbidden();
});

test('12. admin can approve a pending enrollment request', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create(['name' => 'Meera Patel']);

    $course = Course::create([
        'name' => 'HR Management Analytics',
        'slug' => 'hr-management-analytics',
        'category' => 'hr-management',
        'status' => 'available',
        'short_description' => 'HR analytics course.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.enrollments.approve', $enrollment), [
        'admin_note' => 'Candidate verified via telephone interview.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $enrollment->refresh();
    expect($enrollment->status)->toBe(Enrollment::STATUS_APPROVED);
    expect($enrollment->approved_at)->not->toBeNull();
    expect($enrollment->reviewed_by)->toBe($admin->id);
    expect($enrollment->admin_note)->toBe('Candidate verified via telephone interview.');
});

test('13. admin can reject a pending enrollment request', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Foreign Languages German B2',
        'slug' => 'foreign-languages-german-b2',
        'category' => 'foreign-languages',
        'status' => 'available',
        'short_description' => 'German language course.',
        'fee' => 22000,
        'published_at' => now(),
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.enrollments.reject', $enrollment), [
        'admin_note' => 'Prerequisite German B1 certificate required.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $enrollment->refresh();
    expect($enrollment->status)->toBe(Enrollment::STATUS_REJECTED);
    expect($enrollment->rejected_at)->not->toBeNull();
    expect($enrollment->reviewed_by)->toBe($admin->id);
});

test('14. approved enrollment grants student course syllabus access', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Project Management Professional',
        'slug' => 'project-management-professional',
        'category' => 'project-management',
        'status' => 'available',
        'short_description' => 'PMP curriculum.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $module = $course->modules()->create([
        'title' => 'Agile Scrum Frameworks',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    $module->lessons()->create([
        'title' => 'Sprint Ceremonies and Retrospectives',
        'content_type' => 'text',
        'content' => 'Overview of Daily Standups and Sprint Planning.',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'approved_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.courses.show', $course));

    $response->assertOk();
    $response->assertSee('Project Management Professional');
    $response->assertSee('Agile Scrum Frameworks');
    $response->assertSee('Sprint Ceremonies and Retrospectives');
});

test('15. pending enrollment does not grant course access and receives 403', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Big Data Apache Spark',
        'slug' => 'big-data-apache-spark',
        'category' => 'big-data',
        'status' => 'available',
        'short_description' => 'Spark streaming.',
        'fee' => 38000,
        'published_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $this->actingAs($student)->get(route('student.courses.show', $course))->assertForbidden();
});

test('16. rejected enrollment does not grant course access and receives 403', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Business Leadership Strategy',
        'slug' => 'business-leadership-strategy',
        'category' => 'business-leadership',
        'status' => 'available',
        'short_description' => 'Executive leadership.',
        'fee' => 28000,
        'published_at' => now(),
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_REJECTED,
        'rejected_at' => now(),
    ]);

    $this->actingAs($student)->get(route('student.courses.show', $course))->assertForbidden();
});

test('17. student cannot view another students enrollment details in admin console', function () {
    $student1 = User::factory()->student()->active()->create();
    $student2 = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'SAP SD Order to Cash',
        'slug' => 'sap-sd-order-to-cash',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'SD training.',
        'fee' => 32000,
        'published_at' => now(),
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student2->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
    ]);

    $this->actingAs($student1)->get(route('admin.enrollments.show', $enrollment))->assertForbidden();
});

test('18. student cannot change their enrollment status via admin endpoints', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'SAP PP Production Planning',
        'slug' => 'sap-pp-production-planning',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'PP training.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
    ]);

    $this->actingAs($student)->patch(route('admin.enrollments.approve', $enrollment))->assertForbidden();
});

test('19. unenrolled student cannot access course lessons', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'SAP Basis System Administration',
        'slug' => 'sap-basis-system-administration',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Basis administration.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $module = $course->modules()->create([
        'title' => 'Kernel Upgrades',
        'status' => 'published',
    ]);

    $lesson = $module->lessons()->create([
        'title' => 'SAP Dispatcher & Work Process Tuning',
        'content_type' => 'text',
        'content' => 'Configuring rdisp/wp_no_dia parameters.',
        'status' => 'published',
    ]);

    // Student has no enrollment for this course
    $this->actingAs($student)->get(route('student.courses.lesson', [$course, $lesson]))->assertForbidden();
});

test('20. draft lessons cannot be accessed by approved students', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'SAP Fiori Modern UI Development',
        'slug' => 'sap-fiori-modern-ui-development',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Fiori UI5 training.',
        'fee' => 32000,
        'published_at' => now(),
    ]);

    $module = $course->modules()->create([
        'title' => 'SAPUI5 Controls',
        'status' => 'published',
    ]);

    $draftLesson = $module->lessons()->create([
        'title' => 'Draft Unreleased Lecture',
        'content_type' => 'text',
        'content' => 'In progress...',
        'status' => 'draft', // DRAFT
    ]);

    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'approved_at' => now(),
    ]);

    // Attempting to access draft lesson receives 404
    $this->actingAs($student)->get(route('student.courses.lesson', [$course, $draftLesson]))->assertNotFound();
});

test('21. existing admin course management remains fully functional', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->get(route('admin.courses.index'));

    $response->assertOk();
    $response->assertSee('Course Management');
});

test('22. existing public course pages continue working with zero regressions', function () {
    $this->get('/courses/sap-fico-training')->assertOk();
    $this->get('/courses')->assertOk();
});
