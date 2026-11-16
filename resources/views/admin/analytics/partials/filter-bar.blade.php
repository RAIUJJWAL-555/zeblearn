@props([
    'action' => url()->current(),
    'exportType' => null,
    'courses' => null,
    'statuses' => null,
    'statusParam' => 'status',
    'statusLabel' => 'Status',
    'showDateRange' => true,
    'filters' => [],
])

@php
    $datePreset = $filters['date_range'] ?? 'all_time';
    $exportParams = array_filter($filters);
    $csvExportUrl = $exportType ? route('admin.analytics.export', array_merge(['type' => $exportType, 'format' => 'csv'], $exportParams)) : null;
    $pdfExportUrl = $exportType ? route('admin.analytics.export', array_merge(['type' => $exportType, 'format' => 'pdf'], $exportParams)) : null;
@endphp

<div
    x-data="{
        datePreset: '{{ $datePreset }}',
        showCustomDates: '{{ $datePreset }}' === 'custom',
        exportOpen: false
    }"
    class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs mb-6"
>
    <form method="GET" action="{{ $action }}" class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-orange-50 text-[#FB6221] border border-orange-100">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                    Filter Analytics & Generate Reports
                </h3>
            </div>

            @if ($exportType)
                <div class="relative" @click.outside="exportOpen = false">
                    <button
                        type="button"
                        @click="exportOpen = !exportOpen"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition-colors shadow-xs"
                    >
                        <svg class="w-3.5 h-3.5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Export Report</span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        x-show="exportOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-lg border border-slate-200 py-1.5 z-30"
                        style="display: none;"
                    >
                        <a
                            href="{{ $csvExportUrl }}"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900"
                        >
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Download CSV (Data)</span>
                        </a>
                        <a
                            href="{{ $pdfExportUrl }}"
                            class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-900 border-t border-slate-100"
                        >
                            <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span>Download PDF (Print)</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-end">
            {{-- Date Range Preset --}}
            @if ($showDateRange)
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        Time Period
                    </label>
                    <select
                        name="date_range"
                        x-model="datePreset"
                        @change="showCustomDates = (datePreset === 'custom')"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    >
                        <option value="all_time" @selected($datePreset === 'all_time')>All Time</option>
                        <option value="today" @selected($datePreset === 'today')>Today</option>
                        <option value="yesterday" @selected($datePreset === 'yesterday')>Yesterday</option>
                        <option value="last_7_days" @selected($datePreset === 'last_7_days')>Last 7 Days</option>
                        <option value="last_30_days" @selected($datePreset === 'last_30_days')>Last 30 Days</option>
                        <option value="last_90_days" @selected($datePreset === 'last_90_days')>Last 90 Days</option>
                        <option value="this_month" @selected($datePreset === 'this_month')>This Month</option>
                        <option value="last_month" @selected($datePreset === 'last_month')>Last Month</option>
                        <option value="this_year" @selected($datePreset === 'this_year')>This Year</option>
                        <option value="custom" @selected($datePreset === 'custom')>Custom Range...</option>
                    </select>
                </div>
            @endif

            {{-- Course Filter (if passed) --}}
            @if ($courses && $courses->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        Course Filter
                    </label>
                    <select
                        name="course_id"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    >
                        <option value="">All Courses</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}" @selected(($filters['course_id'] ?? '') == $c->id)>
                                {{ $c->name ?? $c->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Status Filter (if passed) --}}
            @if ($statuses)
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        {{ $statusLabel }}
                    </label>
                    <select
                        name="{{ $statusParam }}"
                        class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2.5 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221] transition-all"
                    >
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st }}" @selected(($filters[$statusParam] ?? '') === $st)>
                                {{ ucfirst(str_replace('_', ' ', $st)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Actions: Apply & Reset --}}
            <div class="flex items-center gap-2">
                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#FB6221] hover:bg-[#e05417] shadow-sm shadow-orange-500/20 transition-all focus:ring-2 focus:ring-[#FB6221]"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Apply Filter</span>
                </button>

                @if (! empty($filters))
                    <a
                        href="{{ $action }}"
                        class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 hover:text-slate-900 transition-colors"
                        title="Reset all filters"
                    >
                        Reset
                    </a>
                @endif
            </div>
        </div>

        {{-- Custom Date Inputs (shown only when custom preset selected) --}}
        <div x-show="showCustomDates" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-100">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    Start Date
                </label>
                <input
                    type="date"
                    name="start_date"
                    value="{{ $filters['start_date'] ?? '' }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">
                    End Date
                </label>
                <input
                    type="date"
                    name="end_date"
                    value="{{ $filters['end_date'] ?? '' }}"
                    class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50/50 py-2 px-3 focus:bg-white focus:ring-2 focus:ring-[#FB6221]/20 focus:border-[#FB6221]"
                />
            </div>
        </div>
    </form>
</div>
