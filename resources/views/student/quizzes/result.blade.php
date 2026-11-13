@extends('layouts.app')

@section('title', 'Assessment Result: ' . $quiz->title . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $course->name }}</a>
                <span>/</span>
                <a href="{{ route('student.quizzes.show', [$course, $quiz]) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $quiz->title }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Result</span>
            </nav>
            <x-ui.button
                href="{{ route('student.courses.show', $course) }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Syllabus
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

        {{-- RESULT SCORE CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-700">
                            Attempt #{{ $attempt->attempt_number }} of {{ $quiz->attempts_allowed }}
                        </span>
                        @if ($attempt->is_passed)
                            <x-ui.badge variant="success" size="sm" dot>
                                ASSESSMENT PASSED
                            </x-ui.badge>
                        @else
                            <x-ui.badge variant="danger" size="sm" dot>
                                PASS CRITERIA NOT MET
                            </x-ui.badge>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        {{ $quiz->title }}
                    </h1>
                    <p class="text-xs font-mono text-slate-500">
                        Submitted: {{ $attempt->submitted_at?->format('d M Y, h:i A') }} • Threshold: {{ $quiz->passing_percentage }}%
                    </p>
                </div>

                {{-- SCORE CIRCLE / METRIC BOX --}}
                <div class="p-6 rounded-2xl {{ $attempt->is_passed ? 'bg-emerald-50/70 border border-emerald-200' : 'bg-rose-50/70 border border-rose-200' }} text-center sm:text-right shrink-0">
                    <span class="block text-[11px] font-mono uppercase tracking-wider {{ $attempt->is_passed ? 'text-emerald-700' : 'text-rose-700' }}">
                        Your Score
                    </span>
                    <span class="text-4xl font-black font-heading {{ $attempt->is_passed ? 'text-emerald-900' : 'text-rose-900' }} block mt-1">
                        {{ $attempt->score ?? 0 }} <span class="text-xl font-normal opacity-60">/ {{ $attempt->total_marks }}</span>
                    </span>
                    <span class="text-xs font-mono font-bold {{ $attempt->is_passed ? 'text-emerald-700' : 'text-rose-700' }} mt-1 block">
                        {{ $attempt->percentage ?? 0 }}% Overall
                    </span>
                </div>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex items-center gap-3 mt-8 pt-6 border-t border-slate-100 flex-wrap">
                @if ($quiz->canUserAttempt(auth()->user()))
                    <form method="POST" action="{{ route('student.quizzes.start', [$course, $quiz]) }}">
                        @csrf
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                            icon="arrow-right"
                        >
                            Retake Assessment
                        </x-ui.button>
                    </form>
                @endif

                <x-ui.button
                    href="{{ route('student.courses.show', $course) }}"
                    variant="secondary"
                    size="sm"
                >
                    Back to Course Syllabus
                </x-ui.button>

                <x-ui.button
                    href="{{ route('student.dashboard') }}"
                    variant="secondary"
                    size="sm"
                >
                    Student Dashboard
                </x-ui.button>

                <x-ui.button
                    href="{{ route('student.quizzes.history', [$course, $quiz]) }}"
                    variant="ghost"
                    size="sm"
                >
                    Attempt History
                </x-ui.button>
            </div>
        </x-ui.card>

        {{-- DETAILED ANSWER REVIEW (ONLY WHEN show_correct_answers IS ENABLED) --}}
        @if ($canReviewAnswers)
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <h2 class="font-heading font-bold text-xl text-slate-900">
                        Detailed Answer Review
                    </h2>
                    <span class="text-xs font-mono text-slate-400">
                        {{ $reviewQuestions->count() }} Questions Reviewed
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach ($reviewQuestions as $index => $item)
                        @php
                            $question = $item['question'];
                            $options = $item['options'];
                            $selectedOptionId = $item['selected_option_id'];
                            $correctOptionId = $item['correct_option_id'];
                            $isCorrect = $item['is_correct'];
                            $awardedMarks = $item['awarded_marks'];
                            $explanation = $item['explanation'];
                        @endphp

                        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-100 text-slate-700">
                                        Question {{ $index + 1 }}
                                    </span>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug font-sans">
                                        {{ $question->question_text }}
                                    </h3>
                                </div>

                                <div class="shrink-0 text-right font-mono text-xs">
                                    @if ($isCorrect)
                                        <x-ui.badge variant="success" size="sm">
                                            +{{ $awardedMarks }} / {{ $question->marks }} Marks
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger" size="sm">
                                            0 / {{ $question->marks }} Marks
                                        </x-ui.badge>
                                    @endif
                                </div>
                            </div>

                            {{-- Options List with Clear Visual Indicators --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                @foreach ($options as $opt)
                                    @php
                                        $isUserPick = (int) $opt->id === (int) $selectedOptionId;
                                        $isKey = (int) $opt->id === (int) $correctOptionId;
                                    @endphp

                                    <div class="p-3.5 rounded-2xl border text-xs flex items-center justify-between gap-3 {{ $isKey ? 'bg-emerald-50/80 border-emerald-300 text-emerald-950 font-semibold' : ($isUserPick ? 'bg-rose-50/80 border-rose-300 text-rose-950 font-semibold' : 'bg-slate-50/40 border-slate-200 text-slate-600') }}">
                                        <div class="flex items-center gap-2.5 truncate">
                                            @if ($isKey)
                                                <x-ui.icon name="check" size="xs" class="text-emerald-600 shrink-0" />
                                            @elseif ($isUserPick)
                                                <x-ui.icon name="x" size="xs" class="text-rose-600 shrink-0" />
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                                            @endif
                                            <span class="truncate">{{ $opt->option_text }}</span>
                                        </div>

                                        <div class="shrink-0 text-[10px] font-mono">
                                            @if ($isUserPick && $isKey)
                                                <span class="text-emerald-700 font-bold">Your Choice (Correct Answer)</span>
                                            @elseif ($isUserPick)
                                                <span class="text-rose-700 font-bold">Your Choice</span>
                                            @elseif ($isKey)
                                                <span class="text-emerald-700 font-bold">Correct Answer</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($explanation)
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 space-y-1 font-sans">
                                    <strong class="font-bold text-slate-900 block font-heading">Explanation:</strong>
                                    <p class="leading-relaxed">{{ $explanation }}</p>
                                </div>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        @else
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs text-center space-y-2">
                <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto mb-2">
                    <x-ui.icon name="shield-check" size="sm" />
                </div>
                <h3 class="font-heading font-bold text-slate-800 text-sm">Detailed Answer Review is Disabled</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto font-sans leading-relaxed">
                    Detailed answers hidden per assessment policy. The instructor has configured this assessment to not reveal the correct answer key after submission.
                </p>
            </x-ui.card>
        @endif

        {{-- ATTEMPT HISTORY LIST --}}
        @if ($history->count() > 1)
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-3">
                <h3 class="font-heading font-bold text-base text-slate-900 border-b border-slate-100 pb-2">
                    All Attempts History
                </h3>
                <div class="divide-y divide-slate-100">
                    @foreach ($history as $h)
                        <div class="py-3 flex items-center justify-between text-xs font-mono">
                            <div>
                                <strong class="text-slate-800">Attempt #{{ $h->attempt_number }}</strong>
                                <span class="text-slate-400 mx-2">•</span>
                                <span class="text-slate-500">{{ $h->submitted_at?->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span>{{ $h->score }}/{{ $h->total_marks }} ({{ $h->percentage }}%)</span>
                                <x-ui.badge variant="{{ $h->is_passed ? 'success' : 'danger' }}" size="xs">
                                    {{ $h->is_passed ? 'PASSED' : 'FAILED' }}
                                </x-ui.badge>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

    </div>
</div>
@endsection
