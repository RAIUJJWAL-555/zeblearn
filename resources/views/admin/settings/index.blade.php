@extends('layouts.admin')

@section('title', 'System Settings & Platform Configuration | ZebLearn LMS')
@section('page-title', 'Platform Settings')

@section('content')
<div class="space-y-6">

    {{-- Header Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A0A0A] via-[#141414] to-[#221207] text-white p-6 sm:p-8 shadow-xl border border-white/10">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/10 text-xs font-mono text-[#FB6221]">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                    <span>PLATFORM GOVERNANCE & CONFIGURATION</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white tracking-tight">
                    System & Platform Settings
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed">
                    Centrally govern platform branding, security credentials, course accessibility, automated notifications, payment gateways, and system maintenance.
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($isMaintenance)
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs font-bold tracking-wide">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
                        </span>
                        <span>MAINTENANCE MODE ACTIVE</span>
                    </div>
                @else
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-bold tracking-wide">
                        <span class="relative flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>PLATFORM OPERATIONAL</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('status'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="text-xs text-emerald-900">
                <p class="font-bold">Settings Updated Successfully</p>
                <p class="mt-0.5 text-emerald-800">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="text-xs text-rose-900 space-y-1">
                <p class="font-bold">Please correct the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @php
        $tabMeta = [
            'general' => [
                'label' => 'General Identity',
                'description' => 'Platform name, contacts & operational URLs',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
            ],
            'branding' => [
                'label' => 'Branding & Socials',
                'description' => 'Brand colors, logos, icons & community channels',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />',
            ],
            'payments' => [
                'label' => 'Payment Gateway',
                'description' => 'Razorpay keys, mode & webhook endpoints',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />',
            ],
            'email' => [
                'label' => 'Email & Notifications',
                'description' => 'Sender identity & role notification toggles',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
            ],
            'courses' => [
                'label' => 'Course Policies',
                'description' => 'Enrollment gates, approvals & registration',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />',
            ],
            'certificates' => [
                'label' => 'Certificates',
                'description' => 'Issuing institute, signatories & serial format',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />',
            ],
            'seo' => [
                'label' => 'SEO & Metadata',
                'description' => 'Meta titles, descriptions & crawler indexing',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />',
            ],
            'maintenance' => [
                'label' => 'System Maintenance',
                'description' => 'Soft maintenance mode toggle & public alerts',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
            ],
        ];
    @endphp

    {{-- Main Layout: Navigation Sidebar / Tabs & Content Area --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">

        {{-- Navigation Menu (Desktop Sidebar & Mobile Scrollable List) --}}
        <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200/80 p-3 shadow-xs space-y-1">
            <div class="px-3 py-2 text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold">
                Configuration Domains
            </div>

            @foreach($groups as $groupKey)
                @php
                    $isActive = ($activeTab === $groupKey);
                    $meta = $tabMeta[$groupKey] ?? [
                        'label' => ucfirst($groupKey),
                        'description' => '',
                        'icon' => '<circle cx="12" cy="12" r="9" stroke-width="2" />',
                    ];
                @endphp
                <a
                    href="{{ route('admin.settings.index', ['group' => $groupKey]) }}"
                    class="group flex items-start gap-3 p-3 rounded-xl transition {{ $isActive ? 'bg-[#FB6221]/10 text-[#FB6221] font-bold border border-[#FB6221]/20 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                >
                    <svg class="w-4 h-4 shrink-0 mt-0.5 {{ $isActive ? 'text-[#FB6221]' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $meta['icon'] !!}
                    </svg>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold leading-tight flex items-center justify-between">
                            <span>{{ $meta['label'] }}</span>
                            @if($groupKey === 'maintenance' && $isMaintenance)
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                            @endif
                        </div>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5 leading-snug">
                            {{ $meta['description'] }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Configuration Panel & Audit Sidebar --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Active Group Partial Form --}}
            @if(view()->exists('admin.settings.partials.' . $activeTab))
                @include('admin.settings.partials.' . $activeTab)
            @else
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-500 text-xs">
                    Configuration group [{{ $activeTab }}] not found.
                </div>
            @endif

            {{-- Configuration Audit History Section --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold font-heading text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Platform Configuration Audit Trail</span>
                        </h4>
                        <p class="text-[11px] text-slate-500">Most recent changes committed to system settings by authorized administrative staff.</p>
                    </div>
                </div>

                @if($recentAuditLogs->isEmpty())
                    <p class="text-xs text-slate-400 py-3 italic">No configuration modifications logged yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-[10px] font-mono uppercase text-slate-400">
                                    <th class="py-2 pr-3">Timestamp</th>
                                    <th class="py-2 pr-3">Administrator</th>
                                    <th class="py-2 pr-3">Setting Key</th>
                                    <th class="py-2 pr-3">Previous Value</th>
                                    <th class="py-2 pr-3">Updated Value</th>
                                    <th class="py-2 text-right">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100/80">
                                @foreach($recentAuditLogs as $log)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2.5 pr-3 text-slate-500 font-mono text-[11px] whitespace-nowrap">
                                            {{ $log->created_at->format('M d, H:i') }}
                                        </td>
                                        <td class="py-2.5 pr-3 font-medium text-slate-800 whitespace-nowrap">
                                            {{ $log->user->name ?? 'System' }}
                                        </td>
                                        <td class="py-2.5 pr-3 font-mono text-[11px] text-[#FB6221] whitespace-nowrap">
                                            {{ $log->key }}
                                        </td>
                                        <td class="py-2.5 pr-3 font-mono text-[11px] text-slate-500 max-w-[120px] truncate">
                                            {{ $log->old_value ?? '—' }}
                                        </td>
                                        <td class="py-2.5 pr-3 font-mono text-[11px] text-slate-900 max-w-[120px] truncate">
                                            {{ $log->new_value ?? '—' }}
                                        </td>
                                        <td class="py-2.5 text-right font-mono text-[11px] text-slate-400 whitespace-nowrap">
                                            {{ $log->ip_address ?? '127.0.0.1' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection

