<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminCurriculumController extends Controller
{
    /**
     * Display the course curriculum management console.
     */
    public function index(Course $course): View
    {
        $course->load([
            'modules' => function ($query) {
                $query->orderBy('sort_order', 'asc')->with([
                    'lessons' => function ($lQuery) {
                        $lQuery->orderBy('sort_order', 'asc');
                    },
                ]);
            },
        ]);

        $modules = $course->modules;

        $stats = [
            'total_modules' => $modules->count(),
            'published_modules' => $modules->where('status', 'published')->count(),
            'draft_modules' => $modules->where('status', 'draft')->count(),
            'total_lessons' => $modules->sum(fn ($m) => $m->lessons->count()),
            'published_lessons' => $modules->sum(fn ($m) => $m->lessons->where('status', 'published')->count()),
            'total_duration' => $modules->sum(fn ($m) => $m->lessons->sum('duration_minutes')),
        ];

        return view('admin.courses.curriculum', [
            'course' => $course,
            'modules' => $modules,
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created module for the given course.
     */
    public function storeModule(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:published,draft'],
        ]);

        $nextOrder = (int) ($course->modules()->max('sort_order') ?? 0) + 1;

        $course->modules()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'sort_order' => $nextOrder,
        ]);

        return back()->with('success', 'Curriculum module created successfully.');
    }

    /**
     * Update an existing module.
     */
    public function updateModule(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        abort_if($module->course_id !== $course->id, 404, 'Module not found for this course.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:published,draft'],
        ]);

        $module->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Curriculum module updated successfully.');
    }

    /**
     * Delete a module safely.
     */
    public function destroyModule(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        abort_if($module->course_id !== $course->id, 404, 'Module not found for this course.');

        // Dependency check: Lessons in this module with active student progress records
        $hasProgress = LessonProgress::whereHas('lesson', function ($q) use ($module) {
            $q->where('course_module_id', $module->id);
        })->exists();

        if ($hasProgress) {
            return back()->with(
                'error',
                "Cannot delete module '{$module->title}' because one or more nested lessons contain student learning progress."
            );
        }

        $lessonCount = $module->lessons()->count();

        if ($lessonCount > 0 && ! $request->boolean('force')) {
            return back()->with(
                'error',
                "Module '{$module->title}' contains {$lessonCount} lesson(s). Please delete all nested lessons first, or confirm safe deletion."
            );
        }

        // Clean up nested lessons if force confirmed and no progress records exist
        if ($lessonCount > 0 && $request->boolean('force')) {
            $module->lessons()->delete();
        }

        $module->delete();

        return back()->with('success', 'Curriculum module deleted successfully.');
    }

    /**
     * Reorder modules within a course.
     */
    public function reorderModules(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'module_ids' => ['required', 'array'],
            'module_ids.*' => ['required', 'integer'],
        ]);

        $moduleIds = $validated['module_ids'];

        // Verify all submitted module IDs belong strictly to this course
        $existingCount = $course->modules()->whereIn('id', $moduleIds)->count();

        if ($existingCount !== count(array_unique($moduleIds))) {
            abort(422, 'Invalid module IDs submitted for this course.');
        }

        DB::transaction(function () use ($moduleIds, $course) {
            foreach ($moduleIds as $index => $id) {
                CourseModule::where('id', $id)
                    ->where('course_id', $course->id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('success', 'Module order updated successfully.');
    }

    /**
     * Store a newly created lesson within a module.
     */
    public function storeLesson(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        abort_if($module->course_id !== $course->id, 404, 'Module not found for this course.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_type' => ['required', 'in:text,video,resource'],
            'content' => ['nullable', 'string', 'required_if:content_type,text'],
            'video_url' => ['nullable', 'url', 'max:500', 'required_if:content_type,video'],
            'resource_url' => ['nullable', 'url', 'max:500', 'required_if:content_type,resource'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'status' => ['required', 'in:published,draft'],
        ]);

        $nextOrder = (int) ($module->lessons()->max('sort_order') ?? 0) + 1;

        $module->lessons()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content_type' => $validated['content_type'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'resource_url' => $validated['resource_url'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'status' => $validated['status'],
            'sort_order' => $nextOrder,
        ]);

        return back()->with('success', 'Lesson added to module successfully.');
    }

    /**
     * Update an existing lesson.
     */
    public function updateLesson(Request $request, Course $course, CourseModule $module, Lesson $lesson): RedirectResponse
    {
        abort_if($module->course_id !== $course->id, 404, 'Module not found for this course.');
        abort_if($lesson->course_module_id !== $module->id, 404, 'Lesson not found in this module.');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'content_type' => ['required', 'in:text,video,resource'],
            'content' => ['nullable', 'string', 'required_if:content_type,text'],
            'video_url' => ['nullable', 'url', 'max:500', 'required_if:content_type,video'],
            'resource_url' => ['nullable', 'url', 'max:500', 'required_if:content_type,resource'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'status' => ['required', 'in:published,draft'],
        ]);

        $lesson->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'content_type' => $validated['content_type'],
            'content' => $validated['content'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'resource_url' => $validated['resource_url'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Lesson updated successfully.');
    }

    /**
     * Delete an existing lesson.
     */
    public function destroyLesson(Request $request, Course $course, CourseModule $module, Lesson $lesson): RedirectResponse
    {
        abort_if($module->course_id !== $course->id, 404, 'Module not found for this course.');
        abort_if($lesson->course_module_id !== $module->id, 404, 'Lesson not found in this module.');

        // Prevent deletion if lesson has active student progress records
        if ($lesson->progress()->exists()) {
            return back()->with(
                'error',
                "Cannot delete lesson '{$lesson->title}' because active student learning progress records exist for it."
            );
        }

        $lesson->delete();

        return back()->with('success', 'Lesson deleted successfully.');
    }

    /**
     * Reorder lessons within a module.
     */
    public function reorderLessons(Request $request, Course $course, CourseModule $module): RedirectResponse
    {
        abort_if($module->course_id !== $course->id, 404, 'Module not found for this course.');

        $validated = $request->validate([
            'lesson_ids' => ['required', 'array'],
            'lesson_ids.*' => ['required', 'integer'],
        ]);

        $lessonIds = $validated['lesson_ids'];

        // Verify all submitted lesson IDs belong strictly to this module
        $existingCount = $module->lessons()->whereIn('id', $lessonIds)->count();

        if ($existingCount !== count(array_unique($lessonIds))) {
            abort(422, 'Invalid lesson IDs submitted for this module.');
        }

        DB::transaction(function () use ($lessonIds, $module) {
            foreach ($lessonIds as $index => $id) {
                Lesson::where('id', $id)
                    ->where('course_module_id', $module->id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('success', 'Lesson order updated successfully.');
    }
}
