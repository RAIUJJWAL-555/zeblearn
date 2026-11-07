@extends('layouts.app')

@section('content')
<main class="w-full pt-8 sm:pt-10 bg-canvas-base min-h-screen">
    <div class="flex flex-col w-full">
        <!-- HERO SECTION -->
        <section class="relative w-full overflow-hidden px-margin-mobile md:px-margin pt-space-xl pb-space-3xl">
            <div class="max-w-[1440px] mx-auto relative">
                <!-- Top Meta Track Bar (cleaned) -->
                <div class="flex items-center justify-between border-b border-hairline-border pb-space-sm mb-space-md hidden md:flex">
                    <div class="flex items-center gap-space-sm">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-white text-ink-primary font-label-mono text-label-mono uppercase shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
                            2,500+ learners trained
                        </span>
                    </div>
                </div>
                <!-- Massive Condensed Display Headline -->
                <div class="relative select-none text-center leading-none">
                    <h1 class="font-display-xl text-[clamp(5rem,17vw,17rem)] leading-[0.85] uppercase text-ink-primary block">
                        LEARN SAP ONLINE. GET HIRED.
                    </h1>
                </div>
                <!-- TACTILE HERO COMPOSITION -->
                <div class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center">
                    <!-- Reaching Hand (transparent PNG/WebP required) -->
                    <img src="{{ asset('hero-hand.webp') }}" alt="Hand reaching" class="w-48 sm:w-64 md:w-80 lg:w-96 -mb-16 sm:-mb-24 md:-mb-32 z-20 pointer-events-none drop-shadow-2xl" />
                    <!-- Orange Enroll Key (transparent PNG/WebP required) -->
                    <img src="{{ asset('hero-key.webp') }}" alt="Enroll key" class="w-32 sm:w-40 md:w-48 aspect-square drop-shadow-[0_24px_36px_rgba(251,98,33,0.45)] z-30" />
                </div>
                <!-- Flanking Editorial Meta Blocks -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mt-space-xl pt-space-xl items-end relative z-10">
                    <!-- Left: Mission Statement -->
                    <div class="md:col-span-4 bg-surface-white/80 backdrop-blur-sm p-space-lg rounded-[24px] shadow-sm">
                        <div class="flex items-center gap-2 mb-space-sm">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span>
                            <span class="font-label-mono text-label-mono uppercase tracking-widest text-ink-primary font-bold">About ZebLearn</span>
                        </div>
                        <p class="font-body-lg text-body-lg text-on-surface leading-snug font-medium mb-space-sm">
                            Live SAP training in FICO, MM, SD and PP with real system practice, mock interviews and placement support. Trusted by 2,500+ learners.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            <a href="#" data-path="contact" class="px-4 py-2 rounded-full text-white font-body-md font-semibold text-[13px] shadow-sm hover:scale-[1.02] transition-transform" style="background-color: #FB6221;">
                                <!-- Lucide Arrow Right -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="inline-block mr-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
                                Book a Free Demo
                            </a>
                            <a href="#course-deck" data-path="courses" class="px-4 py-2 rounded-full border border-hairline-border bg-surface-white font-body-md font-semibold text-[13px] text-ink-primary hover:bg-canvas-glow transition-colors">
                                <!-- Lucide Play Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="inline-block mr-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v18l15-9L5 3"/></svg>
                                Explore SAP Courses
                            </a>
                        </div>
                    </div>
                    <!-- Center Spacer -->
                    <div class="hidden md:block md:col-span-4 h-1"></div>
                    <!-- Right: Video Thumbnail -->
                    <div class="md:col-span-4 flex justify-end">
                        <div class="relative w-48 h-28 sm:w-64 sm:h-36 bg-surface-white rounded-lg overflow-hidden">
                            <!-- Placeholder thumbnail image -->
                            <img src="{{ asset('video-thumbnail.jpg') }}" alt="Video preview" class="w-full h-full object-cover" />
                            <!-- Orange play circle -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="#FB6221"><circle cx="12" cy="12" r="12" opacity="0.2"/><polygon points="10,8 16,12 10,16" fill="#FB6221"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection


