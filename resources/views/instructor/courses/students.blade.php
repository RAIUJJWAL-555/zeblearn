@extends('layouts.instructor')

@section('title', 'Students - ' . $course->name . ' | ZebLearn LMS')

@section('content')
<div class="space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('instructor.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Portal</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span>/</span>
                <a href="{{ route('instructor.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors">{{ $course->name }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Students</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Enrolled Students &amp; Progress
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Student roster, attendance and curriculum completion for {{ $course->name }}.
            </p>
        </div>

        <x-ui.button
            href="{{ route('instructor.courses.show', $course) }}"
            variant="secondary"
            size="sm"
            icon="arrow-left"
        >
            Back to Course
        </x-ui.button>
    </div>

    {{-- STUDENTS TABLE CARD --}}
    <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
        @if ($studentsWithProgress->isEmpty())
            <div class="p-8">
                <x-ui.empty-state
                    title="No Students Enrolled"
                    description="There are currently no approved student enrollments for this course."
                    icon="user-group"
                />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Student</th>
                            <th class="py-3.5 px-4 sm:px-6">Enrollment Date</th>
                            <th class="py-3.5 px-4 sm:px-6">Curriculum Progress</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Completion Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($studentsWithProgress as $item)
                            @php
                                $student = $item['student'];
                                $enrollment = $item['enrollment'];
                                $progress = $item['progress'];
                                $pct = $progress['percentage'] ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-slate-800 text-white flex items-center justify-center font-heading font-bold text-xs shrink-0 border border-slate-700">
                                            {{ strtoupper(substr($student?->name ?? 'ST', 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-900 block truncate font-heading">
                                                {{ $student?->name ?? 'Student' }}
                                            </span>
                                            <span class="text-xs text-slate-500 block truncate">
                                                {{ $student?->email }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-4 sm:px-6 font-mono text-xs text-slate-500">
                                    {{ $enrollment->approved_at ? $enrollment->approved_at->format('d M Y') : $enrollment->created_at->format('d M Y') }}
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    <div class="space-y-1.5 max-w-xs">
                                        <div class="flex items-center justify-between text-xs font-mono">
                                            <span class="font-bold text-slate-800">{{ $pct }}%</span>
                                            <span class="text-slate-400">{{ $progress['completed'] ?? 0 }}/{{ $progress['total'] ?? 0 }} Lessons</span>
                                        </div>
                                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                            <div
                                                class="h-full rounded-full transition-all duration-300 {{ $pct >= 100 ? 'bg-emerald-500' : 'bg-[#FB6221]' }}"
                                                style="width: {{ $pct }}%"
                                            ></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-4 sm:px-6 text-right">
                                    @if ($pct >= 100)
                                        <x-ui.badge variant="success" size="xs" dot>Course Completed</x-ui.badge>
                                    @elseif ($pct > 0)
                                        <x-ui.badge variant="info" size="xs">In Progress</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="xs">Not Started</x-ui.badge>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($enrollments->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $enrollments->links() }}
                </div>
            @endif
        @endif
    </x-ui.card>
</div>
@endsection

