@extends('layouts.admin')

@section('title', $quiz->title . ' - Quiz Management | ZebLearn LMS')
@section('page-title', 'Quiz Builder')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.quizzes.index') }}" class="hover:text-[#FB6221] transition-colors">Quizzes</a>
            <span>/</span>
            <span class="text-slate-900 font-bold truncate max-w-xs">{{ $quiz->title }}</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.quizzes.index') }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Quizzes
        </x-ui.button>
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

    {{-- PUBLISHING READINESS ALERT --}}
    @if (! empty($publishingErrors))
        <x-ui.alert variant="warning">
            <strong class="font-bold flex items-center gap-2 mb-1">
                Publishing Requirements ({{ count($publishingErrors) }} Pending Action{{ count($publishingErrors) > 1 ? 's' : '' }}):
            </strong>
            <ul class="list-disc list-inside space-y-0.5 font-mono text-[11px] text-amber-800">
                @foreach ($publishingErrors as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- QUIZ HEADER CARD --}}
    <x-ui.card padding="md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2 flex-wrap">
                    @if ($quiz->isLessonLevel())
                        <x-ui.badge variant="primary" size="xs" class="font-mono font-bold uppercase">
                            {{ $quiz->context_label }}
                        </x-ui.badge>
                    @elseif ($quiz->isModuleLevel())
                        <x-ui.badge variant="info" size="xs" class="font-mono font-bold uppercase">
                            {{ $quiz->context_label }}
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="neutral" size="xs" class="font-mono font-bold uppercase">
                            {{ $quiz->context_label }}
                        </x-ui.badge>
                    @endif

                    @if ($quiz->isPublished())
                        <x-ui.badge variant="success" size="xs" dot>
                            Published &amp; Active
                        </x-ui.badge>
                    @else
                        <x-ui.badge variant="warning" size="xs" dot>
                            Draft
                        </x-ui.badge>
                    @endif

                    <span class="text-xs font-mono text-slate-500">
                        Course: <strong class="text-slate-800">{{ $quiz->course->name }}</strong>
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $quiz->title }}
                </h1>

                @if ($quiz->description)
                    <p class="text-xs sm:text-sm text-slate-600 max-w-2xl leading-relaxed">
                        {{ $quiz->description }}
                    </p>
                @endif

                <div class="flex items-center gap-4 pt-2 text-xs font-mono text-slate-500 flex-wrap">
                    <span>Total Marks: <strong class="text-slate-900">{{ $quiz->total_marks }}</strong></span>
                    <span>•</span>
                    <span>Pass Score: <strong class="text-slate-900">{{ $quiz->passing_percentage }}%</strong></span>
                    <span>•</span>
                    <span>Time Limit: <strong class="text-slate-900">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' min' : 'None' }}</strong></span>
                    <span>•</span>
                    <span>Attempts: <strong class="text-slate-900">{{ $quiz->attempts_allowed }} max</strong></span>
                </div>
            </div>

            <div class="flex items-center gap-2.5 self-start md:self-center shrink-0 flex-wrap">
                <x-ui.button
                    href="{{ route('admin.quizzes.edit', $quiz) }}"
                    variant="secondary"
                    size="sm"
                    icon="pencil"
                >
                    Edit Settings
                </x-ui.button>

                <form method="POST" action="{{ route('admin.quizzes.toggle-status', $quiz) }}">
                    @csrf
                    @method('PATCH')
                    <x-ui.button
                        type="submit"
                        variant="{{ $quiz->isPublished() ? 'dark' : 'primary' }}"
                        size="sm"
                    >
                        {{ $quiz->isPublished() ? 'Unpublish to Draft' : 'Publish Assessment' }}
                    </x-ui.button>
                </form>
            </div>
        </div>
    </x-ui.card>

    {{-- MAIN CONTENT GRID: QUESTIONS (LEFT) + ATTEMPTS (RIGHT) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- QUESTIONS COLUMN (8 COLS) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- ADD QUESTION FORM CARD --}}
            <div
                class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-2xs space-y-5"
                x-data="{
                    isOpen: false,
                    options: ['', ''],
                    correctOption: 0,
                    addOption() {
                        if (this.options.length < 6) {
                            this.options.push('');
                        }
                    },
                    removeOption(index) {
                        if (this.options.length > 2) {
                            this.options.splice(index, 1);
                            if (this.correctOption >= this.options.length) {
                                this.correctOption = this.options.length - 1;
                            }
                        }
                    }
                }"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="font-heading font-bold text-base text-slate-900">
                            Assessment Questions ({{ $quiz->questions->count() }})
                        </h2>
                        <p class="text-xs text-slate-500">
                            Build single-choice MCQ questions. Exactly 1 option must be designated as correct.
                        </p>
                    </div>

                    @if (! $quiz->attempts()->exists())
                        <x-ui.button
                            type="button"
                            @click="isOpen = !isOpen"
                            variant="dark"
                            size="xs"
                            icon="plus"
                        >
                            <span x-text="isOpen ? 'Close Form' : 'Add Question'"></span>
                        </x-ui.button>
                    @endif
                </div>

                {{-- COLLAPSIBLE ADD QUESTION FORM --}}
                <div x-show="isOpen" x-cloak class="pt-2 space-y-4">
                    <form method="POST" action="{{ route('admin.quizzes.questions.store', $quiz) }}" class="space-y-4">
                        @csrf

                        <div class="space-y-1.5">
                            <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                                Question Text <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                name="question_text"
                                rows="2"
                                required
                                placeholder="e.g. Which SAP FICO transaction code is used to post a General Ledger document?"
                                class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                                    Marks <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="marks"
                                    value="1"
                                    min="1"
                                    max="100"
                                    required
                                    class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden font-mono"
                                >
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                                    Explanation <span class="text-slate-400 font-normal">(Optional post-test review)</span>
                                </label>
                                <input
                                    type="text"
                                    name="explanation"
                                    placeholder="e.g. FB50 is standard for posting GL accounts directly."
                                    class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden"
                                >
                            </div>
                        </div>

                        {{-- DYNAMIC ANSWER OPTIONS --}}
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                                    Answer Options (Select the correct radio button)
                                </span>
                                <button
                                    type="button"
                                    @click="addOption()"
                                    class="text-xs text-[#FB6221] hover:underline font-semibold flex items-center gap-1 cursor-pointer"
                                >
                                    + Add Another Option
                                </button>
                            </div>

                            <div class="space-y-2">
                                <template x-for="(opt, idx) in options" :key="idx">
                                    <div class="flex items-center gap-3">
                                        <input
                                            type="radio"
                                            name="correct_option"
                                            :value="idx"
                                            x-model="correctOption"
                                            required
                                            class="w-4 h-4 text-[#FB6221] focus:ring-[#FB6221] cursor-pointer"
                                            title="Mark as correct answer"
                                        >
                                        <input
                                            type="text"
                                            :name="`options[${idx}]`"
                                            x-model="options[idx]"
                                            required
                                            :placeholder="`Option ${idx + 1}...`"
                                            class="flex-1 text-xs rounded-xl border border-slate-200 px-3.5 py-2 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden"
                                        >
                                        <button
                                            type="button"
                                            x-show="options.length > 2"
                                            @click="removeOption(idx)"
                                            class="p-1.5 text-slate-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Remove option"
                                        >
                                            <x-ui.icon name="trash" size="xs" />
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="pt-3 flex justify-end gap-2">
                            <x-ui.button
                                type="button"
                                @click="isOpen = false"
                                variant="secondary"
                                size="sm"
                            >
                                Cancel
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="sm"
                            >
                                Save Question
                            </x-ui.button>
                        </div>
                    </form>
                </div>

                {{-- QUESTIONS LIST --}}
                <div class="space-y-4 pt-2">
                    @forelse ($quiz->questions as $qIndex => $question)
                        <div class="rounded-2xl p-4 sm:p-5 border border-slate-200/80 bg-slate-50/40 space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-slate-200/80 text-slate-700">
                                        Question {{ $qIndex + 1 }} • {{ $question->marks }} Mark{{ $question->marks > 1 ? 's' : '' }}
                                    </span>
                                    <h3 class="text-sm font-bold text-slate-900 leading-snug">
                                        {{ $question->question_text }}
                                    </h3>
                                    @if ($question->explanation)
                                        <p class="text-[11px] text-slate-500 font-mono">
                                            💡 Explanation: {{ $question->explanation }}
                                        </p>
                                    @endif
                                </div>

                                @if (! $quiz->attempts()->exists())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}"
                                        onsubmit="return confirm('Delete this question?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="p-1 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer"
                                            title="Delete question"
                                        >
                                            <x-ui.icon name="trash" size="xs" />
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Options --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                @foreach ($question->options as $opt)
                                    <div class="p-2.5 rounded-xl border text-xs flex items-center gap-2 {{ $opt->is_correct ? 'bg-emerald-50/80 border-emerald-300 text-emerald-900 font-semibold' : 'bg-white border-slate-200 text-slate-600' }}">
                                        @if ($opt->is_correct)
                                            <x-ui.icon name="check-circle" size="xs" class="text-emerald-600 shrink-0" />
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                                        @endif
                                        <span class="truncate">{{ $opt->option_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center border border-dashed border-slate-300 rounded-2xl">
                            <p class="text-xs font-semibold text-slate-600">No questions added yet.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click "Add Question" above to start building the evaluation.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ATTEMPTS COLUMN (4 COLS) --}}
        <div class="lg:col-span-4 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-tight">
                        Student Attempts
                    </h3>
                    <p class="text-[11px] font-mono text-slate-400 mt-0.5">
                        {{ $quiz->attempts->count() }} total recorded
                    </p>
                </div>
                <span class="text-xs font-mono font-bold text-[#FB6221]">
                    Pass: {{ $quiz->passing_percentage }}%
                </span>
            </div>

            <div class="space-y-3 max-h-[70vh] overflow-y-auto pr-1">
                @forelse ($quiz->attempts as $att)
                    <div class="p-3.5 rounded-2xl border border-slate-100 bg-slate-50/50 space-y-1.5 text-xs">
                        <div class="flex items-center justify-between">
                            <strong class="font-bold text-slate-900 truncate max-w-[150px]">
                                {{ $att->user->name }}
                            </strong>
                            @if ($att->is_passed)
                                <x-ui.badge variant="success" size="xs" dot>
                                    PASSED
                                </x-ui.badge>
                            @elseif ($att->isSubmitted() || $att->isExpired())
                                <x-ui.badge variant="danger" size="xs" dot>
                                    FAILED
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="warning" size="xs" dot>
                                    IN PROGRESS
                                </x-ui.badge>
                            @endif
                        </div>

                        <div class="flex items-center justify-between text-[11px] font-mono text-slate-500">
                            <span>Score: <strong class="text-slate-800">{{ $att->score ?? 0 }}/{{ $att->total_marks }}</strong> ({{ $att->percentage ?? 0 }}%)</span>
                            <span>Attempt #{{ $att->attempt_number }}</span>
                        </div>

                        <div class="flex items-center justify-between text-[10px] font-mono text-slate-400 pt-1.5 border-t border-slate-200/50">
                            <span>{{ $att->submitted_at?->format('d M, h:i A') ?? 'Active' }}</span>
                            <a
                                href="{{ route('admin.quizzes.attempts.show', [$quiz, $att]) }}"
                                class="text-[#FB6221] hover:underline font-bold inline-flex items-center gap-1"
                            >
                                <span>View Answers</span>
                                <x-ui.icon name="arrow-right" size="xs" />
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-400">
                        No student attempts yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
