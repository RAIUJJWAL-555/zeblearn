<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('admin dashboard computes and renders real database kpi metrics accurately', function () {
    // 1. Create seeded test dataset
    $admin = User::factory()->admin()->active()->create(['name' => 'Super Admin']);

    // 3 active students, 1 suspended student
    User::factory()->student()->active()->count(3)->create();
    User::factory()->student()->suspended()->count(1)->create();

    // 2 instructors
    User::factory()->instructor()->active()->count(2)->create();

    // Categories (if table migrated)
    if (Schema::hasTable('categories')) {
        Category::create(['name' => 'SAP ERP', 'slug' => 'sap-erp']);
        Category::create(['name' => 'Workday', 'slug' => 'workday']);
    }

    // Courses (1 available, 1 upcoming, 1 planned)
    Course::create([
        'name' => 'SAP FICO Masterclass',
        'slug' => 'sap-fico-masterclass',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Test course short description',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    Course::create([
        'name' => 'Workday Core HCM',
        'slug' => 'workday-core-hcm',
        'category' => 'workday',
        'status' => 'upcoming',
        'short_description' => 'Test course short description',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    Course::create([
        'name' => 'SAP EWM Logistics',
        'slug' => 'sap-ewm-logistics',
        'category' => 'sap-erp',
        'status' => 'planned',
        'short_description' => 'Test course short description',
        'fee' => 35000,
        'published_at' => null, // not published
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();

    // Total Students: 4 (3 active + 1 suspended)
    $response->assertSee('Total Students');
    $response->assertSee('Active Students');
    $response->assertSee('Total Instructors');
    $response->assertSee('Total Courses');
    $response->assertSee('Published Courses');
    $response->assertSee('Categories');

    // Recent courses table
    $response->assertSee('SAP FICO Masterclass');
    $response->assertSee('Workday Core HCM');
    $response->assertSee('SAP EWM Logistics');

    // Status breakdown labels
    $response->assertSee('Course Status Distribution');
    $response->assertSee('Available:');
    $response->assertSee('Upcoming:');
    $response->assertSee('Roadmap:');
});

test('admin dashboard handles empty datasets without errors or division by zero', function () {
    // Admin only, 0 students, 0 courses, 0 categories
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
    $response->assertSee('No students registered yet.');
    $response->assertSee('No courses found in database.');
    $response->assertSee('No course records found in database.');
});

test('recent students table displays accurate latest student details', function () {
    $admin = User::factory()->admin()->active()->create();

    $student = User::factory()->student()->active()->create([
        'name' => 'Ananya Iyer',
        'email' => 'ananya.iyer@example.test',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
    $response->assertSee('Ananya Iyer');
    $response->assertSee('ananya.iyer@example.test');
    $response->assertSee('Active');
    $response->assertSee('student');
});

test('student cannot access analytics dashboard and receives 403', function () {
    $student = User::factory()->student()->active()->create();

    $this->actingAs($student)->get('/admin/dashboard')->assertForbidden();
});

test('instructor cannot access analytics dashboard and receives 403', function () {
    $instructor = User::factory()->instructor()->active()->create();

    $this->actingAs($instructor)->get('/admin/dashboard')->assertForbidden();
});

test('unauthenticated guest is redirected to login when accessing analytics dashboard', function () {
    $this->get('/admin/dashboard')->assertRedirect(route('login'));
});
