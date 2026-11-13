<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminEnrollmentController extends Controller
{
    /**
     * Display a paginated listing of student enrollments with search and filters.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', 'all');
        $courseId = $request->query('course_id', 'all');

        $query = Enrollment::query()->with(['user', 'course', 'reviewer']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uQ) use ($search) {
                    $uQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('course', function ($cQ) use ($search) {
                    $cQ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            });
        }

        if ($status !== 'all' && in_array($status, Enrollment::STATUSES, true)) {
            $query->where('status', $status);
        }

        if ($courseId !== 'all' && is_numeric($courseId)) {
            $query->where('course_id', (int) $courseId);
        }

        $enrollments = $query
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'approved' THEN 2 ELSE 3 END")
            ->latest('requested_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Enrollment::count(),
            'pending' => Enrollment::where('status', Enrollment::STATUS_PENDING)->count(),
            'approved' => Enrollment::where('status', Enrollment::STATUS_APPROVED)->count(),
            'rejected' => Enrollment::where('status', Enrollment::STATUS_REJECTED)->count(),
            'cancelled' => Enrollment::where('status', Enrollment::STATUS_CANCELLED)->count(),
        ];

        $courses = Course::orderBy('name', 'asc')->get(['id', 'name']);

        return view('admin.enrollments.index', [
            'enrollments' => $enrollments,
            'stats' => $stats,
            'courses' => $courses,
            'activeStatus' => $status,
            'activeCourse' => $courseId,
            'search' => $search,
        ]);
    }

    /**
     * Display full details of a specific enrollment request.
     */
    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['user', 'course.modules.lessons', 'reviewer']);

        return view('admin.enrollments.show', [
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Approve a student enrollment request and grant course access.
     */
    public function approve(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $enrollment) {
            $enrollment->update([
                'status' => Enrollment::STATUS_APPROVED,
                'approved_at' => now(),
                'rejected_at' => null,
                'reviewed_by' => auth()->id(),
                'admin_note' => $validated['admin_note'] ?? $enrollment->admin_note,
            ]);
        });

        return back()->with(
            'success',
            "Enrollment for {$enrollment->user->name} in {$enrollment->course->name} approved. Course access is now granted."
        );
    }

    /**
     * Reject a student enrollment request.
     */
    public function reject(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $enrollment) {
            $enrollment->update([
                'status' => Enrollment::STATUS_REJECTED,
                'rejected_at' => now(),
                'reviewed_by' => auth()->id(),
                'admin_note' => $validated['admin_note'] ?? 'Enrollment request declined by admissions team.',
            ]);
        });

        return back()->with(
            'success',
            "Enrollment for {$enrollment->user->name} was rejected."
        );
    }

    /**
     * Safely cancel an existing enrollment.
     */
    public function cancel(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated, $enrollment) {
            $enrollment->update([
                'status' => Enrollment::STATUS_CANCELLED,
                'reviewed_by' => auth()->id(),
                'admin_note' => $validated['admin_note'] ?? 'Enrollment cancelled by administration.',
            ]);
        });

        return back()->with(
            'success',
            "Enrollment for {$enrollment->user->name} has been cancelled."
        );
    }
}
