@extends('layouts.admin')

@section('title', 'Student Engagement Analytics | ZebLearn LMS')
@section('page-title', 'Student Engagement')

@section('content')
<div class="space-y-6">

    {{-- Subnavigation --}}
    @include('admin.analytics.partials.subnav')

    {{-- Filter Bar --}}
    @include('admin.analytics.partials.filter-bar', [
        'action' => route('admin.analytics.students'),
        'exportType' => 'students',
        'filters' => $filters,
    ])

    {{-- 1. ENGAGEMENT RATIO & RECENT ACTIVITY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Students --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Student Body</span>
            <p class="text-3xl font-extrabold text-slate-900 font-heading mt-2">
                {{ number_format($engagement['total_students']) }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">Total registered student profiles</p>
        </div>

        {{-- Active Students (30 Days) --}}
        <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-xs bg-emerald-50/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Active Students</span>
                <span class="text-[10px] font-mono font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full border border-emerald-200">
                    Last {{ $engagement['activity_window_days'] }} Days
                </span>
            </div>
            <p class="text-3xl font-extrabold text-emerald-700 font-heading mt-2">
                {{ number_format($engagement['active_students']) }}
            </p>
            <p class="text-[11px] text-emerald-700 mt-1">
                {{ $engagement['activity_rate'] }}% of total student base active
            </p>
        </div>

        {{-- Inactive Students --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Inactive Students</span>
            <p class="text-3xl font-extrabold text-slate-700 font-heading mt-2">
                {{ number_format($engagement['inactive_students']) }}
            </p>
            <p class="text-[11px] text-slate-400 mt-1">
                No lesson, quiz, assignment or enrollment activity in {{ $engagement['activity_window_days'] }} days
            </p>
        </div>

        {{-- Quiz Pass Rate --}}
        <div class="bg-white rounded-2xl border border-purple-200/80 p-5 shadow-xs bg-purple-50/20">
            <span class="text-xs font-bold uppercase tracking-wider text-purple-800">Quiz Pass Rate</span>
            <p class="text-3xl font-extrabold text-purple-700 font-heading mt-2">
                {{ $engagement['quiz_participation']['pass_rate'] }}%
            </p>
            <p class="text-[11px] text-purple-700 mt-1">
                {{ $engagement['quiz_participation']['passed'] }} passed of {{ $engagement['quiz_participation']['total_attempts'] }} submitted attempts
            </p>
        </div>
    </div>

    {{-- 2. PROGRESS BUCKETS & LESSON COMPLETIONS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Student Learning Progress Distribution --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-900 font-heading">Course Progress Distribution</h3>
                <p class="text-xs text-slate-500">Curriculum progression tiers across all approved enrollments</p>
            </div>

            <div class="space-y-3 py-2">
                @php
                    $maxBucket = max(max($engagement['progress_distribution']), 1);
                @endphp
                @foreach ($engagement['progress_distribution'] as $bucket => $count)
                    @php
                        $pct = max(3, (int) round(($count / $maxBucket) * 100));
                        $sharePct = $engagement['total_approved_enrollments'] > 0
                            ? (int) round(($count / $engagement['total_approved_enrollments']) * 100)
                            : 0;
                    @endphp
                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-700">{{ $bucket }} Progress</span>
                            <span class="font-mono text-slate-900 font-bold">
                                {{ $count }} <span class="text-slate-400 font-normal">({{ $sharePct }}%)</span>
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div
                                class="h-2.5 rounded-full transition-all duration-500 {{ $bucket === '100%' ? 'bg-emerald-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500' }}"
                                style="width: {{ $pct }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex items-center justify-between">
                <span>Total Active Track Enrollments: <strong>{{ $engagement['total_approved_enrollments'] }}</strong></span>
                <span class="text-emerald-600 font-semibold">100% Tier = Fully Completed</span>
            </div>
        </div>

        {{-- Monthly Lesson Completion Velocity --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4 flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Lesson Completion Trends</h3>
                    <p class="text-xs text-slate-500">Monthly student lesson completion milestones</p>
                </div>

                <div class="py-4">
                    @if ($engagement['lesson_completion_trend']->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-xs">
                            No lesson completions recorded in this timeframe.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($engagement['lesson_completion_trend'] as $item)
                                @php
                                    $bar = max(4, (int) round(($item['count'] / $engagement['max_lesson_completion']) * 100));
                                @endphp
                                <div class="space-y-1 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-700">{{ $item['label'] }}</span>
                                        <span class="font-mono font-bold text-slate-900">{{ number_format($item['count']) }} completed</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-[#FB6221] to-amber-400 h-2.5 rounded-full transition-all" style="width: {{ $bar }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                Tracked accurately from server-authoritative <code class="font-mono text-slate-700">lesson_progress</code> audit records.
            </div>
        </div>
    </div>

    {{-- 3. ASSESSMENT & ASSIGNMENT ENGAGEMENT SNAPSHOT --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Quiz Engagement Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-900 font-heading">Quiz Evaluation Metrics</h4>
                <span class="text-xs font-mono font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-lg">
                    Avg Score: {{ $engagement['quiz_participation']['avg_score'] }}%
                </span>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center pt-1">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Attempts</span>
                    <p class="text-xl font-bold text-slate-900 font-heading mt-1">{{ $engagement['quiz_participation']['total_attempts'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Passed</span>
                    <p class="text-xl font-bold text-emerald-800 font-heading mt-1">{{ $engagement['quiz_participation']['passed'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Failed</span>
                    <p class="text-xl font-bold text-rose-800 font-heading mt-1">{{ $engagement['quiz_participation']['failed'] }}</p>
                </div>
            </div>
        </div>

        {{-- Assignment Submissions Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h4 class="text-sm font-bold text-slate-900 font-heading">Assignment Activity</h4>
                <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg">
                    {{ $engagement['assignment_activity']['graded'] }} Evaluated
                </span>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center pt-1">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Total Subs</span>
                    <p class="text-xl font-bold text-slate-900 font-heading mt-1">{{ $engagement['assignment_activity']['total_submissions'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">On-Time</span>
                    <p class="text-xl font-bold text-emerald-800 font-heading mt-1">{{ $engagement['assignment_activity']['on_time'] }}</p>
                </div>
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Late</span>
                    <p class="text-xl font-bold text-amber-800 font-heading mt-1">{{ $engagement['assignment_activity']['late'] }}</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

