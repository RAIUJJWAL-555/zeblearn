@extends('layouts.admin')

@section('title', 'Submission #' . $formSubmission->id . ' Details | ZebLearn Admin')
@section('page-title', 'Form Submission Details')

@section('content')
<div class="space-y-6 max-w-6xl">

    {{-- 1. BREADCRUMBS & HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.form-submissions.index') }}" class="hover:text-[#FB6221] transition-colors">Form Submissions</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">#{{ $formSubmission->id }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                    {{ $formSubmission->name ?: 'Anonymous Submission' }}
                </h1>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider border {{ $formSubmission->status_badge_class }}">
                    {{ strtoupper($formSubmission->status) }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-mono">
                {{ $formSubmission->form_name }} &bull; Submitted {{ $formSubmission->submitted_at ? $formSubmission->submitted_at->format('M d, Y \a\t h:i A') : '-' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a
                href="{{ route('admin.form-submissions.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-mono font-bold uppercase tracking-wider transition-all"
            >
                &larr; Back to List
            </a>

            <form
                method="POST"
                action="{{ route('admin.form-submissions.destroy', $formSubmission) }}"
                onsubmit="return confirm('Are you sure you want to permanently delete this submission record?');"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="p-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs transition-colors cursor-pointer inline-flex items-center justify-center"
                    title="Delete Submission"
                >
                    <x-ui.icon name="delete" size="sm" />
                </button>
            </form>
        </div>
    </div>

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <x-ui.alert variant="success" dismissible>
            {{ session('success') }}
        </x-ui.alert>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- 2. MAIN COLUMN: DETAILS (8 COLS) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Personal Information Card --}}
            <x-ui.card>
                <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-[#FB6221] text-[20px]">person</span>
                    <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-slate-900">
                        Lead &amp; Contact Information
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Full Name</span>
                        <span class="font-semibold text-slate-900 text-sm mt-0.5 block">
                            {{ $formSubmission->name ?: 'Not provided' }}
                        </span>
                    </div>

                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Phone Number</span>
                        @if ($formSubmission->phone)
                            <a href="tel:{{ $formSubmission->phone }}" class="font-mono text-sm font-bold text-slate-900 hover:text-[#FB6221] flex items-center gap-1 mt-0.5">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600">call</span>
                                <span>{{ $formSubmission->phone }}</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Not provided</span>
                        @endif
                    </div>

                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Email Address</span>
                        @if ($formSubmission->email)
                            <a href="mailto:{{ $formSubmission->email }}" class="font-mono text-xs text-slate-900 hover:text-[#FB6221] flex items-center gap-1 mt-0.5">
                                <span class="material-symbols-outlined text-[16px] text-blue-600">mail</span>
                                <span>{{ $formSubmission->email }}</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Not provided</span>
                        @endif
                    </div>

                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">WhatsApp Contact</span>
                        @if ($formSubmission->whatsapp_number)
                            <a href="https://wa.me/{{ preg_replace('/[^\d]/', '', $formSubmission->whatsapp_number) }}" target="_blank" class="font-mono text-xs text-slate-900 hover:text-emerald-600 flex items-center gap-1 mt-0.5">
                                <span class="material-symbols-outlined text-[16px] text-emerald-500">chat</span>
                                <span>{{ $formSubmission->whatsapp_number }}</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Same as phone</span>
                        @endif
                    </div>

                    @if ($formSubmission->company_name)
                        <div class="sm:col-span-2">
                            <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Company / Organization</span>
                            <span class="font-medium text-slate-800 text-xs mt-0.5 block">
                                {{ $formSubmission->company_name }}
                            </span>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            {{-- Course Interest Card --}}
            <x-ui.card>
                <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-[#FB6221] text-[20px]">school</span>
                    <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-slate-900">
                        Associated Program &amp; Module
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Course Name</span>
                        @if ($formSubmission->course)
                            <a href="{{ route('courses.show', $formSubmission->course) }}" target="_blank" class="font-bold text-sm text-[#FB6221] hover:underline flex items-center gap-1 mt-0.5">
                                <span>{{ $formSubmission->course->name }}</span>
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            </a>
                        @elseif ($formSubmission->course_name)
                            <span class="font-bold text-sm text-slate-900 mt-0.5 block">
                                {{ $formSubmission->course_name }}
                            </span>
                        @else
                            <span class="text-slate-400 italic">General Website Inquiry</span>
                        @endif
                    </div>

                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Course Category / ID</span>
                        <span class="font-mono text-xs text-slate-600 mt-0.5 block">
                            {{ $formSubmission->course ? $formSubmission->course->category_badge : 'N/A' }} (ID: {{ $formSubmission->course_id ?: 'None' }})
                        </span>
                    </div>
                </div>
            </x-ui.card>

            {{-- Message Content (If submitted) --}}
            @if ($formSubmission->message)
                <x-ui.card>
                    <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-100">
                        <span class="material-symbols-outlined text-[#FB6221] text-[20px]">chat</span>
                        <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-slate-900">
                            Submitted Query / Message
                        </h2>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs sm:text-sm text-slate-800 leading-relaxed font-sans whitespace-pre-wrap">
                        {{ $formSubmission->message }}
                    </div>
                </x-ui.card>
            @endif

            {{-- Additional Form Specific Fields (JSON Payload) --}}
            @if (!empty($formSubmission->additional_data))
                <x-ui.card>
                    <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-100">
                        <span class="material-symbols-outlined text-[#FB6221] text-[20px]">tune</span>
                        <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-slate-900">
                            Custom Form Fields &amp; Parameters
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        @foreach ($formSubmission->additional_data as $fieldKey => $fieldVal)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="font-mono text-[10px] uppercase font-bold text-slate-400 block">
                                    {{ ucwords(str_replace(['_', '-'], ' ', $fieldKey)) }}
                                </span>
                                <span class="font-sans font-semibold text-slate-900 text-xs mt-1 block">
                                    @if (is_array($fieldVal) || is_object($fieldVal))
                                        <pre class="font-mono text-[10px] mt-1 overflow-x-auto bg-white p-2 rounded border border-slate-200">{{ json_encode($fieldVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    @elseif (is_bool($fieldVal))
                                        {{ $fieldVal ? 'Yes' : 'No' }}
                                    @else
                                        {{ (string) $fieldVal }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            {{-- Attribution & Technical Telemetry --}}
            <x-ui.card>
                <div class="flex items-center gap-2 pb-4 mb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-[#FB6221] text-[20px]">explore</span>
                    <h2 class="font-heading text-sm font-bold uppercase tracking-wider text-slate-900">
                        Attribution &amp; Source Tracking
                    </h2>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Origin Page URL</span>
                        @if ($formSubmission->page_url)
                            <a href="{{ $formSubmission->page_url }}" target="_blank" class="font-mono text-[11px] text-[#FB6221] hover:underline break-all flex items-center gap-1 mt-0.5">
                                <span>{{ $formSubmission->page_url }}</span>
                                <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                            </a>
                        @else
                            <span class="text-slate-400 italic">Not available</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        <div>
                            <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">Source</span>
                            <span class="font-mono text-xs text-slate-800 mt-0.5 block">{{ $formSubmission->source ?: 'direct' }}</span>
                        </div>
                        <div>
                            <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">UTM Source</span>
                            <span class="font-mono text-xs text-slate-800 mt-0.5 block">{{ $formSubmission->utm_source ?: 'none' }}</span>
                        </div>
                        <div>
                            <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">UTM Medium</span>
                            <span class="font-mono text-xs text-slate-800 mt-0.5 block">{{ $formSubmission->utm_medium ?: 'none' }}</span>
                        </div>
                        <div>
                            <span class="block font-mono text-[10px] uppercase font-bold text-slate-400">UTM Campaign</span>
                            <span class="font-mono text-xs text-slate-800 mt-0.5 block">{{ $formSubmission->utm_campaign ?: 'none' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px] font-mono text-slate-500">
                        <div>
                            <span class="font-bold text-slate-600">IP Address:</span> {{ $formSubmission->ip_address ?: 'Unknown' }}
                        </div>
                        <div class="truncate" title="{{ $formSubmission->user_agent }}">
                            <span class="font-bold text-slate-600">User Agent:</span> {{ $formSubmission->user_agent ?: 'Unknown' }}
                        </div>
                    </div>
                </div>
            </x-ui.card>

        </div>

        {{-- 3. SIDEBAR: STATUS & NOTES (4 COLS) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- Status Update Card --}}
            <x-ui.card>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-[#FB6221] text-[18px]">flag</span>
                    <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-slate-900">
                        Lifecycle Status
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.form-submissions.status', $formSubmission) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="status-select" class="block text-[11px] font-mono uppercase font-bold text-slate-600 mb-1.5">
                            Update Lead Status
                        </label>
                        <select
                            id="status-select"
                            name="status"
                            class="w-full px-3 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] outline-hidden text-slate-800 font-medium font-sans cursor-pointer"
                        >
                            @foreach ($statuses as $k => $label)
                                <option value="{{ $k }}" {{ $formSubmission->status === $k ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-black text-white text-xs font-mono font-bold uppercase tracking-wider transition-colors cursor-pointer shadow-2xs"
                    >
                        Save Status
                    </button>
                </form>
            </x-ui.card>

            {{-- Admin Notes Card --}}
            <x-ui.card>
                <div class="flex items-center gap-2 pb-3 mb-4 border-b border-slate-100">
                    <span class="material-symbols-outlined text-[#FB6221] text-[18px]">note_alt</span>
                    <h3 class="font-heading text-xs font-bold uppercase tracking-wider text-slate-900">
                        Internal Counselor Notes
                    </h3>
                </div>

                <form method="POST" action="{{ route('admin.form-submissions.notes', $formSubmission) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="admin-notes" class="block text-[11px] font-mono uppercase font-bold text-slate-600 mb-1.5">
                            Staff / Counselor Notes
                        </label>
                        <textarea
                            id="admin-notes"
                            name="admin_notes"
                            rows="6"
                            placeholder="Add notes about call discussion, batch preference, fee discussed, follow-up timeline..."
                            class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden text-slate-800 placeholder-slate-400 font-sans"
                        >{{ old('admin_notes', $formSubmission->admin_notes) }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-mono font-bold uppercase tracking-wider transition-all cursor-pointer shadow-md"
                    >
                        Save Admin Notes
                    </button>
                </form>
            </x-ui.card>

            {{-- Metadata Summary Card --}}
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-[11px] font-mono text-slate-500 space-y-2">
                <div class="flex justify-between">
                    <span>Record ID:</span>
                    <strong class="text-slate-900">#{{ $formSubmission->id }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Submitted:</span>
                    <span class="text-slate-700">{{ $formSubmission->submitted_at ? $formSubmission->submitted_at->diffForHumans() : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Last Updated:</span>
                    <span class="text-slate-700">{{ $formSubmission->updated_at->diffForHumans() }}</span>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

