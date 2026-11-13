@extends('layouts.admin')

@section('title', 'Grade Submission #' . $submission->attempt_number . ' — ' . $submission->user?->name . ' | ZebLearn LMS')
@section('page-title', 'Submission Review')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div>
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.assignments.index') }}" class="hover:text-[#FB6221] transition-colors">Assignments</a>
            <span>/</span>
            <a href="{{ route('admin.assignments.show', $assignment) }}" class="hover:text-[#FB6221] transition-colors">{{ Str::limit($assignment->title, 20) }}</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Submission #{{ $submission->attempt_number }}</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    Evaluate Student Submission
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Candidate: <strong class="text-slate-900">{{ $submission->user?->name }}</strong> ({{ $submission->user?->email }})
                    • Attempt <span class="font-mono font-bold">#{{ $submission->attempt_number }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if ($submission->isGraded())
                    <x-ui.badge variant="success" size="md">
                        Graded: {{ $submission->marks_awarded }} / {{ $assignment->max_marks }} pts ({{ $submission->percentage }}%)
                    </x-ui.badge>
                @elseif ($submission->isResubmissionRequired())
                    <x-ui.badge variant="warning" size="md">Resubmission Requested</x-ui.badge>
                @else
                    <x-ui.badge variant="info" size="md">Awaiting Grading</x-ui.badge>
                @endif

                @if ($submission->is_late)
                    <x-ui.badge variant="danger" size="md">Late Submission</x-ui.badge>
                @endif
            </div>
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

    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <strong class="block font-bold">Please correct the following errors:</strong>
            <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- SUBMISSION METADATA --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
        <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
            Submission Details &amp; Timestamp
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs font-sans">
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Submitted Timestamp:</span>
                <span class="font-semibold text-slate-800 font-mono">{{ $submission->submitted_at?->format('d M Y, h:i:s A') }}</span>
            </div>

            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Assignment Due Date:</span>
                <span class="font-semibold text-slate-800 font-mono">
                    {{ $assignment->due_at ? $assignment->due_at->format('d M Y, h:i:s A') : 'No deadline' }}
                </span>
            </div>

            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Timing Status:</span>
                @if ($submission->is_late)
                    <span class="text-rose-600 font-bold">Turned in late</span>
                @else
                    <span class="text-emerald-600 font-bold">On time</span>
                @endif
            </div>

            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Maximum Marks:</span>
                <span class="font-bold text-slate-900 font-mono">{{ $assignment->max_marks }} pts</span>
            </div>
        </div>

        {{-- WRITTEN TEXT SUBMISSION --}}
        @if ($submission->submission_text)
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 block">
                    Student Written Response:
                </span>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/70 text-slate-800 text-xs leading-relaxed whitespace-pre-wrap font-sans">
                    {{ $submission->submission_text }}
                </div>
            </div>
        @endif

        {{-- UPLOADED ATTACHMENTS --}}
        @if ($submission->files->isNotEmpty())
            <div class="pt-3 border-t border-slate-100 space-y-2">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 block">
                    Attached Deliverables ({{ $submission->files->count() }}):
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($submission->files as $file)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/80 bg-white">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="p-2 rounded-lg bg-slate-100 text-slate-600 font-mono text-[11px] font-bold uppercase">
                                    {{ $file->extension }}
                                </span>
                                <div class="truncate">
                                    <span class="text-xs font-bold text-slate-900 block truncate" title="{{ $file->original_filename }}">
                                        {{ $file->original_filename }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-mono block">
                                        {{ $file->formatted_size }}
                                    </span>
                                </div>
                            </div>

                            <x-ui.button
                                href="{{ route('admin.assignments.files.download', [$assignment, $submission, $file]) }}"
                                variant="secondary"
                                size="xs"
                                icon="download"
                            >
                                Download
                            </x-ui.button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </x-ui.card>

    {{-- GRADING CONSOLE --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-5">
        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
            <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider">
                Evaluation &amp; Feedback Console
            </h2>
            @if ($submission->isGraded())
                <span class="text-[11px] text-slate-400 font-mono">
                    Graded by {{ $submission->grader?->name ?? 'Administrator' }} on {{ $submission->graded_at?->format('d M Y, h:i A') }}
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.assignments.submissions.grade', [$assignment, $submission]) }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Marks Awarded (Out of {{ $assignment->max_marks }}) <span class="text-rose-600">*</span>
                    </label>
                    <x-ui.input
                        type="number"
                        name="marks_awarded"
                        min="0"
                        max="{{ $assignment->max_marks }}"
                        value="{{ old('marks_awarded', $submission->marks_awarded) }}"
                        required
                    />
                    @if ($assignment->passing_marks)
                        <span class="text-[11px] text-slate-400 block mt-1">
                            Passing threshold: <strong>{{ $assignment->passing_marks }} pts</strong>
                        </span>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Evaluation Action
                    </label>
                    <x-ui.select name="action">
                        <option value="grade" @selected(old('action') !== 'resubmission')>Final Grade &amp; Publish Feedback</option>
                        <option value="resubmission" @selected(old('action') === 'resubmission')>Request Resubmission from Student</option>
                    </x-ui.select>
                    <span class="text-[11px] text-slate-400 block mt-1">
                        Resubmission allows student to submit another attempt if allowed.
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Student Feedback <span class="text-slate-400 font-normal">(Visible to the student on their portal)</span>
                </label>
                <x-ui.textarea
                    name="feedback"
                    rows="4"
                    placeholder="Constructive feedback, highlights, recommendations, or grading rationale..."
                >{{ old('feedback', $submission->feedback) }}</x-ui.textarea>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold text-slate-700">
                        Private Administrator / Staff Notes <span class="text-rose-600 font-semibold font-mono text-[10px] uppercase tracking-wider">[INTERNAL ONLY]</span>
                    </label>
                    <span class="text-[10px] text-slate-400 font-mono">Never visible to student</span>
                </div>
                <x-ui.textarea
                    name="private_notes"
                    rows="2"
                    placeholder="Internal audit notes, plagiarism checks, or instructor notes..."
                >{{ old('private_notes', $submission->private_notes) }}</x-ui.textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <x-ui.button href="{{ route('admin.assignments.show', $assignment) }}" variant="ghost" size="md">
                    Back to Assignment
                </x-ui.button>

                <x-ui.button type="submit" variant="primary" size="md" icon="check">
                    Save Grade &amp; Feedback
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection

