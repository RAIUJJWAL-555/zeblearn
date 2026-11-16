@extends('layouts.admin')

@section('title', 'Platform Analytics & Reports | ZebLearn LMS')
@section('page-title', 'Analytics Hub')

@section('content')
<div class="space-y-6">

    {{-- Subnavigation --}}
    @include('admin.analytics.partials.subnav')

    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A0A0A] via-[#141414] to-[#221207] text-white p-6 sm:p-8 shadow-xl border border-white/10">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-xs font-mono text-[#FB6221]">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                    <span>PLATFORM TELEMETRY & REPORTING HUB</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white tracking-tight">
                    LMS Operational Analytics
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Centralized, authoritative intelligence across platform enrollment velocity, verified revenue reconciliation, course completion rates, and faculty performance.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a
                    href="{{ route('admin.analytics.export', ['type' => 'enrollments', 'format' => 'csv']) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white/10 hover:bg-white/20 text-white border border-white/10 transition-colors"
                >
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Enrollments CSV</span>
                </a>
                <a
                    href="{{ route('admin.analytics.export', ['type' => 'revenue', 'format' => 'pdf']) }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-[#FB6221] hover:bg-[#e05417] text-white shadow-sm shadow-orange-500/20 transition-all"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Revenue PDF</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    @include('admin.analytics.partials.filter-bar', [
        'action' => route('admin.analytics.index'),
        'exportType' => 'enrollments',
        'courses' => $courses,
        'filters' => $filters,
    ])

    {{-- 1. PRIMARY PLATFORM OVERVIEW METRIC CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total & Active Students --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-[#FB6221]/30 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Students</span>
                <span class="p-2.5 rounded-xl bg-orange-50 text-[#FB6221] border border-orange-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 font-heading">
                    {{ number_format($overview['total_students']) }}
                </span>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                    {{ $overview['active_students'] }} Active ({{ $overview['activity_window_days'] }}d)
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                {{ $overview['inactive_students'] }} inactive students without activity in last {{ $overview['activity_window_days'] }} days
            </p>
        </div>

        {{-- Verified Revenue (INR) --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Collected Revenue</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-emerald-600 font-heading">
                    {{ $overview['formatted_total_revenue'] }}
                </span>
            </div>
            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500">
                <span>{{ $financials['paid_count'] }} paid orders</span>
                <span class="font-semibold text-emerald-700">{{ $financials['success_rate'] }}% success rate</span>
            </div>
        </div>

        {{-- Total Enrollments --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Enrollments</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 font-heading">
                    {{ number_format($overview['total_enrollments']) }}
                </span>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                    {{ $overview['approved_enrollments'] }} Approved
                </span>
            </div>
            <p class="text-[11px] text-amber-600 font-medium mt-2">
                {{ $overview['pending_enrollments'] }} pending verification reviews
            </p>
        </div>

        {{-- Course Completion Rate --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden group hover:border-purple-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Completion Rate</span>
                <span class="p-2.5 rounded-xl bg-purple-50 text-purple-600 border border-purple-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-slate-900 font-heading">
                    {{ $overview['course_completion_rate'] }}%
                </span>
                <span class="text-xs font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-100">
                    Certified
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                Percentage of approved students who completed track
            </p>
        </div>
    </div>

    {{-- 2. SECONDARY METRIC CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Courses Catalog</span>
            <p class="text-xl font-bold text-slate-900 font-heading mt-1">{{ $overview['total_courses'] }}</p>
            <span class="text-[11px] text-slate-500">{{ $overview['published_courses'] }} published • {{ $overview['draft_courses'] }} draft</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Instructors</span>
            <p class="text-xl font-bold text-slate-900 font-heading mt-1">{{ $overview['total_instructors'] }}</p>
            <a href="{{ route('admin.analytics.instructors') }}" class="text-[11px] text-[#FB6221] hover:underline">View faculty metrics &rarr;</a>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Pending Amount</span>
            <p class="text-xl font-bold text-amber-600 font-heading mt-1">{{ $financials['formatted_pending_amount'] }}</p>
            <span class="text-[11px] text-slate-500">{{ $financials['pending_count'] }} awaiting verification</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/80 p-4 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Refunds Recorded</span>
            <p class="text-xl font-bold text-rose-600 font-heading mt-1">{{ $financials['formatted_refunded_amount'] }}</p>
            <span class="text-[11px] text-slate-500">{{ $financials['refunded_count'] }} refunded transactions</span>
        </div>
    </div>

    {{-- 3. VISUAL TELEMETRY: REVENUE TRENDS & ENROLLMENT BREAKDOWN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Monthly Revenue Trend Chart --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Verified Revenue Velocity</h3>
                    <p class="text-xs text-slate-500">Monthly collected fees (INR) from successful student payments</p>
                </div>
                <a href="{{ route('admin.analytics.revenue') }}" class="text-xs font-semibold text-[#FB6221] hover:underline">
                    Detailed Finance &rarr;
                </a>
            </div>

            <div class="py-6">
                @if ($financials['monthly_trend']->isEmpty())
                    <div class="py-12 text-center text-slate-400 text-xs">
                        No payment activity recorded for this date range.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($financials['monthly_trend'] as $month)
                            @php
                                $barPct = max(4, (int) round(($month['amount'] / $financials['max_monthly_revenue']) * 100));
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-slate-700">{{ $month['label'] }}</span>
                                    <span class="font-mono font-bold text-slate-900">{{ $month['formatted_amount'] }} <span class="text-[10px] text-slate-400 font-normal">({{ $month['transactions'] }} tx)</span></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                    <div
                                        class="bg-gradient-to-r from-emerald-500 to-teal-400 h-2.5 rounded-full transition-all duration-500"
                                        style="width: {{ $barPct }}%"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Total Collected: <strong class="text-slate-900">{{ $financials['formatted_collected_revenue'] }}</strong></span>
                <span>Success Rate: <strong class="text-emerald-600">{{ $financials['success_rate'] }}%</strong></span>
            </div>
        </div>

        {{-- Enrollment Status Distribution & Monthly Velocity --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Enrollment Pipeline</h3>
                    <p class="text-xs text-slate-500">Student enrollment status breakdown and verification approvals</p>
                </div>
                <a href="{{ route('admin.analytics.enrollments') }}" class="text-xs font-semibold text-[#FB6221] hover:underline">
                    Detailed Enrollments &rarr;
                </a>
            </div>

            <div class="py-6 space-y-4">
                {{-- Status Pills --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Approved</span>
                        <p class="text-xl font-extrabold text-emerald-800 font-heading mt-0.5">{{ $enrollments['approved_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Pending</span>
                        <p class="text-xl font-extrabold text-amber-800 font-heading mt-0.5">{{ $enrollments['pending_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-rose-50 border border-rose-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Rejected</span>
                        <p class="text-xl font-extrabold text-rose-800 font-heading mt-0.5">{{ $enrollments['rejected_count'] }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-700">Cancelled</span>
                        <p class="text-xl font-extrabold text-slate-800 font-heading mt-0.5">{{ $enrollments['cancelled_count'] }}</p>
                    </div>
                </div>

                {{-- Approval Velocity Bar --}}
                <div class="space-y-1.5 pt-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700">Enrollment Approval Ratio</span>
                        <span class="font-mono font-bold text-slate-900">{{ $enrollments['approval_rate'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden flex">
                        <div class="bg-emerald-500 h-3" style="width: {{ $enrollments['approval_rate'] }}%"></div>
                        <div class="bg-amber-400 h-3" style="width: {{ $enrollments['total_enrollments'] > 0 ? round(($enrollments['pending_count'] / $enrollments['total_enrollments']) * 100) : 0 }}%"></div>
                        <div class="bg-rose-400 h-3" style="width: {{ $enrollments['total_enrollments'] > 0 ? round(($enrollments['rejected_count'] / $enrollments['total_enrollments']) * 100) : 0 }}%"></div>
                    </div>
                    <div class="flex items-center gap-3 text-[10px] text-slate-400 pt-1">
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Approved</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Pending</span>
                        <span class="inline-flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-400"></span> Rejected</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Total Applications: <strong class="text-slate-900">{{ $enrollments['total_enrollments'] }}</strong></span>
                <a href="{{ route('admin.enrollments.index') }}" class="font-semibold text-blue-600 hover:underline">Manage Pending &rarr;</a>
            </div>
        </div>
    </div>

    {{-- 4. QUICK SHORTCUTS TO DETAILED MODULE ANALYTICS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a
            href="{{ route('admin.analytics.courses') }}"
            class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all group"
        >
            <div class="flex items-center justify-between">
                <span class="p-2.5 rounded-xl bg-orange-50 text-[#FB6221] group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </span>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-[#FB6221]">&rarr;</span>
            </div>
            <h4 class="text-sm font-bold text-slate-900 font-heading mt-3">Course Performance</h4>
            <p class="text-xs text-slate-500 mt-1">Review completion percentages, quiz pass rates, and assignment grading stats across all courses.</p>
        </a>

        <a
            href="{{ route('admin.analytics.students') }}"
            class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all group"
        >
            <div class="flex items-center justify-between">
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-blue-600">&rarr;</span>
            </div>
            <h4 class="text-sm font-bold text-slate-900 font-heading mt-3">Student Engagement</h4>
            <p class="text-xs text-slate-500 mt-1">Inspect active/inactive ratios, lesson completion frequency, and progress bucket distributions.</p>
        </a>

        <a
            href="{{ route('admin.analytics.instructors') }}"
            class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all group"
        >
            <div class="flex items-center justify-between">
                <span class="p-2.5 rounded-xl bg-purple-50 text-purple-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </span>
                <span class="text-xs font-semibold text-slate-400 group-hover:text-purple-600">&rarr;</span>
            </div>
            <h4 class="text-sm font-bold text-slate-900 font-heading mt-3">Faculty & Grading Workload</h4>
            <p class="text-xs text-slate-500 mt-1">Track assigned teaching loads, pending submission backlogs, and turnaround hours per instructor.</p>
        </a>
    </div>

</div>
@endsection

