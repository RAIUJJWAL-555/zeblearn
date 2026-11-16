<?php

namespace App\Notifications;

use App\Models\Payment;

class PaymentFailedNotification extends LmsNotification
{
    public function __construct(public Payment $payment, ?string $reason = null)
    {
        $course = $payment->course;
        $courseName = $course ? $course->name : 'the course';
        $failureReason = $reason ?: $payment->failure_reason;

        $message = "Your payment transaction for '{$courseName}' could not be completed.";
        if ($failureReason) {
            $message .= " Reason: {$failureReason}";
        }

        parent::__construct([
            'title' => 'Payment Failed',
            'message' => $message,
            'url' => $course ? route('student.checkout', $course->slug) : route('student.payments.history'),
            'icon' => 'alert',
            'category' => 'payment',
            'badge_variant' => 'danger',
            'action_label' => 'Retry Payment',
            'metadata' => [
                'payment_id' => $payment->id,
                'course_id' => $payment->course_id,
            ],
        ]);
    }
}
