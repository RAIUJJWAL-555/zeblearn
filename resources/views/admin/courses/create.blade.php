@extends('layouts.admin')

@section('title', 'Create New Course | ZebLearn LMS')
@section('page-title', 'Create Course')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#FB6221] transition-colors mb-2">
                <x-ui.icon name="arrow-left" size="xs" />
                <span>Back to Courses</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Create New Course
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Configure module details, curriculum parameters, tuition fees, and public visibility.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.courses.store') }}" method="POST">
        @csrf
        @include('admin.courses._form', ['course' => $course, 'categories' => $categories])
    </form>
</div>
@endsection
