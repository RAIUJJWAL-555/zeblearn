@extends('layouts.app')

@section('title', 'Learning Profile & Overview | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & TOP BAR --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.profile.show') }}" class="hover:text-[#FB6221] transition-colors">My Profile</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Learning Overview</span>
            </nav>
            <div class="flex items-center gap-3">
                <x-ui.button
                    href="{{ route('student.profile.show') }}"
                    variant="secondary"
                    size="sm"
                    icon="user"
                >
                    Personal Profile
                </x-ui.button>
                <x-ui.button
                    href="{{ route('student.dashboard') }}"
                    variant="primary"
                    size="sm"
                    icon="academic-cap"
                >
                    Go to Dashboard
                </x-ui.button>
            </div>
        </div>

        {{-- HEADER BANNER --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                            Academic Transcript &amp; Milestones
                        </span>
                        <x-ui.badge variant="success" size="xs">
                            {{ $completedCoursesCount }} Completed / {{ $approvedCourses->count() }} Enrolled
                        </x-ui.badge>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight mt-1">
                        {{ $user->name }}'s Learning Profile
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-sans mt-0.5">
                        Comprehensive record of verified course progress, assessment submissions, and earned credentials.
                    </p>
                </div>

                {{-- OVERALL PROGRESS WHEEL / STAT PILL --}}
                <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 sm:p-5 flex items-center gap-4 shrink-0">
                    <div class="w-14 h-14 rounded-full bg-orange-100 text-[#FB6221] flex items-center justify-center font-heading font-black text-xl">
                        {{ $overallPercentage }}%
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-slate-800 font-heading">Overall Curriculum Progress</span>
                        <span class="block text-[11px] font-mono text-slate-500 mt-0.5">
                            {{ $totalCompletedLessons }} of {{ $totalCourseLessons }} Lessons Completed
                        </span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- KEY METRICS 4-COL GRID --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Enrolled Courses</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">
                    {{ $approvedCourses->count() }}
                </span>
                <span class="text-[11px] font-mono text-emerald-600 mt-1 block">
                    {{ $completedCoursesCount }} Completed • {{ $activeCoursesCount }} In Progress
                </span>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Completed Lessons</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">
                    {{ $totalCompletedLessons }}
                </span>
                <span class="text-[11px] font-mono text-slate-500 mt-1 block">
                    Across all enrolled modules
                </span>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Coursework Graded</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">
                    {{ $completedAssignmentsCount }}
                </span>
                <span class="text-[11px] font-mono text-amber-600 mt-1 block">
                    {{ $pendingAssignmentsCount }} Awaiting Review
                </span>
            </div>

            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Certificates Earned</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">
                    {{ $certificates->whereNull('revoked_at')->count() }}
                </span>
                <span class="text-[11px] font-mono text-indigo-600 mt-1 block">
                    100% Verifiable Credentials
                </span>
            </div>
        </div>

        {{-- SECTION 1: ENROLLED COURSES PROGRESS --}}
        <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 font-heading uppercase tracking-wider">
                        Enrolled Courses &amp; Progress
                    </h2>
                    <p class="text-[11px] text-slate-500">Live progress breakdown for all approved course enrollments.</p>
                </div>
                <x-ui.button
                    href="{{ route('student.dashboard') }}"
                    variant="ghost"
                    size="xs"
                >
                    Learning Dashboard &rarr;
                </x-ui.button>
            </div>

            @if ($approvedCourses->isEmpty())
                <div class="px-6 py-12 text-center text-slate-400">
                    <p class="text-sm font-medium text-slate-600">No active course enrollments</p>
                    <p class="text-xs text-slate-400 mt-1">Browse our catalog and request enrollment to start learning.</p>
                    <x-ui.button href="{{ route('courses.index') }}" variant="primary" size="sm" class="mt-4">
                        Browse Courses
                    </x-ui.button>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($approvedCourses as $course)
                        @php
                            $progress = $courseProgressMap[$course->id] ?? [
                                'total' => 0,
                                'completed' => 0,
                                'percentage' => 0,
                                'is_completed' => false,
                            ];
                        @endphp
                        <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:bg-slate-50/50 transition-colors">
                            <div class="space-y-2 flex-1 max-w-xl">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 font-heading">
                                        {{ $course->name }}
                                    </h3>
                                    @if ($progress['is_completed'])
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-100 text-[#FB6221]">
                                            In Progress
                                        </span>
                                    @endif
                                </div>

                                {{-- PROGRESS BAR --}}
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between text-xs font-mono text-slate-500">
                                        <span>Progress: {{ $progress['completed'] }} / {{ $progress['total'] }} lessons</span>
                                        <span class="font-bold text-slate-900">{{ $progress['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                                        <div
                                            class="h-2.5 rounded-full {{ $progress['is_completed'] ? 'bg-emerald-500' : 'bg-[#FB6221]' }} transition-all duration-500"
                                            style="width: {{ $progress['percentage'] }}%"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0">
                                <x-ui.button
                                    href="{{ route('student.courses.show', $course->slug) }}"
                                    variant="secondary"
                                    size="sm"
                                    icon="book"
                                >
                                    Course Modules
                                </x-ui.button>
                                <x-ui.button
                                    href="{{ route('student.courses.resume', $course->slug) }}"
                                    variant="primary"
                                    size="sm"
                                    icon="play"
                                >
                                    {{ $progress['completed'] > 0 ? 'Continue' : 'Start' }}
                                </x-ui.button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        {{-- SECTION 2: ACADEMIC ASSESSMENTS & CERTIFICATES (2-COL) --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- ASSIGNMENTS SUMMARY --}}
            <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                        Assignment Submissions ({{ $submissions->count() }})
                    </h3>
                </div>

                @if ($submissions->isEmpty())
                    <div class="px-6 py-10 text-center text-slate-400">
                        <p class="text-xs text-slate-500">No assignment submissions recorded yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                        @foreach ($submissions->take(5) as $submission)
                            <div class="p-4 flex items-center justify-between gap-4 text-xs">
                                <div class="space-y-0.5 min-w-0 flex-1">
                                    <p class="font-bold text-slate-900 truncate">
                                        {{ $submission->assignment?->title ?? 'Assignment' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $submission->assignment?->course?->name ?? 'Course' }} • Attempt #{{ $submission->attempt_number }}
                                    </p>
                                    <span class="text-[10px] font-mono text-slate-400">
                                        Submitted {{ $submission->submitted_at?->format('d M Y') }}
                                    </span>
                                </div>

                                <div class="text-right shrink-0">
                                    @if ($submission->isGraded())
                                        <span class="font-heading font-black text-sm text-emerald-600 block">
                                            {{ $submission->marks_awarded }}/{{ $submission->assignment?->max_marks }}
                                        </span>
                                        <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded">
                                            Graded ({{ $submission->percentage }}%)
                                        </span>
                                    @elseif ($submission->status === \App\Models\AssignmentSubmission::STATUS_RESUBMISSION_REQUIRED)
                                        <span class="text-[10px] font-mono font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">
                                            Resubmission Required
                                        </span>
                                    @else
                                        <span class="text-[10px] font-mono font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded">
                                            Under Review
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- CERTIFICATES ACHIEVEMENTS --}}
            <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                        Earned Certificates ({{ $certificates->whereNull('revoked_at')->count() }})
                    </h3>
                    <a href="{{ route('student.certificates.index') }}" class="text-[11px] font-semibold text-[#FB6221] hover:underline">
                        View Registry &rarr;
                    </a>
                </div>

                @if ($certificates->isEmpty())
                    <div class="px-6 py-10 text-center text-slate-400">
                        <p class="text-xs text-slate-500">No certificates earned yet.</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Complete all required curriculum lessons in an enrolled course to unlock your certificate.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                        @foreach ($certificates as $cert)
                            <div class="p-4 flex items-center justify-between gap-4 text-xs">
                                <div class="space-y-0.5 min-w-0 flex-1">
                                    <p class="font-bold text-slate-900 truncate">
                                        {{ $cert->course?->name ?? 'Course' }}
                                    </p>
                                    <p class="text-[11px] font-mono text-[#FB6221]">
                                        {{ $cert->certificate_number }}
                                    </p>
                                    <span class="text-[10px] font-mono text-slate-400">
                                        Issued {{ $cert->issued_at?->format('d M Y') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <x-ui.button
                                        href="{{ route('student.certificates.show', $cert->id) }}"
                                        variant="secondary"
                                        size="xs"
                                        icon="eye"
                                    >
                                        View
                                    </x-ui.button>
                                    <x-ui.button
                                        href="{{ route('student.certificates.download', $cert->id) }}"
                                        variant="primary"
                                        size="xs"
                                        icon="arrow-down"
                                    >
                                        PDF
                                    </x-ui.button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>
        </div>

    </div>
</div>
@endsection

