@extends('layouts.admin')

@section('title', 'Course CSV Import | ZebLearn LMS')
@section('page-title', 'Course CSV Import')

@section('content')
<div class="space-y-8">

    {{-- Alert Messages --}}
    @if ($errors->any())
        <x-ui.alert variant="danger" dismissible>
            <div class="space-y-1">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc pl-5 space-y-0.5 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </x-ui.alert>
    @endif

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

    @if (session('warning'))
        <x-ui.alert variant="warning" dismissible>
            {{ session('warning') }}
        </x-ui.alert>
    @endif

    {{-- Breadcrumbs & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-slate-800 transition">Courses</a>
                <span>/</span>
                <span class="text-[#FB6221] font-bold">CSV Import</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Course CSV Import Console
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Batch import courses with 100% field parity with the manual course creation form, including pricing, Markdown descriptions, cohorts, FAQs, modules, lessons, and trust controls.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <x-ui.button
                href="{{ route('admin.courses.import.guide') }}"
                variant="secondary"
                size="md"
                icon="menu_book"
            >
                Format Specification Guide
            </x-ui.button>
            <x-ui.button
                href="{{ route('admin.courses.import.template') }}"
                variant="primary"
                size="md"
                icon="download"
            >
                Download CSV Template
            </x-ui.button>
        </div>
    </div>

    {{-- Workflow Steps Strip --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-mono font-bold text-xs flex items-center justify-center">1</span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Download Template</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Download the complete CSV containing all required column headers and sample data.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-mono font-bold text-xs flex items-center justify-center">2</span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Fill Course Data</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Add 1 row per course. Use valid JSON in cohort, FAQ, module, and lesson columns.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-mono font-bold text-xs flex items-center justify-center">3</span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-900">Upload &amp; Validate</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Upload CSV. Server validates every field, pricing math, categories, and JSON trees.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-[#FB6221]/15 text-[#FB6221] font-mono font-bold text-xs flex items-center justify-center">4</span>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-[#FB6221]">Preview &amp; Confirm</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Inspect rich course preview cards before database creation. Choose draft or publish.
            </p>
        </div>
    </div>

    {{-- Upload Form Card --}}
    <x-ui.card
        title="Upload Completed Course CSV"
        subtitle="Select or drop your CSV file for instant validation and visual course preview"
        padding="lg"
    >
        <form
            action="{{ route('admin.courses.import.preview') }}"
            method="POST"
            enctype="multipart/form-data"
            x-data="{
                fileName: '',
                fileSize: '',
                isDragging: false,
                onFileSelected(e) {
                    const files = e.target.files;
                    if (files && files.length > 0) {
                        this.fileName = files[0].name;
                        const bytes = files[0].size;
                        if (bytes < 1024) this.fileSize = bytes + ' B';
                        else if (bytes < 1048576) this.fileSize = (bytes / 1024).toFixed(1) + ' KB';
                        else this.fileSize = (bytes / 1048576).toFixed(1) + ' MB';
                    }
                },
                clearFile() {
                    this.fileName = '';
                    this.fileSize = '';
                    this.$refs.fileInput.value = '';
                }
            }"
            class="space-y-6"
        >
            @csrf

            {{-- Native Interactive Dropzone Area --}}
            <div
                class="relative border-2 border-dashed rounded-2xl p-8 sm:p-12 text-center transition-all"
                :class="isDragging ? 'border-[#FB6221] bg-orange-50/60' : (fileName ? 'border-emerald-400 bg-emerald-50/20' : 'border-slate-300 bg-slate-50/50 hover:bg-slate-50 hover:border-slate-400')"
            >
                {{-- Native Full Overlay Input: Guarantees 100% Click & Drag Support Across All Browsers --}}
                <input
                    type="file"
                    id="csv_file_input"
                    name="csv_file"
                    x-ref="fileInput"
                    accept=".csv, text/csv, text/plain, application/vnd.ms-excel"
                    required
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                    @dragenter="isDragging = true"
                    @dragleave="isDragging = false"
                    @drop="isDragging = false"
                    @change="onFileSelected($event)"
                    title="Click or drop a CSV file"
                />

                {{-- Visual Content Display --}}
                <div class="space-y-4 pointer-events-none select-none">
                    <template x-if="!fileName">
                        <div class="space-y-3">
                            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 shadow-xs flex items-center justify-center mx-auto text-[#FB6221]">
                                <span class="material-symbols-outlined text-4xl">upload_file</span>
                            </div>

                            <div>
                                <p class="text-base font-bold text-slate-800">
                                    <span class="text-[#FB6221]">Click anywhere here</span> to browse or drag and drop your CSV file
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Supports standard UTF-8 CSV files up to 10MB
                                </p>
                            </div>

                            <div>
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-mono font-bold shadow-2xs">
                                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">folder_open</span>
                                    <span>Browse Files on Computer</span>
                                </span>
                            </div>
                        </div>
                    </template>

                    {{-- Selected File Display --}}
                    <template x-if="fileName">
                        <div class="space-y-3">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto shadow-xs">
                                <span class="material-symbols-outlined text-4xl">description</span>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-slate-900 font-mono" x-text="fileName"></p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5" x-text="'File size: ' + fileSize"></p>
                            </div>

                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                <span>File Selected &amp; Ready for Preview</span>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Action to clear/change if file is already selected --}}
                <template x-if="fileName">
                    <div class="mt-4 relative z-20">
                        <button
                            type="button"
                            @click="clearFile()"
                            class="inline-flex items-center gap-1 text-xs font-mono font-medium text-rose-600 hover:text-rose-800 hover:underline cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[14px]">close</span>
                            <span>Change or remove selected file</span>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Direct Browser File Picker Fallback --}}
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">touch_app</span>
                    <span>Having trouble with drag &amp; drop? Use standard file selector:</span>
                </div>
                <div>
                    <label
                        for="csv_file_input"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:border-[#FB6221] hover:text-[#FB6221] font-mono font-bold text-slate-700 shadow-2xs transition-all cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">file_open</span>
                        <span>Select CSV File</span>
                    </label>
                </div>
            </div>

            {{-- Options & Submit Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100">
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="update_existing"
                        value="1"
                        class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300 cursor-pointer mt-0.5"
                    />
                    <div class="text-xs">
                        <span class="font-bold text-slate-800">Update Existing Courses</span>
                        <span class="text-slate-500 block text-[11px] mt-0.5">
                            If a course with the same URL slug already exists in MySQL, update its fields and curriculum instead of reporting a conflict.
                        </span>
                    </div>
                </label>

                <div class="flex items-center gap-3 justify-end">
                    <x-ui.button
                        href="{{ route('admin.courses.index') }}"
                        variant="secondary"
                        size="md"
                    >
                        Cancel
                    </x-ui.button>
                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="md"
                        icon="fact_check"
                    >
                        Validate &amp; Preview
                    </x-ui.button>
                </div>
            </div>
        </form>
    </x-ui.card>

    {{-- Field Reference Specification --}}
    <x-ui.card
        title="Complete CSV Columns Reference (100% Form Parity)"
        subtitle="Every field supported in the Admin Course Creator mapped to its exact CSV column header"
        padding="md"
    >
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 font-mono uppercase text-[11px] text-slate-600">
                        <th class="p-3 font-bold">Column Header</th>
                        <th class="p-3 font-bold">Section</th>
                        <th class="p-3 font-bold">Required</th>
                        <th class="p-3 font-bold">Type</th>
                        <th class="p-3 font-bold">Description &amp; Constraints</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($columns as $colName => $meta)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                                <code>{{ $colName }}</code>
                            </td>
                            <td class="p-3 text-slate-600 font-medium whitespace-nowrap">
                                {{ $meta['section'] }}
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                @if ($meta['required'])
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Required
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-mono text-slate-500 bg-slate-100">
                                        Optional
                                    </span>
                                @endif
                            </td>
                            <td class="p-3 font-mono text-slate-600 whitespace-nowrap">
                                {{ $meta['type'] }}
                            </td>
                            <td class="p-3 text-slate-600 leading-relaxed max-w-md">
                                {{ $meta['description'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>

</div>
@endsection
