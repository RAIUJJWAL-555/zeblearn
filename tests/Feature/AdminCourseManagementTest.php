<?php

use App\Models\Course;
use App\Models\User;

test('admin can access course management listing', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'SAP MM Sourcing & Procurement',
        'slug' => 'sap-mm-sourcing-procurement',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive materials management curriculum.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.index'));

    $response->assertOk();
    $response->assertSee('Course Management');
    $response->assertSee('SAP MM Sourcing & Procurement');
    $response->assertSee('₹30,000');
    $response->assertSee('Live');
});

test('unauthenticated guest is redirected to login when accessing admin courses', function () {
    $response = $this->get(route('admin.courses.index'));

    $response->assertRedirect(route('login'));
});

test('student cannot access admin courses and receives 403', function () {
    $student = User::factory()->student()->active()->create();

    $this->actingAs($student)->get(route('admin.courses.index'))->assertForbidden();
});

test('instructor cannot access admin courses and receives 403', function () {
    $instructor = User::factory()->instructor()->active()->create();

    $this->actingAs($instructor)->get(route('admin.courses.index'))->assertForbidden();
});

test('suspended admin cannot access admin courses and receives 403', function () {
    $suspendedAdmin = User::factory()->admin()->suspended()->create();

    $this->actingAs($suspendedAdmin)->get(route('admin.courses.index'))->assertForbidden();
});

test('admin can view course creation form', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->get(route('admin.courses.create'));

    $response->assertOk();
    $response->assertSee('Create New Course');
    $response->assertSee('Course Identity');
    $response->assertSee('Published (Live Online)');
});

test('admin can create a valid course with auto-generated slug', function () {
    $admin = User::factory()->admin()->active()->create();

    $payload = [
        'name' => 'Workday Financial Management 2026',
        'slug' => '',
        'category' => 'workday',
        'status' => 'upcoming',
        'fee' => 45000,
        'original_fee' => 70000,
        'discount' => '35% OFF',
        'modules_count' => 14,
        'duration' => '3 to 4 Months',
        'short_description' => 'Hands-on enterprise Workday financials training.',
        'overview' => 'Detailed multi-module curriculum for ERP consultants.',
        'highlights' => "General Ledger Architecture\nAccounts Payable\nFinancial Reporting",
        'is_published' => '1',
    ];

    $response = $this->actingAs($admin)->post(route('admin.courses.store'), $payload);

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('courses', [
        'name' => 'Workday Financial Management 2026',
        'slug' => 'workday-financial-management-2026',
        'category' => 'workday',
        'status' => 'upcoming',
        'fee' => 45000,
    ]);
});

test('course creation fails validation when required fields are missing', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->post(route('admin.courses.store'), [
        'name' => '',
        'category' => '',
        'status' => 'invalid-status',
        'fee' => -100,
    ]);

    $response->assertSessionHasErrors(['name', 'category', 'status', 'fee']);
});

test('admin can view course edit form', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Salesforce CPQ Billing Master',
        'slug' => 'salesforce-cpq-billing-master',
        'category' => 'salesforce',
        'status' => 'available',
        'short_description' => 'Salesforce CPQ configuration training.',
        'fee' => 35000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.edit', $course));

    $response->assertOk();
    $response->assertSee('Edit Course');
    $response->assertSee('Salesforce CPQ Billing Master');
});

test('admin can update course details successfully', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Cloud DevOps AWS',
        'slug' => 'cloud-devops-aws',
        'category' => 'cloud-computing',
        'status' => 'upcoming',
        'short_description' => 'Initial summary',
        'fee' => 28000,
    ]);

    $payload = [
        'name' => 'Cloud DevOps AWS & Kubernetes Master',
        'slug' => 'cloud-devops-aws-kubernetes-master',
        'category' => 'cloud-computing',
        'status' => 'available',
        'fee' => 32000,
        'short_description' => 'Updated curriculum description with Kubernetes.',
        'is_published' => '1',
    ];

    $response = $this->actingAs($admin)->put(route('admin.courses.update', $course), $payload);

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('success');

    $course->refresh();
    expect($course->name)->toBe('Cloud DevOps AWS & Kubernetes Master');
    expect($course->slug)->toBe('cloud-devops-aws-kubernetes-master');
    expect($course->status)->toBe('available');
    expect($course->fee)->toBe(32000);
    expect($course->published_at)->not->toBeNull();
});

test('admin can filter courses by category and status', function () {
    $admin = User::factory()->admin()->active()->create();

    $sapAvailable = Course::create([
        'name' => 'SAP FICO Specialization',
        'slug' => 'sap-fico-specialization',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'SAP FICO description',
        'fee' => 25000,
    ]);

    $workdayUpcoming = Course::create([
        'name' => 'Workday Advanced Reporting',
        'slug' => 'workday-advanced-reporting',
        'category' => 'workday',
        'status' => 'upcoming',
        'short_description' => 'Workday reporting description',
        'fee' => 30000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.index', [
        'category' => 'workday',
        'status' => 'upcoming',
    ]));

    $response->assertOk();
    $response->assertSee('Workday Advanced Reporting');
    $response->assertDontSee('SAP FICO Specialization');
});

test('admin can search courses by keyword', function () {
    $admin = User::factory()->admin()->active()->create();

    Course::create([
        'name' => 'Big Data PySpark Hadoop',
        'slug' => 'big-data-pyspark-hadoop',
        'category' => 'big-data',
        'status' => 'available',
        'short_description' => 'Big data engineering pipelines.',
        'fee' => 40000,
    ]);

    Course::create([
        'name' => 'Cyber Security SOC Analyst',
        'slug' => 'cyber-security-soc-analyst',
        'category' => 'cyber-security',
        'status' => 'available',
        'short_description' => 'SOC operations and SIEM.',
        'fee' => 35000,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.courses.index', ['search' => 'PySpark']));

    $response->assertOk();
    $response->assertSee('Big Data PySpark Hadoop');
    $response->assertDontSee('Cyber Security SOC Analyst');
});

test('admin can toggle course status via patch request', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Full-Stack MERN Architecture',
        'slug' => 'full-stack-mern-architecture',
        'category' => 'software-development',
        'status' => 'planned',
        'short_description' => 'MERN stack training description',
        'fee' => 25000,
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.courses.toggle-status', $course), [
        'status' => 'available',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $course->refresh();
    expect($course->status)->toBe('available');
});

test('toggle status rejects invalid status value', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Digital Marketing Growth',
        'slug' => 'digital-marketing-growth',
        'category' => 'digital-marketing',
        'status' => 'available',
        'short_description' => 'Growth marketing',
        'fee' => 20000,
    ]);

    $response = $this->actingAs($admin)->patch(route('admin.courses.toggle-status', $course), [
        'status' => 'discontinued',
    ]);

    $response->assertSessionHasErrors(['status']);

    $course->refresh();
    expect($course->status)->toBe('available');
});

test('flagship sap fico training course cannot be deleted', function () {
    $admin = User::factory()->admin()->active()->create();

    $flagshipCourse = Course::create([
        'name' => 'SAP FICO Training',
        'slug' => 'sap-fico-training',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Flagship enterprise finance course.',
        'fee' => 75000,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.courses.destroy', $flagshipCourse));

    $response->assertRedirect();
    $response->assertSessionHas('error');

    $this->assertDatabaseHas('courses', [
        'id' => $flagshipCourse->id,
        'slug' => 'sap-fico-training',
    ]);
});

test('admin can safely delete standard course', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Temporary Sandbox Course',
        'slug' => 'temporary-sandbox-course',
        'category' => 'sap-erp',
        'status' => 'planned',
        'short_description' => 'Course for deletion test',
        'fee' => 10000,
    ]);

    $response = $this->actingAs($admin)->delete(route('admin.courses.destroy', $course));

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('courses', [
        'id' => $course->id,
    ]);
});

test('public course pages continue operating with zero regressions', function () {
    // Flagship marketing course page
    $this->get('/courses/sap-fico-training')->assertOk();

    // Catalog page
    $this->get('/courses')->assertOk();
});
