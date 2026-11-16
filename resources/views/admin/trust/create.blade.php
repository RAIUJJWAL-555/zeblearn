@extends('layouts.admin')

@section('title', 'Add Trust & Proof Record | ZebLearn LMS')
@section('page-title', 'Trust & Proof CMS')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Breadcrumb & Title --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.trust.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#FB6221] transition-colors mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Back to Central Trust Library</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Add Trust &amp; Proof Record
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Centralized entry for placement proofs, corporate partners, testimonials, capstone projects, and credentials.
            </p>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.trust.store') }}" method="POST">
        @csrf
        @include('admin.trust._form', [
            'trustItem' => $trustItem,
            'types' => $types,
            'categories' => $categories,
            'allCourses' => $allCourses,
        ])
    </form>
</div>
@endsection

