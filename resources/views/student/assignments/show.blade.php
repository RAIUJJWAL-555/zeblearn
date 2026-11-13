@extends('layouts.auth')

@section('title', $assignment->title . ' — Assignment | ZebLearn LMS')

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- BREADCRUMBS & HEADER --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors">{{ $course->name }}</a>
                <span>/</span>
                <a href="{{ route('student.assignments.index', $course) }}" class="hover:text-[#FB6221] transition-colors">Assignments</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-xs">{{ $assignment->title }}</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                            {{ $assignment->title }}
                        </h1>
                        @if ($assignment->isLessonLevel())
                            <x-ui.badge variant="neutral" size="sm">Lesson Task</x-ui.badge>
                        @elseif ($assignment->isModuleLevel())
                            <x-ui.badge variant="warning" size="sm">Module Project</x-ui.badge>
                        @else
                            <x-ui.badge variant="info" size="sm">Course Final Project</x-ui.badge>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ $course->name }}
                        @if ($assignment->isLessonLevel())
                            • Lesson: {{ $assignment->lesson?->title }}
                        @elseif ($assignment->isModuleLevel())
                            • Module: {{ $assignment->module?->title }}
                        @endif
                    </p>
                </div>

                <x-ui.button href="{{ route('student.assignments.index', $course) }}" variant="secondary" size="sm" icon="arrow-left">
                    All Assignments
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

        {{-- ASSIGNMENT SPECIFICATIONS STRIP --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                Assignment Policy &amp; Rules
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs font-sans">
                <div>
                    <span class="text-slate-400 block font-mono text-[11px]">Due Date:</span>
                    <span class="font-semibold text-slate-800 font-mono">
                        {{ $assignment->due_at ? $assignment->due_at->format('d M Y, h:i A') : 'No deadline' }}
                    </span>
                    @if ($assignment->isPastDue())
                        <span class="block text-[10px] text-rose-600 font-bold uppercase mt-0.5">Deadline Passed</span>
                    @endif
                </div>

                <div>
                    <span class="text-slate-400 block font-mono text-[11px]">Maximum Marks:</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $assignment->max_marks }} pts</span>
                    @if ($assignment->passing_marks)
                        <span class="block text-[10px] text-slate-500 font-mono">Pass: {{ $assignment->passing_marks }} pts</span>
                    @endif
                </div>

                <div>
                    <span class="text-slate-400 block font-mono text-[11px]">Allowed Attempts:</span>
                    <span class="font-semibold text-slate-800 font-mono">
                        {{ $submissions->count() }} of {{ $assignment->max_attempts }} used
                    </span>
                    <span class="block text-[10px] text-slate-500 font-mono">
                        {{ $remainingAttempts }} {{ Str::plural('attempt', $remainingAttempts) }} remaining
                    </span>
                </div>

                <div>
                    <span class="text-slate-400 block font-mono text-[11px]">File Format:</span>
                    <span class="font-semibold text-slate-800 font-mono text-[11px] block truncate" title="{{ $assignment->allowed_file_types }}">
                        {{ $assignment->allow_file_submission ? strtoupper($assignment->allowed_file_types) : 'No files' }}
                    </span>
                    @if ($assignment->allow_file_submission)
                        <span class="block text-[10px] text-slate-500 font-mono">Max: {{ $assignment->max_file_size_mb }}MB/file</span>
                    @endif
                </div>
            </div>

            @if ($assignment->instructions)
                <div class="pt-3 border-t border-slate-100 space-y-1">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500 block">
                        Instructions &amp; Deliverables:
                    </span>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60 text-xs text-slate-700 whitespace-pre-wrap leading-relaxed font-sans">
                        {{ $assignment->instructions }}
                    </div>
                </div>
            @endif
        </x-ui.card>

        {{-- PREVIOUS SUBMISSION HISTORY --}}
        @if ($submissions->isNotEmpty())
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Your Submissions ({{ $submissions->count() }})
                </h2>

                <div class="space-y-3">
                    @foreach ($submissions as $sub)
                        <div class="p-4 rounded-xl border border-slate-200 bg-white hover:border-slate-300 transition-all space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-sm text-slate-900">
                                        Attempt #{{ $sub->attempt_number }}
                                    </span>
                                    @if ($sub->isGraded())
                                        <x-ui.badge variant="success" size="sm">
                                            Score: {{ $sub->marks_awarded }} / {{ $assignment->max_marks }} ({{ $sub->percentage }}%)
                                        </x-ui.badge>
                                    @elseif ($sub->isResubmissionRequired())
                                        <x-ui.badge variant="warning" size="sm">Resubmission Requested</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="info" size="sm">Submitted — Awaiting Grading</x-ui.badge>
                                    @endif

                                    @if ($sub->is_late)
                                        <x-ui.badge variant="danger" size="sm">Late</x-ui.badge>
                                    @endif
                                </div>

                                <div class="text-[11px] font-mono text-slate-400">
                                    Submitted {{ $sub->submitted_at?->format('d M Y, h:i A') }}
                                </div>
                            </div>

                            @if ($sub->submission_text)
                                <div class="p-3 bg-slate-50 rounded-lg text-xs text-slate-700 whitespace-pre-wrap font-sans line-clamp-3">
                                    {{ $sub->submission_text }}
                                </div>
                            @endif

                            @if ($sub->files->isNotEmpty())
                                <div class="flex flex-wrap items-center gap-2 pt-1">
                                    @foreach ($sub->files as $file)
                                        <a
                                            href="{{ route('student.assignments.files.download', [$course, $assignment, $sub, $file]) }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 text-xs hover:border-[#FB6221] hover:text-[#FB6221] transition-colors"
                                        >
                                            <x-ui.icon name="download" size="xs" />
                                            <span class="truncate max-w-[150px]">{{ $file->original_filename }}</span>
                                            <span class="text-slate-400 font-mono text-[10px]">({{ $file->formatted_size }})</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif

                            @if ($sub->isGraded() && $sub->feedback)
                                <div class="p-3 bg-emerald-50/70 border border-emerald-200/70 rounded-lg text-xs space-y-1">
                                    <span class="font-bold text-emerald-900 block font-mono text-[11px] uppercase tracking-wider">
                                        Instructor Feedback:
                                    </span>
                                    <p class="text-emerald-800 whitespace-pre-wrap leading-relaxed">{{ $sub->feedback }}</p>
                                </div>
                            @endif

                            <div class="flex justify-end pt-1">
                                <x-ui.button
                                    href="{{ route('student.assignments.submissions.show', [$course, $assignment, $sub]) }}"
                                    variant="secondary"
                                    size="xs"
                                >
                                    View Full Result
                                </x-ui.button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        {{-- SUBMISSION FORM OR STATUS BANNER --}}
        @if ($canSubmit)
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                <div class="border-b border-slate-100 pb-2">
                    <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider">
                        Submit Attempt #{{ $submissions->count() + 1 }}
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        You have {{ $remainingAttempts }} {{ Str::plural('attempt', $remainingAttempts) }} remaining.
                        @if ($assignment->isPastDue())
                            <span class="text-rose-600 font-semibold font-mono">(Late submission permitted)</span>
                        @endif
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('student.assignments.submit', [$course, $assignment]) }}"
                    enctype="multipart/form-data"
                    class="space-y-4"
                >
                    @csrf

                    @if ($assignment->allow_text_submission)
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Written Response
                                @if (! $assignment->allow_file_submission)
                                    <span class="text-rose-600">*</span>
                                @else
                                    <span class="text-slate-400 font-normal">(Optional if uploading files)</span>
                                @endif
                            </label>
                            <x-ui.textarea
                                name="submission_text"
                                rows="5"
                                placeholder="Type your written response, notes, solution write-up, or answers here..."
                            >{{ old('submission_text') }}</x-ui.textarea>
                        </div>
                    @endif

                    @if ($assignment->allow_file_submission)
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Attach Files
                                @if (! $assignment->allow_text_submission)
                                    <span class="text-rose-600">*</span>
                                @else
                                    <span class="text-slate-400 font-normal">(Optional if text provided)</span>
                                @endif
                            </label>
                            <input
                                type="file"
                                name="files[]"
                                multiple
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer"
                            />
                            <span class="text-[11px] text-slate-400 block mt-1.5 font-mono">
                                Allowed formats: <strong>{{ strtoupper($assignment->allowed_file_types) }}</strong> (Max {{ $assignment->max_file_size_mb }}MB per file).
                            </span>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">
                            Make sure your deliverables comply with assignment guidelines before submitting.
                        </span>

                        <x-ui.button type="submit" variant="primary" size="md" icon="check">
                            Turn in Assignment
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        @else
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs text-center py-8 space-y-2">
                <x-ui.icon name="lock-closed" size="md" class="text-slate-400 mx-auto" />
                <h3 class="text-base font-heading font-bold text-slate-800">
                    Submissions Closed
                </h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    @if ($assignment->isBeforeStart())
                        This assignment is scheduled to open on {{ $assignment->start_at?->format('d M Y, h:i A') }}.
                    @elseif ($assignment->isPastDue() && ! $assignment->allow_late_submissions)
                        The submission deadline for this assignment was {{ $assignment->due_at?->format('d M Y, h:i A') }} and late submissions are not allowed.
                    @elseif ($remainingAttempts <= 0)
                        You have used all {{ $assignment->max_attempts }} allowed {{ Str::plural('attempt', $assignment->max_attempts) }} for this assignment.
                    @endif
                </p>
            </x-ui.card>
        @endif
    </div>
</div>
@endsection

