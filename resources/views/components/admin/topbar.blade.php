<header class="sticky top-0 z-20 h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between transition-all">
    {{-- Left Section: Toggles & Breadcrumbs --}}
    <div class="flex items-center gap-3 sm:gap-4">
        {{-- Mobile Hamburger Button --}}
        <button
            type="button"
            @click="mobileSidebarOpen = true"
            class="lg:hidden p-2 -ml-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] cursor-pointer"
            aria-label="Open mobile sidebar"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Desktop Sidebar Expand/Collapse Toggle --}}
        <button
            type="button"
            @click="sidebarOpen = !sidebarOpen"
            class="hidden lg:flex p-2 -ml-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] cursor-pointer"
            :aria-label="sidebarOpen ? 'Collapse sidebar' : 'Expand sidebar'"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16" />
            </svg>
        </button>

        {{-- Breadcrumb Navigation --}}
        <nav aria-label="Breadcrumb" class="hidden sm:flex items-center gap-2 text-xs">
            <span class="text-slate-400 font-mono">LMS</span>
            <span class="text-slate-300">/</span>
            <span class="text-slate-400 font-medium">Administration</span>
            <span class="text-slate-300">/</span>
            <span class="font-semibold text-slate-800 font-heading">
                @yield('page-title', 'Overview')
            </span>
        </nav>
    </div>

    {{-- Center / Middle: Fast Search Placeholder --}}
    <div class="hidden md:flex items-center flex-1 max-w-xs lg:max-w-md mx-4">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input
                type="text"
                readonly
                aria-label="Quick search courses and students"
                placeholder="Quick search courses, students... (⌘K)"
                class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-100/80 hover:bg-slate-100 text-slate-700 border border-slate-200/80 rounded-lg placeholder:text-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/20 cursor-pointer transition-colors"
                title="Search will be enabled in the upcoming data modules"
            />
        </div>
    </div>

    {{-- Right Section: Alerts, Actions & Admin Profile Dropdown --}}
    <div class="flex items-center gap-2 sm:gap-3">
        {{-- Notification Placeholder Pill --}}
        <button
            type="button"
            class="relative p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] cursor-pointer"
            aria-label="View notifications"
            title="Notification alerts"
        >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            {{-- Notification Dot --}}
            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#FB6221] ring-2 ring-white"></span>
        </button>

        {{-- Divider --}}
        <div class="h-6 w-px bg-slate-200 my-auto"></div>

        {{-- Admin Profile Dropdown (Alpine.js) --}}
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button
                type="button"
                @click="open = !open"
                class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100 transition-colors focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] cursor-pointer"
                :aria-expanded="open.toString()"
                aria-haspopup="true"
            >
                {{-- Avatar Circle with Initials --}}
                <div class="w-8 h-8 rounded-lg bg-[#0A0A0A] text-white flex items-center justify-center font-mono font-bold text-xs shadow-xs border border-white/20 shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>

                {{-- Name & Role Label (Desktop) --}}
                <div class="hidden sm:block text-left pr-1">
                    <p class="text-xs font-semibold text-slate-800 font-heading leading-tight truncate max-w-[120px]">
                        {{ auth()->user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-[10px] font-mono text-[#FB6221] font-semibold uppercase tracking-wider">
                        Super Admin
                    </p>
                </div>

                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Dropdown Menu --}}
            <div
                x-show="open"
                x-cloak
                x-transition:enter="transition ease-out duration-150 transform"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100 transform"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-slate-200/80 py-2 z-50 text-xs focus:outline-hidden divide-y divide-slate-100"
            >
                {{-- User Info Header --}}
                <div class="px-4 py-3">
                    <p class="font-semibold text-slate-800 truncate font-heading">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 truncate text-[11px]">{{ auth()->user()->email }}</p>
                    <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ● Account Active
                    </div>
                </div>

                {{-- Links --}}
                <div class="py-1">
                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="flex items-center justify-between px-4 py-2 text-slate-700 hover:bg-slate-50 transition-colors"
                    >
                        <span>View Public Website</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>

                {{-- Sign Out Action --}}
                <div class="py-1">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button
                            type="submit"
                            class="w-full flex items-center gap-2 px-4 py-2 text-rose-600 hover:bg-rose-50 transition-colors font-semibold cursor-pointer text-left"
                        >
                            <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

