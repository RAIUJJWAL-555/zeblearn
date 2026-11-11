@php
    $links = [
        ['label' => 'Courses', 'href' => url('/#course-deck')],
        ['label' => 'Placements', 'href' => url('/#placements')],
        ['label' => 'Stories', 'href' => url('/#stories')],
        ['label' => 'Blogs', 'href' => route('blogs')],
        ['label' => 'About', 'href' => route('about')],
        ['label' => 'Contact', 'href' => route('contact')],
    ];
@endphp

<header class="fixed inset-x-0 top-3 sm:top-4 z-50 px-3 sm:px-5">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 rounded-full border border-white/70 bg-white/80 px-5 py-2.5 shadow-[0_8px_32px_rgba(10,10,10,0.08)] backdrop-blur-xl sm:px-7">
        
        <a href="{{ url('/') }}" aria-label="ZebLearn home" class="flex shrink-0 items-center h-8 sm:h-9 w-[102px] sm:w-[114px] overflow-hidden group">
            <picture class="dark:hidden">
                <source srcset="{{ asset('logo/logo1-400.webp') }}" type="image/webp">
                <img src="{{ asset('logo/logo1.png') }}" alt="ZebLearn" width="400" height="126" fetchpriority="high" decoding="sync" style="aspect-ratio: 1000/315;" class="h-8 sm:h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]" />
            </picture>
            <picture class="hidden dark:block">
                <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                <img src="{{ asset('logo/logo2.png') }}" alt="ZebLearn" width="400" height="126" decoding="async" style="aspect-ratio: 1000/315;" class="h-8 sm:h-9 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]" />
            </picture>
        </a>

        <nav aria-label="Main navigation" class="hidden items-center gap-1 lg:flex">
            @foreach($links as $link)
                <a href="{{ $link['href'] }}" class="rounded-full px-3 py-2 text-sm font-medium text-[#292929] transition hover:bg-white hover:text-[#FB6221]">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('book-demo') }}" class="rounded-full bg-[#FB6221] px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#e94f12] sm:px-5 sm:text-sm">
                Book a Demo
            </a>
            <details class="group relative lg:hidden">
                <summary aria-label="Open navigation menu" class="grid h-10 w-10 cursor-pointer list-none place-items-center rounded-full border border-black/10 bg-white text-[#0A0A0A] [&::-webkit-details-marker]:hidden">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <nav aria-label="Mobile navigation" class="absolute right-0 top-12 z-50 w-64 rounded-3xl border border-white/70 bg-white/95 p-3 shadow-xl backdrop-blur-xl">
                    @foreach($links as $link)
                        <a href="{{ $link['href'] }}" class="block rounded-2xl px-4 py-3 text-sm font-medium text-[#0A0A0A] transition hover:bg-[#F4F4F4] hover:text-[#FB6221]">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </nav>
            </details>
        </div>
    </div>
</header>
