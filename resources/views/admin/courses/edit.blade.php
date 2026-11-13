@extends('layouts.admin')

@section('title', 'Edit Course: ' . $course->name . ' | ZebLearn LMS')
@section('page-title', 'Edit Course')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#FB6221] transition-colors mb-2">
                <x-ui.icon name="arrow-left" size="xs" />
                <span>Back to Courses</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight truncate max-w-xl">
                    Edit Course: {{ $course->name }}
                </h1>
                @if ($course->published_at)
                    <x-ui.badge variant="success" size="xs" dot>
                        Live Published
                    </x-ui.badge>
                @else
                    <x-ui.badge variant="neutral" size="xs">
                        Draft / Unpublished
                    </x-ui.badge>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Last updated {{ $course->updated_at?->diffForHumans() ?? 'recently' }} • ID #{{ $course->id }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Curriculum Link --}}
            <x-ui.button
                href="{{ route('admin.courses.curriculum', $course) }}"
                variant="dark"
                size="sm"
                icon="book"
            >
                Curriculum &amp; Lessons
            </x-ui.button>

            {{-- Preview Link on Public Website --}}
            <x-ui.button
                href="{{ $course->url }}"
                variant="secondary"
                size="sm"
                icon="external-link"
                target="_blank"
            >
                View Live Listing
            </x-ui.button>

            {{-- Delete Button Form (Protected) --}}
            @if ($course->slug !== 'sap-fico-training')
                <form
                    action="{{ route('admin.courses.destroy', $course) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this course? This action is permanent and cannot be undone.')"
                >
                    @csrf
                    @method('DELETE')
                    <x-ui.button
                        type="submit"
                        variant="danger-outline"
                        size="sm"
                        icon="trash"
                    >
                        Delete
                    </x-ui.button>
                </form>
            @endif
        </div>
    </div>

    {{-- Edit Form --}}
    <form action="{{ route('admin.courses.update', $course) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.courses._form', ['course' => $course, 'categories' => $categories])
    </form>
</div>
@endsection
