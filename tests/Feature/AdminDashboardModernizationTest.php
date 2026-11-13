<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

test('admin dashboard renders modernized design system elements and active navigation shortcuts', function () {
    $admin = User::factory()->admin()->active()->create([
        'name' => 'Aditi Sharma',
        'email' => 'aditi.admin@zeblearn.test',
    ]);

    // Create sample data
    User::factory()->student()->active()->count(2)->create();
    User::factory()->instructor()->active()->create();

    if (Schema::hasTable('categories')) {
        Category::firstOrCreate(['slug' => 'sap-erp'], ['name' => 'SAP ERP']);
    }

    $course = Course::create([
        'name' => 'SAP S/4HANA Finance',
        'slug' => 'sap-s4hana-finance',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Test course description',
        'fee' => 45000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();

    // 1. Welcome banner & branding
    $response->assertSee('Welcome back, Aditi Sharma');
    $response->assertSee('ADMINISTRATION CONSOLE • REAL-TIME TELEMETRY');
    $response->assertSee('Overview Telemetry Active');
    $response->assertSee(route('admin.courses.create'));
    $response->assertSee(route('admin.enrollments.index'));
    $response->assertSee(route('courses.index'));

    // 2. Core academy metric cards
    $response->assertSee('Core Academy Metrics');
    $response->assertSee('Total Students');
    $response->assertSee('Active Students');
    $response->assertSee('Total Instructors');
    $response->assertSee('Total Courses');
    $response->assertSee('Published Courses');
    $response->assertSee('Categories');

    // 3. Status distribution card
    $response->assertSee('Course Status Distribution');
    $response->assertSee('Available Courses');
    $response->assertSee('Upcoming Batches');
    $response->assertSee('Roadmap / Planned');

    // 4. Analytics & trends
    $response->assertSee('Student Registration Trend');
    $response->assertSee('Course Distribution by Category');
    $response->assertSee('Monthly Velocity');
    $response->assertSee('Top Domains');
    $response->assertSee('Peak Velocity:');

    // 5. Recent tables
    $response->assertSee('Recent Student Registrations');
    $response->assertSee('Recent Courses');
    $response->assertSee('SAP S/4HANA Finance');
    $response->assertSee(route('admin.courses.edit', $course));

    // 6. Navigation Shortcuts
    $response->assertSee('Administrative Navigation Shortcuts');
    $response->assertSee(route('admin.courses.index'));
    $response->assertSee(route('admin.enrollments.index'));
    $response->assertSee(route('admin.payments.index'));
    $response->assertSee(route('admin.quizzes.index'));
    $response->assertSee(route('home'));
    $response->assertSee(route('book-demo'));
});

test('modernized admin dashboard renders graceful empty states when catalog and students are empty', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('No students registered yet.');
    $response->assertSee('No courses found in database.');
    $response->assertSee('No course records found in database.');
});
