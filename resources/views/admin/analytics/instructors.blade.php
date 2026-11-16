@extends('layouts.admin')

@section('title', 'Instructor Performance Analytics | ZebLearn LMS')
@section('page-title', 'Instructor Performance')

@section('content')
<div class="space-y-6">

    {{-- Subnavigation --}}
    @include('admin.analytics.partials.subnav')

    {{-- Filter Bar --}}
    @include('admin.analytics.partials.filter-bar', [
        'action' => route('admin.analytics.instructors'),
        'exportType' => 'instructors',
        'showDateRange' => false,
        'filters' => $filters,
    ])

    {{-- 1. FACULTY AGGREGATE KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Instructors</span>
            <p class="text-3xl font-extrabold text-slate-900 font-heading mt-2">{{ $summary['total_instructors_analyzed'] }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Teaching faculty profiles</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Assigned Courses</span>
            <p class="text-3xl font-extrabold text-slate-900 font-heading mt-2">{{ $summary['total_assigned_courses'] }}</p>
            <p class="text-[11px] text-slate-500 mt-1">Total active teaching allocations</p>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-200/80 p-5 shadow-xs bg-emerald-50/20">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Evaluations Graded</span>
            <p class="text-3xl font-extrabold text-emerald-700 font-heading mt-2">{{ number_format($summary['total_graded_reviews']) }}</p>
            <p class="text-[11px] text-emerald-700 mt-1">Completed assignment submissions</p>
        </div>

        <div class="bg-white rounded-2xl border border-amber-200/80 p-5 shadow-xs bg-amber-50/20">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Pending Review Workload</span>
            <p class="text-3xl font-extrabold text-amber-700 font-heading mt-2">{{ number_format($summary['total_pending_workload']) }}</p>
            <p class="text-[11px] text-amber-800 mt-1">Awaiting evaluation in assigned tracks</p>
        </div>
    </div>

    {{-- 2. INSTRUCTORS DOSSIER TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-bold text-slate-900 font-heading">Faculty Teaching & Evaluation Dossier</h3>
                <p class="text-xs text-slate-500">Course allocation, student reach, grading backlogs, and turnaround time per instructor</p>
            </div>
        </div>

        @if ($instructors->isEmpty())
            <div class="py-12 text-center text-slate-400 text-xs">
                No instructors found.
            </div>
        @else
            <div class="overflow-x-auto -mx-5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-y border-slate-200/80 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-5">Instructor</th>
                            <th class="py-3 px-4">Assigned Courses</th>
                            <th class="py-3 px-4 text-center">Student Reach (App/Tot)</th>
                            <th class="py-3 px-4 text-center">Completed Students</th>
                            <th class="py-3 px-4 text-center">Graded Reviews</th>
                            <th class="py-3 px-4 text-center">Pending Workload</th>
                            <th class="py-3 px-5 text-right">Avg Turnaround</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($instructors as $inst)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($inst['name'], 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.instructors.show', $inst['instructor_id']) }}" class="font-bold text-slate-900 hover:text-[#FB6221]">
                                                {{ $inst['name'] }}
                                            </a>
                                            <span class="block text-[11px] text-slate-500 font-normal">
                                                {{ $inst['designation'] }} • {{ $inst['specialization'] }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if ($inst['assigned_courses']->isEmpty())
                                        <span class="text-slate-400 italic">None assigned</span>
                                    @else
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach ($inst['assigned_courses'] as $course)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                                    {{ $course['title'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono">
                                    <span class="font-bold text-emerald-600">{{ $inst['approved_students'] }}</span>
                                    <span class="text-slate-400">/ {{ $inst['total_students'] }}</span>
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono font-bold text-purple-700">
                                    {{ $inst['completed_students'] }}
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono font-semibold text-slate-800">
                                    {{ $inst['submissions_graded'] }}
                                </td>

                                <td class="py-3.5 px-4 text-center font-mono font-bold text-amber-600">
                                    {{ $inst['pending_workload'] }}
                                </td>

                                <td class="py-3.5 px-5 text-right font-mono">
                                    @if ($inst['avg_turnaround_hours'] !== null)
                                        <span class="font-semibold text-slate-900">{{ $inst['avg_turnaround_hours'] }} hrs</span>
                                    @else
                                        <span class="text-slate-400 italic">N/A</span>
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

