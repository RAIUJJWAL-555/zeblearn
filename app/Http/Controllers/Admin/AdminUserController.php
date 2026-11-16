<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a paginated directory of platform users with search and filters.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = (string) $request->query('role', 'all');
        $status = (string) $request->query('status', 'all');

        $query = User::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role !== 'all' && in_array($role, User::ROLES, true)) {
            $query->where('role', $role);
        }

        if ($status !== 'all' && in_array($status, User::STATUSES, true)) {
            $query->where('status', $status);
        }

        $users = $query->latest('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'students' => User::where('role', User::ROLE_STUDENT)->count(),
            'instructors' => User::where('role', User::ROLE_INSTRUCTOR)->count(),
            'admins' => User::where('role', User::ROLE_ADMIN)->count(),
            'active' => User::where('status', User::STATUS_ACTIVE)->count(),
            'suspended' => User::where('status', User::STATUS_SUSPENDED)->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'activeRole' => $role,
            'activeStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Display comprehensive details and academic/financial history for a user.
     */
    public function show(User $user): View
    {
        $user->load([
            'enrollments.course',
            'payments.course',
            'certificates.course',
            'assignedCourses',
        ]);

        $quizAttempts = $user->quizAttempts()->with('quiz.course')->latest()->take(10)->get();
        $assignmentSubmissions = $user->assignmentSubmissions()->with('assignment.course')->latest('submitted_at')->take(10)->get();
        $gradedCount = AssignmentSubmission::where('grader_id', $user->id)->count();

        return view('admin.users.show', [
            'user' => $user,
            'quizAttempts' => $quizAttempts,
            'assignmentSubmissions' => $assignmentSubmissions,
            'gradedCount' => $gradedCount,
        ]);
    }

    /**
     * Show form to edit user account details.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update user profile, role, status, and faculty credentials.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'status' => ['required', 'string', Rule::in(User::STATUSES)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'designation' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        // Self-protection: Admin cannot demote their own account or suspend themselves
        if ((int) $request->user()->id === (int) $user->id) {
            if ($validated['role'] !== User::ROLE_ADMIN) {
                return back()->withInput()->with('error', 'You cannot remove administrative privileges from your own account.');
            }
            if ($validated['status'] !== User::STATUS_ACTIVE) {
                return back()->withInput()->with('error', 'You cannot suspend your own administrative account.');
            }
        }

        // Handle avatar upload safely
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($user->avatar_path && ! str_starts_with($user->avatar_path, 'http')) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'user_'.Str::random(32).'.'.$extension;
            $path = $file->storeAs('avatars', $filename, 'public');

            $user->avatar_path = $path;
        }

        // Apply attributes explicitly
        $user->name = trim($validated['name']);
        $user->email = trim($validated['email']);
        $user->phone = ! empty($validated['phone']) ? trim($validated['phone']) : null;
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        $user->bio = ! empty($validated['bio']) ? trim($validated['bio']) : null;
        $user->designation = ! empty($validated['designation']) ? trim($validated['designation']) : null;
        $user->specialization = ! empty($validated['specialization']) ? trim($validated['specialization']) : null;
        $user->experience_years = isset($validated['experience_years']) ? (int) $validated['experience_years'] : null;
        $user->qualification = ! empty($validated['qualification']) ? trim($validated['qualification']) : null;
        $user->save();

        return redirect()->route('admin.users.show', $user)->with('success', "User account for {$user->name} was updated successfully.");
    }

    /**
     * Toggle a user account between active and suspended.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('error', 'You cannot suspend your own administrative account.');
        }

        $newStatus = $user->isActive() ? User::STATUS_SUSPENDED : User::STATUS_ACTIVE;
        $user->status = $newStatus;
        $user->save();

        $actionText = $newStatus === User::STATUS_ACTIVE ? 'activated' : 'suspended';

        return back()->with('success', "User {$user->name} has been {$actionText}.");
    }

    /**
     * Delete an unreferenced user account safely.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('error', 'You cannot delete your own administrative account.');
        }

        $canDeleteResult = $user->canDelete();
        if (! $canDeleteResult['can_delete']) {
            return back()->with('error', $canDeleteResult['reason']);
        }

        $userName = $user->name;

        if ($user->avatar_path && ! str_starts_with($user->avatar_path, 'http')) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User account {$userName} was permanently deleted.");
    }
}
