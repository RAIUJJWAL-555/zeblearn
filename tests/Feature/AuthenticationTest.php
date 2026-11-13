<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

test('student registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk();
    $response->assertSee('Student');
    $response->assertSee('Register as Student');
});

test('students can register and are automatically assigned student role and redirected to student dashboard', function () {
    Event::fake([Registered::class]);

    $response = $this->post('/register', [
        'name' => 'Aarav Sharma',
        'email' => 'aarav.sharma@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ]);

    Event::assertDispatched(Registered::class);

    $this->assertAuthenticated();

    $user = User::where('email', 'aarav.sharma@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Aarav Sharma')
        ->and($user->role)->toBe(User::ROLE_STUDENT)
        ->and($user->status)->toBe(User::STATUS_ACTIVE)
        ->and($user->isStudent())->toBeTrue();

    $response->assertRedirect(route('student.dashboard'));
});

test('public registration cannot create admin users or modify role/status', function () {
    $response = $this->post('/register', [
        'name' => 'Malicious User',
        'email' => 'malicious@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'role' => 'admin',
        'status' => 'suspended',
    ]);

    $this->assertAuthenticated();

    $user = User::where('email', 'malicious@example.com')->first();
    expect($user->role)->toBe(User::ROLE_STUDENT)
        ->and($user->status)->toBe(User::STATUS_ACTIVE)
        ->and($user->isAdmin())->toBeFalse();

    $response->assertRedirect(route('student.dashboard'));
});

test('registration enforces field validations', function () {
    // Missing fields
    $this->post('/register', [])
        ->assertSessionHasErrors(['name', 'email', 'password']);

    // Password confirmation mismatch
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ])->assertSessionHasErrors(['password']);

    // Duplicate email
    User::factory()->create(['email' => 'duplicate@example.com']);
    $this->post('/register', [
        'name' => 'Duplicate User',
        'email' => 'duplicate@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors(['email']);
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertOk();
    $response->assertSee('Sign in to');
    $response->assertSee('ZebLearn');
});

test('users can authenticate using valid credentials', function () {
    $user = User::factory()->student()->active()->create([
        'email' => 'student@zeblearn.test',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $response = $this->post('/login', [
        'email' => 'student@zeblearn.test',
        'password' => 'CorrectPassword123!',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('student.dashboard'));
});

test('users cannot authenticate with incorrect password', function () {
    $user = User::factory()->student()->active()->create([
        'email' => 'student@zeblearn.test',
        'password' => Hash::make('CorrectPassword123!'),
    ]);

    $response = $this->post('/login', [
        'email' => 'student@zeblearn.test',
        'password' => 'WrongPassword!',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('suspended users cannot authenticate even with valid credentials', function () {
    $user = User::factory()->student()->suspended()->create([
        'email' => 'suspended@zeblearn.test',
        'password' => Hash::make('ValidPassword123!'),
    ]);

    $response = $this->post('/login', [
        'email' => 'suspended@zeblearn.test',
        'password' => 'ValidPassword123!',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email' => 'Your account is suspended. Please contact support.']);
});

test('role-based dashboard redirects direct users to their respective portals', function () {
    // 1. Admin login redirects to admin dashboard
    $admin = User::factory()->admin()->active()->create([
        'email' => 'admin@zeblearn.test',
        'password' => Hash::make('AdminPass123!'),
    ]);

    $this->post('/login', [
        'email' => 'admin@zeblearn.test',
        'password' => 'AdminPass123!',
    ])->assertRedirect(route('admin.dashboard'));

    $this->post('/logout');

    // 2. Instructor login redirects to instructor dashboard
    $instructor = User::factory()->instructor()->active()->create([
        'email' => 'instructor@zeblearn.test',
        'password' => Hash::make('InstructorPass123!'),
    ]);

    $this->post('/login', [
        'email' => 'instructor@zeblearn.test',
        'password' => 'InstructorPass123!',
    ])->assertRedirect(route('instructor.dashboard'));

    $this->post('/logout');

    // 3. Student login redirects to student dashboard
    $student = User::factory()->student()->active()->create([
        'email' => 'student.portal@zeblearn.test',
        'password' => Hash::make('StudentPass123!'),
    ]);

    $this->post('/login', [
        'email' => 'student.portal@zeblearn.test',
        'password' => 'StudentPass123!',
    ])->assertRedirect(route('student.dashboard'));
});

test('authenticated users can log out and their session is invalidated', function () {
    $user = User::factory()->student()->active()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('home'));
});

test('unauthenticated browser users are redirected to login when accessing protected lms routes', function () {
    $response = $this->get('/student/dashboard');

    $response->assertRedirect(route('login'));
});

test('forgot password screen can be rendered and reset link requested', function () {
    $response = $this->get('/forgot-password');
    $response->assertOk();

    $user = User::factory()->student()->active()->create([
        'email' => 'forgot@zeblearn.test',
    ]);

    $postResponse = $this->post('/forgot-password', [
        'email' => 'forgot@zeblearn.test',
    ]);

    $postResponse->assertSessionHas('status');

    // Non-existent email also returns safe generic message (does not reveal user enumeration)
    $unknownResponse = $this->post('/forgot-password', [
        'email' => 'nonexistent@zeblearn.test',
    ]);

    $unknownResponse->assertSessionHas('status', 'If an account exists for this email, a password reset link has been sent.');
});

test('password can be reset with a valid token', function () {
    Event::fake([PasswordReset::class]);

    $user = User::factory()->student()->active()->create([
        'email' => 'resetme@zeblearn.test',
        'password' => Hash::make('OldPassword123!'),
    ]);

    $token = Password::createToken($user);

    $renderResponse = $this->get("/reset-password/{$token}?email=".urlencode($user->email));
    $renderResponse->assertOk();

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'NewBrandPassword123!',
        'password_confirmation' => 'NewBrandPassword123!',
    ]);

    Event::assertDispatched(PasswordReset::class);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    $user->refresh();
    expect(Hash::check('NewBrandPassword123!', $user->password))->toBeTrue();
});

test('existing public website routes still work', function () {
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
