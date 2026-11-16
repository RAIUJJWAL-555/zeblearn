@extends('layouts.app')

@section('title', 'My Notifications | ZebLearn LMS')
@section('body-class', "bg-[#F8F9FA] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="min-h-screen bg-[#F8F9FA] pt-28 sm:pt-32 pb-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- BREADCRUMBS & TOP BAR --}}
        <div class="flex items-center justify-between">
            <nav class="flex items-center gap-2 text-xs font-mono text-slate-500 uppercase tracking-wider">
                <a href="{{ route('student.dashboard') }}" class="hover:text-[#FB6221] transition-colors">Dashboard</a>
                <span>/</span>
                <span class="text-slate-900 font-bold">Notifications</span>
            </nav>
            <x-ui.button
                href="{{ route('student.dashboard') }}"
                variant="secondary"
                size="sm"
                icon="arrow-left"
            >
                Back to Dashboard
            </x-ui.button>
        </div>

        {{-- FLASH MESSAGES --}}
        @if (session('success'))
            <x-ui.alert variant="success" dismissible>
                {{ session('success') }}
            </x-ui.alert>
        @endif

        {{-- HEADER CARD --}}
        <x-ui.card padding="lg" class="border-slate-200/90 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                            Learning Updates
                        </span>
                        @if ($stats['unread'] > 0)
                            <x-ui.badge variant="warning" size="xs">
                                {{ $stats['unread'] }} Unread
                            </x-ui.badge>
                        @endif
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-heading tracking-tight">
                        Notifications &amp; Activity
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 font-sans">
                        Stay informed on enrollment approvals, assignment evaluations, payment receipts, and certificates.
                    </p>
                </div>

                @if ($stats['unread'] > 0)
                    <div>
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            <x-ui.button
                                type="submit"
                                variant="secondary"
                                size="sm"
                                icon="check"
                            >
                                Mark All as Read
                            </x-ui.button>
                        </form>
                    </div>
                @endif
            </div>
        </x-ui.card>

        {{-- STATS FILTER CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a
                href="{{ route('student.notifications.index') }}"
                class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'all' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
            >
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Total Updates</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['total'] }}</span>
            </a>

            <a
                href="{{ route('student.notifications.index', ['status' => 'unread']) }}"
                class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'unread' ? 'border-[#FB6221] ring-2 ring-[#FB6221]/15' : 'border-slate-200/80 hover:border-slate-300' }}"
            >
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-amber-600">Unread</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['unread'] }}</span>
            </a>

            <a
                href="{{ route('student.notifications.index', ['status' => 'read']) }}"
                class="bg-white rounded-2xl p-4 sm:p-5 border transition-all shadow-2xs {{ $activeStatus === 'read' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-slate-200/80 hover:border-slate-300' }}"
            >
                <span class="block text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">Read</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 font-heading mt-1 block">{{ $stats['read'] }}</span>
            </a>
        </div>

        {{-- NOTIFICATIONS LIST --}}
        <x-ui.card padding="none" class="border-slate-200/80 shadow-2xs overflow-hidden">
            @if ($notifications->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-[#FB6221] mx-auto flex items-center justify-center mb-4">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 font-heading">No notifications found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                        {{ $activeStatus === 'unread' ? 'You have read all your notifications!' : 'No notifications for your student account yet.' }}
                    </p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($notifications as $notification)
                        @php
                            $isUnread = $notification->unread();
                            $data = $notification->data;
                            $title = $data['title'] ?? 'Notification';
                            $message = $data['message'] ?? '';
                            $category = $data['category'] ?? 'academic';
                            $icon = $data['icon'] ?? 'bell';
                            $actionLabel = $data['action_label'] ?? 'View Details';
                        @endphp
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors {{ $isUnread ? 'bg-orange-50/20' : 'bg-white hover:bg-slate-50/60' }}">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-orange-100 text-[#FB6221]' : 'bg-slate-100 text-slate-500' }}">
                                    @if ($icon === 'check-circle')
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif ($icon === 'x-circle')
                                        <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    @elseif ($icon === 'academic-cap')
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                    @elseif ($icon === 'document-text')
                                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-bold text-slate-900 font-heading">
                                            {{ $title }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-slate-100 text-slate-600">
                                            {{ $category }}
                                        </span>
                                        @if ($isUnread)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-orange-100 text-[#FB6221]">
                                                Unread
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed max-w-2xl">
                                        {{ $message }}
                                    </p>
                                    <div class="flex items-center gap-3 text-[11px] font-mono text-slate-400 pt-0.5">
                                        <span>{{ $notification->created_at->format('d M Y, h:i A') }}</span>
                                        <span>&bull;</span>
                                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center pl-14 sm:pl-0">
                                @if ($isUnread)
                                    <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                                        @csrf
                                        <x-ui.button
                                            type="submit"
                                            variant="ghost"
                                            size="xs"
                                            icon="check"
                                        >
                                            Mark Read
                                        </x-ui.button>
                                    </form>
                                @endif

                                <x-ui.button
                                    href="{{ route('notifications.read-and-redirect', $notification->id) }}"
                                    variant="primary"
                                    size="xs"
                                    icon="arrow-right"
                                >
                                    {{ $actionLabel }}
                                </x-ui.button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($notifications->hasPages())
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </x-ui.card>
    </div>
</div>
@endsection

