@extends('layouts.admin')

@section('title', 'Edit Trust Record | ZebLearn LMS')
@section('page-title', 'Trust & Proof CMS')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    {{-- Breadcrumb & Title --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.trust.index', ['type' => $trustItem->type]) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-[#FB6221] transition-colors mb-2">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Back to Central Trust Library</span>
            </a>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                    Edit {{ $trustItem->type_label }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                    ID #{{ $trustItem->id }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Updates made here will propagate automatically to all linked course landing pages.
            </p>
        </div>

        <form action="{{ route('admin.trust.destroy', $trustItem) }}" method="POST" onsubmit="return confirm('Permanently remove this trust record from the central library? It will be removed from all course pages.')">
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="px-4 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-mono font-bold uppercase tracking-wider transition-colors flex items-center gap-1.5"
            >
                <span class="material-symbols-outlined text-[16px]">delete</span>
                <span>Delete Record</span>
            </button>
        </form>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.trust.update', $trustItem) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.trust._form', [
            'trustItem' => $trustItem,
            'types' => $types,
            'categories' => $categories,
            'allCourses' => $allCourses,
        ])
    </form>
</div>
@endsection

