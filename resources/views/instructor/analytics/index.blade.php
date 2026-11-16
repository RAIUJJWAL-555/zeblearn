@extends('layouts.instructor')

@section('title', 'Faculty Analytics & Performance | ZebLearn LMS')

@section('content')
<main class="flex-1 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Header & Export Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                    Faculty Academic Telemetry
                </div>
                <h1 class="text-2xl font-bold font-heading text-slate-900 mt-1">
                    Teaching & Course Performance
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">
                    Real-time metrics for your assigned curriculum tracks, student progress velocity, and assignment evaluation turnaround.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('instructor.analytics.export', ['format' => 'csv']) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors"
                >
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Export CSV</span>
                </a>
                <a
                    href="{{ route('instructor.analytics.export', ['format' => 'pdf']) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 hover:bg-slate-800 text-white transition-colors"
                >
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Download PDF</span>
                </a>
            </div>
        </div>

        {{-- 1. KPI SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Assigned Courses</span>
                <p class="text-3xl font-extrabold text-slate-900 font-heading mt-2">
                    {{ $dossier['assigned_courses_count'] }}
                </p>
                <p class="text-[11px] text-slate-500 mt-1">Active curriculum allocations</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Enrolled Students</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span class="text-3xl font-extrabold text-emerald-600 font-heading">
                        {{ $dossier['approved_students'] }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">/ {{ $dossier['total_students'] }} total</span>
                </div>
                <p class="text-[11px] text-purple-700 font-semibold mt-1">
                    {{ $dossier['completed_students'] }} students graduated with certificate
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Evaluations Completed</span>
                <p class="text-3xl font-extrabold text-slate-900 font-heading mt-2">
                    {{ $dossier['submissions_graded'] }}
                </p>
                <p class="text-[11px] text-slate-500 mt-1">Graded by you across all batches</p>
            </div>

            <div class="bg-white rounded-2xl border border-amber-200 p-5 shadow-xs bg-amber-50/20">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Pending Review Workload</span>
                <p class="text-3xl font-extrabold text-amber-700 font-heading mt-2">
                    {{ $dossier['pending_workload'] }}
                </p>
                <div class="mt-1 flex items-center justify-between text-[11px]">
                    <a href="{{ route('instructor.assignments.index') }}" class="font-semibold text-amber-800 hover:underline">
                        Review submissions &rarr;
                    </a>
                    @if ($dossier['avg_turnaround_hours'] !== null)
                        <span class="text-slate-500 font-mono">Avg: {{ $dossier['avg_turnaround_hours'] }}h</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. ASSIGNED COURSES PERFORMANCE DOSSIER --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Assigned Courses Telemetry</h3>
                    <p class="text-xs text-slate-500">Student enrollment, track progress, quiz pass rates, and assignment evaluation metrics</p>
                </div>
            </div>

            @if ($courseReports->isEmpty())
                <div class="py-12 text-center text-slate-400 text-xs">
                    You have not been assigned to any courses yet. When administrator allocates courses to your profile, their analytics will appear here.
                </div>
            @else
                <div class="overflow-x-auto -mx-5">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-y border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-5">Course Title</th>
                                <th class="py-3 px-3 text-center">Approved Students</th>
                                <th class="py-3 px-3 text-center">Completed</th>
                                <th class="py-3 px-3 text-right">Completion Rate</th>
                                <th class="py-3 px-3 text-right">Average Progress</th>
                                <th class="py-3 px-3 text-center">Quiz Avg Score</th>
                                <th class="py-3 px-5 text-center">Assignment Reviews</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($courseReports as $c)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-5">
                                        <a href="{{ route('instructor.courses.show', $c['course_id']) }}" class="font-bold text-slate-900 hover:text-[#FB6221]">
                                            {{ $c['title'] }}
                                        </a>
                                        <span class="block text-[11px] text-slate-400">
                                            {{ $c['category'] ?? 'General' }} • {{ $c['published_lessons'] }} lessons
                                        </span>
                                    </td>

                                    <td class="py-3.5 px-3 text-center font-mono font-bold text-emerald-600">
                                        {{ $c['approved_enrolled'] }}
                                    </td>

                                    <td class="py-3.5 px-3 text-center font-mono font-bold text-purple-700">
                                        {{ $c['completed_students'] }}
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
                                            <span class="font-mono text-slate-900 font-semibold">{{ $c['quiz_stats']['avg_score'] }}%</span>
                                            <span class="block text-[10px] text-slate-400">({{ $c['quiz_stats']['total_attempts'] }} attempts)</span>
                                        @else
                                            <span class="text-slate-400 italic">No attempts</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-5 text-center">
                                        @if ($c['assignment_stats']['submissions'] > 0)
                                            <span class="font-mono text-slate-900 font-semibold">{{ $c['assignment_stats']['graded'] }} / {{ $c['assignment_stats']['submissions'] }} graded</span>
                                            @if ($c['assignment_stats']['pending_grading'] > 0)
                                                <span class="block text-[10px] text-amber-600 font-bold">({{ $c['assignment_stats']['pending_grading'] }} pending)</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic">No submissions</span>
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
</main>
@endsection

