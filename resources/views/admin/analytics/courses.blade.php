@extends('layouts.admin')

@section('title', 'Course Performance Reports | ZebLearn LMS')
@section('page-title', 'Course Performance')

@section('content')
<div class="space-y-6">

    {{-- Subnavigation --}}
    @include('admin.analytics.partials.subnav')

    {{-- Filter Bar --}}
    @include('admin.analytics.partials.filter-bar', [
        'action' => route('admin.analytics.courses'),
        'exportType' => 'courses',
        'courses' => $courses,
        'showDateRange' => false,
        'filters' => $filters,
    ])

    {{-- Summary KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Courses Analyzed</span>
            <p class="text-3xl font-extrabold text-slate-900 font-heading mt-2">{{ $totalCoursesAnalyzed }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Enterprise & technology tracks</p>
        </div>

        <div class="bg-white rounded-2xl border border-purple-200/80 p-5 shadow-xs bg-purple-50/20">
            <span class="text-xs font-bold uppercase tracking-wider text-purple-700">Platform Average Completion</span>
            <p class="text-3xl font-extrabold text-purple-700 font-heading mt-2">{{ $avgPlatformCompletion }}%</p>
            <p class="text-[11px] text-purple-600 mt-1">Average graduation rate of enrolled students</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Download Dossier</span>
                <p class="text-xs text-slate-500 mt-1">Export full curriculum matrix</p>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.analytics.export', array_merge(['type' => 'courses', 'format' => 'csv'], array_filter($filters))) }}"
                    class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-800 transition-colors"
                >
                    CSV
                </a>
                <a
                    href="{{ route('admin.analytics.export', array_merge(['type' => 'courses', 'format' => 'pdf'], array_filter($filters))) }}"
                    class="px-3 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white transition-colors"
                >
                    PDF
                </a>
            </div>
        </div>
    </div>

    {{-- COURSE PERFORMANCE TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Course Performance Dossier</h3>
                <p class="text-xs text-slate-500">Detailed student progression, completion rates, quiz scores, and assignment evaluation metrics</p>
            </div>
        </div>

        @if ($reports->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                No courses found matching the selected filter criteria.
            </div>
        @else
            <div class="overflow-x-auto -mx-5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-y border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-5">Course</th>
                            <th class="py-3 px-3 text-center">Enrolled (App/Tot)</th>
                            <th class="py-3 px-3 text-center">Progress Status</th>
                            <th class="py-3 px-3 text-right">Completion Rate</th>
                            <th class="py-3 px-3 text-right">Avg Progress</th>
                            <th class="py-3 px-3 text-center">Quizzes</th>
                            <th class="py-3 px-5 text-center">Assignments</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($reports as $c)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.courses.edit', $c['course_id']) }}" class="font-bold text-slate-900 hover:text-[#FB6221] transition-colors">
                                            {{ $c['title'] }}
                                        </a>
                                        @if ($c['status'] === 'available')
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">Live</span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">{{ $c['status'] }}</span>
                                        @endif
                                    </div>
                                    <span class="block text-[11px] text-slate-400 mt-0.5">
                                        {{ $c['category'] ?? 'General' }} • {{ $c['published_lessons'] }} published lessons
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-center font-mono">
                                    <span class="font-bold text-emerald-600">{{ $c['approved_enrolled'] }}</span>
                                    <span class="text-slate-400">/ {{ $c['total_enrolled'] }}</span>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    <span class="inline-flex items-center gap-1.5 text-[11px]">
                                        <span class="font-semibold text-purple-700">{{ $c['completed_students'] }} done</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="text-slate-600">{{ $c['in_progress_students'] }} active</span>
                                    </span>
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-14 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $c['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="font-mono font-bold text-purple-700 text-[11px] w-8 text-right">{{ $c['completion_rate'] }}%</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-14 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $c['average_progress'] }}%"></div>
                                        </div>
                                        <span class="font-mono font-semibold text-slate-800 text-[11px] w-8 text-right">{{ $c['average_progress'] }}%</span>
                                    </div>
                                </td>

                                <td class="py-3.5 px-3 text-center">
                                    @if ($c['quiz_stats']['total_attempts'] > 0)
                                        <span class="font-mono text-slate-800 font-semibold">{{ $c['quiz_stats']['total_attempts'] }} att.</span>
                                        <span class="block text-[10px] text-emerald-600">{{ $c['quiz_stats']['avg_score'] }}% avg ({{ $c['quiz_stats']['pass_rate'] }}% pass)</span>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">No attempts</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-5 text-center">
                                    @if ($c['assignment_stats']['submissions'] > 0)
                                        <span class="font-mono text-slate-800 font-semibold">{{ $c['assignment_stats']['submissions'] }} subs</span>
                                        <span class="block text-[10px] text-blue-600">
                                            {{ $c['assignment_stats']['graded'] }} graded •
                                            @if ($c['assignment_stats']['avg_marks'] !== null)
                                                avg {{ $c['assignment_stats']['avg_marks'] }}
                                            @else
                                                pending
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px] italic">No submissions</span>
                                    @endif
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

