@extends('layouts.admin')

@section('title', 'Student Enrollment Approvals | ZebLearn LMS')
@section('page-title', 'Enrollments')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Enrollments</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Student Enrollment Approvals
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Review, approve, or decline student enrollment requests across all academy courses.
            </p>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
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

    {{-- STATS & QUICK FILTER TABS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a
            href="{{ route('admin.enrollments.index') }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'all' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Requests</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['total'] }}</span>
        </a>

        <a
            href="{{ route('admin.enrollments.index', ['status' => 'pending']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'pending' ? 'border-amber-400 ring-2 ring-amber-400/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Pending Review</span>
                @if ($stats['pending'] > 0)
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                @endif
            </div>
            <span class="text-2xl sm:text-3xl font-black text-amber-600 font-heading mt-1 block">{{ $stats['pending'] }}</span>
        </a>

        <a
            href="{{ route('admin.enrollments.index', ['status' => 'approved']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Approved</span>
            <span class="text-2xl sm:text-3xl font-black text-emerald-600 font-heading mt-1 block">{{ $stats['approved'] }}</span>
        </a>

        <a
            href="{{ route('admin.enrollments.index', ['status' => 'rejected']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'rejected' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-rose-600">Declined</span>
            <span class="text-2xl sm:text-3xl font-black text-rose-600 font-heading mt-1 block">{{ $stats['rejected'] }}</span>
        </a>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <x-ui.card padding="sm">
        <form method="GET" action="{{ route('admin.enrollments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            {{-- Search Input --}}
            <div class="sm:col-span-5 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <x-ui.icon name="search" size="sm" />
                </div>
                <input
                    type="text"
                    name="search"
                    aria-label="Search enrollments by student name, email, or course"
                    value="{{ $search }}"
                    placeholder="Search by student name, email, or course..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/80 pl-10 pr-4 py-2.5 text-xs text-slate-900 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden font-sans"
                />
            </div>

            {{-- Course Filter --}}
            <div class="sm:col-span-4">
                <select
                    name="course_id"
                    aria-label="Filter enrollments by course"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 text-xs text-slate-700 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden font-medium cursor-pointer"
                >
                    <option value="all">All Courses ({{ $courses->count() }})</option>
                    @foreach ($courses as $c)
                        <option value="{{ $c->id }}" {{ (string)$activeCourse === (string)$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="sm:col-span-2">
                <select
                    name="status"
                    aria-label="Filter enrollments by status"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50/80 px-3.5 py-2.5 text-xs text-slate-700 focus:bg-white focus:border-[#FB6221] focus:ring-3 focus:ring-[#FB6221]/15 transition-all outline-hidden font-medium cursor-pointer"
                >
                    <option value="all" {{ $activeStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                    <option value="pending" {{ $activeStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $activeStatus === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $activeStatus === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ $activeStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Submit / Reset --}}
            <div class="sm:col-span-1 flex items-center gap-2">
                <x-ui.button
                    type="submit"
                    variant="dark"
                    size="sm"
                    fullWidth
                    icon="filter"
                    title="Apply Filters"
                >
                    Filter
                </x-ui.button>
            </div>
        </form>
    </x-ui.card>

    {{-- ENROLLMENTS TABLE --}}
    <x-ui.card padding="none">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-6">Student</th>
                        <th class="py-3.5 px-6">Course</th>
                        <th class="py-3.5 px-6">Requested</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Reviewed By</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($enrollments as $enrollment)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            {{-- Student --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs font-heading shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($enrollment->user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 font-heading text-sm">
                                            {{ $enrollment->user->name }}
                                        </div>
                                        <div class="text-xs text-slate-500 font-mono">
                                            {{ $enrollment->user->email }}
                                        </div>
                                        @if ($enrollment->user->phone)
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                {{ $enrollment->user->phone }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Course --}}
                            <td class="py-4 px-6">
                                <a
                                    href="{{ route('admin.courses.edit', $enrollment->course) }}"
                                    class="font-semibold text-slate-900 hover:text-[#FB6221] transition-colors"
                                >
                                    {{ $enrollment->course->name }}
                                </a>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $enrollment->course->category_badge }} • {{ $enrollment->course->display_price }}
                                </div>
                            </td>

                            {{-- Requested Date --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">
                                    {{ $enrollment->requested_at?->format('d M Y') ?? '—' }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $enrollment->requested_at?->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if ($enrollment->isApproved())
                                    <x-ui.badge variant="success" size="xs" dot>
                                        Approved
                                    </x-ui.badge>
                                @elseif ($enrollment->isPending())
                                    <x-ui.badge variant="warning" size="xs" dot dotPulse>
                                        Pending Review
                                    </x-ui.badge>
                                @elseif ($enrollment->isRejected())
                                    <x-ui.badge variant="danger" size="xs">
                                        Declined
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral" size="xs">
                                        {{ ucfirst($enrollment->status) }}
                                    </x-ui.badge>
                                @endif
                            </td>

                            {{-- Reviewed By --}}
                            <td class="py-4 px-6 whitespace-nowrap text-slate-600">
                                @if ($enrollment->reviewer)
                                    <div class="font-medium text-slate-900">{{ $enrollment->reviewer->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        {{ $enrollment->approved_at?->format('d M Y') ?? $enrollment->rejected_at?->format('d M Y') }}
                                    </div>
                                @else
                                    <span class="text-slate-400 font-mono">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Inspect Details --}}
                                    <a
                                        href="{{ route('admin.enrollments.show', $enrollment) }}"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                        title="View Details"
                                    >
                                        <x-ui.icon name="eye" size="xs" />
                                    </a>

                                    @if ($enrollment->isPending())
                                        {{-- Quick Approve --}}
                                        <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors cursor-pointer"
                                                title="Approve Enrollment"
                                                onclick="return confirm('Approve enrollment for {{ addslashes($enrollment->user->name) }} in {{ addslashes($enrollment->course->name) }}?')"
                                            >
                                                Approve
                                            </button>
                                        </form>

                                        {{-- Quick Reject --}}
                                        <form action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="px-2.5 py-1 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer"
                                                title="Decline Enrollment"
                                                onclick="return confirm('Decline enrollment for {{ addslashes($enrollment->user->name) }}?')"
                                            >
                                                Decline
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-slate-500">
                                <x-ui.empty-state
                                    title="No enrollment requests found"
                                    description="Try adjusting your search criteria or filter status."
                                    icon="users"
                                    :bordered="false"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($enrollments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $enrollments->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
@endsection
