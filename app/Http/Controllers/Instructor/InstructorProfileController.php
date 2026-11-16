<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InstructorProfileController extends Controller
{
    /**
     * Display instructor faculty profile details.
     */
    public function show(Request $request): View
    {
        /** @var User $instructor */
        $instructor = $request->user();

        $assignedCourses = $instructor->assignedCourses()->withCount(['modules', 'enrollments'])->get();
        $totalCourses = $assignedCourses->count();
        $gradedCount = AssignmentSubmission::where('grader_id', $instructor->id)->count();

        return view('instructor.profile.show', [
            'instructor' => $instructor,
            'assignedCourses' => $assignedCourses,
            'totalCourses' => $totalCourses,
            'gradedCount' => $gradedCount,
        ]);
    }

    /**
     * Show form to edit instructor faculty profile.
     */
    public function edit(Request $request): View
    {
        return view('instructor.profile.edit', [
            'instructor' => $request->user(),
        ]);
    }

    /**
     * Update permitted instructor personal and faculty credentials.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $instructor */
        $instructor = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'designation' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($instructor->avatar_path && ! str_starts_with($instructor->avatar_path, 'http')) {
                Storage::disk('public')->delete($instructor->avatar_path);
            }

            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'instructor_'.Str::random(32).'.'.$extension;
            $path = $file->storeAs('avatars', $filename, 'public');

            $instructor->avatar_path = $path;
        }

        // STRICT SECURITY: Only update permitted faculty attributes (protect role & status)
        $instructor->name = trim($validated['name']);
        $instructor->phone = ! empty($validated['phone']) ? trim($validated['phone']) : null;
        $instructor->bio = ! empty($validated['bio']) ? trim($validated['bio']) : null;
        $instructor->designation = ! empty($validated['designation']) ? trim($validated['designation']) : null;
        $instructor->specialization = ! empty($validated['specialization']) ? trim($validated['specialization']) : null;
        $instructor->experience_years = isset($validated['experience_years']) ? (int) $validated['experience_years'] : null;
        $instructor->qualification = ! empty($validated['qualification']) ? trim($validated['qualification']) : null;
        $instructor->save();

        return redirect()->route('instructor.profile.show')->with('success', 'Faculty profile updated successfully.');
    }

    /**
     * Remove instructor profile photo.
     */
    public function destroyAvatar(Request $request): RedirectResponse
    {
        /** @var User $instructor */
        $instructor = $request->user();

        if ($instructor->avatar_path && ! str_starts_with($instructor->avatar_path, 'http')) {
            Storage::disk('public')->delete($instructor->avatar_path);
        }

        $instructor->avatar_path = null;
        $instructor->save();

        return back()->with('success', 'Profile photo removed.');
    }

    /**
     * Update instructor password with current-password verification.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        /** @var User $instructor */
        $instructor = $request->user();
        $instructor->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
