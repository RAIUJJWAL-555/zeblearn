@extends('layouts.app')

@section('title', $course->name . ' - Learning Syllabus | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
@php
    $progress = $progress ?? $courseProgress ?? [
        'total' => 0,
        'completed' => 0,
        'percentage' => 0,
        'is_completed' => false,
    ];
@endphp
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & NAVIGATION --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate max-w-sm">{{ $course->name }}</span>
            </nav>
            <x-ui.button
                href="{{ route('student.dashboard') }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Dashboard
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

        {{-- COURSE HEADER CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs relative overflow-hidden">
            <div class="flex items-center gap-2.5 mb-3 flex-wrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                    {{ $course->category_badge }}
                </span>
                <x-ui.badge variant="success" size="xs" dot>
                    Enrolled &amp; Active
                </x-ui.badge>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 font-heading tracking-tight leading-tight">
                        {{ $course->name }}
                    </h1>

                    <p class="text-xs sm:text-base text-slate-600 leading-relaxed mt-3 font-sans">
                        {{ $course->description ?? $course->short_description }}
                    </p>
                </div>

                {{-- RESUME / START LEARNING CTA --}}
                @if ($resumeLesson)
                    <div class="shrink-0">
                        <x-ui.button
                            href="{{ route('student.courses.resume', $course) }}"
                            variant="primary"
                            size="lg"
                            icon="arrow-right"
                        >
                            <span>{{ $progress['percentage'] > 0 ? 'Resume Learning' : 'Start Course' }}</span>
                        </x-ui.button>
                    </div>
                @endif
            </div>

            {{-- PROGRESS TRACKER BAR --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between text-xs font-mono mb-2">
                    <span class="font-bold text-slate-700 uppercase tracking-wider">
                        Course Progress: {{ $progress['percentage'] }}%
                    </span>
                    <span class="text-slate-500">
                        {{ $progress['completed'] }} of {{ $progress['total'] }} lessons completed
                    </span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div
                        class="bg-gradient-to-r from-[#FB6221] to-emerald-500 h-2.5 rounded-full transition-all duration-500"
                        style="width: {{ $progress['percentage'] }}%"
                    ></div>
                </div>
            </div>

            <div class="flex items-center gap-6 mt-6 pt-4 text-xs font-mono text-slate-500 flex-wrap">
                <div>
                    <span class="text-slate-400 uppercase text-[10px] block">Curriculum</span>
                    <strong class="text-slate-800 text-sm font-sans">{{ $modules->count() }} Modules</strong>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] block">Duration</span>
                    <strong class="text-slate-800 text-sm font-sans">{{ $course->display_duration }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] block">Total Lessons</span>
                    <strong class="text-slate-800 text-sm font-sans">{{ $progress['total'] }} Lessons</strong>
                </div>
            </div>
        </x-ui.card>

        {{-- MODULES & LESSONS SYLLABUS ACCORDION --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-slate-900">
                    Course Learning Modules
                </h2>
                <span class="font-mono text-xs text-slate-500">
                    {{ $progress['total'] }} Published Lessons
                </span>
            </div>

            @if ($modules->isEmpty())
                <x-ui.empty-state
                    title="Curriculum In Preparation"
                    description="Course curriculum is currently being updated by instructors. Please check back shortly."
                    icon="book"
                />
            @else
                <div class="space-y-4">
                    @foreach ($modules as $moduleIndex => $module)
                        <div
                            x-data="{ open: true }"
                            class="bg-white rounded-3xl border border-slate-200/90 shadow-2xs overflow-hidden transition-all"
                        >
                            {{-- Module Header Accordion Trigger --}}
                            <button
                                type="button"
                                @click="open = !open"
                                class="w-full text-left p-5 sm:p-6 bg-slate-50/70 hover:bg-slate-50 border-b border-slate-200/80 flex items-center justify-between gap-4 cursor-pointer transition-colors"
                            >
                                <div class="min-w-0">
                                    <span class="font-mono text-xs font-bold text-[#FB6221] uppercase tracking-widest block mb-1">
                                        MODULE {{ str_pad($moduleIndex + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading truncate">
                                        {{ $module->title }}
                                    </h3>
                                    @if ($module->description)
                                        <p class="text-xs text-slate-600 mt-1 leading-relaxed font-sans line-clamp-1">
                                            {{ $module->description }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 shrink-0">
                                    <span class="text-xs font-mono text-slate-500 hidden sm:inline">
                                        {{ $module->lessons->count() }} Lessons
                                    </span>
                                    <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500">
                                        <x-ui.icon
                                            name="chevron-down"
                                            size="sm"
                                            class="transition-transform duration-200"
                                            x-bind:class="open ? 'rotate-180' : ''"
                                        />
                                    </div>
                                </div>
                            </button>

                            {{-- Lessons List --}}
                            <div x-show="open" x-collapse class="divide-y divide-slate-100">
                                @forelse ($module->lessons as $lesson)
                                    @php
                                        $lProgress = $progressMap->get($lesson->id);
                                        $isCompleted = $lProgress && $lProgress->isCompleted();
                                    @endphp
                                    <a
                                        href="{{ route('student.courses.lesson', [$course, $lesson]) }}"
                                        class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-orange-50/40 transition-colors group"
                                    >
                                        <div class="flex items-center gap-3.5 min-w-0">
                                            {{-- Status / Type Icon --}}
                                            @if ($isCompleted)
                                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-200">
                                                    <x-ui.icon name="check" size="xs" class="text-emerald-600" />
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ $lesson->isVideo() ? 'bg-blue-50 text-blue-600' : ($lesson->isResource() ? 'bg-purple-50 text-purple-600' : 'bg-slate-100 text-slate-600') }}">
                                                    @if ($lesson->isVideo())
                                                        <x-ui.icon name="sparkles" size="xs" class="text-blue-600" />
                                                    @elseif ($lesson->isResource())
                                                        <x-ui.icon name="external-link" size="xs" class="text-purple-600" />
                                                    @else
                                                        <x-ui.icon name="book" size="xs" class="text-slate-600" />
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="font-bold text-sm text-slate-900 group-hover:text-[#FB6221] transition-colors truncate">
                                                        {{ $lesson->title }}
                                                    </h4>
                                                    @if ($isCompleted)
                                                        <x-ui.badge variant="success" size="xs">
                                                            Completed
                                                        </x-ui.badge>
                                                    @endif
                                                    @if ($lesson->quizzes && $lesson->quizzes->isNotEmpty())
                                                        <x-ui.badge variant="primary" size="xs">
                                                            Quiz ({{ $lesson->quizzes->count() }})
                                                        </x-ui.badge>
                                                    @endif
                                                </div>
                                                @if ($lesson->description)
                                                    <p class="text-xs text-slate-500 line-clamp-1 mt-0.5 font-sans">
                                                        {{ $lesson->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3 shrink-0">
                                            @if ($lesson->duration_minutes)
                                                <span class="text-xs font-mono text-slate-400">
                                                    {{ $lesson->formatted_duration }}
                                                </span>
                                            @endif
                                            <x-ui.icon
                                                name="arrow-right"
                                                size="sm"
                                                class="text-slate-300 group-hover:text-[#FB6221] group-hover:translate-x-0.5 transition-all"
                                            />
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-xs text-slate-400 text-center font-mono">
                                        No published lessons in this module.
                                    </div>
                                @endforelse
                            </div>

                            {{-- Module-Level Assessments --}}
                            @if ($module->quizzes && $module->quizzes->isNotEmpty())
                                <div class="bg-purple-50/40 p-4 sm:p-5 border-t border-purple-100">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-purple-900">
                                            Module Assessments ({{ $module->quizzes->count() }})
                                        </span>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach ($module->quizzes as $mQuiz)
                                            <div class="flex items-center justify-between gap-3 p-3 bg-white rounded-xl border border-purple-200/80 hover:border-purple-300 transition-colors">
                                                <div>
                                                    <h5 class="text-sm font-bold text-slate-900 font-heading">{{ $mQuiz->title }}</h5>
                                                    <div class="flex items-center gap-3 text-[11px] text-slate-500 font-mono mt-0.5">
                                                        <span>{{ $mQuiz->formatted_time_limit }}</span>
                                                        <span>&bull;</span>
                                                        <span>Pass: {{ $mQuiz->pass_percentage }}%</span>
                                                    </div>
                                                </div>
                                                <x-ui.button
                                                    href="{{ route('student.quizzes.show', [$course, $mQuiz]) }}"
                                                    variant="primary"
                                                    size="xs"
                                                    class="bg-purple-600 hover:bg-purple-700 text-white"
                                                >
                                                    Attempt Quiz
                                                </x-ui.button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- COURSE-LEVEL FINAL ASSESSMENTS --}}
        @if ($course->quizzes && $course->quizzes->isNotEmpty())
            <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-black text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-md space-y-4">
                <div class="flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-500/20 text-[#FB6221] border border-orange-500/30">
                            Course Certification &amp; Comprehensive Quizzes
                        </span>
                        <h3 class="text-xl font-black font-heading tracking-tight mt-2 text-white">
                            Course Assessments
                        </h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($course->quizzes as $cQuiz)
                        <div class="bg-slate-800/80 rounded-2xl p-5 border border-slate-700 flex flex-col justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-base text-white font-heading">{{ $cQuiz->title }}</h4>
                                @if ($cQuiz->description)
                                    <p class="text-xs text-slate-300 mt-1 line-clamp-2 font-sans">{{ $cQuiz->description }}</p>
                                @endif
                                <div class="flex items-center gap-3 text-xs font-mono text-slate-400 mt-3">
                                    <span>{{ $cQuiz->formatted_time_limit }}</span>
                                    <span>&bull;</span>
                                    <span>Pass: {{ $cQuiz->pass_percentage }}%</span>
                                    @if ($cQuiz->allowed_attempts)
                                        <span>&bull;</span>
                                        <span>Max: {{ $cQuiz->allowed_attempts }} attempts</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <x-ui.button
                                    href="{{ route('student.quizzes.show', [$course, $cQuiz]) }}"
                                    variant="primary"
                                    size="sm"
                                    icon="arrow-right"
                                >
                                    View Assessment
                                </x-ui.button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
