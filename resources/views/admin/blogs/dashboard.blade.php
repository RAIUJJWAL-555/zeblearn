@extends('layouts.admin')

@section('title', 'Blog & Knowledge CMS Dashboard | ZebLearn LMS')
@section('page-title', 'Blog Dashboard')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & QUICK ACTIONS --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-2">
                BLOG &amp; KNOWLEDGE CMS
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Blog Overview &amp; Content Hub
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Create, edit, schedule and publish technical guides, SAP interview notes, and legacy articles directly with complete SEO controls.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button
                href="{{ route('admin.blogs.migration.index') }}"
                variant="outline"
                size="md"
            >
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                WP Migration Hub
            </x-ui.button>
            <x-ui.button
                href="{{ route('admin.blogs.create') }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                Create New Blog
            </x-ui.button>
        </div>
    </div>

    {{-- 2. TELEMETRY CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-slate-500 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">TOTAL ARTICLES</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">{{ number_format($stats['total']) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-emerald-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">PUBLISHED</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading">{{ number_format($stats['published']) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-slate-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">DRAFTS</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-700 font-heading">{{ number_format($stats['draft']) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-blue-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">SCHEDULED</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-blue-600 font-heading">{{ number_format($stats['scheduled']) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-amber-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">INCOMPLETE</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading">{{ number_format($stats['incomplete']) }}</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">LEGACY RECOVERED</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-[#FB6221] font-heading">{{ number_format($stats['fully_recovered']) }}</span>
            </div>
        </div>
    </div>

    {{-- 3. RECENT BLOGS & MIGRATION TELEMETRY --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Recently Updated Articles --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="font-heading text-base font-bold text-slate-900">Recently Updated Articles</h2>
                        <p class="text-xs text-slate-500">Latest changes across published and draft articles</p>
                    </div>
                    <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono font-bold text-[#FB6221] hover:underline uppercase">
                        View All Articles &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentlyUpdated as $b)
                        <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-slate-50/60 transition-colors">
                            <div class="flex items-center gap-3.5 min-w-0">
                                @if($b->featured_image)
                                    <img src="{{ Str::startsWith($b->featured_image, ['http://', 'https://']) ? $b->featured_image : asset($b->featured_image) }}" alt="{{ $b->title }}" class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-200" />
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center shrink-0 text-[#FB6221]">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('admin.blogs.edit', $b) }}" class="font-heading text-sm font-bold text-slate-900 hover:text-[#FB6221] transition-colors truncate block">
                                        {{ $b->title }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-2 mt-1 text-[11px] font-mono text-slate-500">
                                        <span>{{ $b->effective_author_name }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $b->updated_at->diffForHumans() }}</span>
                                        @if($b->categories->isNotEmpty())
                                            <span>&bull;</span>
                                            <span class="text-[#FB6221] font-semibold">{{ $b->categories->first()->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                @if($b->status === 'published')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Published
                                    </span>
                                @elseif($b->status === 'incomplete')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                        Incomplete
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-700 border border-slate-200">
                                        Draft
                                    </span>
                                @endif

                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.blogs.preview', $b) }}" target="_blank" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="Preview Article">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('admin.blogs.edit', $b) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-[#FB6221] hover:bg-orange-50 transition-colors" title="Edit Article">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 font-sans text-xs">
                            No articles created yet. Click "Create New Blog" or run the Legacy Migration.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Side Controls & Migration Summary --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Quick Links Card --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs">
                <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Taxonomy &amp; Tools</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.blogs.categories.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-orange-50 hover:text-[#FB6221] text-xs font-semibold text-slate-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">folder</span>
                            <span>Categories ({{ $stats['categories'] }})</span>
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('admin.blogs.tags.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-orange-50 hover:text-[#FB6221] text-xs font-semibold text-slate-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">label</span>
                            <span>Tags ({{ $stats['tags'] }})</span>
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('admin.blogs.migration.index') }}" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-orange-50 hover:text-[#FB6221] text-xs font-semibold text-slate-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">cloud_sync</span>
                            <span>Legacy Crawler &amp; Logs</span>
                        </span>
                        <span>&rarr;</span>
                    </a>
                    <a href="{{ route('blogs') }}" target="_blank" class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-orange-50 hover:text-[#FB6221] text-xs font-semibold text-slate-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                            <span>Visit Public Blog</span>
                        </span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Migration Stream Snippet --}}
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">Migration Audit</h3>
                    <a href="{{ route('admin.blogs.migration.logs') }}" class="text-[11px] font-mono text-[#FB6221] hover:underline">Full Logs</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentLogs as $log)
                        <div class="text-xs p-3 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-1">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[10px] uppercase font-bold {{ $log->status === 'imported' ? 'text-emerald-600' : ($log->status === 'duplicate' ? 'text-slate-500' : 'text-amber-600') }}">
                                    {{ $log->status }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="font-bold text-slate-800 truncate block">{{ $log->title ?: $log->slug }}</span>
                            <span class="text-[10px] text-slate-400 truncate block">{{ $log->source_url }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No migration runs executed yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
