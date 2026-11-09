@php
    $links = [
        ['label' => 'Courses', 'href' => route('courses.index')],
        ['label' => 'Methodology', 'href' => url('/#method')],
        ['label' => 'Stories', 'href' => url('/#stories')],
        ['label' => 'Placements', 'href' => url('/#placements')],
        ['label' => 'Blog', 'href' => route('blogs')],
        ['label' => 'About', 'href' => route('about')],
        ['label' => 'Contact', 'href' => route('contact')],
    ];
@endphp

<header class="fixed inset-x-0 top-4 sm:top-5 z-50 px-4 sm:px-6 pointer-events-none">
    <div class="max-w-6xl mx-auto pointer-events-auto rounded-full px-4 py-2 sm:px-6 sm:py-2.5 flex items-center justify-between transition-all duration-300 border border-white/60 shadow-[0_8px_32px_rgba(10,10,10,0.08),inset_0_1px_1px_rgba(255,255,255,0.9)]" style="background: rgba(255, 255, 255, 0.65); backdrop-filter: blur(20px) saturate(190%); -webkit-backdrop-filter: blur(20px) saturate(190%);">
        <!-- Brand Logo with Reserved Bounding Dimensions for Zero CLS -->
        <a href="{{ url('/') }}" aria-label="ZebLearn Home" class="flex shrink-0 items-center h-8 sm:h-9 w-[102px] sm:w-[114px] overflow-hidden group">
            <picture class="dark:hidden">
                <source srcset="{{ asset('logo/logo1-400.webp') }}" type="image/webp">
                <img
                    src="{{ asset('logo/logo1.png') }}"
                    alt="ZebLearn"
                    width="400"
                    height="126"
                    fetchpriority="high"
                    decoding="sync"
                    style="aspect-ratio: 1000/315;"
                    class="h-8 sm:h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                />
            </picture>
            <picture class="hidden dark:block">
                <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                <img
                    src="{{ asset('logo/logo2.png') }}"
                    alt="ZebLearn"
                    width="400"
                    height="126"
                    decoding="async"
                    style="aspect-ratio: 1000/315;"
                    class="h-8 sm:h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                />
            </picture>
        </a>

        <!-- Desktop Navigation -->
        <nav aria-label="Main navigation" class="hidden items-center gap-1 md:flex">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}" class="rounded-full px-3.5 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-800 hover:text-[#FB6221] hover:bg-white/70 active:bg-white/90 transition-all">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <!-- Right Action Bar -->
        <div class="flex shrink-0 items-center gap-2.5">
            <a href="tel:+918506888288" class="hidden lg:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-[#0A0A0A] px-3 py-1.5 rounded-full hover:bg-white/60 transition-all">
                <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                <span>+91 8506 888 288</span>
            </a>

            <a href="{{ route('book-demo') }}" class="inline-flex items-center gap-1.5 rounded-full bg-[#FB6221] hover:bg-[#e05214] px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-[0_4px_16px_rgba(251,98,33,0.3)] hover:shadow-[0_6px_20px_rgba(251,98,33,0.4)] transition-all duration-200 hover:scale-[1.02]">
                <span>Book Free Demo</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>

            <!-- Mobile Menu Dropdown -->
            <details class="group relative md:hidden">
                <summary aria-label="Open navigation menu" class="grid h-9 w-9 cursor-pointer list-none place-items-center rounded-full border border-white/70 text-[#0A0A0A] hover:bg-white/80 transition-all shadow-xs [&::-webkit-details-marker]:hidden" style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <nav aria-label="Mobile navigation" class="absolute right-0 top-12 z-50 w-64 rounded-2xl border border-white/70 p-3 shadow-[0_16px_40px_rgba(0,0,0,0.12),inset_0_1px_1px_rgba(255,255,255,0.9)] flex flex-col gap-1" style="background: rgba(255, 255, 255, 0.82); backdrop-filter: blur(24px) saturate(190%); -webkit-backdrop-filter: blur(24px) saturate(190%);">
                    @foreach($links as $link)
                        <a href="{{ $link['href'] }}" class="rounded-xl px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-slate-800 hover:bg-white/80 hover:text-[#FB6221] transition-all">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    <div class="pt-2 mt-2 border-t border-slate-200/60 flex flex-col gap-2">
                        <a href="tel:+918506888288" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 hover:text-[#0A0A0A]">
                            <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                            <span>+91 8506 888 288</span>
                        </a>
                        <a href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20know%20more%20about%20SAP%20courses." target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-emerald-600 hover:text-emerald-700">
                            <span class="material-symbols-outlined text-[16px]">chat</span>
                            <span>WhatsApp Helpline</span>
                        </a>
                    </div>
                </nav>
            </details>
        </div>
    </div>
</header>
