<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminBlogCategoryController extends Controller
{
    /**
     * Display categories listing and management.
     */
    public function index(): View
    {
        $categories = BlogCategory::withCount('blogs')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.blogs.categories.index', compact('categories'));
    }

    /**
     * Store a new category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_categories,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        BlogCategory::create($validated);

        return redirect()->route('admin.blogs.categories.index')
            ->with('success', "Category '{$validated['name']}' created successfully.");
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_categories', 'slug')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $category->update($validated);

        return redirect()->route('admin.blogs.categories.index')
            ->with('success', "Category '{$category->name}' updated successfully.");
    }

    /**
     * Delete a category.
     */
    public function destroy(BlogCategory $category): RedirectResponse
    {
        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.blogs.categories.index')
            ->with('success', "Category '{$name}' deleted.");
    }
}
