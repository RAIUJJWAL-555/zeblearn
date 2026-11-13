@extends('layouts.admin')

@section('title', $assignment->title . ' — Assignment Console | ZebLearn LMS')
@section('page-title', 'Assignment Console')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.assignments.index') }}" class="hover:text-[#FB6221] transition-colors">Assignments</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-[200px] sm:max-w-xs">{{ $assignment->title }}</span>
            </nav>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $assignment->title }}
                </h1>
                @if ($assignment->isPublished())
                    <x-ui.badge variant="success" size="md">Published</x-ui.badge>
                @else
                    <x-ui.badge variant="neutral" size="md">Draft</x-ui.badge>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ $assignment->course?->name }}
                @if ($assignment->isLessonLevel())
                    • Lesson: {{ $assignment->lesson?->title }}
                @elseif ($assignment->isModuleLevel())
                    • Module: {{ $assignment->module?->title }}
                @else
                    • Course Final Project
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <form method="POST" action="{{ route('admin.assignments.toggle-status', $assignment) }}">
                @csrf
                @method('PATCH')
                <x-ui.button
                    type="submit"
                    variant="{{ $assignment->isDraft() ? 'primary' : 'secondary' }}"
                    size="sm"
                    icon="{{ $assignment->isDraft() ? 'check' : 'pause' }}"
                >
                    {{ $assignment->isDraft() ? 'Publish Assignment' : 'Move to Draft' }}
                </x-ui.button>
            </form>

            <x-ui.button
                href="{{ route('admin.assignments.edit', $assignment) }}"
                variant="secondary"
                size="sm"
                icon="pencil"
            >
                Edit Settings
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

    {{-- STATS STRIP --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Submissions</span>
            <span class="text-2xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['total'] }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Pending Evaluation</span>
            <span class="text-2xl font-black text-amber-600 font-heading mt-1 block">{{ $stats['pending'] }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Graded</span>
            <span class="text-2xl font-black text-emerald-600 font-heading mt-1 block">{{ $stats['graded'] }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-indigo-600">Max Score</span>
            <span class="text-2xl font-black text-indigo-600 font-heading mt-1 block">{{ $assignment->max_marks }} pts</span>
        </div>
    </div>

    {{-- ASSIGNMENT SPECIFICATIONS --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-3">
        <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
            Configuration &amp; Submission Rules
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Due Date:</span>
                <span class="font-semibold text-slate-800">
                    {{ $assignment->due_at ? $assignment->due_at->format('d M Y, h:i A') : 'No deadline' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Allowed Attempts:</span>
                <span class="font-semibold text-slate-800">{{ $assignment->max_attempts }} {{ Str::plural('attempt', $assignment->max_attempts) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Late Policy:</span>
                <span class="font-semibold text-slate-800">{{ $assignment->allow_late_submissions ? 'Accepted (marked late)' : 'Strict cutoff' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Allowed Files:</span>
                <span class="font-semibold text-slate-800 font-mono">{{ $assignment->allowed_file_types ?: 'None' }} (≤ {{ $assignment->max_file_size_mb }}MB)</span>
            </div>
        </div>

        @if ($assignment->instructions)
            <div class="pt-3 border-t border-slate-100">
                <span class="text-slate-400 block font-mono text-[11px] mb-1">Instructions:</span>
                <p class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">{{ $assignment->instructions }}</p>
            </div>
        @endif
    </x-ui.card>

    {{-- SUBMISSIONS LIST --}}
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-lg font-heading font-black text-slate-900">
                Student Submissions ({{ $submissions->total() }})
            </h2>

            <form method="GET" action="{{ route('admin.assignments.show', $assignment) }}" class="flex items-center gap-2">
                <x-ui.select name="status" size="sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="submitted" @selected(request('status') === 'submitted')>Submitted (Pending)</option>
                    <option value="graded" @selected(request('status') === 'graded')>Graded</option>
                    <option value="resubmission_required" @selected(request('status') === 'resubmission_required')>Resubmission Requested</option>
                </x-ui.select>
            </form>
        </div>

        <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
            @if ($submissions->isEmpty())
                <div class="p-8 text-center text-slate-500 text-xs">
                    No student submissions recorded for this assignment yet.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-sans">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4">Student</th>
                                <th class="py-3 px-4">Attempt #</th>
                                <th class="py-3 px-4">Submitted At</th>
                                <th class="py-3 px-4">Files</th>
                                <th class="py-3 px-4">Marks Awarded</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($submissions as $sub)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4 font-medium">
                                        <div class="font-bold text-slate-900">{{ $sub->user?->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $sub->user?->email }}</div>
                                    </td>

                                    <td class="py-3.5 px-4 font-mono font-semibold">
                                        #{{ $sub->attempt_number }}
                                    </td>

                                    <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                                        <div>{{ $sub->submitted_at?->format('d M Y, h:i A') }}</div>
                                        @if ($sub->is_late)
                                            <span class="inline-block text-[10px] text-rose-600 font-bold uppercase tracking-wider">Late Submission</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4">
                                        @if ($sub->files->isNotEmpty())
                                            <span class="inline-flex items-center gap-1 font-mono text-xs text-slate-700">
                                                <span>{{ $sub->files->count() }}</span>
                                                <span class="text-slate-400">files</span>
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-xs">Text only</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 font-mono font-bold">
                                        @if ($sub->marks_awarded !== null)
                                            <span class="{{ $sub->is_passed ? 'text-emerald-600' : 'text-slate-900' }}">
                                                {{ $sub->marks_awarded }} / {{ $assignment->max_marks }}
                                            </span>
                                            <span class="text-slate-400 text-[11px]">({{ $sub->percentage }}%)</span>
                                        @else
                                            <span class="text-amber-600 font-normal">Ungraded</span>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4">
                                        @if ($sub->isGraded())
                                            <x-ui.badge variant="success" size="sm">Graded</x-ui.badge>
                                        @elseif ($sub->isResubmissionRequired())
                                            <x-ui.badge variant="warning" size="sm">Resubmission</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="info" size="sm">Submitted</x-ui.badge>
                                        @endif
                                    </td>

                                    <td class="py-3.5 px-4 text-right">
                                        <x-ui.button
                                            href="{{ route('admin.assignments.submissions.show', [$assignment, $sub]) }}"
                                            variant="{{ $sub->isGraded() ? 'secondary' : 'primary' }}"
                                            size="xs"
                                        >
                                            {{ $sub->isGraded() ? 'Review' : 'Grade' }}
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
</div>
@endsection

