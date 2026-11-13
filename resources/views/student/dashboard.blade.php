@extends('layouts.app')

@section('title', 'Student Learning Dashboard | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- STUDENT PROFILE GREETING BAR --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-2xl shadow-sm shrink-0 border border-slate-700">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                                Student Console
                            </span>
                            <x-ui.badge variant="success" size="xs" dot>
                                {{ $user->status }}
                            </x-ui.badge>
                            <span class="text-[11px] font-mono text-slate-400">
                                {{ now()->format('l, d F Y') }}
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight mt-1">
                            Welcome, {{ $user->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-sans mt-0.5">
                            Track your course progress, continue lessons, and review your assessments.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 flex-wrap">
                    <x-ui.button
                        href="{{ route('student.payments.history') }}"
                        variant="secondary"
                        size="sm"
                        icon="credit-card"
                    >
                        My Payments
                    </x-ui.button>

                    <x-ui.button
                        href="{{ route('courses.index') }}"
                        variant="secondary"
                        size="sm"
                        icon="book"
                    >
                        Browse Courses
                    </x-ui.button>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <x-ui.button
                            type="submit"
                            variant="ghost"
                            size="sm"
                            icon="arrow-right"
                            class="text-rose-600 hover:bg-rose-50 hover:text-rose-700 border border-rose-200"
                        >
                            Sign Out
                        </x-ui.button>
                    </form>
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

        @if (session('error'))
            <x-ui.alert variant="error" dismissible>
                {{ session('error') }}
            </x-ui.alert>
        @endif

        {{-- LEARNING KPI METRICS GRID --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <x-ui.card padding="sm" class="space-y-1">
                <div class="flex items-center justify-between">
                    <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Enrolled Courses</span>
                    <x-ui.icon name="academic-cap" size="xs" class="text-slate-400" />
                </div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading block">{{ $totalEnrolled }}</span>
                <span class="text-[11px] text-slate-500 font-sans block">Approved academy seats</span>
            </x-ui.card>

            <x-ui.card padding="sm" class="space-y-1">
                <div class="flex items-center justify-between">
                    <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Completed Lessons</span>
                    <x-ui.icon name="check-circle" size="xs" class="text-emerald-500" />
                </div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading block">{{ $totalCompletedLessons }}</span>
                <span class="text-[11px] text-slate-500 font-sans block">Verified completions</span>
            </x-ui.card>

            <x-ui.card padding="sm" class="space-y-1">
                <div class="flex items-center justify-between">
                    <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">In Progress</span>
                    <x-ui.icon name="clock" size="xs" class="text-amber-500" />
                </div>
                <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading block">{{ $inProgressCoursesCount }}</span>
                <span class="text-[11px] text-slate-500 font-sans block">Active coursework</span>
            </x-ui.card>

            <x-ui.card padding="sm" class="space-y-1">
                <div class="flex items-center justify-between">
                    <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Finished</span>
                    <x-ui.icon name="shield-check" size="xs" class="text-emerald-600" />
                </div>
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading block">{{ $completedCoursesCount }}</span>
                <span class="text-[11px] text-slate-500 font-sans block">Fully completed courses</span>
            </x-ui.card>

            <x-ui.card padding="sm" class="col-span-2 sm:col-span-1 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Quiz Attempts</span>
                    <x-ui.icon name="academic-cap" size="xs" class="text-[#FB6221]" />
                </div>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading block">{{ $totalQuizAttempts }}</span>
                <span class="text-[11px] text-emerald-600 font-semibold font-sans block">
                    {{ $passedQuizAttempts }} Passed
                </span>
            </x-ui.card>
        </div>

        {{-- CONTINUE LEARNING HERO SECTION --}}
        @if ($resumeCourse && $resumeProgress)
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-black p-6 sm:p-8 text-white border border-slate-800 shadow-md">
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-3 max-w-2xl">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-[#FB6221]/20 text-[#FB6221] border border-[#FB6221]/30">
                                UP NEXT / RESUME LEARNING
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-white/10 text-slate-200 border border-white/10">
                                {{ $resumeCourse->category_badge }}
                            </span>
                        </div>

                        <div>
                            <h2 class="text-xl sm:text-2xl font-black font-heading tracking-tight text-white">
                                {{ $resumeCourse->name }}
                            </h2>
                            @if ($resumeLesson)
                                <p class="text-xs sm:text-sm text-slate-300 font-sans mt-1 flex items-center gap-1.5">
                                    <x-ui.icon name="book" size="xs" class="text-[#FB6221]" />
                                    <span>Current: <strong>{{ $resumeLesson->title }}</strong></span>
                                    @if ($resumeLesson->module)
                                        <span class="text-slate-500">• {{ $resumeLesson->module->title }}</span>
                                    @endif
                                </p>
                            @endif
                        </div>

                        {{-- Progress bar --}}
                        <div class="space-y-1.5 pt-1">
                            <div class="flex items-center justify-between text-xs font-mono">
                                <span class="font-bold text-slate-200">
                                    {{ $resumeProgress['percentage'] }}% Complete
                                </span>
                                <span class="text-slate-400">
                                    {{ $resumeProgress['completed'] }}/{{ $resumeProgress['total'] }} Lessons
                                </span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden">
                                <div
                                    class="bg-gradient-to-r from-[#FB6221] to-emerald-400 h-2.5 rounded-full transition-all duration-500"
                                    style="width: {{ $resumeProgress['percentage'] }}%"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row md:flex-col gap-2.5 shrink-0">
                        @if ($resumeProgress['total'] > 0)
                            <x-ui.button
                                href="{{ route('student.courses.resume', $resumeCourse) }}"
                                variant="primary"
                                size="md"
                                icon="arrow-right"
                            >
                                <span>{{ $resumeProgress['percentage'] > 0 ? 'Continue Learning' : 'Start Learning' }}</span>
                            </x-ui.button>
                        @endif

                        <x-ui.button
                            href="{{ route('student.courses.show', $resumeCourse) }}"
                            variant="secondary"
                            size="md"
                            class="bg-slate-800/80 text-white border-slate-700 hover:bg-slate-700"
                        >
                            View Syllabus
                        </x-ui.button>
                    </div>
                </div>

                {{-- Subtle ambient glow --}}
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#FB6221]/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
            </div>
        @endif

        {{-- SECTION 1: APPROVED ENROLLED COURSES --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-slate-900">
                        My Enrolled Courses ({{ $approvedEnrollments->count() }})
                    </h2>
                </div>
                <span class="font-mono text-xs text-slate-500 font-medium hidden sm:inline">
                    Active Learning Access
                </span>
            </div>

            @if ($approvedEnrollments->isEmpty())
                <x-ui.empty-state
                    title="No Active Course Access Yet"
                    description="You do not currently have any approved active courses. Explore our multi-module academy catalog and request enrollment."
                    icon="book"
                >
                    <x-ui.button
                        href="{{ route('courses.index') }}"
                        variant="primary"
                        size="sm"
                        icon="arrow-right"
                    >
                        Explore Course Catalog
                    </x-ui.button>
                </x-ui.empty-state>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($approvedEnrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $progress = $courseProgressMap[$course->id] ?? [
                                'completed' => 0,
                                'total' => 0,
                                'percentage' => 0,
                                'is_completed' => false,
                            ];
                        @endphp
                        <x-ui.card padding="md" class="border-slate-200/90 hover:border-emerald-500/60 transition-all flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200/60">
                                        {{ $course->category_badge }}
                                    </span>
                                    <x-ui.badge variant="success" size="xs" dot>
                                        Approved
                                    </x-ui.badge>
                                </div>

                                <h3 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-slate-900 leading-snug">
                                    {{ $course->name }}
                                </h3>
                                <p class="text-xs text-slate-600 line-clamp-2 mt-2 leading-relaxed font-sans">
                                    {{ $course->short_description }}
                                </p>

                                {{-- PROGRESS BAR & STATS --}}
                                <div class="mt-4 pt-4 border-t border-slate-100 space-y-2">
                                    <div class="flex items-center justify-between text-xs font-mono">
                                        <span class="font-bold text-slate-700">
                                            {{ $progress['percentage'] }}% Complete
                                        </span>
                                        <span class="text-slate-400">
                                            @if ($progress['total'] > 0)
                                                {{ $progress['completed'] }}/{{ $progress['total'] }} Lessons
                                            @else
                                                Curriculum in prep
                                            @endif
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div
                                            class="bg-gradient-to-r from-[#FB6221] to-emerald-500 h-2 rounded-full transition-all duration-500"
                                            style="width: {{ $progress['percentage'] }}%"
                                        ></div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 mt-3 text-xs text-slate-500 font-mono">
                                    <span>{{ $course->modules->count() }} Modules</span>
                                    <span>•</span>
                                    <span>{{ $course->display_duration }}</span>
                                    <span>•</span>
                                    <span class="text-emerald-700 font-semibold">Active Enrollment</span>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-col sm:flex-row gap-2.5">
                                @if ($progress['total'] > 0)
                                    <x-ui.button
                                        href="{{ route('student.courses.resume', $course) }}"
                                        variant="dark"
                                        size="sm"
                                        icon="arrow-right"
                                        class="flex-1 hover:!bg-[#FB6221]"
                                    >
                                        <span>{{ $progress['percentage'] > 0 ? 'Continue Learning' : 'Start Learning' }}</span>
                                    </x-ui.button>
                                @endif

                                <x-ui.button
                                    href="{{ route('student.courses.show', $course) }}"
                                    variant="secondary"
                                    size="sm"
                                    class="{{ $progress['total'] === 0 ? 'w-full' : '' }}"
                                >
                                    View Syllabus
                                </x-ui.button>
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- SECTION 2: PENDING ADMISSIONS APPLICATIONS --}}
        @if ($pendingEnrollments->isNotEmpty())
            <div class="space-y-4 pt-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-slate-900">
                            Pending Admissions Applications ({{ $pendingEnrollments->count() }})
                        </h2>
                    </div>
                    <span class="font-mono text-xs text-amber-600 font-semibold hidden sm:inline">
                        Awaiting Admissions Approval
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach ($pendingEnrollments as $enrollment)
                        @php $course = $enrollment->course; @endphp
                        <div class="bg-amber-50/70 rounded-2xl p-5 border border-amber-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-900 text-sm font-heading">{{ $course->name }}</span>
                                    <x-ui.badge variant="warning" size="xs" dot dotPulse>
                                        Under Review
                                    </x-ui.badge>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 font-sans">
                                    Requested on {{ $enrollment->requested_at?->format('d M Y, h:i A') }} • Track: {{ $course->category_label }}
                                </p>
                            </div>

                            <div class="text-xs font-mono text-slate-500 sm:text-right shrink-0">
                                <span>Admissions status: </span>
                                <strong class="text-amber-800">Pending Review</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SECTION 3: DECLINED / CANCELLED APPLICATIONS --}}
        @if ($declinedEnrollments->isNotEmpty())
            <div class="space-y-4 pt-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <h2 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-slate-900">
                            Application History &amp; Declined Requests
                        </h2>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($declinedEnrollments as $enrollment)
                        @php $course = $enrollment->course; @endphp
                        <x-ui.card padding="sm" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm font-heading">{{ $course->name }}</span>
                                    <x-ui.badge variant="danger" size="xs">
                                        {{ ucfirst($enrollment->status) }}
                                    </x-ui.badge>
                                </div>
                                @if ($enrollment->admin_note)
                                    <p class="text-xs text-slate-500 mt-1 font-sans">
                                        <strong>Admissions Note:</strong> {{ $enrollment->admin_note }}
                                    </p>
                                @endif
                            </div>

                            @if ($course->isAvailableForEnrollment())
                                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                    @csrf
                                    <x-ui.button
                                        type="submit"
                                        variant="secondary"
                                        size="xs"
                                    >
                                        Re-apply for Enrollment
                                    </x-ui.button>
                                </form>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SECTION 4: 2-COLUMN SPLIT: RECENT ACTIVITY & QUIZ ASSESSMENTS --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-4">

            {{-- COLUMN 1: RECENT LEARNING ACTIVITY --}}
            <x-ui.card padding="md" class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="space-y-0.5">
                        <h3 class="font-heading font-bold text-base text-slate-900">
                            Recent Learning Activity
                        </h3>
                        <p class="text-xs text-slate-500 font-sans">
                            Your latest achievements and course events
                        </p>
                    </div>
                    <x-ui.icon name="clock" size="xs" class="text-slate-400" />
                </div>

                @if ($recentActivities->isEmpty())
                    <x-ui.empty-state
                        title="No Recent Activity"
                        description="Complete lessons and submit quizzes to build your learning timeline."
                        icon="clock"
                        :bordered="false"
                    />
                @else
                    <div class="space-y-3">
                        @foreach ($recentActivities as $activity)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200/60 hover:bg-white hover:border-slate-300 transition-all">
                                <div class="mt-0.5 w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center shrink-0">
                                    <x-ui.icon :name="$activity['icon']" size="xs" class="{{ $activity['badge_variant'] === 'success' ? 'text-emerald-600' : 'text-[#FB6221]' }}" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-xs font-bold text-slate-900 truncate">
                                            {{ $activity['title'] }}
                                        </p>
                                        <span class="text-[10px] font-mono text-slate-400 shrink-0">
                                            {{ $activity['timestamp']->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                        {{ $activity['subtitle'] }}
                                    </p>
                                    @if ($activity['url'])
                                        <a href="{{ $activity['url'] }}" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#FB6221] hover:underline mt-1">
                                            <span>Open Details</span>
                                            <x-ui.icon name="arrow-right" size="xs" />
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- COLUMN 2: QUIZ ASSESSMENTS & KNOWLEDGE CHECKS --}}
            <x-ui.card padding="md" class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="space-y-0.5">
                        <h3 class="font-heading font-bold text-base text-slate-900">
                            Quiz Assessments
                        </h3>
                        <p class="text-xs text-slate-500 font-sans">
                            Knowledge checks for your enrolled courses
                        </p>
                    </div>
                    <x-ui.icon name="academic-cap" size="xs" class="text-slate-400" />
                </div>

                @if ($quizAttempts->isEmpty() && $availableQuizzes->isEmpty())
                    <x-ui.empty-state
                        title="No Quizzes Available"
                        description="Assessment tests attached to your enrolled courses will appear here."
                        icon="academic-cap"
                        :bordered="false"
                    />
                @else
                    <div class="space-y-3">
                        {{-- Recent attempts --}}
                        @if ($quizAttempts->isNotEmpty())
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                Recent Quiz Scores
                            </span>
                            @foreach ($quizAttempts as $attempt)
                                @if ($attempt->quiz && $attempt->quiz->course)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                        <div class="min-w-0 pr-2">
                                            <span class="text-xs font-bold text-slate-900 block truncate font-heading">
                                                {{ $attempt->quiz->title }}
                                            </span>
                                            <span class="text-[11px] text-slate-500 block truncate">
                                                {{ $attempt->quiz->course->name }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2 shrink-0">
                                            @if ($attempt->is_passed)
                                                <x-ui.badge variant="success" size="xs">
                                                    {{ $attempt->percentage }}% Passed
                                                </x-ui.badge>
                                            @elseif ($attempt->status === 'submitted')
                                                <x-ui.badge variant="warning" size="xs">
                                                    {{ $attempt->percentage }}% Completed
                                                </x-ui.badge>
                                            @else
                                                <x-ui.badge variant="neutral" size="xs">
                                                    {{ ucfirst($attempt->status) }}
                                                </x-ui.badge>
                                            @endif

                                            <x-ui.button
                                                href="{{ route('student.quizzes.result', [$attempt->quiz->course, $attempt->quiz, $attempt]) }}"
                                                variant="secondary"
                                                size="xs"
                                            >
                                                Result
                                            </x-ui.button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- Available quizzes --}}
                        @if ($availableQuizzes->isNotEmpty())
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block pt-2 mb-1">
                                Available Course Quizzes
                            </span>
                            @foreach ($availableQuizzes as $quiz)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200">
                                    <div class="min-w-0 pr-2">
                                        <span class="text-xs font-bold text-slate-900 block truncate font-heading">
                                            {{ $quiz->title }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 block truncate">
                                            {{ $quiz->course->name }} • {{ $quiz->time_limit_minutes }}m limit
                                        </span>
                                    </div>

                                    <x-ui.button
                                        href="{{ route('student.quizzes.show', [$quiz->course, $quiz]) }}"
                                        variant="dark"
                                        size="xs"
                                        class="hover:!bg-[#FB6221]"
                                    >
                                        View Quiz
                                    </x-ui.button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- SECTION 5: COURSEWORK & ASSIGNMENT SUBMISSIONS --}}
        <div class="space-y-4 pt-4">
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                    <h2 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-slate-900">
                        Assignments &amp; Projects
                    </h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- UPCOMING ASSIGNMENTS --}}
                <x-ui.card padding="md" class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="space-y-0.5">
                            <h3 class="font-heading font-bold text-base text-slate-900">
                                Upcoming Assignments
                            </h3>
                            <p class="text-xs text-slate-500 font-sans">
                                Tasks and deliverables assigned in your enrolled courses
                            </p>
                        </div>
                        <x-ui.icon name="document-text" size="xs" class="text-slate-400" />
                    </div>

                    @if ($upcomingAssignments->isEmpty())
                        <x-ui.empty-state
                            title="No Upcoming Assignments"
                            description="New course assignments will appear here once published by your instructors."
                            icon="document-text"
                            :bordered="false"
                        />
                    @else
                        <div class="space-y-3">
                            @foreach ($upcomingAssignments as $asgn)
                                <div class="flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-all">
                                    <div class="min-w-0 pr-2">
                                        <span class="text-xs font-bold text-slate-900 block truncate font-heading">
                                            {{ $asgn->title }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 block truncate font-sans">
                                            {{ $asgn->course?->name }} • {{ $asgn->max_marks }} pts
                                        </span>
                                        @if ($asgn->due_at)
                                            <span class="text-[10px] font-mono {{ $asgn->isPastDue() ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                                Due: {{ $asgn->due_at->format('d M, h:i A') }}
                                            </span>
                                        @endif
                                    </div>

                                    <x-ui.button
                                        href="{{ route('student.assignments.show', [$asgn->course, $asgn]) }}"
                                        variant="secondary"
                                        size="xs"
                                        icon="arrow-right"
                                    >
                                        Open
                                    </x-ui.button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>

                {{-- SUBMISSIONS & GRADING FEEDBACK --}}
                <x-ui.card padding="md" class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="space-y-0.5">
                            <h3 class="font-heading font-bold text-base text-slate-900">
                                Submissions &amp; Evaluations
                            </h3>
                            <p class="text-xs text-slate-500 font-sans">
                                Track grading progress and review feedback
                            </p>
                        </div>
                        <x-ui.icon name="check-circle" size="xs" class="text-slate-400" />
                    </div>

                    @if ($recentlyGradedAssignments->isEmpty() && $pendingSubmissions->isEmpty())
                        <x-ui.empty-state
                            title="No Submissions Recorded"
                            description="Turn in your assignments to track evaluations and instructor remarks here."
                            icon="check-circle"
                            :bordered="false"
                        />
                    @else
                        <div class="space-y-3">
                            {{-- Recently graded --}}
                            @foreach ($recentlyGradedAssignments as $sub)
                                @if ($sub->assignment && $sub->assignment->course)
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-200/70">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-slate-900 block truncate font-heading">
                                                    {{ $sub->assignment->title }}
                                                </span>
                                                <x-ui.badge variant="success" size="xs">
                                                    {{ $sub->marks_awarded }}/{{ $sub->assignment->max_marks }} pts
                                                </x-ui.badge>
                                            </div>
                                            <span class="text-[11px] text-slate-500 block truncate font-sans">
                                                {{ $sub->assignment->course->name }} • Graded {{ $sub->graded_at?->diffForHumans() }}
                                            </span>
                                        </div>

                                        <x-ui.button
                                            href="{{ route('student.assignments.submissions.show', [$sub->assignment->course, $sub->assignment, $sub]) }}"
                                            variant="secondary"
                                            size="xs"
                                        >
                                            Feedback
                                        </x-ui.button>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Pending grading --}}
                            @foreach ($pendingSubmissions as $sub)
                                @if ($sub->assignment && $sub->assignment->course)
                                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center gap-1.5">
                                                <span class="text-xs font-bold text-slate-900 block truncate font-heading">
                                                    {{ $sub->assignment->title }}
                                                </span>
                                                <x-ui.badge variant="info" size="xs">Under Review</x-ui.badge>
                                            </div>
                                            <span class="text-[11px] text-slate-500 block truncate font-sans">
                                                {{ $sub->assignment->course->name }} • Attempt #{{ $sub->attempt_number }}
                                            </span>
                                        </div>

                                        <x-ui.button
                                            href="{{ route('student.assignments.show', [$sub->assignment->course, $sub->assignment]) }}"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            Details
                                        </x-ui.button>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
            </div>
        </div>

    </div>
</div>
@endsection
