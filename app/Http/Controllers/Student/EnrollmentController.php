<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Submit an enrollment request for an available course via POST form.
     */
    public function store(Request $request, Course $course): RedirectResponse
    {
        return $this->processEnrollment($request->user(), $course);
    }

    /**
     * Submit an enrollment request via GET (for direct CTA clicks or post-login redirection).
     */
    public function create(Request $request, Course $course): RedirectResponse
    {
        return $this->processEnrollment($request->user(), $course);
    }

    /**
     * Core enrollment request handling and duplicate check.
     */
    protected function processEnrollment(?User $user, Course $course): RedirectResponse
    {
        if (! $user) {
            return redirect()->guest(route('login'));
        }

        // 1. Role verification
        if (! $user->isStudent()) {
            abort(403, 'Only students can request course enrollment.');
        }

        // 2. Active status verification
        if (! $user->isActive()) {
            abort(403, 'Suspended student accounts cannot request course enrollment.');
        }

        // 3. Course availability verification
        if (! $course->isAvailableForEnrollment()) {
            return back()->with('error', 'This course is currently not open for new enrollments.');
        }

        // 4. Check for existing enrollment record
        $existing = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            if ($existing->isApproved()) {
                return redirect()->route('student.dashboard')->with('info', 'You already have an approved enrollment in this course.');
            }

            if ($existing->isPending()) {
                return redirect()->route('student.dashboard')->with('info', 'Your enrollment request for this course is already pending admin review.');
            }

            // If previously rejected or cancelled, reopen as pending
            $existing->update([
                'status' => Enrollment::STATUS_PENDING,
                'requested_at' => now(),
                'approved_at' => null,
                'rejected_at' => null,
                'reviewed_by' => null,
                'admin_note' => null,
            ]);

            return redirect()->route('student.dashboard')->with(
                'success',
                "Your enrollment request for {$course->name} has been resubmitted for admin review."
            );
        }

        // 5. Create fresh pending enrollment
        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'status' => Enrollment::STATUS_PENDING,
            'enrollment_source' => 'web',
            'requested_at' => now(),
        ]);

        return redirect()->route('student.dashboard')->with(
            'success',
            "Your enrollment request for {$course->name} has been submitted successfully and is awaiting admin approval."
        );
    }
}
