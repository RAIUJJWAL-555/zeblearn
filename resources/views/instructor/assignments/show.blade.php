@extends('layouts.instructor')

@section('title', 'Evaluate Submission #' . $submission->attempt_number . ' - ' . $submission->student?->name . ' | ZebLearn LMS')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Portal</a>
                <span>/</span>
                <a href="{{ route('instructor.assignments.index') }}" class="hover:text-[#FB6221] transition-colors">Assignment Reviews</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Submission #{{ $submission->attempt_number }}</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Review &amp; Grade Submission
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Candidate: <strong class="text-slate-900">{{ $submission->student?->name }}</strong> • Attempt #{{ $submission->attempt_number }}
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
                <x-ui.badge variant="info" size="md">Awaiting Faculty Grade</x-ui.badge>
            @endif

            @if ($submission->is_late)
                <x-ui.badge variant="danger" size="md">Late</x-ui.badge>
            @endif
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('info'))
        <x-ui.alert variant="info" dismissible>
            {{ session('info') }}
        </x-ui.alert>
    @endif

    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <strong class="block font-bold">Please correct the following input errors:</strong>
            <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- SUBMISSION METADATA CARD --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
        <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
            Deliverable Overview &amp; Task Context
        </h2>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Course:</span>
                <span class="font-bold text-slate-900 block truncate">{{ $assignment->course?->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Assignment:</span>
                <span class="font-bold text-slate-900 block truncate">{{ $assignment->title }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Maximum Score:</span>
                <span class="font-bold text-[#FB6221] block font-mono">{{ $assignment->max_marks }} Points</span>
            </div>
            <div>
                <span class="text-slate-400 block font-mono text-[11px]">Submitted On:</span>
                <span class="font-bold text-slate-900 block font-mono">{{ $submission->submitted_at?->format('d M Y, h:i A') }}</span>
            </div>
        </div>
    </x-ui.card>

    {{-- STUDENT WORK DELIVERABLES --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
        <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
            Student Submission Content
        </h2>

        {{-- TEXT CONTENT --}}
        @if ($submission->content)
            <div class="space-y-2">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700 block">
                    Written Answer / Technical Documentation:
                </span>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 font-sans whitespace-pre-wrap leading-relaxed">
                    {{ $submission->content }}
                </div>
            </div>
        @endif

        {{-- ATTACHED FILES --}}
        @if ($submission->files->isNotEmpty())
            <div class="space-y-2 pt-2">
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700 block">
                    Attached Deliverable Files ({{ $submission->files->count() }}):
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($submission->files as $file)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-all">
                            <div class="min-w-0 pr-2">
                                <span class="font-bold text-xs text-slate-900 block truncate font-mono">
                                    {{ $file->original_filename }}
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono block">
                                    {{ $file->human_file_size }} • {{ strtoupper($file->file_type) }}
                                </span>
                            </div>

                            <x-ui.button
                                href="{{ route('instructor.assignments.files.download', [$assignment, $submission, $file]) }}"
                                variant="secondary"
                                size="xs"
                                icon="arrow-down-tray"
                            >
                                Download
                            </x-ui.button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if (! $submission->content && $submission->files->isEmpty())
            <p class="text-xs text-slate-400 italic">No text or files attached to this submission attempt.</p>
        @endif
    </x-ui.card>

    {{-- FACULTY GRADING FORM --}}
    <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="font-heading font-black text-base text-slate-900">
                    Faculty Evaluation &amp; Grade Form
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Assign marks out of {{ $assignment->max_marks }} and provide pedagogical feedback to the student.
                </p>
            </div>
            @if ($submission->grader)
                <span class="text-xs text-slate-400 font-mono">
                    Last graded by: {{ $submission->grader->name }}
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('instructor.assignments.submissions.grade', [$assignment, $submission]) }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="marks_awarded" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Marks Awarded (Max: {{ $assignment->max_marks }}) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="number"
                        id="marks_awarded"
                        name="marks_awarded"
                        value="{{ old('marks_awarded', $submission->marks_awarded) }}"
                        min="0"
                        max="{{ $assignment->max_marks }}"
                        required
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm font-mono font-bold bg-slate-50 border @error('marks_awarded') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                    >
                    @error('marks_awarded')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="action" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Review Decision <span class="text-rose-500">*</span>
                    </label>
                    <select
                        id="action"
                        name="action"
                        class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                    >
                        <option value="grade" {{ old('action') !== 'resubmission' ? 'selected' : '' }}>
                            Accept &amp; Save Final Grade
                        </option>
                        <option value="resubmission" {{ old('action') === 'resubmission' ? 'selected' : '' }}>
                            Request Resubmission (Requires rework)
                        </option>
                    </select>
                </div>
            </div>

            <div>
                <label for="feedback" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Student Feedback (Visible to Student)
                </label>
                <textarea
                    id="feedback"
                    name="feedback"
                    rows="4"
                    placeholder="Provide constructive assessment feedback, corrections, and compliments..."
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >{{ old('feedback', $submission->feedback) }}</textarea>
                @error('feedback')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="private_notes" class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Faculty Private Notes (Internal Only)
                </label>
                <textarea
                    id="private_notes"
                    name="private_notes"
                    rows="2"
                    placeholder="Internal evaluation remarks or coordinator notes (hidden from student)..."
                    class="w-full px-3.5 py-2.5 rounded-xl text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >{{ old('private_notes', $submission->private_notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <x-ui.button
                    href="{{ route('instructor.assignments.index') }}"
                    variant="ghost"
                    size="md"
                >
                    Cancel
                </x-ui.button>

                <x-ui.button
                    type="submit"
                    variant="primary"
                    size="md"
                    icon="check"
                >
                    Submit Grade &amp; Evaluation
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection

