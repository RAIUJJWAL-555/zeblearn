@extends('layouts.admin')

@section('title', 'Migration Audit Logs | ZebLearn LMS')
@section('page-title', 'Migration Logs')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-1">
                AUDIT TRAIL
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Migration Audit Logs
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Complete trace of all crawled URLs, duplicate detections, incomplete items, and error reasons.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.blogs.migration.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-xs font-bold uppercase transition-colors">
                &larr; Migration Console
            </a>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.blogs.migration.logs') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search slug, title, or source URL..."
                    class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-800 placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] font-sans"
                />
            </div>
            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-800 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] font-sans">
                    <option value="">All Statuses</option>
                    <option value="imported" {{ request('status') === 'imported' ? 'selected' : '' }}>Imported</option>
                    <option value="incomplete" {{ request('status') === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
                    <option value="duplicate" {{ request('status') === 'duplicate' ? 'selected' : '' }}>Duplicate</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2 bg-[#0A0A0A] hover:bg-[#FB6221] text-white text-xs font-mono font-bold uppercase rounded-xl transition-colors">
                    Filter Logs
                </button>
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 font-mono text-[11px] uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Time</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Title / Slug</th>
                        <th class="py-3 px-4">Source URL</th>
                        <th class="py-3 px-4">Reason / Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 text-slate-400 whitespace-nowrap">
                                {{ $log->created_at->format('M d, H:i') }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($log->status === 'imported')
                                    <span class="px-2 py-0.5 rounded-md font-bold uppercase text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Imported
                                    </span>
                                @elseif($log->status === 'incomplete')
                                    <span class="px-2 py-0.5 rounded-md font-bold uppercase text-[10px] bg-amber-50 text-amber-700 border border-amber-200">
                                        Incomplete
                                    </span>
                                @elseif($log->status === 'duplicate')
                                    <span class="px-2 py-0.5 rounded-md font-bold uppercase text-[10px] bg-slate-100 text-slate-600 border border-slate-200">
                                        Duplicate
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md font-bold uppercase text-[10px] bg-rose-50 text-rose-700 border border-rose-200">
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 max-w-xs font-sans text-slate-900 font-bold truncate">
                                {{ $log->title ?: ($log->slug ?: '—') }}
                            </td>
                            <td class="py-3 px-4 max-w-xs text-slate-500 truncate">
                                <a href="{{ $log->source_url }}" target="_blank" class="hover:text-[#FB6221] underline">
                                    {{ $log->source_url }}
                                </a>
                            </td>
                            <td class="py-3 px-4 max-w-xs text-slate-600 font-sans text-xs truncate">
                                {{ $log->reason ?: '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-sans">
                                No migration logs recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
