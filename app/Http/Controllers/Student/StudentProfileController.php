<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\LessonProgressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    public function __construct(
        protected LessonProgressService $progressService
    ) {}

    /**
     * Display student personal profile details.
     */
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        // Basic academic summary for profile sidebar
        $approvedEnrollmentsCount = $user->enrollments()->where('status', Enrollment::STATUS_APPROVED)->count();
        $certificatesCount = $user->certificates()->whereNull('revoked_at')->count();
        $completedLessonsCount = $user->lessonProgress()->where('status', LessonProgress::STATUS_COMPLETED)->count();

        return view('student.profile.show', [
            'user' => $user,
            'approvedEnrollmentsCount' => $approvedEnrollmentsCount,
            'certificatesCount' => $certificatesCount,
            'completedLessonsCount' => $completedLessonsCount,
        ]);
    }

    /**
     * Show form to edit personal student profile details.
     */
    public function edit(Request $request): View
    {
        return view('student.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update permitted personal information and preferences.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'preferences' => ['nullable', 'array'],
            'preferences.email_notifications' => ['nullable', 'boolean'],
            'preferences.course_reminders' => ['nullable', 'boolean'],
            'preferences.marketing_emails' => ['nullable', 'boolean'],
        ]);

        // Handle avatar upload safely
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($user->avatar_path && ! str_starts_with($user->avatar_path, 'http')) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'avatar_'.Str::random(32).'.'.$extension;
            $path = $file->storeAs('avatars', $filename, 'public');

            $user->avatar_path = $path;
        }

        // Format preferences boolean states
        $preferences = [
            'email_notifications' => (bool) ($request->input('preferences.email_notifications', false)),
            'course_reminders' => (bool) ($request->input('preferences.course_reminders', false)),
            'marketing_emails' => (bool) ($request->input('preferences.marketing_emails', false)),
        ];

        // STRICT SECURITY: Explicitly only update permitted attributes (protect role & status)
        $user->name = trim($validated['name']);
        $user->phone = ! empty($validated['phone']) ? trim($validated['phone']) : null;
        $user->bio = ! empty($validated['bio']) ? trim($validated['bio']) : null;
        $user->preferences = $preferences;
        $user->save();

        return redirect()->route('student.profile.show')->with('success', 'Profile information updated successfully.');
    }

    /**
     * Remove profile avatar.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->avatar_path && ! str_starts_with($user->avatar_path, 'http')) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->avatar_path = null;
        $user->save();

        return back()->with('success', 'Profile photo removed.');
    }

    /**
     * Update account password with current-password verification.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Display comprehensive authentic student learning profile.
     */
    public function learningProfile(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $enrollments = $user->enrollments()
            ->with(['course.modules.lessons'])
            ->latest('requested_at')
            ->get();

        $approvedEnrollments = $enrollments->where('status', Enrollment::STATUS_APPROVED);
        $approvedCourses = $approvedEnrollments->pluck('course')->filter();

        // Calculate progress using official service
        $courseProgressMap = $this->progressService->getMultipleCoursesProgress($user, $approvedCourses);

        $totalCompletedLessons = (int) collect($courseProgressMap)->sum('completed');
        $totalCourseLessons = (int) collect($courseProgressMap)->sum('total');
        $overallPercentage = $totalCourseLessons > 0
            ? (int) round(($totalCompletedLessons / $totalCourseLessons) * 100)
            : 0;

        $completedCoursesCount = collect($courseProgressMap)->filter(fn ($p) => $p['is_completed'])->count();
        $activeCoursesCount = collect($courseProgressMap)->filter(fn ($p) => ! $p['is_completed'])->count();

        // Submissions & Academic Activity
        $submissions = $user->assignmentSubmissions()
            ->with(['assignment.course'])
            ->latest('submitted_at')
            ->get();

        $completedAssignmentsCount = $submissions->where('status', AssignmentSubmission::STATUS_GRADED)->count();
        $pendingAssignmentsCount = $submissions->whereIn('status', [
            AssignmentSubmission::STATUS_SUBMITTED,
            AssignmentSubmission::STATUS_UNDER_REVIEW,
        ])->count();
        $resubmissionRequiredCount = $submissions->where('status', AssignmentSubmission::STATUS_RESUBMISSION_REQUIRED)->count();

        // Quiz attempts
        $quizAttempts = $user->quizAttempts()
            ->with(['quiz.course'])
            ->latest('created_at')
            ->get();

        $totalQuizAttempts = $quizAttempts->count();
        $passedQuizAttempts = $quizAttempts->where('is_passed', true)->count();
        $averageQuizScore = $totalQuizAttempts > 0
            ? (int) round($quizAttempts->avg('score_percentage'))
            : 0;

        // Certificates
        $certificates = $user->certificates()
            ->with(['course'])
            ->latest('issued_at')
            ->get();

        return view('student.profile.learning', [
            'user' => $user,
            'enrollments' => $enrollments,
            'approvedCourses' => $approvedCourses,
            'courseProgressMap' => $courseProgressMap,
            'totalCompletedLessons' => $totalCompletedLessons,
            'totalCourseLessons' => $totalCourseLessons,
            'overallPercentage' => min(100, $overallPercentage),
            'completedCoursesCount' => $completedCoursesCount,
            'activeCoursesCount' => $activeCoursesCount,
            'submissions' => $submissions,
            'completedAssignmentsCount' => $completedAssignmentsCount,
            'pendingAssignmentsCount' => $pendingAssignmentsCount,
            'resubmissionRequiredCount' => $resubmissionRequiredCount,
            'quizAttempts' => $quizAttempts,
            'totalQuizAttempts' => $totalQuizAttempts,
            'passedQuizAttempts' => $passedQuizAttempts,
            'averageQuizScore' => $averageQuizScore,
            'certificates' => $certificates,
        ]);
    }
}
