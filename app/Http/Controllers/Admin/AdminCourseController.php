<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CourseCmsService;
use App\Services\TrustProofService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCourseController extends Controller
{
    public function __construct(
        protected CourseCmsService $cmsService
    ) {}

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
        $availableInstructors = User::whereIn('role', ['instructor', 'admin'])->orderBy('name')->get();

        return view('admin.courses.create', [
            'course' => new Course([
                'status' => 'available',
                'featured' => false,
                'discount_percent' => 60,
                'modules_count' => 12,
                'duration' => '3 Months',
                'delivery_mode' => 'Live Online + Real System Practice',
                'robots_directive' => 'index, follow',
            ]),
            'categories' => $this->getCategories(),
            'availableInstructors' => $availableInstructors,
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

        // Harmonize & Sanitize Pricing
        if (! empty($validated['discount']) && empty($validated['discount_percent'])) {
            $validated['discount_percent'] = (int) filter_var($validated['discount'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (! empty($validated['overview']) && empty($validated['description'])) {
            $validated['description'] = $validated['overview'];
        }
        $validated = $this->cmsService->harmonizePricing($validated);

        // Highlights & Outcomes transformation
        $validated['highlights'] = $this->parseList($request->input('highlights'));
        $validated['learning_outcomes'] = $this->parseList($request->input('learning_outcomes'));
        $validated['skills_covered'] = $this->parseList($request->input('skills_covered'));
        $validated['certification_skills'] = $this->parseList($request->input('certification_skills'));
        $validated['certification_benefits'] = $this->parseList($request->input('certification_benefits'));

        // Handle section configuration
        if ($request->has('section_config') && is_array($request->input('section_config'))) {
            $validated['section_config'] = $request->input('section_config');
        }

        // Handle trust & proof configuration
        if ($request->has('trust_config') && is_array($request->input('trust_config'))) {
            $validated['trust_config'] = $request->input('trust_config');
        }

        // Published at handling
        $validated['published_at'] = $request->boolean('is_published') ? now() : null;
        $validated['featured'] = $request->boolean('featured');

        // Sync category & category_id
        $this->syncCategoryKeys($validated);

        $course = Course::create($validated);

        // Sync Repeaters
        if ($request->has('faqs')) {
            $this->cmsService->syncFaqs($course, is_array($request->input('faqs')) ? $request->input('faqs') : []);
        }
        if ($request->has('batches')) {
            $this->cmsService->syncBatches($course, is_array($request->input('batches')) ? $request->input('batches') : []);
        }
        if ($request->has('testimonials') && is_array($request->input('testimonials'))) {
            $this->cmsService->syncTestimonials($course, $request->input('testimonials'));
        }
        if ($request->has('partners') && is_array($request->input('partners'))) {
            $this->cmsService->syncPartners($course, $request->input('partners'));
        }
        if ($request->has('faculty') && is_array($request->input('faculty'))) {
            $this->cmsService->syncFaculty($course, $request->input('faculty'));
        }

        $this->cmsService->invalidateCourseCache($course);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$course->name}' has been created successfully.");
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        $course->load([
            'faqs',
            'batches',
            'testimonials',
            'partners',
            'instructors',
        ]);

        $availableInstructors = User::whereIn('role', ['instructor', 'admin'])->orderBy('name')->get();

        return view('admin.courses.edit', [
            'course' => $course,
            'categories' => $this->getCategories(),
            'availableInstructors' => $availableInstructors,
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $this->validateCourse($request, $course->id);

        $oldSlug = $course->slug;

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $newSlug = $validated['slug'];

        // Harmonize & Sanitize Pricing
        if (! empty($validated['discount']) && empty($validated['discount_percent'])) {
            $validated['discount_percent'] = (int) filter_var($validated['discount'], FILTER_SANITIZE_NUMBER_INT);
        }
        if (! empty($validated['overview']) && empty($validated['description'])) {
            $validated['description'] = $validated['overview'];
        }
        $validated = $this->cmsService->harmonizePricing($validated);

        // List transformations
        $validated['highlights'] = $this->parseList($request->input('highlights'));
        $validated['learning_outcomes'] = $this->parseList($request->input('learning_outcomes'));
        $validated['skills_covered'] = $this->parseList($request->input('skills_covered'));
        $validated['certification_skills'] = $this->parseList($request->input('certification_skills'));
        $validated['certification_benefits'] = $this->parseList($request->input('certification_benefits'));

        // Handle section configuration
        if ($request->has('section_config') && is_array($request->input('section_config'))) {
            $validated['section_config'] = $request->input('section_config');
        }

        // Handle trust & proof configuration
        if ($request->has('trust_config') && is_array($request->input('trust_config'))) {
            $validated['trust_config'] = $request->input('trust_config');
        }

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

        // Check if slug changed; record 301 permanent redirect
        if ($oldSlug !== $newSlug) {
            $this->cmsService->recordSlugChange($course, $oldSlug, $newSlug);
        }

        // Sync Repeaters
        if ($request->has('faqs')) {
            $this->cmsService->syncFaqs($course, is_array($request->input('faqs')) ? $request->input('faqs') : []);
        }
        if ($request->has('batches')) {
            $this->cmsService->syncBatches($course, is_array($request->input('batches')) ? $request->input('batches') : []);
        }
        if ($request->has('testimonials') && is_array($request->input('testimonials'))) {
            $this->cmsService->syncTestimonials($course, $request->input('testimonials'));
        }
        if ($request->has('partners') && is_array($request->input('partners'))) {
            $this->cmsService->syncPartners($course, $request->input('partners'));
        }
        if ($request->has('faculty') && is_array($request->input('faculty'))) {
            $this->cmsService->syncFaculty($course, $request->input('faculty'));
        }

        $this->cmsService->invalidateCourseCache($course);

        return redirect()->route('admin.courses.index')
            ->with('success', "Course '{$course->name}' has been updated successfully.");
    }

    /**
     * Preview the course landing page as admin in preview mode.
     */
    public function preview(Course $course): View
    {
        $course->load([
            'modules' => function ($query) {
                $query->ordered()->withCount('lessons')->with(['lessons' => function ($lq) {
                    $lq->ordered();
                }]);
            },
            'instructors' => function ($query) {
                $query->orderByPivot('sort_order', 'asc');
            },
            'activeFaqs',
            'activeBatches',
            'publishedTestimonials',
            'activePartners',
        ]);

        $trustRecords = app(TrustProofService::class)->getRecordsForCourse($course);

        return view('pages.courses.landing', [
            'course' => $course,
            'enrollment' => null,
            'relatedCourses' => $course->relatedCourses(3),
            'isPreview' => true,
            'trustRecords' => $trustRecords,
        ]);
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

        $this->cmsService->invalidateCourseCache($course);

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
        $this->cmsService->invalidateCourseCache($course);
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
            'overview' => ['nullable', 'string'],
            'duration' => ['nullable', 'string', 'max:100'],
            'modules_count' => ['nullable', 'integer', 'min:0'],
            'delivery_mode' => ['nullable', 'string', 'max:255'],
            'fee' => ['nullable', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount' => ['nullable', 'string'],
            'original_fee' => ['nullable', 'integer', 'min:0'],
            'badge_label' => ['nullable', 'string', 'max:100'],
            'tag_badge' => ['nullable', 'string', 'max:50'],
            'highlights' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],

            // CMS & SEO Fields
            'thumbnail_path' => ['nullable', 'string', 'max:255'],
            'banner_path' => ['nullable', 'string', 'max:255'],
            'hero_heading' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string'],
            'hero_cta_text' => ['nullable', 'string', 'max:100'],
            'hero_cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_cta_text' => ['nullable', 'string', 'max:100'],
            'secondary_cta_url' => ['nullable', 'string', 'max:255'],
            'learning_outcomes' => ['nullable'],
            'skills_covered' => ['nullable'],
            'course_objectives' => ['nullable', 'string'],
            'prerequisites' => ['nullable', 'string'],
            'target_audience_text' => ['nullable', 'string'],
            'career_opportunities' => ['nullable', 'string'],
            'certification_name' => ['nullable', 'string', 'max:255'],
            'certification_authority' => ['nullable', 'string', 'max:255'],
            'certification_description' => ['nullable', 'string'],
            'certification_image_path' => ['nullable', 'string', 'max:255'],
            'certification_skills' => ['nullable'],
            'certification_benefits' => ['nullable'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_image_path' => ['nullable', 'string', 'max:255'],
            'robots_directive' => ['nullable', 'string', 'max:100'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'section_config' => ['nullable', 'array'],
            'trust_config' => ['nullable', 'array'],
        ]);
    }

    /**
     * Parse multi-line string or array input into clean string array.
     *
     * @return list<string>
     */
    protected function parseList(mixed $input): array
    {
        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }

        if (! is_string($input) || trim($input) === '') {
            return [];
        }

        $lines = array_map('trim', explode("\n", $input));

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
