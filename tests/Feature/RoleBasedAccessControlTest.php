<?php

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed();

    Route::middleware('role:admin')->get('/_test/admin-protected', fn () => response()->json(['status' => 'admin_ok']));
    Route::middleware('role:instructor')->get('/_test/instructor-protected', fn () => response()->json(['status' => 'instructor_ok']));
    Route::middleware('role:student')->get('/_test/student-protected', fn () => response()->json(['status' => 'student_ok']));
    Route::middleware('role:admin,instructor')->get('/_test/staff-protected', fn () => response()->json(['status' => 'staff_ok']));
});

test('default role for a newly created user is student and status is active', function () {
    $user = User::create([
        'name' => 'Regular Student',
        'email' => 'student@zeblearn.test',
        'password' => 'secret123',
    ]);

    expect($user->role)->toBe('student')
        ->and($user->status)->toBe('active')
        ->and($user->isStudent())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse()
        ->and($user->isInstructor())->toBeFalse()
        ->and($user->isActive())->toBeTrue();
});

test('role and status cannot be mass assigned through untrusted request attributes', function () {
    $user = User::create([
        'name' => 'Sneaky User',
        'email' => 'sneaky@zeblearn.test',
        'password' => 'secret123',
        'role' => 'admin',
        'status' => 'suspended',
    ]);

    expect($user->role)->toBe('student')
        ->and($user->status)->toBe('active')
        ->and($user->isAdmin())->toBeFalse();
});

test('admin can pass admin role authorization and access protected endpoints', function () {
    $admin = User::factory()->admin()->active()->create();

    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->isActive())->toBeTrue();

    // Gate checks
    expect(Gate::forUser($admin)->allows('admin-only'))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('access-admin'))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('access-instructor'))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('access-lms'))->toBeTrue();

    // Middleware route access
    $this->actingAs($admin)
        ->getJson('/_test/admin-protected')
        ->assertOk()
        ->assertJson(['status' => 'admin_ok']);

    $this->actingAs($admin)
        ->getJson('/_test/staff-protected')
        ->assertOk()
        ->assertJson(['status' => 'staff_ok']);
});

test('student cannot access admin-protected functionality', function () {
    $student = User::factory()->student()->active()->create();

    expect($student->isStudent())->toBeTrue()
        ->and($student->isAdmin())->toBeFalse();

    // Gate checks
    expect(Gate::forUser($student)->allows('admin-only'))->toBeFalse()
        ->and(Gate::forUser($student)->allows('access-admin'))->toBeFalse()
        ->and(Gate::forUser($student)->allows('instructor-only'))->toBeFalse()
        ->and(Gate::forUser($student)->allows('student-only'))->toBeTrue();

    // Middleware route access rejected with 403 Forbidden
    $this->actingAs($student)
        ->getJson('/_test/admin-protected')
        ->assertForbidden();

    $this->actingAs($student)
        ->getJson('/_test/staff-protected')
        ->assertForbidden();

    // Student can access student-protected route
    $this->actingAs($student)
        ->getJson('/_test/student-protected')
        ->assertOk()
        ->assertJson(['status' => 'student_ok']);
});

test('instructor cannot access admin-only functionality', function () {
    $instructor = User::factory()->instructor()->active()->create();

    expect($instructor->isInstructor())->toBeTrue()
        ->and($instructor->isAdmin())->toBeFalse();

    // Gate checks
    expect(Gate::forUser($instructor)->allows('admin-only'))->toBeFalse()
        ->and(Gate::forUser($instructor)->allows('access-admin'))->toBeFalse()
        ->and(Gate::forUser($instructor)->allows('instructor-only'))->toBeTrue()
        ->and(Gate::forUser($instructor)->allows('access-instructor'))->toBeTrue()
        ->and(Gate::forUser($instructor)->allows('student-only'))->toBeFalse();

    // Middleware route access rejected with 403 Forbidden
    $this->actingAs($instructor)
        ->getJson('/_test/admin-protected')
        ->assertForbidden();

    // Instructor can access staff-protected and instructor-protected routes
    $this->actingAs($instructor)
        ->getJson('/_test/instructor-protected')
        ->assertOk()
        ->assertJson(['status' => 'instructor_ok']);

    $this->actingAs($instructor)
        ->getJson('/_test/staff-protected')
        ->assertOk()
        ->assertJson(['status' => 'staff_ok']);
});

test('suspended users cannot access protected LMS functionality', function () {
    $suspendedAdmin = User::factory()->admin()->suspended()->create();
    $suspendedStudent = User::factory()->student()->suspended()->create();

    expect($suspendedAdmin->isActive())->toBeFalse()
        ->and($suspendedStudent->isActive())->toBeFalse();

    // Gates reject suspended accounts
    expect(Gate::forUser($suspendedAdmin)->allows('admin-only'))->toBeFalse()
        ->and(Gate::forUser($suspendedAdmin)->allows('access-admin'))->toBeFalse()
        ->and(Gate::forUser($suspendedStudent)->allows('student-only'))->toBeFalse()
        ->and(Gate::forUser($suspendedStudent)->allows('access-lms'))->toBeFalse();

    // Middleware blocks suspended users
    $this->actingAs($suspendedAdmin)
        ->getJson('/_test/admin-protected')
        ->assertForbidden();

    $this->actingAs($suspendedStudent)
        ->getJson('/_test/student-protected')
        ->assertForbidden();
});

test('unauthenticated users cannot access protected LMS routes', function () {
    $this->getJson('/_test/admin-protected')
        ->assertForbidden();

    $this->getJson('/_test/student-protected')
        ->assertForbidden();
});

test('public users can still access existing website routes', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('ZebLearn');

    $this->get('/about')
        ->assertOk();

    $this->get('/contact')
        ->assertOk();

    $this->get('/book-demo')
        ->assertOk();

    $this->get('/blogs')
        ->assertOk();
});

test('existing public course functionality remains unaffected', function () {
    $this->get('/courses')
        ->assertOk()
        ->assertSee('SAP Industrial Machinery Training')
        ->assertSee('SAP ABAP Training');

    $this->get('/courses?category=sap-erp')
        ->assertOk();

    $this->get('/courses/sap-fico-training')
        ->assertOk()
        ->assertSee('SAP FICO');
});
