@extends('layouts.instructor')

@section('title', 'Faculty Profile | ZebLearn LMS')

@section('content')
<div class="space-y-8">
    {{-- BREADCRUMBS & TOP BAR --}}
    <div class="flex items-center justify-between">
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
            <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Instructor</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Faculty Profile</span>
        </nav>
        <x-ui.button
            href="{{ route('instructor.profile.edit') }}"
            variant="primary"
            size="sm"
            icon="pencil"
        >
            Edit Profile
        </x-ui.button>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    {{-- PROFILE HEADER CARD --}}
    <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                @if ($instructor->avatarUrl())
                    <img
                        src="{{ $instructor->avatarUrl() }}"
                        alt="{{ $instructor->name }}"
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-slate-200 shadow-sm shrink-0"
                    />
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-3xl sm:text-4xl shadow-sm shrink-0 border border-slate-700">
                        {{ strtoupper(substr($instructor->name, 0, 2)) }}
                    </div>
                @endif

                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                            Faculty Member
                        </span>
                        <x-ui.badge variant="success" size="xs" dot>
                            {{ ucfirst($instructor->status) }}
                        </x-ui.badge>
                        <span class="text-[11px] font-mono text-slate-400">
                            Faculty ID: #FAC-{{ str_pad($instructor->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        {{ $instructor->name }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium font-sans">
                        {{ $instructor->designation ?: 'Senior Instructor' }} • {{ $instructor->specialization ?: 'ERP Specialist' }}
                    </p>
                </div>
            </div>

            {{-- QUICK STATS PILLS --}}
            <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-center min-w-[100px]">
                    <span class="block text-lg sm:text-xl font-black text-slate-900 font-heading">{{ $totalCourses }}</span>
                    <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Assigned Courses</span>
                </div>
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-center min-w-[100px]">
                    <span class="block text-lg sm:text-xl font-black text-slate-900 font-heading">{{ $gradedCount }}</span>
                    <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Graded Attempts</span>
                </div>
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-center min-w-[100px]">
                    <span class="block text-lg sm:text-xl font-black text-slate-900 font-heading">{{ $instructor->experience_years ? $instructor->experience_years.' yrs' : 'N/A' }}</span>
                    <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Experience</span>
                </div>
            </div>
        </div>
    </x-ui.card>

    {{-- CREDENTIALS & ASSIGNED COURSES 2-COL GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- LEFT: FACULTY INFORMATION & BIO (2 COLS) --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 font-heading">
                        Academic Credentials &amp; Faculty Information
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Professional background and curriculum qualifications.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-slate-100">
                    <div>
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Academic Designation</span>
                        <span class="block text-sm font-semibold text-slate-900 mt-1">
                            {{ $instructor->designation ?: 'Lead Instructor' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Area of Specialization</span>
                        <span class="block text-sm font-semibold text-slate-900 mt-1">
                            {{ $instructor->specialization ?: 'Enterprise Business Systems' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Highest Academic Qualification</span>
                        <span class="block text-sm font-semibold text-slate-900 mt-1">
                            {{ $instructor->qualification ?: 'Not specified' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Years of Experience</span>
                        <span class="block text-sm font-semibold text-slate-900 mt-1">
                            {{ $instructor->experience_years ? $instructor->experience_years.' Years Industry Experience' : 'Not specified' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Email Address</span>
                        <span class="block text-sm font-semibold text-slate-900 mt-1">{{ $instructor->email }}</span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Phone Number</span>
                        <span class="block text-sm font-semibold text-slate-900 mt-1">{{ $instructor->phone ?: 'Not provided' }}</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 mb-1.5">Instructor Biography</span>
                    <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-200/70 text-xs sm:text-sm text-slate-700 leading-relaxed">
                        {{ $instructor->bio ?: 'No biography details provided yet. Add your teaching philosophy and industry background.' }}
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- RIGHT: ASSIGNED COURSES & SECURITY (1 COL) --}}
        <div class="space-y-6">
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                    Assigned Courses ({{ $totalCourses }})
                </h3>

                @if ($assignedCourses->isEmpty())
                    <p class="text-xs text-slate-400 py-3 text-center">No courses currently assigned by administration.</p>
                @else
                    <div class="space-y-2">
                        @foreach ($assignedCourses as $c)
                            <a
                                href="{{ route('instructor.courses.show', $c->id) }}"
                                class="block p-3 rounded-xl border border-slate-200/80 hover:bg-slate-50 transition-colors group"
                            >
                                <p class="text-xs font-bold text-slate-900 group-hover:text-[#FB6221] truncate">
                                    {{ $c->name }}
                                </p>
                                <div class="flex items-center justify-between text-[10px] font-mono text-slate-500 mt-1">
                                    <span>{{ $c->modules_count }} Modules</span>
                                    <span>{{ $c->enrollments_count }} Students</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                    Account Security
                </h3>

                <p class="text-xs text-slate-600">
                    Keep your instructor account password protected to maintain academic grading integrity.
                </p>

                <x-ui.button
                    href="{{ route('instructor.profile.edit') }}#password-section"
                    variant="secondary"
                    size="sm"
                    icon="lock-closed"
                    class="w-full justify-center"
                >
                    Change Password
                </x-ui.button>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection

