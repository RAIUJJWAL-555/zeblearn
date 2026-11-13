@extends('layouts.app')

@section('title', $quiz->title . ' - Assessment | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- BREADCRUMBS --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $course->name }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-xs">{{ $quiz->title }}</span>
            </nav>
            <x-ui.button
                href="{{ route('student.courses.show', $course) }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Course
            </x-ui.button>
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

        @if (session('error'))
            <x-ui.alert variant="error" dismissible>
                {{ session('error') }}
            </x-ui.alert>
        @endif

        {{-- QUIZ HERO INTRO CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase {{ $quiz->isLessonLevel() ? 'bg-purple-50 text-purple-700 border border-purple-200' : ($quiz->isModuleLevel() ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-orange-50 text-[#FB6221] border border-orange-200') }}">
                    {{ $quiz->context_label }}
                </span>
                <span class="text-xs font-mono text-slate-500">
                    Track: {{ $course->category_label }}
                </span>
            </div>

            <div>
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 font-heading tracking-tight leading-tight">
                    {{ $quiz->title }}
                </h1>

                @if ($quiz->description)
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-3 font-sans">
                        {{ $quiz->description }}
                    </p>
                @endif
            </div>

            {{-- SPECIFICATIONS GRID --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4 border-y border-slate-100 text-xs font-mono">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 uppercase text-[10px] block mb-1">Questions</span>
                    <strong class="text-slate-900 text-sm font-sans block">{{ $quiz->questions()->count() }} MCQs</strong>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 uppercase text-[10px] block mb-1">Total Score</span>
                    <strong class="text-slate-900 text-sm font-sans block">{{ $quiz->total_marks }} Marks</strong>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 uppercase text-[10px] block mb-1">Passing Score</span>
                    <strong class="text-slate-900 text-sm font-sans block">{{ $quiz->passing_percentage }}%</strong>
                </div>

                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <span class="text-slate-400 uppercase text-[10px] block mb-1">Time Limit</span>
                    <strong class="text-slate-900 text-sm font-sans block">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' Minutes' : 'No Time Limit' }}</strong>
                </div>
            </div>

            {{-- ASSESSMENT POLICIES & BRIEFING --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-sans">
                <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <x-ui.icon name="clock" size="xs" class="text-slate-500 mt-0.5 shrink-0" />
                    <div>
                        <strong class="text-slate-900 block font-semibold">Timed Assessment</strong>
                        <span class="text-slate-500">{{ $quiz->time_limit_minutes ? 'Server-authoritative timer counts down and automatically submits on expiry.' : 'Untimed assessment, complete at your own pace.' }}</span>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <x-ui.icon name="check-circle" size="xs" class="text-slate-500 mt-0.5 shrink-0" />
                    <div>
                        <strong class="text-slate-900 block font-semibold">Instant Autosaving</strong>
                        <span class="text-slate-500">Each selected answer is saved instantly. You may review and change choices prior to submission.</span>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <x-ui.icon name="shield-check" size="xs" class="text-slate-500 mt-0.5 shrink-0" />
                    <div>
                        <strong class="text-slate-900 block font-semibold">Attempt Policy</strong>
                        <span class="text-slate-500">Max {{ $quiz->attempts_allowed }} {{ Str::plural('attempt', $quiz->attempts_allowed) }}. {{ $remainingAttempts }} {{ Str::plural('attempt', $remainingAttempts) }} remaining.</span>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                    <x-ui.icon name="academic-cap" size="xs" class="text-slate-500 mt-0.5 shrink-0" />
                    <div>
                        <strong class="text-slate-900 block font-semibold">Answer Review</strong>
                        <span class="text-slate-500">{{ $quiz->show_correct_answers ? 'Full answer keys and explanations are revealed after submission.' : 'Scores are calculated immediately; correct answers remain hidden.' }}</span>
                    </div>
                </div>
            </div>

            {{-- INSTRUCTIONS --}}
            @if ($quiz->instructions)
                <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/90 space-y-2">
                    <h3 class="text-xs font-mono font-bold uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                        <x-ui.icon name="info" size="xs" class="text-amber-600" />
                        <span>Assessment Instructions</span>
                    </h3>
                    <p class="text-xs text-slate-700 leading-relaxed font-sans whitespace-pre-line">
                        {{ $quiz->instructions }}
                    </p>
                </div>
            @endif

            {{-- ACTION & CTA --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 border-t border-slate-100">
                <div class="text-xs font-mono text-slate-500">
                    <span>Attempts Allowed: <strong>{{ $quiz->attempts_allowed }}</strong></span>
                    <span class="mx-2">•</span>
                    <span>Remaining: <strong class="{{ $remainingAttempts > 0 ? 'text-emerald-700' : 'text-rose-600' }}">{{ $remainingAttempts }}</strong></span>
                </div>

                <div>
                    @if ($activeAttempt)
                        <x-ui.button
                            href="{{ route('student.quizzes.take', [$course, $quiz, $activeAttempt]) }}"
                            variant="primary"
                            size="md"
                            icon="arrow-right"
                            class="bg-amber-500 hover:bg-amber-600"
                        >
                            <span>Resume In-Progress Attempt</span>
                        </x-ui.button>
                    @elseif ($canAttempt)
                        <form method="POST" action="{{ route('student.quizzes.start', [$course, $quiz]) }}">
                            @csrf
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="md"
                                icon="arrow-right"
                            >
                                <span>{{ $attempts->isEmpty() ? 'Start Assessment' : 'Retake Assessment' }}</span>
                            </x-ui.button>
                        </form>
                    @else
                        <x-ui.badge variant="neutral" size="lg">
                            Max Attempts Reached
                        </x-ui.badge>
                    @endif
                </div>
            </div>
        </x-ui.card>

        {{-- PREVIOUS ATTEMPTS TABLE --}}
        @if ($attempts->isNotEmpty())
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-heading font-bold text-base text-slate-900">
                        Your Past Attempts ({{ $attempts->count() }})
                    </h3>
                    @if ($bestAttempt)
                        <x-ui.badge variant="success" size="sm">
                            Best Score: {{ $bestAttempt->percentage }}%
                        </x-ui.badge>
                    @endif
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach ($attempts as $att)
                        <div class="py-3.5 flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <strong class="text-xs font-bold text-slate-900 font-heading">Attempt #{{ $att->attempt_number }}</strong>
                                    @if ($att->is_passed)
                                        <x-ui.badge variant="success" size="xs">Passed</x-ui.badge>
                                    @elseif ($att->isSubmitted() || $att->isExpired())
                                        <x-ui.badge variant="danger" size="xs">Failed</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="xs" dot dotPulse>In Progress</x-ui.badge>
                                    @endif
                                </div>
                                <span class="text-[11px] font-mono text-slate-400 mt-0.5 block">
                                    {{ $att->submitted_at?->format('d M Y, h:i A') ?? 'Started ' . $att->started_at->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right font-mono text-xs">
                                    <strong class="text-slate-900 font-heading">{{ $att->score ?? 0 }}/{{ $att->total_marks }}</strong>
                                    <span class="text-slate-400 block text-[11px]">({{ $att->percentage ?? 0 }}%)</span>
                                </div>

                                @if ($att->isSubmitted() || $att->isExpired())
                                    <x-ui.button
                                        href="{{ route('student.quizzes.result', [$course, $quiz, $att]) }}"
                                        variant="secondary"
                                        size="xs"
                                    >
                                        View Result
                                    </x-ui.button>
                                @else
                                    <x-ui.button
                                        href="{{ route('student.quizzes.take', [$course, $quiz, $att]) }}"
                                        variant="primary"
                                        size="xs"
                                        class="bg-amber-500 hover:bg-amber-600"
                                    >
                                        Resume
                                    </x-ui.button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

    </div>
</div>
@endsection
