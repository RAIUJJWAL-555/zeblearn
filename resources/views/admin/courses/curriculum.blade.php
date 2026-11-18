@extends('layouts.admin')

@section('title', 'Curriculum & Lessons - ' . $course->name . ' | ZebLearn LMS')
@section('page-title', 'Curriculum')

@section('content')
<div
    class="max-w-7xl mx-auto space-y-6 pb-20"
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

        expandedModules: {{ json_encode($modules->pluck('id')->values()->all()) }},

        toggleModule(id) {
            if (this.expandedModules.includes(id)) {
                this.expandedModules = this.expandedModules.filter(mId => mId !== id);
            } else {
                this.expandedModules.push(id);
            }
        },

        isExpanded(id) {
            return this.expandedModules.includes(id);
        },

        expandAll() {
            this.expandedModules = {{ json_encode($modules->pluck('id')->values()->all()) }};
        },

        collapseAll() {
            this.expandedModules = [];
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
        },

        init() {
            this.$watch('moduleModalOpen || moduleEditModalOpen || lessonModalOpen || lessonEditModalOpen', value => {
                if (value) {
                    document.body.classList.add('overflow-hidden');
                } else {
                    document.body.classList.remove('overflow-hidden');
                }
            });
        }
    }"
    @keydown.escape.window="
        moduleModalOpen = false;
        moduleEditModalOpen = false;
        lessonModalOpen = false;
        lessonEditModalOpen = false;
    "
>
    {{-- A. PAGE HEADER & BREADCRUMBS --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 bg-white p-6 sm:p-7 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div class="space-y-2">
            {{-- Breadcrumbs --}}
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span>/</span>
                <a href="{{ route('admin.courses.edit', $course) }}" class="hover:text-[#FB6221] transition-colors truncate max-w-xs" title="{{ $course->name }}">{{ $course->name }}</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Curriculum</span>
            </nav>

            {{-- Title & Status --}}
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    Course Curriculum
                </h1>

                @if ($course->status === 'available')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Course
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Draft Course
                    </span>
                @endif

                <x-ui.badge variant="primary" size="xs" class="font-mono font-bold uppercase tracking-wider">
                    {{ $course->category_badge }}
                </x-ui.badge>
            </div>

            <p class="text-xs sm:text-sm text-slate-500 max-w-3xl leading-relaxed">
                Structure syllabus for <strong class="text-slate-800 font-semibold">{{ $course->name }}</strong> into progressive learning modules, rich lesson readings, external video streams, and practical resources.
            </p>
        </div>

        {{-- Top Actions --}}
        <div class="flex items-center gap-3 shrink-0 self-start lg:self-center">
            <x-ui.button
                href="{{ route('admin.courses.edit', $course) }}"
                variant="secondary"
                size="md"
                icon="arrow-left"
            >
                Back to Course
            </x-ui.button>

            <x-ui.button
                type="button"
                @click="openAddModule()"
                variant="primary"
                size="md"
                icon="plus"
                class="shadow-sm shadow-[#FB6221]/20 cursor-pointer"
            >
                Add Module
            </x-ui.button>
        </div>
    </div>

    {{-- FLASH MESSAGES & FEEDBACK --}}
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

    {{-- B. CURRICULUM OVERVIEW METRICS (5-CARD COMPACT GRID) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
        {{-- Card 1: Total Modules --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider">Total Modules</span>
                <x-ui.icon name="book" size="sm" class="text-slate-400" />
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    {{ $stats['total_modules'] }}
                </span>
                <span class="text-xs text-slate-500 font-medium">modules</span>
            </div>
            <div class="mt-1 text-[11px] text-slate-500 font-mono">
                {{ $stats['published_modules'] }} live • {{ $stats['draft_modules'] }} draft
            </div>
        </div>

        {{-- Card 2: Total Lessons --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider">Total Lessons</span>
                <x-ui.icon name="academic-cap" size="sm" class="text-slate-400" />
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    {{ $stats['total_lessons'] }}
                </span>
                <span class="text-xs text-slate-500 font-medium">lessons</span>
            </div>
            <div class="mt-1 text-[11px] text-slate-500 font-mono">
                {{ $stats['published_lessons'] }} visible to students
            </div>
        </div>

        {{-- Card 3: Published Modules --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider">Published</span>
                <x-ui.icon name="check-circle" size="sm" class="text-emerald-500" />
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading">
                    {{ $stats['published_modules'] }}
                </span>
                <span class="text-xs text-emerald-700/80 font-medium">live modules</span>
            </div>
            <div class="mt-1 text-[11px] text-emerald-600 font-medium">
                Publicly accessible
            </div>
        </div>

        {{-- Card 4: Draft Modules --}}
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider">Draft Modules</span>
                <x-ui.icon name="alert-circle" size="sm" class="text-amber-500" />
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading">
                    {{ $stats['draft_modules'] }}
                </span>
                <span class="text-xs text-amber-700/80 font-medium">in draft</span>
            </div>
            <div class="mt-1 text-[11px] text-amber-600 font-medium">
                Hidden from students
            </div>
        </div>

        {{-- Card 5: Total Learning Duration --}}
        <div class="col-span-2 md:col-span-1 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider">Total Duration</span>
                <x-ui.icon name="clock" size="sm" class="text-[#FB6221]" />
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">
                    @if ($stats['total_duration'] > 0)
                        {{ round($stats['total_duration'] / 60, 1) }}<span class="text-sm font-normal text-slate-500"> hrs</span>
                    @else
                        0<span class="text-sm font-normal text-slate-500"> mins</span>
                    @endif
                </span>
            </div>
            <div class="mt-1 text-[11px] text-slate-500 font-mono">
                Total runtime across lessons
            </div>
        </div>
    </div>

    {{-- C. MODULE MANAGEMENT INTERFACE --}}
    @if ($modules->isEmpty())
        {{-- E. EMPTY STATE: NO MODULES IN COURSE --}}
        <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-10 sm:p-16 text-center shadow-2xs">
            <div class="w-16 h-16 rounded-2xl bg-orange-50 text-[#FB6221] border border-orange-200/60 mx-auto flex items-center justify-center shadow-xs">
                <x-ui.icon name="book" size="xl" />
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 font-heading mt-5">
                Build Your Course Curriculum
            </h2>
            <p class="text-sm text-slate-500 max-w-md mx-auto mt-2 leading-relaxed">
                Organize learning content into sequential modules, lessons, video streams, and practical resources. Start by creating the first module.
            </p>
            <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                <x-ui.button
                    type="button"
                    @click="openAddModule()"
                    variant="primary"
                    size="md"
                    icon="plus"
                    class="cursor-pointer"
                >
                    Create Your First Module
                </x-ui.button>
            </div>
        </div>
    @else
        {{-- CURRICULUM ACCORDION CONTROLS BAR --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-1">
            <div class="flex items-center gap-2">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-700 font-mono">
                    Curriculum Modules ({{ count($modules) }})
                </h2>
                <span class="text-xs text-slate-400 font-mono">• Reorder using arrow controls</span>
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="expandAll()"
                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white border border-slate-200 transition-colors cursor-pointer"
                >
                    Expand All
                </button>
                <button
                    type="button"
                    @click="collapseAll()"
                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-white border border-slate-200 transition-colors cursor-pointer"
                >
                    Collapse All
                </button>
            </div>
        </div>

        {{-- MODULES STACK --}}
        <div class="space-y-4">
            @foreach ($modules as $moduleIndex => $module)
                @php
                    $moduleMinutes = $module->lessons->sum('duration_minutes');
                    $moduleDuration = null;
                    if ($moduleMinutes > 0) {
                        $hrs = floor($moduleMinutes / 60);
                        $rem = $moduleMinutes % 60;
                        $moduleDuration = $hrs > 0 ? ($rem > 0 ? "{$hrs}h {$rem}m" : "{$hrs}h") : "{$rem}m";
                    }
                @endphp

                <div
                    class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs transition-all overflow-hidden"
                    x-data="{
                        expanded: isExpanded({{ $module->id }})
                    }"
                    :class="{ 'ring-2 ring-[#FB6221]/15 border-[#FB6221]/40': isExpanded({{ $module->id }}) }"
                >
                    {{-- MODULE ACCORDION HEADER --}}
                    <div
                        class="p-4 sm:p-5 bg-slate-50/70 border-b border-slate-200/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4 transition-colors"
                    >
                        {{-- Left side: Reorder + Module Info --}}
                        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                            {{-- Reorder Controls (Up / Down) --}}
                            <div class="flex flex-col items-center gap-0.5 shrink-0 pt-0.5">
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
                                        <button type="submit" class="p-1 rounded text-slate-400 hover:text-slate-900 hover:bg-slate-200/70 transition-colors cursor-pointer" title="Move Module Up">
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
                                        <button type="submit" class="p-1 rounded text-slate-400 hover:text-slate-900 hover:bg-slate-200/70 transition-colors cursor-pointer" title="Move Module Down">
                                            <x-ui.icon name="chevron-down" size="xs" />
                                        </button>
                                    </form>
                                @else
                                    <span class="p-1 text-slate-200 cursor-not-allowed">
                                        <x-ui.icon name="chevron-down" size="xs" />
                                    </span>
                                @endif
                            </div>

                            {{-- Module Main Information --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2.5 flex-wrap">
                                    {{-- Module Number --}}
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-slate-200/80 text-slate-700">
                                        MODULE {{ str_pad($moduleIndex + 1, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    {{-- Publishing Status Badge --}}
                                    @if ($module->isPublished())
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                            Draft
                                        </span>
                                    @endif

                                    {{-- Lesson Count --}}
                                    <span class="text-xs text-slate-500 font-mono">
                                        • {{ $module->lessons->count() }} {{ \Illuminate\Support\Str::plural('Lesson', $module->lessons->count()) }}
                                    </span>

                                    {{-- Duration if Available --}}
                                    @if ($moduleDuration)
                                        <span class="inline-flex items-center gap-1 text-xs text-slate-500 font-mono">
                                            • <x-ui.icon name="clock" size="xs" class="text-slate-400" /> {{ $moduleDuration }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Title with Expand/Collapse Click Trigger --}}
                                <button
                                    type="button"
                                    @click="toggleModule({{ $module->id }})"
                                    class="text-left group mt-1.5 flex items-center gap-2 cursor-pointer w-full"
                                >
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 font-heading group-hover:text-[#FB6221] transition-colors">
                                        {{ $module->title }}
                                    </h3>
                                    <x-ui.icon
                                        name="chevron-down"
                                        size="xs"
                                        class="text-slate-400 group-hover:text-[#FB6221] transition-transform duration-200 shrink-0"
                                        x-bind:class="{ 'rotate-180': isExpanded({{ $module->id }}) }"
                                    />
                                </button>

                                @if ($module->description)
                                    <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl leading-relaxed">
                                        {{ $module->description }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        {{-- Right side: Module Actions --}}
                        <div class="flex items-center gap-2 self-end lg:self-center shrink-0 pt-2 lg:pt-0">
                            {{-- Add Lesson Button --}}
                            <button
                                type="button"
                                @click="openAddLesson({{ json_encode($module) }}, '{{ route('admin.courses.lessons.store', [$course, $module]) }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition-colors shadow-2xs cursor-pointer"
                                title="Add a new lesson to this module"
                            >
                                <x-ui.icon name="plus" size="xs" />
                                <span>Add Lesson</span>
                            </button>

                            {{-- Quick Toggle Publish / Unpublish --}}
                            <form action="{{ route('admin.courses.modules.update', [$course, $module]) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $module->title }}" />
                                <input type="hidden" name="description" value="{{ $module->description }}" />
                                <input type="hidden" name="status" value="{{ $module->isPublished() ? 'draft' : 'published' }}" />
                                <button
                                    type="submit"
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-medium border transition-colors cursor-pointer {{ $module->isPublished() ? 'border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"
                                    title="{{ $module->isPublished() ? 'Unpublish module (hide from students)' : 'Publish module (make visible to students)' }}"
                                >
                                    {{ $module->isPublished() ? 'Unpublish' : 'Publish' }}
                                </button>
                            </form>

                            {{-- Edit Module --}}
                            <button
                                type="button"
                                @click="openEditModule({{ json_encode($module) }}, '{{ route('admin.courses.modules.update', [$course, $module]) }}')"
                                class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-200/70 transition-colors cursor-pointer"
                                title="Edit Module Details"
                            >
                                <x-ui.icon name="pencil" size="xs" />
                            </button>

                            {{-- Delete Module (Safe Check) --}}
                            <form
                                action="{{ route('admin.courses.modules.destroy', [$course, $module]) }}"
                                method="POST"
                                onsubmit="return confirm('{{ $module->lessons->count() > 0 ? "WARNING: This module contains {$module->lessons->count()} lesson(s). Are you sure you want to delete this module and all nested lessons?" : "Delete module \'{$module->title}\'?" }}')"
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

                    {{-- D. LESSON MANAGEMENT INTERFACE (COLLAPSIBLE BODY) --}}
                    <div x-show="isExpanded({{ $module->id }})" x-transition.opacity>
                        @if ($module->lessons->isEmpty())
                            {{-- E. EMPTY STATE: NO LESSONS IN MODULE --}}
                            <div class="p-8 sm:p-10 text-center bg-slate-50/40">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                                    <x-ui.icon name="academic-cap" size="sm" />
                                </div>
                                <h4 class="text-sm font-semibold text-slate-700 mt-2">No lessons in this module yet</h4>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                                    Add your first text lesson, video streaming URL, or external practical sandbox to begin building this module.
                                </p>
                                <div class="mt-4">
                                    <button
                                        type="button"
                                        @click="openAddLesson({{ json_encode($module) }}, '{{ route('admin.courses.lessons.store', [$course, $module]) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#FB6221] text-white hover:bg-[#e05316] transition-colors shadow-2xs cursor-pointer"
                                    >
                                        <x-ui.icon name="plus" size="xs" />
                                        <span>Add First Lesson</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            {{-- LESSONS LIST --}}
                            <div class="divide-y divide-slate-100 bg-white">
                                @foreach ($module->lessons as $lessonIndex => $lesson)
                                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/60 transition-colors group">
                                        {{-- Lesson Reorder & Content --}}
                                        <div class="flex items-start gap-3 sm:gap-3.5 flex-1 min-w-0">
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

                                            {{-- Content Type Distinctive Icon --}}
                                            <div class="shrink-0 mt-0.5">
                                                @if ($lesson->isVideo())
                                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200/70" title="Video Lesson">
                                                        <x-ui.icon name="eye" size="xs" />
                                                    </div>
                                                @elseif ($lesson->isResource())
                                                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-200/70" title="Resource Link">
                                                        <x-ui.icon name="external-link" size="xs" />
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-200/70" title="Text Lesson">
                                                        <x-ui.icon name="book" size="xs" />
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Lesson Details --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    {{-- Sequence --}}
                                                    <span class="text-xs font-mono font-semibold text-slate-400">
                                                        {{ $moduleIndex + 1 }}.{{ $lessonIndex + 1 }}
                                                    </span>

                                                    {{-- Title --}}
                                                    <h4 class="font-semibold text-sm text-slate-900 font-heading">
                                                        {{ $lesson->title }}
                                                    </h4>

                                                    {{-- Content Type Badge --}}
                                                    @if ($lesson->isVideo())
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200/60">
                                                            Video Lesson
                                                        </span>
                                                    @elseif ($lesson->isResource())
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200/60">
                                                            Resource Link
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                            Text Lesson
                                                        </span>
                                                    @endif

                                                    {{-- Publishing Status --}}
                                                    @if ($lesson->isPublished())
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-emerald-600">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            Published
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-400">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                                            Draft
                                                        </span>
                                                    @endif

                                                    {{-- Duration --}}
                                                    @if ($lesson->duration_minutes)
                                                        <span class="text-[11px] text-slate-400 font-mono inline-flex items-center gap-1">
                                                            • <x-ui.icon name="clock" size="xs" class="text-slate-300" /> {{ $lesson->formatted_duration }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if ($lesson->description)
                                                    <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                                        {{ $lesson->description }}
                                                    </p>
                                                @endif

                                                {{-- External URL Preview if Video or Resource --}}
                                                @if ($lesson->isVideo() && $lesson->video_url)
                                                    <div class="mt-1 flex items-center gap-1.5">
                                                        <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:text-blue-800 hover:underline max-w-md truncate font-mono">
                                                            <span>{{ $lesson->video_url }}</span>
                                                            <x-ui.icon name="external-link" size="xs" />
                                                        </a>
                                                    </div>
                                                @elseif ($lesson->isResource() && $lesson->resource_url)
                                                    <div class="mt-1 flex items-center gap-1.5">
                                                        <a href="{{ $lesson->resource_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-[11px] text-purple-600 hover:text-purple-800 hover:underline max-w-md truncate font-mono">
                                                            <span>{{ $lesson->resource_url }}</span>
                                                            <x-ui.icon name="external-link" size="xs" />
                                                        </a>
                                                    </div>
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
                                                title="Edit Lesson Details"
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
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- =========================================================================
         5. MODAL & OVERLAY SYSTEM — 100% OPAQUE, ZERO BLUR GLITCH, Z-INDEX HARDENED
         ========================================================================= --}}

    {{-- MODAL 1: ADD MODULE --}}
    <template x-teleport="body">
        <div
            x-show="moduleModalOpen"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="add-module-modal-title"
        >
            {{-- Solid Dark Backdrop (Opaque, High Z-Index, No Blur to avoid GPU blur glitches) --}}
            <div
                x-show="moduleModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 transition-opacity"
                @click="moduleModalOpen = false"
                aria-hidden="true"
            ></div>

            {{-- Centering Container --}}
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                {{-- Modal Dialog Panel --}}
                <div
                    x-show="moduleModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-3 sm:scale-98"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-3 sm:scale-98"
                    class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-200/90 z-10"
                    @click.stop
                    x-data="{ submitting: false }"
                >
                    <form
                        action="{{ route('admin.courses.modules.store', $course) }}"
                        method="POST"
                        @submit="submitting = true"
                    >
                        @csrf
                        {{-- Modal Header --}}
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 id="add-module-modal-title" class="text-lg font-bold text-slate-900 font-heading">
                                    Add Curriculum Module
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Create a sequential learning module for {{ $course->name }}
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="moduleModalOpen = false"
                                class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                aria-label="Close dialog"
                            >
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                            <div>
                                <label for="add_module_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Module Title <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="add_module_title"
                                    name="title"
                                    required
                                    placeholder="e.g. Enterprise General Ledger & Document Splitting"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                />
                            </div>

                            <div>
                                <label for="add_module_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Description <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <textarea
                                    id="add_module_description"
                                    name="description"
                                    rows="3"
                                    placeholder="Summary of core competencies, topics, and objectives covered in this module..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                ></textarea>
                            </div>

                            <div>
                                <label for="add_module_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Publishing Status <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="add_module_status"
                                    name="status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                                >
                                    <option value="published" selected>Published (Visible to Enrolled Students)</option>
                                    <option value="draft">Draft (Curriculum in Preparation)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <x-ui.button
                                type="button"
                                @click="moduleModalOpen = false"
                                variant="secondary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                Cancel
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                <span x-show="!submitting">Create Module</span>
                                <span x-show="submitting" class="inline-flex items-center gap-1.5">
                                    <x-ui.icon name="spinner" size="xs" class="animate-spin" /> Saving...
                                </span>
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL 2: EDIT MODULE --}}
    <template x-teleport="body">
        <div
            x-show="moduleEditModalOpen"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-module-modal-title"
        >
            {{-- Solid Dark Backdrop --}}
            <div
                x-show="moduleEditModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 transition-opacity"
                @click="moduleEditModalOpen = false"
                aria-hidden="true"
            ></div>

            {{-- Centering Container --}}
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                {{-- Modal Dialog Panel --}}
                <div
                    x-show="moduleEditModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-3 sm:scale-98"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-3 sm:scale-98"
                    class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-200/90 z-10"
                    @click.stop
                    x-data="{ submitting: false }"
                >
                    <form
                        :action="activeModule.updateUrl"
                        method="POST"
                        @submit="submitting = true"
                    >
                        @csrf
                        @method('PUT')
                        {{-- Modal Header --}}
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 id="edit-module-modal-title" class="text-lg font-bold text-slate-900 font-heading">
                                    Edit Curriculum Module
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Update title, description, or visibility for this module
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="moduleEditModalOpen = false"
                                class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                aria-label="Close dialog"
                            >
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                            <div>
                                <label for="edit_module_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Module Title <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="edit_module_title"
                                    name="title"
                                    x-model="activeModule.title"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                />
                            </div>

                            <div>
                                <label for="edit_module_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Description <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <textarea
                                    id="edit_module_description"
                                    name="description"
                                    rows="3"
                                    x-model="activeModule.description"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                ></textarea>
                            </div>

                            <div>
                                <label for="edit_module_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Publishing Status <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="edit_module_status"
                                    name="status"
                                    x-model="activeModule.status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                                >
                                    <option value="published">Published (Visible to Enrolled Students)</option>
                                    <option value="draft">Draft (Curriculum in Preparation)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <x-ui.button
                                type="button"
                                @click="moduleEditModalOpen = false"
                                variant="secondary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                Cancel
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                <span x-show="!submitting">Save Changes</span>
                                <span x-show="submitting" class="inline-flex items-center gap-1.5">
                                    <x-ui.icon name="spinner" size="xs" class="animate-spin" /> Saving...
                                </span>
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL 3: ADD LESSON --}}
    <template x-teleport="body">
        <div
            x-show="lessonModalOpen"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="add-lesson-modal-title"
        >
            {{-- Solid Dark Backdrop --}}
            <div
                x-show="lessonModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 transition-opacity"
                @click="lessonModalOpen = false"
                aria-hidden="true"
            ></div>

            {{-- Centering Container --}}
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                {{-- Modal Dialog Panel --}}
                <div
                    x-show="lessonModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-3 sm:scale-98"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-3 sm:scale-98"
                    x-data="{ contentType: 'text', submitting: false }"
                    class="relative w-full max-w-xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-200/90 z-10"
                    @click.stop
                >
                    <form
                        :action="activeModuleForLesson.storeUrl"
                        method="POST"
                        @submit="submitting = true"
                    >
                        @csrf
                        {{-- Modal Header --}}
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 id="add-lesson-modal-title" class="text-lg font-bold text-slate-900 font-heading">
                                    Add Lesson
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Adding to <span class="font-bold text-slate-800" x-text="activeModuleForLesson.title"></span>
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="lessonModalOpen = false"
                                class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                aria-label="Close dialog"
                            >
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                            <div>
                                <label for="add_lesson_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Lesson Title <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="add_lesson_title"
                                    name="title"
                                    required
                                    placeholder="e.g. Configuring Zero-Balance Clearing Rules"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="add_lesson_content_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                        Content Type <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="add_lesson_content_type"
                                        name="content_type"
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
                                    <label for="add_lesson_duration" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                        Duration <span class="text-slate-400 font-normal">(minutes)</span>
                                    </label>
                                    <input
                                        type="number"
                                        id="add_lesson_duration"
                                        name="duration_minutes"
                                        min="1"
                                        max="1440"
                                        placeholder="e.g. 45"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono"
                                    />
                                </div>
                            </div>

                            {{-- DYNAMIC FIELD: TEXT CONTENT --}}
                            <div x-show="contentType === 'text'" x-transition.opacity>
                                <label for="add_lesson_content" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Lesson Text Content
                                </label>
                                <textarea
                                    id="add_lesson_content"
                                    name="content"
                                    rows="5"
                                    placeholder="Write the lesson reading material, code examples, steps, or configuration guidelines..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                                ></textarea>
                                <p class="text-[11px] text-slate-400 mt-1 font-mono">
                                    Tip: Markdown format, code blocks, and bullet points are fully supported.
                                </p>
                            </div>

                            {{-- DYNAMIC FIELD: VIDEO STREAM URL --}}
                            <div x-show="contentType === 'video'" x-transition.opacity>
                                <label for="add_lesson_video_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Video Stream URL <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="url"
                                    id="add_lesson_video_url"
                                    name="video_url"
                                    placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                                />
                                <p class="text-[11px] text-slate-400 mt-1 font-mono">
                                    Direct video link (YouTube, Vimeo, AWS CloudFront, or hosted MP4).
                                </p>
                            </div>

                            {{-- DYNAMIC FIELD: RESOURCE LINK --}}
                            <div x-show="contentType === 'resource'" x-transition.opacity>
                                <label for="add_lesson_resource_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    External Resource URL <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="url"
                                    id="add_lesson_resource_url"
                                    name="resource_url"
                                    placeholder="https://drive.google.com/... or https://sandbox.sap.zeblearn.com"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                                />
                                <p class="text-[11px] text-slate-400 mt-1 font-mono">
                                    Link to shared Google Drive materials, GitHub repositories, or live sandbox servers.
                                </p>
                            </div>

                            <div>
                                <label for="add_lesson_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Short Lesson Summary <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <textarea
                                    id="add_lesson_description"
                                    name="description"
                                    rows="2"
                                    placeholder="Quick outcome or bullet points for the lesson syllabus..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                ></textarea>
                            </div>

                            <div>
                                <label for="add_lesson_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Publishing Status <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="add_lesson_status"
                                    name="status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                                >
                                    <option value="published" selected>Published (Visible to Enrolled Students)</option>
                                    <option value="draft">Draft (Content in Review)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <x-ui.button
                                type="button"
                                @click="lessonModalOpen = false"
                                variant="secondary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                Cancel
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                <span x-show="!submitting">Create Lesson</span>
                                <span x-show="submitting" class="inline-flex items-center gap-1.5">
                                    <x-ui.icon name="spinner" size="xs" class="animate-spin" /> Saving...
                                </span>
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL 4: EDIT LESSON --}}
    <template x-teleport="body">
        <div
            x-show="lessonEditModalOpen"
            x-cloak
            class="fixed inset-0 z-[100] overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-lesson-modal-title"
        >
            {{-- Solid Dark Backdrop --}}
            <div
                x-show="lessonEditModalOpen"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 transition-opacity"
                @click="lessonEditModalOpen = false"
                aria-hidden="true"
            ></div>

            {{-- Centering Container --}}
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                {{-- Modal Dialog Panel --}}
                <div
                    x-show="lessonEditModalOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-3 sm:scale-98"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-3 sm:scale-98"
                    class="relative w-full max-w-xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-slate-200/90 z-10"
                    @click.stop
                    x-data="{ submitting: false }"
                >
                    <form
                        :action="activeLesson.updateUrl"
                        method="POST"
                        @submit="submitting = true"
                    >
                        @csrf
                        @method('PUT')
                        {{-- Modal Header --}}
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 id="edit-lesson-modal-title" class="text-lg font-bold text-slate-900 font-heading">
                                    Edit Lesson
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Modify content, duration, URL, or publishing status
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="lessonEditModalOpen = false"
                                class="p-1 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                aria-label="Close dialog"
                            >
                                <x-ui.icon name="close" size="sm" />
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                            <div>
                                <label for="edit_lesson_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Lesson Title <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="edit_lesson_title"
                                    name="title"
                                    x-model="activeLesson.title"
                                    required
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_lesson_content_type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                        Content Type <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        id="edit_lesson_content_type"
                                        name="content_type"
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
                                    <label for="edit_lesson_duration" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                        Duration (minutes)
                                    </label>
                                    <input
                                        type="number"
                                        id="edit_lesson_duration"
                                        name="duration_minutes"
                                        min="1"
                                        max="1440"
                                        x-model="activeLesson.duration_minutes"
                                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono"
                                    />
                                </div>
                            </div>

                            {{-- DYNAMIC FIELD: TEXT CONTENT --}}
                            <div x-show="activeLesson.content_type === 'text'" x-transition.opacity>
                                <label for="edit_lesson_content" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Lesson Text Content
                                </label>
                                <textarea
                                    id="edit_lesson_content"
                                    name="content"
                                    rows="5"
                                    x-model="activeLesson.content"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                                ></textarea>
                            </div>

                            {{-- DYNAMIC FIELD: VIDEO STREAM URL --}}
                            <div x-show="activeLesson.content_type === 'video'" x-transition.opacity>
                                <label for="edit_lesson_video_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Video Stream URL
                                </label>
                                <input
                                    type="url"
                                    id="edit_lesson_video_url"
                                    name="video_url"
                                    x-model="activeLesson.video_url"
                                    placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                                />
                            </div>

                            {{-- DYNAMIC FIELD: RESOURCE LINK --}}
                            <div x-show="activeLesson.content_type === 'resource'" x-transition.opacity>
                                <label for="edit_lesson_resource_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    External Resource URL
                                </label>
                                <input
                                    type="url"
                                    id="edit_lesson_resource_url"
                                    name="resource_url"
                                    x-model="activeLesson.resource_url"
                                    placeholder="https://drive.google.com/... or https://sandbox.sap.zeblearn.com"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-mono text-xs"
                                />
                            </div>

                            <div>
                                <label for="edit_lesson_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Description <span class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <textarea
                                    id="edit_lesson_description"
                                    name="description"
                                    rows="2"
                                    x-model="activeLesson.description"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs"
                                ></textarea>
                            </div>

                            <div>
                                <label for="edit_lesson_status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-2">
                                    Publishing Status <span class="text-rose-500">*</span>
                                </label>
                                <select
                                    id="edit_lesson_status"
                                    name="status"
                                    x-model="activeLesson.status"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-xs font-medium cursor-pointer"
                                >
                                    <option value="published">Published (Visible to Enrolled Students)</option>
                                    <option value="draft">Draft (Content in Review)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-100">
                            <x-ui.button
                                type="button"
                                @click="lessonEditModalOpen = false"
                                variant="secondary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                Cancel
                            </x-ui.button>
                            <x-ui.button
                                type="submit"
                                variant="primary"
                                size="sm"
                                x-bind:disabled="submitting"
                            >
                                <span x-show="!submitting">Save Changes</span>
                                <span x-show="submitting" class="inline-flex items-center gap-1.5">
                                    <x-ui.icon name="spinner" size="xs" class="animate-spin" /> Saving...
                                </span>
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
