@extends('layouts.admin')

@section('title', 'Centralized Trust & Proof of Work Library | ZebLearn LMS')
@section('page-title', 'Trust & Proof Library')

@section('content')
<div class="space-y-6">

    {{-- 1. HEADER & METRICS --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-[10px] font-mono font-bold tracking-wider uppercase mb-2">
                CENTRALIZED TRUST &amp; PROOF CMS
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                Trust &amp; Proof of Work Library
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Manage student placements, testimonials, certifications, trainers, partners, projects, and awards centrally. Map records across courses with zero redundant data entry.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <x-ui.button
                href="{{ route('admin.trust.create', ['type' => $activeType !== 'all' ? $activeType : 'placement']) }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                Add Trust Item
            </x-ui.button>
        </div>
    </div>

    {{-- Telemetry Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-slate-500 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">TOTAL RECORDS</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading">{{ number_format($totalCount) }}</span>
                <span class="text-xs text-slate-400 font-mono">items</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-emerald-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">LIVE PUBLISHED</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading">{{ number_format($publishedCount) }}</span>
                <span class="text-xs text-slate-400 font-mono">active</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">VERIFIED EVIDENCE</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-[#FB6221] font-heading">{{ number_format($verifiedCount) }}</span>
                <span class="text-xs text-slate-400 font-mono">verified</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
            <span class="text-indigo-600 font-mono text-[10px] font-bold uppercase tracking-wider block mb-1">PROOF CATEGORIES</span>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-indigo-600 font-heading">{{ count($types) }}</span>
                <span class="text-xs text-slate-400 font-mono">disciplines</span>
            </div>
        </div>
    </div>

    {{-- 2. TYPE FILTER TABS --}}
    <div class="bg-white rounded-2xl p-2 border border-slate-200/90 shadow-2xs overflow-x-auto scrollbar-none flex items-center gap-1">
        <a
            href="{{ route('admin.trust.index', array_merge(request()->query(), ['type' => 'all'])) }}"
            class="px-3.5 py-1.5 rounded-xl font-mono text-xs font-bold uppercase tracking-wider transition-all shrink-0 {{ $activeType === 'all' ? 'bg-[#0A0A0A] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
        >
            All Disciplines
        </a>

        @foreach ($types as $typeKey => $typeLabel)
            <a
                href="{{ route('admin.trust.index', array_merge(request()->query(), ['type' => $typeKey])) }}"
                class="px-3.5 py-1.5 rounded-xl font-mono text-xs font-bold uppercase tracking-wider transition-all shrink-0 {{ $activeType === $typeKey ? 'bg-[#0A0A0A] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}"
            >
                {{ $typeLabel }}
            </a>
        @endforeach
    </div>

    {{-- 3. SEARCH & ADVANCED FILTER BAR --}}
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.trust.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <input type="hidden" name="type" value="{{ $activeType }}">

            {{-- Keyword Search --}}
            <div class="lg:col-span-4 relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input
                    type="text"
                    name="q"
                    value="{{ $searchQuery ?? '' }}"
                    placeholder="Search candidate, company, title..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:border-[#FB6221] outline-hidden"
                >
            </div>

            {{-- Filter by Course --}}
            <div class="lg:col-span-3">
                <select
                    name="course_id"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#FB6221] outline-hidden"
                >
                    <option value="all" {{ ($activeCourse ?? 'all') === 'all' ? 'selected' : '' }}>All Courses</option>
                    @foreach ($allCourses as $c)
                        <option value="{{ $c->id }}" {{ ($activeCourse ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter by Category --}}
            <div class="lg:col-span-2">
                <select
                    name="category"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#FB6221] outline-hidden"
                >
                    <option value="all" {{ ($activeCategory ?? 'all') === 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach ($categories as $catKey => $catData)
                        <option value="{{ $catKey }}" {{ ($activeCategory ?? '') === $catKey ? 'selected' : '' }}>
                            {{ $catData['name'] ?? $catKey }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter by Status --}}
            <div class="lg:col-span-2">
                <select
                    name="status"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-[#FB6221] outline-hidden"
                >
                    <option value="all" {{ ($activeStatus ?? 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="published" {{ ($activeStatus ?? '') === 'published' ? 'selected' : '' }}>Live Published</option>
                    <option value="draft" {{ ($activeStatus ?? '') === 'draft' ? 'selected' : '' }}>Draft / Inactive</option>
                    <option value="verified" {{ ($activeStatus ?? '') === 'verified' ? 'selected' : '' }}>Verified Only</option>
                    <option value="unverified" {{ ($activeStatus ?? '') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>
            </div>

            {{-- Filter Action Buttons --}}
            <div class="lg:col-span-1 flex items-center gap-1.5">
                <button
                    type="submit"
                    class="w-full py-2 px-3 bg-[#FB6221] hover:bg-[#e05214] text-white font-mono text-xs font-bold rounded-xl transition-all shadow-2xs text-center"
                    title="Apply Filters"
                >
                    Filter
                </button>
                @if (!empty($searchQuery) || ($activeCourse ?? 'all') !== 'all' || ($activeCategory ?? 'all') !== 'all' || ($activeStatus ?? 'all') !== 'all' || ($activeType ?? 'all') !== 'all')
                    <a
                        href="{{ route('admin.trust.index') }}"
                        class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-all"
                        title="Clear filters"
                    >
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 4. ITEMS LIST TABLE --}}
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
        @if ($items->isEmpty())
            <div class="py-16 text-center text-slate-500 font-sans">
                <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">verified</span>
                <h3 class="font-heading text-base font-bold text-slate-800">No Trust &amp; Proof Records Found</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    No items match the current search filters. Create your first record or clear active filters.
                </p>
                <div class="mt-4">
                    <x-ui.button
                        href="{{ route('admin.trust.create', ['type' => $activeType !== 'all' ? $activeType : 'placement']) }}"
                        variant="primary"
                        size="sm"
                        icon="plus"
                    >
                        Create Proof Record
                    </x-ui.button>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50/80 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Proof Item / Identity</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Dynamic Mapping Scope</th>
                            <th class="py-3 px-4">Verification</th>
                            <th class="py-3 px-4">State</th>
                            <th class="py-3 px-4 text-center">Priority</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @foreach ($items as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                {{-- Item Details --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                                            @if($item->display_image)
                                                <img src="{{ $item->display_image }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="font-mono text-xs font-bold text-slate-500 uppercase">
                                                    {{ substr($item->title, 0, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-xs sm:max-w-sm">
                                            <div class="font-heading font-bold text-slate-900 truncate text-sm">
                                                {{ $item->title }}
                                            </div>
                                            @if($item->subtitle)
                                                <div class="text-[11px] text-slate-500 truncate">
                                                    {{ $item->subtitle }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="py-3.5 px-4 font-mono">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                                        {{ $item->type_label }}
                                    </span>
                                </td>

                                {{-- Scope & Mapping --}}
                                <td class="py-3.5 px-4">
                                    @if ($item->scope_type === 'global' || $item->scope_type === 'all')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Global (All Courses)
                                        </span>
                                    @elseif ($item->scope_type === 'category')
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                            Category: {{ $item->scope_category }}
                                        </span>
                                    @elseif ($item->scope_type === 'selected_courses')
                                        <div class="space-y-1">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-800 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                                <span>{{ $item->courses->count() }} Specific Courses</span>
                                            </span>
                                            @if ($item->courses->isNotEmpty())
                                                <div class="text-[10px] text-slate-400 font-mono truncate max-w-xs">
                                                    {{ $item->courses->pluck('name')->implode(', ') }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Verification --}}
                                <td class="py-3.5 px-4">
                                    @if ($item->is_verified)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold uppercase text-emerald-700">
                                            <span class="material-symbols-outlined text-[14px] text-emerald-600">verified</span>
                                            <span>Verified</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold uppercase text-slate-400">
                                            <span>Unverified</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Publication State --}}
                                <td class="py-3.5 px-4">
                                    <form action="{{ route('admin.trust.toggle-status', $item) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="field" value="is_published">
                                        <button
                                            type="submit"
                                            class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase transition-all {{ $item->is_published ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}"
                                            title="Click to toggle publication"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full {{ $item->is_published ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span>{{ $item->is_published ? 'Live' : 'Draft' }}</span>
                                        </button>
                                    </form>
                                </td>

                                {{-- Priority --}}
                                <td class="py-3.5 px-4 text-center font-mono text-[11px] text-slate-500">
                                    {{ $item->display_priority }}
                                </td>

                                {{-- Actions --}}
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a
                                            href="{{ route('admin.trust.edit', $item) }}"
                                            class="p-1.5 text-slate-600 hover:text-[#FB6221] hover:bg-orange-50 rounded-lg transition-colors"
                                            title="Edit Item"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>

                                        <form
                                            action="{{ route('admin.trust.destroy', $item) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this trust record? It will automatically be removed from all mapped courses.')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                title="Delete Item"
                                            >
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $items->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection

