@extends('layouts.instructor')

@section('title', 'Assignment Reviews & Grading | ZebLearn LMS')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Portal</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Assignment Reviews</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Student Assignment Submissions
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Evaluate student coursework, provide constructive feedback, and assign grades for your teaching courses.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-mono font-bold bg-amber-50 border border-amber-200 text-amber-800">
                {{ $pendingSubmissions }} Pending Review
            </span>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    {{-- SEARCH & FILTER TOOLBAR --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('instructor.assignments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-4 relative">
                <input
                    type="text"
                    name="student"
                    value="{{ $currentSearch }}"
                    placeholder="Search by student name or email..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >
                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                    <x-ui.icon name="magnifying-glass" size="xs" />
                </div>
            </div>

            <div class="sm:col-span-3">
                <select
                    name="course_id"
                    class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >
                    <option value="">All Assigned Courses</option>
                    @foreach ($assignedCourses as $c)
                        <option value="{{ $c->id }}" {{ (string) $currentCourse === (string) $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <select
                    name="status"
                    class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >
                    <option value="">All Review Statuses</option>
                    <option value="submitted" {{ $currentStatus === 'submitted' ? 'selected' : '' }}>Submitted (Awaiting Review)</option>
                    <option value="under_review" {{ $currentStatus === 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="graded" {{ $currentStatus === 'graded' ? 'selected' : '' }}>Graded</option>
                    <option value="resubmission_requested" {{ $currentStatus === 'resubmission_requested' ? 'selected' : '' }}>Resubmission Requested</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <x-ui.button type="submit" variant="dark" size="sm" class="w-full">
                    Filter
                </x-ui.button>
                @if ($currentSearch || $currentCourse || $currentStatus)
                    <a
                        href="{{ route('instructor.assignments.index') }}"
                        class="p-2 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors"
                        title="Reset Filters"
                    >
                        <x-ui.icon name="x-mark" size="sm" />
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- SUBMISSIONS TABLE --}}
    <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
        @if ($submissions->isEmpty())
            <div class="p-8">
                <x-ui.empty-state
                    title="No Submissions Found"
                    description="No student assignment submissions match the selected filters."
                    icon="document-text"
                />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Candidate</th>
                            <th class="py-3.5 px-4 sm:px-6">Course &amp; Task</th>
                            <th class="py-3.5 px-4 sm:px-6">Submitted At</th>
                            <th class="py-3.5 px-4 sm:px-6">Status &amp; Score</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($submissions as $sub)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-800 text-white flex items-center justify-center font-heading font-bold text-xs shrink-0 border border-slate-700">
                                            {{ strtoupper(substr($sub->student?->name ?? 'ST', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-900 block truncate font-heading">
                                                {{ $sub->student?->name ?? 'Student' }}
                                            </span>
                                            <span class="text-xs text-slate-500 block truncate">
                                                {{ $sub->student?->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    <div class="space-y-0.5 max-w-xs">
                                        <span class="font-semibold text-slate-900 block truncate">
                                            {{ $sub->assignment?->title }}
                                        </span>
                                        <span class="text-xs text-slate-500 block truncate">
                                            {{ $sub->assignment?->course?->name }} • Attempt #{{ $sub->attempt_number }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    <span class="font-mono text-xs text-slate-700 block">
                                        {{ $sub->submitted_at?->format('d M Y, h:i A') }}
                                    </span>
                                    @if ($sub->is_late)
                                        <span class="text-[10px] font-mono text-rose-600 font-bold block">Late Submission</span>
                                    @endif
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    @if ($sub->isGraded())
                                        <div class="space-y-1">
                                            <x-ui.badge variant="success" size="xs">
                                                Graded: {{ $sub->marks_awarded }}/{{ $sub->assignment?->max_marks }} pts
                                            </x-ui.badge>
                                            <span class="text-[10px] font-mono text-slate-400 block">Score: {{ $sub->percentage }}%</span>
                                        </div>
                                    @elseif ($sub->isResubmissionRequired())
                                        <x-ui.badge variant="warning" size="xs">Resubmission</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="info" size="xs">Needs Grading</x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-4 px-4 sm:px-6 text-right">
                                    <x-ui.button
                                        href="{{ route('instructor.assignments.submissions.show', [$sub->assignment, $sub]) }}"
                                        variant="{{ $sub->isGraded() ? 'secondary' : 'primary' }}"
                                        size="xs"
                                    >
                                        {{ $sub->isGraded() ? 'Review Feedback' : 'Grade Submission' }}
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($submissions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $submissions->links() }}
                </div>
            @endif
        @endif
    </x-ui.card>
</div>
@endsection

