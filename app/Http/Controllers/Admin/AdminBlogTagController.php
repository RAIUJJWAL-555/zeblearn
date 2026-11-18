<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminBlogTagController extends Controller
{
    /**
     * Display tags listing and creation interface.
     */
    public function index(): View
    {
        $tags = BlogTag::withCount('blogs')
            ->orderBy('name')
            ->paginate(30);

        return view('admin.blogs.tags.index', compact('tags'));
    }

    /**
     * Store a new tag.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:blog_tags,slug'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BlogTag::create($validated);

        return redirect()->route('admin.blogs.tags.index')
            ->with('success', "Tag '{$validated['name']}' created successfully.");
    }

    /**
     * Delete a tag.
     */
    public function destroy(BlogTag $tag): RedirectResponse
    {
        $name = $tag->name;
        $tag->delete();

        return redirect()->route('admin.blogs.tags.index')
            ->with('success', "Tag '{$name}' deleted.");
    }
}
