@extends('layouts.app')

@section('title', 'My Profile | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & TOP NAVIGATION --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">My Profile</span>
            </nav>
            <div class="flex items-center gap-3">
                <x-ui.button
                    href="{{ route('student.profile.learning') }}"
                    variant="secondary"
                    size="sm"
                    icon="academic-cap"
                >
                    Learning Overview
                </x-ui.button>
                <x-ui.button
                    href="{{ route('student.profile.edit') }}"
                    variant="primary"
                    size="sm"
                    icon="pencil"
                >
                    Edit Profile
                </x-ui.button>
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        @endif

        {{-- PROFILE HEADER CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    @if ($user->avatarUrl())
                        <img
                            src="{{ $user->avatarUrl() }}"
                            alt="{{ $user->name }}"
                            class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-slate-200 shadow-sm shrink-0"
                        />
                    @else
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-tr from-[#0A0A0A] to-slate-800 text-white flex items-center justify-center font-heading font-black text-3xl sm:text-4xl shadow-sm shrink-0 border border-slate-700">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif

                    <div class="space-y-1">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                                Student Account
                            </span>
                            <x-ui.badge variant="success" size="xs" dot>
                                {{ ucfirst($user->status) }}
                            </x-ui.badge>
                            <span class="text-[11px] font-mono text-slate-400">
                                Member since {{ $user->created_at->format('M Y') }}
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                            {{ $user->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-sans">
                            {{ $user->email }}
                        </p>
                    </div>
                </div>

                {{-- QUICK STATS PILLS --}}
                <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                        <span class="block text-lg sm:text-xl font-black text-slate-900 font-heading">{{ $approvedEnrollmentsCount }}</span>
                        <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Enrolled</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                        <span class="block text-lg sm:text-xl font-black text-slate-900 font-heading">{{ $completedLessonsCount }}</span>
                        <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Lessons</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl px-4 py-2.5 text-center min-w-[90px]">
                        <span class="block text-lg sm:text-xl font-black text-slate-900 font-heading">{{ $certificatesCount }}</span>
                        <span class="block text-[10px] font-mono uppercase tracking-wider text-slate-500">Certificates</span>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- PROFILE DETAILS & SETTINGS GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- LEFT: CONTACT & PERSONAL INFO (2 COLS) --}}
            <div class="lg:col-span-2 space-y-6">
                <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 font-heading">
                            Personal &amp; Contact Details
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Information associated with your student records and learning credentials.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-slate-100">
                        <div>
                            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Full Legal Name</span>
                            <span class="block text-sm font-semibold text-slate-900 mt-1">{{ $user->name }}</span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Email Address</span>
                            <span class="block text-sm font-semibold text-slate-900 mt-1">{{ $user->email }}</span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Phone Number</span>
                            <span class="block text-sm font-semibold text-slate-900 mt-1">
                                {{ $user->phone ?: 'Not provided' }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Account ID</span>
                            <span class="block text-sm font-mono text-slate-600 mt-1">#STU-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400 mb-1.5">Biography &amp; Learning Goals</span>
                        <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-200/70 text-xs sm:text-sm text-slate-700 leading-relaxed">
                            {{ $user->bio ?: 'No biography or personal goals added yet. Tell instructors and peers about your background and career aspirations.' }}
                        </div>
                    </div>
                </x-ui.card>

                {{-- PROFILE PREFERENCES CARD --}}
                <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs space-y-6">
                    <div>
                        <h2 class="text-base font-bold text-slate-900 font-heading">
                            Learning &amp; Notification Preferences
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Configure how you receive updates and alerts regarding your courses.
                        </p>
                    </div>

                    <div class="divide-y divide-slate-100 pt-2 border-t border-slate-100">
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm font-semibold text-slate-800">Email Notifications</p>
                                <p class="text-[11px] text-slate-500">Receive email alerts when assignments are graded and certificates are issued.</p>
                            </div>
                            <x-ui.badge variant="{{ $user->getPreference('email_notifications', true) ? 'success' : 'default' }}" size="xs">
                                {{ $user->getPreference('email_notifications', true) ? 'Enabled' : 'Disabled' }}
                            </x-ui.badge>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm font-semibold text-slate-800">Course Reminders</p>
                                <p class="text-[11px] text-slate-500">Weekly study reminders and upcoming assignment due date alerts.</p>
                            </div>
                            <x-ui.badge variant="{{ $user->getPreference('course_reminders', true) ? 'success' : 'default' }}" size="xs">
                                {{ $user->getPreference('course_reminders', true) ? 'Enabled' : 'Disabled' }}
                            </x-ui.badge>
                        </div>

                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <p class="text-xs sm:text-sm font-semibold text-slate-800">Career &amp; Placement Newsletter</p>
                                <p class="text-[11px] text-slate-500">Periodic updates on enterprise hiring drives and new course announcements.</p>
                            </div>
                            <x-ui.badge variant="{{ $user->getPreference('marketing_emails', false) ? 'success' : 'default' }}" size="xs">
                                {{ $user->getPreference('marketing_emails', false) ? 'Subscribed' : 'Off' }}
                            </x-ui.badge>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            {{-- RIGHT: ACCOUNT ACTIONS & SECURITY (1 COL) --}}
            <div class="space-y-6">
                {{-- QUICK NAVIGATION SHORTCUTS --}}
                <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                        Academic Shortcuts
                    </h3>

                    <div class="space-y-2">
                        <a
                            href="{{ route('student.profile.learning') }}"
                            class="flex items-center justify-between p-3 rounded-xl bg-orange-50/50 hover:bg-orange-50 border border-orange-200/60 transition-colors group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#FB6221] text-white flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 group-hover:text-[#FB6221]">Learning Overview</p>
                                    <p class="text-[10px] text-slate-500">Track progress &amp; grades</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-[#FB6221] group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a
                            href="{{ route('student.certificates.index') }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 border border-slate-200/80 transition-colors group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 group-hover:text-[#FB6221]">Certificates</p>
                                    <p class="text-[10px] text-slate-500">Download credentials</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-[#FB6221] group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                        <a
                            href="{{ route('student.payments.history') }}"
                            class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50 border border-slate-200/80 transition-colors group"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900 group-hover:text-[#FB6221]">Payment Receipts</p>
                                    <p class="text-[10px] text-slate-500">Invoices &amp; transaction log</p>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-[#FB6221] group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </x-ui.card>

                {{-- ACCOUNT SECURITY OVERVIEW CARD --}}
                <x-ui.card padding="md" class="border-slate-200/90 shadow-2xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                        Account Security
                    </h3>

                    <div class="space-y-3 text-xs text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Password</span>
                            <span class="font-mono text-[11px] text-slate-400">••••••••••••</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Status</span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                Authorized
                            </span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <x-ui.button
                            href="{{ route('student.profile.edit') }}#password-section"
                            variant="secondary"
                            size="sm"
                            icon="lock-closed"
                            class="w-full justify-center"
                        >
                            Change Password
                        </x-ui.button>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</div>
@endsection

