<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Display the Razorpay checkout page for a selected course.
     */
    public function checkout(Request $request, Course $course): View|RedirectResponse
    {
        $user = $request->user();

        // 1. Role and account active checks
        if (! $user->isStudent()) {
            abort(403, 'Only students can access checkout.');
        }

        if (! $user->isActive()) {
            abort(403, 'Suspended students cannot initiate payments.');
        }

        // 2. Prevent double payment if student already has an approved enrollment
        if ($user->hasApprovedEnrollment($course)) {
            return redirect()->route('student.courses.show', $course)->with(
                'info',
                'You already have full active access to this course.'
            );
        }

        // 3. Create server-side Razorpay order
        try {
            $orderData = $this->paymentService->createOrder($user, $course);
        } catch (Throwable $e) {
            return redirect()->route('courses.index')->with(
                'error',
                $e->getMessage()
            );
        }

        return view('student.checkout', [
            'course' => $course,
            'orderData' => $orderData,
        ]);
    }

    /**
     * Verify payment signature submitted by Razorpay checkout callback.
     */
    public function verify(Request $request, Course $course): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isStudent() || ! $user->isActive()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        try {
            $this->paymentService->verifyPayment(
                $user,
                $validated['razorpay_order_id'],
                $validated['razorpay_payment_id'],
                $validated['razorpay_signature']
            );

            return redirect()->route('student.courses.show', $course)->with(
                'success',
                "Payment verified successfully! Welcome to {$course->name}."
            );
        } catch (HttpExceptionInterface $e) {
            throw $e;
        } catch (Throwable $e) {
            return redirect()->route('student.dashboard')->with(
                'error',
                'Payment verification failed: '.$e->getMessage()
            );
        }
    }

    /**
     * Display student's own verified payment transaction history.
     */
    public function history(Request $request): View
    {
        $user = $request->user();

        $payments = $user->payments()
            ->with(['course', 'enrollment'])
            ->latest()
            ->paginate(10);

        return view('student.payments.history', [
            'payments' => $payments,
        ]);
    }

    /**
     * View a verifiable payment receipt.
     */
    public function receipt(Request $request, Payment $payment): View
    {
        $user = $request->user();

        if ($payment->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this payment receipt.');
        }

        $payment->load(['course', 'enrollment']);

        return view('student.payments.receipt', [
            'payment' => $payment,
            'course' => $payment->course,
            'user' => $user,
        ]);
    }
}
