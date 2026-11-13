@extends('layouts.auth')

@section('title', 'Submission #' . $submission->attempt_number . ' — ' . $assignment->title . ' | ZebLearn LMS')

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- BREADCRUMBS & HEADER --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors">{{ $course->name }}</a>
                <span>/</span>
                <a href="{{ route('student.assignments.show', [$course, $assignment]) }}" class="hover:text-[#FB6221] transition-colors">{{ Str::limit($assignment->title, 20) }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Attempt #{{ $submission->attempt_number }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        Submission Evaluation
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ $assignment->title }} • Attempt #{{ $submission->attempt_number }}
                    </p>
                </div>

                <x-ui.button href="{{ route('student.assignments.show', [$course, $assignment]) }}" variant="secondary" size="sm" icon="arrow-left">
                    Back to Assignment
                </x-ui.button>
            </div>
        </div>

        {{-- GRADING RESULT CARD --}}
        @if ($submission->isGraded())
            <x-ui.card padding="md" class="border-emerald-200 bg-emerald-50/40 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-emerald-200/60 pb-3">
                    <div>
                        <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-800 block">
                            Final Evaluation Result
                        </span>
                        <div class="flex items-baseline gap-2 mt-1">
                            <span class="text-3xl font-black text-emerald-700 font-heading">
                                {{ $submission->marks_awarded }}
                            </span>
                            <span class="text-sm font-bold text-emerald-600 font-mono">
                                / {{ $assignment->max_marks }} points ({{ $submission->percentage }}%)
                            </span>
                        </div>
                    </div>

                    <div>
                        @if ($submission->is_passed)
                            <x-ui.badge variant="success" size="lg">Passed</x-ui.badge>
                        @else
                            <x-ui.badge variant="danger" size="lg">Needs Improvement</x-ui.badge>
                        @endif
                    </div>
                </div>

                @if ($submission->feedback)
                    <div class="space-y-1.5">
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-900 block">
                            Instructor Feedback:
                        </span>
                        <p class="text-xs text-slate-800 leading-relaxed whitespace-pre-wrap bg-white/80 p-4 rounded-xl border border-emerald-200/60">
                            {{ $submission->feedback }}
                        </p>
                    </div>
                @endif
            </x-ui.card>
        @elseif ($submission->isResubmissionRequired())
            <x-ui.card padding="md" class="border-amber-200 bg-amber-50/40 shadow-2xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-amber-800">
                        Resubmission Requested
                    </span>
                    <x-ui.badge variant="warning" size="md">Action Required</x-ui.badge>
                </div>
                @if ($submission->feedback)
                    <p class="text-xs text-slate-800 leading-relaxed whitespace-pre-wrap bg-white/80 p-4 rounded-xl border border-amber-200/60">
                        {{ $submission->feedback }}
                    </p>
                @endif
            </x-ui.card>
        @else
            <x-ui.card padding="md" class="border-slate-200 bg-slate-50/70 shadow-2xs flex items-center justify-between">
                <div>
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 block">
                        Submission Status
                    </span>
                    <span class="text-sm font-bold text-slate-800 block mt-0.5">
                        Under Review / Awaiting Grading
                    </span>
                </div>
                <x-ui.badge variant="info" size="md">Submitted</x-ui.badge>
            </x-ui.card>
        @endif

        {{-- SUBMITTED DELIVERABLES --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                Submitted Deliverables
            </h2>

            <div class="grid grid-cols-2 gap-4 text-xs font-mono text-slate-600">
                <div>
                    <span class="text-slate-400 block text-[11px]">Submitted Timestamp:</span>
                    <span class="font-bold text-slate-800">{{ $submission->submitted_at?->format('d M Y, h:i:s A') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Submission Timing:</span>
                    @if ($submission->is_late)
                        <span class="text-rose-600 font-bold">Late Submission</span>
                    @else
                        <span class="text-emerald-600 font-bold">On Time</span>
                    @endif
                </div>
            </div>

            @if ($submission->submission_text)
                <div class="pt-3 border-t border-slate-100 space-y-1.5">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 block">
                        Your Written Text:
                    </span>
                    <div class="p-4 rounded-xl bg-slate-50 text-slate-800 text-xs whitespace-pre-wrap leading-relaxed font-sans border border-slate-200/60">
                        {{ $submission->submission_text }}
                    </div>
                </div>
            @endif

            @if ($submission->files->isNotEmpty())
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 block">
                        Uploaded Files ({{ $submission->files->count() }}):
                    </span>
                    <div class="space-y-2">
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
                                    href="{{ route('student.assignments.files.download', [$course, $assignment, $submission, $file]) }}"
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
    </div>
</div>
@endsection

