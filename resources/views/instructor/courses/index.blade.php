@extends('layouts.instructor')

@section('title', 'My Assigned Courses | ZebLearn LMS')

@section('content')
<div class="space-y-6">
    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Portal</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Assigned Courses</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                My Assigned Courses
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Courses assigned to you for teaching, syllabus oversight, and student evaluation.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-mono font-bold bg-white border border-slate-200 text-slate-700 shadow-2xs">
                Total: {{ $courses->total() }} {{ Str::plural('Course', $courses->total()) }}
            </span>
        </div>
    </div>

    {{-- COURSES GRID --}}
    @if ($courses->isEmpty())
        <x-ui.card padding="lg" class="text-center">
            <div class="max-w-md mx-auto space-y-3 py-8">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-[#FB6221] flex items-center justify-center mx-auto border border-orange-200">
                    <x-ui.icon name="book" size="md" />
                </div>
                <h3 class="font-heading font-bold text-base text-slate-900">
                    No Courses Assigned
                </h3>
                <p class="text-xs text-slate-500">
                    You have not been assigned to any courses yet. Please contact an academy administrator to assign your curriculum modules.
                </p>
            </div>
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($courses as $course)
                <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs relative group hover:border-[#FB6221]/40 transition-all flex flex-col justify-between">
                    <div class="space-y-4">
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
                            <h2 class="font-heading font-black text-lg text-slate-900 group-hover:text-[#FB6221] transition-colors line-clamp-1">
                                {{ $course->name }}
                            </h2>
                            <p class="text-xs text-slate-500 line-clamp-2 mt-1">
                                {{ $course->short_description }}
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-100 text-center">
                            <div>
                                <span class="text-xs font-black text-slate-900 block font-heading">{{ $course->modules_count }}</span>
                                <span class="text-[10px] font-mono uppercase text-slate-400 block">Modules</span>
                            </div>
                            <div>
                                <span class="text-xs font-black text-slate-900 block font-heading">{{ $course->enrollments->count() }}</span>
                                <span class="text-[10px] font-mono uppercase text-slate-400 block">Students</span>
                            </div>
                            <div>
                                <span class="text-xs font-black text-slate-900 block font-heading">{{ $course->assignments_count }}</span>
                                <span class="text-[10px] font-mono uppercase text-slate-400 block">Projects</span>
                            </div>
                        </div>

                        <div class="text-[11px] font-mono text-slate-400">
                            Role: <span class="text-slate-700 font-semibold">{{ $course->pivot->role_or_title ?? 'Lead Instructor' }}</span>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                        <x-ui.button
                            href="{{ route('instructor.courses.students', $course) }}"
                            variant="secondary"
                            size="xs"
                            icon="user-group"
                        >
                            Students ({{ $course->enrollments->count() }})
                        </x-ui.button>

                        <x-ui.button
                            href="{{ route('instructor.courses.show', $course) }}"
                            variant="dark"
                            size="xs"
                            icon="arrow-right"
                        >
                            Syllabus
                        </x-ui.button>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        @if ($courses->hasPages())
            <div class="pt-4">
                {{ $courses->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

