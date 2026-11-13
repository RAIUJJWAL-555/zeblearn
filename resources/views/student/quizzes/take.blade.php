@extends('layouts.app')

@section('title', 'Assessment in Progress: ' . $quiz->title . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div
    class="min-h-screen bg-[#F8F9FA] pt-24 pb-20"
    x-data="{
        questions: {{ Js::from($questions) }},
        answers: {{ Js::from($savedAnswers) }},
        currentIndex: 0,
        timeRemaining: {{ $timeRemainingSeconds ?? 'null' }},
        saveStatus: 'idle',
        submitModalOpen: false,
        timerInterval: null,

        init() {
            if (this.timeRemaining !== null && this.timeRemaining > 0) {
                this.timerInterval = setInterval(() => {
                    this.timeRemaining--;
                    if (this.timeRemaining <= 0) {
                        clearInterval(this.timerInterval);
                        this.autoSubmitOnExpiry();
                    }
                }, 1000);
            }
        },

        get formattedTime() {
            if (this.timeRemaining === null) return null;
            const minutes = Math.floor(Math.max(0, this.timeRemaining) / 60);
            const seconds = Math.max(0, this.timeRemaining) % 60;
            return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        },

        get currentQuestion() {
            return this.questions[this.currentIndex] || null;
        },

        get answeredCount() {
            return Object.keys(this.answers).filter(k => this.answers[k] !== null && this.answers[k] !== undefined).length;
        },

        async selectOption(questionId, optionId) {
            this.answers[questionId] = optionId;
            this.saveStatus = 'saving';

            try {
                const response = await fetch('{{ route('student.quizzes.save-answer', [$course, $quiz, $attempt]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        option_id: optionId
                    })
                });

                if (response.ok) {
                    this.saveStatus = 'saved';
                    setTimeout(() => {
                        if (this.saveStatus === 'saved') this.saveStatus = 'idle';
                    }, 2000);
                } else {
                    this.saveStatus = 'error';
                }
            } catch (e) {
                console.error(e);
                this.saveStatus = 'error';
            }
        },

        nextQuestion() {
            if (this.currentIndex < this.questions.length - 1) {
                this.currentIndex++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevQuestion() {
            if (this.currentIndex > 0) {
                this.currentIndex--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        goToQuestion(index) {
            this.currentIndex = index;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        autoSubmitOnExpiry() {
            alert('Time has expired. Your assessment is being submitted now.');
            document.getElementById('quiz-submit-form').submit();
        }
    }"
>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- ASSESSMENT TOP BAR WITH AUTHORITATIVE TIMER & PROGRESS --}}
        <x-ui.card padding="sm" class="border-slate-200/90 shadow-2xs sticky top-20 z-20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 border border-orange-100">
                        <x-ui.icon name="academic-cap" size="sm" />
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-[#FB6221] font-bold block truncate">
                            {{ $quiz->context_label }} • Attempt #{{ $attempt->attempt_number }}
                        </span>
                        <h1 class="text-sm sm:text-base font-bold text-slate-900 font-heading truncate">
                            {{ $quiz->title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-5 justify-between sm:justify-end shrink-0 flex-wrap">
                    {{-- TIMER DISPLAY --}}
                    <template x-if="formattedTime !== null">
                        <div
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border font-mono text-xs font-bold transition-colors"
                            :class="{
                                'bg-rose-50 border-rose-300 text-rose-700 animate-pulse': timeRemaining < 60,
                                'bg-amber-50 border-amber-300 text-amber-800': timeRemaining >= 60 && timeRemaining < 300,
                                'bg-slate-50 border-slate-200 text-slate-800': timeRemaining >= 300
                            }"
                        >
                            <x-ui.icon name="clock" size="xs" />
                            <span x-text="formattedTime"></span>
                        </div>
                    </template>

                    {{-- ANSWERED COUNTER & SAVE STATUS --}}
                    <div class="flex items-center gap-2 text-xs font-mono">
                        <span class="text-slate-500">
                            Answered: <strong class="text-slate-900" x-text="`${answeredCount}/${questions.length}`"></strong>
                        </span>

                        <span x-show="saveStatus === 'saving'" x-cloak class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                            <x-ui.icon name="spinner" size="xs" />
                            <span>Saving...</span>
                        </span>
                        <span x-show="saveStatus === 'saved'" x-cloak class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold">
                            <x-ui.icon name="check" size="xs" />
                            <span>Saved</span>
                        </span>
                        <span x-show="saveStatus === 'error'" x-cloak class="inline-flex items-center gap-1 text-[11px] text-rose-600 font-semibold">
                            <x-ui.icon name="x" size="xs" />
                            <span>Save failed</span>
                        </span>
                    </div>

                    <x-ui.button
                        type="button"
                        variant="primary"
                        size="xs"
                        @click="submitModalOpen = true"
                    >
                        Submit Test
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>

        {{-- QUESTION NAVIGATOR PILLS --}}
        <x-ui.card padding="sm" class="border-slate-200/90 shadow-2xs flex items-center gap-2 overflow-x-auto">
            <span class="text-[11px] font-mono text-slate-400 uppercase font-bold shrink-0 mr-1">
                Jump to:
            </span>
            <template x-for="(q, idx) in questions" :key="q.id">
                <button
                    type="button"
                    @click="goToQuestion(idx)"
                    class="w-8 h-8 rounded-xl font-mono text-xs font-bold flex items-center justify-center transition-all cursor-pointer shrink-0"
                    :class="{
                        'bg-[#0A0A0A] text-white ring-2 ring-[#FB6221] shadow-2xs': currentIndex === idx,
                        'bg-emerald-100 text-emerald-800 border border-emerald-200': currentIndex !== idx && answers[q.id],
                        'bg-slate-100 text-slate-600 hover:bg-slate-200': currentIndex !== idx && !answers[q.id]
                    }"
                    x-text="idx + 1"
                ></button>
            </template>
        </x-ui.card>

        {{-- MAIN QUESTION CARD --}}
        <template x-if="currentQuestion">
            <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">

                {{-- QUESTION HEADER --}}
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <span
                        class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-bold uppercase bg-slate-100 text-slate-800"
                        x-text="`Question ${currentIndex + 1} of ${questions.length}`"
                    ></span>

                    <span class="text-xs font-mono font-bold text-slate-500" x-text="`${currentQuestion.marks} Mark${currentQuestion.marks > 1 ? 's' : ''}`"></span>
                </div>

                {{-- QUESTION TEXT --}}
                <div class="text-base sm:text-xl font-bold text-slate-900 leading-relaxed font-sans" x-text="currentQuestion.question_text"></div>

                {{-- OPTIONS LIST --}}
                <div class="space-y-3 pt-2">
                    <template x-for="option in currentQuestion.options" :key="option.id">
                        <label
                            class="p-4 rounded-2xl border flex items-center gap-4 transition-all cursor-pointer select-none"
                            :class="answers[currentQuestion.id] === option.id ? 'border-[#FB6221] bg-orange-50/50 ring-2 ring-[#FB6221]/30 shadow-2xs font-semibold' : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50/60'"
                            @click="selectOption(currentQuestion.id, option.id)"
                        >
                            <input
                                type="radio"
                                :name="`q_${currentQuestion.id}`"
                                :value="option.id"
                                :checked="answers[currentQuestion.id] === option.id"
                                class="w-4 h-4 text-[#FB6221] focus:ring-[#FB6221]"
                            >
                            <span class="text-xs sm:text-sm text-slate-800 font-sans leading-relaxed flex-1" x-text="option.option_text"></span>
                        </label>
                    </template>
                </div>

                {{-- NAVIGATION CONTROLS --}}
                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <x-ui.button
                        type="button"
                        variant="secondary"
                        size="sm"
                        icon="arrow-left"
                        @click="prevQuestion()"
                        x-bind:disabled="currentIndex === 0"
                    >
                        Previous Question
                    </x-ui.button>

                    <div>
                        <template x-if="currentIndex < questions.length - 1">
                            <x-ui.button
                                type="button"
                                variant="dark"
                                size="sm"
                                icon="arrow-right"
                                class="hover:!bg-[#FB6221]"
                                @click="nextQuestion()"
                            >
                                Next Question
                            </x-ui.button>
                        </template>

                        <template x-if="currentIndex === questions.length - 1">
                            <x-ui.button
                                type="button"
                                variant="primary"
                                size="sm"
                                icon="check"
                                class="bg-emerald-600 hover:bg-emerald-700"
                                @click="submitModalOpen = true"
                            >
                                Complete &amp; Submit
                            </x-ui.button>
                        </template>
                    </div>
                </div>

            </x-ui.card>
        </template>

    </div>

    {{-- SUBMIT CONFIRMATION MODAL --}}
    <div
        x-show="submitModalOpen"
        x-cloak
        role="dialog"
        aria-modal="true"
        aria-labelledby="submit-modal-title"
        class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div
            @click.outside="submitModalOpen = false"
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-4"
        >
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-[#FB6221] flex items-center justify-center mx-auto border border-orange-100">
                <x-ui.icon name="check-circle" size="md" />
            </div>

            <div class="text-center space-y-1">
                <h3 id="submit-modal-title" class="font-heading font-black text-xl text-slate-900">Submit Assessment?</h3>
                <p class="text-xs text-slate-600 font-sans">
                    You have answered <strong x-text="`${answeredCount} of ${questions.length}`"></strong> questions.
                </p>
                <template x-if="answeredCount < questions.length">
                    <p class="text-[11px] font-mono text-amber-600 pt-1">
                        ⚠️ You have <span x-text="questions.length - answeredCount"></span> unanswered question(s). Unanswered questions receive 0 marks.
                    </p>
                </template>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <x-ui.button
                    type="button"
                    variant="secondary"
                    size="sm"
                    @click="submitModalOpen = false"
                >
                    Review Answers
                </x-ui.button>

                <form
                    id="quiz-submit-form"
                    method="POST"
                    action="{{ route('student.quizzes.submit', [$course, $quiz, $attempt]) }}"
                >
                    @csrf
                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="sm"
                    >
                        Confirm &amp; Submit
                    </x-ui.button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
