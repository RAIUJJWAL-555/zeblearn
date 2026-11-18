@extends('layouts.admin')

@section('title', 'Validate & Preview Course CSV | ZebLearn LMS')
@section('page-title', 'Course CSV Import Preview')

@section('content')
<div class="space-y-8">

    {{-- Header & Breadcrumbs --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-800 transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.courses.index') }}" class="hover:text-slate-800 transition">Courses</a>
                <span>/</span>
                <a href="{{ route('admin.courses.import.index') }}" class="hover:text-slate-800 transition">CSV Import</a>
                <span>/</span>
                <span class="text-[#FB6221] font-bold">Preview</span>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-heading tracking-tight">
                    Pre-Import Course Preview
                </h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-slate-100 text-slate-700">
                    File: {{ $filename }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                Review the parsed course fields, discount calculations, Markdown descriptions, cohorts, FAQs, modules, and trust settings before committing to the database.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <x-ui.button
                href="{{ route('admin.courses.import.index') }}"
                variant="secondary"
                size="md"
            >
                Upload Different File
            </x-ui.button>
        </div>
    </div>

    {{-- Validation Summary Banner --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Total Courses in CSV</p>
            <p class="text-2xl font-black text-slate-900 mt-1 font-heading">{{ $preview['total_rows'] }}</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-emerald-600">Valid &amp; Ready</p>
            <p class="text-2xl font-black text-emerald-600 mt-1 font-heading">{{ $preview['valid_rows'] }}</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-rose-600">Rows with Errors</p>
            <p class="text-2xl font-black text-rose-600 mt-1 font-heading">{{ $preview['invalid_rows'] }}</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
            <p class="text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Existing Slugs</p>
            @php
                $existingCount = collect($preview['courses'])->filter(fn($c) => !empty($c['data']['_is_update']))->count();
            @endphp
            <p class="text-2xl font-black text-amber-600 mt-1 font-heading">{{ $existingCount }}</p>
        </div>
    </div>

    {{-- Global Confirmation Form Form --}}
    <form action="{{ route('admin.courses.import.process') }}" method="POST">
        @csrf

        {{-- Execution Controls Card --}}
        <x-ui.card
            title="Import Execution Controls"
            subtitle="Configure publishing behavior and duplicate handling before final database transaction"
            padding="md"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                {{-- Publishing Strategy --}}
                <div>
                    <label class="block text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Publishing Strategy
                    </label>
                    <select
                        name="publish_override"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 font-medium focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15 transition-all outline-hidden cursor-pointer"
                    >
                        <option value="default">Respect CSV is_published flag (Default)</option>
                        <option value="draft">Import All as Draft / Roadmap (Keep hidden until reviewed)</option>
                        <option value="publish">Publish All Immediately (Make live on public website)</option>
                    </select>
                </div>

                {{-- Update Existing Option --}}
                <div class="pt-2">
                    <label class="flex items-start gap-2.5 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            name="update_existing"
                            value="1"
                            @checked($updateExisting)
                            class="w-4 h-4 rounded text-[#FB6221] focus:ring-[#FB6221] border-slate-300 cursor-pointer mt-0.5"
                        />
                        <div class="text-xs">
                            <span class="font-bold text-slate-900">Update Existing Courses (Overwrite)</span>
                            <span class="text-slate-500 block text-[11px] mt-0.5">
                                When checked, existing courses with matching slugs will have their details, pricing, cohorts, FAQs, modules, and trust settings updated.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between pt-5 mt-5 border-t border-slate-100">
                <div class="text-xs text-slate-500 font-mono">
                    Transactions are atomic per course. Any failed course will roll back cleanly without partial records.
                </div>

                <div class="flex items-center gap-3">
                    <x-ui.button
                        href="{{ route('admin.courses.import.index') }}"
                        variant="secondary"
                        size="md"
                    >
                        Back
                    </x-ui.button>

                    <x-ui.button
                        type="submit"
                        variant="primary"
                        size="md"
                        icon="cloud_upload"
                        :disabled="$preview['valid_rows'] === 0"
                    >
                        Confirm &amp; Import {{ $preview['valid_rows'] }} Course(s)
                    </x-ui.button>
                </div>
            </div>
        </x-ui.card>

        {{-- Course Cards List --}}
        <div class="space-y-6 mt-8">
            @foreach ($preview['courses'] as $cIdx => $cItem)
                @php
                    $d = $cItem['data'];
                    $isValid = $cItem['is_valid'];
                    $hasWarnings = !empty($cItem['warnings']);
                    $hasErrors = !empty($cItem['errors']);
                @endphp

                <div
                    x-data="{ activeTab: 'overview' }"
                    class="rounded-2xl border {{ $hasErrors ? 'border-rose-300 bg-rose-50/20' : ($hasWarnings ? 'border-amber-300 bg-white' : 'border-slate-200 bg-white') }} shadow-2xs overflow-hidden"
                >
                    {{-- Header Bar --}}
                    <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                        <div class="flex items-start gap-3">
                            <span class="w-8 h-8 rounded-full {{ $hasErrors ? 'bg-rose-100 text-rose-700' : ($hasWarnings ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700') }} font-mono font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                {{ $cIdx + 1 }}
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-base font-bold text-slate-900 font-heading">
                                        {{ $d['name'] ?: 'Untitled Course' }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-mono bg-slate-100 text-slate-700">
                                        /courses/{{ $d['slug'] }}
                                    </span>
                                    @if ($d['_is_update'])
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Existing Course (Will Update)
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            New Course
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-slate-500 font-mono">
                                    <span>Category: <strong>{{ $d['category'] }}</strong></span>
                                    <span>•</span>
                                    <span>Status: <strong>{{ ucfirst($d['status']) }}</strong></span>
                                    <span>•</span>
                                    <span>Line: {{ $cItem['line_number'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Status Pill --}}
                        <div class="flex items-center gap-2 shrink-0">
                            @if ($hasErrors)
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-rose-100 text-rose-800 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    <span>{{ count($cItem['errors']) }} Error(s)</span>
                                </span>
                            @elseif ($hasWarnings)
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-100 text-amber-800 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">warning</span>
                                    <span>{{ count($cItem['warnings']) }} Notice(s)</span>
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    <span>Validated</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Errors & Warnings Banners --}}
                    @if ($hasErrors)
                        <div class="p-4 bg-rose-50 border-b border-rose-200 text-xs text-rose-800 space-y-1">
                            <p class="font-bold font-mono uppercase tracking-wider">Validation Errors (Row cannot be imported):</p>
                            <ul class="list-disc pl-5 space-y-0.5">
                                @foreach ($cItem['errors'] as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($hasWarnings)
                        <div class="p-4 bg-amber-50 border-b border-amber-200 text-xs text-amber-800 space-y-1">
                            <p class="font-bold font-mono uppercase tracking-wider">Notices / Adjustments:</p>
                            <ul class="list-disc pl-5 space-y-0.5">
                                @foreach ($cItem['warnings'] as $warn)
                                    <li>{{ $warn }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Navigation Tabs --}}
                    <div class="px-5 pt-3 border-b border-slate-200 bg-white flex flex-wrap gap-2 text-xs font-mono">
                        <button
                            type="button"
                            @click="activeTab = 'overview'"
                            :class="activeTab === 'overview' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            Overview &amp; Identity
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'pricing'"
                            :class="activeTab === 'pricing' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            Pricing &amp; Logistics
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'markdown'"
                            :class="activeTab === 'markdown' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            Markdown Content Preview
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'cohorts'"
                            :class="activeTab === 'cohorts' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            Cohorts ({{ count($d['cohorts']) }})
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'faqs'"
                            :class="activeTab === 'faqs' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            FAQs ({{ count($d['faqs']) }})
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'curriculum'"
                            :class="activeTab === 'curriculum' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            Modules &amp; Lessons ({{ count($d['modules']) }} mods, {{ count($d['lessons']) }} lessons)
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'trust'"
                            :class="activeTab === 'trust' ? 'border-[#FB6221] text-[#FB6221] font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
                            class="pb-2.5 border-b-2 transition-all cursor-pointer"
                        >
                            Trust Controls &amp; SEO
                        </button>
                    </div>

                    {{-- Tab Contents --}}
                    <div class="p-5">
                        {{-- TAB 1: OVERVIEW & IDENTITY --}}
                        <div x-show="activeTab === 'overview'" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Course Name</span>
                                    <p class="font-bold text-slate-900 mt-0.5 text-sm">{{ $d['name'] }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Public URL Slug</span>
                                    <p class="font-mono font-bold text-slate-900 mt-0.5">{{ $d['slug'] }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Subject Category</span>
                                    <p class="font-bold text-slate-900 mt-0.5">{{ $d['category'] }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Lifecycle Status</span>
                                    <p class="font-bold text-slate-900 mt-0.5">{{ ucfirst($d['status']) }}</p>
                                </div>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-mono uppercase text-[10px] block mb-1">Card Short Description</span>
                                <p class="text-xs text-slate-700 leading-relaxed">{{ $d['short_description'] }}</p>
                            </div>

                            @if (!empty($d['highlights']))
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block mb-2">Curriculum Highlights</span>
                                    <ul class="list-disc pl-5 text-xs text-slate-700 space-y-1">
                                        @foreach ($d['highlights'] as $hl)
                                            <li>{{ $hl }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- TAB 2: PRICING & LOGISTICS --}}
                        <div x-show="activeTab === 'pricing'" class="space-y-4">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Effective Fee</span>
                                    <p class="font-bold text-slate-900 font-mono text-base mt-0.5">₹{{ number_format($d['fee'] ?? 0) }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Original Fee</span>
                                    <p class="font-mono text-slate-500 line-through text-sm mt-0.5">₹{{ number_format($d['original_fee'] ?? 0) }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Discount Rate</span>
                                    <p class="font-bold text-emerald-700 font-mono text-base mt-0.5">{{ $d['discount_percent'] ?? 0 }}% OFF</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Modules Count</span>
                                    <p class="font-bold text-slate-900 font-mono text-base mt-0.5">{{ $d['modules_count'] }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Estimated Duration</span>
                                    <p class="font-bold text-slate-900 mt-0.5">{{ $d['duration'] }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Delivery Methodology</span>
                                    <p class="font-bold text-slate-900 mt-0.5">{{ $d['delivery_mode'] }}</p>
                                </div>
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                    <span class="text-slate-400 font-mono uppercase text-[10px] block">Badges</span>
                                    <p class="mt-0.5 flex gap-1.5">
                                        <span class="px-2 py-0.5 rounded bg-orange-100 text-[#FB6221] font-mono font-bold text-[10px]">{{ $d['tag_badge'] }}</span>
                                        <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold text-[10px]">{{ $d['badge_label'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: MARKDOWN CONTENT PREVIEW --}}
                        <div x-show="activeTab === 'markdown'" class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                                <span class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700">
                                    Rendered HTML Preview (Safe CommonMark Engine)
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">Headings converted to H2/H3 for SEO</span>
                            </div>
                            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200">
                                <div class="course-prose">
                                    {!! $d['rendered_description'] !!}
                                </div>
                            </div>
                        </div>

                        {{-- TAB 4: COHORTS --}}
                        <div x-show="activeTab === 'cohorts'" class="space-y-4">
                            @if (empty($d['cohorts']))
                                <p class="text-xs text-slate-500 italic p-4 text-center">No upcoming cohorts defined in CSV.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-slate-50 font-mono uppercase text-[10px] text-slate-500 border-b border-slate-200">
                                                <th class="p-2.5">Cohort Name</th>
                                                <th class="p-2.5">Start Date</th>
                                                <th class="p-2.5">Schedule &amp; Timings</th>
                                                <th class="p-2.5">Mode</th>
                                                <th class="p-2.5">Seats</th>
                                                <th class="p-2.5">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 font-mono">
                                            @foreach ($d['cohorts'] as $c)
                                                <tr>
                                                    <td class="p-2.5 font-bold text-slate-900 font-sans">{{ $c['name'] }}</td>
                                                    <td class="p-2.5 text-slate-700">{{ $c['start_date'] }}</td>
                                                    <td class="p-2.5 text-slate-600">{{ $c['days'] }} ({{ $c['timings'] }})</td>
                                                    <td class="p-2.5 text-slate-600">{{ $c['delivery_mode'] }}</td>
                                                    <td class="p-2.5 text-slate-700">{{ $c['available_seats'] }} / {{ $c['total_seats'] }} left</td>
                                                    <td class="p-2.5">
                                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                            {{ $c['status'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- TAB 5: FAQS --}}
                        <div x-show="activeTab === 'faqs'" class="space-y-3">
                            @if (empty($d['faqs']))
                                <p class="text-xs text-slate-500 italic p-4 text-center">No FAQs defined in CSV.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach ($d['faqs'] as $faq)
                                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70">
                                            <p class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[#FB6221] text-[16px]">quiz</span>
                                                <span>{{ $faq['question'] }}</span>
                                            </p>
                                            <p class="text-xs text-slate-600 mt-1 pl-6 leading-relaxed">{{ $faq['answer'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- TAB 6: CURRICULUM MODULES & LESSONS --}}
                        <div x-show="activeTab === 'curriculum'" class="space-y-4">
                            @if (empty($d['modules']))
                                <p class="text-xs text-slate-500 italic p-4 text-center">No modules defined in CSV.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach ($d['modules'] as $mIdx => $mod)
                                        @php
                                            $nestedLessons = collect($d['lessons'])->filter(fn($l) => ($l['module_title'] ?? '') === $mod['title']);
                                        @endphp
                                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-xs font-bold text-slate-900 font-mono flex items-center gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-[#FB6221]/15 text-[#FB6221] flex items-center justify-center text-[10px] font-bold">{{ $mIdx + 1 }}</span>
                                                    <span>{{ $mod['title'] }}</span>
                                                </h4>
                                                <span class="text-[10px] font-mono text-slate-500 uppercase">{{ $mod['status'] }} • {{ $nestedLessons->count() }} lesson(s)</span>
                                            </div>
                                            @if ($mod['description'])
                                                <p class="text-xs text-slate-600 mt-1 pl-7">{{ $mod['description'] }}</p>
                                            @endif

                                            @if ($nestedLessons->isNotEmpty())
                                                <div class="mt-3 pl-7 space-y-1.5 border-t border-slate-200/60 pt-2.5">
                                                    @foreach ($nestedLessons as $les)
                                                        <div class="flex items-center justify-between text-xs bg-white p-2 rounded-lg border border-slate-100">
                                                            <div class="flex items-center gap-2">
                                                                <span class="material-symbols-outlined text-[14px] text-slate-400">play_circle</span>
                                                                <span class="font-medium text-slate-800">{{ $les['title'] }}</span>
                                                            </div>
                                                            <span class="text-[10px] font-mono text-slate-500">{{ $les['duration_minutes'] }} mins • {{ $les['content_type'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- TAB 7: TRUST CONTROLS & SEO --}}
                        <div x-show="activeTab === 'trust'" class="space-y-5">
                            <div class="p-4 rounded-xl bg-orange-50/60 border border-orange-200 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold uppercase tracking-wider text-slate-900">Institution-Wide Proof Inheritance</span>
                                    <span class="font-mono font-bold {{ $d['trust_config']['include_global'] ? 'text-emerald-700' : 'text-slate-500' }}">
                                        {{ $d['trust_config']['include_global'] ? 'ENABLED' : 'DISABLED' }}
                                    </span>
                                </div>
                                <p class="text-slate-600 mt-1">
                                    When enabled, global partner logos, general placement records, and institutional awards are automatically inherited by this course without manual duplication.
                                </p>
                            </div>

                            {{-- Quotas Grid --}}
                            <div>
                                <h4 class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700 mb-2">Display Quotas:</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-mono">
                                    @foreach ($d['trust_config']['sections'] as $secKey => $secVal)
                                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-between">
                                            <span class="capitalize text-slate-600">{{ str_replace('_', ' ', $secKey) }}</span>
                                            <span class="font-bold text-slate-900">{{ $secVal['limit'] }} max</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- SEO Preview Box --}}
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-[10px] font-mono uppercase text-slate-400 block mb-2">Google SERP Snippet Preview</span>
                                <div class="space-y-0.5">
                                    <span class="text-[11px] text-slate-500 font-mono block">https://sap.zeblearn.com/courses/{{ $d['slug'] }}</span>
                                    <h4 class="text-blue-700 font-medium text-sm hover:underline cursor-pointer">{{ $d['seo_title'] }}</h4>
                                    <p class="text-xs text-slate-600 leading-relaxed max-w-xl">{{ $d['seo_description'] }}</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>
@endsection
