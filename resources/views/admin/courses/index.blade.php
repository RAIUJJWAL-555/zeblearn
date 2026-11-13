@extends('layouts.admin')

@section('title', 'Course Management | ZebLearn LMS')
@section('page-title', 'Course Management')

@section('content')
<div class="space-y-6">

    {{-- Alert Messages --}}
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

    {{-- Top Action Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                    Course Management
                </h1>
                <x-ui.badge variant="primary" size="sm" class="font-mono font-bold">
                    {{ $totalCount }}
                </x-ui.badge>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Manage curriculum tracks, publish state, tuition fees, and module metadata.
            </p>
        </div>

        <div class="shrink-0">
            <x-ui.button
                href="{{ route('admin.courses.create') }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                Create New Course
            </x-ui.button>
        </div>
    </div>

    {{-- Search and Filters Card --}}
    <x-ui.card padding="sm">
        <form action="{{ route('admin.courses.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            {{-- Search Bar --}}
            <div class="lg:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-ui.icon name="search" size="sm" />
                </div>
                <input
                    type="text"
                    name="search"
                    value="{{ $currentSearch }}"
                    aria-label="Search courses by name or keyword"
                    placeholder="Search by course name or keyword..."
                    class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50/80 border border-slate-200 rounded-xl text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden"
                />
            </div>

            {{-- Category Filter --}}
            <div>
                <select
                    name="category"
                    aria-label="Filter courses by category"
                    onchange="this.form.submit()"
                    class="w-full px-3 py-2.5 text-xs bg-slate-50/80 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden cursor-pointer"
                >
                    <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->slug }}" {{ $currentCategory === $cat->slug ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter & Reset --}}
            <div class="flex items-center gap-2">
                <select
                    name="status"
                    aria-label="Filter courses by status"
                    onchange="this.form.submit()"
                    class="w-full px-3 py-2.5 text-xs bg-slate-50/80 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden cursor-pointer"
                >
                    <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="available" {{ $currentStatus === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="upcoming" {{ $currentStatus === 'upcoming' ? 'selected' : '' }}>Upcoming Batch</option>
                    <option value="planned" {{ $currentStatus === 'planned' ? 'selected' : '' }}>Roadmap Planned</option>
                </select>

                @if ($currentSearch || $currentCategory !== 'all' || $currentStatus !== 'all')
                    <a
                        href="{{ route('admin.courses.index') }}"
                        class="p-2.5 text-xs font-semibold text-slate-500 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 rounded-xl transition-colors shrink-0 flex items-center justify-center border border-slate-200"
                        title="Reset all filters"
                    >
                        <x-ui.icon name="close" size="xs" />
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- Courses Table Card --}}
    <x-ui.card padding="none">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] font-mono uppercase tracking-wider text-slate-400 bg-slate-50/70 border-b border-slate-200/80 select-none">
                    <tr>
                        <th class="py-3.5 px-6">Course Name</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tuition Fee</th>
                        <th class="py-3.5 px-4 text-center">Modules</th>
                        <th class="py-3.5 px-4 text-center">Published</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($courses as $course)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Course Name & Slug --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('admin.courses.edit', $course) }}"
                                        class="font-bold text-slate-900 font-heading text-sm hover:text-[#FB6221] transition-colors"
                                    >
                                        {{ $course->name }}
                                    </a>
                                    @if ($course->featured)
                                        <x-ui.badge variant="warning" size="xs" class="font-mono font-bold">
                                            FEATURED
                                        </x-ui.badge>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-400 font-mono">
                                    <span>/courses/{{ $course->slug }}</span>
                                    @if ($course->tag_badge)
                                        <span class="text-slate-300">•</span>
                                        <span class="text-[#FB6221]">{{ $course->tag_badge }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Category Badge --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <x-ui.badge variant="neutral" size="xs">
                                    {{ $course->category_label }}
                                </x-ui.badge>
                            </td>

                            {{-- Status with inline toggle form --}}
                            <td class="py-4 px-4 whitespace-nowrap">
                                <form action="{{ route('admin.courses.toggle-status', $course) }}" method="POST" class="inline-flex">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="status"
                                        aria-label="Change status for {{ $course->name }}"
                                        onchange="this.form.submit()"
                                        class="text-[11px] font-semibold rounded-lg px-2.5 py-1 border transition-colors cursor-pointer outline-hidden
                                            {{ $course->status === 'available' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : '' }}
                                            {{ $course->status === 'upcoming' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                            {{ $course->status === 'planned' ? 'bg-slate-100 text-slate-700 border-slate-300' : '' }}"
                                    >
                                        <option value="available" {{ $course->status === 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="upcoming" {{ $course->status === 'upcoming' ? 'selected' : '' }}>Upcoming Batch</option>
                                        <option value="planned" {{ $course->status === 'planned' ? 'selected' : '' }}>Roadmap Planned</option>
                                    </select>
                                </form>
                            </td>

                            {{-- Fee --}}
                            <td class="py-4 px-4 whitespace-nowrap font-mono font-semibold text-slate-900">
                                <div>{{ $course->display_price }}</div>
                                @if ($course->original_fee)
                                    <div class="text-[10px] text-slate-400 line-through">₹{{ number_format($course->original_fee) }}</div>
                                @endif
                            </td>

                            {{-- Modules & Duration --}}
                            <td class="py-4 px-4 whitespace-nowrap text-center font-mono">
                                <span class="font-semibold text-slate-900">{{ $course->modules_count ?? 12 }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $course->duration ?? '3 Months' }}</span>
                            </td>

                            {{-- Published State --}}
                            <td class="py-4 px-4 whitespace-nowrap text-center">
                                @if ($course->published_at && $course->published_at <= now())
                                    <x-ui.badge variant="success" size="xs" dot>
                                        Live
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral" size="xs">
                                        Draft
                                    </x-ui.badge>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Curriculum --}}
                                    <a
                                        href="{{ route('admin.courses.curriculum', $course) }}"
                                        class="p-1.5 min-w-[32px] min-h-[32px] inline-flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-200"
                                        aria-label="Manage Curriculum for {{ $course->name }}"
                                        title="Manage Curriculum & Lessons"
                                    >
                                        <x-ui.icon name="book" size="sm" />
                                    </a>

                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('admin.courses.edit', $course) }}"
                                        class="p-1.5 min-w-[32px] min-h-[32px] inline-flex items-center justify-center text-slate-500 hover:text-[#FB6221] hover:bg-orange-50 rounded-lg transition-colors border border-transparent hover:border-orange-200"
                                        aria-label="Edit Course {{ $course->name }}"
                                        title="Edit Course"
                                    >
                                        <x-ui.icon name="pencil" size="sm" />
                                    </a>

                                    {{-- Public Preview --}}
                                    <a
                                        href="{{ $course->url }}"
                                        target="_blank"
                                        class="p-1.5 min-w-[32px] min-h-[32px] inline-flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors border border-transparent hover:border-slate-200"
                                        aria-label="Preview live listing for {{ $course->name }}"
                                        title="Preview Live Listing"
                                    >
                                        <x-ui.icon name="external-link" size="sm" />
                                    </a>

                                    {{-- Delete --}}
                                    @if ($course->slug !== 'sap-fico-training')
                                        <form
                                            action="{{ route('admin.courses.destroy', $course) }}"
                                            method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete \'{{ addslashes($course->name) }}\'? This action cannot be undone.')"
                                            class="inline-flex"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-1.5 min-w-[32px] min-h-[32px] inline-flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer border border-transparent hover:border-rose-200"
                                                aria-label="Delete Course {{ $course->name }}"
                                                title="Delete Course"
                                            >
                                                <x-ui.icon name="trash" size="sm" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-6 text-center">
                                <x-ui.empty-state
                                    title="No courses match your query"
                                    description="Try adjusting your search terms or filter selection."
                                    icon="book"
                                    :bordered="false"
                                >
                                    <x-ui.button href="{{ route('admin.courses.index') }}" variant="secondary" size="sm">
                                        Reset Filters
                                    </x-ui.button>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($courses->hasPages())
            <div class="p-4 sm:p-6 border-t border-slate-100 bg-slate-50/50">
                {{ $courses->links() }}
            </div>
        @endif
    </x-ui.card>

</div>
@endsection
