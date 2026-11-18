@extends('layouts.admin')

@section('title', 'Manage Blog Categories | ZebLearn LMS')
@section('page-title', 'Blog Categories')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-1">
                TAXONOMY
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Blog Categories
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Organize blog articles into searchable educational categories and SEO archives.
            </p>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono font-bold text-slate-500 hover:text-slate-800">
            &larr; Back to Articles
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Create Category Form (4 cols) --}}
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs">
            <h2 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Add New Category</h2>
            <form method="POST" action="{{ route('admin.blogs.categories.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Category Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        required
                        placeholder="e.g. SAP FICO Guides"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <div>
                    <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Slug (URL)
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        placeholder="sap-fico-guides"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-mono text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Description
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="3"
                        placeholder="Brief overview of articles in this category..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    ></textarea>
                </div>

                <div>
                    <label for="seo_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        SEO Title
                    </label>
                    <input
                        type="text"
                        name="seo_title"
                        id="seo_title"
                        placeholder="Custom Archive Title Tag..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <div>
                    <label for="meta_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Meta Description
                    </label>
                    <textarea
                        name="meta_description"
                        id="meta_description"
                        rows="2"
                        placeholder="Meta description for search engines..."
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="w-full py-2.5 bg-[#0A0A0A] hover:bg-[#FB6221] text-white text-xs font-mono font-bold uppercase rounded-xl transition-colors cursor-pointer shadow-xs"
                >
                    Create Category
                </button>
            </form>
        </div>

        {{-- Categories Table (8 cols) --}}
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Name</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4">Articles</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/70 transition-colors" x-data="{ editing: false }">
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                <div>
                                    <span>{{ $category->name }}</span>
                                    @if($category->description)
                                        <p class="text-[11px] font-normal text-slate-500 mt-0.5 line-clamp-1">{{ $category->description }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
                                <a href="{{ route('blogs.category', $category->slug) }}" target="_blank" class="hover:text-[#FB6221] underline">
                                    /category/{{ $category->slug }}/
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700">
                                    {{ $category->blogs_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.blogs.categories.destroy', $category) }}" onsubmit="return confirm('Delete category {{ $category->name }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer" title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
