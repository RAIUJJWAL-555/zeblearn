<?php

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.razorpay.key', 'rzp_test_mock_key');
    config()->set('services.razorpay.secret', 'mock_secret_key_123456');
    config()->set('services.razorpay.webhook_secret', 'mock_webhook_secret_789');
});

test('1. authenticated active student can initiate payment checkout', function () {
    Http::fake([
        'https://api.razorpay.com/v1/orders' => Http::response([
            'id' => 'order_test_101',
            'amount' => 2500000,
            'currency' => 'INR',
        ], 200),
    ]);

    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'SAP FICO Online Course',
        'slug' => 'sap-fico-online-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'FICO training.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.checkout', $course));

    $response->assertOk();
    $response->assertSee('order_test_101');
    $response->assertSee('SAP FICO Online Course');
    $response->assertSee('25,000');

    $this->assertDatabaseHas('payments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway_order_id' => 'order_test_101',
        'amount' => 2500000,
        'status' => Payment::STATUS_CREATED,
    ]);
});

test('2. unauthenticated guest cannot initiate checkout and is redirected', function () {
    $course = Course::create([
        'name' => 'Guest Checkout Course',
        'slug' => 'guest-checkout-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Guest check.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $response = $this->get(route('student.checkout', $course));

    $response->assertRedirect(route('login'));
});

test('3. admin cannot use student checkout', function () {
    $admin = User::factory()->admin()->active()->create();

    $course = Course::create([
        'name' => 'Admin Test Course',
        'slug' => 'admin-test-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Admin test.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $this->actingAs($admin)->get(route('student.checkout', $course))->assertForbidden();
});

test('4. suspended student cannot initiate checkout', function () {
    $suspendedStudent = User::factory()->student()->suspended()->create();

    $course = Course::create([
        'name' => 'Suspended Checkout Course',
        'slug' => 'suspended-checkout-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Suspended test.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $this->actingAs($suspendedStudent)->get(route('student.checkout', $course))->assertForbidden();
});

test('5. course price is calculated server-side from database fee', function () {
    Http::fake([
        'https://api.razorpay.com/v1/orders' => function ($request) {
            $data = $request->data();
            expect($data['amount'])->toBe(4500000); // 45,000 * 100 paise

            return Http::response(['id' => 'order_server_calc_450', 'amount' => 4500000], 200);
        },
    ]);

    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Server Price Test Course',
        'slug' => 'server-price-test-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Fee check.',
        'fee' => 45000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.checkout', $course));

    $response->assertOk();
});

test('6. client cannot manipulate payment amount', function () {
    // There is no client-submitted amount in checkout route; amount is strictly read from Course->fee in DB
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Tamper Test Course',
        'slug' => 'tamper-test-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Tamper test.',
        'fee' => 50000,
        'published_at' => now(),
    ]);

    Http::fake([
        'https://api.razorpay.com/v1/orders' => function ($request) {
            // Even if client attempted passing fake parameters, server calculates 50,000 * 100
            expect($request->data()['amount'])->toBe(5000000);

            return Http::response(['id' => 'order_tamper_safe_500'], 200);
        },
    ]);

    $this->actingAs($student)->get(route('student.checkout', ['course' => $course, 'amount' => 1]))->assertOk();
});

test('7. invalid or unavailable course cannot be checked out', function () {
    $student = User::factory()->student()->active()->create();

    $unavailableCourse = Course::create([
        'name' => 'Upcoming Batch Course',
        'slug' => 'upcoming-batch-course',
        'category' => 'sap-erp',
        'status' => 'upcoming', // not available
        'short_description' => 'Upcoming.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.checkout', $unavailableCourse));

    $response->assertRedirect(route('courses.index'));
    $response->assertSessionHas('error');
});

test('8. payment order creation failure is handled safely without crashing', function () {
    Http::fake([
        'https://api.razorpay.com/v1/orders' => Http::response([
            'error' => ['description' => 'Razorpay internal service unavailable'],
        ], 500),
    ]);

    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Error Handled Course',
        'slug' => 'error-handled-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Error handling.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.checkout', $course));

    $response->assertRedirect(route('courses.index'));
    $response->assertSessionHas('error');
});

test('9. valid signature verification succeeds and approves enrollment', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Verified Signature Course',
        'slug' => 'verified-signature-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Signature test.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $orderId = 'order_valid_999';
    $paymentId = 'pay_valid_888';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured',
            'amount' => 3000000,
            'currency' => 'INR',
            'method' => 'upi',
        ], 200),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.courses.show', $course));
    $response->assertSessionHas('success');

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PAID);
    expect($payment->gateway_payment_id)->toBe($paymentId);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'online_payment',
    ]);
});

test('10. invalid signature is rejected and preserves legitimate pending payment state', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Invalid Sig Course',
        'slug' => 'invalid-sig-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Sig fail.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $orderId = 'order_fake_order_777';
    $paymentId = 'pay_fake_pay_666';
    $bogusSignature = 'tampered_bogus_signature_hash_123';

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $bogusSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    $payment->refresh();
    // Security: pending order is NOT marked failed and payment id is NOT overwritten with attacker input
    expect($payment->status)->toBe(Payment::STATUS_CREATED);
    expect($payment->gateway_payment_id)->toBeNull();

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('11. wrong order ID is rejected', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Wrong Order ID Course',
        'slug' => 'wrong-order-id-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Wrong order.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => 'non_existent_order_id_404',
        'razorpay_payment_id' => 'pay_some_payment_id',
        'razorpay_signature' => 'sig_any',
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');
});

test('12. wrong user cannot verify another users payment', function () {
    $student1 = User::factory()->student()->active()->create();
    $student2 = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Cross User Payment Course',
        'slug' => 'cross-user-payment-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Cross user.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_student1_order';
    $paymentId = 'pay_student1_pay';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, 'mock_secret_key_123456');

    Payment::create([
        'user_id' => $student1->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    // Student 2 attempts to verify Student 1's payment
    $this->actingAs($student2)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ])->assertForbidden();
});

test('13. missing required verification fields are rejected', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Validation Check Course',
        'slug' => 'validation-check-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Validation.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => '',
        'razorpay_payment_id' => '',
        'razorpay_signature' => '',
    ]);

    $response->assertSessionHasErrors(['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature']);
});

test('14. duplicate verification does not create duplicate enrollment', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Duplicate Verification Course',
        'slug' => 'duplicate-verification-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Duplicate test.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $orderId = 'order_dup_test_123';
    $paymentId = 'pay_dup_test_456';
    $sig = hash_hmac('sha256', $orderId.'|'.$paymentId, 'mock_secret_key_123456');

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured',
            'amount' => 3000000,
            'currency' => 'INR',
            'method' => 'card',
        ], 200),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    // First verification
    $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $sig,
    ])->assertRedirect();

    // Second duplicate verification attempt
    $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $sig,
    ])->assertRedirect();

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
    expect(Payment::where('gateway_order_id', $orderId)->count())->toBe(1);
});

test('15. verified payment approves existing pending enrollment', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Pending To Approved Course',
        'slug' => 'pending-to-approved-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Transition test.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    // Student previously had a pending enrollment
    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $orderId = 'order_pending_to_approved';
    $paymentId = 'pay_pending_to_approved';
    $sig = hash_hmac('sha256', $orderId.'|'.$paymentId, 'mock_secret_key_123456');

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured',
            'amount' => 2000000,
            'currency' => 'INR',
            'method' => 'netbanking',
        ], 200),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $sig,
    ]);

    $enrollment->refresh();
    expect($enrollment->status)->toBe(Enrollment::STATUS_APPROVED);
    expect($enrollment->enrollment_source)->toBe('online_payment');
});

test('16. failed payment does not grant course access', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Failed Payment Access Course',
        'slug' => 'failed-payment-access-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Access check.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_failed_001',
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_FAILED,
        'failure_reason' => 'Bank declined card.',
    ]);

    // Attempting to access course learning area is forbidden
    $this->actingAs($student)->get(route('student.courses.show', $course))->assertForbidden();
});

test('17. unverified webhook is rejected with 400', function () {
    $payload = json_encode(['event' => 'payment.captured', 'payload' => []]);

    $response = $this->call(
        'POST',
        route('webhooks.razorpay'),
        [],
        [],
        [],
        ['HTTP_X-Razorpay-Signature' => 'invalid_webhook_sig'],
        $payload
    );

    $response->assertStatus(400);
});

test('18. valid webhook signature is processed and approves enrollment', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Webhook Approved Course',
        'slug' => 'webhook-approved-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Webhook course.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $orderId = 'order_webhook_live_123';
    $paymentId = 'pay_webhook_live_456';

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $event = [
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => 3500000,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'method' => 'upi',
                ],
            ],
        ],
    ];

    $rawPayload = json_encode($event);
    $webhookSecret = 'mock_webhook_secret_789';
    $validWebhookSig = hash_hmac('sha256', $rawPayload, $webhookSecret);

    $response = $this->call(
        'POST',
        route('webhooks.razorpay'),
        [],
        [],
        [],
        ['HTTP_X-Razorpay-Signature' => $validWebhookSig],
        $rawPayload
    );

    $response->assertOk();

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PAID);
    expect($payment->gateway_payment_id)->toBe($paymentId);
    expect($payment->payment_method)->toBe('upi');

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'online_payment',
    ]);
});

test('19. duplicate webhook delivery is handled idempotently', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Idempotent Webhook Course',
        'slug' => 'idempotent-webhook-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Idempotent.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $orderId = 'order_idempotent_999';

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $event = [
        'event' => 'order.paid',
        'payload' => [
            'order' => [
                'entity' => [
                    'id' => $orderId,
                    'amount_paid' => 2000000,
                ],
            ],
            'payment' => [
                'entity' => [
                    'id' => 'pay_idem_001',
                    'order_id' => $orderId,
                    'amount' => 2000000,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'method' => 'card',
                ],
            ],
        ],
    ];

    $rawPayload = json_encode($event);
    $sig = hash_hmac('sha256', $rawPayload, 'mock_webhook_secret_789');

    // Deliver 1
    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $rawPayload)->assertOk();

    // Deliver 2 (duplicate)
    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $rawPayload)->assertOk();

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('20. webhook arriving before checkout verification is safe', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Early Webhook Course',
        'slug' => 'early-webhook-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Order check.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_early_webhook_333';
    $paymentId = 'pay_early_webhook_444';

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    // 1. Webhook arrives first
    $event = [
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => 2500000,
                    'currency' => 'INR',
                    'status' => 'captured',
                ],
            ],
        ],
    ];
    $rawPayload = json_encode($event);
    $sig = hash_hmac('sha256', $rawPayload, 'mock_webhook_secret_789');
    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $rawPayload)->assertOk();

    // 2. Checkout verification callback arrives later
    $checkoutSig = hash_hmac('sha256', $orderId.'|'.$paymentId, 'mock_secret_key_123456');
    $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $checkoutSig,
    ])->assertRedirect(route('student.courses.show', $course));

    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
    expect(Enrollment::where('user_id', $student->id)->first()->status)->toBe(Enrollment::STATUS_APPROVED);
});

test('21. student cannot view another students payment receipt', function () {
    $student1 = User::factory()->student()->active()->create();
    $student2 = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Isolation Receipt Course',
        'slug' => 'isolation-receipt-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Receipt isolation.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $payment = Payment::create([
        'user_id' => $student1->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_isolation_1',
        'gateway_payment_id' => 'pay_isolation_1',
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
    ]);

    $this->actingAs($student2)->get(route('student.payments.receipt', $payment))->assertForbidden();
});

test('22. admin can view payment records and transaction audit details', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create(['name' => 'Vikram Seth']);

    $course = Course::create([
        'name' => 'Admin Payment Audit Course',
        'slug' => 'admin-payment-audit-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Admin payment audit.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_audit_admin_123',
        'gateway_payment_id' => 'pay_audit_admin_456',
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
    ]);

    // Admin index
    $response = $this->actingAs($admin)->get(route('admin.payments.index'));
    $response->assertOk();
    $response->assertSee('Payment Transactions &amp; Audit', false);
    $response->assertSee('Vikram Seth');
    $response->assertSee('30,000');

    // Admin show
    $showResponse = $this->actingAs($admin)->get(route('admin.payments.show', $payment));
    $showResponse->assertOk();
    $showResponse->assertSee('order_audit_admin_123');
    $showResponse->assertSee('pay_audit_admin_456');
});

test('23. student payment history displays their own records accurately', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Student History Course',
        'slug' => 'student-history-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'History check.',
        'fee' => 28000,
        'published_at' => now(),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_history_test_1',
        'gateway_payment_id' => 'pay_history_test_1',
        'amount' => 2800000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.payments.history'));

    $response->assertOk();
    $response->assertSee('Student History Course');
    $response->assertSee('28,000.00');
    $response->assertSee(route('student.payments.receipt', $payment));
});

test('24. existing manual enrollment remains functional without payment record', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Manual Enrollment Course',
        'slug' => 'manual-enrollment-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Manual enrollment.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    // Student requests manual enrollment
    $this->actingAs($student)->post(route('courses.enroll', $course))->assertRedirect(route('student.dashboard'));

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_PENDING,
        'enrollment_source' => 'web',
    ]);

    // Zero payment records created for manual enrollment
    expect(Payment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(0);
});

test('25. existing course access protection remains strictly enforced', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Protected Content Course',
        'slug' => 'protected-content-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Protected.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    // No payment, no approved enrollment -> forbidden
    $this->actingAs($student)->get(route('student.courses.show', $course))->assertForbidden();
});

test('26. invalid callback cannot overwrite a legitimate payment ID', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Overwrite Guard Course',
        'slug' => 'overwrite-guard-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Overwrite guard.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_legit_order_555';
    $legitPaymentId = 'pay_legit_payment_999';

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'gateway_payment_id' => $legitPaymentId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => 'pay_attacker_injected_id_000',
        'razorpay_signature' => 'invalid_tampered_sig_xyz',
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $payment->refresh();
    expect($payment->gateway_payment_id)->toBe($legitPaymentId);
    expect($payment->status)->toBe(Payment::STATUS_CREATED);
});

test('27. gateway payment with mismatched captured amount is rejected', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Amount Check Course',
        'slug' => 'amount-check-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Amount check.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $orderId = 'order_amt_mismatch_1';
    $paymentId = 'pay_amt_mismatch_1';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured',
            'amount' => 100, // Attacker paid 1 Rupee instead of 30,000
            'currency' => 'INR',
        ], 200),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('28. gateway payment with mismatched currency is rejected', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Currency Check Course',
        'slug' => 'currency-check-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Currency check.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $orderId = 'order_curr_mismatch_1';
    $paymentId = 'pay_curr_mismatch_1';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured',
            'amount' => 3000000,
            'currency' => 'USD', // Not INR
        ], 200),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');
});

test('29. gateway payment not in captured or authorized status cannot grant access', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Uncaptured Payment Course',
        'slug' => 'uncaptured-payment-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Uncaptured check.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $orderId = 'order_uncaptured_1';
    $paymentId = 'pay_uncaptured_1';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'failed', // Failed status on gateway
            'amount' => 2000000,
            'currency' => 'INR',
        ], 200),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('30. gateway payment belonging to another order is rejected', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Cross Order Check Course',
        'slug' => 'cross-order-check-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Cross order check.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $orderId = 'order_actual_order_1';
    $paymentId = 'pay_different_order_1';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => 'order_DIFFERENT_order_999', // Belongs to different order
            'status' => 'captured',
            'amount' => 2000000,
            'currency' => 'INR',
        ], 200),
    ]);

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');
});

test('31. failed webhook cannot downgrade a confirmed paid transaction', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Paid Protection Course',
        'slug' => 'paid-protection-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Paid protection.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_confirmed_paid_123';
    $paymentId = 'pay_confirmed_paid_456';

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'gateway_payment_id' => $paymentId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'online_payment',
        'approved_at' => now(),
    ]);

    $payload = [
        'event' => 'payment.failed',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => 'pay_failed_attempt_999',
                    'order_id' => $orderId,
                    'error_description' => 'Customer dropped out',
                ],
            ],
        ],
    ];

    $rawContent = json_encode($payload);
    $signature = hash_hmac('sha256', $rawContent, 'mock_webhook_secret_789');

    $response = $this->call(
        'POST',
        route('webhooks.razorpay'),
        [],
        [],
        [],
        ['HTTP_X_RAZORPAY_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'],
        $rawContent
    );

    $response->assertOk();

    // Invariant: Paid payment status is never overwritten or downgraded to failed
    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PAID);

    $enrollment->refresh();
    expect($enrollment->status)->toBe(Enrollment::STATUS_APPROVED);
});

test('32. admin payment detail view does not expose raw cryptographic signature', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Signature Redaction Course',
        'slug' => 'signature-redaction-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Redaction test.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $rawSecretSignature = 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855';

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_redacted_sig_1',
        'gateway_payment_id' => 'pay_redacted_sig_1',
        'gateway_signature' => $rawSecretSignature,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
        'verified_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.payments.show', $payment));

    $response->assertOk();
    $response->assertSee('Verified (HMAC-SHA256 Match)');
    $response->assertDontSee($rawSecretSignature);
});

test('33. payment model serialization hides gateway_signature and metadata', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Serialization Guard Course',
        'slug' => 'serialization-guard-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Serialization guard.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_serial_1',
        'gateway_payment_id' => 'pay_serial_1',
        'gateway_signature' => 'secret_raw_signature_data',
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'metadata' => ['internal_secret_token' => 'do_not_leak'],
    ]);

    $serialized = $payment->toArray();

    expect($serialized)->not->toHaveKey('gateway_signature');
    expect($serialized)->not->toHaveKey('metadata');
});

test('34. payment receipt does not falsely claim to be a tax invoice', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Receipt Wording Course',
        'slug' => 'receipt-wording-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Receipt wording.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => 'order_receipt_word_1',
        'gateway_payment_id' => 'pay_receipt_word_1',
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_PAID,
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($student)->get(route('student.payments.receipt', $payment));

    $response->assertOk();
    $response->assertSee('Payment Receipt');
    $response->assertDontSee('Tax Invoice');
    $response->assertDontSee('GSTIN:');
    $response->assertDontSee('CGST');
    $response->assertDontSee('SGST');
});

test('35. discounted course calculates correct final payable tuition amount in integer paise', function () {
    $courseWithDiscount = Course::create([
        'name' => 'Discount Calculation Course',
        'slug' => 'discount-calc-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Discount test.',
        'original_fee' => 50000,
        'discount_percent' => 20, // 20% off 50,000 = 40,000 payable
        'published_at' => now(),
    ]);

    expect($courseWithDiscount->getPayableAmountInRupees())->toBe(40000);
    expect($courseWithDiscount->getPayableAmountInPaise())->toBe(4000000);

    $courseWithFixedFee = Course::create([
        'name' => 'Fixed Fee Course',
        'slug' => 'fixed-fee-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Fixed fee test.',
        'fee' => 35000,
        'original_fee' => 70000,
        'discount_percent' => 50,
        'published_at' => now(),
    ]);

    // Explicit fee attribute takes strict precedence
    expect($courseWithFixedFee->getPayableAmountInRupees())->toBe(35000);
    expect($courseWithFixedFee->getPayableAmountInPaise())->toBe(3500000);
});

test('36. valid HMAC with Razorpay API timeout does not grant access and keeps payment pending', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'API Timeout Course',
        'slug' => 'api-timeout-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Timeout check.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_timeout_001';
    $paymentId = 'pay_timeout_001';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    // Simulate Connection Timeout on Razorpay's API
    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => function () {
            throw new ConnectionException('Connection timed out after 10000ms');
        },
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    // Invariant: Fail-closed. Access is NOT granted; payment is NOT marked failed
    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PENDING);
    expect($payment->failure_reason)->toContain('timed out');

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('37. valid HMAC with Razorpay server error does not grant access', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Gateway 500 Course',
        'slug' => 'gateway-500-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Gateway 500.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $orderId = 'order_500_001';
    $paymentId = 'pay_500_001';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'error' => ['description' => 'Internal Server Error'],
        ], 500),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PENDING);

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('38. valid HMAC with Razorpay connection failure does not grant access', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Connection Drop Course',
        'slug' => 'connection-drop-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Connection drop.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $orderId = 'order_drop_001';
    $paymentId = 'pay_drop_001';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => function () {
            throw new RuntimeException('Failed to connect to api.razorpay.com port 443: Connection refused');
        },
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PENDING);

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('39. authorized payment does not approve enrollment', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Authorized Only Course',
        'slug' => 'authorized-only-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Authorized check.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_auth_only_001';
    $paymentId = 'pay_auth_only_001';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    // Gateway returns status: 'authorized' (funds not yet captured)
    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'authorized', // Not captured!
            'amount' => 2500000,
            'currency' => 'INR',
            'method' => 'card',
        ], 200),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.dashboard'));
    $response->assertSessionHas('error');

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PENDING);
    expect($payment->failure_reason)->toContain('authorized but awaiting capture');

    // Crucial: Authorized payments MUST NOT grant course access
    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('40. captured payment approves enrollment successfully', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Captured Access Course',
        'slug' => 'captured-access-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Captured check.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $orderId = 'order_captured_001';
    $paymentId = 'pay_captured_001';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::response([
            'id' => $paymentId,
            'order_id' => $orderId,
            'status' => 'captured', // Confirmed captured!
            'amount' => 3500000,
            'currency' => 'INR',
            'method' => 'netbanking',
        ], 200),
    ]);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $response->assertRedirect(route('student.courses.show', $course));
    $response->assertSessionHas('success');

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PAID);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'enrollment_source' => 'online_payment',
    ]);
});

test('41. payment verification can be retried successfully after temporary gateway failure', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Retryable Verification Course',
        'slug' => 'retryable-verification-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Retryable test.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_retry_101';
    $paymentId = 'pay_retry_101';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    // Sequence: 1st attempt returns 503, 2nd attempt returns captured 200
    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => Http::sequence()
            ->push(['error' => '503 Service Unavailable'], 503)
            ->push([
                'id' => $paymentId,
                'order_id' => $orderId,
                'status' => 'captured',
                'amount' => 2500000,
                'currency' => 'INR',
                'method' => 'upi',
            ], 200),
    ]);

    $firstResponse = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $firstResponse->assertRedirect(route('student.dashboard'));
    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PENDING);

    // 2. Second attempt (Retry): Sequence delivers the 200 captured response
    $secondResponse = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $secondResponse->assertRedirect(route('student.courses.show', $course));
    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PAID);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('42. verified webhook can approve enrollment after earlier API timeout', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Webhook Recovery Course',
        'slug' => 'webhook-recovery-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Recovery test.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    $orderId = 'order_recovery_001';
    $paymentId = 'pay_recovery_001';
    $secret = 'mock_secret_key_123456';
    $validSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2500000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    // 1. Browser callback attempts verification but times out
    Http::fake([
        "https://api.razorpay.com/v1/payments/{$paymentId}" => function () {
            throw new ConnectionException('Timed out');
        },
    ]);

    $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => $orderId,
        'razorpay_payment_id' => $paymentId,
        'razorpay_signature' => $validSignature,
    ]);

    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PENDING);
    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);

    // 2. Verified Razorpay webhook arrives asynchronously
    $event = [
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => 2500000,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'method' => 'card',
                ],
            ],
        ],
    ];

    $raw = json_encode($event);
    $sig = hash_hmac('sha256', $raw, 'mock_webhook_secret_789');

    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $raw)->assertOk();

    // Payment and enrollment are now confirmed and approved
    $payment->refresh();
    expect($payment->status)->toBe(Payment::STATUS_PAID);

    $this->assertDatabaseHas('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('43. order.paid without verified captured payment cannot grant access', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Order Paid Uncaptured Course',
        'slug' => 'order-paid-uncaptured-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Uncaptured check.',
        'fee' => 30000,
        'published_at' => now(),
    ]);

    $orderId = 'order_paid_uncaptured_1';

    $payment = Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 3000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    // Webhook order.paid payload with NO captured status or authorized-only status
    $event = [
        'event' => 'order.paid',
        'payload' => [
            'order' => [
                'entity' => [
                    'id' => $orderId,
                    'amount_paid' => 3000000,
                ],
            ],
            'payment' => [
                'entity' => [
                    'id' => 'pay_uncaptured_1',
                    'order_id' => $orderId,
                    'amount' => 3000000,
                    'currency' => 'INR',
                    'status' => 'authorized', // Not captured!
                ],
            ],
        ],
    ];

    $raw = json_encode($event);
    $sig = hash_hmac('sha256', $raw, 'mock_webhook_secret_789');

    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $raw)->assertOk();

    // Access is NOT granted
    $payment->refresh();
    expect($payment->status)->not->toBe(Payment::STATUS_PAID);

    $this->assertDatabaseMissing('enrollments', [
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
    ]);
});

test('44. duplicate captured webhook remains strictly idempotent', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Duplicate Captured Webhook Course',
        'slug' => 'duplicate-captured-webhook-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Dup check.',
        'fee' => 20000,
        'published_at' => now(),
    ]);

    $orderId = 'order_dup_captured_999';
    $paymentId = 'pay_dup_captured_999';

    Payment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'gateway' => 'razorpay',
        'gateway_order_id' => $orderId,
        'amount' => 2000000,
        'currency' => 'INR',
        'status' => Payment::STATUS_CREATED,
    ]);

    $event = [
        'event' => 'payment.captured',
        'payload' => [
            'payment' => [
                'entity' => [
                    'id' => $paymentId,
                    'order_id' => $orderId,
                    'amount' => 2000000,
                    'currency' => 'INR',
                    'status' => 'captured',
                    'method' => 'upi',
                ],
            ],
        ],
    ];

    $raw = json_encode($event);
    $sig = hash_hmac('sha256', $raw, 'mock_webhook_secret_789');

    // Deliver first time
    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $raw)->assertOk();

    // Deliver second time (duplicate)
    $this->call('POST', route('webhooks.razorpay'), [], [], [], ['HTTP_X-Razorpay-Signature' => $sig], $raw)->assertOk();

    // Invariant: Exactly 1 payment, exactly 1 enrollment
    expect(Payment::where('gateway_order_id', $orderId)->count())->toBe(1);
    expect(Enrollment::where('user_id', $student->id)->where('course_id', $course->id)->count())->toBe(1);
});

test('45. rate limiting is enforced on payment verification endpoint', function () {
    $student = User::factory()->student()->active()->create();

    $course = Course::create([
        'name' => 'Rate Limited Verification Course',
        'slug' => 'rate-limited-verification-course',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Rate limit.',
        'fee' => 25000,
        'published_at' => now(),
    ]);

    // Send 10 verification requests within 1 minute
    for ($i = 0; $i < 10; $i++) {
        $this->actingAs($student)->post(route('student.checkout.verify', $course), [
            'razorpay_order_id' => 'order_rate_'.$i,
            'razorpay_payment_id' => 'pay_rate_'.$i,
            'razorpay_signature' => 'sig_rate_'.$i,
        ]);
    }

    // The 11th request must be throttled with HTTP 429 Too Many Requests
    $response = $this->actingAs($student)->post(route('student.checkout.verify', $course), [
        'razorpay_order_id' => 'order_rate_11',
        'razorpay_payment_id' => 'pay_rate_11',
        'razorpay_signature' => 'sig_rate_11',
    ]);

    $response->assertStatus(429);
});
