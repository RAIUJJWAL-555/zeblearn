<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\LessonProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    /**
     * Display a paginated listing of courses with search and filters.
     */
    public function index(Request $request): View
    {
        $query = Course::query();

        // 1. Search by name, slug or description
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Category
        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->byCategory($category);
            }
        }

        // 3. Filter by Status
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->byStatus($status);
            }
        }

        $courses = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(12)
            ->withQueryString();

        $categories = $this->getCategories();

        return view('admin.courses.index', [
            'courses' => $courses,
            'categories' => $categories,
            'currentSearch' => $search,
            'currentCategory' => $category ?? 'all',
            'currentStatus' => $status ?? 'all',
            'totalCount' => Course::count(),
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        return view('admin.courses.create', [
            'course' => new Course([
                'status' => 'available',
                'featured' => false,
                'discount_percent' => 60,
                'modules_count' => 12,
                'duration' => '3 Months',
                'delivery_mode' => 'Live Online + Real System Practice',
            ]),
            'categories' => $this->getCategories(),
        ]);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCourse($request);

        // Safe Unique Slug Generation
        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (Course::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Highlights transformation
        $validated['highlights'] = $this->parseHighlights($request->input('highlights'));

        // Published at handling
        $validated['published_at'] = $request->boolean('is_published') ? now() : null;
        $validated['featured'] = $request->boolean('featured');

        // Sync category & category_id
        $this->syncCategoryKeys($validated);

        $course = Course::create($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$course->name}' has been created successfully.");
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => $this->getCategories(),
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $this->validateCourse($request, $course->id);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Highlights transformation
        $validated['highlights'] = $this->parseHighlights($request->input('highlights'));

        // Published at handling
        if ($request->boolean('is_published')) {
            $validated['published_at'] = $course->published_at ?? now();
        } else {
            $validated['published_at'] = null;
        }

        $validated['featured'] = $request->boolean('featured');

        // Sync category & category_id
        $this->syncCategoryKeys($validated);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$course->name}' has been updated successfully.");
    }

    /**
     * Toggle or update course status directly.
     */
    public function toggleStatus(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:available,upcoming,planned'],
        ]);

        $course->update([
            'status' => $validated['status'],
        ]);

        return back()->with('success', "Status for '{$course->name}' has been updated to ".ucfirst($validated['status']).'.');
    }

    /**
     * Remove the specified course from storage safely.
     */
    public function destroy(Course $course): RedirectResponse
    {
        // 1. Dependency check: Core Flagship Course Protection
        if ($course->slug === 'sap-fico-training') {
            return back()->with('error', "Cannot delete the flagship course '{$course->name}' because core marketing routes and SEO bindings depend on it.");
        }

        // 2. Dependency check: Active Enrollments
        if (method_exists($course, 'enrollments') && $course->enrollments()->exists()) {
            return back()->with('error', "Cannot delete '{$course->name}' because students are currently enrolled in it.");
        }

        // 3. Dependency check: Student Progress Records on lessons
        $hasProgress = LessonProgress::whereHas('lesson', function ($lQ) use ($course) {
            $lQ->whereHas('module', function ($mQ) use ($course) {
                $mQ->where('course_id', $course->id);
            });
        })->exists();

        if ($hasProgress) {
            return back()->with('error', "Cannot delete '{$course->name}' because students have active progress records in its curriculum.");
        }

        $name = $course->name;
        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$name}' has been removed successfully.");
    }

    /**
     * Validate course input.
     *
     * @return array<string, mixed>
     */
    protected function validateCourse(Request $request, ?int $courseId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('courses', 'slug')->ignore($courseId)],
            'category' => ['required', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:available,upcoming,planned'],
            'short_description' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'duration' => ['nullable', 'string', 'max:100'],
            'modules_count' => ['nullable', 'integer', 'min:0'],
            'delivery_mode' => ['nullable', 'string', 'max:255'],
            'fee' => ['nullable', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'original_fee' => ['nullable', 'integer', 'min:0'],
            'badge_label' => ['nullable', 'string', 'max:100'],
            'tag_badge' => ['nullable', 'string', 'max:50'],
            'highlights' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
        ]);
    }

    /**
     * Parse highlights textarea into an array.
     *
     * @return list<string>
     */
    protected function parseHighlights(?string $highlightsInput): array
    {
        if (! $highlightsInput) {
            return [];
        }

        $lines = array_map('trim', explode("\n", $highlightsInput));

        return array_values(array_filter($lines));
    }

    /**
     * Synchronize category string and category_id.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function syncCategoryKeys(array &$validated): void
    {
        if (Schema::hasTable('categories')) {
            if (! empty($validated['category_id'])) {
                $cat = Category::find($validated['category_id']);
                if ($cat) {
                    $validated['category'] = $cat->slug;
                }
            } elseif (! empty($validated['category'])) {
                $cat = Category::where('slug', $validated['category'])->first();
                if ($cat) {
                    $validated['category_id'] = $cat->id;
                }
            }
        }
    }

    /**
     * Retrieve available categories.
     */
    protected function getCategories(): Collection
    {
        if (Schema::hasTable('categories')) {
            $dbCategories = Category::orderBy('name')->get();
            if ($dbCategories->isNotEmpty()) {
                return $dbCategories;
            }
        }

        return collect(Course::CATEGORIES)->map(function ($item, $key) {
            return (object) [
                'id' => null,
                'slug' => $key,
                'name' => $item['name'],
            ];
        })->values();
    }
}
