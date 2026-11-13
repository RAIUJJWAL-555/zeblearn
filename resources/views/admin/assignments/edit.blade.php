@extends('layouts.admin')

@section('title', 'Edit Assignment — ' . $assignment->title . ' | ZebLearn LMS')
@section('page-title', 'Edit Assignment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- BREADCRUMBS & HEADER --}}
    <div>
        <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
            <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
            <span>/</span>
            <a href="{{ route('admin.assignments.index') }}" class="hover:text-[#FB6221] transition-colors">Assignments</a>
            <span>/</span>
            <a href="{{ route('admin.assignments.show', $assignment) }}" class="hover:text-[#FB6221] transition-colors">{{ Str::limit($assignment->title, 20) }}</a>
            <span>/</span>
            <span class="text-slate-900 font-bold">Edit</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    Edit Assignment Configuration
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Update assignment parameters, deadlines, and requirements.
                </p>
            </div>
            @if ($hasSubmissions)
                <x-ui.badge variant="warning" size="md">
                    Configuration Locked ({{ $assignment->submissions()->count() }} Submissions)
                </x-ui.badge>
            @endif
        </div>
    </div>

    @if ($hasSubmissions)
        <x-ui.alert variant="warning">
            <strong class="font-bold block">Integrity Lock Active:</strong>
            <span>Student submissions already exist for this assignment. Structural settings (course/module context, max marks, due date, max attempts, and file constraints) are permanently frozen to protect grading records. Non-structural metadata (title, description, instructions) may still be modified.</span>
        </x-ui.alert>
    @endif

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

    {{-- EDIT FORM --}}
    <form method="POST" action="{{ route('admin.assignments.update', $assignment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- CONTEXT & TARGET --}}
        <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
            <h2 class="text-sm font-heading font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">
                1. Assignment Context &amp; Placement
            </h2>

            <div
                class="grid grid-cols-1 sm:grid-cols-3 gap-4"
                x-data="{
                    courses: {{ $courses->toJson() }},
                    selectedCourseId: '{{ old('course_id', $assignment->course_id) }}',
                    selectedModuleId: '{{ old('course_module_id', $assignment->course_module_id) }}',
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
                    <x-ui.select name="course_id" x-model="selectedCourseId" :disabled="$hasSubmissions" required>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}" @selected(old('course_id', $assignment->course_id) == $c->id)>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    @if ($hasSubmissions)
                        <input type="hidden" name="course_id" value="{{ $assignment->course_id }}" />
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Module <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <x-ui.select name="course_module_id" x-model="selectedModuleId" :disabled="$hasSubmissions">
                        <option value="">-- Course-Level (No Module) --</option>
                        <template x-for="m in modules" :key="m.id">
                            <option :value="m.id" x-text="m.title" :selected="m.id == selectedModuleId"></option>
                        </template>
                    </x-ui.select>
                    @if ($hasSubmissions && $assignment->course_module_id)
                        <input type="hidden" name="course_module_id" value="{{ $assignment->course_module_id }}" />
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Lesson <span class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <x-ui.select name="lesson_id" :disabled="$hasSubmissions">
                        <option value="">-- None (Module / Course Level) --</option>
                        <template x-for="l in lessons" :key="l.id">
                            <option :value="l.id" x-text="l.title" :selected="l.id == '{{ old('lesson_id', $assignment->lesson_id) }}'"></option>
                        </template>
                    </x-ui.select>
                    @if ($hasSubmissions && $assignment->lesson_id)
                        <input type="hidden" name="lesson_id" value="{{ $assignment->lesson_id }}" />
                    @endif
                </div>
            </div>
        </x-ui.card>

        {{-- BASIC INFORMATION (ALWAYS EDITABLE) --}}
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
                    value="{{ old('title', $assignment->title) }}"
                    required
                />
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Brief Description
                </label>
                <x-ui.textarea
                    name="description"
                    rows="2"
                >{{ old('description', $assignment->description) }}</x-ui.textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Detailed Instructions &amp; Requirements
                </label>
                <x-ui.textarea
                    name="instructions"
                    rows="6"
                >{{ old('instructions', $assignment->instructions) }}</x-ui.textarea>
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
                        value="{{ old('max_marks', $assignment->max_marks) }}"
                        :disabled="$hasSubmissions"
                        required
                    />
                    @if ($hasSubmissions)
                        <input type="hidden" name="max_marks" value="{{ $assignment->max_marks }}" />
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Passing Marks
                    </label>
                    <x-ui.input
                        type="number"
                        name="passing_marks"
                        min="0"
                        max="1000"
                        value="{{ old('passing_marks', $assignment->passing_marks) }}"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Start Date &amp; Time
                    </label>
                    <x-ui.input
                        type="datetime-local"
                        name="start_at"
                        value="{{ old('start_at', $assignment->start_at?->format('Y-m-d\TH:i')) }}"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Due Date &amp; Time
                    </label>
                    <x-ui.input
                        type="datetime-local"
                        name="due_at"
                        value="{{ old('due_at', $assignment->due_at?->format('Y-m-d\TH:i')) }}"
                        :disabled="$hasSubmissions"
                    />
                    @if ($hasSubmissions)
                        <input type="hidden" name="due_at" value="{{ $assignment->due_at?->format('Y-m-d\TH:i') }}" />
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div class="flex items-start gap-2.5">
                    <input
                        type="checkbox"
                        id="allow_late_submissions"
                        name="allow_late_submissions"
                        value="1"
                        @checked(old('allow_late_submissions', $assignment->allow_late_submissions))
                        @disabled($hasSubmissions)
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
                    @if ($hasSubmissions)
                        <input type="hidden" name="allow_late_submissions" value="{{ $assignment->allow_late_submissions ? '1' : '0' }}" />
                    @endif
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
                        value="{{ old('max_attempts', $assignment->max_attempts) }}"
                        :disabled="$hasSubmissions"
                        required
                    />
                    @if ($hasSubmissions)
                        <input type="hidden" name="max_attempts" value="{{ $assignment->max_attempts }}" />
                    @endif
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
                        @checked(old('allow_text_submission', $assignment->allow_text_submission))
                        @disabled($hasSubmissions)
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
                    @if ($hasSubmissions)
                        <input type="hidden" name="allow_text_submission" value="{{ $assignment->allow_text_submission ? '1' : '0' }}" />
                    @endif
                </div>

                <div class="flex items-start gap-2.5">
                    <input
                        type="checkbox"
                        id="allow_file_submission"
                        name="allow_file_submission"
                        value="1"
                        @checked(old('allow_file_submission', $assignment->allow_file_submission))
                        @disabled($hasSubmissions)
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
                    @if ($hasSubmissions)
                        <input type="hidden" name="allow_file_submission" value="{{ $assignment->allow_file_submission ? '1' : '0' }}" />
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Allowed File Extensions
                    </label>
                    <x-ui.input
                        type="text"
                        name="allowed_file_types"
                        value="{{ old('allowed_file_types', $assignment->allowed_file_types) }}"
                        :disabled="$hasSubmissions"
                    />
                    @if ($hasSubmissions)
                        <input type="hidden" name="allowed_file_types" value="{{ $assignment->allowed_file_types }}" />
                    @endif
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
                        value="{{ old('max_file_size_mb', $assignment->max_file_size_mb) }}"
                        :disabled="$hasSubmissions"
                    />
                    @if ($hasSubmissions)
                        <input type="hidden" name="max_file_size_mb" value="{{ $assignment->max_file_size_mb }}" />
                    @endif
                </div>
            </div>
        </x-ui.card>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-between pt-4 border-t border-slate-200">
            <div>
                @if (! $hasSubmissions)
                    <form method="POST" action="{{ route('admin.assignments.destroy', $assignment) }}" onsubmit="return confirm('Are you sure you want to delete this assignment?');" class="inline">
                        @csrf
                        @method('DELETE')
                        <x-ui.button type="submit" variant="danger" size="sm">
                            Delete Assignment
                        </x-ui.button>
                    </form>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <x-ui.button href="{{ route('admin.assignments.show', $assignment) }}" variant="ghost" size="md">
                    Cancel
                </x-ui.button>
                <x-ui.button type="submit" variant="primary" size="md" icon="check">
                    Save Changes
                </x-ui.button>
            </div>
        </div>
    </form>
</div>
@endsection

