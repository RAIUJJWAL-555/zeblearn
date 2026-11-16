<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

function setupProfileTestContext(): array
{
    $admin = User::factory()->create([
        'name' => 'Root Administrator',
        'role' => User::ROLE_ADMIN,
        'status' => User::STATUS_ACTIVE,
        'email' => 'admin_prof_'.uniqid().'@example.com',
    ]);

    $instructor = User::factory()->instructor()->create([
        'name' => 'Prof. Sangeeta Nair',
        'email' => 'sangeeta_prof_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
        'designation' => 'Lead SAP MM Architect',
        'specialization' => 'Materials Management & Logistics',
        'experience_years' => 14,
        'qualification' => 'M.Tech, SAP Certified Associate',
        'bio' => 'Experienced enterprise consultant.',
    ]);

    $student = User::factory()->student()->create([
        'name' => 'Rahul Sharma',
        'email' => 'rahul_prof_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
        'phone' => '+91 98765 11111',
        'bio' => 'Aspiring SAP Consultant.',
    ]);

    $otherStudent = User::factory()->student()->create([
        'name' => 'Pooja Hegde',
        'email' => 'pooja_prof_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
    ]);

    $course = Course::create([
        'name' => 'SAP S/4HANA Finance Masterclass',
        'slug' => 'sap-s4hana-fin-'.uniqid(),
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Enterprise financial management.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Financial Accounting Basics',
        'slug' => 'fi-basics-'.uniqid(),
        'order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'General Ledger Setup',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('admin', 'instructor', 'student', 'otherStudent', 'course', 'module', 'lesson');
}

it('allows student to view their own profile with academic stats', function () {
    $ctx = setupProfileTestContext();

    Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
        'approved_at' => now(),
    ]);

    LessonProgress::create([
        'user_id' => $ctx['student']->id,
        'lesson_id' => $ctx['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    $response = $this->actingAs($ctx['student'])
        ->get(route('student.profile.show'));

    $response->assertOk();
    $response->assertSee('Rahul Sharma');
    $response->assertSee('Aspiring SAP Consultant.');
    $response->assertSee('Personal &amp; Contact Details', false);
});

it('allows student to update personal profile details and preferences', function () {
    $ctx = setupProfileTestContext();

    $response = $this->actingAs($ctx['student'])
        ->put(route('student.profile.update'), [
            'name' => 'Rahul K. Sharma',
            'phone' => '+91 99999 88888',
            'bio' => 'Updated goals: SAP S/4HANA Certification candidate.',
            'preferences' => [
                'email_notifications' => '1',
                'course_reminders' => '1',
                'marketing_emails' => '0',
            ],
        ]);

    $response->assertRedirect(route('student.profile.show'));
    $response->assertSessionHas('success');

    $student = $ctx['student']->fresh();
    expect($student->name)->toBe('Rahul K. Sharma');
    expect($student->phone)->toBe('+91 99999 88888');
    expect($student->bio)->toBe('Updated goals: SAP S/4HANA Certification candidate.');
    expect($student->getPreference('email_notifications'))->toBeTrue();
    expect($student->getPreference('course_reminders'))->toBeTrue();
    expect($student->getPreference('marketing_emails'))->toBeFalse();
});

it('validates and uploads student avatar image safely', function () {
    Storage::fake('public');
    $ctx = setupProfileTestContext();

    $file = UploadedFile::fake()->image('my_avatar.png', 400, 400);

    $response = $this->actingAs($ctx['student'])
        ->put(route('student.profile.update'), [
            'name' => $ctx['student']->name,
            'avatar' => $file,
        ]);

    $response->assertRedirect(route('student.profile.show'));

    $student = $ctx['student']->fresh();
    expect($student->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($student->avatar_path);
});

it('rejects oversized or invalid avatar file uploads', function () {
    Storage::fake('public');
    $ctx = setupProfileTestContext();

    // 1. Text file disguised as image
    $fakeTxt = UploadedFile::fake()->create('malicious.pdf', 500, 'application/pdf');

    $response = $this->actingAs($ctx['student'])
        ->put(route('student.profile.update'), [
            'name' => $ctx['student']->name,
            'avatar' => $fakeTxt,
        ]);

    $response->assertSessionHasErrors('avatar');

    // 2. Oversized image (> 2MB = 2048KB)
    $bigImage = UploadedFile::fake()->image('huge.png')->size(3000);

    $response2 = $this->actingAs($ctx['student'])
        ->put(route('student.profile.update'), [
            'name' => $ctx['student']->name,
            'avatar' => $bigImage,
        ]);

    $response2->assertSessionHasErrors('avatar');
});

it('allows student to remove their avatar photo', function () {
    Storage::fake('public');
    $ctx = setupProfileTestContext();

    $file = UploadedFile::fake()->image('to_remove.jpg', 300, 300);
    $path = $file->storeAs('avatars', 'to_remove.jpg', 'public');
    $ctx['student']->update(['avatar_path' => $path]);

    expect($ctx['student']->avatar_path)->toBe($path);

    $response = $this->actingAs($ctx['student'])
        ->delete(route('student.profile.avatar.destroy'));

    $response->assertRedirect();
    expect($ctx['student']->fresh()->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

it('allows student to update password with valid current password', function () {
    $ctx = setupProfileTestContext();

    $response = $this->actingAs($ctx['student'])
        ->put(route('student.profile.password.update'), [
            'current_password' => 'password',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    expect(Hash::check('NewSecurePassword123!', $ctx['student']->fresh()->password))->toBeTrue();
});

it('rejects password update with incorrect current password', function () {
    $ctx = setupProfileTestContext();

    $response = $this->actingAs($ctx['student'])
        ->put(route('student.profile.password.update'), [
            'current_password' => 'wrong-pass',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

    $response->assertSessionHasErrors('current_password');
    expect(Hash::check('password', $ctx['student']->fresh()->password))->toBeTrue();
});

it('prevents students from modifying role, status, or administrative fields', function () {
    $ctx = setupProfileTestContext();

    $response = $this->actingAs($ctx['student'])
        ->put(route('student.profile.update'), [
            'name' => 'Hacker Name',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_SUSPENDED,
            'email' => 'tampered@example.com',
        ]);

    $response->assertRedirect();

    $student = $ctx['student']->fresh();
    expect($student->role)->toBe(User::ROLE_STUDENT);
    expect($student->status)->toBe(User::STATUS_ACTIVE);
    expect($student->email)->toBe($ctx['student']->email);
});

it('displays accurate learning profile metrics computed from verified progress', function () {
    $ctx = setupProfileTestContext();

    // Approved enrollment
    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
        'approved_at' => now(),
    ]);

    // Complete lesson
    LessonProgress::create([
        'user_id' => $ctx['student']->id,
        'lesson_id' => $ctx['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    // Assignment submission
    $assignment = Assignment::create([
        'course_id' => $ctx['course']->id,
        'title' => 'SAP FI Journal Entry Test',
        'slug' => 'journal-entry-'.uniqid(),
        'status' => 'published',
        'max_marks' => 100,
        'max_attempts' => 1,
    ]);

    AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $ctx['student']->id,
        'attempt_number' => 1,
        'status' => AssignmentSubmission::STATUS_GRADED,
        'marks_awarded' => 95,
        'submitted_at' => now(),
    ]);

    // Certificate
    Certificate::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-TEST01',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($ctx['student'])
        ->get(route('student.profile.learning'));

    $response->assertOk();
    $response->assertSee('Learning Profile');
    $response->assertSee('SAP S/4HANA Finance Masterclass');
    $response->assertSee('100%');
    $response->assertSee('ZEB-2026-TEST01');
    $response->assertSee('95/100');
});

it('allows instructor to view and update faculty profile and credentials', function () {
    $ctx = setupProfileTestContext();

    // View profile
    $viewResponse = $this->actingAs($ctx['instructor'])
        ->get(route('instructor.profile.show'));

    $viewResponse->assertOk();
    $viewResponse->assertSee('Prof. Sangeeta Nair');
    $viewResponse->assertSee('Lead SAP MM Architect');

    // Update profile
    $updateResponse = $this->actingAs($ctx['instructor'])
        ->put(route('instructor.profile.update'), [
            'name' => 'Prof. Sangeeta Nair, Ph.D.',
            'phone' => '+91 91234 56789',
            'designation' => 'Principal SAP Solutions Architect',
            'specialization' => 'SAP MM, SCM & Ariba',
            'experience_years' => 16,
            'qualification' => 'Ph.D. in Supply Chain, SAP MM Master',
            'bio' => 'Two decades of consulting experience.',
        ]);

    $updateResponse->assertRedirect(route('instructor.profile.show'));
    $updateResponse->assertSessionHas('success');

    $instructor = $ctx['instructor']->fresh();
    expect($instructor->name)->toBe('Prof. Sangeeta Nair, Ph.D.');
    expect($instructor->designation)->toBe('Principal SAP Solutions Architect');
    expect($instructor->specialization)->toBe('SAP MM, SCM & Ariba');
    expect($instructor->experience_years)->toBe(16);
});

it('prevents instructors from altering their own role or status', function () {
    $ctx = setupProfileTestContext();

    $response = $this->actingAs($ctx['instructor'])
        ->put(route('instructor.profile.update'), [
            'name' => 'Attempted Privilege Escalation',
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_SUSPENDED,
        ]);

    $response->assertRedirect();

    $instructor = $ctx['instructor']->fresh();
    expect($instructor->role)->toBe(User::ROLE_INSTRUCTOR);
    expect($instructor->status)->toBe(User::STATUS_ACTIVE);
});

it('allows admin to search and filter users in directory', function () {
    $ctx = setupProfileTestContext();

    // 1. Directory Index
    $response = $this->actingAs($ctx['admin'])
        ->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSee('User Directory &amp; Accounts', false);
    $response->assertSee('Rahul Sharma');
    $response->assertSee('Prof. Sangeeta Nair');

    // 2. Search query
    $searchResponse = $this->actingAs($ctx['admin'])
        ->get(route('admin.users.index', ['search' => 'Sangeeta']));

    $searchResponse->assertOk();
    $searchResponse->assertSee('Prof. Sangeeta Nair');
    $searchResponse->assertDontSee('Rahul Sharma');

    // 3. Filter by role
    $roleResponse = $this->actingAs($ctx['admin'])
        ->get(route('admin.users.index', ['role' => 'student']));

    $roleResponse->assertOk();
    $roleResponse->assertSee('Rahul Sharma');
    $roleResponse->assertDontSee('Prof. Sangeeta Nair');
});

it('allows admin to view detailed user academic and financial history', function () {
    $ctx = setupProfileTestContext();

    Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
        'approved_at' => now(),
    ]);

    Payment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_hist_1',
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($ctx['admin'])
        ->get(route('admin.users.show', $ctx['student']));

    $response->assertOk();
    $response->assertSee('Rahul Sharma');
    $response->assertSee('SAP S/4HANA Finance Masterclass');
    $response->assertSee('order_hist_1');
});

it('allows admin to edit user information, role, and status', function () {
    $ctx = setupProfileTestContext();

    $response = $this->actingAs($ctx['admin'])
        ->put(route('admin.users.update', $ctx['student']), [
            'name' => 'Rahul Sharma (Promoted)',
            'email' => $ctx['student']->email,
            'role' => User::ROLE_INSTRUCTOR,
            'status' => User::STATUS_ACTIVE,
            'designation' => 'Junior Faculty Associate',
        ]);

    $response->assertRedirect(route('admin.users.show', $ctx['student']));
    $response->assertSessionHas('success');

    $student = $ctx['student']->fresh();
    expect($student->name)->toBe('Rahul Sharma (Promoted)');
    expect($student->role)->toBe(User::ROLE_INSTRUCTOR);
    expect($student->designation)->toBe('Junior Faculty Associate');
});

it('prevents admin from demoting or suspending their own account', function () {
    $ctx = setupProfileTestContext();

    // 1. Try to demote self
    $response1 = $this->actingAs($ctx['admin'])
        ->put(route('admin.users.update', $ctx['admin']), [
            'name' => $ctx['admin']->name,
            'email' => $ctx['admin']->email,
            'role' => User::ROLE_STUDENT,
            'status' => User::STATUS_ACTIVE,
        ]);

    $response1->assertSessionHas('error');
    expect($ctx['admin']->fresh()->role)->toBe(User::ROLE_ADMIN);

    // 2. Try to suspend self via toggle-status
    $response2 = $this->actingAs($ctx['admin'])
        ->patch(route('admin.users.toggle-status', $ctx['admin']));

    $response2->assertSessionHas('error');
    expect($ctx['admin']->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

it('toggles user account status between active and suspended', function () {
    $ctx = setupProfileTestContext();

    expect($ctx['student']->status)->toBe(User::STATUS_ACTIVE);

    // Suspend
    $response = $this->actingAs($ctx['admin'])
        ->patch(route('admin.users.toggle-status', $ctx['student']));

    $response->assertRedirect();
    $response->assertSessionHas('success');
    expect($ctx['student']->fresh()->status)->toBe(User::STATUS_SUSPENDED);

    // Reactivate
    $response2 = $this->actingAs($ctx['admin'])
        ->patch(route('admin.users.toggle-status', $ctx['student']));

    $response2->assertRedirect();
    expect($ctx['student']->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

it('prevents deletion of users associated with enrollments, payments, or grading records', function () {
    $ctx = setupProfileTestContext();

    Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
    ]);

    $response = $this->actingAs($ctx['admin'])
        ->delete(route('admin.users.destroy', $ctx['student']));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(User::where('id', $ctx['student']->id)->exists())->toBeTrue();
});

it('allows safe deletion of unreferenced user accounts', function () {
    $ctx = setupProfileTestContext();

    $cleanUser = User::factory()->student()->create([
        'name' => 'Clean User to Delete',
        'email' => 'clean_'.uniqid().'@example.com',
    ]);

    $response = $this->actingAs($ctx['admin'])
        ->delete(route('admin.users.destroy', $cleanUser));

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success');
    expect(User::where('id', $cleanUser->id)->exists())->toBeFalse();
});

it('enforces strict RBAC boundaries and ownership across profile routes', function () {
    $ctx = setupProfileTestContext();

    // Guest cannot access any profile route
    $this->get(route('student.profile.show'))->assertRedirect(route('login'));
    $this->get(route('instructor.profile.show'))->assertRedirect(route('login'));
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));

    // Student cannot access instructor profile or admin user management
    $this->actingAs($ctx['student'])->get(route('instructor.profile.show'))->assertForbidden();
    $this->actingAs($ctx['student'])->get(route('admin.users.index'))->assertForbidden();

    // Instructor cannot access student profile or admin user management
    $this->actingAs($ctx['instructor'])->get(route('student.profile.show'))->assertForbidden();
    $this->actingAs($ctx['instructor'])->get(route('admin.users.index'))->assertForbidden();

    // Admin can access admin user directory
    $this->actingAs($ctx['admin'])->get(route('admin.users.index'))->assertOk();

    // Student can access their own profile
    $this->actingAs($ctx['student'])->get(route('student.profile.show'))->assertOk();

    // Instructor can access their own profile
    $this->actingAs($ctx['instructor'])->get(route('instructor.profile.show'))->assertOk();
});
