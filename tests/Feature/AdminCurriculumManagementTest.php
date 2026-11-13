<?php

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;

test('admin can access course curriculum management screen', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'SAP S/4HANA Finance Masterclass',
        'slug' => 'sap-s4hana-finance-masterclass',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive financial management course.',
        'fee' => 45000,
        'published_at' => now(),
    ]);

    $module = $course->modules()->create([
        'title' => 'General Ledger Setup & Architecture',
        'description' => 'Master chart of accounts and posting periods.',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    $module->lessons()->create([
        'title' => 'Company Code Configuration',
        'content_type' => 'text',
        'content' => 'Execute transaction OX02 and configure fiscal variant.',
        'duration_minutes' => 30,
        'status' => 'published',
        'sort_order' => 1,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.curriculum', $course));

    $response->assertOk();
    $response->assertSee('Curriculum');
    $response->assertSee('SAP S/4HANA Finance Masterclass');
    $response->assertSee('General Ledger Setup & Architecture');
    $response->assertSee('Company Code Configuration');
});

test('unauthenticated guest cannot access curriculum and is redirected to login', function () {
    $course = Course::create([
        'name' => 'Test Course',
        'slug' => 'test-course-guest-check',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Short summary',
        'fee' => 20000,
    ]);

    $response = $this->get(route('admin.courses.curriculum', $course));

    $response->assertRedirect(route('login'));
});

test('student cannot access curriculum and receives 403', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Student Blocked Course',
        'slug' => 'student-blocked-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Short summary',
        'fee' => 20000,
    ]);

    $this->actingAs($student)->get(route('admin.courses.curriculum', $course))->assertForbidden();
});

test('instructor cannot access curriculum and receives 403', function () {
    $instructor = User::factory()->instructor()->active()->create();

    $course = Course::create([
        'name' => 'Instructor Blocked Course',
        'slug' => 'instructor-blocked-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Short summary',
        'fee' => 20000,
    ]);

    $this->actingAs($instructor)->get(route('admin.courses.curriculum', $course))->assertForbidden();
});

test('suspended admin cannot access curriculum and receives 403', function () {
    $suspendedAdmin = User::factory()->admin()->suspended()->create();

    $course = Course::create([
        'name' => 'Suspended Admin Course',
        'slug' => 'suspended-admin-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Short summary',
        'fee' => 20000,
    ]);

    $this->actingAs($suspendedAdmin)->get(route('admin.courses.curriculum', $course))->assertForbidden();
});

test('admin can create a module for a course', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Workday Integrations Core',
        'slug' => 'workday-integrations-core',
        'category' => 'workday',
        'status' => 'available',
        'short_description' => 'Workday EIB and Studio course.',
        'fee' => 38000,
    ]);

    $payload = [
        'title' => 'Enterprise Interface Builder (EIB)',
        'description' => 'Inbound and outbound EIB configurations.',
        'status' => 'published',
    ];

    $response = $this->actingAs($admin)->post(route('admin.courses.modules.store', $course), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('course_modules', [
        'course_id' => $course->id,
        'title' => 'Enterprise Interface Builder (EIB)',
        'status' => 'published',
        'sort_order' => 1,
    ]);
});

test('admin can edit an existing module', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Salesforce Admin & Dev',
        'slug' => 'salesforce-admin-dev',
        'category' => 'salesforce',
        'status' => 'available',
        'short_description' => 'Salesforce masterclass.',
        'fee' => 32000,
    ]);

    $module = $course->modules()->create([
        'title' => 'Old Module Title',
        'description' => 'Old description',
        'status' => 'draft',
        'sort_order' => 1,
    ]);

    $payload = [
        'title' => 'Salesforce Security Model & Roles',
        'description' => 'Updated security rules, profiles and permission sets.',
        'status' => 'published',
    ];

    $response = $this->actingAs($admin)->put(route('admin.courses.modules.update', [$course, $module]), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $module->refresh();
    expect($module->title)->toBe('Salesforce Security Model & Roles');
    expect($module->status)->toBe('published');
});

test('admin can create a text lesson', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Python for Data Science',
        'slug' => 'python-for-data-science',
        'category' => 'data-science-analytics',
        'status' => 'available',
        'short_description' => 'Python data course.',
        'fee' => 25000,
    ]);

    $module = $course->modules()->create([
        'title' => 'NumPy & Pandas Essentials',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    $payload = [
        'title' => 'DataFrame Indexing & Filtering',
        'content_type' => 'text',
        'content' => 'Learn .loc, .iloc, and boolean mask query techniques.',
        'duration_minutes' => 45,
        'status' => 'published',
    ];

    $response = $this->actingAs($admin)->post(route('admin.courses.lessons.store', [$course, $module]), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('lessons', [
        'course_module_id' => $module->id,
        'title' => 'DataFrame Indexing & Filtering',
        'content_type' => 'text',
        'duration_minutes' => 45,
    ]);
});

test('admin can create a video lesson with a valid url', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'AWS Certified Solutions Architect',
        'slug' => 'aws-certified-solutions-architect',
        'category' => 'cloud-computing',
        'status' => 'available',
        'short_description' => 'AWS architecture course.',
        'fee' => 35000,
    ]);

    $module = $course->modules()->create([
        'title' => 'VPC Networking Deep Dive',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    $payload = [
        'title' => 'Subnets, Route Tables & Internet Gateways',
        'content_type' => 'video',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'duration_minutes' => 60,
        'status' => 'published',
    ];

    $response = $this->actingAs($admin)->post(route('admin.courses.lessons.store', [$course, $module]), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('lessons', [
        'course_module_id' => $module->id,
        'title' => 'Subnets, Route Tables & Internet Gateways',
        'content_type' => 'video',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
    ]);
});

test('invalid lesson data is rejected with validation errors', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Validation Test Course',
        'slug' => 'validation-test-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Short summary',
        'fee' => 10000,
    ]);

    $module = $course->modules()->create([
        'title' => 'Test Module',
        'status' => 'published',
    ]);

    // Missing title, invalid content type, video lesson without video_url
    $response = $this->actingAs($admin)->post(route('admin.courses.lessons.store', [$course, $module]), [
        'title' => '',
        'content_type' => 'video',
        'video_url' => '',
        'duration_minutes' => -5,
        'status' => 'invalid-status',
    ]);

    $response->assertSessionHasErrors(['title', 'video_url', 'duration_minutes', 'status']);
});

test('admin can update a lesson', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Full-Stack Web Development',
        'slug' => 'full-stack-web-dev',
        'category' => 'software-development',
        'status' => 'available',
        'short_description' => 'Web development.',
        'fee' => 28000,
    ]);

    $module = $course->modules()->create([
        'title' => 'Database Design',
        'status' => 'published',
    ]);

    $lesson = $module->lessons()->create([
        'title' => 'Initial Lesson Title',
        'content_type' => 'text',
        'content' => 'Old content',
        'duration_minutes' => 20,
        'status' => 'draft',
    ]);

    $payload = [
        'title' => 'Relational Database Normalization',
        'content_type' => 'text',
        'content' => '1NF, 2NF, 3NF and BCNF guidelines with real schema examples.',
        'duration_minutes' => 50,
        'status' => 'published',
    ];

    $response = $this->actingAs($admin)->put(
        route('admin.courses.lessons.update', [$course, $module, $lesson]),
        $payload
    );

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $lesson->refresh();
    expect($lesson->title)->toBe('Relational Database Normalization');
    expect($lesson->duration_minutes)->toBe(50);
    expect($lesson->status)->toBe('published');
});

test('admin can reorder modules deterministically', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Reorder Module Course',
        'slug' => 'reorder-module-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Summary',
        'fee' => 20000,
    ]);

    $m1 = $course->modules()->create(['title' => 'Module A', 'sort_order' => 1, 'status' => 'published']);
    $m2 = $course->modules()->create(['title' => 'Module B', 'sort_order' => 2, 'status' => 'published']);

    // Swap order: m2 first, m1 second
    $response = $this->actingAs($admin)->patch(route('admin.courses.modules.reorder', $course), [
        'module_ids' => [$m2->id, $m1->id],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $m1->refresh();
    $m2->refresh();
    expect($m2->sort_order)->toBe(1);
    expect($m1->sort_order)->toBe(2);
});

test('admin can reorder lessons within a module', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Reorder Lesson Course',
        'slug' => 'reorder-lesson-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Summary',
        'fee' => 20000,
    ]);

    $module = $course->modules()->create(['title' => 'Module One', 'status' => 'published']);

    $l1 = $module->lessons()->create(['title' => 'Lesson 1', 'sort_order' => 1, 'status' => 'published']);
    $l2 = $module->lessons()->create(['title' => 'Lesson 2', 'sort_order' => 2, 'status' => 'published']);

    // Swap order
    $response = $this->actingAs($admin)->patch(route('admin.courses.lessons.reorder', [$course, $module]), [
        'lesson_ids' => [$l2->id, $l1->id],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $l1->refresh();
    $l2->refresh();
    expect($l2->sort_order)->toBe(1);
    expect($l1->sort_order)->toBe(2);
});

test('cross-course module and lesson access is rejected with 404 or 422', function () {
    $admin = User::factory()->admin()->active()->create();

    $courseA = Course::create([
        'name' => 'Course Alpha',
        'slug' => 'course-alpha',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Alpha',
        'fee' => 10000,
    ]);

    $courseB = Course::create([
        'name' => 'Course Beta',
        'slug' => 'course-beta',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Beta',
        'fee' => 10000,
    ]);

    $moduleA = $courseA->modules()->create(['title' => 'Module Alpha', 'status' => 'published']);
    $lessonA = $moduleA->lessons()->create(['title' => 'Lesson Alpha', 'status' => 'published']);

    // Attempt to update Module A under Course B
    $this->actingAs($admin)->put(route('admin.courses.modules.update', [$courseB, $moduleA]), [
        'title' => 'Malicious update',
        'status' => 'published',
    ])->assertNotFound();

    // Attempt to reorder Module A under Course B
    $this->actingAs($admin)->patch(route('admin.courses.modules.reorder', $courseB), [
        'module_ids' => [$moduleA->id],
    ])->assertStatus(422);

    // Attempt to update Lesson A under Course B
    $this->actingAs($admin)->put(route('admin.courses.lessons.update', [$courseB, $moduleA, $lessonA]), [
        'title' => 'Malicious lesson update',
        'content_type' => 'text',
        'status' => 'published',
    ])->assertNotFound();
});

test('draft modules and lessons are not exposed to published scopes', function () {
    $course = Course::create([
        'name' => 'Publication Scope Test',
        'slug' => 'publication-scope-test',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Scope summary',
        'fee' => 15000,
        'published_at' => now(),
    ]);

    $pubModule = $course->modules()->create(['title' => 'Published Module', 'status' => 'published']);
    $draftModule = $course->modules()->create(['title' => 'Draft Module', 'status' => 'draft']);

    $pubLesson = $pubModule->lessons()->create(['title' => 'Published Lesson', 'status' => 'published']);
    $draftLesson = $pubModule->lessons()->create(['title' => 'Draft Lesson', 'status' => 'draft']);

    // Check module published scope
    expect($course->modules()->published()->pluck('id')->toArray())
        ->toContain($pubModule->id)
        ->not->toContain($draftModule->id);

    // Check lesson published scope
    expect($pubModule->lessons()->published()->pluck('id')->toArray())
        ->toContain($pubLesson->id)
        ->not->toContain($draftLesson->id);
});

test('existing public course pages continue operating with zero regressions', function () {
    $this->get('/courses/sap-fico-training')->assertOk();
    $this->get('/courses')->assertOk();
});
