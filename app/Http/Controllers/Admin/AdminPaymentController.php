<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends Controller
{
    /**
     * Display a listing of all payment transactions with filters and search.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');
        $courseId = $request->query('course_id', 'all');

        $query = Payment::query()->with(['user', 'course', 'enrollment']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('gateway_order_id', 'like', "%{$search}%")
                    ->orWhere('gateway_payment_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uQ) use ($search) {
                        $uQ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('course', function ($cQ) use ($search) {
                        $cQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== 'all' && in_array($status, Payment::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($courseId !== 'all' && is_numeric($courseId)) {
            $query->where('course_id', (int) $courseId);
        }

        $payments = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => Payment::count(),
            'paid_count' => Payment::where('status', Payment::STATUS_PAID)->count(),
            'paid_sum_rupees' => (float) (Payment::where('status', Payment::STATUS_PAID)->sum('amount') / 100),
            'failed_count' => Payment::where('status', Payment::STATUS_FAILED)->count(),
        ];

        $courses = Course::orderBy('name', 'asc')->get(['id', 'name']);

        return view('admin.payments.index', [
            'payments' => $payments,
            'stats' => $stats,
            'courses' => $courses,
            'activeStatus' => $status,
            'activeCourse' => $courseId,
            'search' => $search,
        ]);
    }

    /**
     * Display detailed transaction audit data for a single payment.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['user', 'course.modules', 'enrollment']);

        return view('admin.payments.show', [
            'payment' => $payment,
        ]);
    }
}
