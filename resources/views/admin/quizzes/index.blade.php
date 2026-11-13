@extends('layouts.admin')

@section('title', 'Quiz & Assessment Management | ZebLearn LMS')
@section('page-title', 'Quizzes')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Assessments</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Quiz &amp; MCQ Assessments
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Configure MCQ evaluations, assign to courses, modules, or lessons, and review student attempt scores.
            </p>
        </div>

        <div class="shrink-0">
            <x-ui.button
                href="{{ route('admin.quizzes.create') }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                Create New Quiz
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

    {{-- ANALYTICS KPI CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Quizzes</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $totalQuizzes }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Published Active</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading mt-1 block">{{ $publishedQuizzes }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Attempts</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $totalAttempts }}</span>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Average Score</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $avgScore }}%</span>
        </div>

        <div class="col-span-2 lg:col-span-1 bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Pass Rate</span>
            <span class="text-2xl sm:text-3xl font-black text-[#FB6221] font-heading mt-1 block">{{ $passRate }}%</span>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <x-ui.card padding="sm">
        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            {{-- Search --}}
            <div class="sm:col-span-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-ui.icon name="search" size="sm" />
                </div>
                <input
                    type="text"
                    name="search"
                    aria-label="Search quizzes by title"
                    value="{{ request('search') }}"
                    placeholder="Search quiz title..."
                    class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/80 pl-10 pr-3.5 py-2.5 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 outline-hidden transition-all"
                >
            </div>

            {{-- Course Filter --}}
            <div class="sm:col-span-3">
                <select
                    name="course_id"
                    aria-label="Filter quizzes by course"
                    class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 outline-hidden text-slate-700 cursor-pointer"
                >
                    <option value="">All Courses</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" {{ (string) request('course_id') === (string) $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Context Filter --}}
            <div class="sm:col-span-2">
                <select
                    name="context"
                    aria-label="Filter quizzes by context"
                    class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 outline-hidden text-slate-700 cursor-pointer"
                >
                    <option value="">All Contexts</option>
                    <option value="course" {{ request('context') === 'course' ? 'selected' : '' }}>Course Level</option>
                    <option value="module" {{ request('context') === 'module' ? 'selected' : '' }}>Module Level</option>
                    <option value="lesson" {{ request('context') === 'lesson' ? 'selected' : '' }}>Lesson Level</option>
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="sm:col-span-2">
                <select
                    name="status"
                    aria-label="Filter quizzes by status"
                    class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 outline-hidden text-slate-700 cursor-pointer"
                >
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="sm:col-span-1 flex items-center gap-2">
                <x-ui.button
                    type="submit"
                    variant="dark"
                    size="sm"
                    fullWidth
                >
                    Filter
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- QUIZZES TABLE --}}
    <x-ui.card padding="none">
        @if ($quizzes->isEmpty())
            <div class="p-10 text-center">
                <x-ui.empty-state
                    title="No Quizzes Found"
                    description="No quizzes match your selected filter criteria. Create your first MCQ assessment to get started."
                    icon="book"
                    :bordered="false"
                >
                    <x-ui.button href="{{ route('admin.quizzes.create') }}" variant="primary" size="sm" icon="plus">
                        Create Quiz
                    </x-ui.button>
                </x-ui.empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/75 border-b border-slate-200/80 text-[11px] font-mono uppercase tracking-wider text-slate-400">
                            <th class="py-3.5 px-4 sm:px-6">Quiz Title &amp; Scope</th>
                            <th class="py-3.5 px-4">Questions</th>
                            <th class="py-3.5 px-4">Marks &amp; Pass %</th>
                            <th class="py-3.5 px-4">Rules</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @foreach ($quizzes as $quiz)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            @if ($quiz->isLessonLevel())
                                                <x-ui.badge variant="primary" size="xs" class="font-mono font-bold uppercase">
                                                    {{ $quiz->context_label }}
                                                </x-ui.badge>
                                            @elseif ($quiz->isModuleLevel())
                                                <x-ui.badge variant="info" size="xs" class="font-mono font-bold uppercase">
                                                    {{ $quiz->context_label }}
                                                </x-ui.badge>
                                            @else
                                                <x-ui.badge variant="neutral" size="xs" class="font-mono font-bold uppercase">
                                                    {{ $quiz->context_label }}
                                                </x-ui.badge>
                                            @endif
                                            <span class="text-[11px] font-mono text-slate-500 truncate max-w-xs">
                                                {{ $quiz->course->name }}
                                            </span>
                                        </div>
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="font-bold text-sm text-slate-900 hover:text-[#FB6221] transition-colors block">
                                            {{ $quiz->title }}
                                        </a>
                                        @if ($quiz->isLessonLevel())
                                            <p class="text-[11px] font-mono text-slate-400 truncate">
                                                Attached to lesson: {{ $quiz->lesson?->title }}
                                            </p>
                                        @elseif ($quiz->isModuleLevel())
                                            <p class="text-[11px] font-mono text-slate-400 truncate">
                                                Attached to module: {{ $quiz->module?->title }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-4 px-4 font-mono">
                                    <span class="font-bold text-slate-800 text-sm">{{ $quiz->questions_count }}</span>
                                    <span class="text-slate-400 text-[10px] block">Questions</span>
                                </td>

                                <td class="py-4 px-4 font-mono">
                                    <div class="text-slate-800">
                                        <strong>{{ $quiz->total_marks }}</strong> Total Marks
                                    </div>
                                    <div class="text-slate-400 text-[11px]">
                                        Pass: <strong class="text-slate-700">{{ $quiz->passing_percentage }}%</strong>
                                    </div>
                                </td>

                                <td class="py-4 px-4 text-xs font-mono text-slate-500 space-y-0.5">
                                    <div>Time: <strong class="text-slate-700">{{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes.' min' : 'No Limit' }}</strong></div>
                                    <div>Attempts: <strong class="text-slate-700">{{ $quiz->attempts_allowed }} max</strong></div>
                                    <div>Attempts Taken: <strong class="text-[#FB6221]">{{ $quiz->attempts_count }}</strong></div>
                                </td>

                                <td class="py-4 px-4">
                                    @if ($quiz->isPublished())
                                        <x-ui.badge variant="success" size="xs" dot>
                                            Published
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="xs" dot>
                                            Draft
                                        </x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-4 px-4 sm:px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            href="{{ route('admin.quizzes.show', $quiz) }}"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            title="Manage Questions & Attempts"
                                        >
                                            <x-ui.icon name="eye" size="xs" />
                                        </a>

                                        <a
                                            href="{{ route('admin.quizzes.edit', $quiz) }}"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                            title="Edit Settings"
                                        >
                                            <x-ui.icon name="pencil" size="xs" />
                                        </a>

                                        <form method="POST" action="{{ route('admin.quizzes.toggle-status', $quiz) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="p-1.5 rounded-lg {{ $quiz->isPublished() ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition-colors cursor-pointer"
                                                title="{{ $quiz->isPublished() ? 'Unpublish to Draft' : 'Publish Assessment' }}"
                                            >
                                                @if ($quiz->isPublished())
                                                    <x-ui.icon name="eye-off" size="xs" />
                                                @else
                                                    <x-ui.icon name="check" size="xs" />
                                                @endif
                                            </button>
                                        </form>

                                        @if ($quiz->attempts_count === 0)
                                            <form
                                                method="POST"
                                                action="{{ route('admin.quizzes.destroy', $quiz) }}"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to permanently delete this quiz?');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors cursor-pointer"
                                                    title="Delete Quiz"
                                                >
                                                    <x-ui.icon name="trash" size="xs" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($quizzes->hasPages())
                <div class="p-4 sm:p-5 border-t border-slate-100 bg-slate-50/50">
                    {{ $quizzes->links() }}
                </div>
            @endif
        @endif
    </x-ui.card>
</div>
@endsection
