<!DOCTYPE html>
<html lang="en-IN" class="h-full bg-[#0A0A0A]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0A0A0A">
    <title>Platform Maintenance | ZebLearn LMS</title>
    <meta name="robots" content="noindex, nofollow">

    {{-- Typography: Inter Primary & IBM Plex Mono --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-white font-sans bg-[#0A0A0A] relative overflow-hidden select-none">

    {{-- Ambient radial glows --}}
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative z-10 max-w-lg w-full bg-[#141414] border border-white/10 rounded-3xl p-8 sm:p-10 shadow-2xl text-center space-y-6">

        {{-- Logo --}}
        <div class="flex justify-center">
            <picture>
                <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                <img
                    src="{{ asset('logo/logo2.png') }}"
                    alt="ZebLearn"
                    width="400"
                    height="126"
                    class="h-8 w-auto object-contain"
                />
            </picture>
        </div>

        {{-- Maintenance Badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[#FB6221] text-xs font-mono font-bold tracking-wider uppercase">
            <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
            <span>SYSTEM MAINTENANCE IN PROGRESS</span>
        </div>

        {{-- Heading --}}
        <h1 class="text-2xl sm:text-3xl font-extrabold font-heading text-white tracking-tight">
            We'll Be Back Shortly
        </h1>

        {{-- Message --}}
        <p class="text-sm text-slate-300 leading-relaxed font-sans">
            {{ $maintenanceMessage }}
        </p>

        {{-- Info Box --}}
        <div class="p-4 rounded-2xl bg-white/5 border border-white/5 text-left text-xs space-y-2 font-mono text-slate-400">
            <div class="flex items-center justify-between">
                <span>Status:</span>
                <span class="text-amber-400 font-semibold">Scheduled Upgrade</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Estimated Window:</span>
                <span class="text-slate-300">Under 60 Minutes</span>
            </div>
            <div class="flex items-center justify-between">
                <span>Contact Desk:</span>
                <a href="mailto:enquiry@zeblearn.com" class="text-[#FB6221] hover:underline">enquiry@zeblearn.com</a>
            </div>
        </div>

        {{-- Staff Link --}}
        <div class="pt-2 border-t border-white/10 text-xs text-slate-500">
            Platform Administrator?
            <a href="{{ route('login') }}" class="text-[#FB6221] hover:underline font-semibold ml-1">
                Admin Sign In &rarr;
            </a>
        </div>
    </div>
</body>
</html>

