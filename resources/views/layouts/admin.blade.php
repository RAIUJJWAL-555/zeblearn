<!DOCTYPE html>
<html lang="en-IN" class="h-full bg-[#F8F9FA]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0A0A0A">

    <title>@yield('title', 'Admin Dashboard | ZebLearn LMS')</title>
    <meta name="robots" content="noindex, nofollow">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800;900&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800;900&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    @stack('head')
</head>

<body
    class="h-full font-body-md antialiased text-slate-800 bg-[#F8F9FA] overflow-x-hidden"
    x-data="{
        sidebarOpen: true,
        mobileSidebarOpen: false
    }"
    @keydown.escape.window="mobileSidebarOpen = false"
>

    <div class="min-h-full flex">

        {{-- Sidebar Component (Desktop + Mobile Drawer) --}}
        <x-admin.sidebar />

        {{-- Main Dashboard Viewport --}}
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300">

            {{-- Top Navigation Bar --}}
            <x-admin.topbar />

            {{-- Main Dynamic Content Area --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>

            {{-- Dashboard Minimal Footer --}}
            <footer class="py-4 px-4 sm:px-6 lg:px-8 border-t border-slate-200/80 bg-white/50 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} ZebLearn Academy. Enterprise LMS Console.</p>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Services Operational
                    </span>
                    <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">
                        Public Portal
                    </a>
                </div>
            </footer>

        </div>

    </div>

    @stack('scripts')
</body>
</html>

