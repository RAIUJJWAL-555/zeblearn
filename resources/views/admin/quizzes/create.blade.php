@extends('layouts.admin')

@section('title', 'Create New Quiz | ZebLearn LMS')
@section('page-title', 'Create Quiz')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.quizzes.index') }}" class="hover:text-[#FB6221] transition-colors">Quizzes</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Create</span>
        </nav>
        <x-ui.button
            href="{{ route('admin.quizzes.index') }}"
            variant="secondary"
            size="sm"
        >
            Cancel
        </x-ui.button>
    </div>

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
            Create Assessment Quiz
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Configure MCQ assessment settings and link to course curriculum.
        </p>
    </div>

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <x-ui.alert variant="danger">
            <strong class="font-bold block mb-1">Please correct the errors below:</strong>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- FORM WITH ALPINE COURSE/MODULE/LESSON CASCADE --}}
    <form
        method="POST"
        action="{{ route('admin.quizzes.store') }}"
        class="space-y-6"
        x-data="{
            courses: {{ Js::from($courses) }},
            selectedCourseId: '{{ old('course_id', $preselectedCourseId) }}',
            selectedModuleId: '{{ old('course_module_id', $preselectedModuleId) }}',
            selectedLessonId: '{{ old('lesson_id', $preselectedLessonId) }}',
            get modules() {
                const c = this.courses.find(item => item.id == this.selectedCourseId);
                return c && c.modules ? c.modules : [];
            },
            get lessons() {
                const m = this.modules.find(item => item.id == this.selectedModuleId);
                return m && m.lessons ? m.lessons : [];
            }
        }"
    >
        @csrf

        {{-- SECTION 1: ASSESSMENT CONTEXT --}}
        <x-ui.card
            title="1. Curriculum Assessment Scope"
            subtitle="Select where this quiz belongs: Course-level (final exam), Module-level (section review), or Lesson-level."
            padding="md"
        >
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Course Select --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Course <span class="text-rose-500">*</span>
                    </label>
                    <select
                        name="course_id"
                        x-model="selectedCourseId"
                        @change="selectedModuleId = ''; selectedLessonId = '';"
                        required
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden bg-white text-slate-800 cursor-pointer"
                    >
                        <option value="">-- Select Course --</option>
                        <template x-for="c in courses" :key="c.id">
                            <option :value="c.id" x-text="c.name" :selected="c.id == selectedCourseId"></option>
                        </template>
                    </select>
                </div>

                {{-- Module Select (Optional) --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Module <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <select
                        name="course_module_id"
                        x-model="selectedModuleId"
                        @change="selectedLessonId = '';"
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden bg-white text-slate-800 cursor-pointer"
                    >
                        <option value="">Course-Level (All Modules)</option>
                        <template x-for="m in modules" :key="m.id">
                            <option :value="m.id" x-text="m.title" :selected="m.id == selectedModuleId"></option>
                        </template>
                    </select>
                </div>

                {{-- Lesson Select (Optional) --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Lesson <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <select
                        name="lesson_id"
                        x-model="selectedLessonId"
                        :disabled="!selectedModuleId"
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden bg-white text-slate-800 disabled:bg-slate-50 disabled:text-slate-400 cursor-pointer"
                    >
                        <option value="">Module-Level Quiz</option>
                        <template x-for="l in lessons" :key="l.id">
                            <option :value="l.id" x-text="l.title" :selected="l.id == selectedLessonId"></option>
                        </template>
                    </select>
                </div>
            </div>
        </x-ui.card>

        {{-- SECTION 2: BASIC QUIZ INFORMATION --}}
        <x-ui.card
            title="2. Quiz Details"
            subtitle="Configure title, description, and instructions displayed before attempt."
            padding="md"
        >
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Quiz Title <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="e.g., SAP FICO General Ledger Mastery Assessment"
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Short Description <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <textarea
                        name="description"
                        rows="2"
                        placeholder="Brief summary of what topics this assessment covers..."
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Student Instructions <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <textarea
                        name="instructions"
                        rows="3"
                        placeholder="Detailed test instructions shown before the student starts..."
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden"
                    >{{ old('instructions') }}</textarea>
                </div>
            </div>
        </x-ui.card>

        {{-- SECTION 3: ASSESSMENT RULES & TIMING --}}
        <x-ui.card
            title="3. Rules & Evaluation Parameters"
            subtitle="Set passing criteria, attempt limits, timing, and randomization behavior."
            padding="md"
        >
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Passing Percentage <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="passing_percentage"
                            value="{{ old('passing_percentage', 70) }}"
                            min="0"
                            max="100"
                            required
                            class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 pr-8 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden font-mono"
                        >
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-mono">%</span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Allowed Attempts <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="number"
                        name="attempts_allowed"
                        value="{{ old('attempts_allowed', 1) }}"
                        min="1"
                        max="100"
                        required
                        class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden font-mono"
                    >
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                        Time Limit <span class="text-slate-400 font-normal">(Minutes, optional)</span>
                    </label>
                    <div class="relative">
                        <input
                            type="number"
                            name="time_limit_minutes"
                            value="{{ old('time_limit_minutes') }}"
                            min="1"
                            max="360"
                            placeholder="e.g. 30"
                            class="w-full text-xs rounded-xl border border-slate-200 px-3.5 py-2.5 pr-12 focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden font-mono"
                        >
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-mono">min</span>
                    </div>
                </div>
            </div>

            {{-- TOGGLE CHECKBOXES --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                <label class="flex items-start gap-3 p-3.5 rounded-2xl border border-slate-200/80 hover:bg-slate-50 transition-colors cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="shuffle_questions"
                        value="1"
                        {{ old('shuffle_questions') ? 'checked' : '' }}
                        class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]"
                    >
                    <div>
                        <strong class="text-xs font-bold text-slate-900 block">Shuffle Questions</strong>
                        <span class="text-[11px] text-slate-500">Randomize question sequence for each attempt</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3.5 rounded-2xl border border-slate-200/80 hover:bg-slate-50 transition-colors cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="shuffle_options"
                        value="1"
                        {{ old('shuffle_options') ? 'checked' : '' }}
                        class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]"
                    >
                    <div>
                        <strong class="text-xs font-bold text-slate-900 block">Shuffle Options</strong>
                        <span class="text-[11px] text-slate-500">Randomize MCQ choice ordering for students</span>
                    </div>
                </label>

                <label class="flex items-start gap-3 p-3.5 rounded-2xl border border-slate-200/80 hover:bg-slate-50 transition-colors cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="show_correct_answers"
                        value="1"
                        {{ old('show_correct_answers', true) ? 'checked' : '' }}
                        class="mt-0.5 rounded text-[#FB6221] focus:ring-[#FB6221]"
                    >
                    <div>
                        <strong class="text-xs font-bold text-slate-900 block">Review Answers</strong>
                        <span class="text-[11px] text-slate-500">Reveal correct answers and explanations after test submission</span>
                    </div>
                </label>
            </div>
        </x-ui.card>

        {{-- SUBMIT ACTIONS --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <x-ui.button
                href="{{ route('admin.quizzes.index') }}"
                variant="secondary"
                size="md"
            >
                Cancel
            </x-ui.button>
            <x-ui.button
                type="submit"
                variant="primary"
                size="md"
            >
                Create Quiz &amp; Add Questions
            </x-ui.button>
        </div>
    </form>
</div>
@endsection
