@extends('layouts.app')

@php
    $pageTitle = $course->resolved_seo_title;
    $pageDesc = $course->resolved_seo_description;
    $canonicalPath = request()->getPathInfo();
    $defaultCanonical = 'https://sap.zeblearn.com' . ($canonicalPath === '/' ? '/' : rtrim($canonicalPath, '/'));
    $canonicalUrl = !empty($course->canonical_url) ? $course->canonical_url : $defaultCanonical;
    $ogImage = $course->resolved_og_image;
    $robots = $course->robots_directive ?: 'index, follow, max-image-preview:large';
    $sections = $course->resolved_section_config;
    $trustConfig = $course->resolved_trust_config;
    $trustRecords = app(\App\Services\TrustProofService::class)->normalizeRecords(
        $trustRecords ?? app(\App\Services\TrustProofService::class)->getRecordsForCourse($course)
    );

    // Structured JSON-LD Data Builder
    $courseSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Course',
        'name' => $course->name,
        'description' => $pageDesc,
        'provider' => [
            '@type' => 'Organization',
            'name' => 'ZebLearn',
            'sameAs' => 'https://sap.zeblearn.com',
            'logo' => asset('images/logo.png'),
        ],
        'offers' => [
            '@type' => 'Offer',
            'category' => 'Paid',
            'price' => (string) ($course->fee ?? 25000),
            'priceCurrency' => 'INR',
            'availability' => $course->isAvailable() ? 'https://schema.org/InStock' : 'https://schema.org/PreOrder',
            'url' => $canonicalUrl,
        ],
        'aggregateRating' => [
            '@type' => 'AggregateRating',
            'ratingValue' => (string) ($course->rating ?? 4.9),
            'reviewCount' => (string) ($course->ratings_count ?? 1250),
            'bestRating' => '5',
            'worstRating' => '1',
        ],
    ];

    if ($course->activeBatches->isNotEmpty()) {
        $courseInstances = [];
        foreach ($course->activeBatches as $batch) {
            $courseInstances[] = [
                '@type' => 'CourseInstance',
                'courseMode' => $batch->delivery_mode ?? 'online',
                'startDate' => $batch->start_date ? $batch->start_date->toDateString() : now()->toDateString(),
                'location' => [
                    '@type' => 'VirtualLocation',
                    'url' => $canonicalUrl,
                ],
            ];
        }
        $courseSchema['hasCourseInstance'] = $courseInstances;
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Courses',
                'item' => route('courses.index'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $course->name,
                'item' => $canonicalUrl,
            ],
        ],
    ];

    $faqSchema = null;
    if ($course->activeFaqs->isNotEmpty()) {
        $faqItems = [];
        foreach ($course->activeFaqs as $faq) {
            $faqItems[] = [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => strip_tags($faq->answer),
                ],
            ];
        }
        $faqSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqItems,
        ];
    }
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)
@section('canonical', $canonicalUrl)
@section('robots', $robots)
@section('og_type', 'website')
@section('og_title', $pageTitle)
@section('og_description', $pageDesc)
@section('og_image', $ogImage)
@section('twitter_title', $pageTitle)
@section('twitter_description', $pageDesc)
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@push('schema')
    <script type="application/ld+json">{!! json_encode($courseSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @if ($faqSchema)
        <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
@endpush

@section('content')
<div
    class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]"
    x-data="{
        videoModalOpen: false,
        activeVideoUrl: '',
        activeVideoTitle: '',
        activeVideoStudent: '',
        activeVideoRole: '',
        certModalOpen: false,
        activeCertImg: '',
        activeCertTitle: '',
        activeCertIssuer: '',
        activeCertDesc: '',
        activeCertId: '',
        openVideo(url, title, student, role) {
            this.activeVideoUrl = url;
            this.activeVideoTitle = title;
            this.activeVideoStudent = student || '';
            this.activeVideoRole = role || '';
            this.videoModalOpen = true;
        },
        openCert(img, title, issuer, desc, id) {
            this.activeCertImg = img;
            this.activeCertTitle = title;
            this.activeCertIssuer = issuer || 'ZebLearn Academy';
            this.activeCertDesc = desc || '';
            this.activeCertId = id || '';
            this.certModalOpen = true;
        }
    }"
    @keydown.escape.window="videoModalOpen = false; certModalOpen = false"
>

    @if (!empty($isPreview))
        <!-- Admin Preview Mode Banner -->
        <aside aria-label="Admin preview mode" class="w-full bg-amber-500 text-slate-950 px-4 py-2 text-center text-xs font-bold uppercase tracking-wider sticky top-0 z-50 shadow-md flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[16px]">visibility</span>
            <span>Admin Live Preview Mode &bull; {{ $course->isAvailableForEnrollment() ? 'Status: Published' : 'Status: Draft / Unpublished' }}</span>
        </aside>
    @endif

    @foreach ($sections as $sectionKey => $section)
        @if ($section['visible'])

            {{-- 1. HERO SECTION --}}
            @if ($sectionKey === 'hero')
                <section class="relative w-full pt-32 sm:pt-36 lg:pt-40 pb-12 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto overflow-hidden">
                    <!-- Breadcrumbs & Status Telemetry -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200">
                        <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
                            <span class="text-slate-300">/</span>
                            <a href="{{ route('courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                            <span class="text-slate-300">/</span>
                            <span class="text-[#0A0A0A] font-bold truncate max-w-xs sm:max-w-md">{{ $course->name }}</span>
                        </nav>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                            <span class="w-2 h-2 rounded-full {{ $course->isAvailable() ? 'bg-emerald-500 animate-pulse' : 'bg-[#FB6221]' }}"></span>
                            <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">{{ $course->status_label }}</span>
                        </div>
                    </div>

                    <!-- Hero Grid: Left Content, Right Quick Enrollment Card -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                        <div class="lg:col-span-8">
                            <div class="flex items-center gap-2 mb-3 flex-wrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                                    {{ $course->category_badge }}
                                </span>
                                @if($course->display_tag)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-black uppercase tracking-wider bg-amber-400 text-black shadow-2xs">
                                        {{ $course->display_tag }}
                                    </span>
                                @endif
                                @if(!empty($course->delivery_mode))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-medium bg-slate-100 text-slate-700">
                                        {{ $course->delivery_mode }}
                                    </span>
                                @endif
                            </div>

                            <h1 class="font-heading text-3xl sm:text-5xl font-black uppercase tracking-tight text-[#0A0A0A] leading-tight">
                                {{ $course->hero_heading ?: $course->name }}
                            </h1>

                            @if($course->hero_subtitle)
                                <p class="font-sans text-lg sm:text-xl font-semibold text-slate-800 mt-3">
                                    {{ $course->hero_subtitle }}
                                </p>
                            @endif

                            <p class="font-sans text-base sm:text-lg text-slate-600 leading-relaxed mt-4 max-w-3xl">
                                {{ $course->hero_description ?: ($course->short_description ?: $course->description) }}
                            </p>

                            <!-- Meta Metrics Strip -->
                            <div class="flex flex-wrap items-center gap-4 mt-6 text-xs text-slate-600 font-medium">
                                <div class="flex items-center gap-1.5 text-amber-500">
                                    <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                                    <strong class="text-slate-900 font-bold">{{ $course->display_rating }}</strong>
                                    <span class="text-slate-500">({{ $course->display_ratings_count }} ratings)</span>
                                </div>
                                <span class="text-slate-300">•</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">schedule</span>
                                    <span>{{ $course->display_duration }}</span>
                                </div>
                                <span class="text-slate-300">•</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">menu_book</span>
                                    <span>{{ $course->display_modules }}</span>
                                </div>
                                <span class="text-slate-300">•</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-slate-400">group</span>
                                    <span>{{ $course->display_students }}</span>
                                </div>
                            </div>

                            <!-- CTAs -->
                            <div class="flex flex-wrap items-center gap-4 mt-8">
                                <a
                                    href="{{ $course->hero_cta_url ?: '#enrollment-section' }}"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-sm font-bold uppercase tracking-wider transition-all shadow-md"
                                >
                                    <span>{{ $course->hero_cta_text ?: 'Enroll in Program' }}</span>
                                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                                </a>
                                <a
                                    href="{{ $course->secondary_cta_url ?: route('book-demo', ['course' => $course->slug]) }}"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-[#0A0A0A] font-['Inter'] text-sm font-semibold uppercase tracking-wider border border-slate-300 transition-all shadow-xs"
                                >
                                    <span class="material-symbols-outlined text-[18px] text-[#FB6221]">play_circle</span>
                                    <span>{{ $course->secondary_cta_text ?: 'Book Free Demo' }}</span>
                                </a>
                            </div>
                        </div>

                        <!-- Right Column Quick Card -->
                        <div class="lg:col-span-4 lg:sticky lg:top-28" id="enrollment-section">
                            <div class="bg-[#0A0A0A] text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl relative overflow-hidden">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-mono text-[10px] font-bold uppercase text-[#FB6221] tracking-widest">
                                        {{ $course->isAvailableForEnrollment() ? 'ENROLMENT OPEN' : 'NEXT BATCH' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-white font-mono text-[10px] uppercase">
                                        {{ $course->delivery_mode ?: 'LIVE ONLINE' }}
                                    </span>
                                </div>

                                <!-- Tuition Fee Display -->
                                <div class="mb-6">
                                    <div class="flex items-baseline gap-2">
                                        <span class="font-heading text-4xl sm:text-5xl font-extrabold leading-none text-white tracking-tight">
                                            {{ $course->formatted_fee ?? ('₹' . number_format($course->fee ?? 25000)) }}
                                        </span>
                                        <span class="font-mono text-xs uppercase text-slate-400 font-bold">+ GST</span>
                                    </div>
                                    @if ($course->original_fee && $course->original_fee > ($course->fee ?? 0))
                                        <div class="flex items-center gap-2 mt-2">
                                            <span class="text-xs text-slate-400 line-through">₹{{ number_format($course->original_fee) }}</span>
                                            <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold uppercase rounded bg-emerald-500/20 text-emerald-300">
                                                {{ $course->discount_percent ? $course->discount_percent . '% OFF' : 'SPECIAL TUITION' }}
                                            </span>
                                        </div>
                                    @endif
                                    <p class="font-sans text-xs text-slate-400 mt-2">Live online interactive classes. Placement support included.</p>
                                </div>

                                <!-- Highlights Table -->
                                <div class="divide-y divide-white/10 mb-6 font-['Inter'] text-xs">
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="text-slate-400">Duration:</span>
                                        <span class="font-bold text-white">{{ $course->display_duration }}</span>
                                    </div>
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="text-slate-400">Format:</span>
                                        <span class="font-bold text-white">{{ $course->delivery_mode ?: 'Live Online + Labs' }}</span>
                                    </div>
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="text-slate-400">Certification:</span>
                                        <span class="font-bold text-white">{{ $course->certification_name ?: 'Course Completion Certificate' }}</span>
                                    </div>
                                    <div class="py-2.5 flex items-center justify-between">
                                        <span class="text-slate-400">Placement:</span>
                                        <span class="font-bold text-white">PlacementBaba Network</span>
                                    </div>
                                </div>

                                <!-- 5-State Enrollment CTA -->
                                <div class="space-y-3 mb-6">
                                    @if (! $course->isAvailableForEnrollment())
                                        @if ($course->status === 'upcoming')
                                            <a
                                                href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                                class="w-full py-3.5 px-4 bg-amber-400 hover:bg-amber-500 text-black font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                            >
                                                <span>Pre-Register for Next Batch</span>
                                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                            </a>
                                        @else
                                            <div class="w-full py-3 px-4 bg-white/10 rounded-xl text-center font-mono text-xs text-slate-300">
                                                Curriculum in Development
                                            </div>
                                        @endif
                                    @else
                                        {{-- Available: 5-state logic --}}
                                        @if ($enrollment && $enrollment->isApproved())
                                            <a
                                                href="{{ route('student.courses.show', $course) }}"
                                                class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                            >
                                                <span class="material-symbols-outlined text-[16px]">play_circle</span>
                                                <span>Continue Learning / View Course</span>
                                            </a>
                                        @elseif ($enrollment && $enrollment->isPending())
                                            <div class="w-full py-3 px-4 bg-amber-500/20 border border-amber-500/50 text-amber-300 font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl text-center flex items-center justify-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                                <span>Awaiting Approval</span>
                                            </div>
                                            <p class="text-[11px] text-center text-slate-400 font-sans">
                                                Your enrollment request is pending review by admissions.
                                            </p>
                                        @elseif ($enrollment && ($enrollment->isRejected() || $enrollment->isCancelled()))
                                            <div class="space-y-2 text-center">
                                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-red-500/20 text-red-300 text-[10px] font-mono font-bold uppercase border border-red-500/30">
                                                    Application Declined
                                                </span>
                                                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="w-full py-3 px-4 bg-white/20 hover:bg-[#FB6221] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all text-center"
                                                    >
                                                        <span>Re-apply for Enrollment</span>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            {{-- Not Enrolled --}}
                                            @auth
                                                @if (auth()->user()->isStudent())
                                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2"
                                                        >
                                                            <span>Enroll Now</span>
                                                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <div class="w-full py-2.5 px-4 bg-white/10 rounded-xl text-center text-xs text-slate-300">
                                                        Staff View
                                                    </div>
                                                @endif
                                            @else
                                                <a
                                                    href="{{ route('courses.enroll.get', $course) }}"
                                                    class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                                >
                                                    <span>Enroll Now</span>
                                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                                </a>
                                            @endauth
                                        @endif
                                    @endif

                                    <!-- Free Demo Button -->
                                    <a
                                        href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                        class="w-full py-3 px-4 border border-white/20 hover:bg-white/10 text-white font-['Inter'] text-xs font-semibold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 text-center"
                                    >
                                        <span>Book a Free Demo</span>
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </a>

                                    <a
                                        href="tel:+918506888288"
                                        class="w-full py-2.5 px-4 text-slate-300 hover:text-white font-['Inter'] text-xs transition-colors flex items-center justify-center gap-2 text-center"
                                    >
                                        <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                                        <span>+91 8506 888 288</span>
                                    </a>
                                </div>

                                <div class="pt-4 border-t border-white/10 text-center">
                                    <p class="font-mono text-[10px] text-slate-500 uppercase">
                                        100% Placement Assistance &bull; LMS Access Included
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 2. ACCREDITATION & HIRING PARTNERS SECTION --}}
            @if ($sectionKey === 'partners' && ($trustConfig['sections']['partners']['enabled'] ?? true))
                @php
                    $partnersList = collect($trustRecords['partners'] ?? []);
                    $displayPartners = $partnersList->isNotEmpty()
                        ? $partnersList
                        : ($course->activePartners->isNotEmpty() ? $course->activePartners : collect());

                    $partnerFallbackLogos = [
                        'deloitte' => '/images/partners/deloitte.svg',
                        'ibm' => '/images/partners/ibm.svg',
                        'pwc' => '/images/partners/pwc.svg',
                        'accenture' => '/images/partners/accenture.svg',
                        'infosys' => '/images/partners/infosys.svg',
                        'tcs' => '/images/partners/tcs.svg',
                        'wipro' => '/images/partners/wipro.svg',
                        'capgemini' => '/images/partners/capgemini.svg',
                    ];
                @endphp
                @if ($displayPartners->isNotEmpty())
                    <section class="w-full bg-slate-100/70 border-y border-slate-200 py-10 px-4 sm:px-6 lg:px-12 overflow-hidden">
                        <div class="max-w-[1440px] mx-auto text-center">
                            <div class="flex flex-wrap items-center justify-center gap-2 mb-3">
                                <span class="px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-700 shadow-2xs">
                                    HIRING PARTNERS
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-700 shadow-2xs">
                                    GLOBAL SYSTEMS INTEGRATORS
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full bg-white border border-slate-200 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-700 shadow-2xs">
                                    ENTERPRISE ALLIANCES
                                </span>
                            </div>

                            <p class="font-mono text-xs uppercase font-bold text-slate-600 tracking-wider mb-6">
                                TRUSTED BY LEARNERS HIRED ACROSS GLOBAL ENTERPRISES &bull; VERIFIED PLACEMENT PIPELINE
                            </p>

                            <!-- Infinite Marquee of Company Logos -->
                            <div class="zeb-marquee-container relative -mx-4 px-4 sm:-mx-6 sm:px-6 py-2 overflow-hidden" role="region" aria-label="Ecosystem and Hiring Partner Logos Marquee">
                                <!-- Edge Fade Masks -->
                                <div class="pointer-events-none absolute inset-y-0 left-0 w-12 sm:w-24 bg-gradient-to-r from-slate-100 via-slate-100/80 to-transparent z-10"></div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 w-12 sm:w-24 bg-gradient-to-l from-slate-100 via-slate-100/80 to-transparent z-10"></div>

                                <div class="zeb-marquee-track zeb-marquee-left flex gap-4 sm:gap-6 items-center focus:outline-none" style="--zeb-marquee-duration: 26s;">
                                    {{-- Primary Set --}}
                                    @foreach ($displayPartners as $partner)
                                        @php
                                            $partnerName = $partner->title ?? $partner->name ?? '';
                                            $slug = strtolower(trim($partnerName));
                                            $partnerLogo = $partner->image_path ?? $partner->logo_path ?? ($partnerFallbackLogos[$slug] ?? null);
                                            $partnerUrl = $partner->media_url ?? $partner->website_url ?? null;
                                            $partnerCategory = $partner->meta('partner_category') ?? 'Hiring Partner';
                                        @endphp
                                        <div class="shrink-0 h-20 px-6 sm:px-8 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#FB6221]/30 transition-all duration-200 flex flex-col items-center justify-center grayscale hover:grayscale-0 opacity-85 hover:opacity-100 group">
                                            @if($partnerUrl)
                                                <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" title="{{ $partnerName }} ({{ $partnerCategory }})" class="flex flex-col items-center justify-center h-full">
                                            @endif
                                            @if($partnerLogo)
                                                <img src="{{ $partnerLogo }}" alt="{{ $partnerName }} logo" class="h-6 sm:h-7 max-w-[120px] sm:max-w-[130px] object-contain select-none transition-transform group-hover:scale-105" loading="lazy">
                                            @else
                                                <span class="font-mono text-sm font-bold text-slate-800 tracking-tight">{{ $partnerName }}</span>
                                            @endif
                                            <span class="text-[9px] font-mono uppercase font-semibold text-slate-400 group-hover:text-[#FB6221] transition-colors mt-1">
                                                {{ $partnerCategory }}
                                            </span>
                                            @if($partnerUrl)
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- Duplicate Set for Seamless Continuous Loop --}}
                                    @foreach ($displayPartners as $partner)
                                        @php
                                            $partnerName = $partner->title ?? $partner->name ?? '';
                                            $slug = strtolower(trim($partnerName));
                                            $partnerLogo = $partner->image_path ?? $partner->logo_path ?? ($partnerFallbackLogos[$slug] ?? null);
                                            $partnerUrl = $partner->media_url ?? $partner->website_url ?? null;
                                            $partnerCategory = $partner->meta('partner_category') ?? 'Hiring Partner';
                                        @endphp
                                        <div class="shrink-0 h-20 px-6 sm:px-8 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#FB6221]/30 transition-all duration-200 flex flex-col items-center justify-center grayscale hover:grayscale-0 opacity-85 hover:opacity-100 group" aria-hidden="true">
                                            @if($partnerUrl)
                                                <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" tabindex="-1" class="flex flex-col items-center justify-center h-full">
                                            @endif
                                            @if($partnerLogo)
                                                <img src="{{ $partnerLogo }}" alt="{{ $partnerName }} logo" class="h-6 sm:h-7 max-w-[120px] sm:max-w-[130px] object-contain select-none transition-transform group-hover:scale-105" loading="lazy">
                                            @else
                                                <span class="font-mono text-sm font-bold text-slate-800 tracking-tight">{{ $partnerName }}</span>
                                            @endif
                                            <span class="text-[9px] font-mono uppercase font-semibold text-slate-400 group-hover:text-[#FB6221] transition-colors mt-1">
                                                {{ $partnerCategory }}
                                            </span>
                                            @if($partnerUrl)
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 3. OVERVIEW & OBJECTIVES SECTION --}}
            @if ($sectionKey === 'overview')
                <section class="w-full py-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-8 space-y-8">
                            <div>
                                <h2 class="font-heading text-2xl sm:text-3xl font-black uppercase tracking-tight text-[#0A0A0A] mb-4">
                                    Course Overview &amp; Curriculum Scope
                                </h2>
                                <div class="font-sans text-base text-slate-600 leading-relaxed space-y-4">
                                    {!! nl2br(e($course->course_objectives ?: ($course->description ?: $course->short_description))) !!}
                                </div>
                            </div>

                            @if($course->prerequisites)
                                <div class="bg-white rounded-2xl p-6 border border-slate-200">
                                    <h3 class="font-heading text-lg font-bold uppercase tracking-tight text-slate-900 mb-2 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[#FB6221]">check_circle</span>
                                        <span>Prerequisites &amp; Eligibility</span>
                                    </h3>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        {{ $course->prerequisites }}
                                    </p>
                                </div>
                            @endif

                            @if($course->target_audience_text || !empty($course->target_audience))
                                <div class="bg-white rounded-2xl p-6 border border-slate-200">
                                    <h3 class="font-heading text-lg font-bold uppercase tracking-tight text-slate-900 mb-2 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[#FB6221]">groups</span>
                                        <span>Who Should Take This Course?</span>
                                    </h3>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        {{ $course->target_audience_text ?: (is_array($course->target_audience) ? implode(', ', $course->target_audience) : $course->target_audience) }}
                                    </p>
                                </div>
                            @endif

                            @if($course->career_opportunities)
                                <div class="bg-white rounded-2xl p-6 border border-slate-200">
                                    <h3 class="font-heading text-lg font-bold uppercase tracking-tight text-slate-900 mb-2 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[#FB6221]">work</span>
                                        <span>Career Paths &amp; Job Profiles</span>
                                    </h3>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        {{ $course->career_opportunities }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="lg:col-span-4 space-y-6">
                            <div class="bg-white rounded-2xl p-6 border border-slate-200">
                                <h3 class="font-heading text-base font-bold uppercase tracking-tight text-slate-900 mb-4">
                                    Key Course Highlights
                                </h3>
                                @php
                                    $highlightsList = $course->highlights ?: [
                                        '100% Live Instructor-led training with real consultants',
                                        '24/7 High-speed remote SAP server sandbox practice',
                                        'Real-world business blueprinting & enterprise implementation projects',
                                        'Placement assistance with interview prep and resume enhancement',
                                        'Official certificate of course completion',
                                    ];
                                @endphp
                                <ul class="space-y-3 text-xs sm:text-sm text-slate-600 font-sans">
                                    @foreach ($highlightsList as $highlight)
                                        <li class="flex items-start gap-2.5">
                                            <span class="material-symbols-outlined text-[18px] text-[#FB6221] shrink-0 mt-0.5">verified</span>
                                            <span>{{ $highlight }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 4. OUTCOMES & SKILLS COVERED --}}
            @if ($sectionKey === 'outcomes')
                <section class="w-full bg-white py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-200">
                    <div class="max-w-[1440px] mx-auto">
                        <div class="text-center max-w-3xl mx-auto mb-12">
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">COMPETENCY FRAMEWORK</span>
                            <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                                Learning Outcomes &amp; Skills Acquired
                            </h2>
                            <p class="text-slate-600 text-sm sm:text-base mt-2">
                                Master real enterprise workflows with tangible practical skills demanded by top employers.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @php
                                $outcomesList = $course->learning_outcomes ?: [
                                    'End-to-End Enterprise Process Configuration & Customization',
                                    'Real-time Business Scenario Troubleshooting & Issue Resolution',
                                    'Integration with Cross-Functional Functional & Technical Modules',
                                    'Standard Operating Procedures & Audit Compliance Workflows',
                                    'Data Migration, Legacy Upload & Cutover Readiness',
                                    'Hands-on Practical Case Studies from Live Industry Blueprints',
                                ];
                            @endphp

                            @foreach ($outcomesList as $index => $outcome)
                                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-[#FB6221]/50 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-orange-100 text-[#FB6221] font-mono font-bold text-xs flex items-center justify-center mb-4">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <h3 class="font-heading text-base font-bold text-slate-900 mb-2">
                                        {{ $outcome }}
                                    </h3>
                                    <p class="text-xs text-slate-500 font-sans leading-relaxed">
                                        Demonstrate professional competency through rigorous project evaluations and practical tasks.
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        @if (!empty($course->skills_covered))
                            <div class="mt-12 pt-8 border-t border-slate-100">
                                <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-slate-500 text-center mb-6">
                                    TOOLS, PLATFORMS &amp; SKILLS COVERED
                                </h3>
                                <div class="flex flex-wrap items-center justify-center gap-2 max-w-4xl mx-auto">
                                    @foreach ($course->skills_covered as $skill)
                                        <span class="px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-orange-50 text-slate-800 hover:text-[#FB6221] border border-slate-200 text-xs font-mono font-semibold transition-colors">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- 5. VERIFIED STUDENT PLACEMENTS SECTION --}}
            @if ($sectionKey === 'placements' && ($trustConfig['sections']['placements']['enabled'] ?? true))
                @php
                    $placements = collect($trustRecords['placements'] ?? []);
                @endphp
                @if ($placements->isNotEmpty())
                    <section class="w-full bg-white py-16 px-4 sm:px-6 lg:px-12 border-b border-slate-200">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">CAREER IMPACT &amp; PLACEMENTS</span>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                                    Recent Verified Student Placements
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base mt-2">
                                    Documented alumni transition into enterprise SAP consulting, functional procurement, and supply chain roles.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($placements as $item)
                                    @php
                                        $pMeta = $item->metadata ?? [];
                                        $studentName = $item->title;
                                        $role = $item->subtitle ?: ($pMeta['role'] ?? 'SAP Consultant');
                                        $company = $pMeta['company'] ?? ($item->content_category ?: 'Enterprise Consulting');
                                        $package = $pMeta['package_lpa'] ?? ($pMeta['package'] ?? null);
                                        $spec = $pMeta['specialization'] ?? null;
                                        $year = $pMeta['batch_year'] ?? '2026';
                                    @endphp
                                    <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/60 hover:bg-white hover:border-[#FB6221]/30 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                                        <div class="space-y-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-center gap-3">
                                                    @if ($item->image_path)
                                                        <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white shadow-xs shrink-0 ring-1 ring-slate-200">
                                                            <img src="{{ $item->image_path }}" alt="{{ $studentName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                                                        </div>
                                                    @else
                                                        <div class="w-12 h-12 rounded-full bg-orange-100 text-[#FB6221] font-bold font-mono text-sm flex items-center justify-center uppercase border border-orange-200 shrink-0">
                                                            {{ substr($studentName, 0, 2) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h3 class="font-heading text-sm font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors">{{ $studentName }}</h3>
                                                        <p class="text-xs text-slate-500 font-sans line-clamp-1">{{ $role }}</p>
                                                    </div>
                                                </div>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                                    <span class="material-symbols-outlined text-[13px]">verified</span>
                                                    <span>Verified</span>
                                                </span>
                                            </div>

                                            @if ($spec)
                                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-white border border-slate-200 text-slate-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                                                    <span>{{ $spec }}</span>
                                                </div>
                                            @endif

                                            @if ($item->description)
                                                <p class="text-xs text-slate-600 italic line-clamp-3 leading-relaxed">
                                                    &ldquo;{{ $item->description }}&rdquo;
                                                </p>
                                            @endif
                                        </div>

                                        <div class="pt-4 border-t border-slate-200/80 mt-4 flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-slate-400 text-[16px]">business</span>
                                                <span class="font-mono text-xs font-bold text-slate-800 truncate">{{ $company }}</span>
                                                <span class="text-slate-300 text-xs">•</span>
                                                <span class="font-mono text-[11px] text-slate-400">{{ $year }}</span>
                                            </div>
                                            @if ($package)
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-extrabold text-[#FB6221] bg-orange-50 border border-orange-200 shrink-0">
                                                    {{ $package }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-10 text-center">
                                <a
                                    href="{{ route('home') }}#placements"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-mono text-xs font-bold uppercase tracking-wider transition-all"
                                >
                                    <span>Explore Verified Student Career Transitions</span>
                                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 5. COMPREHENSIVE CURRICULUM ACCORDION --}}
            @if ($sectionKey === 'curriculum')
                <section class="w-full py-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto" id="curriculum">
                    <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                        <div>
                            <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">SYLLABUS &amp; LESSONS</span>
                            <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                                Comprehensive Curriculum
                            </h2>
                            <p class="text-slate-600 text-sm mt-1">
                                Structured step-by-step learning path engineered for beginner-to-advanced proficiency.
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700">
                                {{ $course->modules->count() }} Modules
                            </span>
                            <span class="font-mono text-xs font-bold px-3 py-1.5 rounded-lg bg-orange-50 border border-orange-200 text-[#FB6221]">
                                {{ $course->lessons->count() }} Total Lessons
                            </span>
                        </div>
                    </div>

                    @if ($course->modules->isEmpty())
                        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 text-slate-500">
                            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">menu_book</span>
                            <h3 class="font-heading text-lg font-bold text-slate-800">Curriculum is being customized</h3>
                            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                Our instructors regularly update syllabus topics to reflect current industry version releases. Book a demo to review the complete lesson catalog.
                            </p>
                        </div>
                    @else
                        <div x-data="{ openModule: 0 }" class="space-y-4">
                            @foreach ($course->modules as $index => $module)
                                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs transition-all">
                                    <button
                                        type="button"
                                        @click="openModule = (openModule === {{ $index }} ? -1 : {{ $index }})"
                                        class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50 transition-colors"
                                    >
                                        <div class="flex items-center gap-4">
                                            <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 font-mono text-xs font-bold flex items-center justify-center shrink-0">
                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <div>
                                                <h3 class="font-heading text-base font-bold text-slate-900">
                                                    {{ $module->title }}
                                                </h3>
                                                @if($module->description)
                                                    <p class="text-xs text-slate-500 font-sans mt-0.5 line-clamp-1">
                                                        {{ $module->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <span class="font-mono text-xs text-slate-400">
                                                {{ $module->lessons_count ?? $module->lessons->count() }} Lessons
                                            </span>
                                            <span class="material-symbols-outlined text-slate-400 text-[20px] transition-transform duration-200" :class="openModule === {{ $index }} ? 'rotate-180' : ''">
                                                expand_more
                                            </span>
                                        </div>
                                    </button>

                                    <div x-show="openModule === {{ $index }}" x-collapse class="border-t border-slate-100 px-6 py-4 bg-slate-50/50">
                                        @if ($module->lessons->isEmpty())
                                            <p class="text-xs text-slate-500 italic py-2">Lessons list available during orientation.</p>
                                        @else
                                            <ul class="divide-y divide-slate-100 text-xs sm:text-sm font-sans">
                                                @foreach ($module->lessons as $lIndex => $lesson)
                                                    <li class="py-2.5 flex items-center justify-between text-slate-700">
                                                        <div class="flex items-center gap-3">
                                                            <span class="material-symbols-outlined text-[18px] text-slate-400">
                                                                {{ $lesson->type === 'video' ? 'play_circle' : 'article' }}
                                                            </span>
                                                            <span>{{ $lesson->title }}</span>
                                                        </div>
                                                        @if($lesson->duration_minutes)
                                                            <span class="font-mono text-[11px] text-slate-400">
                                                                {{ $lesson->duration_minutes }} mins
                                                            </span>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            {{-- 8. INDUSTRY PROJECTS & CASE STUDIES SECTION --}}
            @if ($sectionKey === 'projects' && ($trustConfig['sections']['projects']['enabled'] ?? true))
                @php
                    $projects = collect($trustRecords['projects'] ?? []);
                @endphp
                @if ($projects->isNotEmpty())
                    <section class="w-full bg-[#0B1120] text-white py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-800" id="projects">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#FB6221] font-mono text-[11px] font-bold uppercase tracking-wider mb-2 border border-white/10">
                                    <span class="material-symbols-outlined text-[14px]">terminal</span>
                                    <span>PRACTICAL IMPLEMENTATION BLUEPRINTS</span>
                                </div>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-white mt-1">
                                    Enterprise Capstone &amp; Implementation Projects
                                </h2>
                                <p class="text-slate-400 text-sm sm:text-base mt-2">
                                    Production-grade SAP configuration scenarios. Clearly categorized between simulated enterprise capstones and practical implementation case studies.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($projects as $proj)
                                    @php
                                        $projMeta = $proj->metadata ?? [];
                                        $categoryLabel = $projMeta['category_label'] ?? 'Simulated Enterprise Capstone';
                                        $industry = $projMeta['industry'] ?? ($proj->content_category ?: 'Manufacturing & Logistics');
                                        $duration = $projMeta['duration'] ?? '3-4 Weeks';
                                        $scenario = $projMeta['scenario'] ?? null;
                                        $tasks = $projMeta['tasks'] ?? [];
                                        $modules = $projMeta['modules'] ?? ($projMeta['tech_stack'] ?? ['SAP MM']);
                                        $deliverables = $projMeta['deliverables'] ?? [];
                                        $outcomes = $projMeta['learning_outcomes'] ?? null;
                                    @endphp
                                    <div class="p-6 rounded-3xl border border-slate-800 bg-slate-900/90 hover:border-[#FB6221]/50 hover:bg-slate-900 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col justify-between group">
                                        <div class="space-y-4">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold uppercase tracking-wider bg-orange-500/10 text-[#FB6221] border border-orange-500/30">
                                                    {{ $categoryLabel }}
                                                </span>
                                                <span class="text-xs font-mono text-slate-400 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px] text-slate-400">schedule</span>
                                                    <span>{{ $duration }}</span>
                                                </span>
                                            </div>

                                            <div>
                                                <div class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-400 mb-1">
                                                    <span class="material-symbols-outlined text-[13px] text-slate-400">factory</span>
                                                    <span>{{ $industry }}</span>
                                                </div>
                                                <h3 class="font-heading text-base font-bold text-white group-hover:text-[#FB6221] transition-colors leading-snug">
                                                    {{ $proj->title }}
                                                </h3>
                                                @if ($proj->subtitle)
                                                    <p class="text-xs font-mono text-slate-400 mt-1 line-clamp-1">{{ $proj->subtitle }}</p>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-300 leading-relaxed font-sans line-clamp-3">
                                                {{ $proj->description }}
                                            </p>

                                            @if ($scenario)
                                                <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800/80 text-[11px] text-slate-400 font-sans">
                                                    <strong class="text-slate-300 block mb-0.5 font-mono text-[10px] uppercase tracking-wider">Business Context:</strong>
                                                    <span class="line-clamp-2">{{ $scenario }}</span>
                                                </div>
                                            @endif

                                            @if (!empty($tasks) && is_array($tasks))
                                                <div class="space-y-1.5 pt-2">
                                                    <p class="text-[10px] font-mono uppercase font-bold text-slate-400 tracking-wider">Core Practical Tasks:</p>
                                                    <ul class="space-y-1 text-xs text-slate-300 font-sans">
                                                        @foreach (array_slice($tasks, 0, 3) as $task)
                                                            <li class="flex items-start gap-2">
                                                                <span class="material-symbols-outlined text-[#FB6221] text-[14px] shrink-0 mt-0.5">check_circle</span>
                                                                <span class="line-clamp-1">{{ $task }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="pt-4 border-t border-slate-800/80 mt-5 space-y-3">
                                            @if (!empty($modules))
                                                <div class="flex flex-wrap items-center gap-1">
                                                    @foreach ((array) $modules as $modName)
                                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-[10px] font-mono text-slate-300 border border-slate-700/60">
                                                            {{ trim($modName) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if (!empty($deliverables) && is_array($deliverables))
                                                <div class="flex items-center gap-1.5 text-[10px] font-mono text-slate-400 truncate">
                                                    <span class="material-symbols-outlined text-[13px] text-emerald-400">task_alt</span>
                                                    <span class="truncate">Deliverables: {{ implode(', ', array_slice($deliverables, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-10 p-4 rounded-2xl bg-white/5 border border-white/10 text-center text-xs text-slate-400 max-w-2xl mx-auto flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[#FB6221] text-[18px]">info</span>
                                <span>Note: All training projects are executed in sandboxed dedicated training cloud servers reflecting genuine enterprise ERP configuration rules.</span>
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 7. EXPERT FACULTY & MENTORS SECTION --}}
            @if ($sectionKey === 'faculty' && ($trustConfig['sections']['trainers']['enabled'] ?? true))
                @php
                    $trainersList = collect($trustRecords['trainers'] ?? []);
                    $displayFaculty = $trainersList->isNotEmpty()
                        ? $trainersList
                        : ($course->instructors->isNotEmpty() ? $course->instructors : collect());
                @endphp
                @if ($displayFaculty->isNotEmpty())
                    <section class="w-full bg-white py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-200" id="faculty">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 text-[#FB6221] font-mono text-[11px] font-bold uppercase tracking-wider mb-2 border border-orange-200">
                                    <span class="material-symbols-outlined text-[14px]">badge</span>
                                    <span>PRACTITIONER FACULTY &bull; INDUSTRY MENTORSHIP</span>
                                </div>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1">
                                    Learn Directly From Certified Industry Mentors
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base mt-2">
                                    Guided by seasoned SAP practitioners with 10+ years of active enterprise implementation and S/4HANA migration experience.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                @foreach ($displayFaculty as $faculty)
                                    @php
                                        $fName = $faculty->title ?? $faculty->name;
                                        $fRole = $faculty->subtitle ?? ($faculty->pivot->role_or_title ?? ($faculty->meta('designation') ?? 'Lead Enterprise Consultant'));
                                        $fBio = $faculty->description ?: ($faculty->bio ?: 'Specialist in enterprise lifecycle rollouts, configuration standards, and consultant mentoring.');
                                        $fAvatar = $faculty->image_path ?? $faculty->avatar;
                                        $fExpYears = $faculty->meta('experience_years') ?? ($faculty->experience_years ?? 12);
                                        $fSpec = $faculty->meta('specialization') ?? ($faculty->specialization ?? 'SAP MM & S/4HANA Sourcing');
                                        $fQual = $faculty->meta('qualification') ?? ($faculty->qualification ?? 'B.Tech, MBA');
                                        $fCerts = $faculty->meta('certifications') ?? ['SAP Certified Associate'];
                                        $fLinkedIn = $faculty->media_url ?? ($faculty->meta('linkedin_url') ?? 'https://www.linkedin.com/company/zeblearn/');
                                    @endphp
                                    <div class="bg-slate-50/70 hover:bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 hover:border-slate-300 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                                        <div>
                                            <div class="flex items-start justify-between gap-4 mb-5">
                                                <div class="relative">
                                                    <div class="w-20 h-20 rounded-2xl bg-orange-100 text-[#FB6221] font-bold text-xl flex items-center justify-center uppercase overflow-hidden border-2 border-white shadow-sm ring-1 ring-slate-200">
                                                        @if($fAvatar)
                                                            <img src="{{ $fAvatar }}" alt="{{ $fName }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                                        @else
                                                            {{ substr($fName, 0, 2) }}
                                                        @endif
                                                    </div>
                                                    <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white rounded-full p-1 shadow-xs" title="Verified Practitioner Faculty">
                                                        <span class="material-symbols-outlined text-[14px] block">verified</span>
                                                    </div>
                                                </div>

                                                @if($fExpYears)
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[11px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200/80 shrink-0">
                                                        <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                                                        <span>{{ $fExpYears }}+ Yrs Exp</span>
                                                    </span>
                                                @endif
                                            </div>

                                            <div>
                                                <h3 class="font-heading text-lg font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors">{{ $fName }}</h3>
                                                <p class="text-xs font-mono font-semibold text-slate-700 mt-0.5">{{ $fRole }}</p>
                                                @if($fSpec)
                                                    <div class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-0.5 rounded-md text-[11px] font-mono text-slate-600 bg-white border border-slate-200 max-w-full truncate">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] shrink-0"></span>
                                                        <span class="truncate">{{ $fSpec }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-600 font-sans leading-relaxed mt-4 line-clamp-3">
                                                {{ $fBio }}
                                            </p>

                                            @if($fQual)
                                                <div class="mt-4 pt-3 border-t border-slate-200/70 flex items-center gap-2 text-xs text-slate-500 font-sans">
                                                    <span class="material-symbols-outlined text-slate-400 text-[16px] shrink-0">school</span>
                                                    <span class="truncate">{{ $fQual }}</span>
                                                </div>
                                            @endif

                                            @if(!empty($fCerts) && is_array($fCerts))
                                                <div class="mt-3 flex flex-wrap gap-1.5">
                                                    @foreach($fCerts as $certName)
                                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200/80 text-[10px] font-mono text-slate-600 font-medium">
                                                            {{ $certName }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        @if($fLinkedIn)
                                            <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center justify-between">
                                                <span class="text-[11px] font-mono text-slate-400">Verified Mentor</span>
                                                <a href="{{ $fLinkedIn }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-[#FB6221] hover:text-slate-900 transition-colors">
                                                    <span>LinkedIn Profile</span>
                                                    <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 9. CERTIFICATION & CERTIFICATE SHOWCASE SECTION --}}
            @if ($sectionKey === 'certification')
                @php
                    $certifications = collect($trustRecords['certifications'] ?? []);
                @endphp
                <section class="w-full py-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto" id="certification">
                    <div class="text-center max-w-3xl mx-auto mb-12">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 text-[#FB6221] font-mono text-[11px] font-bold uppercase tracking-wider mb-2 border border-orange-200">
                            <span class="material-symbols-outlined text-[14px]">workspace_premium</span>
                            <span>CREDENTIAL SHOWCASE &bull; VERIFICATION STANDARDS</span>
                        </div>
                        <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1">
                            Two-Tier Certification Architecture
                        </h2>
                        <p class="text-slate-600 text-sm sm:text-base mt-2">
                            Distinguishing ZebLearn's verifiable course completion credential from official external vendor examination readiness.
                        </p>
                    </div>

                    <!-- Dual Category Cards Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch mb-12">
                        <!-- Category A: ZebLearn Course Completion Certificate -->
                        <div class="lg:col-span-6 bg-gradient-to-br from-slate-900 via-slate-900 to-[#0A0A0A] text-white rounded-3xl p-7 sm:p-8 border border-slate-800 shadow-xl flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-800">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-xl bg-[#FB6221]/20 text-[#FB6221] flex items-center justify-center border border-[#FB6221]/30">
                                            <span class="material-symbols-outlined text-[22px]">verified</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-mono uppercase font-bold text-[#FB6221] tracking-wider block">CATEGORY A</span>
                                            <h3 class="font-heading text-lg font-bold text-white">ZebLearn Course Completion Certificate</h3>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        Included
                                    </span>
                                </div>

                                <!-- Sample Certificate Card Preview -->
                                <div class="mt-6 rounded-2xl overflow-hidden border border-slate-700/80 bg-slate-950 p-3 group relative cursor-pointer"
                                     @click="openCert('/images/certificates/certificate-1.webp', 'ZebLearn Certified SAP Materials Management Professional', 'ZebLearn Academy', 'Cryptographically Verifiable Institutional Credential', 'ZEB-MM-2026-8941')">
                                    <div class="relative aspect-[16/10] bg-slate-800 rounded-xl overflow-hidden">
                                        <img src="/images/certificates/certificate-1.webp" alt="ZebLearn Course Completion Certificate Sample" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300" loading="lazy">
                                        <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900/90 text-white text-xs font-mono font-bold shadow-md">
                                                <span class="material-symbols-outlined text-[16px]">zoom_in</span>
                                                <span>Click to Inspect Sample</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="pt-3 px-1 flex items-center justify-between text-[11px] font-mono text-slate-400">
                                        <span>Sample ID: ZEB-MM-2026-8941</span>
                                        <span class="text-emerald-400 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">qr_code_2</span>
                                            <span>QR Verifiable</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Rigorous Requirements -->
                                <div class="mt-6 space-y-3">
                                    <h4 class="text-xs font-mono font-bold uppercase text-slate-300 tracking-wider">Mandatory Completion Criteria:</h4>
                                    <ul class="space-y-2 text-xs text-slate-300 font-sans">
                                        <li class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-[#FB6221] text-[15px] shrink-0 mt-0.5">check_circle</span>
                                            <span><strong>85%+ Live Cohort Attendance:</strong> Active participation in live lectures and lab exercises.</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-[#FB6221] text-[15px] shrink-0 mt-0.5">check_circle</span>
                                            <span><strong>100% Practical Assignment Completion:</strong> Submitting modular configurations for instructor evaluation.</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-[#FB6221] text-[15px] shrink-0 mt-0.5">check_circle</span>
                                            <span><strong>End-of-Course Capstone Evaluation:</strong> Achieving minimum 75% score on cumulative evaluation.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-6 pt-5 border-t border-slate-800 flex items-center justify-between text-xs">
                                <span class="font-mono text-slate-400 text-[11px]">Issuer: ZebLearn Academy</span>
                                <a href="{{ route('certificates.verify') }}" class="text-[#FB6221] hover:underline font-mono font-bold flex items-center gap-1">
                                    <span>Verify Any Credential</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </div>
                        </div>

                        <!-- Category B: External Professional Certifications Alignment -->
                        <div class="lg:col-span-6 bg-white rounded-3xl p-7 sm:p-8 border border-slate-200 shadow-xl flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-200">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-200">
                                            <span class="material-symbols-outlined text-[22px]">public</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-mono uppercase font-bold text-blue-600 tracking-wider block">CATEGORY B</span>
                                            <h3 class="font-heading text-lg font-bold text-slate-900">External Professional Certification Alignment</h3>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                        Exam Prep
                                    </span>
                                </div>

                                <!-- External Credential Details -->
                                <div class="mt-6 p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-mono font-bold uppercase">
                                                Exam Code: C_TS452
                                            </span>
                                            <h4 class="font-heading text-base font-bold text-slate-900 mt-1.5">
                                                SAP Certified Associate – Procurement with SAP S/4HANA
                                            </h4>
                                            <p class="text-xs text-slate-500 font-sans mt-0.5">
                                                Official Global Certification Exam Conferred Directly by SAP SE
                                            </p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-2 shrink-0">
                                            <img src="/images/certificates/SAP 1.webp" alt="SAP Logo" class="max-h-full max-w-full object-contain" loading="lazy">
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-slate-200/80 grid grid-cols-2 gap-3 text-xs font-mono">
                                        <div>
                                            <span class="text-slate-400 text-[10px] block">ISSUING BODY</span>
                                            <span class="font-bold text-slate-800">SAP SE</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 text-[10px] block">EXAM FEES</span>
                                            <span class="font-bold text-slate-800">Separate (Paid to SAP)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Comprehensive Preparation Support -->
                                <div class="mt-6 space-y-3">
                                    <h4 class="text-xs font-mono font-bold uppercase text-slate-700 tracking-wider">Included Preparation Support:</h4>
                                    <ul class="space-y-2 text-xs text-slate-600 font-sans">
                                        <li class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-blue-600 text-[15px] shrink-0 mt-0.5">task_alt</span>
                                            <span><strong>Official Exam Blueprint Coverage:</strong> Complete syllabus alignment with SAP S/4HANA sourcing topics.</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-blue-600 text-[15px] shrink-0 mt-0.5">task_alt</span>
                                            <span><strong>200+ Practice Mock Questions:</strong> Timed simulated test environments matching official testing rigor.</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="material-symbols-outlined text-blue-600 text-[15px] shrink-0 mt-0.5">task_alt</span>
                                            <span><strong>Consultant Walkthroughs:</strong> Deep review of scenario-based questions and common pitfall traps.</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Explicit Compliance Disclaimer -->
                                <div class="mt-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 leading-relaxed font-sans flex items-start gap-2">
                                    <span class="material-symbols-outlined text-amber-600 text-[16px] shrink-0 mt-0.5">warning</span>
                                    <span><strong>Regulatory Notice:</strong> ZebLearn provides comprehensive exam preparation and syllabus coaching. ZebLearn does not claim to issue official SAP-authorized vendor certifications, which are administered directly by SAP SE.</span>
                                </div>
                            </div>

                            <div class="mt-6 pt-5 border-t border-slate-200 flex items-center justify-between text-xs">
                                <span class="font-mono text-slate-500 text-[11px]">Official Portal: learning.sap.com</span>
                                <a href="https://learning.sap.com/certification" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline font-mono font-bold flex items-center gap-1">
                                    <span>SAP Certification Details</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Gallery Strip -->
                    @if ($certifications->isNotEmpty())
                        <div class="pt-8 border-t border-slate-200">
                            <h3 class="font-mono text-xs font-bold uppercase tracking-wider text-slate-500 text-center mb-6">
                                VERIFIED CERTIFICATE SAMPLES &amp; CREDENTIAL GALLERY
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach ($certifications as $cert)
                                    @php
                                        $cMeta = $cert->metadata ?? [];
                                        $cImg = $cert->image_path ?: '/images/certificates/certificate-1.webp';
                                        $cIssuer = $cMeta['issuer'] ?? 'ZebLearn Academy';
                                        $cType = $cMeta['credential_type'] ?? 'Course Completion';
                                        $cId = $cMeta['sample_credential_id'] ?? ($cert->id ? 'ZEB-CR-' . $cert->id : 'ZEB-SAMPLE');
                                        $isSample = !empty($cMeta['is_sample']);
                                    @endphp
                                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs hover:shadow-md transition-all group flex flex-col justify-between">
                                        <div>
                                            <div class="relative aspect-[16/10] rounded-xl overflow-hidden bg-slate-100 border border-slate-200/80 mb-3 cursor-pointer"
                                                 @click="openCert('{{ $cImg }}', '{{ addslashes($cert->title) }}', '{{ addslashes($cIssuer) }}', '{{ addslashes($cert->description ?? '') }}', '{{ $cId }}')">
                                                <img src="{{ $cImg }}" alt="{{ $cert->title }}" class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-300" loading="lazy">
                                                <div class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                                    <span class="px-2.5 py-1 rounded-full bg-slate-900/90 text-white font-mono text-[10px] font-bold">
                                                        Inspect Full Size
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $isSample ? 'bg-orange-50 text-[#FB6221] border border-orange-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                                {{ $cType }}
                                            </span>
                                            <h4 class="font-heading text-sm font-bold text-slate-900 mt-2 line-clamp-1">{{ $cert->title }}</h4>
                                            <p class="text-xs text-slate-500 font-sans mt-1 line-clamp-2">{{ $cert->description }}</p>
                                        </div>
                                        <div class="pt-3 border-t border-slate-100 mt-4 flex items-center justify-between text-[11px] font-mono text-slate-400">
                                            <span>{{ $cIssuer }}</span>
                                            <span class="text-slate-600 font-semibold">{{ $cId }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            {{-- 8. UPCOMING BATCHES SECTION --}}
            @if ($sectionKey === 'batches')
                <section class="w-full bg-white py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-200" id="batches">
                    <div class="max-w-[1440px] mx-auto">
                        <div class="flex flex-wrap items-end justify-between gap-4 mb-8">
                            <div>
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">SCHEDULE &amp; SEATS</span>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                                    Upcoming Live Cohort Batches
                                </h2>
                                <p class="text-slate-600 text-sm mt-1">
                                    Small interactive cohorts capped at 25 participants for high mentor engagement.
                                </p>
                            </div>
                        </div>

                        @if ($course->activeBatches->isEmpty())
                            <div class="bg-slate-50 rounded-2xl p-8 border border-slate-200 text-center">
                                <span class="material-symbols-outlined text-4xl text-[#FB6221] mb-2 block">event_available</span>
                                <h3 class="font-heading text-lg font-bold text-slate-800">New Cohorts Opening Soon</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                                    We announce new weekend and weekday cohorts bi-weekly. Pre-register to reserve early priority access.
                                </p>
                                <a href="{{ route('book-demo', ['course' => $course->slug]) }}" class="inline-block mt-4 px-5 py-2.5 rounded-xl bg-[#FB6221] text-white font-mono text-xs font-bold uppercase tracking-wider">
                                    Join Next Cohort Waitlist
                                </a>
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($course->activeBatches as $batch)
                                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase bg-orange-100 text-[#FB6221]">
                                                    {{ $batch->delivery_mode ?: 'Live Online' }}
                                                </span>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $batch->status === 'filling_fast' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $batch->status)) }}
                                                </span>
                                            </div>
                                            <h3 class="font-heading text-base font-bold text-slate-900 mb-2">
                                                {{ $batch->name }}
                                            </h3>
                                            <div class="space-y-2 text-xs text-slate-600 font-sans my-4">
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[16px] text-slate-400">calendar_month</span>
                                                    <span>Starts: <strong>{{ $batch->start_date ? $batch->start_date->format('d M, Y') : 'Announced Soon' }}</strong></span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                                                    <span>{{ $batch->timings }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[16px] text-slate-400">date_range</span>
                                                    <span>{{ $batch->days }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="material-symbols-outlined text-[16px] text-slate-400">airline_seat_recline_normal</span>
                                                    <span class="text-amber-700 font-medium">{{ $batch->available_seats }} of {{ $batch->total_seats }} seats remaining</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pt-4 border-t border-slate-200 mt-2">
                                            <a
                                                href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                                class="w-full py-2.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-xs flex items-center justify-center gap-2 text-center"
                                            >
                                                <span>Reserve Seat &bull; Book Demo</span>
                                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- 9. PRICING & TUITION BREAKDOWN SECTION --}}
            @if ($sectionKey === 'pricing')
                <section class="w-full py-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto">
                    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-xs">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            <div class="lg:col-span-7 space-y-4">
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">INVESTMENT &amp; ROI</span>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A]">
                                    Transparent Tuition &amp; Flexible Financial Options
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                                    No hidden fees. Your tuition covers complete live instruction, 24/7 dedicated system server access, exam preparation, and dedicated placement support.
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 text-xs text-slate-700 font-medium">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check</span>
                                        <span>No-cost EMI installments available</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check</span>
                                        <span>Corporate sponsorships accepted</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check</span>
                                        <span>GST invoice for tax deduction</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">check</span>
                                        <span>Lifetime access to course recordings</span>
                                    </div>
                                </div>
                            </div>

                            <div class="lg:col-span-5 bg-slate-50 rounded-2xl p-6 sm:p-8 border border-slate-200 text-center">
                                <span class="font-mono text-xs uppercase font-bold text-slate-500">PAYABLE TUITION FEE</span>
                                <div class="flex items-baseline justify-center gap-2 my-3">
                                    <span class="font-heading text-4xl sm:text-5xl font-extrabold text-slate-900">
                                        {{ $course->formatted_fee ?? ('₹' . number_format($course->fee ?? 25000)) }}
                                    </span>
                                </div>
                                @if ($course->original_fee && $course->original_fee > ($course->fee ?? 0))
                                    <div class="text-xs text-slate-500 mb-4">
                                        Standard Tuition: <span class="line-through">₹{{ number_format($course->original_fee) }}</span>
                                        <span class="font-bold text-emerald-600 ml-1">Save {{ $course->discount_percent ? $course->discount_percent . '%' : '' }}</span>
                                    </div>
                                @endif
                                <a
                                    href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                    class="w-full py-3.5 px-6 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-sm font-bold uppercase tracking-wider transition-all shadow-md block text-center"
                                >
                                    Book Free Class &amp; Inquire
                                </a>
                                <p class="text-[11px] text-slate-400 mt-2 font-mono">Scholarship discounts available for fresh college graduates.</p>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 10. TESTIMONIALS & REVIEWS SECTION --}}
            @if ($sectionKey === 'testimonials' && ($trustConfig['sections']['testimonials']['enabled'] ?? true))
                @php
                    $testimonialsList = collect($trustRecords['testimonials'] ?? []);
                    $displayTestimonials = $testimonialsList->isNotEmpty()
                        ? $testimonialsList
                        : ($course->publishedTestimonials->isNotEmpty() ? $course->publishedTestimonials : collect());
                @endphp
                @if ($displayTestimonials->isNotEmpty())
                    <section class="w-full bg-slate-50 py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-200">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">ALUMNI REVIEWS</span>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                                    What Our Students Say
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base mt-2">
                                    Real career transitions from working professionals and students who mastered this module.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($displayTestimonials as $test)
                                    @php
                                        $tName = $test->title ?? $test->student_name;
                                        $tRole = $test->subtitle ?? ($test->student_title_or_company ?? ($test->meta('author_role') ?? 'Student Alumni'));
                                        $tText = $test->description ?? $test->review_text;
                                        $tRating = (int) ($test->meta('rating') ?? ($test->rating ?? 5));
                                        $tAvatar = $test->image_path ?? $test->avatar_path;
                                    @endphp
                                    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center gap-1 text-amber-500 mb-3">
                                                @for ($i = 0; $i < $tRating; $i++)
                                                    <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                                                @endfor
                                            </div>
                                            <p class="text-xs sm:text-sm text-slate-700 font-sans leading-relaxed italic">
                                                &ldquo;{{ $tText }}&rdquo;
                                            </p>
                                        </div>
                                        <div class="pt-4 border-t border-slate-100 mt-4 flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-orange-100 text-[#FB6221] font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                                                @if($tAvatar)
                                                    <img src="{{ $tAvatar }}" alt="{{ $tName }}" class="w-full h-full object-cover rounded-full">
                                                @else
                                                    {{ substr($tName, 0, 2) }}
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-heading text-xs font-bold text-slate-900">{{ $tName }}</h4>
                                                <p class="text-[11px] text-slate-500 font-sans">{{ $tRole }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 10. STUDENT VIDEO STORIES SECTION --}}
            @if ($sectionKey === 'video_stories' && ($trustConfig['sections']['video_stories']['enabled'] ?? true))
                @php
                    $videos = collect($trustRecords['video_stories'] ?? []);
                @endphp
                @if ($videos->isNotEmpty())
                    <section class="w-full bg-white py-16 px-4 sm:px-6 lg:px-12 border-b border-slate-200" id="video-stories">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 text-[#FB6221] font-mono text-[11px] font-bold uppercase tracking-wider mb-2 border border-orange-200">
                                    <span class="material-symbols-outlined text-[14px]">smart_display</span>
                                    <span>ALUMNI VIDEO TESTIMONIALS</span>
                                </div>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1">
                                    Watch Genuine Student Success Stories
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base mt-2">
                                    Documented, consented video interviews of working professionals and freshers who launched their ERP careers with ZebLearn.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                @foreach ($videos as $vid)
                                    @php
                                        $vMeta = $vid->metadata ?? [];
                                        $duration = $vMeta['duration'] ?? '3 min';
                                        $vUrl = $vid->media_url ?: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                                        $student = $vMeta['student_name'] ?? $vid->title;
                                        $role = $vMeta['role'] ?? ($vid->subtitle ?: 'SAP MM Consultant');
                                        $company = $vMeta['company'] ?? 'Enterprise ERP Solutions';
                                        $package = $vMeta['package_lpa'] ?? null;
                                        $quote = $vid->description ?: "ZebLearn's hands-on server practice and real-world P2P project scenarios gave me the exact confidence I needed to clear technical interview rounds.";
                                    @endphp
                                    <div class="group rounded-3xl border border-slate-200 bg-slate-50/60 hover:bg-white overflow-hidden hover:shadow-xl hover:border-[#FB6221]/30 transition-all duration-300 flex flex-col justify-between">
                                        <div>
                                            {{-- Video Thumbnail Card with Play Trigger --}}
                                            <div
                                                class="relative aspect-video bg-slate-900 flex items-center justify-center overflow-hidden cursor-pointer"
                                                @click="openVideo('{{ $vUrl }}', '{{ addslashes($vid->title) }}', '{{ addslashes($student) }}', '{{ addslashes($role . ' @ ' . $company) }}')"
                                            >
                                                @if ($vid->image_path)
                                                    <img src="{{ $vid->image_path }}" alt="{{ $student }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 opacity-80" loading="lazy" />
                                                @endif
                                                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                                    <div class="w-14 h-14 rounded-full bg-[#FB6221] text-white flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform">
                                                        <span class="material-symbols-outlined text-[28px] fill-current">play_arrow</span>
                                                    </div>
                                                </div>
                                                <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded-md bg-black/80 backdrop-blur-xs text-white font-mono text-[10px] font-semibold">
                                                    {{ $duration }}
                                                </span>
                                                <span class="absolute top-3 left-3 px-2.5 py-0.5 rounded-full bg-emerald-500/90 text-white font-mono text-[10px] font-bold uppercase tracking-wider flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                                    <span>Consented Story</span>
                                                </span>
                                            </div>

                                            <div class="p-6 space-y-3">
                                                <div>
                                                    <h3 class="font-heading text-base font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors">
                                                        {{ $student }}
                                                    </h3>
                                                    <p class="text-xs font-mono font-semibold text-slate-700 mt-0.5">
                                                        {{ $role }} &bull; <span class="text-[#FB6221]">{{ $company }}</span>
                                                    </p>
                                                </div>

                                                <p class="text-xs text-slate-600 font-sans italic leading-relaxed line-clamp-3">
                                                    &ldquo;{{ $quote }}&rdquo;
                                                </p>
                                            </div>
                                        </div>

                                        <div class="px-6 pb-6 pt-3 border-t border-slate-200/80 flex items-center justify-between">
                                            @if($package)
                                                <span class="px-2 py-0.5 rounded-md bg-orange-50 text-[#FB6221] border border-orange-200 font-mono text-[11px] font-bold">
                                                    {{ $package }}
                                                </span>
                                            @else
                                                <span class="text-[11px] font-mono text-slate-400">Verified Placement</span>
                                            @endif

                                            <button
                                                type="button"
                                                @click="openVideo('{{ $vUrl }}', '{{ addslashes($vid->title) }}', '{{ addslashes($student) }}', '{{ addslashes($role . ' @ ' . $company) }}')"
                                                class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-[#FB6221] hover:text-slate-900 transition-colors cursor-pointer"
                                            >
                                                <span>Watch Video Story</span>
                                                <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 11. FAQS ACCORDION SECTION --}}
            @if ($sectionKey === 'faqs')
                <section class="w-full py-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto" id="faqs">
                    <div class="text-center max-w-3xl mx-auto mb-12">
                        <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">GOT QUESTIONS?</span>
                        <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                            Frequently Asked Questions
                        </h2>
                        <p class="text-slate-600 text-sm mt-1">
                            Everything you need to know about the curriculum, prerequisites, server access, and placement support.
                        </p>
                    </div>

                    @php
                        $faqsList = $course->activeFaqs->isNotEmpty() ? $course->activeFaqs : collect([
                            (object) [
                                'question' => 'How does the live online training format work?',
                                'answer' => 'Classes are conducted via live interactive sessions led by working industry consultants. You can share your screen, ask questions in real time, and all sessions are recorded for lifetime revision.',
                            ],
                            (object) [
                                'question' => 'Do I get dedicated remote system server access?',
                                'answer' => 'Yes, every enrolled student receives individual remote login access to our 24/7 dedicated training servers for hands-on configuration and end-to-end business process execution.',
                            ],
                            (object) [
                                'question' => 'What is the placement assistance process?',
                                'answer' => 'Our dedicated placement team conducts mock technical interviews, resume refining sessions, and refers your profile directly to hiring partners across our PlacementBaba consulting network.',
                            ],
                            (object) [
                                'question' => 'Can I attend a free demo class before enrolling?',
                                'answer' => 'Yes! We encourage all prospective students to attend an interactive free demo class to experience our teaching methodology and interact directly with our mentors before committing.',
                            ],
                        ]);
                    @endphp

                    <div x-data="{ openFaq: 0 }" class="max-w-3xl mx-auto space-y-4">
                        @foreach ($faqsList as $index => $faq)
                            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                                <button
                                    type="button"
                                    @click="openFaq = (openFaq === {{ $index }} ? -1 : {{ $index }})"
                                    class="w-full px-6 py-5 flex items-center justify-between text-left hover:bg-slate-50 transition-colors"
                                >
                                    <span class="font-heading text-base font-bold text-slate-900 pr-4">
                                        {{ $faq->question }}
                                    </span>
                                    <span class="material-symbols-outlined text-slate-400 text-[20px] shrink-0 transition-transform duration-200" :class="openFaq === {{ $index }} ? 'rotate-180' : ''">
                                        expand_more
                                    </span>
                                </button>
                                <div x-show="openFaq === {{ $index }}" x-collapse class="px-6 pb-5 pt-1 text-slate-600 text-sm font-sans leading-relaxed border-t border-slate-100">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- 13. AWARDS & RECOGNITIONS SECTION --}}
            @if ($sectionKey === 'awards' && ($trustConfig['sections']['awards']['enabled'] ?? true))
                @php
                    $awards = collect($trustRecords['awards'] ?? []);
                @endphp
                @if ($awards->isNotEmpty())
                    <section class="w-full bg-slate-100/80 py-16 px-4 sm:px-6 lg:px-12 border-b border-slate-200" id="awards">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[#FB6221] font-mono text-[11px] font-bold uppercase tracking-wider mb-2 shadow-2xs">
                                    <span class="material-symbols-outlined text-[14px]">emoji_events</span>
                                    <span>EXCELLENCE &bull; VERIFIED RECOGNITIONS</span>
                                </div>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1">
                                    Awards &amp; Institutional Recognition
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base mt-2">
                                    Documented industry accolades honoring our training quality, server infrastructure, and corporate placement ecosystem.
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach ($awards as $award)
                                    @php
                                        $aMeta = $award->metadata ?? [];
                                        $org = $aMeta['conferred_by'] ?? ($aMeta['issuing_organization'] ?? ($award->subtitle ?: 'National Education Forum'));
                                        $year = $aMeta['year'] ?? '2026';
                                        $sourceUrl = $aMeta['official_url'] ?? ($award->media_url ?? route('about'));
                                    @endphp
                                    <div class="p-7 rounded-3xl bg-white border border-slate-200 shadow-xs hover:shadow-lg transition-all flex flex-col justify-between group">
                                        <div class="space-y-4">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                                    <span class="material-symbols-outlined text-[26px]">emoji_events</span>
                                                </div>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="material-symbols-outlined text-[12px]">verified</span>
                                                    <span>Verified</span>
                                                </span>
                                            </div>

                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-mono text-[11px] font-bold text-[#FB6221] uppercase tracking-wider">{{ $org }}</span>
                                                    <span class="text-slate-300">•</span>
                                                    <span class="font-mono text-[11px] font-semibold text-slate-500">{{ $year }}</span>
                                                </div>
                                                <h3 class="font-heading text-base font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors leading-snug">
                                                    {{ $award->title }}
                                                </h3>
                                            </div>

                                            @if ($award->description)
                                                <p class="text-xs text-slate-600 leading-relaxed font-sans line-clamp-3">
                                                    {{ $award->description }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="pt-4 border-t border-slate-100 mt-5 flex items-center justify-between text-xs">
                                            <span class="font-mono text-slate-400 text-[11px]">Academic Honor</span>
                                            <a href="{{ $sourceUrl }}" class="font-mono text-xs font-bold text-slate-700 hover:text-[#FB6221] flex items-center gap-1 transition-colors">
                                                <span>Source Details</span>
                                                <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 12. RELATED PROGRAMS SECTION --}}
            @if ($sectionKey === 'related' && !empty($relatedCourses) && $relatedCourses->isNotEmpty())
                <section class="w-full bg-slate-100/60 py-16 px-4 sm:px-6 lg:px-12 border-t border-slate-200">
                    <div class="max-w-[1440px] mx-auto">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">CAREER PATHWAYS</span>
                                <h2 class="font-heading text-2xl sm:text-3xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1">
                                    Explore Related Programs
                                </h2>
                            </div>
                            <a href="{{ route('courses.index') }}" class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221] hover:underline flex items-center gap-1">
                                <span>View All Courses</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($relatedCourses as $relCourse)
                                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between hover:border-[#FB6221]/50 transition-colors">
                                    <div>
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold uppercase bg-orange-50 text-[#FB6221] border border-orange-200">
                                                {{ $relCourse->category_badge }}
                                            </span>
                                            <span class="text-xs font-mono text-slate-500">{{ $relCourse->display_duration }}</span>
                                        </div>
                                        <h3 class="font-heading text-base font-bold text-slate-900 mb-2">
                                            <a href="{{ $relCourse->url }}" class="hover:text-[#FB6221] transition-colors">
                                                {{ $relCourse->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-slate-500 font-sans line-clamp-2 mb-4">
                                            {{ $relCourse->short_description }}
                                        </p>
                                    </div>
                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                        <span class="font-heading text-sm font-bold text-slate-900">
                                            {{ $relCourse->display_price }}
                                        </span>
                                        <a href="{{ $relCourse->url }}" class="font-mono text-xs font-bold uppercase text-[#FB6221] hover:underline flex items-center gap-1">
                                            <span>Explore</span>
                                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            {{-- 13. FINAL CTA BANNER --}}
            @if ($sectionKey === 'cta')
                <section class="w-full bg-[#0A0A0A] text-white py-16 px-4 sm:px-6 lg:px-12 border-t border-slate-800">
                    <div class="max-w-[1440px] mx-auto text-center">
                        <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">ADMISSIONS OPEN</span>
                        <h2 class="font-heading text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mt-3 max-w-3xl mx-auto">
                            Transform Your Career with Hands-On Enterprise Skills
                        </h2>
                        <p class="text-slate-400 text-sm sm:text-base mt-4 max-w-xl mx-auto">
                            Schedule a free 1-on-1 counseling session with our senior advisors to plan your curriculum roadmap.
                        </p>
                        <div class="flex flex-wrap items-center justify-center gap-4 mt-8">
                            <a
                                href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                class="px-8 py-4 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-sm font-bold uppercase tracking-wider transition-all shadow-lg flex items-center gap-2"
                            >
                                <span>Book Free Demo Class</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </a>
                            <a
                                href="tel:+918506888288"
                                class="px-8 py-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-['Inter'] text-sm font-semibold uppercase tracking-wider transition-all flex items-center gap-2"
                            >
                                <span class="material-symbols-outlined text-[18px] text-[#FB6221]">call</span>
                                <span>Call Admissions (+91 8506 888 288)</span>
                            </a>
                        </div>
                    </div>
                </section>
            @endif

        @endif
    @endforeach

    {{-- VIDEO LIGHTBOX MODAL --}}
    <div
        x-show="videoModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop Scrim --}}
        <div
            x-show="videoModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/85 transition-opacity"
            @click="videoModalOpen = false; activeVideoUrl = ''"
            aria-hidden="true"
        ></div>

        {{-- Centering Container --}}
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4 text-center sm:p-6 pointer-events-none">
            {{-- Modal Dialog Panel --}}
            <div
                x-show="videoModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-20 w-full max-w-4xl transform overflow-hidden rounded-3xl bg-slate-950 text-left shadow-2xl transition-all border border-slate-800 pointer-events-auto"
                @click.stop
            >
                <div class="p-4 sm:p-5 flex items-center justify-between border-b border-slate-800 text-white">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                            <span class="text-xs font-mono font-bold uppercase text-[#FB6221] tracking-wider" x-text="activeVideoStudent"></span>
                            <span class="text-slate-500 text-xs">•</span>
                            <span class="text-xs text-slate-400 font-sans truncate" x-text="activeVideoRole"></span>
                        </div>
                        <h3 class="font-heading text-base font-bold text-white mt-1 line-clamp-1" x-text="activeVideoTitle"></h3>
                    </div>
                    <button
                        type="button"
                        @click="videoModalOpen = false; activeVideoUrl = ''"
                        class="p-2 rounded-full bg-slate-800 text-slate-400 hover:text-white hover:bg-slate-700 transition-colors cursor-pointer"
                        aria-label="Close Video"
                    >
                        <span class="material-symbols-outlined text-[20px] block">close</span>
                    </button>
                </div>

                <div class="relative aspect-video w-full bg-black">
                    <template x-if="videoModalOpen && activeVideoUrl">
                        <iframe
                            :src="activeVideoUrl.includes('youtube.com') || activeVideoUrl.includes('youtu.be')
                                ? 'https://www.youtube-nocookie.com/embed/' + (activeVideoUrl.includes('v=') ? activeVideoUrl.split('v=')[1].split('&')[0] : activeVideoUrl.split('/').pop()) + '?autoplay=1&rel=0'
                                : activeVideoUrl"
                            class="w-full h-full border-0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </template>
                </div>

                <div class="p-4 bg-slate-900/90 text-xs text-slate-400 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800">
                    <span class="font-mono text-[11px] text-emerald-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">verified</span>
                        <span>Genuine, Consented Alumni Success Story &bull; ZebLearn LMS</span>
                    </span>
                    <a
                        href="{{ route('book-demo', ['course' => $course->slug]) }}"
                        class="text-[#FB6221] hover:underline font-mono font-bold flex items-center gap-1"
                    >
                        <span>Start Your Learning Journey</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- CERTIFICATE PREVIEW LIGHTBOX MODAL --}}
    <div
        x-show="certModalOpen"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        {{-- Backdrop Scrim --}}
        <div
            x-show="certModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-950/85 transition-opacity"
            @click="certModalOpen = false"
            aria-hidden="true"
        ></div>

        {{-- Centering Container --}}
        <div class="relative z-10 flex min-h-screen items-center justify-center p-4 text-center sm:p-6 pointer-events-none">
            {{-- Modal Dialog Panel --}}
            <div
                x-show="certModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative z-20 w-full max-w-3xl transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all border border-slate-200 pointer-events-auto"
                @click.stop
            >
                <div class="p-4 sm:p-5 flex items-center justify-between border-b border-slate-100">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase text-[#FB6221] tracking-wider" x-text="activeCertIssuer"></span>
                        <h3 class="font-heading text-lg font-bold text-slate-900 mt-0.5 line-clamp-1" x-text="activeCertTitle"></h3>
                    </div>
                    <button
                        type="button"
                        @click="certModalOpen = false"
                        class="p-2 rounded-full bg-slate-100 text-slate-500 hover:text-slate-900 hover:bg-slate-200 transition-colors cursor-pointer"
                        aria-label="Close Certificate Preview"
                    >
                        <span class="material-symbols-outlined text-[20px] block">close</span>
                    </button>
                </div>

                <div class="p-4 sm:p-6 bg-slate-50 flex items-center justify-center">
                    <img
                        :src="activeCertImg"
                        :alt="activeCertTitle"
                        class="max-h-[65vh] w-auto max-w-full rounded-2xl shadow-xl border border-slate-200 object-contain"
                    />
                </div>

                <div class="p-5 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-slate-100 text-xs">
                    <div>
                        <p class="font-mono text-slate-700 font-bold" x-text="'Sample Credential ID: ' + activeCertId"></p>
                        <p class="text-slate-500 font-sans mt-0.5" x-text="activeCertDesc"></p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <a
                            href="{{ route('certificates.verify') }}"
                            class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-mono text-xs font-bold uppercase tracking-wider transition-all shadow-xs flex items-center gap-1.5"
                        >
                            <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                            <span>Verify Portal</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
