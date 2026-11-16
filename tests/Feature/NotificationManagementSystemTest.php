<?php

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\AssignmentGradedNotification;
use App\Notifications\AssignmentResubmissionRequestedNotification;
use App\Notifications\CertificateIssuedNotification;
use App\Notifications\CourseAssignedNotification;
use App\Notifications\EnrollmentApprovedNotification;
use App\Notifications\EnrollmentRejectedNotification;
use App\Notifications\NewAssignmentSubmissionNotification;
use App\Notifications\NewEnrollmentRequestNotification;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentReceivedAdminNotification;
use App\Services\AssignmentService;
use App\Services\CertificateService;
use App\Services\InstructorService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;

function setupNotificationTestContext(): array
{
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
        'status' => User::STATUS_ACTIVE,
        'email' => 'admin_notif_'.uniqid().'@example.com',
    ]);

    $instructor = User::factory()->instructor()->create([
        'name' => 'Prof. Ananya Sen',
        'email' => 'ananya_notif_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
    ]);

    $otherInstructor = User::factory()->instructor()->create([
        'name' => 'Prof. Rohit Verma',
        'email' => 'rohit_notif_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
    ]);

    $student = User::factory()->student()->create([
        'name' => 'Aditi Sharma',
        'email' => 'aditi_notif_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
    ]);

    $otherStudent = User::factory()->student()->create([
        'name' => 'Karan Patel',
        'email' => 'karan_notif_'.uniqid().'@example.com',
        'status' => User::STATUS_ACTIVE,
    ]);

    $course = Course::create([
        'name' => 'SAP FICO Professional Masterclass',
        'slug' => 'sap-fico-master-'.uniqid(),
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive enterprise financial accounting.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $module = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'General Ledger Accounting',
        'slug' => 'gl-accounting-'.uniqid(),
        'order' => 1,
        'status' => 'published',
    ]);

    $lesson = Lesson::create([
        'course_module_id' => $module->id,
        'title' => 'Chart of Accounts Architecture',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    return compact('admin', 'instructor', 'otherInstructor', 'student', 'otherStudent', 'course', 'module', 'lesson');
}

it('dispatches notification when admin approves student enrollment', function () {
    $ctx = setupNotificationTestContext();

    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_PENDING,
        'enrollment_source' => 'web',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($ctx['admin'])
        ->patch(route('admin.enrollments.approve', $enrollment), [
            'admin_note' => 'Welcome to the masterclass!',
        ]);

    $response->assertRedirect();
    expect($enrollment->fresh()->status)->toBe(Enrollment::STATUS_APPROVED);

    $notifications = $ctx['student']->notifications()->get();
    expect($notifications)->toHaveCount(1);
    expect($notifications->first()->type)->toBe(EnrollmentApprovedNotification::class);
    expect($notifications->first()->data['title'])->toBe('Enrollment Approved!');
    expect($notifications->first()->data['badge_variant'])->toBe('success');
    expect($notifications->first()->data['url'])->toBe(route('student.courses.show', $ctx['course']->slug));
});

it('dispatches notification when admin rejects student enrollment', function () {
    $ctx = setupNotificationTestContext();

    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_PENDING,
        'enrollment_source' => 'web',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($ctx['admin'])
        ->patch(route('admin.enrollments.reject', $enrollment), [
            'admin_note' => 'Incomplete prerequisite details provided.',
        ]);

    $response->assertRedirect();
    expect($enrollment->fresh()->status)->toBe(Enrollment::STATUS_REJECTED);

    $notifications = $ctx['student']->notifications()->get();
    expect($notifications)->toHaveCount(1);
    expect($notifications->first()->type)->toBe(EnrollmentRejectedNotification::class);
    expect($notifications->first()->data['message'])->toContain('Incomplete prerequisite details provided.');
    expect($notifications->first()->data['badge_variant'])->toBe('danger');
});

it('notifies administrators when student submits a new enrollment request', function () {
    $ctx = setupNotificationTestContext();

    $response = $this->actingAs($ctx['student'])
        ->post(route('courses.enroll', $ctx['course']));

    $response->assertRedirect(route('student.dashboard'));

    $adminNotifications = $ctx['admin']->notifications()->get();
    expect($adminNotifications)->toHaveCount(1);
    expect($adminNotifications->first()->type)->toBe(NewEnrollmentRequestNotification::class);
    expect($adminNotifications->first()->data['message'])->toContain($ctx['student']->name);
    expect($adminNotifications->first()->data['message'])->toContain($ctx['course']->name);
});

it('notifies student and admins upon payment success', function () {
    $ctx = setupNotificationTestContext();

    $payment = Payment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'amount' => 3500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_'.uniqid(),
        'gateway_payment_id' => 'pay_'.uniqid(),
        'paid_at' => now(),
    ]);

    app(NotificationService::class)->notifyPaymentSuccess($payment);

    // Student notification
    $studentNotif = $ctx['student']->notifications()->first();
    expect($studentNotif)->not->toBeNull();
    expect($studentNotif->type)->toBe(PaymentConfirmedNotification::class);
    expect($studentNotif->data['title'])->toBe('Payment Successful');

    // Admin notification
    $adminNotif = $ctx['admin']->notifications()->first();
    expect($adminNotif)->not->toBeNull();
    expect($adminNotif->type)->toBe(PaymentReceivedAdminNotification::class);
    expect($adminNotif->data['title'])->toBe('Payment Received');
});

it('notifies student when a certificate of completion is issued', function () {
    $ctx = setupNotificationTestContext();

    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
        'approved_at' => now(),
    ]);

    // Complete the single lesson
    LessonProgress::create([
        'user_id' => $ctx['student']->id,
        'lesson_id' => $ctx['lesson']->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    $certService = app(CertificateService::class);
    $certificate = $certService->issueCertificate($ctx['student'], $ctx['course'], $enrollment);

    expect($certificate)->not->toBeNull();

    $studentNotifs = $ctx['student']->notifications()->get();
    expect($studentNotifs)->toHaveCount(1);
    expect($studentNotifs->first()->type)->toBe(CertificateIssuedNotification::class);
    expect($studentNotifs->first()->data['title'])->toBe('Certificate Earned!');
    expect($studentNotifs->first()->data['message'])->toContain($certificate->certificate_number);
});

it('notifies student when an assignment is graded and when resubmission is requested', function () {
    $ctx = setupNotificationTestContext();

    $assignment = Assignment::create([
        'course_id' => $ctx['course']->id,
        'title' => 'SAP Document Splitting Exercise',
        'slug' => 'sap-doc-split-'.uniqid(),
        'status' => 'published',
        'max_marks' => 100,
        'max_attempts' => 3,
        'allow_text_submission' => true,
    ]);

    $submission = AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $ctx['student']->id,
        'attempt_number' => 1,
        'status' => AssignmentSubmission::STATUS_SUBMITTED,
        'submission_text' => 'Here is my configuration solution for document splitting.',
        'submitted_at' => now(),
    ]);

    $assignmentService = app(AssignmentService::class);

    // 1. Grade submission
    $assignmentService->gradeSubmission(
        grader: $ctx['instructor'],
        submission: $submission,
        marksAwarded: 92,
        feedback: 'Excellent work on passive splitting rules.'
    );

    $notifs = $ctx['student']->notifications()->get();
    expect($notifs)->toHaveCount(1);
    expect($notifs->first()->type)->toBe(AssignmentGradedNotification::class);
    expect($notifs->first()->data['title'])->toBe('Assignment Graded');
    expect($notifs->first()->data['message'])->toContain('92/100');

    // 2. Request resubmission on second attempt
    $submission2 = AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'user_id' => $ctx['student']->id,
        'attempt_number' => 2,
        'status' => AssignmentSubmission::STATUS_SUBMITTED,
        'submission_text' => 'Updated revision.',
        'submitted_at' => now(),
    ]);

    $assignmentService->requestResubmission(
        grader: $ctx['instructor'],
        submission: $submission2,
        feedback: 'Please verify the segment derivation logic.'
    );

    $resubNotifs = $ctx['student']->notifications()->where('type', AssignmentResubmissionRequestedNotification::class)->get();
    expect($resubNotifs)->toHaveCount(1);
    expect($resubNotifs->first()->data['title'])->toBe('Resubmission Requested');
    expect($resubNotifs->first()->data['message'])->toContain('Please verify the segment derivation logic.');
});

it('notifies assigned instructor when course is assigned and when new submission is submitted', function () {
    Storage::fake('local');
    $ctx = setupNotificationTestContext();

    $instructorService = app(InstructorService::class);
    $instructorService->assignInstructor($ctx['course'], $ctx['instructor'], 'Lead Consultant');

    // Instructor receives course assigned notification
    $instructorNotifs = $ctx['instructor']->notifications()->get();
    expect($instructorNotifs)->toHaveCount(1);
    expect($instructorNotifs->first()->type)->toBe(CourseAssignedNotification::class);
    expect($instructorNotifs->first()->data['title'])->toBe('Course Assigned');
    expect($instructorNotifs->first()->data['message'])->toContain($ctx['course']->name);

    // Unassigned instructor does not receive it
    expect($ctx['otherInstructor']->notifications()->count())->toBe(0);

    // Student submits an assignment
    Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
        'approved_at' => now(),
    ]);

    $assignment = Assignment::create([
        'course_id' => $ctx['course']->id,
        'title' => 'Company Code Configuration Paper',
        'slug' => 'company-code-paper-'.uniqid(),
        'status' => 'published',
        'max_marks' => 50,
        'max_attempts' => 2,
        'allow_text_submission' => true,
    ]);

    $assignmentService = app(AssignmentService::class);
    $submission = $assignmentService->submitAssignment(
        user: $ctx['student'],
        assignment: $assignment,
        submissionText: 'Completed company code configuration writeup.',
        uploadedFiles: []
    );

    // Assigned instructor receives new submission notification
    $assignedInstNotifs = $ctx['instructor']->notifications()->where('type', NewAssignmentSubmissionNotification::class)->get();
    expect($assignedInstNotifs)->toHaveCount(1);
    expect($assignedInstNotifs->first()->data['title'])->toBe('New Submission to Review');
    expect($assignedInstNotifs->first()->data['message'])->toContain($ctx['student']->name);

    // Unassigned instructor still receives nothing
    expect($ctx['otherInstructor']->notifications()->count())->toBe(0);
});

it('allows user to view notifications history page with status filtering', function () {
    $ctx = setupNotificationTestContext();

    // Create 1 read and 2 unread notifications for student
    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
    ]);

    $ctx['student']->notify(new EnrollmentApprovedNotification($enrollment));
    $ctx['student']->notify(new EnrollmentApprovedNotification($enrollment));

    $readNotification = $ctx['student']->notifications()->first();
    $readNotification->markAsRead();

    // 1. All notifications
    $responseAll = $this->actingAs($ctx['student'])
        ->get(route('student.notifications.index'));

    $responseAll->assertOk();
    $responseAll->assertSee('Notifications &amp; Activity', false);
    $responseAll->assertSee('Total Updates');

    // 2. Filter unread
    $responseUnread = $this->actingAs($ctx['student'])
        ->get(route('student.notifications.index', ['status' => 'unread']));

    $responseUnread->assertOk();
    $responseUnread->assertViewHas('activeStatus', 'unread');

    // 3. Filter read
    $responseRead = $this->actingAs($ctx['student'])
        ->get(route('student.notifications.index', ['status' => 'read']));

    $responseRead->assertOk();
    $responseRead->assertViewHas('activeStatus', 'read');
});

it('allows user to mark a notification as read and enforces strict ownership', function () {
    $ctx = setupNotificationTestContext();

    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
    ]);

    $ctx['student']->notify(new EnrollmentApprovedNotification($enrollment));
    $notif = $ctx['student']->unreadNotifications()->first();
    expect($notif)->not->toBeNull();

    // Other student tries to mark it as read -> 404 forbidden
    $otherResponse = $this->actingAs($ctx['otherStudent'])
        ->post(route('notifications.mark-read', $notif->id));

    $otherResponse->assertNotFound();
    expect($notif->fresh()->read_at)->toBeNull();

    // Legitimate owner marks it as read
    $ownerResponse = $this->actingAs($ctx['student'])
        ->post(route('notifications.mark-read', $notif->id));

    $ownerResponse->assertRedirect();
    expect($notif->fresh()->read_at)->not->toBeNull();
});

it('allows user to mark all unread notifications as read', function () {
    $ctx = setupNotificationTestContext();

    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
    ]);

    $ctx['student']->notify(new EnrollmentApprovedNotification($enrollment));
    $ctx['student']->notify(new EnrollmentApprovedNotification($enrollment));
    expect($ctx['student']->unreadNotifications()->count())->toBe(2);

    $response = $this->actingAs($ctx['student'])
        ->post(route('notifications.mark-all-read'));

    $response->assertRedirect();
    expect($ctx['student']->unreadNotifications()->count())->toBe(0);
});

it('marks notification as read and safely redirects on click', function () {
    $ctx = setupNotificationTestContext();

    $enrollment = Enrollment::create([
        'user_id' => $ctx['student']->id,
        'course_id' => $ctx['course']->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'web',
    ]);

    $ctx['student']->notify(new EnrollmentApprovedNotification($enrollment));
    $notif = $ctx['student']->unreadNotifications()->first();
    expect($notif->read_at)->toBeNull();

    $response = $this->actingAs($ctx['student'])
        ->get(route('notifications.read-and-redirect', $notif->id));

    $response->assertRedirect(route('student.courses.show', $ctx['course']->slug));
    expect($notif->fresh()->read_at)->not->toBeNull();
});

it('enforces RBAC and guest security across notification portals', function () {
    $ctx = setupNotificationTestContext();

    // Guest redirected to login
    $this->get(route('admin.notifications.index'))->assertRedirect(route('login'));
    $this->get(route('instructor.notifications.index'))->assertRedirect(route('login'));
    $this->get(route('student.notifications.index'))->assertRedirect(route('login'));

    // Student cannot access admin or instructor notifications
    $this->actingAs($ctx['student'])->get(route('admin.notifications.index'))->assertForbidden();
    $this->actingAs($ctx['student'])->get(route('instructor.notifications.index'))->assertForbidden();

    // Instructor cannot access admin notifications
    $this->actingAs($ctx['instructor'])->get(route('admin.notifications.index'))->assertForbidden();

    // Admin can access admin notifications
    $this->actingAs($ctx['admin'])->get(route('admin.notifications.index'))->assertOk();

    // Instructor can access instructor notifications
    $this->actingAs($ctx['instructor'])->get(route('instructor.notifications.index'))->assertOk();

    // Student can access student notifications
    $this->actingAs($ctx['student'])->get(route('student.notifications.index'))->assertOk();
});
