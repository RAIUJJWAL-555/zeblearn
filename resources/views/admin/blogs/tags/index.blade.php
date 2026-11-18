@extends('layouts.admin')

@section('title', 'Manage Blog Tags | ZebLearn LMS')
@section('page-title', 'Blog Tags')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-1">
                INDEXING
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Blog Tags
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Fine-grained topical keywords connecting related articles across modules.
            </p>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="text-xs font-mono font-bold text-slate-500 hover:text-slate-800">
            &larr; Back to Articles
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        {{-- Add Tag Form (4 cols) --}}
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs">
            <h2 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Add New Tag</h2>
            <form method="POST" action="{{ route('admin.blogs.tags.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Tag Name <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        required
                        placeholder="e.g. S4HANA, OBYC, P2P"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <div>
                    <label for="slug" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1">
                        Slug (optional)
                    </label>
                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        placeholder="s4hana"
                        class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-mono text-slate-900 focus:border-[#FB6221] outline-hidden"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full py-2.5 bg-[#0A0A0A] hover:bg-[#FB6221] text-white text-xs font-mono font-bold uppercase rounded-xl transition-colors cursor-pointer shadow-xs"
                >
                    Create Tag
                </button>
            </form>
        </div>

        {{-- Tags Cloud & Table (8 cols) --}}
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/90 shadow-xs">
            <h3 class="font-heading text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Tag Cloud &amp; Counts</h3>
            <div class="flex flex-wrap gap-2 mb-6">
                @forelse($tags as $tag)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-[#FB6221]/50 text-xs font-mono transition-colors group">
                        <span class="text-slate-700 font-bold">#{{ $tag->name }}</span>
                        <span class="px-1.5 py-0.2 rounded-md bg-white text-[10px] text-slate-400 font-bold border border-slate-200">
                            {{ $tag->blogs_count }}
                        </span>
                        <form method="POST" action="{{ route('admin.blogs.tags.destroy', $tag) }}" onsubmit="return confirm('Delete tag #{{ $tag->name }}?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-300 hover:text-rose-600 ml-1 cursor-pointer" title="Delete">
                                &times;
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400">No tags registered yet.</p>
                @endforelse
            </div>

            @if($tags->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ $tags->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
