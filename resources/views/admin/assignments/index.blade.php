@extends('layouts.admin')

@section('title', 'Assignment Management & Grading | ZebLearn LMS')
@section('page-title', 'Assignments')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Academic</span>
                <span>/</span>
                <span class="text-slate-900 font-bold">Assignments</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Assignment Management &amp; Grading
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Author course, module, and lesson-level assignments, configure submission parameters, and evaluate student submissions.
            </p>
        </div>

        <div class="shrink-0">
            <x-ui.button
                href="{{ route('admin.assignments.create') }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                Create Assignment
            </x-ui.button>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="danger" dismissible>
            {{ session('error') }}
        </x-ui.alert>
    @endif

    {{-- ANALYTICS KPI CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Assignments</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $totalAssignments }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Published Active</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading mt-1 block">{{ $publishedAssignments }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-indigo-600">Total Submissions</span>
            <span class="text-2xl sm:text-3xl font-black text-indigo-600 font-heading mt-1 block">{{ $totalSubmissions }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Awaiting Grading</span>
            <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading mt-1 block">{{ $pendingSubmissions }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs col-span-2 lg:col-span-1">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Graded</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-800 font-heading mt-1 block">{{ $gradedSubmissions }}</span>
        </div>
    </div>

    {{-- FILTER CONTROLS --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.assignments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <x-ui.input
                    type="search"
                    name="search"
                    placeholder="Search by title..."
                    value="{{ request('search') }}"
                    size="sm"
                />
            </div>

            <div>
                <x-ui.select name="course_id" size="sm">
                    <option value="">All Courses</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>

            <div>
                <x-ui.select name="status" size="sm">
                    <option value="">All Statuses</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft Only</option>
                    <option value="published" @selected(request('status') === 'published')>Published Only</option>
                </x-ui.select>
            </div>

            <div>
                <x-ui.select name="context" size="sm">
                    <option value="">All Contexts</option>
                    <option value="course" @selected(request('context') === 'course')>Course Final</option>
                    <option value="module" @selected(request('context') === 'module')>Module Scope</option>
                    <option value="lesson" @selected(request('context') === 'lesson')>Lesson Scope</option>
                </x-ui.select>
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button type="submit" variant="secondary" size="sm" class="flex-1">
                    Filter
                </x-ui.button>
                @if (request()->hasAny(['search', 'course_id', 'status', 'context']))
                    <x-ui.button href="{{ route('admin.assignments.index') }}" variant="ghost" size="sm">
                        Reset
                    </x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- ASSIGNMENTS DATA TABLE --}}
    <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
        @if ($assignments->isEmpty())
            <div class="p-12 text-center">
                <x-ui.empty-state
                    title="No assignments found"
                    description="Create a new assignment to begin accepting and evaluating student coursework."
                    icon="academic-cap"
                >
                    <x-ui.button href="{{ route('admin.assignments.create') }}" variant="primary" size="sm" icon="plus" class="mt-4">
                        Create Assignment
                    </x-ui.button>
                </x-ui.empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs font-sans">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Assignment / Course</th>
                            <th class="py-3 px-4">Context</th>
                            <th class="py-3 px-4">Max Marks</th>
                            <th class="py-3 px-4">Due Date</th>
                            <th class="py-3 px-4">Submissions</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach ($assignments as $assignment)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-medium">
                                    <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-slate-900 font-bold hover:text-[#FB6221] transition-colors block text-sm">
                                        {{ $assignment->title }}
                                    </a>
                                    <span class="text-slate-500 text-[11px] block mt-0.5">
                                        {{ $assignment->course?->name }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if ($assignment->isLessonLevel())
                                        <x-ui.badge variant="neutral" size="sm">
                                            Lesson: {{ Str::limit($assignment->lesson?->title, 18) }}
                                        </x-ui.badge>
                                    @elseif ($assignment->isModuleLevel())
                                        <x-ui.badge variant="warning" size="sm">
                                            Module: {{ Str::limit($assignment->module?->title, 18) }}
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="info" size="sm">
                                            Course Final
                                        </x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-800">
                                    {{ $assignment->max_marks }} pts
                                </td>

                                <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                    @if ($assignment->due_at)
                                        <span class="{{ $assignment->isPastDue() ? 'text-rose-600 font-semibold' : '' }}">
                                            {{ $assignment->due_at->format('d M Y, h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">No deadline</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4">
                                    <a href="{{ route('admin.assignments.show', $assignment) }}" class="inline-flex items-center gap-1 font-mono text-xs font-bold text-slate-700 hover:text-[#FB6221]">
                                        <span>{{ $assignment->submissions_count }}</span>
                                        <span class="text-slate-400 font-normal">sub.</span>
                                    </a>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if ($assignment->isPublished())
                                        <x-ui.badge variant="success" size="sm">Published</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="sm">Draft</x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                                    <x-ui.button
                                        href="{{ route('admin.assignments.show', $assignment) }}"
                                        variant="secondary"
                                        size="xs"
                                    >
                                        Console
                                    </x-ui.button>

                                    <x-ui.button
                                        href="{{ route('admin.assignments.edit', $assignment) }}"
                                        variant="ghost"
                                        size="xs"
                                    >
                                        Edit
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($assignments->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $assignments->links() }}
                </div>
            @endif
        @endif
    </x-ui.card>
</div>
@endsection

