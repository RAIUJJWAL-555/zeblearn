<!DOCTYPE html>
<html lang="en-IN" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0A0A0A">

    <title>@yield('title', 'ZebLearn LMS | Enterprise Learning Platform')</title>
    <meta name="description" content="@yield('description', 'Access your ZebLearn LMS account to continue your enterprise training in SAP, Workday, Salesforce and Cloud Engineering.')">
    <meta name="robots" content="noindex, follow">

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

<body class="h-full font-body-md antialiased text-slate-800 bg-[#FAF9F8]">

    <div class="min-h-full flex flex-col lg:flex-row">

        {{-- LEFT BRANDED HERO PANEL (Visible on lg screens and above) --}}
        <aside class="hidden lg:flex lg:w-1/2 xl:w-5/12 bg-[#0A0A0A] text-white flex-col justify-between p-12 xl:p-16 relative overflow-hidden shrink-0 select-none">
            {{-- Subtle Ambient Glows --}}
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#FB6221]/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
            
            {{-- Grid Pattern Overlay --}}
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:32px_32px] pointer-events-none" aria-hidden="true"></div>

            {{-- Top Branding Header --}}
            <div class="relative z-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] focus:ring-offset-2 focus:ring-offset-[#0A0A0A] rounded-lg">
                    <img
                        src="{{ asset('logo/logo2-400.webp') }}"
                        alt="ZebLearn LMS"
                        width="140"
                        height="44"
                        class="h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                    />
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold tracking-widest uppercase bg-[#FB6221]/20 text-[#FB6221] border border-[#FB6221]/30">
                        LMS PORTAL
                    </span>
                </a>
            </div>

            {{-- Middle Educational Hero Content --}}
            <div class="relative z-10 my-auto py-12">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-mono text-[#FB6221] mb-6">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                    <span>ENTERPRISE LEARNING SYSTEM</span>
                </div>

                <h1 class="text-3xl xl:text-4xl font-extrabold text-white font-heading tracking-tight leading-tight">
                    Master Enterprise Systems.<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-[#FB6221]">
                        Accelerate Your Career.
                    </span>
                </h1>

                <p class="mt-4 text-slate-300 text-sm xl:text-base leading-relaxed max-w-md">
                    Access hands-on live sandbox servers, verified industry curricula, and expert-led modules across SAP, Workday, Salesforce, and Cloud Engineering.
                </p>

                {{-- Interactive Simulated Learning Card --}}
                <div class="mt-8 p-5 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-md shadow-2xl max-w-md">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-[#FB6221]/20 border border-[#FB6221]/40 flex items-center justify-center text-[#FB6221] font-bold text-xs">
                                SAP
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-white">SAP S/4HANA Finance Masterclass</p>
                                <p class="text-[11px] text-slate-400">Next: GL & Accounts Payable Flow</p>
                            </div>
                        </div>
                        <span class="text-xs font-mono font-bold text-[#FB6221]">78%</span>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-[#FB6221] h-1.5 rounded-full" style="width: 78%"></div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-300">
                        <span class="flex items-center gap-1.5 text-emerald-400">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            Verified Sandbox Active
                        </span>
                        <span class="text-slate-400 font-mono">18 / 24 Lessons</span>
                    </div>
                </div>

                {{-- Trust Highlights --}}
                <div class="mt-8 grid grid-cols-2 gap-4 max-w-md">
                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-[#FB6221]/20 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
                        <p class="text-xs text-slate-300 font-medium">85,000+ Enrolled Learners</p>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-[#FB6221]/20 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">✓</div>
                        <p class="text-xs text-slate-300 font-medium">Verifiable Certificates</p>
                    </div>
                </div>
            </div>

            {{-- Bottom Footer Quote --}}
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} ZebLearn Academy. All rights reserved.</p>
                <a href="{{ route('contact') }}" class="hover:text-white transition-colors">
                    Need Help?
                </a>
            </div>
        </aside>

        {{-- RIGHT FORM PANEL --}}
        <main class="flex-1 flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-16 overflow-y-auto">
            {{-- Top Navigation Header --}}
            <div class="flex items-center justify-between w-full max-w-md sm:max-w-lg mx-auto">
                {{-- Mobile Logo --}}
                <a href="{{ route('home') }}" class="lg:hidden flex items-center gap-2 group focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] rounded">
                    <img
                        src="{{ asset('logo/logo1-400.webp') }}"
                        alt="ZebLearn"
                        width="114"
                        height="36"
                        class="h-7 w-auto object-contain"
                    />
                    <span class="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-orange-100 text-[#FB6221]">
                        LMS
                    </span>
                </a>

                {{-- Back to Website Link --}}
                <div class="ml-auto">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#FB6221] transition-colors focus:outline-hidden focus:ring-2 focus:ring-[#FB6221] rounded-md px-2 py-1">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back to Website</span>
                    </a>
                </div>
            </div>

            {{-- Center Form Content Slot --}}
            <div class="w-full max-w-md sm:max-w-lg mx-auto my-auto py-8">
                @yield('content')
            </div>

            {{-- Bottom Simple Mobile-Friendly Footer --}}
            <div class="w-full max-w-md sm:max-w-lg mx-auto pt-6 border-t border-slate-200 text-center text-xs text-slate-500">
                <p>
                    Protected by enterprise-grade 256-bit SSL encryption.
                    <a href="{{ route('about') }}" class="text-slate-600 hover:text-[#FB6221] ml-2">Privacy &amp; Terms</a>
                </p>
            </div>
        </main>

    </div>

    @stack('scripts')
</body>
</html>

