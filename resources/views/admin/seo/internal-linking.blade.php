@extends('layouts.admin')

@section('title', 'SEO Spider Web & Internal Linking Engine | ZebLearn Admin')
@section('page-title', 'SEO Spider Web Architecture')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    {{-- Header Banner --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden border border-slate-800 shadow-xl">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[10px] font-mono font-bold tracking-widest uppercase text-[#FB6221]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] animate-pulse"></span>
                    <span>Fully Automatic SEO Spider Web Engine</span>
                </div>
                <h1 class="font-heading font-black text-2xl sm:text-3xl uppercase tracking-tight text-white">
                    Internal Linking &amp; Topical Graph
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Continuously indexing {{ $stats['total_published_courses'] }} courses and {{ $stats['total_published_blogs'] }} articles. Automatic discovery, bi-directional cross-linking, and contextual link injection active.
                </p>
            </div>

            <form action="{{ route('admin.seo.recalculate') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="px-5 py-3 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-mono text-xs font-bold uppercase tracking-wider transition-all shadow-md hover:scale-[1.02] flex items-center gap-2 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[18px]">sync</span>
                    <span>Reconcile Graph</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Top Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Health Score --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">Spider Health</p>
                    <h3 class="text-2xl font-black text-emerald-600 font-heading mt-1">{{ $stats['health_score'] }}%</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <span class="material-symbols-outlined text-[20px]">verified</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">Topical authority network live</p>
        </div>

        {{-- Total Internal Links --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">Total Internal Links</p>
                    <h3 class="text-2xl font-black text-slate-900 font-heading mt-1">{{ number_format($stats['total_internal_links']) }}+</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FB6221]">
                    <span class="material-symbols-outlined text-[20px]">hub</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">Avg {{ $stats['average_links_per_page'] }} links / published page</p>
        </div>

        {{-- Orphan Pages Detector --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">Orphan Pages</p>
                    <h3 class="text-2xl font-black font-heading mt-1 {{ ($stats['orphan_courses_count'] + $stats['orphan_blogs_count']) === 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $stats['orphan_courses_count'] + $stats['orphan_blogs_count'] }}
                    </h3>
                </div>
                <div class="w-10 h-10 rounded-xl {{ ($stats['orphan_courses_count'] + $stats['orphan_blogs_count']) === 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">link_off</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">Zero dead-end URLs</p>
        </div>

        {{-- Inbound Leads Attribution --}}
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-slate-400">Tracked Inbound Leads</p>
                    <h3 class="text-2xl font-black text-blue-600 font-heading mt-1">{{ number_format($leadsCount) }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                    <span class="material-symbols-outlined text-[20px]">trending_up</span>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 font-mono">Via bridges &amp; demo forms</p>
        </div>
    </div>

    {{-- Main 2-Column: Left Log & Orphan Detector, Right Manual Overrides --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT COLUMN: Recent Activity & Orphan Detector (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Orphan Detector Card --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#FB6221]">radar</span>
                        <h3 class="font-heading font-bold text-base text-slate-900">
                            Orphan Page Health Inspection
                        </h3>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400">
                        {{ $orphans['courses']->count() + $orphans['blogs']->count() }} Detected
                    </span>
                </div>

                @if($orphans['courses']->isEmpty() && $orphans['blogs']->isEmpty())
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-xs font-mono text-emerald-800 flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        <span>All published courses and blogs are properly connected to the Spider Web network.</span>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($orphans['blogs'] as $orphanBlog)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50/60 border border-amber-200 text-xs">
                                <span class="font-medium text-slate-800 truncate max-w-md">{{ $orphanBlog->title }}</span>
                                <span class="font-mono text-[10px] uppercase font-bold text-amber-700">Needs Module Tags</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent Automated Linking Audit Trail --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#FB6221]">history</span>
                        <h3 class="font-heading font-bold text-base text-slate-900">
                            Automated Activity &amp; Audit Trail
                        </h3>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400">Live Telemetry</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 font-mono uppercase text-[10px]">
                                <th class="pb-2.5">Entity</th>
                                <th class="pb-2.5">Action</th>
                                <th class="pb-2.5">Links Created</th>
                                <th class="pb-2.5 text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentLogs as $log)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 font-mono">
                                        <span class="uppercase font-bold text-slate-700">{{ $log->source_type }}</span>
                                        @if($log->source_id)
                                            <span class="text-slate-400">#{{ $log->source_id }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $log->action === 'auto_linked' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 font-mono font-semibold text-slate-900">
                                        {{ $log->links_generated }} qualifying links
                                    </td>
                                    <td class="py-2.5 text-right font-mono text-[10px] text-slate-400">
                                        {{ $log->created_at?->diffForHumans() }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400 font-mono">
                                        No recent logs recorded. The system continuously reconciles on content changes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Manual Override Controls (5 cols) --}}
        <div class="lg:col-span-5 space-y-6">

            {{-- Create Override Form --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-[#FB6221]">tune</span>
                        <h3 class="font-heading font-bold text-base text-slate-900">
                            Manual Rule Override
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pin or exclude specific relationship links when required.
                    </p>
                </div>

                <form action="{{ route('admin.seo.override.store') }}" method="POST" class="space-y-3.5">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-mono font-bold uppercase text-slate-700 mb-1">Source Type</label>
                            <select name="source_type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl outline-hidden focus:border-[#FB6221]">
                                <option value="blog">Blog Article</option>
                                <option value="course">Course Page</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-mono font-bold uppercase text-slate-700 mb-1">Action</label>
                            <select name="action" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl outline-hidden focus:border-[#FB6221]">
                                <option value="pin">Pin (Force Link)</option>
                                <option value="exclude">Exclude (Block Link)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-mono font-bold uppercase text-slate-700 mb-1">Target Course</label>
                        <select name="target_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl outline-hidden focus:border-[#FB6221]">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (ID #{{ $c->id }})</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="target_type" value="course" />
                    </div>

                    <div>
                        <label class="block text-[11px] font-mono font-bold uppercase text-slate-700 mb-1">Source ID (Optional, empty for all)</label>
                        <input
                            type="number"
                            name="source_id"
                            placeholder="e.g. 2 for specific blog ID"
                            class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl outline-hidden focus:border-[#FB6221]"
                        />
                    </div>

                    <div>
                        <label class="block text-[11px] font-mono font-bold uppercase text-slate-700 mb-1">Custom Anchor Text (Optional)</label>
                        <input
                            type="text"
                            name="custom_anchor_text"
                            placeholder="e.g. Explore SAP FICO S/4 HANA"
                            class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl outline-hidden focus:border-[#FB6221]"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2.5 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-mono text-xs font-bold uppercase tracking-wider transition-all shadow-xs cursor-pointer"
                    >
                        Save Link Override
                    </button>
                </form>
            </div>

            {{-- Existing Overrides Table --}}
            @if($overrides->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                    <h4 class="font-heading font-bold text-sm text-slate-900 mb-3">Active Manual Overrides</h4>
                    <div class="space-y-2">
                        @foreach($overrides as $ov)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                                <div>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-mono font-bold uppercase {{ $ov->action === 'pin' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $ov->action }}
                                    </span>
                                    <span class="font-mono text-slate-700 ml-1.5">{{ $ov->source_type }} &rarr; {{ $ov->target_type }} #{{ $ov->target_id }}</span>
                                </div>
                                <form action="{{ route('admin.seo.override.destroy', $ov) }}" method="POST" onsubmit="return confirm('Remove override?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 text-xs font-mono">Remove</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
