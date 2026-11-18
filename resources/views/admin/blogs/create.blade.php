@extends('layouts.admin')

@section('title', 'Create New Blog Article | ZebLearn LMS')
@section('page-title', 'Create Article')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-1">
                NEW PUBLICATION
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Create New Blog Article
            </h1>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono font-bold text-slate-500 hover:text-slate-800">
            &larr; Back to Articles
        </a>
    </div>

    @include('admin.blogs._form', [
        'action' => route('admin.blogs.store'),
        'method' => 'POST',
        'submitLabel' => 'Save & Create Article',
    ])
</div>
@endsection
