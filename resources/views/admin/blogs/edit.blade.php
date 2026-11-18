@extends('layouts.admin')

@section('title', 'Edit Blog: ' . $blog->title . ' | ZebLearn LMS')
@section('page-title', 'Edit Article')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-1">
                EDITING PUBLICATION
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight line-clamp-1">
                {{ $blog->title }}
            </h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.blogs.preview', $blog) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-xs font-bold uppercase transition-colors">
                Preview Article
            </a>
            <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono font-bold text-slate-500 hover:text-slate-800">
                &larr; Back
            </a>
        </div>
    </div>

    @include('admin.blogs._form', [
        'action' => route('admin.blogs.update', $blog),
        'method' => 'PUT',
        'submitLabel' => 'Update Article',
    ])
</div>
@endsection
