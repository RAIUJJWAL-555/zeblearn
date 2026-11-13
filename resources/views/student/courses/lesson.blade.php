@extends('layouts.app')

@section('title', $lesson->title . ' - ' . $course->name . ' | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
@php
    $embedUrl = null;
    $videoUrl = $lesson->video_url ?? '';

    if ($videoUrl) {
        // YouTube: https://www.youtube.com/watch?v=XXXX or https://youtu.be/XXXX
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $matches)) {
            $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
        }
        // Vimeo: https://vimeo.com/XXXX
        elseif (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)/i', $videoUrl, $matches)) {
            $embedUrl = 'https://player.vimeo.com/video/' . end($matches);
        }
    }
@endphp

<div x-data="{ mobileSidebarOpen: false }" class="min-h-screen bg-[#F8F9FA] pt-24 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- PLAYER TOP BAR --}}
        <x-ui.card padding="sm" class="border-slate-200/90 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <x-ui.button
                        href="{{ route('student.courses.show', $course) }}"
                        variant="secondary"
                        size="xs"
                        icon="arrow-left"
                        title="Back to Course Syllabus"
                    >
                        <span class="sr-only sm:not-sr-only">Syllabus</span>
                    </x-ui.button>

                    <div class="min-w-0">
                        <span class="text-[10px] font-mono uppercase tracking-wider text-[#FB6221] font-bold block truncate">
                            {{ $course->name }} • {{ $module->title }}
                        </span>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900 font-heading truncate">
                            {{ $lesson->title }}
                        </h1>
                    </div>
                </div>

                {{-- COURSE OVERALL PROGRESS & ACTIONS IN TOP BAR --}}
                <div class="flex items-center gap-3 sm:gap-5 self-stretch sm:self-auto justify-between sm:justify-end flex-wrap">
                    <div class="flex items-center gap-2.5">
                        <div class="w-28 sm:w-36 bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div
                                class="bg-gradient-to-r from-[#FB6221] to-emerald-500 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $courseProgress['percentage'] }}%"
                            ></div>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-700 whitespace-nowrap">
                            {{ $courseProgress['percentage'] }}%
                        </span>
                    </div>

                    {{-- MOBILE CURRICULUM DRAWER BUTTON --}}
                    <button
                        type="button"
                        @click="mobileSidebarOpen = true"
                        :aria-expanded="mobileSidebarOpen ? 'true' : 'false'"
                        aria-label="Toggle course curriculum menu"
                        class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-colors cursor-pointer"
                    >
                        <x-ui.icon name="menu" size="xs" />
                        <span>Curriculum</span>
                    </button>

                    <x-ui.button
                        href="{{ route('student.dashboard') }}"
                        variant="secondary"
                        size="xs"
                    >
                        Dashboard
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

        @if (session('error'))
            <x-ui.alert variant="error" dismissible>
                {{ session('error') }}
            </x-ui.alert>
        @endif

        {{-- MAIN PLAYER & SYLLABUS GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- MAIN STAGE (8 COLS) --}}
            <main class="lg:col-span-8 space-y-6">
                <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">

                    {{-- LESSON META HEADER --}}
                    <div class="border-b border-slate-100 pb-5">
                        <div class="flex items-center justify-between gap-4 mb-2 flex-wrap">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase bg-orange-50 text-[#FB6221] border border-orange-200">
                                    {{ strtoupper($lesson->content_type) }} LESSON
                                </span>

                                @if ($lesson->duration_minutes)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono text-slate-600 bg-slate-100">
                                        {{ $lesson->formatted_duration }}
                                    </span>
                                @endif
                            </div>

                            {{-- STATUS BADGE --}}
                            @if ($currentProgress && $currentProgress->isCompleted())
                                <x-ui.badge variant="success" size="sm" icon="check">
                                    Completed
                                </x-ui.badge>
                            @else
                                <x-ui.badge variant="warning" size="sm" dot dotPulse>
                                    In Progress
                                </x-ui.badge>
                            @endif
                        </div>

                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-heading tracking-tight">
                            {{ $lesson->title }}
                        </h2>

                        @if ($lesson->description)
                            <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed font-sans">
                                {{ $lesson->description }}
                            </p>
                        @endif
                    </div>

                    {{-- LESSON CONTENT VIEWER --}}
                    <div class="min-h-[220px]">
                        @if ($lesson->isVideo())
                            {{-- VIDEO LESSON --}}
                            @if ($embedUrl)
                                <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-900 shadow-md">
                                    <iframe
                                        src="{{ $embedUrl }}"
                                        title="{{ $lesson->title }}"
                                        class="absolute inset-0 w-full h-full border-0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        loading="lazy"
                                        referrerpolicy="strict-origin-when-cross-origin"
                                    ></iframe>
                                </div>
                                <div class="mt-3 flex items-center justify-end">
                                    <a
                                        href="{{ $lesson->video_url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-xs text-slate-500 hover:text-[#FB6221] flex items-center gap-1 font-mono transition-colors"
                                    >
                                        <span>Open Video Stream in new tab</span>
                                        <x-ui.icon name="external-link" size="xs" />
                                    </a>
                                </div>
                            @elseif ($lesson->video_url)
                                <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-slate-900 via-slate-900 to-black border border-slate-800 shadow-md">
                                    <div class="p-8 sm:p-12 text-center space-y-4">
                                        <div class="w-16 h-16 rounded-3xl bg-[#FB6221] text-white flex items-center justify-center mx-auto shadow-lg shadow-orange-500/30">
                                            <x-ui.icon name="sparkles" size="lg" />
                                        </div>
                                        <h3 class="text-white font-heading font-bold text-lg sm:text-xl">
                                            Watch Enterprise Training Video
                                        </h3>
                                        <p class="text-xs text-slate-300 max-w-md mx-auto font-sans leading-relaxed">
                                            Click below to stream this lesson directly on the secure academy video portal.
                                        </p>
                                        <div>
                                            <a
                                                href="{{ $lesson->video_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#FB6221] hover:bg-[#e05419] text-white font-bold text-xs uppercase tracking-wider shadow-md transition-all cursor-pointer"
                                            >
                                                <span>Open Video Stream</span>
                                                <x-ui.icon name="external-link" size="xs" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <x-ui.empty-state
                                    title="Video In Preparation"
                                    description="Video stream media for this lesson is currently being prepared by the instructors."
                                    icon="sparkles"
                                />
                            @endif
                        @elseif ($lesson->isResource())
                            {{-- RESOURCE LINK LESSON --}}
                            <div class="rounded-2xl p-6 sm:p-8 bg-purple-50/60 border border-purple-200/80 space-y-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                        <x-ui.icon name="external-link" size="md" />
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-slate-900 font-heading text-base">External Learning Resource</h3>
                                        <p class="text-xs text-slate-600 mt-1 font-sans">
                                            This topic provides access to dedicated enterprise sandbox servers, cloud blueprints, or syllabus reference files.
                                        </p>
                                        @if ($lesson->resource_url)
                                            <a
                                                href="{{ $lesson->resource_url }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs transition-colors"
                                            >
                                                <span>Access Resource URL</span>
                                                <x-ui.icon name="external-link" size="xs" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- TEXT LESSON --}}
                            @if (!empty(trim($lesson->content ?? '')))
                                <div class="prose max-w-none text-slate-800 text-sm sm:text-base leading-relaxed space-y-4 font-sans">
                                    {!! nl2br(e($lesson->content)) !!}
                                </div>
                            @else
                                <x-ui.empty-state
                                    title="Content In Preparation"
                                    description="Reading materials and study notes for this lesson will be posted shortly."
                                    icon="book"
                                />
                            @endif
                        @endif
                    </div>

                    {{-- PROGRESS ACTION & COMPLETION TOGGLE --}}
                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        @if ($currentProgress && $currentProgress->isCompleted())
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-700 bg-emerald-50 px-3.5 py-2 rounded-xl border border-emerald-200">
                                    <x-ui.icon name="check-circle" size="xs" class="text-emerald-600" />
                                    Lesson marked as completed
                                </span>
                            </div>

                            <form method="POST" action="{{ route('student.courses.lesson.incomplete', [$course, $lesson]) }}">
                                @csrf
                                <x-ui.button
                                    type="submit"
                                    variant="secondary"
                                    size="sm"
                                    icon="arrow-left"
                                >
                                    Mark as Incomplete
                                </x-ui.button>
                            </form>
                        @else
                            <div class="text-xs text-slate-500 font-mono">
                                Mark this lesson complete once finished reading or watching.
                            </div>

                            <form method="POST" action="{{ route('student.courses.lesson.complete', [$course, $lesson]) }}">
                                @csrf
                                <x-ui.button
                                    type="submit"
                                    variant="primary"
                                    size="sm"
                                    icon="check"
                                    class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 shadow-sm"
                                >
                                    Mark as Complete
                                </x-ui.button>
                            </form>
                        @endif
                    </div>

                    {{-- LESSON ASSESSMENT / QUIZ BANNER --}}
                    @if ($lesson->quizzes && $lesson->quizzes->isNotEmpty())
                        <div class="p-5 rounded-2xl bg-gradient-to-r from-purple-50 via-purple-50/60 to-orange-50 border border-purple-200/90 shadow-2xs space-y-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                    <x-ui.icon name="academic-cap" size="xs" />
                                </span>
                                <div>
                                    <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-purple-700 block">Lesson Assessment</span>
                                    <h4 class="text-sm font-bold text-slate-900 font-heading">
                                        Check your understanding for this lesson
                                    </h4>
                                </div>
                            </div>

                            <div class="space-y-2">
                                @foreach ($lesson->quizzes as $lQuiz)
                                    <div class="flex items-center justify-between gap-4 p-3 bg-white rounded-xl border border-purple-200/80">
                                        <div>
                                            <span class="font-bold text-xs sm:text-sm text-slate-900 block font-heading">{{ $lQuiz->title }}</span>
                                            <div class="flex items-center gap-2 text-[11px] font-mono text-slate-500 mt-0.5">
                                                <span>{{ $lQuiz->formatted_time_limit }}</span>
                                                <span>&bull;</span>
                                                <span>Pass: {{ $lQuiz->pass_percentage }}%</span>
                                                @if ($lQuiz->allowed_attempts)
                                                    <span>&bull;</span>
                                                    <span>Max: {{ $lQuiz->allowed_attempts }} attempts</span>
                                                @endif
                                            </div>
                                        </div>
                                        <x-ui.button
                                            href="{{ route('student.quizzes.show', [$course, $lQuiz]) }}"
                                            variant="primary"
                                            size="xs"
                                            class="bg-purple-600 hover:bg-purple-700 text-white"
                                        >
                                            Take Quiz
                                        </x-ui.button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- PREVIOUS / NEXT NAVIGATION CONTROLS --}}
                    <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                        @if ($prevLesson)
                            <x-ui.button
                                href="{{ route('student.courses.lesson', [$course, $prevLesson]) }}"
                                variant="secondary"
                                size="sm"
                                icon="arrow-left"
                            >
                                <span>Previous Lesson</span>
                            </x-ui.button>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-100 text-xs font-semibold text-slate-300 bg-slate-50 cursor-not-allowed select-none">
                                <x-ui.icon name="arrow-left" size="xs" class="text-slate-300" />
                                <span>First Lesson</span>
                            </span>
                        @endif

                        @if ($nextLesson)
                            <x-ui.button
                                href="{{ route('student.courses.lesson', [$course, $nextLesson]) }}"
                                variant="dark"
                                size="sm"
                                icon="arrow-right"
                                class="hover:!bg-[#FB6221]"
                            >
                                <span>Next Lesson</span>
                            </x-ui.button>
                        @else
                            <x-ui.button
                                href="{{ route('student.courses.show', $course) }}"
                                variant="primary"
                                size="sm"
                                icon="check"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white"
                            >
                                <span>Course Completed</span>
                            </x-ui.button>
                        @endif
                    </div>

                </x-ui.card>
            </main>

            {{-- SYLLABUS SIDEBAR (4 COLS ON DESKTOP) --}}
            <aside class="hidden lg:block lg:col-span-4 bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-2xs space-y-4 sticky top-28">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-tight">
                            Course Syllabus
                        </h3>
                        <p class="text-[11px] font-mono text-slate-500 mt-0.5">
                            {{ $courseProgress['completed'] }} of {{ $courseProgress['total'] }} completed
                        </p>
                    </div>
                    <span class="font-mono text-xs font-bold text-[#FB6221]">
                        {{ $courseProgress['percentage'] }}%
                    </span>
                </div>

                <div class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    @foreach ($modules as $mod)
                        <div class="space-y-1.5">
                            <span class="block text-[10px] font-mono font-bold uppercase tracking-widest text-[#FB6221]">
                                {{ $mod->title }}
                            </span>
                            <div class="space-y-1">
                                @foreach ($mod->lessons as $l)
                                    @php
                                        $lProgress = $progressMap->get($l->id);
                                        $isCompleted = $lProgress && $lProgress->isCompleted();
                                        $isActive = $l->id === $lesson->id;
                                    @endphp
                                    <a
                                        href="{{ route('student.courses.lesson', [$course, $l]) }}"
                                        class="flex items-center justify-between p-2 rounded-xl text-xs transition-colors {{ $isActive ? 'bg-[#0A0A0A] text-white font-bold shadow-2xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                                    >
                                        <div class="flex items-center gap-2.5 truncate">
                                            @if ($isCompleted)
                                                <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                                    <x-ui.icon name="check" size="xs" class="text-white w-2.5 h-2.5" />
                                                </span>
                                            @elseif ($isActive)
                                                <span class="w-4 h-4 rounded-full bg-[#FB6221] text-white flex items-center justify-center shrink-0">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                </span>
                                            @else
                                                <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center shrink-0">
                                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                </span>
                                            @endif
                                            <span class="truncate">{{ $l->title }}</span>
                                        </div>

                                        @if ($l->duration_minutes)
                                            <span class="text-[10px] font-mono {{ $isActive ? 'text-slate-300' : 'text-slate-400' }} shrink-0">
                                                {{ $l->formatted_duration }}
                                            </span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </aside>

        </div>

        {{-- MOBILE DRAWER / SLIDE-OVER MODAL --}}
        <div
            x-show="mobileSidebarOpen"
            x-cloak
            class="fixed inset-0 z-50 lg:hidden"
            role="dialog"
            aria-modal="true"
            aria-label="Course curriculum drawer"
        >
            {{-- Backdrop --}}
            <div
                x-show="mobileSidebarOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs"
                @click="mobileSidebarOpen = false"
                aria-hidden="true"
            ></div>

            {{-- Drawer Content --}}
            <div
                x-show="mobileSidebarOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 max-w-sm w-full bg-white shadow-2xl p-6 flex flex-col justify-between overflow-y-auto"
            >
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-heading font-bold text-base text-slate-900 uppercase tracking-tight">
                                Course Curriculum
                            </h3>
                            <p class="text-xs font-mono text-slate-500 mt-0.5">
                                {{ $courseProgress['completed'] }} of {{ $courseProgress['total'] }} lessons completed ({{ $courseProgress['percentage'] }}%)
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="mobileSidebarOpen = false"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 cursor-pointer"
                            aria-label="Close curriculum drawer"
                        >
                            <x-ui.icon name="x" size="sm" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        @foreach ($modules as $mod)
                            <div class="space-y-1.5">
                                <span class="block text-[10px] font-mono font-bold uppercase tracking-widest text-[#FB6221]">
                                    {{ $mod->title }}
                                </span>
                                <div class="space-y-1">
                                    @foreach ($mod->lessons as $l)
                                        @php
                                            $lProgress = $progressMap->get($l->id);
                                            $isCompleted = $lProgress && $lProgress->isCompleted();
                                            $isActive = $l->id === $lesson->id;
                                        @endphp
                                        <a
                                            href="{{ route('student.courses.lesson', [$course, $l]) }}"
                                            @click="mobileSidebarOpen = false"
                                            class="flex items-center justify-between p-2.5 rounded-xl text-xs transition-colors {{ $isActive ? 'bg-[#0A0A0A] text-white font-bold' : 'text-slate-600 hover:bg-slate-100' }}"
                                        >
                                            <div class="flex items-center gap-2.5 truncate">
                                                @if ($isCompleted)
                                                    <span class="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                                        <x-ui.icon name="check" size="xs" class="text-white w-2.5 h-2.5" />
                                                    </span>
                                                @elseif ($isActive)
                                                    <span class="w-4 h-4 rounded-full bg-[#FB6221] text-white flex items-center justify-center shrink-0">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                                    </span>
                                                @else
                                                    <span class="w-4 h-4 rounded-full border border-slate-300 flex items-center justify-center shrink-0">
                                                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                                    </span>
                                                @endif
                                                <span class="truncate">{{ $l->title }}</span>
                                            </div>

                                            @if ($l->duration_minutes)
                                                <span class="text-[10px] font-mono {{ $isActive ? 'text-slate-300' : 'text-slate-400' }} shrink-0">
                                                    {{ $l->formatted_duration }}
                                                </span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <x-ui.button
                        href="{{ route('student.courses.show', $course) }}"
                        variant="secondary"
                        size="sm"
                        fullWidth
                    >
                        View Full Syllabus
                    </x-ui.button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
