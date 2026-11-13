@extends('layouts.auth')

@section('title', 'Assignments — ' . $course->name . ' | ZebLearn LMS')

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto space-y-6">
        {{-- BREADCRUMBS & HEADER --}}
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors">{{ $course->name }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Course Assignments</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        Course Assignments &amp; Projects
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ $course->name }} • Submit deliverables, track deadlines, and view instructor evaluations.
                    </p>
                </div>

                <x-ui.button href="{{ route('student.courses.show', $course) }}" variant="secondary" size="sm" icon="arrow-left">
                    Back to Course
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

        {{-- ASSIGNMENTS LIST --}}
        @if ($assignments->isEmpty())
            <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs text-center py-12">
                <x-ui.empty-state
                    title="No assignments available yet"
                    description="Your instructor has not published any assignments for this course. Please check back later."
                    icon="academic-cap"
                >
                    <x-ui.button href="{{ route('student.courses.show', $course) }}" variant="secondary" size="sm" class="mt-4">
                        Return to Syllabus
                    </x-ui.button>
                </x-ui.empty-state>
            </x-ui.card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($assignments as $asgn)
                    @php
                        $subs = $userSubmissions->get($asgn->id, collect());
                        $latest = $subs->first();
                        $remaining = $asgn->remainingAttemptsForUser(auth()->user());
                    @endphp

                    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs flex flex-col justify-between hover:border-slate-300 transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                @if ($asgn->isLessonLevel())
                                    <x-ui.badge variant="neutral" size="sm">Lesson Task</x-ui.badge>
                                @elseif ($asgn->isModuleLevel())
                                    <x-ui.badge variant="warning" size="sm">Module Project</x-ui.badge>
                                @else
                                    <x-ui.badge variant="info" size="sm">Course Final Project</x-ui.badge>
                                @endif

                                @if ($latest)
                                    @if ($latest->isGraded())
                                        <x-ui.badge variant="success" size="sm">
                                            Score: {{ $latest->marks_awarded }}/{{ $asgn->max_marks }}
                                        </x-ui.badge>
                                    @elseif ($latest->isResubmissionRequired())
                                        <x-ui.badge variant="warning" size="sm">Resubmission</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="info" size="sm">Under Review</x-ui.badge>
                                    @endif
                                @else
                                    <x-ui.badge variant="neutral" size="sm">Not Submitted</x-ui.badge>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-heading font-bold text-base text-slate-900 group-hover:text-[#FB6221]">
                                    <a href="{{ route('student.assignments.show', [$course, $asgn]) }}" class="hover:text-[#FB6221] transition-colors">
                                        {{ $asgn->title }}
                                    </a>
                                </h3>
                                @if ($asgn->description)
                                    <p class="text-xs text-slate-500 line-clamp-2 mt-1 leading-relaxed">
                                        {{ $asgn->description }}
                                    </p>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-[11px] font-mono text-slate-500 pt-2 border-t border-slate-100">
                                <div>
                                    <span class="text-slate-400 block text-[10px]">Max Marks:</span>
                                    <span class="font-semibold text-slate-800">{{ $asgn->max_marks }} pts</span>
                                </div>

                                <div>
                                    <span class="text-slate-400 block text-[10px]">Due Date:</span>
                                    @if ($asgn->due_at)
                                        <span class="{{ $asgn->isPastDue() ? 'text-rose-600 font-semibold' : 'text-slate-800' }}">
                                            {{ $asgn->due_at->format('d M, h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">Open-ended</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-mono text-slate-400">
                                @if ($subs->isNotEmpty())
                                    {{ $subs->count() }} of {{ $asgn->max_attempts }} {{ Str::plural('attempt', $asgn->max_attempts) }}
                                @else
                                    {{ $remaining }} {{ Str::plural('attempt', $remaining) }} left
                                @endif
                            </span>

                            <x-ui.button
                                href="{{ route('student.assignments.show', [$course, $asgn]) }}"
                                variant="{{ $latest ? 'secondary' : 'primary' }}"
                                size="sm"
                                icon="arrow-right"
                            >
                                {{ $latest ? 'View Submission' : 'Open Assignment' }}
                            </x-ui.button>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

