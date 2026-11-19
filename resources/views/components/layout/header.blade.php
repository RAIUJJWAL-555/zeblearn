@php
    $links = [
        ['label' => 'Courses', 'href' => route('courses.index')],
        ['label' => 'Blog', 'href' => route('blogs')],
        ['label' => 'About', 'href' => route('about')],
        ['label' => 'Contact', 'href' => route('contact')],
    ];
@endphp

<header
    x-data="{
        mobileMenuOpen: false,
        init() {
            this.$watch('mobileMenuOpen', value => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        }
    }"
    @keydown.escape.window="mobileMenuOpen = false"
    class="fixed inset-x-0 top-3 sm:top-5 z-50 px-3 sm:px-6 pointer-events-none"
>
    <!-- Header Card -->
    <div
        class="max-w-7xl mx-auto pointer-events-auto rounded-2xl md:rounded-full px-3.5 py-2.5 sm:px-5 sm:py-3 md:px-7 md:py-2.5 flex flex-col md:flex-row md:items-center md:justify-between transition-all duration-300 border border-white/60 shadow-[0_8px_32px_rgba(10,10,10,0.08),inset_0_1px_1px_rgba(255,255,255,0.9)]"
        style="background: rgba(255, 255, 255, 0.72); backdrop-filter: blur(20px) saturate(190%); -webkit-backdrop-filter: blur(20px) saturate(190%);"
    >
        <!-- Row 1 on Mobile: Logo (Left) + Search & Hamburger (Right) | Left-Aligned on Desktop -->
        <div class="flex items-center justify-between w-full md:w-auto">
            <!-- Brand Logo with Reserved Dimensions for Zero CLS -->
            <a href="{{ url('/') }}" aria-label="ZebLearn Home" class="flex shrink-0 items-center h-8 sm:h-9 w-[102px] sm:w-[114px] overflow-hidden group">
                <picture class="dark:hidden">
                    <source srcset="{{ asset('logo/logo1-400.webp') }}" type="image/webp">
                    <img
                        src="{{ asset('logo/logo1-400.webp') }}"
                        alt="ZebLearn"
                        width="400"
                        height="126"
                        loading="eager"
                        decoding="async"
                        style="aspect-ratio: 1000/315;"
                        class="h-8 sm:h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                    />
                </picture>
                <picture class="hidden dark:block">
                    <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                    <img
                        src="{{ asset('logo/logo2-400.webp') }}"
                        alt="ZebLearn"
                        width="400"
                        height="126"
                        loading="lazy"
                        decoding="async"
                        style="aspect-ratio: 1000/315;"
                        class="h-8 sm:h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                    />
                </picture>
            </a>

            <!-- Mobile Controls (Row 1 Right): Compact Search + Hamburger Button -->
            <div class="flex items-center gap-2 md:hidden">
                <!-- Mobile Compact Search Button -->
                <button
                    type="button"
                    data-search-trigger
                    @click="$dispatch('open-course-search')"
                    onclick="window.dispatchEvent(new CustomEvent('open-course-search'))"
                    class="min-h-[44px] min-w-[44px] grid place-items-center rounded-full bg-slate-100 hover:bg-white text-slate-700 hover:text-[#FB6221] transition-all border border-slate-200/80 shadow-2xs cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/50"
                    title="Search courses (Cmd+K)"
                    aria-label="Search courses"
                >
                    <x-ui.icon name="search" size="xs" class="text-[#FB6221] shrink-0" />
                </button>

                @auth
                    <x-ui.notification-bell theme="light" />
                    <a
                        href="{{ auth()->user()->isStudent() ? route('student.profile.show') : (auth()->user()->isInstructor() ? route('instructor.profile.show') : route('admin.dashboard')) }}"
                        class="min-h-[44px] min-w-[44px] grid place-items-center rounded-full text-slate-600 hover:text-[#FB6221] hover:bg-white/80 transition-colors"
                        title="My Profile"
                        aria-label="My Profile"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </a>
                @endauth

                <!-- Hamburger / Close Button -->
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen.toString()"
                    aria-controls="mobile-nav-panel"
                    aria-label="Toggle navigation menu"
                    class="min-h-[44px] min-w-[44px] grid place-items-center rounded-full border border-slate-200/80 bg-white/80 text-slate-800 hover:text-[#FB6221] hover:bg-white transition-all shadow-2xs cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/50"
                >
                    <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Desktop Navigation (Hidden on Mobile) -->
        <nav aria-label="Main navigation" class="hidden items-center gap-1 md:flex">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}" class="rounded-full px-3.5 py-1.5 text-xs font-semibold uppercase tracking-wider text-slate-800 hover:text-[#FB6221] hover:bg-white/70 active:bg-white/90 transition-all focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/40">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <!-- Desktop Right Action Bar (Hidden on Mobile) -->
        <div class="hidden md:flex shrink-0 items-center gap-2 sm:gap-2.5">
            <!-- Desktop Course Search Trigger -->
            <button
                type="button"
                data-search-trigger
                @click="$dispatch('open-course-search')"
                onclick="window.dispatchEvent(new CustomEvent('open-course-search'))"
                class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-full bg-slate-100 hover:bg-white text-slate-700 hover:text-[#FB6221] text-xs font-semibold transition-all border border-slate-200/80 shadow-2xs cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/40"
                title="Search courses (Cmd+K)"
                aria-label="Search courses"
            >
                <x-ui.icon name="search" size="xs" class="text-[#FB6221] shrink-0" />
                <span class="hidden xl:inline text-[11px] font-mono uppercase">Search</span>
            </button>

            @auth
                <x-ui.notification-bell theme="light" />
                <a
                    href="{{ auth()->user()->isStudent() ? route('student.profile.show') : (auth()->user()->isInstructor() ? route('instructor.profile.show') : route('admin.dashboard')) }}"
                    class="p-1.5 rounded-xl text-slate-600 hover:text-[#FB6221] hover:bg-white/80 transition-colors"
                    title="My Profile"
                    aria-label="My Profile"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </a>
            @endauth

            <a href="tel:+918506888288" class="hidden lg:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-[#0A0A0A] px-3 py-1.5 rounded-full hover:bg-white/60 transition-all focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/40">
                <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                <span>+91 8506 888 288</span>
            </a>

            <a href="{{ route('book-demo') }}" class="inline-flex items-center gap-1.5 rounded-full bg-[#c2410c] hover:bg-[#9a3412] px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-[0_4px_16px_rgba(194,65,12,0.3)] hover:shadow-[0_6px_20px_rgba(194,65,12,0.4)] transition-all duration-200 hover:scale-[1.02] focus:outline-hidden focus:ring-2 focus:ring-[#c2410c]/50">
                <span>Book Free Demo</span>
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>
        </div>

        <!-- Row 2 on Mobile: Full-Width Book Free Demo CTA Button -->
        <div class="mt-2 pt-2 border-t border-slate-200/50 w-full md:hidden">
            <a
                href="{{ route('book-demo') }}"
                class="w-full min-h-[44px] flex items-center justify-center gap-2 rounded-full bg-[#c2410c] hover:bg-[#9a3412] active:bg-[#7c2d12] px-4 py-2.5 text-xs font-semibold uppercase tracking-wider text-white shadow-[0_4px_14px_rgba(194,65,12,0.28)] hover:shadow-[0_6px_20px_rgba(194,65,12,0.38)] transition-all duration-200 active:scale-[0.99] focus:outline-hidden focus:ring-2 focus:ring-[#c2410c]/50"
            >
                <span>Book Free Demo</span>
                <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
            </a>
        </div>
    </div>

    <!-- Mobile Navigation Drawer / Panel Backdrop -->
    <div
        x-show="mobileMenuOpen"
        x-cloak
        @click="mobileMenuOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-40 md:hidden pointer-events-auto"
        aria-hidden="true"
    ></div>

    <!-- Mobile Navigation Panel -->
    <nav
        id="mobile-nav-panel"
        x-show="mobileMenuOpen"
        x-cloak
        @click.outside="mobileMenuOpen = false"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        aria-label="Mobile navigation"
        class="relative max-w-7xl mx-auto mt-2 w-full rounded-2xl border border-white/80 p-3 shadow-[0_20px_48px_rgba(0,0,0,0.14),inset_0_1px_1px_rgba(255,255,255,0.9)] flex flex-col gap-1 md:hidden pointer-events-auto z-50"
        style="background: rgba(255, 255, 255, 0.94); backdrop-filter: blur(28px) saturate(190%); -webkit-backdrop-filter: blur(28px) saturate(190%);"
    >
        <button
            type="button"
            data-search-trigger
            @click="mobileMenuOpen = false; $dispatch('open-course-search')"
            onclick="window.dispatchEvent(new CustomEvent('open-course-search'))"
            class="w-full text-left rounded-xl px-4 py-3 min-h-[44px] text-xs font-semibold uppercase tracking-wider text-slate-800 hover:bg-slate-100 hover:text-[#FB6221] transition-all flex items-center gap-2 cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/40"
        >
            <x-ui.icon name="search" size="xs" class="text-[#FB6221] shrink-0" />
            <span>Search Courses</span>
        </button>

        @foreach($links as $link)
            <a
                href="{{ $link['href'] }}"
                @click="mobileMenuOpen = false"
                class="rounded-xl px-4 py-3 min-h-[44px] flex items-center text-xs font-semibold uppercase tracking-wider text-slate-800 hover:bg-slate-100 hover:text-[#FB6221] transition-all focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/40"
            >
                {{ $link['label'] }}
            </a>
        @endforeach

        <div class="pt-2 mt-1 border-t border-slate-200/70 flex flex-col gap-1">
            <a
                href="tel:+918506888288"
                class="flex items-center gap-2.5 px-4 py-3 min-h-[44px] text-xs font-semibold text-slate-700 hover:text-[#0A0A0A] hover:bg-slate-100 rounded-xl transition-colors focus:outline-hidden focus:ring-2 focus:ring-[#FB6221]/40"
            >
                <span class="material-symbols-outlined text-[18px] text-[#FB6221]">call</span>
                <span>+91 8506 888 288</span>
            </a>
            <a
                href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20know%20more%20about%20SAP%20courses."
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2.5 px-4 py-3 min-h-[44px] text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50/60 rounded-xl transition-colors focus:outline-hidden focus:ring-2 focus:ring-emerald-500/40"
            >
                <span class="material-symbols-outlined text-[18px]">chat</span>
                <span>WhatsApp Helpline</span>
            </a>
        </div>
    </nav>
</header>
