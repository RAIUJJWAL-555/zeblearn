@extends('layouts.admin')

@section('title', 'Instructor Profile: ' . $instructor->name . ' | ZebLearn LMS')
@section('page-title', 'Instructor Profile')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS & PROFILE HERO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.instructors.index') }}" class="hover:text-[#FB6221] transition-colors">Instructors</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">{{ $instructor->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $instructor->name }}
                </h1>
                @if ($instructor->isActive())
                    <x-ui.badge variant="success" size="sm" dot>Active Faculty</x-ui.badge>
                @else
                    <x-ui.badge variant="warning" size="sm" dot>Account Suspended</x-ui.badge>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                {{ $instructor->designation ?: 'Academy Faculty Member' }} • Member since {{ $instructor->created_at->format('M Y') }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <x-ui.button
                href="{{ route('admin.instructors.edit', $instructor) }}"
                variant="secondary"
                size="sm"
                icon="pencil"
            >
                Edit Profile
            </x-ui.button>

            <form method="POST" action="{{ route('admin.instructors.toggle-status', $instructor) }}" class="inline">
                @csrf
                @method('PATCH')
                <x-ui.button
                    type="submit"
                    variant="ghost"
                    size="sm"
                    onclick="return confirm('Change account status for {{ $instructor->name }}?')"
                    class="{{ $instructor->isActive() ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                >
                    {{ $instructor->isActive() ? 'Suspend Account' : 'Activate Account' }}
                </x-ui.button>
            </form>

            @if ($canDelete)
                <form method="POST" action="{{ route('admin.instructors.destroy', $instructor) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-ui.button
                        type="submit"
                        variant="ghost"
                        size="sm"
                        onclick="return confirm('Permanently remove instructor {{ $instructor->name }}? This action cannot be undone.')"
                        class="text-rose-600 hover:bg-rose-50"
                    >
                        Delete
                    </x-ui.button>
                </form>
            @endif
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

    {{-- TOP GRID: PROFILE CARD + ACADEMIC KPI METRICS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: INSTRUCTOR CARD & CREDENTIALS --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-6">
            <div class="flex items-center gap-4">
                @if ($instructor->avatarUrl())
                    <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructor->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-xs shrink-0">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-heading font-black text-xl shadow-xs shrink-0 border border-slate-700">
                        {{ strtoupper(substr($instructor->name, 0, 2)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h3 class="font-bold text-base text-slate-900 truncate font-heading">
                        {{ $instructor->name }}
                    </h3>
                    <p class="text-xs text-[#FB6221] font-medium truncate">
                        {{ $instructor->designation ?: 'Faculty Member' }}
                    </p>
                    <span class="text-[11px] font-mono text-slate-400 block truncate">
                        {{ $instructor->email }}
                    </span>
                </div>
            </div>

            <div class="space-y-3 pt-3 border-t border-slate-100 text-xs sm:text-sm">
                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Phone</span>
                    <span class="font-medium text-slate-800">{{ $instructor->phone ?: 'Not specified' }}</span>
                </div>

                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Specialization</span>
                    <span class="font-medium text-slate-800">{{ $instructor->specialization ?: 'General Enterprise Technology' }}</span>
                </div>

                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Experience</span>
                    <span class="font-medium text-slate-800">{{ $instructor->experience_years ? $instructor->experience_years . ' Years Industry Experience' : 'Not specified' }}</span>
                </div>

                <div>
                    <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block">Qualifications</span>
                    <span class="font-medium text-slate-800">{{ $instructor->qualification ?: 'Standard Faculty Credentials' }}</span>
                </div>

                @if ($instructor->bio)
                    <div class="pt-2">
                        <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 block mb-1">Biography</span>
                        <p class="text-xs text-slate-600 font-sans leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                            {{ $instructor->bio }}
                        </p>
                    </div>
                @endif
            </div>

            @if (! $canDelete && $cannotDeleteReason)
                <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-xs space-y-1">
                    <strong class="font-bold block uppercase text-[10px] font-mono text-amber-700">Deletion Protected</strong>
                    <p>{{ $cannotDeleteReason }}</p>
                </div>
            @endif
        </x-ui.card>

        {{-- RIGHT COLUMN: ASSIGNED COURSES & COURSE ASSIGNMENT TOOL --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ASSIGN COURSE CONTROLLER CARD --}}
            <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-heading font-bold text-base text-slate-900">
                            Course Assignments ({{ $assignedCourses->count() }})
                        </h3>
                        <p class="text-xs text-slate-500">
                            Courses this instructor is authorized to teach and assess
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-orange-50 text-[#FB6221] border border-orange-200">
                        {{ $assignedCourses->count() }} Assigned
                    </span>
                </div>

                {{-- ASSIGN NEW COURSE FORM --}}
                @if ($availableCourses->isNotEmpty())
                    <form method="POST" action="{{ route('admin.instructors.courses.assign', $instructor) }}" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-3">
                        @csrf
                        <div class="flex-1 w-full">
                            <label for="assign_course_id" class="sr-only">Course</label>
                            <select
                                id="assign_course_id"
                                name="course_id"
                                required
                                class="w-full px-3 py-2 rounded-lg text-xs sm:text-sm bg-white border border-slate-200 focus:outline-hidden focus:border-[#FB6221]"
                            >
                                <option value="">Select course to assign...</option>
                                @foreach ($availableCourses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->category_label }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-full sm:w-44">
                            <label for="assign_role" class="sr-only">Role</label>
                            <input
                                type="text"
                                id="assign_role"
                                name="role_or_title"
                                value="Lead Instructor"
                                placeholder="Role (e.g. Lead Instructor)"
                                class="w-full px-3 py-2 rounded-lg text-xs sm:text-sm bg-white border border-slate-200 focus:outline-hidden focus:border-[#FB6221]"
                            >
                        </div>

                        <x-ui.button type="submit" variant="primary" size="sm" class="w-full sm:w-auto shrink-0">
                            Assign Course
                        </x-ui.button>
                    </form>
                @endif

                {{-- CURRENTLY ASSIGNED COURSES LIST --}}
                @if ($assignedCourses->isEmpty())
                    <x-ui.empty-state
                        title="No Courses Assigned"
                        description="This instructor has not been assigned to any academy courses yet. Use the selector above to assign their first course."
                        icon="book"
                        :bordered="false"
                    />
                @else
                    <div class="space-y-3">
                        @foreach ($assignedCourses as $course)
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-white border border-slate-200 hover:border-slate-300 transition-all gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.courses.curriculum', $course) }}" class="font-bold text-slate-900 hover:text-[#FB6221] transition-colors font-heading text-sm block truncate">
                                            {{ $course->name }}
                                        </a>
                                        <x-ui.badge variant="neutral" size="xs">
                                            {{ $course->pivot->role_or_title ?? 'Instructor' }}
                                        </x-ui.badge>
                                        @if ($course->isAvailable())
                                            <x-ui.badge variant="success" size="xs">Available</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" size="xs">{{ ucfirst($course->status) }}</x-ui.badge>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-slate-500 mt-1 flex-wrap">
                                        <span>{{ $course->modules->count() }} Modules</span>
                                        <span>•</span>
                                        <span>{{ $course->enrollments->count() }} Enrolled Students</span>
                                        <span>•</span>
                                        <span class="text-slate-400 font-mono text-[11px]">Assigned {{ \Carbon\Carbon::parse($course->pivot->assigned_at)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <x-ui.button
                                        href="{{ route('admin.courses.curriculum', $course) }}"
                                        variant="secondary"
                                        size="xs"
                                    >
                                        Curriculum
                                    </x-ui.button>

                                    <form method="POST" action="{{ route('admin.instructors.courses.unassign', [$instructor, $course]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button
                                            type="submit"
                                            variant="ghost"
                                            size="xs"
                                            onclick="return confirm('Remove course assignment {{ $course->name }} from {{ $instructor->name }}?')"
                                            class="text-rose-600 hover:bg-rose-50"
                                        >
                                            Unassign
                                        </x-ui.button>
                                    </form>
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

