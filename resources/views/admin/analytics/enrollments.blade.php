@extends('layouts.admin')

@section('title', 'Enrollment Analytics | ZebLearn LMS')
@section('page-title', 'Enrollment Analytics')

@section('content')
<div class="space-y-6">

    {{-- Subnavigation --}}
    @include('admin.analytics.partials.subnav')

    {{-- Filter Bar --}}
    @include('admin.analytics.partials.filter-bar', [
        'action' => route('admin.analytics.enrollments'),
        'exportType' => 'enrollments',
        'courses' => $courses,
        'statuses' => $enrollmentStatuses,
        'statusParam' => 'status',
        'statusLabel' => 'Enrollment Status',
        'filters' => $filters,
    ])

    {{-- 1. ENROLLMENT STATUS CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Applications</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading mt-1">{{ number_format($enrollments['total_enrollments']) }}</p>
            <span class="text-[11px] text-slate-500 mt-1 block">All registered requests</span>
        </div>
        <div class="bg-white rounded-2xl border border-emerald-200/80 p-4 sm:p-5 shadow-xs bg-emerald-50/20">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Approved</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-700 font-heading mt-1">{{ number_format($enrollments['approved_count']) }}</p>
            <span class="text-[11px] text-emerald-600 font-semibold mt-1 block">{{ $enrollments['approval_rate'] }}% approval rate</span>
        </div>
        <div class="bg-white rounded-2xl border border-amber-200/80 p-4 sm:p-5 shadow-xs bg-amber-50/20">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Pending</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-700 font-heading mt-1">{{ number_format($enrollments['pending_count']) }}</p>
            <a href="{{ route('admin.enrollments.index') }}" class="text-[11px] text-amber-800 font-semibold hover:underline mt-1 block">Review queue &rarr;</a>
        </div>
        <div class="bg-white rounded-2xl border border-rose-200/80 p-4 sm:p-5 shadow-xs bg-rose-50/20">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Rejected</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-rose-700 font-heading mt-1">{{ number_format($enrollments['rejected_count']) }}</p>
            <span class="text-[11px] text-rose-600 mt-1 block">Declined or ineligible</span>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Cancelled</span>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-700 font-heading mt-1">{{ number_format($enrollments['cancelled_count']) }}</p>
            <span class="text-[11px] text-slate-500 mt-1 block">Student withdrawn</span>
        </div>
    </div>

    {{-- 2. MONTHLY ENROLLMENT VELOCITY CHART --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Monthly Application Volume & Approvals</h3>
                <p class="text-xs text-slate-500">Comparison of incoming enrollment requests versus approved admissions</p>
            </div>
        </div>

        @if ($enrollments['monthly_trend']->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                No enrollments recorded for this period.
            </div>
        @else
            <div class="space-y-4 py-2">
                @foreach ($enrollments['monthly_trend'] as $item)
                    @php
                        $totalPct = max(3, (int) round(($item['total'] / $enrollments['max_monthly_enrollment']) * 100));
                        $approvedPct = $item['total'] > 0 ? (int) round(($item['approved'] / $item['total']) * 100) : 0;
                    @endphp
                    <div class="grid grid-cols-12 gap-3 items-center text-xs">
                        <div class="col-span-3 sm:col-span-2 font-medium text-slate-700">
                            {{ $item['label'] }}
                        </div>
                        <div class="col-span-6 sm:col-span-8">
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden flex">
                                <div class="bg-emerald-500 h-3" style="width: {{ $approvedPct }}%"></div>
                                <div class="bg-slate-300 h-3 flex-1"></div>
                            </div>
                        </div>
                        <div class="col-span-3 sm:col-span-2 text-right font-mono text-slate-900">
                            <strong>{{ $item['approved'] }}</strong> <span class="text-slate-400">/ {{ $item['total'] }}</span>
                            <span class="block text-[10px] text-emerald-600 font-semibold">{{ $approvedPct }}% app.</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center gap-4 text-[11px] text-slate-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Approved</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-slate-300"></span> Pending / Other</span>
            </div>
        @endif
    </div>

    {{-- 3. COURSE-WISE ENROLLMENT PERFORMANCE TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Course Enrollment Performance</h3>
                <p class="text-xs text-slate-500">Applications, approval ratios, and rejection counts across courses</p>
            </div>
        </div>

        @if ($enrollments['course_enrollments']->isEmpty())
            <div class="py-8 text-center text-slate-400 text-xs">
                No course enrollments found matching current criteria.
            </div>
        @else
            <div class="overflow-x-auto -mx-5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-y border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-5">Course Title</th>
                            <th class="py-3 px-4 text-center">Total Requests</th>
                            <th class="py-3 px-4 text-center">Approved</th>
                            <th class="py-3 px-4 text-center">Pending</th>
                            <th class="py-3 px-4 text-center">Rejected</th>
                            <th class="py-3 px-5 text-right">Approval Ratio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($enrollments['course_enrollments'] as $row)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5 font-semibold text-slate-900">
                                    <a href="{{ route('admin.courses.edit', $row['course_id']) }}" class="hover:text-[#FB6221]">
                                        {{ $row['title'] }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-slate-900">
                                    {{ number_format($row['total_enrollments']) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-semibold text-emerald-600">
                                    {{ number_format($row['approved_count']) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-amber-600">
                                    {{ number_format($row['pending_count']) }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-rose-600">
                                    {{ number_format($row['rejected_count']) }}
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $row['approval_rate'] }}%"></div>
                                        </div>
                                        <span class="font-mono text-slate-700 font-semibold text-[11px] w-8 text-right">{{ $row['approval_rate'] }}%</span>
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

