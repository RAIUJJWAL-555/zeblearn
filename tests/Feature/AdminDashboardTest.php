<?php

use App\Models\User;

test('admin can access admin dashboard layout and see welcome message', function () {
    $admin = User::factory()->admin()->active()->create([
        'name' => 'Vikram Malhotra',
        'email' => 'vikram@zeblearn.test',
    ]);

    $response = $this->actingAs($admin)->get('/admin/dashboard');

    $response->assertOk();
    $response->assertSee('Welcome back, Vikram Malhotra');
    $response->assertSee('ADMINISTRATION CONSOLE');
    $response->assertSee('Overview');
    $response->assertSee('Academic Management');
    $response->assertSee('Student Management');
    $response->assertSee('Finance &amp; Reports', false);
    $response->assertSee('Super Admin');
});

test('students cannot access admin dashboard and receive 403 forbidden', function () {
    $student = User::factory()->student()->active()->create();

    $response = $this->actingAs($student)->get('/admin/dashboard');

    $response->assertForbidden();
});

test('instructors cannot access admin dashboard and receive 403 forbidden', function () {
    $instructor = User::factory()->instructor()->active()->create();

    $response = $this->actingAs($instructor)->get('/admin/dashboard');

    $response->assertForbidden();
});

test('unauthenticated guests are redirected to login when accessing admin dashboard', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertRedirect(route('login'));
});

test('suspended admins cannot access admin dashboard', function () {
    $suspendedAdmin = User::factory()->admin()->suspended()->create();

    $response = $this->actingAs($suspendedAdmin)->get('/admin/dashboard');

    $response->assertForbidden();
});

test('admin can log out from the dashboard and session is invalidated', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('home'));
});
