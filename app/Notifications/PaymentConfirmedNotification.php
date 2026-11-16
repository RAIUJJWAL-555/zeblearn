<?php

namespace App\Notifications;

use App\Models\Payment;

class PaymentConfirmedNotification extends LmsNotification
{
    public function __construct(public Payment $payment)
    {
        $courseName = $payment->course ? $payment->course->name : 'Course';
        $formattedAmount = '₹'.number_format($payment->amount / 100, 2);

        parent::__construct([
            'title' => 'Payment Successful',
            'message' => "Your payment of {$formattedAmount} for '{$courseName}' was successfully verified and confirmed.",
            'url' => route('student.payments.receipt', $payment->id),
            'icon' => 'check-circle',
            'category' => 'payment',
            'badge_variant' => 'success',
            'action_label' => 'View Receipt',
            'metadata' => [
                'payment_id' => $payment->id,
                'course_id' => $payment->course_id,
                'amount' => $payment->amount,
            ],
        ]);
    }
}
