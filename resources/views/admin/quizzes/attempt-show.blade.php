@extends('layouts.admin')

@section('title', 'Attempt Details - ' . $attempt->user->name . ' | ZebLearn LMS')
@section('page-title', 'Attempt Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.quizzes.index') }}" class="hover:text-[#FB6221] transition-colors">Quizzes</a>
            <span>/</span>
            <a href="{{ route('admin.quizzes.show', $quiz) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $quiz->title }}</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Attempt #{{ $attempt->attempt_number }}</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.quizzes.show', $quiz) }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Quiz
        </x-ui.button>
    </div>

    {{-- ATTEMPT RESULT HEADER CARD --}}
    <x-ui.card padding="md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <x-ui.badge variant="neutral" size="xs" class="font-mono font-bold uppercase">
                        Attempt #{{ $attempt->attempt_number }} of {{ $quiz->attempts_allowed }}
                    </x-ui.badge>
                    @if ($attempt->is_passed)
                        <x-ui.badge variant="success" size="xs" dot class="font-bold font-mono">
                            PASSED
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="danger" size="xs" dot class="font-bold font-mono">
                            FAILED
                        </x-ui.badge>
                    @endif
                </div>

                <h1 class="text-xl sm:text-2xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $attempt->user->name }}
                </h1>
                <p class="text-xs font-mono text-slate-500 mt-0.5">
                    {{ $attempt->user->email }} • Submitted on {{ $attempt->submitted_at?->format('d M Y, h:i A') }}
                </p>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200/80 text-center sm:text-right shrink-0">
                <span class="block text-[11px] font-mono uppercase tracking-wider text-slate-400">Final Score</span>
                <span class="text-3xl font-black text-slate-900 font-heading block mt-0.5">
                    {{ $attempt->score ?? 0 }} <span class="text-lg text-slate-400">/ {{ $attempt->total_marks }}</span>
                </span>
                <span class="inline-block mt-1 text-xs font-mono font-bold {{ $attempt->is_passed ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $attempt->percentage ?? 0 }}% (Pass Threshold: {{ $quiz->passing_percentage }}%)
                </span>
            </div>
        </div>
    </x-ui.card>

    {{-- QUESTION-BY-QUESTION AUDIT BREAKDOWN --}}
    <div class="space-y-4">
        <h2 class="font-heading font-bold text-lg text-slate-900">
            Answer Audit &amp; Grading Breakdown
        </h2>

        @php
            $answersMap = $attempt->answers->keyBy('quiz_question_id');
        @endphp

        <div class="space-y-4">
            @foreach ($quiz->questions as $index => $question)
                @php
                    $ans = $answersMap->get($question->id);
                    $selectedOptId = $ans?->quiz_option_id;
                    $correctOpt = $question->options->firstWhere('is_correct', true);
                    $isCorrect = $correctOpt && $selectedOptId && (int) $correctOpt->id === (int) $selectedOptId;
                @endphp

                <x-ui.card padding="md" class="space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-700">
                                Question {{ $index + 1 }}
                            </span>
                            <h3 class="text-sm font-bold text-slate-900">
                                {{ $question->question_text }}
                            </h3>
                        </div>

                        <div class="shrink-0 text-right">
                            @if ($isCorrect)
                                <x-ui.badge variant="success" size="xs" class="font-bold">
                                    +{{ $ans?->awarded_marks ?? $question->marks }} / {{ $question->marks }} Marks
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="danger" size="xs" class="font-bold">
                                    0 / {{ $question->marks }} Marks
                                </x-ui.badge>
                            @endif
                        </div>
                    </div>

                    {{-- Options list --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach ($question->options as $opt)
                            @php
                                $isStudentChoice = (int) $opt->id === (int) $selectedOptId;
                                $isCorrectChoice = (bool) $opt->is_correct;
                            @endphp

                            <div class="p-3 rounded-2xl border text-xs flex items-center justify-between gap-3 {{ $isCorrectChoice ? 'bg-emerald-50/70 border-emerald-300 text-emerald-900 font-semibold' : ($isStudentChoice ? 'bg-rose-50/70 border-rose-300 text-rose-900 font-semibold' : 'bg-slate-50/50 border-slate-200 text-slate-600') }}">
                                <div class="flex items-center gap-2 truncate">
                                    @if ($isCorrectChoice)
                                        <x-ui.icon name="check-circle" size="xs" class="text-emerald-600 shrink-0" />
                                    @elseif ($isStudentChoice)
                                        <x-ui.icon name="close" size="xs" class="text-rose-600 shrink-0" />
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                                    @endif
                                    <span class="truncate">{{ $opt->option_text }}</span>
                                </div>

                                <div class="shrink-0 text-[10px] font-mono">
                                    @if ($isStudentChoice && $isCorrectChoice)
                                        <span class="text-emerald-700 font-bold">Selected &amp; Correct</span>
                                    @elseif ($isStudentChoice)
                                        <span class="text-rose-700 font-bold">Student's Choice</span>
                                    @elseif ($isCorrectChoice)
                                        <span class="text-emerald-700 font-bold">Correct Key</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($question->explanation)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 font-mono">
                            <strong>Explanation:</strong> {{ $question->explanation }}
                        </div>
                    @endif
                </x-ui.card>
            @endforeach
        </div>
    </div>
</div>
@endsection
