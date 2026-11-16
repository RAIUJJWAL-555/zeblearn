<!DOCTYPE html>
<html lang="en-IN" class="h-full bg-[#F8F9FA]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0A0A0A">

    <title>@yield('title', 'Instructor Console | ZebLearn LMS')</title>
    <meta name="robots" content="noindex, nofollow">

    {{-- Typography: Inter Primary & IBM Plex Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @viteReactRefresh
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('head')
</head>

<body class="h-full font-body-md antialiased text-slate-800 bg-[#F8F9FA] flex flex-col min-h-screen">

    {{-- INSTRUCTOR TOP NAVIGATION BAR --}}
    <header class="bg-[#0A0A0A] text-white border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Left: Logo and Portal Badge --}}
                <div class="flex items-center gap-4">
                    <a href="{{ route('instructor.dashboard') }}" class="flex items-center gap-2 group">
                        <picture>
                            <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                            <img
                                src="{{ asset('logo/logo2.png') }}"
                                alt="ZebLearn"
                                width="400"
                                height="126"
                                class="h-7 w-auto object-contain transition-transform group-hover:scale-102"
                            />
                        </picture>
                    </a>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-500/20 text-[#FB6221] border border-orange-500/30">
                        Instructor Portal
                    </span>
                </div>

                {{-- Center: Navigation Links --}}
                <nav class="hidden md:flex items-center gap-1">
                    <a
                        href="{{ route('instructor.dashboard') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->routeIs('instructor.dashboard') ? 'bg-white/10 text-[#FB6221]' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"
                    >
                        Dashboard
                    </a>
                    <a
                        href="{{ route('instructor.courses.index') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->routeIs('instructor.courses.*') ? 'bg-white/10 text-[#FB6221]' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"
                    >
                        Assigned Courses
                    </a>
                    <a
                        href="{{ route('instructor.assignments.index') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->routeIs('instructor.assignments.*') ? 'bg-white/10 text-[#FB6221]' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"
                    >
                        Assignment Reviews
                    </a>
                    <a
                        href="{{ route('instructor.analytics.index') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->routeIs('instructor.analytics.*') ? 'bg-white/10 text-[#FB6221]' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"
                    >
                        Analytics
                    </a>
                    <a
                        href="{{ route('instructor.notifications.index') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->routeIs('instructor.notifications.*') ? 'bg-white/10 text-[#FB6221]' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"
                    >
                        Notifications
                    </a>
                    <a
                        href="{{ route('instructor.profile.show') }}"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-colors {{ request()->routeIs('instructor.profile.*') ? 'bg-white/10 text-[#FB6221]' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"
                    >
                        My Profile
                    </a>
                </nav>

                {{-- Right: Notifications, Profile & Sign Out --}}
                <div class="flex items-center gap-2 sm:gap-3">
                    <x-ui.notification-bell theme="dark" />

                    <a href="{{ route('instructor.profile.show') }}" class="hidden sm:flex flex-col text-right hover:opacity-80 transition-opacity">
                        <span class="text-xs font-bold text-white font-heading">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] font-mono text-slate-400">{{ auth()->user()->designation ?: 'Faculty' }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-300 hover:text-rose-400 hover:bg-white/5 transition-colors"
                        >
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>

            {{-- Mobile Navigation Links Row --}}
            <div class="md:hidden flex items-center gap-1 py-2 border-t border-slate-800 text-xs">
                <a
                    href="{{ route('instructor.dashboard') }}"
                    class="px-3 py-1 rounded-md font-medium {{ request()->routeIs('instructor.dashboard') ? 'text-[#FB6221] bg-white/10' : 'text-slate-300' }}"
                >
                    Dashboard
                </a>
                <a
                    href="{{ route('instructor.courses.index') }}"
                    class="px-3 py-1 rounded-md font-medium {{ request()->routeIs('instructor.courses.*') ? 'text-[#FB6221] bg-white/10' : 'text-slate-300' }}"
                >
                    Courses
                </a>
                <a
                    href="{{ route('instructor.assignments.index') }}"
                    class="px-3 py-1 rounded-md font-medium {{ request()->routeIs('instructor.assignments.*') ? 'text-[#FB6221] bg-white/10' : 'text-slate-300' }}"
                >
                    Assignments
                </a>
                <a
                    href="{{ route('instructor.analytics.index') }}"
                    class="px-3 py-1 rounded-md font-medium {{ request()->routeIs('instructor.analytics.*') ? 'text-[#FB6221] bg-white/10' : 'text-slate-300' }}"
                >
                    Analytics
                </a>
                <a
                    href="{{ route('instructor.notifications.index') }}"
                    class="px-3 py-1 rounded-md font-medium {{ request()->routeIs('instructor.notifications.*') ? 'text-[#FB6221] bg-white/10' : 'text-slate-300' }}"
                >
                    Notifications
                </a>
                <a
                    href="{{ route('instructor.profile.show') }}"
                    class="px-3 py-1 rounded-md font-medium {{ request()->routeIs('instructor.profile.*') ? 'text-[#FB6221] bg-white/10' : 'text-slate-300' }}"
                >
                    My Profile
                </a>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT AREA --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-slate-200/80 py-4 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} ZebLearn Academy. Instructor Academic Portal.</p>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Faculty Authorized
                </span>
                <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Public Portal</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

