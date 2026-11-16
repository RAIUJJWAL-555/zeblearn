@extends('layouts.instructor')

@section('title', 'Instructor Dashboard | ZebLearn LMS')

@section('content')
<div class="space-y-8">
    {{-- INSTRUCTOR GREETING CARD --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                @if ($instructor->avatarUrl())
                    <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-xs shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-2xl shadow-xs shrink-0 border border-slate-700">
                        {{ strtoupper(substr($instructor->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                            Faculty Console
                        </span>
                        <x-ui.badge variant="success" size="xs" dot>
                            {{ $instructor->status }}
                        </x-ui.badge>
                        <span class="text-[11px] font-mono text-slate-400">
                            {{ now()->format('l, d F Y') }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight mt-1">
                        Welcome back, {{ $instructor->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        {{ $instructor->designation ?: 'Academy Instructor' }} • Managing {{ $totalCourses }} Assigned {{ Str::plural('Course', $totalCourses) }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                <x-ui.button
                    href="{{ route('instructor.courses.index') }}"
                    variant="secondary"
                    size="sm"
                    icon="book"
                >
                    My Courses
                </x-ui.button>

                <x-ui.button
                    href="{{ route('instructor.assignments.index') }}"
                    variant="dark"
                    size="sm"
                    icon="document-check"
                >
                    Pending Reviews ({{ $pendingAssignmentsCount }})
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

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

    {{-- KPI METRICS GRID --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <x-ui.card padding="sm" class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Assigned Courses</span>
                <x-ui.icon name="book" size="xs" class="text-slate-400" />
            </div>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading block">{{ $totalCourses }}</span>
            <span class="text-[11px] text-slate-500 font-sans block">
                {{ $publishedCourses }} Published • {{ $draftCourses }} Draft
            </span>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Enrolled Students</span>
                <x-ui.icon name="user-group" size="xs" class="text-emerald-500" />
            </div>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading block">{{ $totalEnrolledStudents }}</span>
            <span class="text-[11px] text-slate-500 font-sans block">Approved active learners</span>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Pending Reviews</span>
                <x-ui.icon name="clock" size="xs" class="text-amber-500" />
            </div>
            <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading block">{{ $pendingAssignmentsCount }}</span>
            <span class="text-[11px] text-slate-500 font-sans block">Awaiting grading</span>
        </x-ui.card>

        <x-ui.card padding="sm" class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Recent Submissions</span>
                <x-ui.icon name="document-text" size="xs" class="text-[#FB6221]" />
            </div>
            <span class="text-2xl sm:text-3xl font-black text-[#FB6221] font-heading block">{{ $recentSubmissions->count() }}</span>
            <span class="text-[11px] text-slate-500 font-sans block">Recent deliverables</span>
        </x-ui.card>
    </div>

    {{-- SECTION: QUICK ACCESS TO ASSIGNED COURSES --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                <h2 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-slate-900">
                    My Teaching Courses
                </h2>
            </div>
            <a href="{{ route('instructor.courses.index') }}" class="text-xs font-semibold text-[#FB6221] hover:underline">
                View All Assigned Courses &rarr;
            </a>
        </div>

        @if ($assignedCourses->isEmpty())
            <x-ui.card padding="lg" class="text-center">
                <div class="max-w-md mx-auto space-y-3 py-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-[#FB6221] flex items-center justify-center mx-auto border border-orange-200">
                        <x-ui.icon name="book" size="md" />
                    </div>
                    <h3 class="font-heading font-bold text-base text-slate-900">
                        No Courses Assigned Yet
                    </h3>
                    <p class="text-xs text-slate-500">
                        The academy administrator has not assigned any courses to your profile yet. Once assigned, your curriculum, students, and grading queues will appear here.
                    </p>
                </div>
            </x-ui.card>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($assignedCourses as $course)
                    <x-ui.card padding="md" class="relative group hover:border-[#FB6221]/40 transition-all flex flex-col justify-between">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                                    {{ $course->category_label }}
                                </span>
                                @if ($course->isAvailable())
                                    <x-ui.badge variant="success" size="xs">Available</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" size="xs">{{ ucfirst($course->status) }}</x-ui.badge>
                                @endif
                            </div>

                            <div>
                                <h3 class="font-heading font-bold text-base text-slate-900 group-hover:text-[#FB6221] transition-colors line-clamp-1">
                                    {{ $course->name }}
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-2 mt-1">
                                    {{ $course->short_description }}
                                </p>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-slate-500 pt-2 border-t border-slate-100 font-mono text-[11px]">
                                <span>{{ $course->modules->count() }} Modules</span>
                                <span>•</span>
                                <span>Role: {{ $course->pivot->role_or_title ?? 'Lead Instructor' }}</span>
                            </div>
                        </div>

                        <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <x-ui.button
                                href="{{ route('instructor.courses.students', $course) }}"
                                variant="secondary"
                                size="xs"
                                icon="user-group"
                            >
                                Students
                            </x-ui.button>

                            <x-ui.button
                                href="{{ route('instructor.courses.show', $course) }}"
                                variant="dark"
                                size="xs"
                                icon="arrow-right"
                            >
                                View Course
                            </x-ui.button>
                        </div>
                    </x-ui.card>
                @endforeach
            </div>
        @endif
    </div>

    {{-- TWO COLUMN SECTION: PENDING SUBMISSIONS + RECENT ENROLLMENTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- LEFT: PENDING ASSIGNMENT REVIEWS --}}
        <x-ui.card padding="md" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="space-y-0.5">
                    <h3 class="font-heading font-bold text-base text-slate-900">
                        Assignments Needing Review
                    </h3>
                    <p class="text-xs text-slate-500">
                        Student deliverables awaiting faculty evaluation
                    </p>
                </div>
                <x-ui.button
                    href="{{ route('instructor.assignments.index', ['status' => 'submitted']) }}"
                    variant="ghost"
                    size="xs"
                >
                    View Queue
                </x-ui.button>
            </div>

            @if ($recentSubmissions->isEmpty())
                <x-ui.empty-state
                    title="All Caught Up!"
                    description="There are no pending submissions requiring review across your assigned courses."
                    icon="check-circle"
                    :bordered="false"
                />
            @else
                <div class="space-y-3">
                    @foreach ($recentSubmissions->take(4) as $sub)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-all gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs sm:text-sm text-slate-900 truncate font-heading">
                                        {{ $sub->assignment?->title }}
                                    </span>
                                    @if ($sub->isGraded())
                                        <x-ui.badge variant="success" size="xs">Graded</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="xs">Review</x-ui.badge>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-500 block truncate mt-0.5">
                                    {{ $sub->student?->name }} • {{ $sub->assignment?->course?->name }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                    Submitted {{ $sub->submitted_at?->diffForHumans() }}
                                </span>
                            </div>

                            <x-ui.button
                                href="{{ route('instructor.assignments.submissions.show', [$sub->assignment, $sub]) }}"
                                variant="secondary"
                                size="xs"
                            >
                                {{ $sub->isGraded() ? 'Inspect' : 'Grade' }}
                            </x-ui.button>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        {{-- RIGHT: RECENT ENROLLMENT ACTIVITY --}}
        <x-ui.card padding="md" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="space-y-0.5">
                    <h3 class="font-heading font-bold text-base text-slate-900">
                        Recent Student Enrollments
                    </h3>
                    <p class="text-xs text-slate-500">
                        Learners recently enrolled in your assigned courses
                    </p>
                </div>
                <x-ui.icon name="user-group" size="xs" class="text-slate-400" />
            </div>

            @if ($recentEnrollments->isEmpty())
                <x-ui.empty-state
                    title="No Enrollments Yet"
                    description="When students are enrolled in your assigned courses, their enrollment details will be listed here."
                    icon="user-group"
                    :bordered="false"
                />
            @else
                <div class="space-y-3">
                    @foreach ($recentEnrollments as $en)
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200 gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs sm:text-sm text-slate-900 truncate">
                                        {{ $en->user?->name }}
                                    </span>
                                    <x-ui.badge variant="success" size="xs">Approved</x-ui.badge>
                                </div>
                                <span class="text-xs text-slate-500 block truncate mt-0.5">
                                    {{ $en->course?->name }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400 block mt-0.5">
                                    Enrolled {{ $en->requested_at?->diffForHumans() }}
                                </span>
                            </div>

                            @if ($en->course)
                                <x-ui.button
                                    href="{{ route('instructor.courses.students', $en->course) }}"
                                    variant="ghost"
                                    size="xs"
                                >
                                    Roster
                                </x-ui.button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</div>
@endsection

