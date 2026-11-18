@extends('layouts.admin')

@section('title', 'Form Submissions & Inquiries | ZebLearn Admin')
@section('page-title', 'Form Submissions')

@section('content')
<div class="space-y-6">

    {{-- 1. PAGE HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Form Submissions</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                Centralized Form Submissions &amp; Leads
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Unified repository of all inbound enquiries, demo bookings, fee calculators, callbacks, and contact submissions.
            </p>
        </div>

        {{-- Export Action --}}
        <div class="flex items-center gap-3">
            <a
                href="{{ route('admin.form-submissions.export', request()->all()) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-mono font-bold uppercase tracking-wider shadow-2xs hover:border-slate-300 transition-all"
                title="Export filtered submissions to CSV"
            >
                <span class="material-symbols-outlined text-[18px] text-emerald-600">file_download</span>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    {{-- 2. STATUS METRIC CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        {{-- Total --}}
        <a
            href="{{ route('admin.form-submissions.index') }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ !request()->filled('status') || request('status') === 'all' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total</span>
            <span class="text-2xl font-black text-slate-900 font-heading mt-1 block">{{ number_format($counts['total']) }}</span>
        </a>

        {{-- New --}}
        <a
            href="{{ route('admin.form-submissions.index', array_merge(request()->except('status', 'page'), ['status' => 'new'])) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ request('status') === 'new' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <div class="flex items-center justify-between">
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">New</span>
                @if ($counts['new'] > 0)
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                @endif
            </div>
            <span class="text-2xl font-black text-emerald-600 font-heading mt-1 block">{{ number_format($counts['new']) }}</span>
        </a>

        {{-- Contacted --}}
        <a
            href="{{ route('admin.form-submissions.index', array_merge(request()->except('status', 'page'), ['status' => 'contacted'])) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ request('status') === 'contacted' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-blue-600">Contacted</span>
            <span class="text-2xl font-black text-blue-600 font-heading mt-1 block">{{ number_format($counts['contacted']) }}</span>
        </a>

        {{-- Qualified --}}
        <a
            href="{{ route('admin.form-submissions.index', array_merge(request()->except('status', 'page'), ['status' => 'qualified'])) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ request('status') === 'qualified' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Qualified</span>
            <span class="text-2xl font-black text-amber-600 font-heading mt-1 block">{{ number_format($counts['qualified']) }}</span>
        </a>

        {{-- Converted --}}
        <a
            href="{{ route('admin.form-submissions.index', array_merge(request()->except('status', 'page'), ['status' => 'converted'])) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ request('status') === 'converted' ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-purple-600">Converted</span>
            <span class="text-2xl font-black text-purple-600 font-heading mt-1 block">{{ number_format($counts['converted']) }}</span>
        </a>

        {{-- Rejected --}}
        <a
            href="{{ route('admin.form-submissions.index', array_merge(request()->except('status', 'page'), ['status' => 'rejected'])) }}"
            class="bg-white rounded-2xl p-4 border transition-all shadow-2xs {{ request('status') === 'rejected' ? 'border-rose-500 ring-2 ring-rose-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
        >
            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-rose-600">Rejected</span>
            <span class="text-2xl font-black text-rose-600 font-heading mt-1 block">{{ number_format($counts['rejected']) }}</span>
        </a>
    </div>

    {{-- 3. SEARCH & FILTERS --}}
    <x-ui.card padding="sm">
        <form method="GET" action="{{ route('admin.form-submissions.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">

            {{-- Left: Search & Dropdowns --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 flex-1">
                {{-- Keyword Search --}}
                <div class="relative">
                    <x-ui.icon name="search" size="sm" class="text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name, phone, email..."
                        class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden text-slate-800 placeholder-slate-400 font-sans"
                    />
                </div>

                {{-- Form Type Filter --}}
                <div>
                    <select
                        name="form_type"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] outline-hidden text-slate-800 font-sans cursor-pointer"
                    >
                        <option value="all">All Form Types</option>
                        @foreach ($formTypes as $typeKey => $typeLabel)
                            <option value="{{ $typeKey }}" {{ request('form_type') === $typeKey ? 'selected' : '' }}>
                                {{ $typeLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <select
                        name="status"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] outline-hidden text-slate-800 font-sans cursor-pointer"
                    >
                        <option value="all">All Statuses</option>
                        @foreach ($statuses as $statKey => $statLabel)
                            <option value="{{ $statKey }}" {{ request('status') === $statKey ? 'selected' : '' }}>
                                {{ $statLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Course Filter --}}
                <div>
                    <select
                        name="course_id"
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] outline-hidden text-slate-800 font-sans cursor-pointer"
                    >
                        <option value="all">All Courses</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Right: Date Range & Buttons --}}
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1">
                    <span class="text-[10px] font-mono uppercase text-slate-400 font-bold">From</span>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="text-xs bg-transparent border-0 p-0 text-slate-700 font-mono outline-hidden cursor-pointer"
                        title="From Date"
                    />
                    <span class="text-slate-300">|</span>
                    <span class="text-[10px] font-mono uppercase text-slate-400 font-bold">To</span>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="text-xs bg-transparent border-0 p-0 text-slate-700 font-mono outline-hidden cursor-pointer"
                        title="To Date"
                    />
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 py-2 px-3.5 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-mono font-bold uppercase transition-colors cursor-pointer shadow-2xs shrink-0"
                    title="Apply Filters"
                >
                    <x-ui.icon name="filter_list" size="xs" class="shrink-0" />
                    <span>Filter</span>
                </button>

                @if (request()->hasAny(['search', 'form_type', 'status', 'course_id', 'date_from', 'date_to']))
                    <a
                        href="{{ route('admin.form-submissions.index') }}"
                        class="inline-flex items-center gap-1 py-2 px-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs transition-colors shrink-0"
                        title="Clear Filters"
                    >
                        <x-ui.icon name="close" size="xs" />
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                @endif
            </div>

        </form>
    </x-ui.card>

    {{-- 4. SUBMISSIONS DATA TABLE --}}
    <x-ui.card padding="none">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-mono uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4 font-bold text-center w-12">#</th>
                        <th class="py-3.5 px-4 font-bold">Contact / Inquirer</th>
                        <th class="py-3.5 px-4 font-bold">Form Type</th>
                        <th class="py-3.5 px-4 font-bold">Target Course</th>
                        <th class="py-3.5 px-4 font-bold">Message / Details</th>
                        <th class="py-3.5 px-4 font-bold">Submitted Date</th>
                        <th class="py-3.5 px-4 font-bold text-center">Status</th>
                        <th class="py-3.5 px-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($submissions as $sub)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            {{-- ID --}}
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-400 text-center">
                                {{ $sub->id }}
                            </td>

                            {{-- Lead Contact --}}
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900 text-xs">
                                    {{ $sub->name ?: 'Anonymous Visitor' }}
                                </div>
                                <div class="flex flex-col gap-0.5 mt-0.5 font-mono text-[11px] text-slate-500">
                                    @if ($sub->phone)
                                        <a href="tel:{{ $sub->phone }}" class="hover:text-[#FB6221] inline-flex items-center gap-1.5 text-slate-700">
                                            <x-ui.icon name="call" size="xs" class="text-slate-400 shrink-0" />
                                            <span>{{ $sub->phone }}</span>
                                        </a>
                                    @endif
                                    @if ($sub->email)
                                        <a href="mailto:{{ $sub->email }}" class="hover:text-[#FB6221] inline-flex items-center gap-1.5 text-slate-500">
                                            <x-ui.icon name="mail" size="xs" class="text-slate-400 shrink-0" />
                                            <span class="truncate max-w-[180px]">{{ $sub->email }}</span>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            {{-- Form Type --}}
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-sans font-semibold border {{ $sub->form_type_badge_class }}">
                                    {{ $sub->form_type_label }}
                                </span>
                                @if ($sub->source)
                                    <span class="block text-[10px] font-mono text-slate-400 mt-1 truncate max-w-[140px]" title="Source: {{ $sub->source }}">
                                        src: {{ $sub->source }}
                                    </span>
                                @endif
                            </td>

                            {{-- Course --}}
                            <td class="py-3 px-4">
                                @if ($sub->course)
                                    <a href="{{ route('courses.show', $sub->course) }}" target="_blank" class="font-bold text-slate-900 hover:text-[#FB6221] block leading-snug">
                                        {{ $sub->course->name }}
                                    </a>
                                @elseif ($sub->course_name)
                                    <span class="font-medium text-slate-800 block leading-snug">
                                        {{ $sub->course_name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Not specified</span>
                                @endif
                            </td>

                            {{-- Message / Additional Data Snippet --}}
                            <td class="py-3 px-4 max-w-[240px]">
                                @if ($sub->message)
                                    <p class="text-slate-600 line-clamp-2 leading-relaxed" title="{{ $sub->message }}">
                                        {{ $sub->message }}
                                    </p>
                                @elseif (!empty($sub->additional_data))
                                    <div class="text-[11px] font-mono text-slate-500 line-clamp-2">
                                        @foreach (array_slice($sub->additional_data, 0, 2) as $k => $v)
                                            <span class="font-semibold text-slate-700">{{ ucwords(str_replace('_', ' ', $k)) }}:</span>
                                            <span>{{ is_array($v) ? json_encode($v) : $v }}; </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No message</span>
                                @endif
                            </td>

                            {{-- Submitted Date --}}
                            <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                <div>{{ $sub->submitted_at ? $sub->submitted_at->format('M d, Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $sub->submitted_at ? $sub->submitted_at->format('h:i A') : '' }}</div>
                            </td>

                            {{-- Status Badge & Quick Update --}}
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.form-submissions.status', $sub) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select
                                        name="status"
                                        onchange="this.form.submit()"
                                        class="text-[10px] font-mono font-bold uppercase tracking-wider px-2 py-1 rounded-full border cursor-pointer outline-hidden transition-all {{ $sub->status_badge_class }}"
                                    >
                                        @foreach ($statuses as $statKey => $statLabel)
                                            <option value="{{ $statKey }}" {{ $sub->status === $statKey ? 'selected' : '' }}>
                                                {{ $statLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a
                                        href="{{ route('admin.form-submissions.show', $sub) }}"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 hover:border-slate-300 text-slate-600 hover:text-[#FB6221] transition-colors shadow-2xs"
                                        title="View Full Submission Details"
                                    >
                                        <x-ui.icon name="visibility" size="sm" class="shrink-0" />
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.form-submissions.destroy', $sub) }}"
                                        onsubmit="return confirm('Are you sure you want to permanently delete this submission record?');"
                                        class="inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 transition-colors cursor-pointer shadow-2xs"
                                            title="Delete Record"
                                        >
                                            <x-ui.icon name="delete" size="sm" class="shrink-0" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500 font-sans">
                                <div class="flex flex-col items-center justify-center">
                                    <x-ui.icon name="inbox" size="xl" class="text-slate-300 mb-2" />
                                    <p class="font-bold text-slate-700 text-sm">No form submissions found.</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Try clearing active search or filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Crawlable Pagination --}}
        @if ($submissions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $submissions->links() }}
            </div>
        @endif
    </x-ui.card>

</div>
@endsection

