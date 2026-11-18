@extends('layouts.admin')

@section('title', 'Course CSV Specification Guide | ZebLearn LMS')
@section('page-title', 'CSV Import Specification Guide')

@section('content')
<div class="space-y-8">

    {{-- Breadcrumbs & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-slate-800 transition">Courses</a>
                <span>/</span>
                <a href="{{ route('admin.courses.import.index') }}" class="hover:text-slate-800 transition">CSV Import</a>
                <span>/</span>
                <span class="text-[#FB6221] font-bold">Specification Guide</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Course CSV Import Format Guide
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Comprehensive documentation, JSON schemas, and AI prompting instructions to format error-free course data for ZebLearn LMS.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <x-ui.button
                href="{{ route('admin.courses.import.guide', ['download' => 1]) }}"
                variant="secondary"
                size="md"
                icon="download"
            >
                Download Markdown (.md)
            </x-ui.button>
            <x-ui.button
                href="{{ route('admin.courses.import.template') }}"
                variant="primary"
                size="md"
                icon="file_download"
            >
                Download CSV Template
            </x-ui.button>
        </div>
    </div>

    {{-- Guide Card --}}
    <x-ui.card padding="lg">
        <div class="course-prose max-w-none">
            {!! app(\App\Services\MarkdownSanitizerService::class)->toHtml($markdown) !!}
        </div>
    </x-ui.card>

    <div class="flex justify-end pt-2">
        <x-ui.button
            href="{{ route('admin.courses.import.index') }}"
            variant="primary"
            size="md"
            icon="arrow_forward"
        >
            Proceed to CSV Upload
        </x-ui.button>
    </div>
</div>
@endsection
