@extends('layouts.admin')

@section('title', 'Instructor Management | ZebLearn LMS')
@section('page-title', 'Instructors')

@section('content')
<div class="space-y-6">
    {{-- PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Instructors</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Instructor &amp; Faculty Directory
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Manage academy teaching staff, professional credentials, and course assignments.
            </p>
        </div>

        <div>
            <x-ui.button
                href="{{ route('admin.instructors.create') }}"
                variant="primary"
                size="md"
                icon="plus"
            >
                Add Instructor
            </x-ui.button>
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

    {{-- STATS & QUICK FILTER CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a
            href="{{ route('admin.instructors.index') }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ empty($currentStatus) ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Faculty</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $totalInstructors }}</span>
            <span class="text-xs text-slate-500 mt-0.5 block">Registered instructors</span>
        </a>

        <a
            href="{{ route('admin.instructors.index', ['status' => 'active']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $currentStatus === 'active' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Active Instructors</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $activeInstructors }}</span>
            <span class="text-xs text-slate-500 mt-0.5 block">Authorized to teach</span>
        </a>

        <a
            href="{{ route('admin.instructors.index', ['status' => 'suspended']) }}"
            class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $currentStatus === 'suspended' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Suspended</span>
            <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $suspendedInstructors }}</span>
            <span class="text-xs text-slate-500 mt-0.5 block">Access restricted</span>
        </a>
    </div>

    {{-- SEARCH & FILTER TOOLBAR --}}
    <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs">
        <form method="GET" action="{{ route('admin.instructors.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6 relative">
                <input
                    type="text"
                    name="search"
                    value="{{ $currentSearch }}"
                    placeholder="Search by instructor name, email, designation, or specialization..."
                    class="w-full pl-9 pr-4 py-2 rounded-xl text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >
                <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                    <x-ui.icon name="magnifying-glass" size="xs" />
                </div>
            </div>

            <div class="sm:col-span-4">
                <select
                    name="status"
                    class="w-full px-3 py-2 rounded-xl text-xs sm:text-sm bg-slate-50 border border-slate-200 focus:bg-white focus:outline-hidden focus:border-[#FB6221] transition-all"
                >
                    <option value="">All Account Statuses</option>
                    <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="suspended" {{ $currentStatus === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-center gap-2">
                <x-ui.button type="submit" variant="dark" size="sm" class="w-full">
                    Filter
                </x-ui.button>
                @if ($currentSearch || $currentStatus)
                    <a
                        href="{{ route('admin.instructors.index') }}"
                        class="p-2 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors"
                        title="Reset Filters"
                    >
                        <x-ui.icon name="x-mark" size="sm" />
                    </a>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- INSTRUCTORS TABLE --}}
    <x-ui.card padding="none" class="border-slate-200/90 shadow-2xs overflow-hidden">
        @if ($instructors->isEmpty())
            <div class="p-8">
                <x-ui.empty-state
                    title="No Instructors Found"
                    description="No instructor profiles match your current search criteria. Try clearing the filter or add a new instructor."
                    icon="user-group"
                >
                    <x-slot:action>
                        <x-ui.button href="{{ route('admin.instructors.create') }}" variant="primary" size="sm">
                            Add New Instructor
                        </x-ui.button>
                    </x-slot:action>
                </x-ui.empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-mono text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 sm:px-6">Instructor</th>
                            <th class="py-3.5 px-4 sm:px-6">Designation &amp; Expertise</th>
                            <th class="py-3.5 px-4 sm:px-6">Assigned Courses</th>
                            <th class="py-3.5 px-4 sm:px-6">Status</th>
                            <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($instructors as $inst)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-4 sm:px-6">
                                    <div class="flex items-center gap-3">
                                        @if ($inst->avatarUrl())
                                            <img src="{{ $inst->avatarUrl() }}" alt="{{ $inst->name }}" class="w-10 h-10 rounded-full object-cover border border-slate-200">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-slate-800 text-white flex items-center justify-center font-heading font-bold text-sm shrink-0 border border-slate-700">
                                                {{ strtoupper(substr($inst->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.instructors.show', $inst) }}" class="font-bold text-slate-900 hover:text-[#FB6221] transition-colors font-heading block truncate">
                                                {{ $inst->name }}
                                            </a>
                                            <span class="text-xs text-slate-500 block truncate">{{ $inst->email }}</span>
                                            @if ($inst->phone)
                                                <span class="text-[11px] font-mono text-slate-400 block">{{ $inst->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    <div class="space-y-0.5">
                                        <span class="font-semibold text-slate-900 block truncate">
                                            {{ $inst->designation ?: 'Faculty Member' }}
                                        </span>
                                        <span class="text-xs text-slate-500 block truncate">
                                            {{ $inst->specialization ?: 'General Instruction' }}
                                        </span>
                                        @if ($inst->experience_years)
                                            <span class="text-[11px] font-mono text-slate-400 block">
                                                {{ $inst->experience_years }} yrs experience
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-mono font-bold {{ $inst->assigned_courses_count > 0 ? 'bg-orange-50 text-[#FB6221] border border-orange-200' : 'bg-slate-100 text-slate-500' }}">
                                        <x-ui.icon name="book" size="xs" />
                                        <span>{{ $inst->assigned_courses_count }} {{ Str::plural('Course', $inst->assigned_courses_count) }}</span>
                                    </span>
                                </td>

                                <td class="py-4 px-4 sm:px-6">
                                    @if ($inst->isActive())
                                        <x-ui.badge variant="success" size="xs" dot>Active</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" size="xs" dot>Suspended</x-ui.badge>
                                    @endif
                                </td>

                                <td class="py-4 px-4 sm:px-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <x-ui.button
                                            href="{{ route('admin.instructors.show', $inst) }}"
                                            variant="secondary"
                                            size="xs"
                                        >
                                            View
                                        </x-ui.button>

                                        <x-ui.button
                                            href="{{ route('admin.instructors.edit', $inst) }}"
                                            variant="ghost"
                                            size="xs"
                                        >
                                            Edit
                                        </x-ui.button>

                                        <form method="POST" action="{{ route('admin.instructors.toggle-status', $inst) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                onclick="return confirm('Are you sure you want to {{ $inst->isActive() ? 'suspend' : 'activate' }} {{ $inst->name }}?')"
                                                class="px-2 py-1 rounded-md text-xs font-semibold {{ $inst->isActive() ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} transition-colors"
                                            >
                                                {{ $inst->isActive() ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($instructors->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $instructors->links() }}
                </div>
            @endif
        @endif
    </x-ui.card>
</div>
@endsection

