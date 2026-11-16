<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\InstructorService;

function setupInstructorTestContext(): array
{
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => User::STATUS_ACTIVE,
        'email' => 'admin_inst_'.uniqid().'@example.com',
    ]);

    $instructor = User::factory()->instructor()->create([
        'name' => 'Prof. Vikram Rao',
        'email' => 'vikram_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
        'designation' => 'Lead SAP FICO Architect',
        'specialization' => 'Financial Accounting & S/4HANA',
        'experience_years' => 12,
        'qualification' => 'MBA Finance, SAP Certified Associate',
    ]);

    $otherInstructor = User::factory()->instructor()->create([
        'name' => 'Dr. Meenakshi Sundaram',
        'email' => 'meenakshi_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
        'designation' => 'Workday HCM Specialist',
        'specialization' => 'Cloud Human Capital Management',
    ]);

    $student = User::factory()->student()->create([
        'name' => 'Kunal Joshi',
        'email' => 'kunal_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
    ]);

    $course1 = Course::create([
        'name' => 'SAP FICO Enterprise Masterclass',
        'slug' => 'sap-fico-master-'.uniqid(),
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive financial accounting.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $course2 = Course::create([
        'name' => 'Workday Core HCM Masterclass',
        'slug' => 'workday-hcm-'.uniqid(),
        'category' => 'workday',
        'status' => 'available',
        'short_description' => 'Enterprise human capital management.',
        'fee' => 40000,
        'published_at' => now(),
    ]);

    $module1 = CourseModule::create([
        'course_id' => $course1->id,
        'title' => 'Module 1: General Ledger',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson1 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'Lesson 1.1: Chart of Accounts',
        'content_type' => 'text',
        'duration_minutes' => 30,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course1->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDays(3),
        'approved_at' => now()->subDays(2),
    ]);

    $assignment = Assignment::create([
        'course_id' => $course1->id,
        'course_module_id' => $module1->id,
        'title' => 'GL Configuration Assessment',
        'slug' => 'gl-config-'.uniqid(),
        'description' => 'Configure standard enterprise chart of accounts.',
        'max_marks' => 100,
        'pass_marks' => 50,
        'max_attempts' => 3,
        'status' => Assignment::STATUS_PUBLISHED,
        'allow_text_submission' => true,
        'allow_file_submission' => true,
        'allowed_file_types' => 'pdf,zip',
        'max_file_size_mb' => 10,
    ]);

    $submission = AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $student->id,
        'attempt_number' => 1,
        'status' => AssignmentSubmission::STATUS_SUBMITTED,
        'content' => 'My detailed SAP general ledger business configuration submission.',
        'submitted_at' => now()->subHours(2),
    ]);

    return compact(
        'admin',
        'instructor',
        'otherInstructor',
        'student',
        'course1',
        'course2',
        'module1',
        'lesson1',
        'enrollment',
        'assignment',
        'submission'
    );
}

test('admin can view paginated instructor directory with search and status filter', function () {
    extract(setupInstructorTestContext());

    $response = $this->actingAs($admin)
        ->get(route('admin.instructors.index'));

    $response->assertOk()
        ->assertSee('Instructor &amp; Faculty Directory', false)
        ->assertSee($instructor->name)
        ->assertSee($instructor->email)
        ->assertSee($instructor->designation);

    // Search by name
    $searchResponse = $this->actingAs($admin)
        ->get(route('admin.instructors.index', ['search' => 'Vikram']));

    $searchResponse->assertOk()
        ->assertSee($instructor->name)
        ->assertDontSee($otherInstructor->name);
});

test('admin can create a new instructor with academic credentials', function () {
    extract(setupInstructorTestContext());

    $newEmail = 'priya_'.uniqid().'@zeblearn.com';

    $response = $this->actingAs($admin)
        ->post(route('admin.instructors.store'), [
            'name' => 'Priya Sharma',
            'email' => $newEmail,
            'password' => 'SecurePass123!',
            'phone' => '+91 98765 12345',
            'designation' => 'Senior Salesforce Architect',
            'specialization' => 'Salesforce Developer & LWC',
            'experience_years' => 9,
            'qualification' => 'B.Tech CS, Salesforce Certified Application Architect',
            'bio' => '10 years experience delivering global enterprise Salesforce implementations.',
            'status' => 'active',
        ]);

    $newInstructor = User::where('email', $newEmail)->first();
    expect($newInstructor)->not->toBeNull()
        ->and($newInstructor->role)->toBe(User::ROLE_INSTRUCTOR)
        ->and($newInstructor->designation)->toBe('Senior Salesforce Architect')
        ->and($newInstructor->experience_years)->toBe(9);

    $response->assertRedirect(route('admin.instructors.show', $newInstructor));
});

test('admin can edit an instructor profile', function () {
    extract(setupInstructorTestContext());

    $response = $this->actingAs($admin)
        ->put(route('admin.instructors.update', $instructor), [
            'name' => 'Prof. Vikram Rao Updated',
            'email' => $instructor->email,
            'phone' => '+91 99999 88888',
            'designation' => 'Distinguished Enterprise Architect',
            'specialization' => 'SAP S/4HANA & Central Finance',
            'experience_years' => 15,
            'qualification' => 'PhD, MBA, SAP Platinum Consultant',
            'bio' => 'Updated biography with extended corporate credentials.',
            'status' => 'active',
        ]);

    $response->assertRedirect(route('admin.instructors.show', $instructor));

    $fresh = $instructor->fresh();
    expect($fresh->name)->toBe('Prof. Vikram Rao Updated')
        ->and($fresh->designation)->toBe('Distinguished Enterprise Architect')
        ->and($fresh->experience_years)->toBe(15);
});

test('admin can toggle instructor status between active and suspended', function () {
    extract(setupInstructorTestContext());

    expect($instructor->isActive())->toBeTrue();

    // Suspend
    $this->actingAs($admin)
        ->patch(route('admin.instructors.toggle-status', $instructor))
        ->assertRedirect();

    expect($instructor->fresh()->status)->toBe(User::STATUS_SUSPENDED);

    // Reactivate
    $this->actingAs($admin)
        ->patch(route('admin.instructors.toggle-status', $instructor))
        ->assertRedirect();

    expect($instructor->fresh()->status)->toBe(User::STATUS_ACTIVE);
});

test('suspended instructor is denied access to protected instructor portal', function () {
    extract(setupInstructorTestContext());

    $instructor->status = User::STATUS_SUSPENDED;
    $instructor->save();

    $response = $this->actingAs($instructor)
        ->get(route('instructor.dashboard'));

    $response->assertForbidden();
});

test('admin can assign course to instructor and duplicate assignments are prevented', function () {
    extract(setupInstructorTestContext());

    expect($instructor->isAssignedToCourse($course1))->toBeFalse();

    // Assign course 1
    $response = $this->actingAs($admin)
        ->post(route('admin.instructors.courses.assign', $instructor), [
            'course_id' => $course1->id,
            'role_or_title' => 'Chief Lead Faculty',
        ]);

    $response->assertRedirect();
    expect($instructor->fresh()->isAssignedToCourse($course1))->toBeTrue();

    // Duplicate assignment attempt should not throw errors or create duplicate records
    $service = app(InstructorService::class);
    $duplicateResult = $service->assignInstructor($course1, $instructor, 'Lead Instructor');

    expect($duplicateResult)->toBeTrue();
    expect($course1->instructors()->where('users.id', $instructor->id)->count())->toBe(1);
});

test('admin can unassign course from instructor', function () {
    extract(setupInstructorTestContext());

    $course1->assignInstructor($instructor);
    expect($instructor->fresh()->isAssignedToCourse($course1))->toBeTrue();

    $response = $this->actingAs($admin)
        ->delete(route('admin.instructors.courses.unassign', [$instructor, $course1]));

    $response->assertRedirect();
    expect($instructor->fresh()->isAssignedToCourse($course1))->toBeFalse();
});

test('admin cannot delete instructor with assigned courses or graded submissions', function () {
    extract(setupInstructorTestContext());

    $course1->assignInstructor($instructor);

    // Attempt delete while assigned to course
    $response = $this->actingAs($admin)
        ->delete(route('admin.instructors.destroy', $instructor));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(User::where('id', $instructor->id)->exists())->toBeTrue();

    // Unassign course
    $course1->removeInstructor($instructor);

    // Now grade a submission as instructor
    $submission->update(['grader_id' => $instructor->id, 'status' => AssignmentSubmission::STATUS_GRADED]);

    $secondDeleteResponse = $this->actingAs($admin)
        ->delete(route('admin.instructors.destroy', $instructor));

    $secondDeleteResponse->assertRedirect();
    $secondDeleteResponse->assertSessionHas('error');
    expect(User::where('id', $instructor->id)->exists())->toBeTrue();
});

test('instructor can access their dedicated dashboard with authentic metrics', function () {
    extract(setupInstructorTestContext());

    $course1->assignInstructor($instructor);

    $response = $this->actingAs($instructor)
        ->get(route('instructor.dashboard'));

    $response->assertOk()
        ->assertSee('Welcome back, '.$instructor->name)
        ->assertSee($course1->name)
        ->assertSee('Faculty Console')
        ->assertSee('Pending Reviews');
});

test('instructor can view their assigned courses but is denied access to unassigned courses', function () {
    extract(setupInstructorTestContext());

    // Assign only course 1 to instructor
    $course1->assignInstructor($instructor);

    // View assigned courses list
    $this->actingAs($instructor)
        ->get(route('instructor.courses.index'))
        ->assertOk()
        ->assertSee($course1->name)
        ->assertDontSee($course2->name);

    // View assigned course details
    $this->actingAs($instructor)
        ->get(route('instructor.courses.show', $course1))
        ->assertOk()
        ->assertSee($course1->name)
        ->assertSee($module1->title);

    // Attempt to view unassigned course details directly via URL manipulation
    $unassignedResponse = $this->actingAs($instructor)
        ->get(route('instructor.courses.show', $course2));

    $unassignedResponse->assertForbidden();
});

test('instructor can view enrolled students roster for assigned course but denied for unassigned course', function () {
    extract(setupInstructorTestContext());

    $course1->assignInstructor($instructor);

    // Authorized access for course 1
    $response = $this->actingAs($instructor)
        ->get(route('instructor.courses.students', $course1));

    $response->assertOk()
        ->assertSee($student->name)
        ->assertSee($student->email);

    // Denied access for course 2
    $deniedResponse = $this->actingAs($instructor)
        ->get(route('instructor.courses.students', $course2));

    $deniedResponse->assertForbidden();
});

test('instructor can review and grade assignment submissions for their assigned courses', function () {
    extract(setupInstructorTestContext());

    $course1->assignInstructor($instructor);

    // Can view assignment submissions queue
    $this->actingAs($instructor)
        ->get(route('instructor.assignments.index'))
        ->assertOk()
        ->assertSee($student->name)
        ->assertSee($assignment->title);

    // Can view individual student submission details
    $this->actingAs($instructor)
        ->get(route('instructor.assignments.submissions.show', [$assignment, $submission]))
        ->assertOk()
        ->assertSee('Review &amp; Grade Submission', false)
        ->assertSee($submission->content);

    // Can submit grade
    $gradeResponse = $this->actingAs($instructor)
        ->post(route('instructor.assignments.submissions.grade', [$assignment, $submission]), [
            'marks_awarded' => 88,
            'feedback' => 'Exceptional chart of accounts mapping and configuration.',
            'action' => 'grade',
        ]);

    $gradeResponse->assertRedirect();
    $freshSubmission = $submission->fresh();
    expect($freshSubmission->isGraded())->toBeTrue()
        ->and($freshSubmission->marks_awarded)->toBe(88)
        ->and($freshSubmission->grader_id)->toBe($instructor->id);
});

test('instructor is denied access to grade assignment submissions for unassigned courses', function () {
    extract(setupInstructorTestContext());

    // Do NOT assign course 1 to otherInstructor
    $response = $this->actingAs($otherInstructor)
        ->get(route('instructor.assignments.submissions.show', [$assignment, $submission]));

    $response->assertForbidden();

    $gradeAttempt = $this->actingAs($otherInstructor)
        ->post(route('instructor.assignments.submissions.grade', [$assignment, $submission]), [
            'marks_awarded' => 95,
            'feedback' => 'Unauthorized grading attempt.',
            'action' => 'grade',
        ]);

    $gradeAttempt->assertForbidden();
});

test('student is denied access to instructor portal routes', function () {
    extract(setupInstructorTestContext());

    $this->actingAs($student)
        ->get(route('instructor.dashboard'))
        ->assertForbidden();

    $this->actingAs($student)
        ->get(route('instructor.courses.index'))
        ->assertForbidden();
});

test('instructor is denied access to admin-only routes', function () {
    extract(setupInstructorTestContext());

    $this->actingAs($instructor)
        ->get(route('admin.dashboard'))
        ->assertForbidden();

    $this->actingAs($instructor)
        ->get(route('admin.instructors.index'))
        ->assertForbidden();
});
