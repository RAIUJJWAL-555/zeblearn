@extends('layouts.app')

@section('title', 'My Learning Analytics | ZebLearn LMS')

@section('content')
<main class="py-10 bg-[#F8F9FA] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- Top Navigation Breadcrumb & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                    <a href="{{ route('student.dashboard') }}" class="hover:text-slate-900 transition-colors">Student Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('student.profile.show') }}" class="hover:text-slate-900 transition-colors">Profile</a>
                    <span>/</span>
                    <span class="text-[#FB6221] font-semibold">Learning Analytics</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-slate-900">
                    My Learning Analytics & Performance
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Your personal curriculum telemetry, milestone achievements, assessment scores, and lesson completion timeline.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <a
                    href="{{ route('student.profile.learning') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors shadow-xs"
                >
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <span>Learning Profile</span>
                </a>
                <a
                    href="{{ route('student.certificates.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-[#0A0A0A] text-white hover:bg-slate-800 transition-colors shadow-xs"
                >
                    <svg class="w-4 h-4 text-[#FB6221]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span>My Certificates ({{ $analytics['certificates_count'] }})</span>
                </a>
            </div>
        </div>

        {{-- 1. OVERALL PROGRESS & TOP METRIC CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Overall Curriculum Completion Gauge --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Overall Completion</span>
                    <span class="p-2.5 rounded-xl bg-purple-50 text-purple-600 border border-purple-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-purple-700 font-heading">
                        {{ $analytics['overall_progress_percentage'] }}%
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $analytics['overall_progress_percentage'] }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    {{ $analytics['completed_lessons'] }} of {{ $analytics['total_lessons'] }} published lessons completed
                </p>
            </div>

            {{-- Course Enrollment Status --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Course Tracks</span>
                    <span class="p-2.5 rounded-xl bg-orange-50 text-[#FB6221] border border-orange-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900 font-heading">
                        {{ $analytics['approved_count'] }}
                    </span>
                    <span class="text-xs text-slate-500">Approved</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    {{ $analytics['completed_courses_count'] }} graduated • {{ $analytics['active_courses_count'] }} in progress
                </p>
            </div>

            {{-- Quiz Performance --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Quiz Performance</span>
                    <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-emerald-600 font-heading">
                        {{ $analytics['quiz_stats']['avg_score'] }}%
                    </span>
                    <span class="text-xs text-slate-500">Avg Score</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    {{ $analytics['quiz_stats']['passed'] }} passed of {{ $analytics['quiz_stats']['total_attempts'] }} submitted attempts
                </p>
            </div>

            {{-- Assignment Grading --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Assignments</span>
                    <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-600 font-heading">
                        {{ $analytics['assignment_stats']['graded_count'] }}
                    </span>
                    <span class="text-xs text-slate-500">Graded</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-2">
                    {{ $analytics['assignment_stats']['total_submissions'] }} submissions submitted •
                    @if ($analytics['assignment_stats']['avg_marks'] !== null)
                        Avg marks: {{ $analytics['assignment_stats']['avg_marks'] }}
                    @else
                        awaiting reviews
                    @endif
                </p>
            </div>
        </div>

        {{-- 2. LEARNING ACTIVITY TIMELINE & COURSE BREAKDOWN --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Course Progress Breakdown --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 font-heading">Enrolled Curriculum Tracks</h3>
                        <p class="text-xs text-slate-500">Track-by-track lesson completion and certification status</p>
                    </div>
                </div>

                @if (empty($analytics['course_analytics']))
                    <div class="py-12 text-center text-slate-400 text-xs">
                        You are not currently enrolled in any courses. Browse the catalog to start learning!
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($analytics['course_analytics'] as $item)
                            @php
                                $c = $item['course'];
                                $p = $item['progress'];
                            @endphp
                            <div class="p-4 rounded-xl border border-slate-200/80 bg-slate-50/40 hover:bg-white hover:border-slate-300 transition-all space-y-3">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 font-heading">{{ $c->title }}</h4>
                                        <span class="text-[11px] text-slate-500">{{ $c->category }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($item['has_certificate'])
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Certified
                                            </span>
                                        @endif
                                        <a
                                            href="{{ route('student.courses.show', $c->slug) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#FB6221] text-white hover:bg-[#e05417] transition-colors"
                                        >
                                            Continue &rarr;
                                        </a>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-600 font-medium">{{ $p['completed_lessons'] }} of {{ $p['total_lessons'] }} lessons completed</span>
                                        <span class="font-mono font-bold text-slate-900">{{ $p['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 to-[#FB6221] h-2 rounded-full transition-all duration-500" style="width: {{ $p['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Monthly Activity Timeline --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-xs space-y-4 flex flex-col justify-between">
                <div>
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 font-heading">Learning Velocity</h3>
                        <p class="text-xs text-slate-500">Lesson completions by month</p>
                    </div>

                    <div class="py-4">
                        @if ($analytics['activity_timeline']->isEmpty())
                            <div class="py-12 text-center text-slate-400 text-xs">
                                No completions recorded in the past 6 months.
                            </div>
                        @else
                            @php
                                $maxCount = max($analytics['activity_timeline']->max('count') ?? 0, 1);
                            @endphp
                            <div class="space-y-3">
                                @foreach ($analytics['activity_timeline'] as $row)
                                    @php
                                        $bar = max(4, (int) round(($row['count'] / $maxCount) * 100));
                                    @endphp
                                    <div class="space-y-1 text-xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium text-slate-700">{{ $row['label'] }}</span>
                                            <span class="font-mono font-bold text-slate-900">{{ $row['count'] }} lessons</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-purple-600 to-indigo-500 h-2 rounded-full" style="width: {{ $bar }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 text-[11px] text-slate-400 text-center">
                    Keep up consistent study to unlock certificates faster!
                </div>
            </div>

        </div>

    </div>
</main>
@endsection
