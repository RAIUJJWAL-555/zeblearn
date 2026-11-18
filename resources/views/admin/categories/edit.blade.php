@extends('layouts.admin')

@section('title', 'Edit Category: ' . $category->name . ' - LMS Administration')
@section('page-title', 'Edit Category')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
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

    {{-- Breadcrumb & Back Action --}}
    <div class="flex items-center justify-between">
        <a
            href="{{ route('admin.categories.index') }}"
            class="inline-flex items-center gap-2 text-xs font-mono font-bold text-slate-500 hover:text-[#FB6221] transition-colors"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to All Categories</span>
        </a>

        <a
            href="{{ url('/' . $category->slug) }}"
            target="_blank"
            class="inline-flex items-center gap-1.5 text-xs font-mono text-slate-500 hover:text-[#FB6221] transition-colors"
        >
            <span>View Public Route</span>
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
        </a>
    </div>

    {{-- Edit Form Card --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200/60 mb-1.5">
                    Editing Category #{{ $category->id }}
                </span>
                <h1 class="font-heading font-bold text-xl text-slate-900">
                    {{ $category->name }}
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Update category title and URL prefix. {{ $category->courses_count }} course(s) currently linked to this track.
                </p>
            </div>

            <div class="text-right">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-mono font-bold {{ $category->courses_count > 0 ? 'bg-orange-50 text-[#FB6221]' : 'bg-slate-100 text-slate-500' }}">
                    <span>{{ $category->courses_count }}</span>
                    <span>linked courses</span>
                </span>
            </div>
        </div>

        <form
            action="{{ route('admin.categories.update', $category) }}"
            method="POST"
            x-data="{
                name: @js(old('name', $category->name)),
                slug: @js(old('slug', $category->slug)),
                slugChanged: false,
                generateSlug() {
                    if (!this.slugChanged) {
                        this.slug = this.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                    }
                }
            }"
            class="p-6 space-y-5"
        >
            @csrf
            @method('PUT')

            {{-- Category Name --}}
            <div>
                <label for="name" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Category Name <span class="text-rose-500">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    x-model="name"
                    @input="generateSlug()"
                    value="{{ old('name', $category->name) }}"
                    required
                    class="w-full px-4 py-2.5 text-sm text-slate-900 border @error('name') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/20 outline-hidden transition-all"
                />
                @error('name')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- URL Slug --}}
            <div>
                <label for="slug" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    URL Slug (Prefix) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-mono text-slate-400">
                        /
                    </span>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        x-model="slug"
                        @input="slugChanged = true"
                        value="{{ old('slug', $category->slug) }}"
                        required
                        class="w-full pl-7 pr-4 py-2.5 text-sm font-mono text-slate-900 border @error('slug') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/20 outline-hidden transition-all"
                    />
                </div>
                @error('slug')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror

                <div class="mt-2.5 p-3 rounded-xl bg-orange-50/70 border border-orange-100 text-xs font-mono text-slate-600">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold mb-0.5">Updated Course URL Format:</span>
                    <span class="text-[#FB6221] font-semibold">https://sap.zeblearn.com/<span x-text="(slug || 'category').replace(/^\/+|\/+$/g, '')"></span>/{course-slug}/</span>
                    <p class="text-[11px] text-amber-700 mt-1 font-sans">
                        Note: Changing the URL prefix will update the public URLs of all {{ $category->courses_count }} courses assigned to this category.
                    </p>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Description (Optional)
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="w-full px-4 py-2.5 text-sm text-slate-900 border @error('description') border-rose-400 bg-rose-50/20 @else border-slate-300 @enderror rounded-xl focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/20 outline-hidden transition-all resize-none"
                    placeholder="Provide context on what this category track covers..."
                >{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Form Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a
                    href="{{ route('admin.categories.index') }}"
                    class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="px-6 py-2.5 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-bold uppercase tracking-wider shadow-sm shadow-orange-500/20 hover:shadow-md hover:shadow-orange-500/30 transition-all cursor-pointer flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
