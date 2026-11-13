@extends('layouts.admin')

@section('title', 'Create New Assignment | ZebLearn LMS')
@section('page-title', 'Create Assignment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div>
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.assignments.index') }}" class="hover:text-[#FB6221] transition-colors">Assignments</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">New Assignment</span>
        </nav>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
            Create Course Assignment
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Define assignment instructions, scoring threshold, file upload allowances, and submission deadlines.
        </p>
    </div>

    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <strong class="block font-bold">Please correct the following errors:</strong>
            <ul class="list-disc list-inside mt-1 space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- CREATE FORM --}}
    <form method="POST" action="{{ route('admin.assignments.store') }}" class="space-y-6">
        @csrf

        {{-- CONTEXT & TARGET --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                1. Assignment Context &amp; Placement
            </h2>

            <div
                class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                x-data="{
                    courses: {{ $courses->toJson() }},
                    selectedCourseId: '{{ old('course_id', $preselectedCourseId) }}',
                    selectedModuleId: '{{ old('course_module_id', $preselectedModuleId) }}',
                    get modules() {
                        const c = this.courses.find(item => item.id == this.selectedCourseId);
                        return c ? (c.modules || []) : [];
                    },
                    get lessons() {
                        const m = this.modules.find(item => item.id == this.selectedModuleId);
                        return m ? (m.lessons || []) : [];
                    }
                }"
            >
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Course <span class="text-rose-600">*</span>
                    </label>
                    <x-ui.select name="course_id" x-model="selectedCourseId" required>
                        <option value="">-- Select Course --</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}" @selected(old('course_id', $preselectedCourseId) == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Module <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <x-ui.select name="course_module_id" x-model="selectedModuleId">
                        <option value="">-- Course-Level (No Module) --</option>
                        <template x-for="m in modules" :key="m.id">
                            <option :value="m.id" x-text="m.title" :selected="m.id == selectedModuleId"></option>
                        </template>
                    </x-ui.select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Lesson <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <x-ui.select name="lesson_id">
                        <option value="">-- None (Module / Course Level) --</option>
                        <template x-for="l in lessons" :key="l.id">
                            <option :value="l.id" x-text="l.title" :selected="l.id == '{{ old('lesson_id', $preselectedLessonId) }}'"></option>
                        </template>
                    </x-ui.select>
                </div>
            </div>
        </x-ui.card>

        {{-- BASIC INFORMATION --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                2. Details &amp; Instructions
            </h2>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Assignment Title <span class="text-rose-600">*</span>
                </label>
                <x-ui.input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="e.g. Enterprise Ledger Reconciliation Project"
                    required
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Brief Description <span class="text-slate-400 font-normal">(Shown on course overview)</span>
                </label>
                <x-ui.textarea
                    name="description"
                    rows="2"
                    placeholder="A concise summary of what the assignment requires..."
                >{{ old('description') }}</x-ui.textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Detailed Instructions &amp; Requirements <span class="text-slate-400 font-normal">(Shown on submission page)</span>
                </label>
                <x-ui.textarea
                    name="instructions"
                    rows="6"
                    placeholder="Provide full instructions, evaluation criteria, steps, and formatting guidelines..."
                >{{ old('instructions') }}</x-ui.textarea>
            </div>
        </x-ui.card>

        {{-- GRADING & SCORING --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                3. Grading &amp; Deadlines
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Maximum Marks <span class="text-rose-600">*</span>
                    </label>
                    <x-ui.input
                        type="number"
                        name="max_marks"
                        min="1"
                        max="1000"
                        value="{{ old('max_marks', 100) }}"
                        required
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Passing Marks <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <x-ui.input
                        type="number"
                        name="passing_marks"
                        min="0"
                        max="1000"
                        value="{{ old('passing_marks', 60) }}"
                        placeholder="e.g. 60"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Start Date &amp; Time
                    </label>
                    <x-ui.input
                        type="datetime-local"
                        name="start_at"
                        value="{{ old('start_at') }}"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Due Date &amp; Time
                    </label>
                    <x-ui.input
                        type="datetime-local"
                        name="due_at"
                        value="{{ old('due_at') }}"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div class="flex items-start gap-2.5">
                    <input
                        type="checkbox"
                        id="allow_late_submissions"
                        name="allow_late_submissions"
                        value="1"
                        @checked(old('allow_late_submissions'))
                        class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221] mt-0.5"
                    />
                    <div>
                        <label for="allow_late_submissions" class="text-xs font-semibold text-slate-900 block cursor-pointer">
                            Allow Late Submissions
                        </label>
                        <span class="text-[11px] text-slate-500">
                            Submissions after the due date will be accepted and marked as "Late".
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Allowed Submission Attempts <span class="text-rose-600">*</span>
                    </label>
                    <x-ui.input
                        type="number"
                        name="max_attempts"
                        min="1"
                        max="100"
                        value="{{ old('max_attempts', 1) }}"
                        required
                    />
                </div>
            </div>
        </x-ui.card>

        {{-- SUBMISSION FORMAT & FILES --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                4. Submission Formats &amp; File Constraints
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-start gap-2.5">
                    <input
                        type="checkbox"
                        id="allow_text_submission"
                        name="allow_text_submission"
                        value="1"
                        @checked(old('allow_text_submission', true))
                        class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221] mt-0.5"
                    />
                    <div>
                        <label for="allow_text_submission" class="text-xs font-semibold text-slate-900 block cursor-pointer">
                            Allow Written Text Responses
                        </label>
                        <span class="text-[11px] text-slate-500">
                            Students can type direct answers into an inline text editor.
                        </span>
                    </div>
                </div>

                <div class="flex items-start gap-2.5">
                    <input
                        type="checkbox"
                        id="allow_file_submission"
                        name="allow_file_submission"
                        value="1"
                        @checked(old('allow_file_submission', true))
                        class="rounded border-slate-300 text-[#FB6221] focus:ring-[#FB6221] mt-0.5"
                    />
                    <div>
                        <label for="allow_file_submission" class="text-xs font-semibold text-slate-900 block cursor-pointer">
                            Allow Document / File Uploads
                        </label>
                        <span class="text-[11px] text-slate-500">
                            Students can attach files (PDF, ZIP, DOCX, etc.).
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Allowed File Extensions <span class="text-slate-400 font-normal">(Comma-separated)</span>
                    </label>
                    <x-ui.input
                        type="text"
                        name="allowed_file_types"
                        value="{{ old('allowed_file_types', 'pdf,doc,docx,zip,txt') }}"
                        placeholder="pdf,doc,docx,zip,txt"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Max File Size (MB per file)
                    </label>
                    <x-ui.input
                        type="number"
                        name="max_file_size_mb"
                        min="1"
                        max="100"
                        value="{{ old('max_file_size_mb', 10) }}"
                    />
                </div>
            </div>
        </x-ui.card>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <x-ui.button href="{{ route('admin.assignments.index') }}" variant="ghost" size="md">
                Cancel
            </x-ui.button>
            <x-ui.button type="submit" variant="primary" size="md" icon="check">
                Create Assignment
            </x-ui.button>
        </div>
    </form>
</div>
@endsection

