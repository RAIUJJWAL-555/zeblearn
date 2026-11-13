@extends('layouts.admin')

@section('title', 'Curriculum & Lessons - ' . $course->name . ' | ZebLearn LMS')
@section('page-title', 'Curriculum')

@section('content')
<div
    class="max-w-7xl mx-auto space-y-6 pb-16"
    x-data="{
        moduleModalOpen: false,
        moduleEditModalOpen: false,
        activeModule: { id: null, title: '', description: '', status: 'published', updateUrl: '' },

        lessonModalOpen: false,
        lessonEditModalOpen: false,
        activeModuleForLesson: { id: null, title: '', storeUrl: '' },
        activeLesson: {
            id: null,
            title: '',
            description: '',
            content_type: 'text',
            content: '',
            video_url: '',
            resource_url: '',
            duration_minutes: '',
            status: 'published',
            updateUrl: ''
        },

        openAddModule() {
            this.moduleModalOpen = true;
        },

        openEditModule(mod, updateUrl) {
            this.activeModule = {
                id: mod.id,
                title: mod.title,
                description: mod.description || '',
                status: mod.status,
                updateUrl: updateUrl
            };
            this.moduleEditModalOpen = true;
        },

        openAddLesson(mod, storeUrl) {
            this.activeModuleForLesson = {
                id: mod.id,
                title: mod.title,
                storeUrl: storeUrl
            };
            this.lessonModalOpen = true;
        },

        openEditLesson(lesson, updateUrl) {
            this.activeLesson = {
                id: lesson.id,
                title: lesson.title,
                description: lesson.description || '',
                content_type: lesson.content_type,
                content: lesson.content || '',
                video_url: lesson.video_url || '',
                resource_url: lesson.resource_url || '',
                duration_minutes: lesson.duration_minutes || '',
                status: lesson.status,
                updateUrl: updateUrl
            };
            this.lessonEditModalOpen = true;
        }
    }"
    @keydown.escape.window="
        moduleModalOpen = false;
        moduleEditModalOpen = false;
        lessonModalOpen = false;
        lessonEditModalOpen = false;
    "
>
    {{-- BREADCRUMBS & TOP BAR --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span>/</span>
                <a href="{{ route('admin.courses.edit', $course) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs">{{ $course->name }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Curriculum</span>
            </nav>

            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight truncate max-w-2xl">
                    {{ $course->name }}
                </h1>
                <x-ui.badge variant="primary" size="sm" class="font-mono font-bold uppercase tracking-wider">
                    {{ $course->category_badge }}
                </x-ui.badge>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Organize curriculum structure into sequential modules and rich learning lessons.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <x-ui.button
                href="{{ route('admin.courses.edit', $course) }}"
                variant="secondary"
                size="md"
                icon="arrow-left"
            >
                Edit Course
            </x-ui.button>

            <x-ui.button
                type="button"
                @click="openAddModule()"
                variant="primary"
                size="md"
                icon="plus"
            >
                Add Module
            </x-ui.button>
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

    @if ($errors->any())
        <x-ui.alert variant="danger">
            <div class="space-y-1">
                <p class="font-bold">Please check your inputs:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-ui.alert>
    @endif

    {{-- STATS OVERVIEW CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Modules</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">{{ $stats['total_modules'] }}</span>
                <span class="text-xs text-slate-500">({{ $stats['published_modules'] }} live)</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Lessons</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">{{ $stats['total_lessons'] }}</span>
                <span class="text-xs text-slate-500">({{ $stats['published_lessons'] }} live)</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Duration</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    @if ($stats['total_duration'] > 0)
                        {{ round($stats['total_duration'] / 60, 1) }}<span class="text-sm font-normal text-slate-500"> hrs</span>
                    @else
                        0<span class="text-sm font-normal text-slate-500"> mins</span>
                    @endif
                </span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Draft Elements</span>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading">
                    {{ $stats['draft_modules'] }}
                </span>
                <span class="text-xs text-slate-500">draft modules</span>
            </div>
        </div>
    </div>

    {{-- CURRICULUM MODULES LIST --}}
    @if ($modules->isEmpty())
        {{-- EMPTY STATE --}}
        <x-ui.card padding="lg" class="border-dashed">
            <x-ui.empty-state
                title="No Curriculum Modules Yet"
                description="Start structuring this course by adding its first curriculum module (e.g., 'Introduction & Architecture', 'General Ledger Configuration')."
                icon="book"
                :bordered="false"
            >
                <x-ui.button
                    type="button"
                    @click="openAddModule()"
                    variant="primary"
                    size="md"
                    icon="plus"
                >
                    Create First Module
                </x-ui.button>
            </x-ui.empty-state>
        </x-ui.card>
    @else
        {{-- MODULES STACK --}}
        <div class="space-y-6">
            @foreach ($modules as $moduleIndex => $module)
                <x-ui.card padding="none">
                    {{-- MODULE HEADER --}}
                    <div class="p-5 sm:p-6 bg-slate-50/70 border-b border-slate-200/80 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3.5">
                            {{-- Module Reorder Controls --}}
                            <div class="flex flex-col items-center gap-1 shrink-0 pt-0.5">
                                {{-- Move Up --}}
                                @if ($moduleIndex > 0)
                                    @php
                                        $upModuleIds = $modules->pluck('id')->toArray();
                                        $temp = $upModuleIds[$moduleIndex];
                                        $upModuleIds[$moduleIndex] = $upModuleIds[$moduleIndex - 1];
                                        $upModuleIds[$moduleIndex - 1] = $temp;
                                    @endphp
                                    <form action="{{ route('admin.courses.modules.reorder', $course) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @foreach ($upModuleIds as $mId)
                                            <input type="hidden" name="module_ids[]" value="{{ $mId }}" />
                                        @endforeach
                                        <button type="submit" class="p-1 rounded text-slate-400 hover:text-slate-900 hover:bg-slate-200/60 transition-colors cursor-pointer" title="Move Module Up">
                                            <x-ui.icon name="chevron-up" size="xs" />
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1 text-slate-200 cursor-not-allowed">
                                        <x-ui.icon name="chevron-up" size="xs" />
                                    </span>
                                @endif

                                {{-- Move Down --}}
                                @if ($moduleIndex < count($modules) - 1)
                                    @php
                                        $downModuleIds = $modules->pluck('id')->toArray();
                                        $temp = $downModuleIds[$moduleIndex];
                                        $downModuleIds[$moduleIndex] = $downModuleIds[$moduleIndex + 1];
                                        $downModuleIds[$moduleIndex + 1] = $temp;
                                    @endphp
                                    <form action="{{ route('admin.courses.modules.reorder', $course) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @foreach ($downModuleIds as $mId)
                                            <input type="hidden" name="module_ids[]" value="{{ $mId }}" />
                                        @endforeach
                                        <button type="submit" class="p-1 rounded text-slate-400 hover:text-slate-900 hover:bg-slate-200/60 transition-colors cursor-pointer" title="Move Module Down">
                                            <x-ui.icon name="chevron-down" size="xs" />
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1 text-slate-200 cursor-not-allowed">
                                        <x-ui.icon name="chevron-down" size="xs" />
                                    </span>
                                @endif
                            </div>

                            <div>
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    <span class="font-mono text-xs font-bold text-slate-400 uppercase tracking-widest">
                                        MODULE {{ str_pad($moduleIndex + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    @if ($module->isPublished())
                                        <x-ui.badge variant="success" size="xs" dot>
                                            Published
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral" size="xs">
                                            Draft
                                        </x-ui.badge>
                                    @endif

                                    <span class="text-xs text-slate-400 font-mono">
                                        • {{ $module->lessons->count() }} {{ \Illuminate\Support\Str::plural('Lesson', $module->lessons->count()) }}
                                    </span>
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading mt-1">
                                    {{ $module->title }}
                                </h3>

                                @if ($module->description)
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                                        {{ $module->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Module Actions --}}
                        <div class="flex items-center gap-2 self-end md:self-center shrink-0">
                            {{-- Add Lesson to this module --}}
                            <x-ui.button
                                type="button"
                                @click="openAddLesson({{ json_encode($module) }}, '{{ route('admin.courses.lessons.store', [$course, $module]) }}')"
                                variant="dark"
                                size="xs"
                                icon="plus"
                            >
                                Add Lesson
                            </x-ui.button>

                            {{-- Edit Module --}}
                            <button
                                type="button"
                                @click="openEditModule({{ json_encode($module) }}, '{{ route('admin.courses.modules.update', [$course, $module]) }}')"
                                class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-200/70 transition-colors cursor-pointer"
                                title="Edit Module"
                            >
                                <x-ui.icon name="pencil" size="xs" />
                            </button>

                            {{-- Delete Module (Safe Check) --}}
                            <form
                                action="{{ route('admin.courses.modules.destroy', [$course, $module]) }}"
                                method="POST"
                                onsubmit="return confirm('{{ $module->lessons->count() > 0 ? "WARNING: This module contains {$module->lessons->count()} lesson(s). Are you sure you want to delete this module and its lessons?" : "Delete module \'{$module->title}\'?" }}')"
                                class="inline"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="force" value="1" />
                                <button
                                    type="submit"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Delete Module"
                                >
                                    <x-ui.icon name="trash" size="xs" />
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- LESSONS LIST --}}
                    <div class="divide-y divide-slate-100">
                        @if ($module->lessons->isEmpty())
                            <div class="p-6 text-center text-slate-400 text-xs">
                                <span>No lessons added to this module yet.</span>
                                <button
                                    type="button"
                                    @click="openAddLesson({{ json_encode($module) }}, '{{ route('admin.courses.lessons.store', [$course, $module]) }}')"
                                    class="text-[#FB6221] font-semibold hover:underline ml-1 cursor-pointer"
                                >
                                    Add the first lesson &rarr;
                                </button>
                            </div>
                        @else
                            @foreach ($module->lessons as $lessonIndex => $lesson)
                                <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        {{-- Lesson Reorder Controls --}}
                                        <div class="flex flex-col items-center gap-0.5 shrink-0 pt-0.5">
                                            @if ($lessonIndex > 0)
                                                @php
                                                    $upLessonIds = $module->lessons->pluck('id')->toArray();
                                                    $temp = $upLessonIds[$lessonIndex];
                                                    $upLessonIds[$lessonIndex] = $upLessonIds[$lessonIndex - 1];
                                                    $upLessonIds[$lessonIndex - 1] = $temp;
                                                @endphp
                                                <form action="{{ route('admin.courses.lessons.reorder', [$course, $module]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    @foreach ($upLessonIds as $lId)
                                                        <input type="hidden" name="lesson_ids[]" value="{{ $lId }}" />
                                                    @endforeach
                                                    <button type="submit" class="p-0.5 rounded text-slate-400 hover:text-slate-900 transition-colors cursor-pointer" title="Move Lesson Up">
                                                        <x-ui.icon name="chevron-up" size="xs" />
                                                    </button>
                                                </form>
                                            @else
                                                <span class="p-0.5 text-slate-200 cursor-not-allowed">
                                                    <x-ui.icon name="chevron-up" size="xs" />
                                                </span>
                                            @endif

                                            @if ($lessonIndex < count($module->lessons) - 1)
                                                @php
                                                    $downLessonIds = $module->lessons->pluck('id')->toArray();
                                                    $temp = $downLessonIds[$lessonIndex];
                                                    $downLessonIds[$lessonIndex] = $downLessonIds[$lessonIndex + 1];
                                                    $downLessonIds[$lessonIndex + 1] = $temp;
                                                @endphp
                                                <form action="{{ route('admin.courses.lessons.reorder', [$course, $module]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    @foreach ($downLessonIds as $lId)
                                                        <input type="hidden" name="lesson_ids[]" value="{{ $lId }}" />
                                                    @endforeach
                                                    <button type="submit" class="p-0.5 rounded text-slate-400 hover:text-slate-900 transition-colors cursor-pointer" title="Move Lesson Down">
                                                        <x-ui.icon name="chevron-down" size="xs" />
                                                    </button>
                                                </form>
                                            @else
                                                <span class="p-0.5 text-slate-200 cursor-not-allowed">
                                                    <x-ui.icon name="chevron-down" size="xs" />
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Content Type Icon --}}
                                        <div class="shrink-0 mt-0.5">
                                            @if ($lesson->isVideo())
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/60" title="Video Lesson">
                                                    <x-ui.icon name="eye" size="xs" />
                                                </div>
                                            @elseif ($lesson->isResource())
                                                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/60" title="Resource Link">
                                                    <x-ui.icon name="external-link" size="xs" />
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/60" title="Text Lesson">
                                                    <x-ui.icon name="book" size="xs" />
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Lesson Details --}}
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-semibold text-sm text-slate-900 font-heading">
                                                    {{ $lesson->title }}
                                                </span>

                                                @if ($lesson->isPublished())
                                                    <x-ui.badge variant="success" size="xs" dot>
                                                        Published
                                                    </x-ui.badge>
                                                @else
                                                    <x-ui.badge variant="neutral" size="xs">
                                                        Draft
                                                    </x-ui.badge>
                                                @endif

                                                <span class="text-[11px] font-mono text-slate-500 uppercase">
                                                    {{ $lesson->content_type }}
                                                </span>

                                                @if ($lesson->duration_minutes)
                                                    <span class="text-[11px] text-slate-400 font-mono">
                                                        • {{ $lesson->formatted_duration }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($lesson->description)
                                                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">
                                                    {{ $lesson->description }}
                                                </p>
                                            @endif

                                            {{-- External URL Preview if Video or Resource --}}
                                            @if ($lesson->isVideo() && $lesson->video_url)
                                                <a href="{{ $lesson->video_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline mt-1">
                                                    <span>{{ $lesson->video_url }}</span>
                                                    <x-ui.icon name="external-link" size="xs" />
                                                </a>
                                            @elseif ($lesson->isResource() && $lesson->resource_url)
                                                <a href="{{ $lesson->resource_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-purple-600 hover:underline mt-1">
                                                    <span>{{ $lesson->resource_url }}</span>
                                                    <x-ui.icon name="external-link" size="xs" />
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Lesson Actions --}}
                                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                        {{-- Edit Lesson --}}
                                        <button
                                            type="button"
                                            @click="openEditLesson({{ json_encode($lesson) }}, '{{ route('admin.courses.lessons.update', [$course, $module, $lesson]) }}')"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-200/60 transition-colors cursor-pointer"
                                            title="Edit Lesson"
                                        >
                                            <x-ui.icon name="pencil" size="xs" />
                                        </button>

                                        {{-- Delete Lesson --}}
                                        <form
                                            action="{{ route('admin.courses.lessons.destroy', [$course, $module, $lesson]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete lesson \'{{ addslashes($lesson->title) }}\'?')"
                                            class="inline"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Delete Lesson"
                                            >
                                                <x-ui.icon name="trash" size="xs" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @endif

    {{-- MODAL 1: ADD MODULE --}}
    <div
        x-show="moduleModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="moduleModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="moduleModalOpen = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="moduleModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200"
            >
                <form action="{{ route('admin.courses.modules.store', $course) }}" method="POST">
                    @csrf
                    <div class="p-6 sm:p-8 space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-heading">Add Curriculum Module</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Create a new sequential module for {{ $course->name }}</p>
                            </div>
                            <button type="button" @click="moduleModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        <div>
                            <label for="module_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Module Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="module_title"
                                name="title"
                                required
                                placeholder="e.g. Enterprise General Ledger & Document Splitting"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            />
                        </div>

                        <div>
                            <label for="module_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Description <span class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <textarea
                                id="module_description"
                                name="description"
                                rows="3"
                                placeholder="Summary of topics covered in this module..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            ></textarea>
                        </div>

                        <div>
                            <label for="module_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Publishing Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="module_status"
                                name="status"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                            >
                                <option value="published" selected>Published (Visible to Enrolled Students)</option>
                                <option value="draft">Draft (Curriculum in Preparation)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <x-ui.button
                            type="button"
                            @click="moduleModalOpen = false"
                            variant="secondary"
                            size="sm"
                        >
                            Cancel
                        </x-ui.button>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                        >
                            Create Module
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: EDIT MODULE --}}
    <div
        x-show="moduleEditModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="moduleEditModalOpen"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="moduleEditModalOpen = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="moduleEditModalOpen"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200"
            >
                <form :action="activeModule.updateUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 sm:p-8 space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-heading">Edit Curriculum Module</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Update title, summary, or publication state</p>
                            </div>
                            <button type="button" @click="moduleEditModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Module Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                aria-label="Edit Module Title"
                                x-model="activeModule.title"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Description <span class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <textarea
                                name="description"
                                aria-label="Edit Module Description"
                                x-model="activeModule.description"
                                rows="3"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Publishing Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="status"
                                aria-label="Edit Module Publishing Status"
                                x-model="activeModule.status"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                            >
                                <option value="published">Published (Visible to Enrolled Students)</option>
                                <option value="draft">Draft (Curriculum in Preparation)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <x-ui.button
                            type="button"
                            @click="moduleEditModalOpen = false"
                            variant="secondary"
                            size="sm"
                        >
                            Cancel
                        </x-ui.button>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                        >
                            Save Changes
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 3: ADD LESSON --}}
    <div
        x-show="lessonModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="lessonModalOpen"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="lessonModalOpen = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="lessonModalOpen"
                x-data="{ contentType: 'text' }"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200"
            >
                <form :action="activeModuleForLesson.storeUrl" method="POST">
                    @csrf
                    <div class="p-6 sm:p-8 space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-heading">Add Lesson</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Adding to <span class="font-bold text-slate-800" x-text="activeModuleForLesson.title"></span>
                                </p>
                            </div>
                            <button type="button" @click="lessonModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Lesson Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                aria-label="Lesson Title"
                                required
                                placeholder="e.g. Configuring Zero-Balance Clearing Rules"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Content Type <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    name="content_type"
                                    aria-label="Lesson Content Type"
                                    x-model="contentType"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                                >
                                    <option value="text">Text Lesson (Reading &amp; Code)</option>
                                    <option value="video">Video Lesson (External Streaming URL)</option>
                                    <option value="resource">Resource Link (Sandbox / Drive / Docs)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Duration <span class="text-slate-400 font-normal">(minutes)</span>
                                </label>
                                <input
                                    type="number"
                                    name="duration_minutes"
                                    aria-label="Lesson Duration in minutes"
                                    min="1"
                                    max="1440"
                                    placeholder="e.g. 45"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono"
                                />
                            </div>
                        </div>

                        {{-- TYPE SPECIFIC INPUT: TEXT CONTENT --}}
                        <div x-show="contentType === 'text'">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Lesson Text Content
                            </label>
                            <textarea
                                name="content"
                                aria-label="Lesson Text Content"
                                rows="5"
                                placeholder="Write the lesson reading material, code examples, steps, or configuration guidelines..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                            ></textarea>
                        </div>

                        {{-- TYPE SPECIFIC INPUT: VIDEO URL --}}
                        <div x-show="contentType === 'video'">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Video Stream URL <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="url"
                                name="video_url"
                                aria-label="Video Stream URL"
                                placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                            />
                        </div>

                        {{-- TYPE SPECIFIC INPUT: RESOURCE URL --}}
                        <div x-show="contentType === 'resource'">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                External Resource URL <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="url"
                                name="resource_url"
                                aria-label="External Resource URL"
                                placeholder="https://drive.google.com/... or https://sandbox.sap.zeblearn.com"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Short Lesson Summary <span class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <textarea
                                name="description"
                                aria-label="Lesson Summary"
                                rows="2"
                                placeholder="Quick outcome or bullet points for the lesson syllabus..."
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Publishing Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="status"
                                aria-label="Lesson Publishing Status"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                            >
                                <option value="published" selected>Published (Visible to Enrolled Students)</option>
                                <option value="draft">Draft (Content in Review)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <x-ui.button
                            type="button"
                            @click="lessonModalOpen = false"
                            variant="secondary"
                            size="sm"
                        >
                            Cancel
                        </x-ui.button>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                        >
                            Create Lesson
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 4: EDIT LESSON --}}
    <div
        x-show="lessonEditModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div
                x-show="lessonEditModalOpen"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                @click="lessonEditModalOpen = false"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="lessonEditModalOpen"
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200"
            >
                <form :action="activeLesson.updateUrl" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="p-6 sm:p-8 space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-heading">Edit Lesson</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Modify content, duration, URL, or publish status</p>
                            </div>
                            <button type="button" @click="lessonEditModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Lesson Title <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                aria-label="Edit Lesson Title"
                                x-model="activeLesson.title"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Content Type <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    name="content_type"
                                    aria-label="Edit Lesson Content Type"
                                    x-model="activeLesson.content_type"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                                >
                                    <option value="text">Text Lesson</option>
                                    <option value="video">Video Lesson</option>
                                    <option value="resource">Resource Link</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Duration (minutes)
                                </label>
                                <input
                                    type="number"
                                    name="duration_minutes"
                                    aria-label="Edit Lesson Duration in minutes"
                                    min="1"
                                    max="1440"
                                    x-model="activeLesson.duration_minutes"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono"
                                />
                            </div>
                        </div>

                        {{-- TYPE SPECIFIC INPUT: TEXT CONTENT --}}
                        <div x-show="activeLesson.content_type === 'text'">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Lesson Text Content
                            </label>
                            <textarea
                                name="content"
                                aria-label="Edit Lesson Text Content"
                                rows="5"
                                x-model="activeLesson.content"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                            ></textarea>
                        </div>

                        {{-- TYPE SPECIFIC INPUT: VIDEO URL --}}
                        <div x-show="activeLesson.content_type === 'video'">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Video Stream URL
                            </label>
                            <input
                                type="url"
                                name="video_url"
                                aria-label="Edit Video Stream URL"
                                x-model="activeLesson.video_url"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                            />
                        </div>

                        {{-- TYPE SPECIFIC INPUT: RESOURCE URL --}}
                        <div x-show="activeLesson.content_type === 'resource'">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                External Resource URL
                            </label>
                            <input
                                type="url"
                                name="resource_url"
                                aria-label="Edit External Resource URL"
                                x-model="activeLesson.resource_url"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Description
                            </label>
                            <textarea
                                name="description"
                                aria-label="Edit Lesson Description"
                                x-model="activeLesson.description"
                                rows="2"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                Publishing Status <span class="text-rose-500">*</span>
                            </label>
                            <select
                                name="status"
                                aria-label="Edit Lesson Publishing Status"
                                x-model="activeLesson.status"
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                            >
                                <option value="published">Published (Visible to Enrolled Students)</option>
                                <option value="draft">Draft (Content in Review)</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <x-ui.button
                            type="button"
                            @click="lessonEditModalOpen = false"
                            variant="secondary"
                            size="sm"
                        >
                            Cancel
                        </x-ui.button>
                        <x-ui.button
                            type="submit"
                            variant="primary"
                            size="sm"
                        >
                            Save Changes
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
