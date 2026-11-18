@extends('layouts.admin')

@section('title', 'Legacy WordPress Blog Migration | ZebLearn LMS')
@section('page-title', 'Legacy Blog Migration')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-1">
                PUBLIC WORDPRESS RECOVERY ENGINE
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                WordPress Migration Console
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Crawl, recover, sanitize, and store all historical SAP articles from <strong>sap.zeblearn.com/category/sap-blogs/</strong> with exact URL preservation.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.blogs.migration.logs') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-xs font-bold uppercase transition-colors">
                View Full Audit Logs
            </a>
            <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono font-bold text-slate-500 hover:text-slate-800">
                &larr; Back to Articles
            </a>
        </div>
    </div>

    {{-- 2. METRICS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-slate-500 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">DISCOVERED URLS</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">{{ number_format($stats['total_discovered']) }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-emerald-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">IMPORTED BLOGS</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading">{{ number_format($stats['total_imported']) }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-blue-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">FULLY RECOVERED</span>
            <span class="text-2xl sm:text-3xl font-black text-blue-600 font-heading">{{ number_format($stats['fully_recovered']) }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-amber-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">INCOMPLETE</span>
            <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading">{{ number_format($stats['incomplete']) }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-slate-500 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">DUPLICATES</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-600 font-heading">{{ number_format($stats['duplicates']) }}</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-rose-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">FAILED ATTEMPTS</span>
            <span class="text-2xl sm:text-3xl font-black text-rose-600 font-heading">{{ number_format($stats['failed']) }}</span>
        </div>
    </div>

    {{-- 3. MIGRATION EXECUTION PANEL & SOURCE AUDIT --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Migration Trigger Card (5 cols) --}}
        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="font-heading text-base font-bold text-slate-900">Run Migration Crawler</h2>
                <p class="text-xs text-slate-500">Automated fetch with multi-source fallback</p>
            </div>

            <div class="p-4 rounded-xl bg-orange-50/60 border border-orange-200 text-xs text-slate-700 space-y-2">
                <div class="font-bold text-[#FB6221] font-mono flex items-center gap-1.5 uppercase text-[11px]">
                    <span class="material-symbols-outlined text-[16px]">priority_high</span>
                    <span>Recovery Priority Chain</span>
                </div>
                <ol class="list-decimal list-inside space-y-1 text-slate-600 font-sans text-[11px]">
                    <li><strong>WordPress REST API:</strong> /wp-json/wp/v2/posts (Categories=11)</li>
                    <li><strong>Category Archive Crawler:</strong> 24 archive pages (/category/sap-blogs/)</li>
                    <li><strong>Yoast XML Sitemap:</strong> /post-sitemap.xml</li>
                    <li><strong>Article Scraping:</strong> Full body extraction &amp; image saving</li>
                </ol>
            </div>

            <form method="POST" action="{{ route('admin.blogs.migration.start') }}" class="space-y-4" onsubmit="if (this.force.checked) { return confirm('CAUTION: You have selected Overwrite mode. A backup snapshot will be saved to migration logs, and existing articles will be updated from WordPress. Are you sure you wish to continue?'); }">
                @csrf
                <div>
                    <label for="limit" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Batch Size Limit <span class="text-slate-400 font-normal">(Leave empty for all accessible)</span>
                    </label>
                    <input
                        type="number"
                        name="limit"
                        id="limit"
                        placeholder="e.g. 50 (or blank for complete recovery)"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-mono text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <div class="pt-1">
                    <label class="flex items-center gap-2 text-xs font-mono text-slate-700 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="force"
                            value="1"
                            class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221]"
                        />
                        <span>Overwrite existing articles if re-crawling</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full py-3 bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-mono font-bold uppercase tracking-wider rounded-xl transition-all shadow-md hover:shadow-lg cursor-pointer flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined text-[18px]">sync</span>
                    <span>Start Migration Crawler</span>
                </button>
            </form>
        </div>

        {{-- Recovered Articles List (7 cols) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider">Recently Recovered Articles</h3>
                    <p class="text-xs text-slate-500">Imported articles stored in Draft/Incomplete state</p>
                </div>
                <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono text-[#FB6221] hover:underline uppercase font-bold">
                    Manage Blogs &rarr;
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentRecoveredBlogs as $item)
                    <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                        <div class="min-w-0">
                            <a href="{{ route('admin.blogs.edit', $item) }}" class="font-heading font-bold text-sm text-slate-900 hover:text-[#FB6221] truncate block">
                                {{ $item->title }}
                            </a>
                            <div class="flex items-center gap-2 mt-1 font-mono text-[10px] text-slate-400 truncate">
                                <span>/{{ $item->slug }}/</span>
                                <span>&bull;</span>
                                <span class="text-slate-500">{{ $item->reading_time }} min read</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($item->recovery_status === 'fully_recovered')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Full Body
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                    Incomplete
                                </span>
                            @endif

                            <a href="{{ route('admin.blogs.edit', $item) }}" class="p-1 text-slate-400 hover:text-[#FB6221] transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 font-sans text-xs">
                        No articles recovered yet. Click "Start Migration Crawler" to begin.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
