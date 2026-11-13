@php
    $navGroups = [
        [
            'title' => 'Dashboard',
            'items' => [
                [
                    'label' => 'Overview',
                    'route' => 'admin.dashboard',
                    'url' => route('admin.dashboard'),
                    'active' => request()->routeIs('admin.dashboard'),
                    'badge' => null,
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'Academic Management',
            'items' => [
                [
                    'label' => 'Courses',
                    'route' => 'admin.courses.index',
                    'url' => route('admin.courses.index'),
                    'active' => request()->routeIs('admin.courses.*'),
                    'badge' => null,
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>',
                ],
                [
                    'label' => 'Quizzes & MCQs',
                    'route' => 'admin.quizzes.index',
                    'url' => route('admin.quizzes.index'),
                    'active' => request()->routeIs('admin.quizzes.*'),
                    'badge' => null,
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>',
                ],
                [
                    'label' => 'Assignments',
                    'route' => 'admin.assignments.index',
                    'url' => route('admin.assignments.index'),
                    'active' => request()->routeIs('admin.assignments.*'),
                    'badge' => null,
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>',
                ],
                [
                    'label' => 'Categories',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>',
                ],
                [
                    'label' => 'Curriculum',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>',
                ],
                [
                    'label' => 'Lessons & Media',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'Student Management',
            'items' => [
                [
                    'label' => 'Students',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>',
                ],
                [
                    'label' => 'Instructors',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>',
                ],
                [
                    'label' => 'Enrollment Approvals',
                    'route' => 'admin.enrollments.index',
                    'url' => route('admin.enrollments.index'),
                    'active' => request()->routeIs('admin.enrollments.*'),
                    'badge' => null,
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'Finance & Reports',
            'items' => [
                [
                    'label' => 'Payments',
                    'route' => 'admin.payments.index',
                    'url' => route('admin.payments.index'),
                    'active' => request()->routeIs('admin.payments.*'),
                    'badge' => null,
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>',
                ],
                [
                    'label' => 'Revenue Reports',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>',
                ],
            ],
        ],
        [
            'title' => 'System',
            'items' => [
                [
                    'label' => 'Notifications',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>',
                ],
                [
                    'label' => 'Settings',
                    'route' => null,
                    'url' => '#',
                    'active' => false,
                    'badge' => 'Upcoming',
                    'icon' => '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
                ],
            ],
        ],
    ];
@endphp

{{-- ========================================== --}}
{{-- 1. DESKTOP PERMANENT / COLLAPSIBLE SIDEBAR --}}
{{-- ========================================== --}}
<aside
    class="hidden lg:flex flex-col bg-[#0A0A0A] text-slate-300 border-r border-white/10 shrink-0 select-none transition-all duration-300 relative z-30"
    :class="sidebarOpen ? 'w-64' : 'w-20'"
>
    {{-- Brand Header --}}
    <div class="h-16 flex items-center px-5 border-b border-white/10 overflow-hidden">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group focus:outline-hidden">
            <img
                src="{{ asset('logo/logo2-400.webp') }}"
                alt="ZebLearn LMS"
                class="h-8 w-auto object-contain transition-transform duration-200 group-hover:scale-105"
            />
            <span
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold tracking-widest uppercase bg-[#FB6221]/20 text-[#FB6221] border border-[#FB6221]/30 shrink-0"
            >
                ADMIN
            </span>
        </a>
    </div>

    {{-- Navigation Scrollable Area --}}
    <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-6 custom-scrollbar" aria-label="Admin Navigation">
        @foreach ($navGroups as $group)
            <div class="space-y-1">
                {{-- Group Title (hidden when collapsed) --}}
                <div
                    x-show="sidebarOpen"
                    x-transition:enter="transition-opacity duration-150"
                    class="px-3 pb-2 text-[10px] font-mono font-bold tracking-wider uppercase text-slate-400"
                >
                    {{ $group['title'] }}
                </div>

                @foreach ($group['items'] as $item)
                    @if ($item['active'])
                        <a
                            href="{{ $item['url'] }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-xs text-white bg-[#FB6221] shadow-sm shadow-orange-500/20 transition-all focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]"
                            :class="!sidebarOpen && 'justify-center px-0'"
                            title="{{ $item['label'] }}"
                        >
                            {!! $item['icon'] !!}
                            <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
                        </a>
                    @else
                        <a
                            href="{{ $item['url'] }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-xs text-slate-400 hover:text-white hover:bg-white/5 transition-all group focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]"
                            :class="!sidebarOpen && 'justify-center px-0'"
                            title="{{ $item['label'] }}"
                        >
                            <div class="flex items-center gap-3 truncate">
                                <span class="text-slate-400 group-hover:text-white transition-colors">
                                    {!! $item['icon'] !!}
                                </span>
                                <span x-show="sidebarOpen" class="truncate">{{ $item['label'] }}</span>
                            </div>

                            @if ($item['badge'])
                                <span
                                    x-show="sidebarOpen"
                                    class="text-[9px] font-mono font-semibold px-1.5 py-0.5 rounded bg-white/10 text-slate-400 group-hover:text-slate-300"
                                >
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        @endforeach
    </nav>

    {{-- Bottom Utility Link & Collapse Trigger --}}
    <div class="p-3 border-t border-white/10 space-y-1">
        <a
            href="{{ route('home') }}"
            target="_blank"
            class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-white/5 transition-all focus:outline-hidden"
            :class="!sidebarOpen && 'justify-center px-0'"
            title="Public Website"
        >
            <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span x-show="sidebarOpen" class="truncate">Public Website</span>
        </a>

        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:text-white hover:bg-white/5 transition-all focus:outline-hidden cursor-pointer"
            :class="!sidebarOpen && 'justify-center px-0'"
            :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
        >
            <svg
                class="w-4 h-4 shrink-0 text-slate-400 transition-transform duration-200"
                :class="!sidebarOpen && 'rotate-180'"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
            <span x-show="sidebarOpen" class="truncate">Collapse Sidebar</span>
        </button>
    </div>
</aside>

{{-- ========================================== --}}
{{-- 2. MOBILE DRAWER WITH BACKDROP             --}}
{{-- ========================================== --}}
<div
    x-show="mobileSidebarOpen"
    x-cloak
    class="relative z-50 lg:hidden"
    role="dialog"
    aria-modal="true"
    aria-label="Mobile Navigation Drawer"
>
    {{-- Backdrop Overlay --}}
    <div
        x-show="mobileSidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/70 backdrop-blur-xs"
        @click="mobileSidebarOpen = false"
        aria-hidden="true"
    ></div>

    <div class="fixed inset-0 flex">
        {{-- Slide-over Drawer Panel --}}
        <div
            x-show="mobileSidebarOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-[#0A0A0A] text-slate-300"
        >
            {{-- Close Button --}}
            <div class="absolute top-4 right-[-48px]">
                <button
                    type="button"
                    @click="mobileSidebarOpen = false"
                    class="flex h-10 w-10 items-center justify-center rounded-full text-white bg-white/10 hover:bg-white/20 focus:outline-hidden"
                    aria-label="Close mobile sidebar"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Mobile Brand Header --}}
            <div class="h-16 flex items-center px-6 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img
                        src="{{ asset('logo/logo2-400.webp') }}"
                        alt="ZebLearn"
                        class="h-8 w-auto object-contain"
                    />
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold tracking-widest uppercase bg-[#FB6221]/20 text-[#FB6221] border border-[#FB6221]/30">
                        ADMIN
                    </span>
                </a>
            </div>

            {{-- Navigation Items --}}
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
                @foreach ($navGroups as $group)
                    <div class="space-y-1">
                        <div class="px-3 pb-2 text-[10px] font-mono font-bold tracking-wider uppercase text-slate-400">
                            {{ $group['title'] }}
                        </div>

                        @foreach ($group['items'] as $item)
                            @if ($item['active'])
                                <a
                                    href="{{ $item['url'] }}"
                                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-xs text-white bg-[#FB6221] shadow-sm shadow-orange-500/20"
                                    @click="mobileSidebarOpen = false"
                                >
                                    {!! $item['icon'] !!}
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @else
                                <a
                                    href="{{ $item['url'] }}"
                                    class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-xs text-slate-400 hover:text-white hover:bg-white/5 transition-all"
                                    @click="mobileSidebarOpen = false"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-400">
                                            {!! $item['icon'] !!}
                                        </span>
                                        <span>{{ $item['label'] }}</span>
                                    </div>
                                    @if ($item['badge'])
                                        <span class="text-[9px] font-mono font-semibold px-1.5 py-0.5 rounded bg-white/10 text-slate-400">
                                            {{ $item['badge'] }}
                                        </span>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </nav>

            {{-- Bottom Mobile Link --}}
            <div class="p-4 border-t border-white/10">
                <a
                    href="{{ route('home') }}"
                    class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-semibold text-slate-300 bg-white/5 hover:bg-white/10 transition-colors"
                >
                    <span>View Public Website</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

