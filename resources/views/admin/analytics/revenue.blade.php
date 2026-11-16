@extends('layouts.admin')

@section('title', 'Revenue & Financial Analytics | ZebLearn LMS')
@section('page-title', 'Revenue Analytics')

@section('content')
<div class="space-y-6">

    {{-- Subnavigation --}}
    @include('admin.analytics.partials.subnav')

    {{-- Filter Bar --}}
    @include('admin.analytics.partials.filter-bar', [
        'action' => route('admin.analytics.revenue'),
        'exportType' => 'revenue',
        'courses' => $courses,
        'statuses' => $paymentStatuses,
        'statusParam' => 'payment_status',
        'statusLabel' => 'Payment Status',
        'filters' => $filters,
    ])

    {{-- 1. FINANCIAL KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Collected Revenue --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Collected Revenue</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-emerald-600 font-heading">
                    {{ $financials['formatted_collected_revenue'] }}
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                {{ $financials['paid_count'] }} verified payments • INR collected
            </p>
        </div>

        {{-- Pending Payments --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pending Amount</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600 border border-amber-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-amber-600 font-heading">
                    {{ $financials['formatted_pending_amount'] }}
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                {{ $financials['pending_count'] }} orders awaiting verification
            </p>
        </div>

        {{-- Failed Transactions --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Failed Volume</span>
                <span class="p-2.5 rounded-xl bg-rose-50 text-rose-600 border border-rose-100">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-rose-600 font-heading">
                    {{ $financials['formatted_failed_amount'] }}
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                {{ $financials['failed_count'] }} rejected or timed-out orders
            </p>
        </div>

        {{-- Refunds Recorded --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Refunds Processed</span>
                <span class="p-2.5 rounded-xl bg-slate-100 text-slate-700 border border-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H4m0 0l3-3m-3 3l3 3m5 4v1a4 4 0 004 4h8m0 0l-3-3m3 3l-3 3" />
                    </svg>
                </span>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-slate-800 font-heading">
                    {{ $financials['formatted_refunded_amount'] }}
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2">
                {{ $financials['refunded_count'] }} refunded transactions recorded
            </p>
        </div>
    </div>

    {{-- 2. MONTHLY REVENUE VELOCITY CHART & BREAKDOWN --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Monthly Revenue Realization</h3>
                <p class="text-xs text-slate-500">Actual reconciled revenue by month (strictly excludes pending/failed payments)</p>
            </div>
            <span class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-lg">
                Reconciliation: 100% Real
            </span>
        </div>

        @if ($financials['monthly_trend']->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                No verified payments found for the selected filter criteria.
            </div>
        @else
            <div class="space-y-4 py-2">
                @foreach ($financials['monthly_trend'] as $item)
                    @php
                        $barPct = max(3, (int) round(($item['amount'] / $financials['max_monthly_revenue']) * 100));
                    @endphp
                    <div class="grid grid-cols-12 gap-3 items-center text-xs">
                        <div class="col-span-3 sm:col-span-2 font-medium text-slate-700">
                            {{ $item['label'] }}
                        </div>
                        <div class="col-span-6 sm:col-span-8">
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-emerald-500 to-teal-400 h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $barPct }}%"
                                ></div>
                            </div>
                        </div>
                        <div class="col-span-3 sm:col-span-2 text-right font-mono font-bold text-slate-900">
                            {{ $item['formatted_amount'] }}
                            <span class="block text-[10px] text-slate-400 font-normal">({{ $item['transactions'] }} paid)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- 3. COURSE-WISE REVENUE CONTRIBUTION TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Course-wise Revenue Contribution</h3>
                <p class="text-xs text-slate-500">Breakdown of gross collected revenue attributed per course catalog track</p>
            </div>
        </div>

        @if ($financials['course_revenue']->isEmpty())
            <div class="py-8 text-center text-slate-400 text-xs">
                No revenue recorded for courses matching current criteria.
            </div>
        @else
            <div class="overflow-x-auto -mx-5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-y border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-5">Course Title</th>
                            <th class="py-3 px-4 text-center">Paid Transactions</th>
                            <th class="py-3 px-4 text-right">Collected Revenue</th>
                            <th class="py-3 px-5 text-right">Revenue Share</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($financials['course_revenue'] as $row)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-semibold text-slate-900">
                                    <a href="{{ route('admin.courses.edit', $row['course_id']) }}" class="hover:text-[#FB6221] transition-colors">
                                        {{ $row['title'] }}
                                    </a>
                                    <span class="block text-[11px] font-mono text-slate-400 font-normal">ID #{{ $row['course_id'] }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-700">
                                    {{ number_format($row['paid_count']) }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-600">
                                    {{ $row['formatted_amount'] }}
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $row['percentage'] }}%"></div>
                                        </div>
                                        <span class="font-mono text-slate-600 font-semibold text-[11px] w-8 text-right">{{ $row['percentage'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

