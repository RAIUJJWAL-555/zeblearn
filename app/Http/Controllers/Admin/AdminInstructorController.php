<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Services\InstructorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminInstructorController extends Controller
{
    public function __construct(
        protected InstructorService $instructorService
    ) {}

    /**
     * Display a paginated, searchable and filterable listing of instructors.
     */
    public function index(Request $request): View
    {
        $query = User::instructors()
            ->withCount('assignedCourses')
            ->latest();

        // Search by keyword
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        // Filter by account status
        if ($status = $request->input('status')) {
            if (in_array($status, [User::STATUS_ACTIVE, User::STATUS_SUSPENDED], true)) {
                $query->where('status', $status);
            }
        }

        $instructors = $query->paginate(12)->withQueryString();

        // Metrics for summary badges
        $totalInstructors = User::instructors()->count();
        $activeInstructors = User::instructors()->where('status', User::STATUS_ACTIVE)->count();
        $suspendedInstructors = User::instructors()->where('status', User::STATUS_SUSPENDED)->count();

        return view('admin.instructors.index', [
            'instructors' => $instructors,
            'totalInstructors' => $totalInstructors,
            'activeInstructors' => $activeInstructors,
            'suspendedInstructors' => $suspendedInstructors,
            'currentSearch' => $request->input('search'),
            'currentStatus' => $request->input('status'),
        ]);
    }

    /**
     * Show form for creating a new instructor account.
     */
    public function create(): View
    {
        return view('admin.instructors.create');
    }

    /**
     * Store a newly created instructor account in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,suspended'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        $instructor = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'avatar_path' => $avatarPath,
        ]);
        $instructor->role = User::ROLE_INSTRUCTOR;
        $instructor->status = $validated['status'];
        $instructor->save();

        return redirect()->route('admin.instructors.show', $instructor)
            ->with('success', "Instructor profile for {$instructor->name} created successfully.");
    }

    /**
     * Display detailed instructor profile, assigned courses, and academic stats.
     */
    public function show(User $instructor): View
    {
        $this->ensureIsInstructor($instructor);

        $instructor->load([
            'assignedCourses.modules',
            'assignedCourses.enrollments' => function ($q) {
                $q->where('status', 'approved');
            },
        ]);

        // Courses available to be assigned to this instructor (not yet assigned)
        $assignedCourseIds = $instructor->assignedCourses->pluck('id');
        $availableCourses = Course::whereNotIn('id', $assignedCourseIds)
            ->orderBy('name')
            ->get();

        $deleteCheck = $this->instructorService->canDeleteInstructor($instructor);

        return view('admin.instructors.show', [
            'instructor' => $instructor,
            'assignedCourses' => $instructor->assignedCourses,
            'availableCourses' => $availableCourses,
            'canDelete' => $deleteCheck['can_delete'],
            'cannotDeleteReason' => $deleteCheck['reason'],
        ]);
    }

    /**
     * Show form for editing an instructor.
     */
    public function edit(User $instructor): View
    {
        $this->ensureIsInstructor($instructor);

        return view('admin.instructors.edit', [
            'instructor' => $instructor,
        ]);
    }

    /**
     * Update an instructor profile.
     */
    public function update(Request $request, User $instructor): RedirectResponse
    {
        $this->ensureIsInstructor($instructor);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($instructor->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'designation' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:active,suspended'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'designation' => $validated['designation'] ?? null,
            'specialization' => $validated['specialization'] ?? null,
            'experience_years' => $validated['experience_years'] ?? null,
            'qualification' => $validated['qualification'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($instructor->avatar_path && Storage::disk('public')->exists($instructor->avatar_path)) {
                Storage::disk('public')->delete($instructor->avatar_path);
            }
            $updateData['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $instructor->fill($updateData);
        $instructor->status = $validated['status'];
        $instructor->save();

        return redirect()->route('admin.instructors.show', $instructor)
            ->with('success', "Instructor profile for {$instructor->name} updated successfully.");
    }

    /**
     * Toggle instructor status between active and suspended.
     */
    public function toggleStatus(User $instructor): RedirectResponse
    {
        $this->ensureIsInstructor($instructor);

        $newStatus = $instructor->isActive() ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE;
        $instructor->status = $newStatus;
        $instructor->save();

        $statusLabel = $newStatus === User::STATUS_ACTIVE ? 'activated' : 'suspended';

        return redirect()->route('admin.instructors.show', $instructor)
            ->with('success', "Instructor {$instructor->name} is now {$statusLabel}.");
    }

    /**
     * Safely delete an instructor account if not tied to academic records.
     */
    public function destroy(User $instructor): RedirectResponse
    {
        $this->ensureIsInstructor($instructor);

        $check = $this->instructorService->canDeleteInstructor($instructor);

        if (! $check['can_delete']) {
            return redirect()->route('admin.instructors.show', $instructor)->with('error', $check['reason']);
        }

        $name = $instructor->name;

        if ($instructor->avatar_path && Storage::disk('public')->exists($instructor->avatar_path)) {
            Storage::disk('public')->delete($instructor->avatar_path);
        }

        $instructor->delete();

        return redirect()->route('admin.instructors.index')
            ->with('success', "Instructor account for {$name} was safely removed.");
    }

    /**
     * Assign a course to an instructor.
     */
    public function assignCourse(Request $request, User $instructor): RedirectResponse
    {
        $this->ensureIsInstructor($instructor);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'role_or_title' => ['nullable', 'string', 'max:100'],
        ]);

        $course = Course::findOrFail($validated['course_id']);

        $this->instructorService->assignInstructor(
            $course,
            $instructor,
            $validated['role_or_title'] ?? 'Lead Instructor'
        );

        return redirect()->route('admin.instructors.show', $instructor)
            ->with('success', "Course '{$course->name}' assigned to {$instructor->name} successfully.");
    }

    /**
     * Unassign a course from an instructor.
     */
    public function unassignCourse(Request $request, User $instructor, Course $course): RedirectResponse
    {
        $this->ensureIsInstructor($instructor);

        $this->instructorService->unassignInstructor($course, $instructor);

        return redirect()->route('admin.instructors.show', $instructor)
            ->with('success', "Course '{$course->name}' unassigned from {$instructor->name}.");
    }

    /**
     * Helper to verify user is an instructor.
     */
    protected function ensureIsInstructor(User $user): void
    {
        if (! $user->isInstructor()) {
            abort(404, 'Instructor record not found.');
        }
    }
}
