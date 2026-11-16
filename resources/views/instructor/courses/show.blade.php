@extends('layouts.instructor')

@section('title', $course->name . ' - Syllabus & Overview | ZebLearn LMS')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS & COURSE HERO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Portal</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span>/</span>
                <span class="text-slate-900 font-bold truncate">{{ $course->name }}</span>
            </nav>
            <div class="flex items-center gap-2.5 flex-wrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                    {{ $course->category_label }}
                </span>
                <x-ui.badge variant="success" size="xs">
                    {{ $enrolledCount }} Enrolled Students
                </x-ui.badge>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight mt-1">
                {{ $course->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                {{ $course->short_description }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <x-ui.button
                href="{{ route('instructor.courses.students', $course) }}"
                variant="primary"
                size="sm"
                icon="user-group"
            >
                View Enrolled Students ({{ $enrolledCount }})
            </x-ui.button>
        </div>
    </div>

    {{-- COURSE STATS ROW --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-ui.card padding="sm">
            <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Curriculum</span>
            <span class="text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $course->modules->count() }} Modules</span>
        </x-ui.card>
        <x-ui.card padding="sm">
            <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Total Lessons</span>
            <span class="text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $course->modules->sum(fn($m) => $m->lessons->count()) }} Lessons</span>
        </x-ui.card>
        <x-ui.card padding="sm">
            <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Quizzes</span>
            <span class="text-2xl font-black text-[#FB6221] font-heading mt-0.5 block">{{ $course->quizzes->count() }} Quizzes</span>
        </x-ui.card>
        <x-ui.card padding="sm">
            <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Assignments</span>
            <span class="text-2xl font-black text-emerald-600 font-heading mt-0.5 block">{{ $course->assignments->count() }} Projects</span>
        </x-ui.card>
    </div>

    {{-- TWO COLUMN: CURRICULUM + ASSESSMENTS SIDEBAR --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT 2 COLS: MODULES & LESSONS --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <h2 class="font-heading font-bold text-base text-slate-900">
                    Course Curriculum &amp; Modules
                </h2>
                <span class="text-xs text-slate-500 font-mono">
                    {{ $course->modules->count() }} Modules Published
                </span>
            </div>

            @if ($course->modules->isEmpty())
                <x-ui.card padding="md" class="text-center py-8">
                    <p class="text-xs text-slate-500">No modules have been published for this course yet.</p>
                </x-ui.card>
            @else
                <div class="space-y-4">
                    @foreach ($course->modules as $index => $module)
                        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-3">
                            <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-2.5">
                                <div>
                                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#FB6221] block">
                                        Module {{ $index + 1 }}
                                    </span>
                                    <h3 class="font-heading font-bold text-sm sm:text-base text-slate-900">
                                        {{ $module->title }}
                                    </h3>
                                </div>
                                <span class="text-xs text-slate-400 font-mono shrink-0">
                                    {{ $module->lessons->count() }} {{ Str::plural('lesson', $module->lessons->count()) }}
                                </span>
                            </div>

                            @if ($module->lessons->isEmpty())
                                <p class="text-xs text-slate-400 italic">No lessons in this module.</p>
                            @else
                                <div class="divide-y divide-slate-100">
                                    @foreach ($module->lessons as $lIndex => $lesson)
                                        <div class="py-2 flex items-center justify-between gap-3 text-xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <span class="w-5 h-5 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center font-mono text-[10px] font-bold shrink-0">
                                                    {{ $lIndex + 1 }}
                                                </span>
                                                <span class="font-medium text-slate-800 truncate">
                                                    {{ $lesson->title }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0 text-[11px] text-slate-400 font-mono">
                                                <span>{{ $lesson->duration_minutes }}m</span>
                                                @if ($lesson->content_type === 'video')
                                                    <x-ui.badge variant="neutral" size="xs">Video</x-ui.badge>
                                                @else
                                                    <x-ui.badge variant="neutral" size="xs">Reading</x-ui.badge>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- RIGHT 1 COL: QUIZZES & ASSIGNMENTS OVERVIEW --}}
        <div class="space-y-6">

            {{-- ASSIGNMENTS BLOCK --}}
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="font-heading font-bold text-sm text-slate-900">
                        Course Assignments
                    </h3>
                    <a href="{{ route('instructor.assignments.index', ['course_id' => $course->id]) }}" class="text-xs font-semibold text-[#FB6221] hover:underline">
                        Review All
                    </a>
                </div>

                @if ($course->assignments->isEmpty())
                    <p class="text-xs text-slate-400 italic">No assignments created for this course.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($course->assignments as $asgn)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-900 block truncate">{{ $asgn->title }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $asgn->max_marks }} marks • {{ $asgn->submissions_count }} turned in</span>
                                </div>
                                <a
                                    href="{{ route('instructor.assignments.index', ['course_id' => $course->id]) }}"
                                    class="text-[#FB6221] font-semibold text-xs shrink-0 hover:underline"
                                >
                                    Grade
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- QUIZZES BLOCK --}}
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-3">
                <div class="border-b border-slate-100 pb-2">
                    <h3 class="font-heading font-bold text-sm text-slate-900">
                        Course Quizzes &amp; MCQs
                    </h3>
                </div>

                @if ($course->quizzes->isEmpty())
                    <p class="text-xs text-slate-400 italic">No quizzes linked to this course.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($course->quizzes as $quiz)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <span class="font-semibold text-slate-900 block truncate">{{ $quiz->title }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $quiz->questions_count }} questions • {{ $quiz->time_limit_minutes }}m</span>
                                </div>
                                <x-ui.badge variant="success" size="xs">Active</x-ui.badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>
    </div>
</div>
@endsection

