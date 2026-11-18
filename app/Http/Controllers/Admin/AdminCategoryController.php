<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of course categories.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));

        $query = Category::query()
            ->withCount('courses')
            ->orderBy('name', 'asc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(15)->withQueryString();

        $totalCategories = Category::count();
        $totalCategorizedCourses = Course::whereNotNull('category_id')->count();

        return view('admin.categories.index', compact(
            'categories',
            'search',
            'totalCategories',
            'totalCategorizedCourses'
        ));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'regex:/^[a-z0-9\-]+$/'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $baseSlug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        if (empty($baseSlug)) {
            $baseSlug = 'category';
        }

        // Ensure slug uniqueness
        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $validated['slug'] = $slug;

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', "Course Category '{$validated['name']}' created successfully.");
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        $category->loadCount('courses');

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:150',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $oldSlug = $category->slug;
        $newSlug = Str::slug($validated['slug']);
        $validated['slug'] = $newSlug;

        $category->update($validated);

        // If slug changed, keep course category string in sync
        if ($oldSlug !== $newSlug) {
            Course::where('category', $oldSlug)->update(['category' => $newSlug]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $coursesCount = $category->courses()->count();

        // Safely reassign courses to default before deletion
        if ($coursesCount > 0) {
            $defaultCategory = Category::where('slug', 'sap-erp')->where('id', '!=', $category->id)->first()
                ?? Category::where('id', '!=', $category->id)->first();

            $newCatId = $defaultCategory?->id;
            $newCatSlug = $defaultCategory?->slug ?? 'sap-erp';

            Course::where('category_id', $category->id)
                ->orWhere('category', $category->slug)
                ->update([
                    'category_id' => $newCatId,
                    'category' => $newCatSlug,
                ]);
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Category '{$name}' deleted successfully. {$coursesCount} associated course(s) were safely re-linked.");
    }
}
