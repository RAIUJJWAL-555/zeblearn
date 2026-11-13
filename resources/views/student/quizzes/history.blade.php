@extends('layouts.app')

@section('title', 'Attempt History: ' . $quiz->title . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- BREADCRUMBS --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $course->name }}</a>
                <span>/</span>
                <a href="{{ route('student.quizzes.show', [$course, $quiz]) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $quiz->title }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">History</span>
            </nav>
            <x-ui.button
                href="{{ route('student.quizzes.show', [$course, $quiz]) }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Assessment
            </x-ui.button>
        </div>

        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
            <div>
                <h1 class="text-2xl font-black text-slate-900 font-heading tracking-tight">
                    Assessment History: {{ $quiz->title }}
                </h1>
                <p class="text-xs text-slate-500 font-mono mt-1">
                    Course: {{ $course->name }} • Passing Threshold: {{ $quiz->passing_percentage }}%
                </p>
            </div>

            @if ($attempts->isEmpty())
                <x-ui.empty-state
                    title="No Submitted Attempts"
                    description="No submitted attempts recorded yet for this assessment."
                    icon="academic-cap"
                />
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($attempts as $att)
                        <div class="py-4 flex items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <strong class="text-sm font-bold text-slate-900 font-heading">Attempt #{{ $att->attempt_number }}</strong>
                                    @if ($att->is_passed)
                                        <x-ui.badge variant="success" size="xs">
                                            Passed
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="danger" size="xs">
                                            Failed
                                        </x-ui.badge>
                                    @endif
                                </div>
                                <span class="text-xs font-mono text-slate-500 mt-1 block">
                                    {{ $att->submitted_at?->format('d M Y, h:i A') }}
                                </span>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right font-mono text-xs">
                                    <strong class="text-slate-900 text-sm font-heading">{{ $att->score }}/{{ $att->total_marks }}</strong>
                                    <span class="text-slate-500 block">({{ $att->percentage }}%)</span>
                                </div>

                                <x-ui.button
                                    href="{{ route('student.quizzes.result', [$course, $quiz, $att]) }}"
                                    variant="secondary"
                                    size="sm"
                                >
                                    View Result
                                </x-ui.button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</div>
@endsection
