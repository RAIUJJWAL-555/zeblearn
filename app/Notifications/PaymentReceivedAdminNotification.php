<?php

namespace App\Notifications;

use App\Models\Payment;

class PaymentReceivedAdminNotification extends LmsNotification
{
    public function __construct(public Payment $payment)
    {
        $studentName = $payment->user ? $payment->user->name : 'A student';
        $courseName = $payment->course ? $payment->course->name : 'a course';
        $formattedAmount = '₹'.number_format($payment->amount / 100, 2);

        parent::__construct([
            'title' => 'Payment Received',
            'message' => "Payment of {$formattedAmount} received from {$studentName} for '{$courseName}'.",
            'url' => route('admin.payments.show', $payment->id),
            'icon' => 'check-circle',
            'category' => 'payment',
            'badge_variant' => 'success',
            'action_label' => 'View Payment',
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => $payment->user_id,
                'course_id' => $payment->course_id,
                'amount' => $payment->amount,
            ],
        ]);
    }
}
