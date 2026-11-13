<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;

// Helper to create an available course
function createTestCourse(array $attributes = []): Course
{
    return Course::create(array_merge([
        'name' => 'SAP S/4HANA Finance Testing Course',
        'slug' => 'sap-s4hana-finance-test-'.uniqid(),
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Hands-on SAP S/4HANA Finance.',
        'fee' => 30000,
        'published_at' => now(),
    ], $attributes));
}

// 1. Authenticated student can submit an enrollment request
test('1. authenticated student can submit an enrollment request', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('success');

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists())->toBeTrue();
});

// 2. Enrollment request is stored against the correct student and course
test('2. enrollment request is stored against the correct student and course', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    $this->actingAs($student)->post(route('courses.enroll', $course));

    $enrollment = Enrollment::where('user_id', $student->id)
        ->where('course_id', $course->id)
        ->first();

    expect($enrollment)->not->toBeNull();
    expect($enrollment->user_id)->toBe($student->id);
    expect($enrollment->course_id)->toBe($course->id);
    expect($enrollment->status)->toBe(Enrollment::STATUS_PENDING);
    expect($enrollment->enrollment_source)->toBe('web');
});

// 3. Student does not get redirected to /book-demo
test('3. student enrollment action does not redirect to /book-demo', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    // 1. Before enrolling: Catalog and detail pages show actual enrollment action instead of /book-demo
    $catalogResponse = $this->actingAs($student)->get(route('courses.index'));
    $catalogResponse->assertOk();
    $catalogResponse->assertSee(route('courses.enroll', $course));

    $detailResponse = $this->actingAs($student)->get(route('courses.show', $course));
    $detailResponse->assertOk();
    $detailResponse->assertSee(route('courses.enroll', $course));

    // 2. Submitting enrollment: Must redirect to student dashboard, NOT /book-demo
    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect(route('student.dashboard'));
    expect($response->headers->get('Location'))->not->toContain('/book-demo');

    // 3. After submitting: Course views show Awaiting Approval state instead of Enroll Now or /book-demo
    $updatedDetail = $this->actingAs($student)->get(route('courses.show', $course));
    $updatedDetail->assertOk();
    $updatedDetail->assertSee('Awaiting Approval');
});

// 4. Public Book Demo route continues working
test('4. public book demo route continues working exactly as before', function () {
    // 1. Book demo page loads
    $response = $this->get('/book-demo');
    $response->assertOk();
    $response->assertSee('Book a Free Demo');

    // 2. Book demo with course parameter
    $courseResponse = $this->get('/book-demo?course=sap-fico');
    $courseResponse->assertOk();

    // 3. Thank you page
    $thanksResponse = $this->get('/book-demo/thank-you');
    $thanksResponse->assertOk();
});

// 5. Unauthenticated enrollment attempts follow login redirection and preserve destination
test('5. unauthenticated enrollment attempts follow login redirection', function () {
    $course = createTestCourse();

    // POST request redirects to login
    $postResponse = $this->post(route('courses.enroll', $course));
    $postResponse->assertRedirect(route('login'));

    // GET request redirects to login and remembers intended route
    $getResponse = $this->get(route('courses.enroll.get', $course));
    $getResponse->assertRedirect(route('login'));

    // After logging in as student, intended redirection proceeds to enrollment
    $student = User::factory()->student()->active()->create(['password' => bcrypt('Secret123!')]);
    $loginResponse = $this->post('/login', [
        'email' => $student->email,
        'password' => 'Secret123!',
    ]);

    $loginResponse->assertRedirect(route('courses.enroll.get', $course));

    // Accessing the intended route executes the enrollment
    $enrollResponse = $this->actingAs($student)->get(route('courses.enroll.get', $course));
    $enrollResponse->assertRedirect(route('student.dashboard'));
    $enrollResponse->assertSessionHas('success');

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->exists())->toBeTrue();
});

// 6. Duplicate enrollment requests are prevented
test('6. duplicate enrollment requests are prevented for same student and course', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    // First request
    $this->actingAs($student)->post(route('courses.enroll', $course));
    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);

    // Second request while pending
    $secondResponse = $this->actingAs($student)->post(route('courses.enroll', $course));
    $secondResponse->assertRedirect(route('student.dashboard'));
    $secondResponse->assertSessionHas('info', 'Your enrollment request for this course is already pending admin review.');
    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);

    // Third request after approved
    Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->update([
        'status' => Enrollment::STATUS_APPROVED,
        'approved_at' => now(),
    ]);

    $thirdResponse = $this->actingAs($student)->post(route('courses.enroll', $course));
    $thirdResponse->assertRedirect(route('student.dashboard'));
    $thirdResponse->assertSessionHas('info', 'You already have an approved enrollment in this course.');
    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
});

// 7. Pending enrollment does not grant unauthorized course access
test('7. pending enrollment does not grant unauthorized course access', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    // Student has pending enrollment
    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    // Student learning area must remain forbidden (403)
    $this->actingAs($student)
        ->get(route('student.courses.show', $course))
        ->assertForbidden();
});

// 8. Approved enrollment follows existing access rules
test('8. approved enrollment follows existing access rules and grants course access', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    // Student has approved enrollment
    Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'approved_at' => now(),
    ]);

    // Student learning area is accessible (200 OK)
    $this->actingAs($student)
        ->get(route('student.courses.show', $course))
        ->assertOk();

    // Detail page shows "Continue Learning"
    $detailResponse = $this->actingAs($student)->get(route('courses.show', $course));
    $detailResponse->assertOk();
    $detailResponse->assertSee('Continue Learning');
});

// 9. Paid courses respect existing Razorpay verification requirements
test('9. paid courses respect existing Razorpay verification requirements', function () {
    config([
        'services.razorpay.key' => 'rzp_test_mock_key_123',
        'services.razorpay.secret' => 'rzp_test_mock_secret_abc',
    ]);

    $student = User::factory()->student()->active()->create();
    $course = createTestCourse(['fee' => 25000]);

    // An unverified fake signature attempt must fail and not grant course access
    $fakeOrderId = 'order_fake_999999';
    $fakePaymentId = 'pay_fake_888888';
    $tamperedSignature = 'tampered_invalid_signature';

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $fakeOrderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $fakeOrderId,
        'razorpay_payment_id' => $fakePaymentId,
        'razorpay_signature' => $tamperedSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    // Access to learning area remains forbidden
    $this->actingAs($student)
        ->get(route('student.courses.show', $course))
        ->assertForbidden();
});

// 10. Admin can see the newly created enrollment request
test('10. admin can see the newly created enrollment request in /admin/enrollments', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create(['name' => 'Aditi Sen']);
    $course = createTestCourse(['name' => 'SAP Ariba Procurement Cloud']);

    // Student submits request
    $this->actingAs($student)->post(route('courses.enroll', $course));

    // Admin views enrollment console
    $response = $this->actingAs($admin)->get(route('admin.enrollments.index'));

    $response->assertOk();
    $response->assertSee('Aditi Sen');
    $response->assertSee('SAP Ariba Procurement Cloud');
    $response->assertSee('Pending Review');
});

// 11. Existing approval and rejection workflows remain functional
test('11. existing admin approval and rejection workflows remain functional', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    // Create pending enrollment
    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    // Admin approves enrollment
    $approveResponse = $this->actingAs($admin)->from(route('admin.enrollments.index'))->patch(route('admin.enrollments.approve', $enrollment), [
        'admin_note' => 'Approved after profile verification.',
    ]);

    $approveResponse->assertRedirect(route('admin.enrollments.index'));
    expect($enrollment->fresh()->status)->toBe(Enrollment::STATUS_APPROVED);
    expect($enrollment->fresh()->reviewed_by)->toBe($admin->id);

    // Course access is now granted
    $this->actingAs($student)->get(route('student.courses.show', $course))->assertOk();

    // Admin can subsequently reject or cancel
    $cancelResponse = $this->actingAs($admin)->from(route('admin.enrollments.index'))->patch(route('admin.enrollments.cancel', $enrollment), [
        'admin_note' => 'Student requested cancellation.',
    ]);

    $cancelResponse->assertRedirect(route('admin.enrollments.index'));
    expect($enrollment->fresh()->status)->toBe(Enrollment::STATUS_CANCELLED);

    // Course access revoked
    $this->actingAs($student)->get(route('student.courses.show', $course))->assertForbidden();
});

// 12. Existing enrollment records are not duplicated or destroyed
test('12. existing enrollment records are not duplicated or destroyed', function () {
    $student = User::factory()->student()->active()->create();
    $course = createTestCourse();

    $originalEnrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_REJECTED,
        'requested_at' => now()->subDays(5),
        'rejected_at' => now()->subDays(4),
        'admin_note' => 'Incomplete prerequisite documentation.',
    ]);

    $originalId = $originalEnrollment->id;

    // Student re-applies for enrollment
    $response = $this->actingAs($student)->post(route('courses.enroll', $course));

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('success');

    // Total enrollment records for this student and course must remain exactly 1 (reopened, NOT duplicated)
    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);

    $reopened = Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->first();
    expect($reopened->id)->toBe($originalId);
    expect($reopened->status)->toBe(Enrollment::STATUS_PENDING);
});
