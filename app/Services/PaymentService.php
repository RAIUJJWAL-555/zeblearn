<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class PaymentService
{
    /**
     * Create a Razorpay order and a local payment record.
     *
     * @return array{
     *     payment: Payment,
     *     order_id: string,
     *     amount: int,
     *     currency: string,
     *     key_id: string,
     *     course_name: string,
     *     user_name: string,
     *     user_email: string,
     *     user_phone: string
     * }
     */
    public function createOrder(User $user, Course $course): array
    {
        // 1. Verify student eligibility and course availability
        if (! $user->isStudent() || ! $user->isActive()) {
            throw new RuntimeException('Only active students can initiate course payment.');
        }

        if (! $course->isAvailableForEnrollment()) {
            throw new RuntimeException('This course is currently not open for enrollment.');
        }

        // 2. Validate course price server-side using centralized Course pricing logic
        $amountInPaise = $course->getPayableAmountInPaise();

        if ($amountInPaise <= 0) {
            throw new RuntimeException('Invalid course pricing configured.');
        }

        $currency = 'INR';

        // 3. Obtain Razorpay credentials
        $keyId = (string) config('services.razorpay.key');
        $keySecret = (string) config('services.razorpay.secret');

        if ($keyId === '' || $keySecret === '') {
            throw new RuntimeException('Razorpay credentials are not configured on the server.');
        }

        $receipt = 'rcpt_'.substr(md5($user->id.'_'.$course->id.'_'.microtime()), 0, 16);

        // 4. Create order via Razorpay API
        $response = Http::withBasicAuth($keyId, $keySecret)
            ->asJson()
            ->timeout(10)
            ->post('https://api.razorpay.com/v1/orders', [
                'amount' => $amountInPaise,
                'currency' => $currency,
                'receipt' => $receipt,
                'notes' => [
                    'user_id' => (string) $user->id,
                    'course_id' => (string) $course->id,
                    'course_name' => $course->name,
                    'student_email' => $user->email,
                ],
            ]);

        if (! $response->successful()) {
            $errorMessage = $response->json('error.description') ?? 'Failed to communicate with Razorpay.';
            throw new RuntimeException('Payment gateway error: '.$errorMessage);
        }

        $orderData = $response->json();
        $orderId = $orderData['id'];

        // Validate gateway returned matching amount and currency if provided
        if (isset($orderData['amount']) && (int) $orderData['amount'] !== $amountInPaise) {
            throw new RuntimeException('Gateway order pricing mismatch.');
        }

        if (isset($orderData['currency']) && strtoupper((string) $orderData['currency']) !== 'INR') {
            throw new RuntimeException('Gateway order currency mismatch.');
        }

        // 5. Store local payment record inside a database transaction
        $payment = DB::transaction(function () use ($user, $course, $orderId, $amountInPaise, $currency, $receipt, $orderData) {
            $existingEnrollmentId = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->value('id');

            return Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_id' => $existingEnrollmentId,
                'gateway' => 'razorpay',
                'gateway_order_id' => $orderId,
                'amount' => $amountInPaise,
                'currency' => $currency,
                'status' => Payment::STATUS_CREATED,
                'metadata' => [
                    'receipt' => $receipt,
                    'razorpay_order' => $orderData,
                ],
            ]);
        });

        return [
            'payment' => $payment,
            'order_id' => $orderId,
            'amount' => $amountInPaise,
            'currency' => $currency,
            'key_id' => $keyId,
            'course_name' => $course->name,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_phone' => $user->phone ?? '',
        ];
    }

    /**
     * Verify a Razorpay payment signature server-side and automatically approve course enrollment.
     */
    public function verifyPayment(User $user, string $orderId, string $paymentId, string $signature): Payment
    {
        $keyId = (string) config('services.razorpay.key');
        $keySecret = (string) config('services.razorpay.secret');

        if ($keySecret === '' || $keyId === '') {
            throw new RuntimeException('Razorpay credentials are not configured on the server.');
        }

        // 1. Look up local payment record by order ID
        $payment = Payment::where('gateway_order_id', $orderId)->first();

        if (! $payment) {
            throw new InvalidArgumentException('Payment order record not found for this transaction.');
        }

        // 2. Enforce user ownership
        if ($payment->user_id !== $user->id) {
            abort(403, 'Unauthorized payment verification attempt.');
        }

        // 3. Idempotency: if already paid, return safely without duplicate enrollment
        if ($payment->isPaid()) {
            return $payment;
        }

        // 4. Server-side HMAC SHA256 signature verification
        $expectedSignature = hash_hmac('sha256', $orderId.'|'.$paymentId, $keySecret);

        if (! hash_equals($expectedSignature, $signature)) {
            // SECURITY: Do NOT overwrite legitimate payment ID or change status to failed!
            // An invalid browser callback must not sabotage a legitimate pending order.
            throw new InvalidArgumentException('Invalid payment signature verification.');
        }

        // 5. FAIL-CLOSED GATEWAY VERIFICATION: Retrieve payment details through authenticated Razorpay API
        try {
            $response = Http::withBasicAuth($keyId, $keySecret)
                ->timeout(10)
                ->get("https://api.razorpay.com/v1/payments/{$paymentId}");
        } catch (ConnectionException) {
            // Gateway connection timeout / network failure: FAIL-CLOSED. Do not approve enrollment.
            if ($payment->status === Payment::STATUS_CREATED) {
                $payment->update([
                    'status' => Payment::STATUS_PENDING,
                    'gateway_payment_id' => $paymentId,
                    'failure_reason' => 'Gateway connection timed out during verification.',
                ]);
            }

            throw new RuntimeException('Payment gateway connection timed out. Please retry verification shortly or check back once confirmation is received.');
        } catch (\Throwable $e) {
            if ($payment->status === Payment::STATUS_CREATED) {
                $payment->update([
                    'status' => Payment::STATUS_PENDING,
                    'gateway_payment_id' => $paymentId,
                    'failure_reason' => 'Gateway verification temporarily unavailable: '.$e->getMessage(),
                ]);
            }

            throw new RuntimeException('Payment gateway verification is temporarily unavailable. Please retry verification shortly or wait for gateway confirmation.');
        }

        if (! $response->successful()) {
            // Server error / Gateway 5xx: Keep payment pending and retryable. Do not approve enrollment.
            if ($payment->status === Payment::STATUS_CREATED) {
                $payment->update([
                    'status' => Payment::STATUS_PENDING,
                    'gateway_payment_id' => $paymentId,
                    'failure_reason' => 'Gateway returned HTTP status '.$response->status().' during verification.',
                ]);
            }

            throw new RuntimeException('Payment gateway returned an error during verification (Status '.$response->status().'). Please retry shortly.');
        }

        $gatewayPayment = $response->json();

        // Gateway Order ID must match local order
        if (($gatewayPayment['order_id'] ?? '') !== $orderId) {
            throw new InvalidArgumentException('Payment is not associated with this order on gateway.');
        }

        // Gateway Payment ID must match callback payment ID
        if (($gatewayPayment['id'] ?? '') !== $paymentId) {
            throw new InvalidArgumentException('Payment ID mismatch between callback and gateway.');
        }

        // Captured payment amount must match local expected amount in paise
        if ((int) ($gatewayPayment['amount'] ?? 0) !== (int) $payment->amount) {
            throw new InvalidArgumentException('Captured payment amount mismatch.');
        }

        // Currency must match
        if (strtoupper((string) ($gatewayPayment['currency'] ?? '')) !== strtoupper((string) $payment->currency)) {
            throw new InvalidArgumentException('Captured payment currency mismatch.');
        }

        // STEP 2: Strict Captured vs Authorized Check
        $gatewayStatus = $gatewayPayment['status'] ?? '';

        if ($gatewayStatus === 'authorized') {
            $payment->update([
                'status' => Payment::STATUS_PENDING,
                'gateway_payment_id' => $paymentId,
                'failure_reason' => 'Payment authorized but awaiting capture by gateway.',
            ]);

            throw new InvalidArgumentException('Payment is authorized but awaiting capture. Course access will be granted once captured.');
        }

        if ($gatewayStatus !== 'captured') {
            throw new InvalidArgumentException('Payment is not in captured status on gateway (status: '.$gatewayStatus.').');
        }

        $paymentMethod = $gatewayPayment['method'] ?? 'online';

        // 6. Transactional state change with row-level locking
        return DB::transaction(function () use ($payment, $paymentId, $signature, $paymentMethod, $user) {
            $lockedPayment = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if (! $lockedPayment || $lockedPayment->isPaid()) {
                return $lockedPayment ?? $payment;
            }

            $lockedPayment->update([
                'status' => Payment::STATUS_PAID,
                'gateway_payment_id' => $paymentId,
                'gateway_signature' => $signature,
                'payment_method' => $paymentMethod,
                'paid_at' => now(),
                'verified_at' => now(),
                'failure_reason' => null,
            ]);

            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $lockedPayment->course_id)
                ->lockForUpdate()
                ->first();

            if ($enrollment) {
                $enrollment->update([
                    'status' => Enrollment::STATUS_APPROVED,
                    'enrollment_source' => 'online_payment',
                    'approved_at' => now(),
                    'rejected_at' => null,
                    'admin_note' => "Automatically approved via verified Razorpay payment {$paymentId}.",
                ]);
            } else {
                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $lockedPayment->course_id,
                    'status' => Enrollment::STATUS_APPROVED,
                    'enrollment_source' => 'online_payment',
                    'requested_at' => now(),
                    'approved_at' => now(),
                    'admin_note' => "Automatically approved via verified Razorpay payment {$paymentId}.",
                ]);
            }

            $lockedPayment->update(['enrollment_id' => $enrollment->id]);

            return $lockedPayment;
        });
    }

    /**
     * Verify a Razorpay Webhook signature using the webhook secret.
     */
    public function verifyWebhookSignature(string $rawPayload, string $signature): bool
    {
        $webhookSecret = (string) config('services.razorpay.webhook_secret');

        if ($webhookSecret === '' || $signature === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $rawPayload, $webhookSecret);

        return hash_equals($expected, $signature);
    }

    /**
     * Process an incoming verified Razorpay webhook payload idempotently.
     */
    public function processWebhook(array $event): void
    {
        $eventType = $event['event'] ?? '';
        $payload = $event['payload'] ?? [];

        if ($eventType === 'payment.captured') {
            $paymentEntity = $payload['payment']['entity'] ?? [];
            $orderId = $paymentEntity['order_id'] ?? null;
            $paymentId = $paymentEntity['id'] ?? null;
            $method = $paymentEntity['method'] ?? 'online';
            $amount = $paymentEntity['amount'] ?? null;
            $currency = $paymentEntity['currency'] ?? null;
            $status = $paymentEntity['status'] ?? null;

            // Strict capture verification: Only confirmed captured payments can approve enrollment
            if (! $orderId || $status !== 'captured' || ! $paymentId) {
                return;
            }

            DB::transaction(function () use ($orderId, $paymentId, $method, $amount, $currency) {
                $payment = Payment::where('gateway_order_id', $orderId)->lockForUpdate()->first();

                if (! $payment || $payment->isPaid()) {
                    return;
                }

                // Verify captured amount matches expected order amount
                if ($amount !== null && (int) $amount !== (int) $payment->amount) {
                    return;
                }

                // Verify currency matches expected currency
                if (! empty($currency) && strtoupper((string) $currency) !== strtoupper((string) $payment->currency)) {
                    return;
                }

                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'gateway_payment_id' => $paymentId,
                    'payment_method' => $method,
                    'paid_at' => now(),
                    'verified_at' => now(),
                    'failure_reason' => null,
                ]);

                $enrollment = Enrollment::where('user_id', $payment->user_id)
                    ->where('course_id', $payment->course_id)
                    ->lockForUpdate()
                    ->first();

                if ($enrollment) {
                    $enrollment->update([
                        'status' => Enrollment::STATUS_APPROVED,
                        'enrollment_source' => 'online_payment',
                        'approved_at' => now(),
                        'rejected_at' => null,
                        'admin_note' => "Automatically approved via verified Razorpay webhook ({$paymentId}).",
                    ]);
                } else {
                    $enrollment = Enrollment::create([
                        'user_id' => $payment->user_id,
                        'course_id' => $payment->course_id,
                        'status' => Enrollment::STATUS_APPROVED,
                        'enrollment_source' => 'online_payment',
                        'requested_at' => now(),
                        'approved_at' => now(),
                        'admin_note' => "Automatically approved via verified Razorpay webhook ({$paymentId}).",
                    ]);
                }

                $payment->update(['enrollment_id' => $enrollment->id]);
            });
        } elseif ($eventType === 'order.paid') {
            $paymentEntity = $payload['payment']['entity'] ?? [];
            $orderId = $paymentEntity['order_id'] ?? ($payload['order']['entity']['id'] ?? null);
            $paymentId = $paymentEntity['id'] ?? null;
            $method = $paymentEntity['method'] ?? 'online';
            $amount = $paymentEntity['amount'] ?? null;
            $currency = $paymentEntity['currency'] ?? null;
            $status = $paymentEntity['status'] ?? null;

            // STEP 3: order.paid ONLY approves enrollment when the associated payment is confirmed captured!
            if (! $orderId || $status !== 'captured' || ! $paymentId) {
                return;
            }

            DB::transaction(function () use ($orderId, $paymentId, $method, $amount, $currency) {
                $payment = Payment::where('gateway_order_id', $orderId)->lockForUpdate()->first();

                if (! $payment || $payment->isPaid()) {
                    return;
                }

                if ($amount !== null && (int) $amount !== (int) $payment->amount) {
                    return;
                }

                if (! empty($currency) && strtoupper((string) $currency) !== strtoupper((string) $payment->currency)) {
                    return;
                }

                $payment->update([
                    'status' => Payment::STATUS_PAID,
                    'gateway_payment_id' => $paymentId,
                    'payment_method' => $method,
                    'paid_at' => now(),
                    'verified_at' => now(),
                    'failure_reason' => null,
                ]);

                $enrollment = Enrollment::where('user_id', $payment->user_id)
                    ->where('course_id', $payment->course_id)
                    ->lockForUpdate()
                    ->first();

                if ($enrollment) {
                    $enrollment->update([
                        'status' => Enrollment::STATUS_APPROVED,
                        'enrollment_source' => 'online_payment',
                        'approved_at' => now(),
                        'rejected_at' => null,
                        'admin_note' => "Automatically approved via verified Razorpay order webhook ({$paymentId}).",
                    ]);
                } else {
                    $enrollment = Enrollment::create([
                        'user_id' => $payment->user_id,
                        'course_id' => $payment->course_id,
                        'status' => Enrollment::STATUS_APPROVED,
                        'enrollment_source' => 'online_payment',
                        'requested_at' => now(),
                        'approved_at' => now(),
                        'admin_note' => "Automatically approved via verified Razorpay order webhook ({$paymentId}).",
                    ]);
                }

                $payment->update(['enrollment_id' => $enrollment->id]);
            });
        } elseif ($eventType === 'payment.authorized') {
            $paymentEntity = $payload['payment']['entity'] ?? [];
            $orderId = $paymentEntity['order_id'] ?? null;

            if ($orderId) {
                DB::transaction(function () use ($orderId, $paymentEntity) {
                    $payment = Payment::where('gateway_order_id', $orderId)->lockForUpdate()->first();

                    if (! $payment || $payment->isPaid()) {
                        return;
                    }

                    $payment->update([
                        'status' => Payment::STATUS_PENDING,
                        'gateway_payment_id' => $paymentEntity['id'] ?? null,
                        'failure_reason' => 'Payment authorized but awaiting capture by gateway.',
                    ]);
                });
            }
        } elseif ($eventType === 'payment.failed') {
            $paymentEntity = $payload['payment']['entity'] ?? [];
            $orderId = $paymentEntity['order_id'] ?? null;
            $errorDescription = $paymentEntity['error_description'] ?? 'Payment transaction failed.';

            if ($orderId) {
                DB::transaction(function () use ($orderId, $errorDescription) {
                    $payment = Payment::where('gateway_order_id', $orderId)->lockForUpdate()->first();

                    if (! $payment || $payment->isPaid()) {
                        // SECURITY: Never downgrade or overwrite a confirmed paid transaction!
                        return;
                    }

                    $payment->update([
                        'status' => Payment::STATUS_FAILED,
                        'failure_reason' => $errorDescription,
                    ]);
                });
            }
        }
    }
}
