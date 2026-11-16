@props([
    'theme' => 'light', // 'light' (admin/student) or 'dark' (instructor header)
])

@php
    $user = auth()->user();
    if (! $user) {
        return;
    }

    $unreadCount = $user->unreadNotifications()->count();
    $recentNotifications = $user->notifications()->latest()->take(5)->get();

    if ($user->isAdmin()) {
        $historyRoute = route('admin.notifications.index');
    } elseif ($user->isInstructor()) {
        $historyRoute = route('instructor.notifications.index');
    } else {
        $historyRoute = route('student.notifications.index');
    }

    $isDark = $theme === 'dark';
    $buttonClass = $isDark
        ? 'text-slate-300 hover:text-white hover:bg-white/10'
        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    {{-- Bell Trigger Button --}}
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-haspopup="true"
        aria-label="View notifications, {{ $unreadCount }} unread"
        class="relative p-2 rounded-xl {{ $buttonClass }} transition-colors focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] cursor-pointer"
    >
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        {{-- Unread Badge --}}
        @if ($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full bg-[#FB6221] text-white font-mono font-bold text-[10px] leading-tight flex items-center justify-center ring-2 {{ $isDark ? 'ring-slate-900' : 'ring-white' }} shadow-xs">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Preview Menu --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white border border-slate-200/90 shadow-2xl overflow-hidden z-50 text-left"
        style="display: none;"
    >
        {{-- Dropdown Header --}}
        <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 font-heading">
                    Notifications
                </h3>
                @if ($unreadCount > 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-orange-100 text-[#FB6221]">
                        {{ $unreadCount }} unread
                    </span>
                @endif
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="text-[11px] font-semibold text-[#FB6221] hover:text-[#e05214] hover:underline cursor-pointer"
                    >
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        {{-- Dropdown List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
            @forelse ($recentNotifications as $notification)
                @php
                    $isUnread = $notification->unread();
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Notification';
                    $message = $data['message'] ?? '';
                    $icon = $data['icon'] ?? 'bell';
                    $badgeVariant = $data['badge_variant'] ?? 'info';
                @endphp
                <a
                    href="{{ route('notifications.read-and-redirect', $notification->id) }}"
                    class="block px-4 py-3 transition-colors {{ $isUnread ? 'bg-orange-50/30 hover:bg-orange-50/60' : 'hover:bg-slate-50/80' }} group"
                >
                    <div class="flex items-start gap-3">
                        {{-- Icon Avatar --}}
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-orange-100 text-[#FB6221]' : 'bg-slate-100 text-slate-500' }}">
                            @if ($icon === 'check-circle')
                                <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($icon === 'x-circle')
                                <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif ($icon === 'academic-cap')
                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                </svg>
                            @elseif ($icon === 'document-text')
                                <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            @else
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <p class="text-xs font-bold text-slate-900 truncate {{ $isUnread ? 'text-[#0A0A0A]' : 'text-slate-700' }}">
                                    {{ $title }}
                                </p>
                                @if ($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-[#FB6221] shrink-0" title="Unread"></span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">
                                {{ $message }}
                            </p>
                            <span class="text-[10px] font-mono text-slate-400 mt-1 block">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-center text-slate-400">
                    <svg class="w-8 h-8 mx-auto text-slate-300 stroke-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-xs font-medium text-slate-600 mt-2">No notifications yet</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">You're all caught up with your updates.</p>
                </div>
            @endforelse
        </div>

        {{-- Dropdown Footer --}}
        <a
            href="{{ $historyRoute }}"
            class="block px-4 py-2.5 text-center text-xs font-semibold text-slate-700 hover:text-[#FB6221] hover:bg-slate-50 border-t border-slate-100 transition-colors"
        >
            View all notifications &rarr;
        </a>
    </div>
</div>

