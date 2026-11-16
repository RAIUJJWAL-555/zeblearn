<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\TrustItem;
use App\Services\TrustProofService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminTrustItemController extends Controller
{
    public function __construct(
        protected TrustProofService $trustProofService
    ) {}

    /**
     * Display a paginated listing of central trust and proof of work items.
     */
    public function index(Request $request): View
    {
        $type = $request->query('type', 'all');
        $courseId = $request->query('course_id', 'all');
        $category = $request->query('category', 'all');
        $status = $request->query('status', 'all');
        $search = trim($request->input('search', $request->input('q', '')));

        $query = TrustItem::query()->with('courses');

        // Filter by Type
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        // Filter by Course
        if ($courseId && $courseId !== 'all') {
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        // Filter by Category
        if ($category && $category !== 'all') {
            $query->where(function ($q) use ($category) {
                $q->where('content_category', $category)
                    ->orWhere('scope_category', $category);
            });
        }

        // Filter by Status
        if ($status && $status !== 'all') {
            match ($status) {
                'published' => $query->where('is_published', true),
                'draft' => $query->where('is_published', false),
                'verified' => $query->where('is_verified', true),
                'unverified' => $query->where('is_verified', false),
                default => null,
            };
        }

        // Keyword Search
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('content_category', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('display_priority', 'asc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $allCourses = Course::orderBy('name')->get(['id', 'name', 'category', 'slug']);

        return view('admin.trust.index', [
            'items' => $items,
            'types' => TrustItem::TYPES,
            'categories' => Course::CATEGORIES,
            'allCourses' => $allCourses,
            'activeType' => $type,
            'activeCourse' => $courseId,
            'activeCategory' => $category,
            'activeStatus' => $status,
            'searchQuery' => $search,
            'totalCount' => TrustItem::count(),
            'publishedCount' => TrustItem::where('is_published', true)->count(),
            'verifiedCount' => TrustItem::where('is_verified', true)->count(),
        ]);
    }

    /**
     * Show the form for creating a new trust item.
     */
    public function create(Request $request): View
    {
        $defaultType = $request->query('type', TrustItem::TYPE_PLACEMENT);
        if (! array_key_exists($defaultType, TrustItem::TYPES)) {
            $defaultType = TrustItem::TYPE_PLACEMENT;
        }

        return view('admin.trust.create', [
            'trustItem' => new TrustItem([
                'type' => $defaultType,
                'scope_type' => TrustItem::SCOPE_GLOBAL,
                'is_verified' => true,
                'is_published' => true,
                'display_priority' => 10,
            ]),
            'types' => TrustItem::TYPES,
            'categories' => Course::CATEGORIES,
            'allCourses' => Course::orderBy('name')->get(['id', 'name', 'category', 'slug']),
        ]);
    }

    /**
     * Store a newly created trust record in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTrustItem($request);

        $courseIds = $request->input('course_ids', []);
        unset($validated['course_ids']);

        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['is_published'] = $request->boolean('is_published');

        $trustItem = TrustItem::create($validated);

        // Sync explicit course mappings if selected_courses
        if ($validated['scope_type'] === TrustItem::SCOPE_SELECTED_COURSES && is_array($courseIds)) {
            $syncData = [];
            foreach ($courseIds as $idx => $cid) {
                if (! empty($cid)) {
                    $syncData[$cid] = ['sort_order' => ($idx + 1) * 10];
                }
            }
            $trustItem->courses()->sync($syncData);
        }

        $this->trustProofService->invalidateForItem($trustItem);

        return redirect()->route('admin.trust.index', ['type' => $trustItem->type])
            ->with('success', "'{$trustItem->title}' has been added to the Trust & Proof Library.");
    }

    /**
     * Show the form for editing the specified trust item.
     */
    public function edit(TrustItem $trustItem): View
    {
        $trustItem->load('courses');

        return view('admin.trust.edit', [
            'trustItem' => $trustItem,
            'types' => TrustItem::TYPES,
            'categories' => Course::CATEGORIES,
            'allCourses' => Course::orderBy('name')->get(['id', 'name', 'category', 'slug']),
        ]);
    }

    /**
     * Update the specified trust record in storage.
     */
    public function update(Request $request, TrustItem $trustItem): RedirectResponse
    {
        $validated = $this->validateTrustItem($request, $trustItem->id);

        $courseIds = $request->input('course_ids', []);
        unset($validated['course_ids']);

        $validated['is_verified'] = $request->boolean('is_verified');
        $validated['is_published'] = $request->boolean('is_published');

        // Invalidate old mapping caches before update
        $this->trustProofService->invalidateForItem($trustItem);

        $trustItem->update($validated);

        // Sync or detach course mappings
        if ($validated['scope_type'] === TrustItem::SCOPE_SELECTED_COURSES && is_array($courseIds)) {
            $syncData = [];
            foreach ($courseIds as $idx => $cid) {
                if (! empty($cid)) {
                    $syncData[$cid] = ['sort_order' => ($idx + 1) * 10];
                }
            }
            $trustItem->courses()->sync($syncData);
        } else {
            $trustItem->courses()->detach();
        }

        // Invalidate new mapping caches
        $this->trustProofService->invalidateForItem($trustItem);

        return redirect()->route('admin.trust.index', ['type' => $trustItem->type])
            ->with('success', "'{$trustItem->title}' has been updated successfully.");
    }

    /**
     * Toggle publication or verification status directly.
     */
    public function toggleStatus(Request $request, TrustItem $trustItem): RedirectResponse
    {
        $field = $request->input('field', 'is_published');
        if (! in_array($field, ['is_published', 'is_verified'])) {
            $field = 'is_published';
        }

        $trustItem->update([
            $field => ! $trustItem->{$field},
        ]);

        $this->trustProofService->invalidateForItem($trustItem);

        return back()->with('success', "Status for '{$trustItem->title}' has been updated.");
    }

    /**
     * Remove the specified trust record from storage.
     */
    public function destroy(TrustItem $trustItem): RedirectResponse
    {
        $title = $trustItem->title;
        $this->trustProofService->invalidateForItem($trustItem);
        $trustItem->delete();

        return redirect()->route('admin.trust.index')
            ->with('success', "'{$title}' has been deleted from the Trust & Proof Library.");
    }

    /**
     * Validate request payload.
     *
     * @return array<string, mixed>
     */
    protected function validateTrustItem(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'type' => ['required', 'string', Rule::in(array_keys(TrustItem::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'media_url' => ['nullable', 'string', 'max:255'],
            'content_category' => ['nullable', 'string', 'max:100'],
            'scope_type' => ['required', 'string', Rule::in([
                TrustItem::SCOPE_GLOBAL,
                TrustItem::SCOPE_ALL,
                TrustItem::SCOPE_CATEGORY,
                TrustItem::SCOPE_SELECTED_COURSES,
            ])],
            'scope_category' => ['nullable', 'string', 'max:100'],
            'display_priority' => ['nullable', 'integer', 'min:0'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
            'metadata' => ['nullable', 'array'],
        ]);
    }
}
