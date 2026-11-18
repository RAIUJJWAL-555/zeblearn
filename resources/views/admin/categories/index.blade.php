@extends('layouts.admin')

@section('title', 'Course Categories - LMS Administration')
@section('page-title', 'Course Categories')

@section('content')
<div
    x-data="{
        createName: '',
        createSlug: '',
        autoGenerateSlug() {
            this.createSlug = this.createName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    }"
    class="space-y-6"
>
    {{-- Alert Messages --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="danger" dismissible>
            {{ session('error') }}
        </x-ui.alert>
    @endif

    {{-- Header Banner & Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200/60">
                    Course Taxonomy
                </span>
                <span class="text-xs font-mono text-slate-400">Hierarchical URL Structure</span>
            </div>
            <h1 class="text-2xl font-bold font-heading text-slate-900 tracking-tight">
                Course Categories
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Manage subject categories and custom course URL prefixes like <code class="px-1.5 py-0.5 rounded bg-slate-100 text-[#FB6221] font-mono text-xs">/{category}/{course-slug}</code>.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                @click="$refs.nameInput.focus(); $refs.nameInput.scrollIntoView({ behavior: 'smooth', block: 'center' })"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-bold uppercase tracking-wider shadow-sm shadow-orange-500/20 hover:shadow-md hover:shadow-orange-500/30 transition-all cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add New Category</span>
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">Total Categories</p>
                    <h3 class="text-2xl font-black text-slate-900 font-heading mt-1">{{ $totalCategories }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FB6221]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">Active subject branches</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">Categorized Courses</p>
                    <h3 class="text-2xl font-black text-slate-900 font-heading mt-1">{{ $totalCategorizedCourses }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">Published courses linked to categories</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">URL Routing</p>
                    <h3 class="text-sm font-black text-emerald-600 font-mono mt-2">/{category}/{course}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-slate-500 mt-2">Fully SEO &amp; Legacy Parity Compliant</p>
        </div>
    </div>

    {{-- Main 2-Column Section: Form on Left, Table on Right --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {{-- LEFT COLUMN: ADD CATEGORY FORM (Pure POST form - 100% reliable) --}}
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sticky top-24">
            <div class="border-b border-slate-100 pb-4 mb-5">
                <h3 class="font-heading font-bold text-base text-slate-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                    <span>Add New Category</span>
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Create a subject track and custom URL slug prefix for course landing pages.
                </p>
            </div>

            <form
                action="{{ route('admin.categories.store') }}"
                method="POST"
                class="space-y-4"
            >
                @csrf

                <div>
                    <label for="create_name" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Category Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="create_name"
                        name="name"
                        x-ref="nameInput"
                        x-model="createName"
                        @input="autoGenerateSlug()"
                        value="{{ old('name') }}"
                        placeholder="e.g. SAP Functional, SAP Technical"
                        required
                        class="w-full px-3.5 py-2.5 text-xs text-slate-900 border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/20 outline-hidden transition-all"
                    />
                    @error('name')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="create_slug" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        URL Slug (Prefix) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-xs font-mono text-slate-400">
                            /
                        </span>
                        <input
                            type="text"
                            id="create_slug"
                            name="slug"
                            x-model="createSlug"
                            value="{{ old('slug') }}"
                            placeholder="sap-functional"
                            required
                            class="w-full pl-6 pr-3.5 py-2.5 text-xs font-mono text-slate-900 border @error('slug') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/20 outline-hidden transition-all"
                        />
                    </div>
                    @error('slug')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 p-2.5 rounded-lg bg-orange-50/60 border border-orange-100 text-[11px] font-mono text-slate-600">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Live URL Preview:</span>
                        <span class="text-[#FB6221] font-semibold">https://sap.zeblearn.com/<span x-text="(createSlug || 'category').replace(/^\/+|\/+$/g, '')"></span>/{course-slug}/</span>
                    </div>
                </div>

                <div>
                    <label for="create_description" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Description (Optional)
                    </label>
                    <textarea
                        id="create_description"
                        name="description"
                        rows="3"
                        placeholder="Brief overview of courses covered under this subject category..."
                        class="w-full px-3.5 py-2 text-xs text-slate-900 border border-slate-300 rounded-xl focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/20 outline-hidden transition-all resize-none"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-bold uppercase tracking-wider shadow-sm shadow-orange-500/20 hover:shadow-md hover:shadow-orange-500/30 transition-all cursor-pointer flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Create Category</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: SEARCH & CATEGORIES LIST TABLE --}}
        <div class="lg:col-span-8 space-y-4">
            {{-- Search Bar --}}
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-4">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search categories by name, slug or description..."
                        class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 hover:bg-white text-slate-700 border border-slate-200 rounded-xl placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    />
                </form>

                @if (! empty($search))
                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors shrink-0"
                    >
                        Clear Search
                    </a>
                @endif
            </div>

            {{-- Categories Table --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3.5 px-5">Category Name</th>
                                <th class="py-3.5 px-4">URL Slug (Prefix)</th>
                                <th class="py-3.5 px-4 text-center">Courses</th>
                                <th class="py-3.5 px-4">Public Route</th>
                                <th class="py-3.5 px-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($categories as $cat)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-5">
                                        <div class="font-bold text-slate-900 font-heading text-sm">
                                            {{ $cat->name }}
                                        </div>
                                        @if ($cat->description)
                                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1 max-w-xs font-sans">
                                                {{ $cat->description }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                            /{{ $cat->slug }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <a
                                            href="{{ route('admin.courses.index', ['category' => $cat->slug]) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-mono font-bold {{ $cat->courses_count > 0 ? 'bg-orange-50 text-[#FB6221] hover:bg-orange-100' : 'bg-slate-100 text-slate-500' }} transition-colors"
                                            title="View courses in this category"
                                        >
                                            <span>{{ $cat->courses_count }}</span>
                                            <span class="text-[10px]">courses</span>
                                        </a>
                                    </td>
                                    <td class="py-4 px-4">
                                        <a
                                            href="{{ url('/' . $cat->slug) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-600 hover:text-[#FB6221] transition-colors"
                                        >
                                            <span>/{{ $cat->slug }}/*</span>
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="py-4 px-5 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            {{-- Edit Link: Standard clean RESTful navigation to Edit Page --}}
                                            <a
                                                href="{{ route('admin.categories.edit', $cat) }}"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-[#FB6221] hover:bg-orange-50 transition-colors inline-flex items-center"
                                                title="Edit Category"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>

                                            <form
                                                action="{{ route('admin.categories.destroy', $cat) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete category \'{{ addslashes($cat->name) }}\'? Any assigned courses will be safely re-linked.');"
                                                class="inline-block"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Delete Category"
                                                >
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                            <p class="text-sm font-semibold text-slate-700">No categories found</p>
                                            <p class="text-xs text-slate-500">Create a new category on the left to organize courses and configure URL structures.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
