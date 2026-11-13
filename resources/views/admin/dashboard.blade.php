@extends('layouts.admin')

@section('title', 'Admin Overview & Analytics | ZebLearn LMS')
@section('page-title', 'Overview')

@section('content')
<div class="space-y-8">

    {{-- 1. WELCOME HERO BANNER --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A0A0A] via-[#141414] to-[#221207] text-white p-6 sm:p-8 xl:p-10 shadow-xl border border-white/10">
        {{-- Ambient brand radial glows --}}
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-xs font-mono text-[#FB6221]">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                    <span>ADMINISTRATION CONSOLE • REAL-TIME TELEMETRY</span>
                </div>

                <h1 class="text-2xl sm:text-3xl xl:text-4xl font-extrabold font-heading text-white tracking-tight">
                    Welcome back, {{ auth()->user()->name }} 👋
                </h1>

                <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                    Live operational metrics for student registrations, course catalog distribution, and enterprise curriculum tracks.
                </p>

                <div class="flex items-center gap-3 text-xs text-slate-400 font-mono pt-1">
                    <span class="inline-flex items-center gap-1.5 text-emerald-400 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        Overview Telemetry Active
                    </span>
                    <span>•</span>
                    <span>{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <x-ui.button
                    href="{{ route('admin.courses.create') }}"
                    variant="primary"
                    size="md"
                    icon="plus"
                >
                    Create Course
                </x-ui.button>

                <x-ui.button
                    href="{{ route('admin.enrollments.index') }}"
                    variant="dark"
                    size="md"
                    icon="user"
                    class="border border-white/15 hover:border-[#FB6221]"
                >
                    Enrollments
                </x-ui.button>

                <x-ui.button
                    href="{{ route('courses.index') }}"
                    variant="secondary"
                    size="md"
                    icon="external-link"
                    target="_blank"
                >
                    Browse Public Catalog
                </x-ui.button>
            </div>
        </div>
    </div>

    {{-- 2. DATABASE-DRIVEN KPI CARDS (6 METRICS) --}}
    <div>
        <div class="flex items-center justify-between mb-3.5">
            <h2 class="text-xs font-mono uppercase tracking-wider text-slate-500 font-semibold">
                Core Academy Metrics
            </h2>
            <span class="text-xs font-mono text-slate-400">Live Database Counters</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 sm:gap-5">
            {{-- Total Students --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-orange-200 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-400 mb-3">
                        <span class="text-xs font-medium text-slate-600 font-sans">Total Students</span>
                        <div class="w-9 h-9 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center border border-orange-100 group-hover:scale-105 transition-transform">
                            <x-ui.icon name="users" size="sm" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                        {{ number_format($kpis['total_students']) }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 font-mono">Role: Student</p>
            </div>

            {{-- Active Students --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-200 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-400 mb-3">
                        <span class="text-xs font-medium text-slate-600 font-sans">Active Students</span>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 group-hover:scale-105 transition-transform">
                            <x-ui.icon name="check-circle" size="sm" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-black text-emerald-600 tracking-tight">
                        {{ number_format($kpis['active_students']) }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 font-mono">Status: Active</p>
            </div>

            {{-- Total Instructors --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-indigo-200 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-400 mb-3">
                        <span class="text-xs font-medium text-slate-600 font-sans">Total Instructors</span>
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 group-hover:scale-105 transition-transform">
                            <x-ui.icon name="academic-cap" size="sm" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                        {{ number_format($kpis['total_instructors']) }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 font-mono">Role: Instructor</p>
            </div>

            {{-- Total Courses --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-200 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-400 mb-3">
                        <span class="text-xs font-medium text-slate-600 font-sans">Total Courses</span>
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 group-hover:scale-105 transition-transform">
                            <x-ui.icon name="book" size="sm" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                        {{ number_format($kpis['total_courses']) }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 font-mono">Catalog Modules</p>
            </div>

            {{-- Published Courses --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-teal-200 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-400 mb-3">
                        <span class="text-xs font-medium text-slate-600 font-sans">Published Courses</span>
                        <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 group-hover:scale-105 transition-transform">
                            <x-ui.icon name="eye" size="sm" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                        {{ number_format($kpis['published_courses']) }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 font-mono">Live On Public Site</p>
            </div>

            {{-- Total Categories --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-purple-200 transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-slate-400 mb-3">
                        <span class="text-xs font-medium text-slate-600 font-sans">Categories</span>
                        <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100 group-hover:scale-105 transition-transform">
                            <x-ui.icon name="folder" size="sm" />
                        </div>
                    </div>
                    <div class="text-2xl sm:text-3xl font-heading font-black text-slate-900 tracking-tight">
                        {{ number_format($kpis['total_categories']) }}
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 font-mono">Enterprise Tracks</p>
            </div>
        </div>
    </div>

    {{-- 3. COURSE STATUS OVERVIEW --}}
    <x-ui.card padding="none">
        <div class="p-6 sm:p-7 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading">Course Status Distribution</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time status breakdown across all database courses</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-xs font-medium">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Available: <strong>{{ $courseStatusBreakdown['available'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 border border-amber-200/80">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>Upcoming: <strong>{{ $courseStatusBreakdown['upcoming'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200/80">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>Roadmap: <strong>{{ $courseStatusBreakdown['planned'] }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Multi-Segment Status Progress Bar --}}
            @php
                $totalForStatus = max(array_sum($courseStatusBreakdown), 1);
                $pctAvailable = round(($courseStatusBreakdown['available'] / $totalForStatus) * 100);
                $pctUpcoming = round(($courseStatusBreakdown['upcoming'] / $totalForStatus) * 100);
                $pctPlanned = round(($courseStatusBreakdown['planned'] / $totalForStatus) * 100);
            @endphp
            <div class="w-full bg-slate-100 rounded-full h-3.5 overflow-hidden flex shadow-inner p-0.5">
                <div
                    class="bg-emerald-500 h-full rounded-l-full transition-all duration-300"
                    style="width: {{ $pctAvailable }}%"
                    title="Available: {{ $courseStatusBreakdown['available'] }} ({{ $pctAvailable }}%)"
                ></div>
                <div
                    class="bg-amber-500 h-full transition-all duration-300"
                    style="width: {{ $pctUpcoming }}%"
                    title="Upcoming: {{ $courseStatusBreakdown['upcoming'] }} ({{ $pctUpcoming }}%)"
                ></div>
                <div
                    class="bg-slate-400 h-full rounded-r-full transition-all duration-300"
                    style="width: {{ $pctPlanned }}%"
                    title="Roadmap Planned: {{ $courseStatusBreakdown['planned'] }} ({{ $pctPlanned }}%)"
                ></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                    <span class="text-xs text-slate-600">Available Courses</span>
                    <span class="text-xs font-mono font-bold text-emerald-700">{{ $pctAvailable }}%</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                    <span class="text-xs text-slate-600">Upcoming Batches</span>
                    <span class="text-xs font-mono font-bold text-amber-700">{{ $pctUpcoming }}%</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/70 flex items-center justify-between">
                    <span class="text-xs text-slate-600">Roadmap / Planned</span>
                    <span class="text-xs font-mono font-bold text-slate-600">{{ $pctPlanned }}%</span>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- 4. CHARTS SECTION (2 COLUMNS) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Chart 1: Student Registrations Trend (Past 6 Months) --}}
        <x-ui.card
            title="Student Registration Trend"
            subtitle="New student accounts created per month (past 6 months)"
            padding="md"
            class="flex flex-col justify-between"
        >
            <x-slot:actions>
                <x-ui.badge variant="neutral" size="xs">Monthly Velocity</x-ui.badge>
            </x-slot:actions>

            {{-- Responsive Visual Bar Chart --}}
            <div class="h-48 pt-6 flex items-end justify-between gap-3 border-b border-slate-100">
                @foreach ($registrationTrend as $point)
                    @php
                        $heightPercent = $maxMonthlyRegistration > 0 ? max(round(($point['count'] / $maxMonthlyRegistration) * 100), 6) : 6;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 group h-full justify-end">
                        {{-- Value Tooltip on Hover --}}
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity text-[10px] font-mono font-bold text-[#FB6221] bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200 mb-1">
                            {{ $point['count'] }}
                        </div>

                        {{-- The Bar --}}
                        <div
                            class="w-full max-w-[40px] rounded-t-lg bg-gradient-to-t from-orange-500 to-[#FB6221] transition-all duration-300 group-hover:from-orange-600 group-hover:to-[#e05419] shadow-xs"
                            style="height: {{ $heightPercent }}%;"
                        ></div>

                        {{-- Month Label --}}
                        <span class="text-[11px] font-mono text-slate-400 group-hover:text-slate-800 transition-colors">
                            {{ $point['short_month'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <x-slot:footer>
                <span class="text-xs text-slate-600">Peak Velocity: <strong>{{ $maxMonthlyRegistration }}</strong> registrations/mo</span>
                <span class="font-mono text-[11px] text-slate-400">Database Source: users table</span>
            </x-slot:footer>
        </x-ui.card>

        {{-- Chart 2: Course Distribution by Category --}}
        <x-ui.card
            title="Course Distribution by Category"
            subtitle="Top enterprise subject domains in curriculum"
            padding="md"
            class="flex flex-col justify-between"
        >
            <x-slot:actions>
                <x-ui.badge variant="neutral" size="xs">Top Domains</x-ui.badge>
            </x-slot:actions>

            {{-- Horizontal Progress Bars --}}
            <div class="space-y-4 pt-1">
                @forelse ($categoryDistribution as $cat)
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs font-medium">
                            <span class="text-slate-800 font-heading truncate max-w-[200px]">{{ $cat['name'] }}</span>
                            <span class="text-slate-500 font-mono">{{ $cat['count'] }} {{ Str::plural('course', $cat['count']) }} ({{ $cat['percentage'] }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5 shadow-inner">
                            <div class="bg-[#FB6221] h-full rounded-full transition-all duration-300" style="width: {{ $cat['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-xs text-slate-400">
                        No course records found in database.
                    </div>
                @endforelse
            </div>

            <x-slot:footer>
                <span class="text-xs text-slate-600">Total Catalog: <strong>{{ $kpis['total_courses'] }}</strong> courses</span>
                <span class="font-mono text-[11px] text-slate-400">Database Source: courses table</span>
            </x-slot:footer>
        </x-ui.card>

    </div>

    {{-- 5. RECENT DATA TABLES (2 COLUMNS) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Table 1: Recent Students --}}
        <x-ui.card
            title="Recent Student Registrations"
            subtitle="Latest 5 students enrolled on the platform"
            padding="none"
        >
            <x-slot:actions>
                <x-ui.badge variant="primary" size="xs">Latest 5</x-ui.badge>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] font-mono uppercase tracking-wider text-slate-400 bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5 sm:px-6">Student</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Role</th>
                            <th class="py-3 px-5 sm:px-6 text-right">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentStudents as $student)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-5 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs font-heading shrink-0 border border-slate-200">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 truncate max-w-[150px] font-heading">{{ $student->name }}</div>
                                            <div class="text-[11px] text-slate-400 truncate max-w-[150px]">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    @if ($student->isActive())
                                        <x-ui.badge variant="success" size="xs" dot>Active</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger" size="xs" dot>Suspended</x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-3 px-3 font-mono text-[11px] text-slate-500 uppercase">
                                    {{ $student->role }}
                                </td>
                                <td class="py-3 px-5 sm:px-6 text-right font-mono text-[11px] text-slate-400">
                                    {{ $student->created_at?->format('M d, Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 px-6 text-center text-slate-400">
                                    No students registered yet. New student registrations will appear here automatically.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        {{-- Table 2: Recent Courses --}}
        <x-ui.card
            title="Recent Courses"
            subtitle="Latest 5 modules added to the academy"
            padding="none"
        >
            <x-slot:actions>
                <x-ui.badge variant="info" size="xs">Latest 5</x-ui.badge>
            </x-slot:actions>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] font-mono uppercase tracking-wider text-slate-400 bg-slate-50/70 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-5 sm:px-6">Course Name</th>
                            <th class="py-3 px-3">Category</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-5 sm:px-6 text-right">Fee</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentCourses as $course)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-5 sm:px-6">
                                    <a
                                        href="{{ route('admin.courses.edit', $course) }}"
                                        class="font-semibold text-slate-900 hover:text-[#FB6221] transition-colors truncate max-w-[160px] block font-heading"
                                        title="{{ $course->name }}"
                                    >
                                        {{ $course->name }}
                                    </a>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        {{ $course->created_at?->format('M d, Y') ?? '—' }}
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <x-ui.badge variant="neutral" size="xs">
                                        {{ $course->category_label }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3 px-3">
                                    @if ($course->status === 'available')
                                        <x-ui.badge variant="success" size="xs" dot>Available</x-ui.badge>
                                    @elseif ($course->status === 'upcoming')
                                        <x-ui.badge variant="warning" size="xs" dot>Upcoming</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="xs">Roadmap</x-ui.badge>
                                    @endif
                                </td>
                                <td class="py-3 px-5 sm:px-6 text-right font-semibold text-slate-900 font-mono">
                                    {{ $course->display_price }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 px-6 text-center text-slate-400">
                                    No courses found in database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

    </div>

    {{-- 5.5 ASSIGNMENT EVALUATION TELEMETRY --}}
    <x-ui.card
        title="Coursework & Assignment Grading Activity"
        subtitle="Live telemetry for student assignment submissions, pending grading queues, and recently evaluated work"
        padding="md"
        class="border-slate-200/90 shadow-2xs"
    >
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Quick KPI Strip --}}
            <div class="space-y-3">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-400 block">Total Assignments</span>
                        <span class="text-2xl font-black text-slate-900 font-heading mt-0.5 block">{{ $assignmentMetrics['total_assignments'] }}</span>
                    </div>
                    <x-ui.button href="{{ route('admin.assignments.create') }}" variant="secondary" size="xs" icon="plus">
                        New
                    </x-ui.button>
                </div>

                <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-amber-700 block">Awaiting Evaluation</span>
                        <span class="text-2xl font-black text-amber-700 font-heading mt-0.5 block">{{ $assignmentMetrics['pending_grading'] }}</span>
                    </div>
                    @if ($assignmentMetrics['pending_grading'] > 0)
                        <x-ui.button href="{{ route('admin.assignments.index', ['status' => 'published']) }}" variant="secondary" size="xs">
                            Review Queue
                        </x-ui.button>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-emerald-50/60 border border-emerald-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-wider text-emerald-700 block">Graded Submissions</span>
                        <span class="text-2xl font-black text-emerald-700 font-heading mt-0.5 block">{{ $assignmentMetrics['graded_submissions'] }}</span>
                    </div>
                    <x-ui.badge variant="success" size="xs">Evaluated</x-ui.badge>
                </div>
            </div>

            {{-- Recently Graded Deliverables --}}
            <div class="lg:col-span-2 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-500">
                        Recently Evaluated Submissions
                    </span>
                    <a href="{{ route('admin.assignments.index') }}" class="text-xs text-[#FB6221] font-semibold hover:underline">
                        View All Assignments &rarr;
                    </a>
                </div>

                @if ($assignmentMetrics['recent_graded']->isEmpty())
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No graded assignment submissions recorded yet.
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($assignmentMetrics['recent_graded'] as $sub)
                            <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200/80 hover:border-slate-300 transition-all text-xs">
                                <div class="min-w-0 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 truncate">{{ $sub->user?->name }}</span>
                                        <x-ui.badge variant="success" size="xs">
                                            {{ $sub->marks_awarded }}/{{ $sub->assignment?->max_marks }} pts
                                        </x-ui.badge>
                                    </div>
                                    <span class="text-[11px] text-slate-500 block truncate">
                                        {{ $sub->assignment?->title }} • Graded by {{ $sub->grader?->name ?? 'Staff' }} {{ $sub->graded_at?->diffForHumans() }}
                                    </span>
                                </div>

                                <x-ui.button
                                    href="{{ route('admin.assignments.submissions.show', [$sub->assignment, $sub]) }}"
                                    variant="secondary"
                                    size="xs"
                                >
                                    Inspect
                                </x-ui.button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </x-ui.card>

    {{-- 6. QUICK ACTIONS & SHORTCUTS --}}
    <x-ui.card
        title="Administrative Navigation Shortcuts"
        subtitle="Direct workflow links for curriculum, enrollments, payments, and assessments"
        padding="md"
    >
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            {{-- Manage Courses --}}
            <a
                href="{{ route('admin.courses.index') }}"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="book" size="sm" />
                </div>
                <span class="text-xs font-semibold">Manage Courses</span>
                <span class="text-[10px] font-mono text-slate-400 mt-0.5">Catalog CRUD</span>
            </a>

            {{-- Manage Enrollments --}}
            <a
                href="{{ route('admin.enrollments.index') }}"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="user" size="sm" />
                </div>
                <span class="text-xs font-semibold">Enrollments</span>
                <span class="text-[10px] font-mono text-slate-400 mt-0.5">Review Queue</span>
            </a>

            {{-- Online Payments --}}
            <a
                href="{{ route('admin.payments.index') }}"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="currency-rupee" size="sm" />
                </div>
                <span class="text-xs font-semibold">Online Payments</span>
                <span class="text-[10px] font-mono text-slate-400 mt-0.5">Razorpay Gateway</span>
            </a>

            {{-- Quizzes & Assessments --}}
            <a
                href="{{ route('admin.quizzes.index') }}"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="academic-cap" size="sm" />
                </div>
                <span class="text-xs font-semibold">Assessments</span>
                <span class="text-[10px] font-mono text-slate-400 mt-0.5">MCQ Engine</span>
            </a>

            {{-- Assignments & Grading --}}
            <a
                href="{{ route('admin.assignments.index') }}"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="document-text" size="sm" />
                </div>
                <span class="text-xs font-semibold">Assignments</span>
                <span class="text-[10px] font-mono text-slate-400 mt-0.5">Grading Desk</span>
            </a>

            {{-- Public Website (Active) --}}
            <a
                href="{{ route('home') }}"
                target="_blank"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 group-hover:text-[#FB6221] flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="home" size="sm" />
                </div>
                <span class="text-xs font-semibold">Public Home</span>
                <span class="text-[10px] font-mono text-emerald-600 mt-0.5">Active Link ↗</span>
            </a>

            {{-- Demo Requests / Lead Desk (Active) --}}
            <a
                href="{{ route('book-demo') }}"
                target="_blank"
                class="flex flex-col items-center justify-center p-4 rounded-2xl border border-slate-200/90 bg-white hover:border-[#FB6221] hover:shadow-sm text-slate-700 hover:text-[#FB6221] transition-all text-center group"
            >
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center mb-2 group-hover:scale-105 transition-transform">
                    <x-ui.icon name="bolt" size="sm" />
                </div>
                <span class="text-xs font-semibold">Demo Leads</span>
                <span class="text-[10px] font-mono text-emerald-600 mt-0.5">Active Link ↗</span>
            </a>
        </div>
    </x-ui.card>

</div>
@endsection
