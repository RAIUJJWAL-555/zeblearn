@extends('layouts.admin')

@section('title', 'Manage Blog Articles | ZebLearn LMS')
@section('page-title', 'Blog Management')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-2">
                ARTICLE REPOSITORY
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                All Blog Articles
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Filter, search, review SEO metadata, and publish articles across all educational disciplines.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button
                href="{{ route('admin.blogs.create') }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                New Article
            </x-ui.button>
        </div>
    </div>

    {{-- 2. FILTERS & SEARCH BAR --}}
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.blogs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            {{-- Search Box --}}
            <div class="lg:col-span-4">
                <label for="search" class="block text-[11px] font-mono font-bold uppercase text-slate-500 mb-1">Search</label>
                <div class="relative">
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="{{ request('search') }}"
                        placeholder="Search by title, slug, or excerpt..."
                        class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 bg-white placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:border-transparent font-sans"
                    />
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="lg:col-span-2">
                <label for="status" class="block text-[11px] font-mono font-bold uppercase text-slate-500 mb-1">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] font-sans">
                    <option value="">All Statuses</option>
                    @foreach(\App\Models\Blog::STATUSES as $val => $lbl)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Category Filter --}}
            <div class="lg:col-span-3">
                <label for="category_id" class="block text-[11px] font-mono font-bold uppercase text-slate-500 mb-1">Category</label>
                <select name="category_id" id="category_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] font-sans">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Author Filter --}}
            <div class="lg:col-span-2">
                <label for="author_id" class="block text-[11px] font-mono font-bold uppercase text-slate-500 mb-1">Author</label>
                <select name="author_id" id="author_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] font-sans">
                    <option value="">All Authors</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="lg:col-span-1 flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-[#0A0A0A] hover:bg-[#FB6221] text-white text-xs font-mono font-bold uppercase rounded-xl transition-colors cursor-pointer flex items-center justify-center shadow-xs">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- 3. ARTICLES TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 w-16">Image</th>
                        <th class="py-3.5 px-4">Title &amp; Slug</th>
                        <th class="py-3.5 px-4">Categories</th>
                        <th class="py-3.5 px-4">Author</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Published Date</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($blogs as $blog)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Image Thumbnail --}}
                            <td class="py-3.5 px-4">
                                @if($blog->featured_image)
                                    <img
                                        src="{{ Str::startsWith($blog->featured_image, ['http://', 'https://']) ? $blog->featured_image : asset($blog->featured_image) }}"
                                        alt="{{ $blog->title }}"
                                        class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-2xs"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-xs font-mono">
                                        N/A
                                    </div>
                                @endif
                            </td>

                            {{-- Title & Slug --}}
                            <td class="py-3.5 px-4 max-w-md">
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="font-heading font-bold text-slate-900 hover:text-[#FB6221] transition-colors line-clamp-1 block text-sm">
                                    {{ $blog->title }}
                                </a>
                                <span class="font-mono text-[10px] text-slate-400 block truncate mt-0.5">
                                    /{{ $blog->slug }}/
                                </span>
                            </td>

                            {{-- Categories --}}
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($blog->categories as $c)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-mono font-medium">
                                            {{ $c->name }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 font-mono text-[10px]">Uncategorized</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Author --}}
                            <td class="py-3.5 px-4 font-sans text-slate-600">
                                {{ $blog->effective_author_name }}
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4">
                                @if($blog->status === 'published')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @elseif($blog->status === 'incomplete')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Incomplete
                                    </span>
                                @elseif($blog->status === 'scheduled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Scheduled
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>

                            {{-- Published Date --}}
                            <td class="py-3.5 px-4 font-mono text-slate-500 text-[11px]">
                                {{ $blog->published_at ? $blog->published_at->format('M d, Y') : '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Publish/Unpublish direct button --}}
                                    @if($blog->status === 'published')
                                        <form method="POST" action="{{ route('admin.blogs.unpublish', $blog) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-mono font-bold uppercase bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="Move to Draft">
                                                Unpublish
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.blogs.publish', $blog) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-mono font-bold uppercase bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition-colors" title="Publish Instantly">
                                                Publish
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Preview --}}
                                    <a href="{{ route('admin.blogs.preview', $blog) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Preview Article">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-[#FB6221] hover:bg-orange-50 transition-colors" title="Edit Article">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" onsubmit="return confirm('Are you sure you want to permanently delete this article?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Delete Article">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 font-sans">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                                    <p class="text-sm font-medium">No blog articles match your filters.</p>
                                    <a href="{{ route('admin.blogs.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#FB6221] text-white text-xs font-mono font-bold uppercase shadow-xs">
                                        Create First Blog
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($blogs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
