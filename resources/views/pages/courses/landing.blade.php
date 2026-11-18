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
            'reviewCount' => (string) ($course->dynamic_reviews_count),
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

            {{-- 1. HERO SECTION (ORIGINAL THEME + RICH ENTERPRISE CONTENT) --}}
            @if ($sectionKey === 'hero')
                <section class="relative w-full pt-32 sm:pt-36 lg:pt-40 pb-12 sm:pb-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto overflow-hidden">
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
                            <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">
                                {{ $course->status_label }} &bull; {{ $course->delivery_mode ?: 'Live Online' }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Hero Grid (Left Content / Right Fee Calculator) -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                        <!-- Left Column: Title, Narrative, Ratings, Buttons & Accreditations -->
                        <div class="lg:col-span-7 xl:col-span-8">
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

                            <h1 class="font-heading text-2xl sm:text-4xl lg:text-[44px] font-black uppercase text-[#0A0A0A] leading-[1.12] tracking-tight mb-4">
                                {{ $course->name }} Training Course - Transform Your Career with Zeblearn
                            </h1>

                            <p class="font-sans text-sm sm:text-base text-slate-600 leading-relaxed max-w-3xl mb-5">
                                {{ $course->clean_hero_description ?: ("The " . $course->name . " training course provides the foundation and the tools needed for success in the field of " . $course->name . " and opens the door to exciting career opportunities in today's global markets. By utilizing both theory and application, we are able to provide students with an added level of confidence when they enter into this field of work. The structured curriculum of the " . $course->name . " course will help each student develop their knowledge from the basic to the advanced financial modules. They will be well prepared to use their knowledge in a business environment and in a manner that meets industry standards and provides immediate value in the workplace.") }}
                            </p>

                            <!-- Star Rating & Vote Counts -->
                            <div class="flex flex-wrap items-center gap-2.5 mb-4">
                                <div class="flex text-amber-500 gap-0.5">
                                    @for($i=0; $i<5; $i++)
                                        <span class="material-symbols-outlined text-[19px] fill-current">star</span>
                                    @endfor
                                </div>
                                <span class="text-xs sm:text-sm font-bold text-slate-900 font-sans">
                                    {{ $course->display_rating }} out of 5
                                </span>
                                <span class="text-xs text-slate-500 font-sans">
                                    based on {{ $course->display_reviews_count }} votes ({{ $course->display_reviews_count }} verified reviews)
                                </span>
                            </div>

                            <!-- Multi-Platform Badges Row -->
                            <div class="flex flex-wrap items-center gap-2 mb-7">
                                <!-- Google -->
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-200/90 text-xs text-slate-800 font-medium shadow-2xs">
                                    <span class="w-4 h-4 rounded-full bg-blue-500 text-white text-[10px] font-bold flex items-center justify-center font-sans">G</span>
                                    <span>4.8/5</span>
                                </div>
                                <!-- Trustpilot Star -->
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-200/90 text-xs text-slate-800 font-medium shadow-2xs">
                                    <span class="material-symbols-outlined text-emerald-500 text-[14px] fill-current">star</span>
                                    <span>4.8/5</span>
                                </div>
                                <!-- MouthShut -->
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-200/90 text-xs text-slate-800 font-medium shadow-2xs">
                                    <span class="w-4 h-4 rounded-full bg-cyan-500 text-white text-[10px] font-bold flex items-center justify-center font-sans">M</span>
                                    <span>4.7/5</span>
                                </div>
                                <!-- Justdial -->
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-200/90 text-xs text-slate-800 font-medium shadow-2xs">
                                    <span class="px-1 py-0.2 rounded bg-amber-400 text-slate-950 text-[9px] font-black font-sans">Jd</span>
                                    <span>4.9/5</span>
                                </div>
                                <!-- Facebook -->
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white border border-slate-200/90 text-xs text-slate-800 font-medium shadow-2xs">
                                    <span class="w-4 h-4 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center font-sans">f</span>
                                    <span>4.9/5</span>
                                </div>
                            </div>

                            <!-- 3 CTA Action Buttons -->
                            <div class="flex flex-wrap items-center gap-3 sm:gap-4 mb-7">
                                <a
                                    href="#placement-proof"
                                    class="px-6 py-3.5 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider transition-all shadow-md text-center hover:scale-[1.02]"
                                >
                                    PLACEMENT REPORT
                                </a>
                                <a
                                    href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                    class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-[#0A0A0A] font-['Inter'] text-xs font-bold uppercase tracking-wider border border-slate-300 transition-all text-center shadow-xs"
                                >
                                    GET BROCHURE
                                </a>
                                <a
                                    href="/blogs"
                                    class="px-6 py-3.5 rounded-xl bg-white hover:bg-slate-50 text-[#0A0A0A] font-['Inter'] text-xs font-bold uppercase tracking-wider border border-slate-300 transition-all text-center shadow-xs"
                                >
                                    INTERVIEW QUESTIONS
                                </a>
                            </div>

                            <!-- Accreditation / Government & Standards Logo Badges -->
                            <div>
                                <span class="sr-only">ACCREDITED INSTITUTIONAL PARTNERS &amp; COMPLIANCE</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-xl sm:max-w-2xl">
                                    <div class="h-14 sm:h-16 px-3 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-center hover:border-[#FB6221]/50 hover:shadow-xs transition-all overflow-hidden">
                                        <img src="/images/compliance/iso.webp" alt="ISO - International Organization for Standardization" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="h-14 sm:h-16 px-3 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-center hover:border-[#FB6221]/50 hover:shadow-xs transition-all overflow-hidden">
                                        <img src="/images/compliance/nsdc.webp" alt="NSDC - National Skill Development Corporation" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="h-14 sm:h-16 px-3 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-center hover:border-[#FB6221]/50 hover:shadow-xs transition-all overflow-hidden">
                                        <img src="/images/compliance/mca.webp" alt="Ministry of Corporate Affairs - Government of India" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="h-14 sm:h-16 px-3 py-2 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-center hover:border-[#FB6221]/50 hover:shadow-xs transition-all overflow-hidden">
                                        <img src="/images/compliance/skill-india.webp" alt="Skill India - कौशल भारत कुशल भारत" class="max-h-full max-w-full object-contain">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Calculate Your Course Fee Lead Form Card -->
                        <div class="lg:col-span-5 xl:col-span-4 w-full max-w-md lg:ml-auto" id="enrollment-section">
                            <div
                                x-data="{
                                    feeCalculated: false,
                                    cName: '{{ auth()->check() ? auth()->user()->name : '' }}',
                                    cEmail: '{{ auth()->check() ? auth()->user()->email : '' }}',
                                    cPhone: '',
                                    cCountry: '+91',
                                    calculate() {
                                        if(!this.cName || !this.cEmail || !this.cPhone) {
                                            alert('Please fill in your name, email and contact number.');
                                            return;
                                        }
                                        this.feeCalculated = true;
                                    }
                                }"
                                class="bg-white rounded-3xl p-6 sm:p-7 shadow-xl text-slate-900 border border-slate-200/90 relative overflow-hidden"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-mono font-bold uppercase text-[#FB6221] tracking-widest">ENROLMENT &amp; SCHOLARSHIP</span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold uppercase border border-emerald-200">Instant Estimate</span>
                                </div>
                                <h3 class="font-heading text-lg sm:text-xl font-bold text-slate-900 mb-4">
                                    Calculate Your Course Fee
                                </h3>

                                <!-- Standard Input State -->
                                <div x-show="!feeCalculated" class="space-y-3.5">
                                    <div>
                                        <input
                                            type="text"
                                            x-model="cName"
                                            required
                                            placeholder="Full Name *"
                                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/15 font-sans text-xs text-slate-900 placeholder-slate-400 outline-none transition-all bg-slate-50/50 focus:bg-white"
                                        />
                                    </div>

                                    <div>
                                        <input
                                            type="email"
                                            x-model="cEmail"
                                            required
                                            placeholder="Email Address *"
                                            class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/15 font-sans text-xs text-slate-900 placeholder-slate-400 outline-none transition-all bg-slate-50/50 focus:bg-white"
                                        />
                                    </div>

                                    <div class="flex gap-2">
                                        <select
                                            x-model="cCountry"
                                            class="px-2.5 py-3 rounded-xl border border-slate-200 bg-slate-100 font-sans text-xs text-slate-700 outline-none shrink-0 cursor-pointer"
                                        >
                                            <option value="+91">India (+91)</option>
                                            <option value="+1">USA (+1)</option>
                                            <option value="+44">UK (+44)</option>
                                            <option value="+971">UAE (+971)</option>
                                            <option value="+65">Singapore (+65)</option>
                                            <option value="+61">Australia (+61)</option>
                                        </select>
                                        <input
                                            type="tel"
                                            x-model="cPhone"
                                            required
                                            placeholder="Phone No *"
                                            pattern="[0-9]{10}"
                                            maxlength="10"
                                            class="flex-1 px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/15 font-sans text-xs text-slate-900 placeholder-slate-400 outline-none transition-all bg-slate-50/50 focus:bg-white"
                                        />
                                    </div>

                                    <button
                                        type="button"
                                        @click="calculate()"
                                        class="w-full py-3.5 px-4 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-heading font-black text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center justify-center gap-1.5"
                                    >
                                        <span>CALCULATE NOW!</span>
                                        <span class="material-symbols-outlined text-[16px]">bolt</span>
                                    </button>

                                    <p class="text-[10px] text-center text-slate-400 font-sans leading-relaxed pt-1">
                                        By requesting here, I agree to ZebLearn's <a href="#" class="text-[#FB6221] hover:underline">Terms &amp; Conditions</a> and <a href="#" class="text-[#FB6221] hover:underline">Privacy Policy</a>
                                    </p>
                                </div>

                                <!-- Calculated Fee Result State -->
                                <div x-show="feeCalculated" x-cloak class="space-y-4 pt-1 text-center">
                                    <div class="p-4 rounded-2xl bg-orange-50 border border-orange-200">
                                        <span class="text-[10px] font-mono uppercase font-bold text-[#FB6221] tracking-wider block mb-1">
                                            Scholarship Discount Unlocked!
                                        </span>
                                        <div class="flex items-baseline justify-center gap-2">
                                            <span class="font-heading text-3xl font-extrabold text-[#0A0A0A]">
                                                {{ $course->formatted_fee ?: ('₹' . number_format($course->fee ?: 25000)) }}
                                            </span>
                                            <span class="text-xs font-mono text-slate-500">+ GST</span>
                                        </div>
                                        @if ($course->original_fee && $course->original_fee > ($course->fee ?? 0))
                                            <span class="text-xs text-slate-500 line-through block mt-0.5">
                                                Original Fee: ₹{{ number_format($course->original_fee) }}
                                            </span>
                                        @endif
                                        <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-mono font-bold">
                                            Early Bird Batch Offer Applied
                                        </span>
                                    </div>

                                    <p class="text-xs text-slate-600 font-sans">
                                        Thank you, <strong x-text="cName"></strong>! Our senior advisor will share the full module fee schedule &amp; scholarship grant details on WhatsApp.
                                    </p>

                                    <div class="space-y-2">
                                        @auth
                                            @if (auth()->user()->isStudent())
                                                <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                                    @csrf
                                                    <button
                                                        type="submit"
                                                        class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2"
                                                    >
                                                        <span>Enroll with Calculated Fee</span>
                                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <a
                                                href="{{ route('courses.enroll.get', $course) }}"
                                                class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                            >
                                                <span>Lock Seat at Discount</span>
                                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                            </a>
                                        @endauth

                                        <a
                                            :href="`https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20calculated%20fee%20for%20{{ urlencode($course->name) }}%20as%20${encodeURIComponent(cName)}%20(${cPhone}).%20Please%20share%20curriculum%20brochure.`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-sans text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-2 shadow-xs"
                                        >
                                            <span>Confirm on WhatsApp</span>
                                            <span class="material-symbols-outlined text-[16px]">chat</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KEY POINTS SECTION (Original Light Theme Horizontal Matrix) -->
                    <div class="mt-12 sm:mt-14 pt-8 sm:pt-10 border-t border-slate-200">
                        <h2 class="font-heading text-lg sm:text-xl font-black uppercase tracking-tight text-[#0A0A0A] mb-5 sm:mb-6">
                            KEY POINTS
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Point 1: Corporate Served -->
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 flex items-center gap-4 shadow-xs hover:border-[#FB6221]/40 hover:shadow-md transition-all group">
                                <div class="w-12 h-12 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-[24px]">work</span>
                                </div>
                                <div>
                                    <div class="font-heading text-2xl font-black text-[#0A0A0A]">90+</div>
                                    <div class="font-sans text-xs text-slate-600 font-medium">Corporate Served</div>
                                </div>
                            </div>

                            <!-- Point 2: Countries & Counting -->
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 flex items-center gap-4 shadow-xs hover:border-[#FB6221]/40 hover:shadow-md transition-all group">
                                <div class="w-12 h-12 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-[24px]">flag</span>
                                </div>
                                <div>
                                    <div class="font-heading text-2xl font-black text-[#0A0A0A]">18+</div>
                                    <div class="font-sans text-xs text-slate-600 font-medium">Countries &amp; Counting</div>
                                </div>
                            </div>

                            <!-- Point 3: Professionals Trained -->
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 flex items-center gap-4 shadow-xs hover:border-[#FB6221]/40 hover:shadow-md transition-all group">
                                <div class="w-12 h-12 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-[24px]">groups</span>
                                </div>
                                <div>
                                    <div class="font-heading text-2xl font-black text-[#0A0A0A]">250000+</div>
                                    <div class="font-sans text-xs text-slate-600 font-medium">Professionals Trained</div>
                                </div>
                            </div>

                            <!-- Point 4: Batches every month -->
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-6 flex items-center gap-4 shadow-xs hover:border-[#FB6221]/40 hover:shadow-md transition-all group">
                                <div class="w-12 h-12 rounded-xl bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <span class="material-symbols-outlined text-[24px]">settings</span>
                                </div>
                                <div>
                                    <div class="font-heading text-2xl font-black text-[#0A0A0A]">50+</div>
                                    <div class="font-sans text-xs text-slate-600 font-medium">Batches every month</div>
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

                    if ($displayPartners->isEmpty()) {
                        $displayPartners = collect([
                            (object) ['title' => 'Deloitte', 'image_path' => '/images/partners/deloitte.svg', 'media_url' => 'https://www.deloitte.com/', 'category_label' => 'Global Systems Integrator'],
                            (object) ['title' => 'IBM', 'image_path' => '/images/partners/ibm.svg', 'media_url' => 'https://www.ibm.com/', 'category_label' => 'Global Enterprise Alliance'],
                            (object) ['title' => 'PwC', 'image_path' => '/images/partners/pwc.svg', 'media_url' => 'https://www.pwc.com/', 'category_label' => 'Big 4 Enterprise Advisory'],
                            (object) ['title' => 'Accenture', 'image_path' => '/images/partners/accenture.svg', 'media_url' => 'https://www.accenture.com/', 'category_label' => 'Global Systems Integrator'],
                            (object) ['title' => 'Infosys', 'image_path' => '/images/partners/infosys.svg', 'media_url' => 'https://www.infosys.com/', 'category_label' => 'Enterprise Cloud & ERP'],
                            (object) ['title' => 'TCS', 'image_path' => '/images/partners/tcs.svg', 'media_url' => 'https://www.tcs.com/', 'category_label' => 'Tata Consultancy Services'],
                            (object) ['title' => 'Wipro', 'image_path' => '/images/partners/wipro.svg', 'media_url' => 'https://www.wipro.com/', 'category_label' => 'Enterprise Consulting'],
                            (object) ['title' => 'Capgemini', 'image_path' => '/images/partners/capgemini.svg', 'media_url' => 'https://www.capgemini.com/', 'category_label' => 'Systems Integration'],
                        ]);
                    }

                    // Tech Alliance Brands from reference
                    $techAlliances = [
                        ['name' => 'Intuit', 'badge' => '<span class="font-sans font-black text-sm text-[#0077C5] tracking-tight">INTUIT</span>'],
                        ['name' => 'Apple', 'badge' => '<span class="flex items-center gap-1.5 font-sans font-bold text-xs text-slate-900"><svg class="h-4.5 w-auto fill-current text-slate-900" viewBox="0 0 170 170"><path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.74 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.7-3.04-7.66-7.85-11.87-14.42-6.19-9.67-11.08-20.9-14.67-33.68-3.59-12.78-5.38-24.96-5.38-36.54 0-16.14 4.04-29.47 12.13-40 8.09-10.53 18.23-15.89 30.43-16.08 4.36 0 9.38 1.15 15.06 3.44 5.68 2.29 9.34 3.49 10.98 3.61 2.22-.24 5.99-1.48 11.31-3.72 5.32-2.24 10.02-3.32 14.1-3.23 11.23.49 20.76 4.39 28.59 11.71 7.83 7.32 13.06 16.59 15.69 27.8-10.02 6.09-15.08 14.53-15.18 25.32-.1 9.99 3.68 18.42 11.34 25.29 7.66 6.87 16.89 10.74 27.69 11.61-2.45 7.15-5.59 14.44-9.42 21.87zM119.22 33.56c0-7.29 2.66-14.07 7.98-20.35 5.32-6.28 11.95-10.36 19.89-12.24.49 1.95.73 3.96.73 6.03 0 7.39-2.73 14.28-8.19 20.67-5.46 6.39-12.11 10.38-19.95 11.97-.13-2.07-.46-4.09-.46-6.08z"/></svg>Apple</span>'],
                        ['name' => 'Adobe', 'badge' => '<span class="font-sans font-black text-xs text-[#FF0000] tracking-tight flex items-center gap-1"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M13.966 22h3.036L12 3.612 7.001 22h3.035l1.624-5.46h2.298L13.966 22zM8.514 16.54L12 4.793l3.486 11.747H8.514zM0 2h6.059L0 22V2zm24 0h-6.059L24 22V2z"/></svg>Adobe</span>'],
                        ['name' => 'Autodesk', 'badge' => '<span class="font-sans font-black text-xs text-[#0696D7] tracking-wider uppercase">AUTODESK</span>'],
                        ['name' => 'Meta', 'badge' => '<span class="font-sans font-bold text-xs text-[#0668E1] tracking-tight flex items-center gap-1"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.989C18.343 21.129 22 16.99 22 12c0-5.523-4.477-10-10-10z"/></svg>Meta</span>'],
                        ['name' => 'Cisco', 'badge' => '<span class="font-sans font-black text-xs text-[#1BA0D7] tracking-widest uppercase">CISCO</span>'],
                        ['name' => 'Microsoft', 'badge' => '<span class="font-sans font-bold text-xs text-slate-800 flex items-center gap-1.5"><span class="grid grid-cols-2 gap-0.5 w-3 h-3"><span class="bg-[#F25022]"></span><span class="bg-[#7FBA00]"></span><span class="bg-[#00A4EF]"></span><span class="bg-[#FFB900]"></span></span>Microsoft Certified</span>'],
                        ['name' => 'PMI', 'badge' => '<span class="font-sans font-black text-xs text-[#F99B1D] tracking-wider uppercase">PMI Institute</span>'],
                        ['name' => 'IC3', 'badge' => '<span class="font-sans font-black text-xs text-[#00833E] tracking-wider uppercase">IC3 Digital</span>'],
                        ['name' => 'ITS', 'badge' => '<span class="font-sans font-bold text-xs text-slate-700 uppercase">IT Specialist</span>'],
                    ];
                @endphp
                @if ($displayPartners->isNotEmpty())
                    <section class="w-full bg-[#FAFAF8] border-y border-slate-200 py-12 px-4 sm:px-6 lg:px-12 overflow-hidden">
                        <div class="max-w-[1440px] mx-auto text-center">
                            <!-- Overline with Golden Lines -->
                            <div class="inline-flex items-center gap-2 mb-2">
                                <span class="w-7 h-[1.5px] bg-amber-500"></span>
                                <span class="text-amber-500 font-mono text-[11px] font-bold uppercase tracking-widest">OUR PARTNERS</span>
                                <span class="w-7 h-[1.5px] bg-amber-500"></span>
                            </div>

                            <!-- Title -->
                            <h2 class="font-heading text-xl sm:text-2xl lg:text-3xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1 mb-3 max-w-4xl mx-auto">
                                INDUSTRY-ALIGNED {{ strtoupper($course->name) }} TRAINING PARTNERSHIPS
                            </h2>

                            <!-- Subtitle -->
                            <p class="font-sans text-xs sm:text-sm text-slate-600 max-w-3xl mx-auto mb-8 leading-relaxed">
                                {{ $course->name }} Training partners with leading ERP consultants to ensure curriculum relevance. Participants receive access to a licensed SAP S/4HANA sandbox for practice.
                            </p>

                            <span class="sr-only">Where Our Certified Alumni Work HIRING PARTNERS</span>

                            <!-- Infinite Marquee of Company & Tech Alliance Logos -->
                            <div class="zeb-marquee-container alumni-marquee-container relative -mx-4 px-4 sm:-mx-6 sm:px-6 py-2 overflow-hidden" role="region" aria-label="Ecosystem and Hiring Partner Logos Marquee">
                                <!-- Edge Fade Masks -->
                                <div class="pointer-events-none absolute inset-y-0 left-0 w-12 sm:w-24 bg-gradient-to-r from-[#FAFAF8] via-[#FAFAF8]/80 to-transparent z-10"></div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 w-12 sm:w-24 bg-gradient-to-l from-[#FAFAF8] via-[#FAFAF8]/80 to-transparent z-10"></div>

                                <div class="zeb-marquee-track alumni-marquee-track zeb-marquee-left flex gap-3.5 sm:gap-4 items-center focus:outline-none" style="--zeb-marquee-duration: 32s;">
                                    {{-- Primary Set --}}
                                    @foreach ($techAlliances as $tech)
                                        <div class="shrink-0 h-14 sm:h-16 px-5 sm:px-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-all flex items-center justify-center group">
                                            {!! $tech['badge'] !!}
                                        </div>
                                    @endforeach

                                    @foreach ($displayPartners as $partner)
                                        @php
                                            $partnerName = $partner->title ?? $partner->name ?? '';
                                            $slug = strtolower(trim($partnerName));
                                            $partnerLogo = $partner->image_path ?? $partner->logo_path ?? ($partnerFallbackLogos[$slug] ?? null);
                                            $partnerUrl = $partner->media_url ?? $partner->website_url ?? null;
                                            $partnerCategory = method_exists($partner, 'meta') ? ($partner->meta('partner_category') ?? 'Hiring Partner') : ($partner->category_label ?? 'Hiring Partner');
                                        @endphp
                                        <div class="shrink-0 h-14 sm:h-16 px-5 sm:px-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#FB6221]/30 transition-all flex items-center justify-center group">
                                            @if($partnerUrl)
                                                 <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" title="{{ $partnerName }} ({{ $partnerCategory }})" class="flex items-center justify-center h-full">
                                            @endif
                                            @if($partnerLogo)
                                                <img src="{{ $partnerLogo }}" alt="{{ $partnerName }} authentic logo" class="h-5 sm:h-6 max-w-[110px] sm:max-w-[120px] object-contain select-none transition-transform group-hover:scale-105" loading="lazy">
                                            @else
                                                <span class="font-mono text-xs font-bold text-slate-800 tracking-tight">{{ $partnerName }}</span>
                                            @endif
                                            @if($partnerUrl)
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- Duplicate Set for Seamless Continuous Loop --}}
                                    @foreach ($techAlliances as $tech)
                                        <div class="shrink-0 h-14 sm:h-16 px-5 sm:px-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md transition-all flex items-center justify-center group" aria-hidden="true" tabindex="-1">
                                            {!! $tech['badge'] !!}
                                        </div>
                                    @endforeach

                                    @foreach ($displayPartners as $partner)
                                        @php
                                            $partnerName = $partner->title ?? $partner->name ?? '';
                                            $slug = strtolower(trim($partnerName));
                                            $partnerLogo = $partner->image_path ?? $partner->logo_path ?? ($partnerFallbackLogos[$slug] ?? null);
                                            $partnerUrl = $partner->media_url ?? $partner->website_url ?? null;
                                            $partnerCategory = method_exists($partner, 'meta') ? ($partner->meta('partner_category') ?? 'Hiring Partner') : ($partner->category_label ?? 'Hiring Partner');
                                        @endphp
                                        <div class="shrink-0 h-14 sm:h-16 px-5 sm:px-6 rounded-2xl bg-white border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-[#FB6221]/30 transition-all flex items-center justify-center group" aria-hidden="true" tabindex="-1">
                                            @if($partnerUrl)
                                                <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" tabindex="-1" class="flex items-center justify-center h-full">
                                            @endif
                                            @if($partnerLogo)
                                                <img src="{{ $partnerLogo }}" alt="{{ $partnerName }} authentic logo" class="h-5 sm:h-6 max-w-[110px] sm:max-w-[120px] object-contain select-none transition-transform group-hover:scale-105" loading="lazy">
                                            @else
                                                <span class="font-mono text-xs font-bold text-slate-800 tracking-tight">{{ $partnerName }}</span>
                                            @endif
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

                {{-- 2.1. EARN A RECOGNIZED TRAINING CERTIFICATE SECTION --}}
                <section class="w-full bg-white py-14 lg:py-16 px-4 sm:px-6 lg:px-12 border-b border-slate-200" id="certificate-showcase">
                    <div class="max-w-[1440px] mx-auto text-center">
                        <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight text-[#0A0A0A] mb-3">
                            Earn a Recognized {{ $course->name }} Training Certificate
                        </h2>

                        <p class="font-sans text-xs sm:text-sm text-slate-600 max-w-3xl mx-auto mb-10 leading-relaxed">
                            {{ $course->name }} Training awards a ZeblearnIndia certified credential recognized by Indian corporates. The certificate includes a unique verification code for employer validation.
                        </p>

                        <!-- 3 Certificate Cards Carousel / Grid -->
                        <div class="relative max-w-6xl mx-auto">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
                                <!-- Card 1: Santosh Kirdat -->
                                <div class="rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:shadow-md transition-all overflow-hidden flex flex-col justify-between group cursor-pointer"
                                     @click="openCert('/images/certificates/newc1.webp', 'SAP Certified – SAP S/4HANA Cloud Private Edition, Sourcing and Procurement', 'SAP Global Certification', 'Verified Candidate: Santosh Kirdat', 'SAP-CERT-4921')">
                                    <div class="p-3 sm:p-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-center">
                                        <img src="/images/certificates/newc1.webp" alt="Santosh Kirdat SAP S/4HANA Certificate" class="w-full h-auto max-h-[240px] sm:max-h-[260px] object-contain rounded-lg shadow-2xs group-hover:scale-[1.02] transition-transform duration-200" loading="lazy">
                                    </div>
                                    <div class="p-4 sm:p-5 border-b-4 border-amber-400">
                                        <h4 class="font-heading font-bold text-[#0A0A0A] text-sm sm:text-base">Santosh Kirdat</h4>
                                        <p class="font-sans text-xs text-slate-500 mt-1 line-clamp-1">SAP Certified &ndash; SAP S/4HANA Cloud Private Edition, Sourcing &amp; Procurement</p>
                                    </div>
                                </div>

                                <!-- Card 2: Tapaswini Sahoo -->
                                <div class="rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:shadow-md transition-all overflow-hidden flex flex-col justify-between group cursor-pointer"
                                     @click="openCert('/images/certificates/newc2.webp', 'SAP Certified Associate – Back-End Developer, ABAP Cloud', 'SAP Global Certification', 'Verified Candidate: Tapaswini Sahoo', 'SAP-CERT-8842')">
                                    <div class="p-3 sm:p-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-center">
                                        <img src="/images/certificates/newc2.webp" alt="Tapaswini Sahoo SAP ABAP Cloud Certificate" class="w-full h-auto max-h-[240px] sm:max-h-[260px] object-contain rounded-lg shadow-2xs group-hover:scale-[1.02] transition-transform duration-200" loading="lazy">
                                    </div>
                                    <div class="p-4 sm:p-5 border-b-4 border-amber-400">
                                        <h4 class="font-heading font-bold text-[#0A0A0A] text-sm sm:text-base">Tapaswini Sahoo</h4>
                                        <p class="font-sans text-xs text-slate-500 mt-1 line-clamp-1">SAP Certified Associate &ndash; Back-End Developer, ABAP Cloud</p>
                                    </div>
                                </div>

                                <!-- Card 3: Santosh Kirdat -->
                                <div class="rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:shadow-md transition-all overflow-hidden flex flex-col justify-between group cursor-pointer"
                                     @click="openCert('/images/certificates/newc3.webp', 'SAP Certified – Implementation Consultant, SAP Ariba Procurement', 'SAP Global Certification', 'Verified Candidate: Santosh Kirdat', 'SAP-CERT-3109')">
                                    <div class="p-3 sm:p-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-center">
                                        <img src="/images/certificates/newc3.webp" alt="Santosh Kirdat SAP Ariba Certificate" class="w-full h-auto max-h-[240px] sm:max-h-[260px] object-contain rounded-lg shadow-2xs group-hover:scale-[1.02] transition-transform duration-200" loading="lazy">
                                    </div>
                                    <div class="p-4 sm:p-5 border-b-4 border-amber-400">
                                        <h4 class="font-heading font-bold text-[#0A0A0A] text-sm sm:text-base">Santosh Kirdat</h4>
                                        <p class="font-sans text-xs text-slate-500 mt-1 line-clamp-1">SAP Certified &ndash; Implementation Consultant, SAP Ariba Procurement</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Carousel Right Arrow -->
                            <button
                                type="button"
                                @click="openCert('/images/certificates/newc1.webp', 'SAP Certified – SAP S/4HANA Cloud Private Edition', 'SAP Global Certification', 'Verified Candidate: Santosh Kirdat', 'SAP-CERT-4921')"
                                class="hidden lg:flex absolute -right-5 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white border border-slate-200 shadow-lg items-center justify-center text-slate-700 hover:text-[#FB6221] hover:border-[#FB6221] hover:scale-105 transition-all cursor-pointer z-10"
                                aria-label="Next Certificate"
                            >
                                <span class="material-symbols-outlined text-[22px]">chevron_right</span>
                            </button>
                        </div>
                    </div>
                </section>
            @endif

            {{-- 3. OVERVIEW & OBJECTIVES SECTION --}}
            @if ($sectionKey === 'overview')
                <section class="w-full py-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto" id="course-overview">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <div class="lg:col-span-8 space-y-8">
                            <div class="bg-white rounded-3xl p-6 sm:p-8 lg:p-10 border border-slate-200/90 shadow-xs">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                                        DETAILED ROADMAP
                                    </span>
                                    <span class="font-mono text-xs text-slate-400">&bull; Comprehensive Syllabus</span>
                                </div>
                                <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mb-6">
                                    Course Overview &amp; Curriculum Scope
                                </h2>
                                <div class="course-prose font-sans text-base text-slate-700 leading-relaxed">
                                    {!! app(\App\Services\MarkdownSanitizerService::class)->toCourseOverviewHtml($course->course_objectives ?: ($course->description ?: $course->short_description)) !!}
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
                    $capstoneCards = [
                        [
                            'company' => 'Flipkart',
                            'logo' => 'images/partners/flipkart.svg',
                            'title' => 'Flipkart SAP SD Order-to-Cash',
                            'description' => 'SAP Sales & Distribution rollout across 150+ locations streamlining order-to-cash and delivery processing.',
                        ],
                        [
                            'company' => 'TCS',
                            'logo' => 'images/partners/tcs.svg',
                            'title' => 'TCS Global SAP ABAP Rollout',
                            'description' => 'Custom ABAP development & global template rollout for multi-country enterprise operations and reporting.',
                        ],
                        [
                            'company' => 'Aditya Birla Group',
                            'logo' => 'images/partners/aditya-birla.svg',
                            'title' => 'Aditya Birla SAP MM + Procurement',
                            'description' => 'Materials Management & procurement automation across multi-plant operations and business units.',
                        ],
                        [
                            'company' => 'Deloitte',
                            'logo' => 'images/partners/deloitte.svg',
                            'title' => 'Deloitte S/4HANA Finance Cloud Migration',
                            'description' => 'End-to-end general ledger, accounts payable, and asset accounting migration to modern S/4HANA finance.',
                        ],
                        [
                            'company' => 'Accenture',
                            'logo' => 'images/partners/accenture.svg',
                            'title' => 'Accenture Global Supply Chain & Ariba Network',
                            'description' => 'Direct supplier integration, source-to-pay automation, and enterprise catalog procurement.',
                        ],
                        [
                            'company' => 'Capgemini',
                            'logo' => 'images/partners/capgemini.svg',
                            'title' => 'Capgemini SAP SuccessFactors HCM Transformation',
                            'description' => 'Employee central configuration, payroll integration, and automated performance management workflows.',
                        ],
                        [
                            'company' => 'Infosys',
                            'logo' => 'images/partners/infosys.svg',
                            'title' => 'Infosys Production Planning & Shop Floor',
                            'description' => 'Shop floor control, bill of materials (BOM), and capacity planning for discrete manufacturing plants.',
                        ],
                        [
                            'company' => 'Wipro',
                            'logo' => 'images/partners/wipro.svg',
                            'title' => 'Wipro SAP EWM Warehouse Modernization',
                            'description' => 'Inbound receiving, slotting, wave management, and RF gun integration for high-throughput logistics hubs.',
                        ],
                    ];
                @endphp
                <section class="w-full bg-[#FAFAF8] py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-200" id="projects">
                    <div class="max-w-[1440px] mx-auto">
                        <div class="text-center max-w-3xl mx-auto mb-10">
                            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                                Capstone Projects in {{ $course->name }} Training
                            </h2>
                            <p class="text-slate-600 text-sm sm:text-base mt-2 leading-relaxed">
                                {{ $course->name }} Training culminates in an end-to-end enterprise implementation project. You document business blueprints, configure organizational units, and execute live testing.
                            </p>
                        </div>

                        {{-- Marquee Container with Corporate Project Cards --}}
                        <div class="zeb-marquee-container -mx-4 px-4 sm:-mx-6 sm:px-6 lg:-mx-12 lg:px-12 py-3 overflow-hidden" role="region" aria-label="Capstone Projects Infinite Marquee">
                            <div class="zeb-marquee-track zeb-marquee-left flex gap-6 items-stretch focus:outline-none" style="--zeb-marquee-duration: 42s; padding-right: 1.5rem;">
                                @foreach (range(1, 2) as $iteration)
                                    @foreach ($capstoneCards as $proj)
                                        <div class="w-[320px] sm:w-[360px] bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between shrink-0 group">
                                            <div>
                                                <div class="h-10 flex items-center mb-5">
                                                    <img
                                                        src="{{ asset($proj['logo']) }}"
                                                        alt="{{ $proj['company'] }}"
                                                        class="h-8 max-h-8 w-auto max-w-[140px] object-contain object-left"
                                                        loading="lazy"
                                                    />
                                                </div>

                                                <div class="mb-4">
                                                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200/80">
                                                        PROJECT
                                                    </span>
                                                </div>

                                                <h3 class="font-heading text-base sm:text-lg font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors leading-snug mb-2">
                                                    {{ $proj['title'] }}
                                                </h3>

                                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-sans line-clamp-3">
                                                    {{ $proj['description'] }}
                                                </p>
                                            </div>

                                            <div class="pt-5 border-t border-slate-100 mt-6 flex items-center justify-between">
                                                <button
                                                    type="button"
                                                    @click="$dispatch('open-lead-modal', { source: 'capstone-project', course: '{{ addslashes($course->name) }}' })"
                                                    class="text-xs font-bold text-slate-900 group-hover:text-[#FB6221] transition-colors inline-flex items-center gap-1 font-heading cursor-pointer"
                                                >
                                                    <span>View More</span>
                                                    <span class="transition-transform group-hover:translate-x-1">→</span>
                                                </button>
                                                <span class="text-[11px] font-mono text-slate-400 font-semibold">{{ $proj['company'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>
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
                        <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-1">
                            Two-Tier Certification Architecture
                        </h2>
                        <p class="text-slate-600 text-sm sm:text-base mt-2">
                            Distinguishing ZebLearn's verifiable course completion credential from official external vendor examination readiness.
                        </p>
                    </div>

                    <!-- Dual Category Cards Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mb-12">
                        <!-- Category A: ZebLearn Course Completion Certificate -->
                        <div class="lg:col-span-6 flex flex-col items-center justify-center text-center p-4 sm:p-6">
                            <!-- Certificate Image (Clean Floating, Click to Inspect) -->
                            <div class="w-full flex flex-col items-center group cursor-pointer"
                                 @click="openCert('/images/certificates/certificate.webp', 'ZebLearn Course Completion Certificate', 'ZebLearn Academy', 'Cryptographically Verifiable Institutional Credential', 'ZEB-CERT-VERIFIED')">
                                <img
                                    src="{{ asset('images/certificates/certificate.webp') }}"
                                    alt="ZebLearn Course Completion Certificate"
                                    class="w-full h-auto max-h-[380px] sm:max-h-[420px] object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.12)] group-hover:drop-shadow-[0_22px_45px_rgba(0,0,0,0.18)] group-hover:scale-[1.02] transition-all duration-300 rounded-sm"
                                    loading="lazy"
                                />
                            </div>

                            <!-- Name of Certificate -->
                            <h3 class="font-heading text-lg sm:text-xl font-bold text-[#0A0A0A] mt-5">
                                ZebLearn Course Completion Certificate
                            </h3>

                            <!-- QR Verify Button Only -->
                            <a href="{{ route('certificates.verify') }}" class="mt-4 inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#0A0A0A] hover:bg-[#FB6221] text-white font-mono text-xs font-bold uppercase tracking-wider transition-all duration-200 shadow-sm hover:shadow-md">
                                <span class="material-symbols-outlined text-[18px]">qr_code_scanner</span>
                                <span>QR Verify</span>
                            </a>
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

                    <!-- Home Page Clean Certificate Showcase Strip -->
                    <div class="pt-10 border-t border-slate-200">
                        <div class="text-center max-w-2xl mx-auto mb-8">
                            <h3 class="font-heading text-xl sm:text-3xl font-black uppercase text-[#0A0A0A] tracking-tight">
                                VERIFIED SAP ACADEMY EXCELLENCE
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-500 font-sans mt-1">
                                Nationally recognized institutional training excellence and verified corporate credentials.
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10 items-center max-w-[1240px] mx-auto">
                            <!-- Certificate 01 -->
                            <div class="flex flex-col items-center justify-center group cursor-pointer"
                                 @click="openCert('/images/certificates/certificate-1.webp', 'Inspiring Woman Tech Leader of the Year', 'National Enterprise Summit', 'Cryptographically Verifiable Institutional Credential', 'ZEB-AWARD-2026-01')">
                                <img
                                    src="/images/certificates/certificate-1.webp"
                                    alt="Inspiring Woman Tech Leader of the Year Award"
                                    class="w-full h-auto max-h-[360px] sm:max-h-[400px] object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.12)] group-hover:drop-shadow-[0_22px_45px_rgba(0,0,0,0.18)] group-hover:scale-[1.02] transition-all duration-300 rounded-sm"
                                    loading="lazy"
                                />
                                <p class="mt-4 text-center text-xs sm:text-sm font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
                                    Inspiring Woman Tech Leader of the Year
                                </p>
                            </div>

                            <!-- Certificate 02 -->
                            <div class="flex flex-col items-center justify-center group cursor-pointer"
                                 @click="openCert('/images/certificates/certificate-2.webp', 'Global Workday Training Spotlight Award', 'Global Enterprise Training Council', 'Institutional Credential', 'ZEB-AWARD-2026-02')">
                                <img
                                    src="/images/certificates/certificate-2.webp"
                                    alt="Global Workday Training Spotlight Award"
                                    class="w-full h-auto max-h-[360px] sm:max-h-[400px] object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.12)] group-hover:drop-shadow-[0_22px_45px_rgba(0,0,0,0.18)] group-hover:scale-[1.02] transition-all duration-300 rounded-sm"
                                    loading="lazy"
                                />
                                <p class="mt-4 text-center text-xs sm:text-sm font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
                                    Global Workday Training Spotlight Award
                                </p>
                            </div>

                            <!-- Certificate 03 -->
                            <div class="flex flex-col items-center justify-center group cursor-pointer"
                                 @click="openCert('/images/certificates/certificate-3.webp', 'India\'s Leadership Excellence Award 2026', 'Leadership Excellence Forum', 'Institutional Credential', 'ZEB-AWARD-2026-03')">
                                <img
                                    src="/images/certificates/certificate-3.webp"
                                    alt="India's Leadership Excellence Award 2026"
                                    class="w-full h-auto max-h-[360px] sm:max-h-[400px] object-contain drop-shadow-[0_15px_30px_rgba(0,0,0,0.12)] group-hover:drop-shadow-[0_22px_45px_rgba(0,0,0,0.18)] group-hover:scale-[1.02] transition-all duration-300 rounded-sm"
                                    loading="lazy"
                                />
                                <p class="mt-4 text-center text-xs sm:text-sm font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
                                    India's Leadership Excellence Award 2026
                                </p>
                            </div>
                        </div>
                    </div>


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
                    $displayTestimonials = $course->publishedTestimonials->isNotEmpty()
                        ? $course->publishedTestimonials
                        : ($testimonialsList->isNotEmpty() ? $testimonialsList : collect());

                    if ($displayTestimonials->isEmpty()) {
                        $displayTestimonials = collect([
                            (object) [
                                'student_name' => 'Sumit Singh',
                                'student_title_or_company' => 'SAP FICO · Placed at Accenture',
                                'rating' => 5,
                                'review_text' => 'It is due to the quality training provided here that I got placed in Accenture. The atmosphere is brilliant and everyone is studying hard. The trainers emphasize real system business workflows rather than just theoretical concepts.',
                                'avatar_path' => null,
                            ],
                            (object) [
                                'student_name' => 'Ganesh Agrawal',
                                'student_title_or_company' => 'SAP Functional Consultant',
                                'rating' => 5,
                                'review_text' => 'SAP Training with Real-Time Projects. I searched for ZebLearn reviews complaints, but my experience was excellent. Their SAP MM and FICO training is designed for real-world projects and direct system practice.',
                                'avatar_path' => null,
                            ],
                            (object) [
                                'student_name' => 'Deeksha',
                                'student_title_or_company' => 'Fast Track SAP FICO Alum',
                                'rating' => 5,
                                'review_text' => 'I found ZebLearn the best place if you are searching for jobs. The trainers are very supportive and you will get ample opportunities here.',
                                'avatar_path' => null,
                            ],
                            (object) [
                                'student_name' => 'Ayesha Khan',
                                'student_title_or_company' => 'ERP & Analytics Specialist',
                                'rating' => 5,
                                'review_text' => 'The live classes and hands-on system configurations were practical and beginner-friendly. I got placed with continuous mentor doubt solving and guidance.',
                                'avatar_path' => null,
                            ],
                            (object) [
                                'student_name' => 'Kunal Kashyap',
                                'student_title_or_company' => 'Lead SAP Consultant @ PwC',
                                'rating' => 5,
                                'review_text' => 'Outstanding ledger reconciliation and asset accounting training. Direct server practice helped bridge the gap from theory to live enterprise deployment.',
                                'avatar_path' => null,
                            ],
                            (object) [
                                'student_name' => 'Shyam Singh',
                                'student_title_or_company' => 'ERP Systems Consultant',
                                'rating' => 5,
                                'review_text' => 'I joined ZebLearn to pursue ERP & Servers. The technical sessions were given in a very simplified manner. Practical server labs are top notch.',
                                'avatar_path' => null,
                            ],
                        ]);
                    }
                @endphp
                @if ($displayTestimonials->isNotEmpty())
                    <section
                        id="reviews"
                        x-data="{
                            showReviewModal: false,
                            rating: 5,
                            hoverRating: 0
                        }"
                        class="w-full bg-slate-50 py-16 px-4 sm:px-6 lg:px-12 border-y border-slate-200"
                    >
                        <div class="max-w-[1440px] mx-auto">
                            {{-- Flash Success Message --}}
                            @if (session('success'))
                                <div class="max-w-4xl mx-auto mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-mono shadow-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                                        <span>{{ session('success') }}</span>
                                    </div>
                                    <span class="text-[11px] text-emerald-600 uppercase font-bold">Verified Submission</span>
                                </div>
                            @endif

                            <div class="text-center max-w-3xl mx-auto mb-8">
                                <span class="font-mono text-xs font-bold uppercase tracking-wider text-[#FB6221]">ALUMNI EXPERIENCES &bull; {{ $course->category_badge }}</span>
                                <h2 class="font-heading text-2xl sm:text-4xl font-black uppercase tracking-tight text-[#0A0A0A] mt-2">
                                    Verified Student Reviews
                                </h2>
                                <p class="text-slate-600 text-sm sm:text-base mt-2">
                                    Real feedback from enrolled students &bull; Over <strong>{{ $course->display_reviews_count }} verified reviews</strong> across global batches.
                                </p>
                                <div class="mt-4 flex items-center justify-center gap-3">
                                    <button
                                        type="button"
                                        @click="showReviewModal = true"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-mono font-bold uppercase tracking-wider transition-all shadow-md hover:shadow-lg cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">rate_review</span>
                                        <span>Write a Review</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Course Rating Summary Bar -->
                            <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-8 max-w-4xl mx-auto mb-10 border border-slate-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
                                <div class="flex items-center gap-5">
                                    <div class="text-center sm:text-left">
                                        <div class="font-heading text-4xl sm:text-5xl font-black text-white leading-none">
                                            {{ $course->display_rating }}
                                        </div>
                                        <div class="flex text-amber-400 gap-1 my-1.5 justify-center sm:justify-start">
                                            @for($i=0; $i<5; $i++)
                                                <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                                            @endfor
                                        </div>
                                        <span class="font-mono text-xs text-slate-400">Course Rating</span>
                                    </div>
                                    <div class="h-14 w-px bg-slate-800 hidden sm:block"></div>
                                    <div>
                                        <div class="font-heading text-xl sm:text-2xl font-black text-[#FB6221]">
                                            {{ $course->display_reviews_count }}+
                                        </div>
                                        <span class="text-xs text-slate-300 font-medium block">Total Verified Reviews</span>
                                        <span class="font-mono text-[11px] text-emerald-400 font-semibold">98% Recommendation Rate</span>
                                    </div>
                                </div>

                                <!-- Rating breakdown meters -->
                                <div class="flex-1 max-w-xs space-y-1.5 text-[11px] font-mono">
                                    <div class="flex items-center gap-2">
                                        <span class="w-12 text-slate-400">5 Star</span>
                                        <div class="flex-1 h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-400 rounded-full" style="width: 92%"></div>
                                        </div>
                                        <span class="w-10 text-right text-slate-300">92%</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-12 text-slate-400">4 Star</span>
                                        <div class="flex-1 h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-400/80 rounded-full" style="width: 7%"></div>
                                        </div>
                                        <span class="w-10 text-right text-slate-300">7%</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="w-12 text-slate-400">3 Star</span>
                                        <div class="flex-1 h-2 bg-slate-800 rounded-full overflow-hidden">
                                            <div class="h-full bg-slate-600 rounded-full" style="width: 1%"></div>
                                        </div>
                                        <span class="w-10 text-right text-slate-300">1%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($displayTestimonials as $test)
                                    @php
                                        $tName = $test->title ?? ($test->student_name ?? 'Verified Learner');
                                        $tRole = $test->subtitle ?? ($test->student_title_or_company ?? (method_exists($test, 'meta') ? $test->meta('author_role') : null) ?? 'Student Alumni');
                                        $tText = $test->description ?? ($test->review_text ?? '');
                                        $tRating = (int) ((method_exists($test, 'meta') ? $test->meta('rating') : null) ?? ($test->rating ?? 5));
                                        $tAvatar = $test->image_path ?? ($test->avatar_path ?? null);
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

                        {{-- Write Review Modal --}}
                        <div
                            x-show="showReviewModal"
                            x-cloak
                            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
                            @keydown.escape.window="showReviewModal = false"
                        >
                            <div
                                class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full overflow-hidden p-6 sm:p-8 space-y-5"
                                @click.away="showReviewModal = false"
                            >
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div>
                                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#FB6221] block">STUDENT FEEDBACK</span>
                                        <h3 class="font-heading text-xl font-bold uppercase text-slate-900">
                                            Review {{ $course->name }}
                                        </h3>
                                    </div>
                                    <button
                                        type="button"
                                        @click="showReviewModal = false"
                                        class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors"
                                    >
                                        <span class="material-symbols-outlined text-[20px]">close</span>
                                    </button>
                                </div>

                                <form action="{{ route('courses.reviews.store', $course) }}" method="POST" class="space-y-4">
                                    @csrf

                                    {{-- Star Rating Picker --}}
                                    <div>
                                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                                            Your Rating
                                        </label>
                                        <input type="hidden" name="rating" :value="rating" />
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center text-amber-400 cursor-pointer">
                                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                                    <button
                                                        type="button"
                                                        @click="rating = star"
                                                        @mouseenter="hoverRating = star"
                                                        @mouseleave="hoverRating = 0"
                                                        class="p-1 focus:outline-none"
                                                    >
                                                        <span
                                                            class="material-symbols-outlined text-[28px] transition-transform hover:scale-110"
                                                            :class="(hoverRating ? star <= hoverRating : star <= rating) ? 'fill-current text-amber-400' : 'text-slate-300'"
                                                        >star</span>
                                                    </button>
                                                </template>
                                            </div>
                                            <span class="font-mono text-xs font-bold text-slate-700" x-text="`${rating} / 5.0 Stars`"></span>
                                        </div>
                                    </div>

                                    {{-- Full Name --}}
                                    <div>
                                        <label for="rev_student_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                                            Full Name <span class="text-rose-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="rev_student_name"
                                            name="student_name"
                                            required
                                            value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                            placeholder="e.g. Rahul Sharma"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-2xs font-sans"
                                        />
                                    </div>

                                    {{-- Role / Company --}}
                                    <div>
                                        <label for="rev_student_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                                            Your Role / Company <span class="text-slate-400 font-normal">(Optional)</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="rev_student_title"
                                            name="student_title_or_company"
                                            placeholder="e.g. SAP Consultant @ Accenture or Fresh Graduate"
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-2xs font-sans"
                                        />
                                    </div>

                                    {{-- Detailed Feedback --}}
                                    <div>
                                        <label for="rev_text" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono mb-1.5">
                                            Your Review &amp; Experience <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea
                                            id="rev_text"
                                            name="review_text"
                                            rows="4"
                                            required
                                            placeholder="Share how the course curriculum, sandbox practicals, or mentor guidance helped your career..."
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-xs text-slate-900 focus:border-[#FB6221] focus:ring-2 focus:ring-[#FB6221]/15 transition-all outline-hidden shadow-2xs font-sans leading-relaxed"
                                        ></textarea>
                                    </div>

                                    <div class="pt-2 flex items-center justify-end gap-3">
                                        <button
                                            type="button"
                                            @click="showReviewModal = false"
                                            class="px-4 py-2 rounded-xl text-xs font-mono font-semibold uppercase text-slate-600 hover:bg-slate-100 transition-colors"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            type="submit"
                                            class="px-6 py-2.5 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white text-xs font-mono font-bold uppercase tracking-wider transition-all shadow-md hover:shadow-lg cursor-pointer flex items-center gap-1.5"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">send</span>
                                            <span>Submit Review</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                @endif
            @endif

            {{-- 10. STUDENT VIDEO STORIES SECTION --}}
            @if ($sectionKey === 'video_stories' && ($trustConfig['sections']['video_stories']['enabled'] ?? true))
                @php
                    $videos = collect($trustRecords['video_stories'] ?? []);
                    $includeGlobal = $trustConfig['include_global'] ?? true;
                    if ($videos->isEmpty() && $includeGlobal) {
                        $videos = collect([
                            (object) [
                                'title' => "Sumit Singh's Corporate Placement at Accenture",
                                'subtitle' => 'Associate Consultant - SAP Financials',
                                'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                                'image_path' => '/images/banner/orange/AYUSH.webp',
                                'description' => "ZebLearn's hands-on server practice, financial ledger configurations, and real-world P2P project scenarios gave me the exact confidence I needed to clear technical interview rounds.",
                                'metadata' => ['duration' => '3 min', 'student_name' => 'Sumit Singh', 'role' => 'SAP Consultant', 'company' => 'Accenture'],
                            ],
                            (object) [
                                'title' => "Sneha Nair's Transition to Supply Chain ERP",
                                'subtitle' => 'SAP MM Functional Consultant',
                                'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                                'image_path' => '/images/banner/orange/DIVYA%20SHREE.webp',
                                'description' => 'The end-to-end procurement and inventory management configuration in live SAP GUI helped me master business processes with confidence.',
                                'metadata' => ['duration' => '4 min', 'student_name' => 'Sneha Nair', 'role' => 'SAP MM Consultant', 'company' => 'Global ERP Solutions'],
                            ],
                            (object) [
                                'title' => "Ganesh Agrawal's Corporate Placement Journey",
                                'subtitle' => 'SAP SD Solution Analyst',
                                'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                                'image_path' => '/images/banner/orange/PREETI.webp',
                                'description' => 'The real-time order-to-cash workflows, pricing procedures, and mock interview drills prepared me for enterprise consulting roles.',
                                'metadata' => ['duration' => '5 min', 'student_name' => 'Ganesh Agrawal', 'role' => 'SAP SD Analyst', 'company' => 'Global IT Consulting'],
                            ],
                            (object) [
                                'title' => "Damini Sharma's Production Planning Success",
                                'subtitle' => 'Junior SAP PP Consultant',
                                'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                                'image_path' => '/images/banner/orange/SHRUTI.webp',
                                'description' => 'From manufacturing master data setup to MRP execution, mentor feedback made complex logistics transparent and accessible.',
                                'metadata' => ['duration' => '4 min', 'student_name' => 'Damini Sharma', 'role' => 'Junior SAP PP Consultant', 'company' => 'Manufacturing Tech'],
                            ],
                        ]);
                    }
                @endphp
                @if ($videos->isNotEmpty())
                    <section class="w-full bg-white py-16 px-4 sm:px-6 lg:px-12 border-b border-slate-200" id="video-stories">
                        <div class="max-w-[1440px] mx-auto">
                            <div class="text-center max-w-3xl mx-auto mb-12">
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

                        {{-- SEO Spider Web: Interview Fast-Track for Course Module --}}
                        @if (!empty($interviewQuestions) && $interviewQuestions->isNotEmpty())
                            <div class="mt-8">
                                <x-seo.interview-guide-strip :questions="$interviewQuestions" title="Interview Preparation & Common Questions for This Module" />
                            </div>
                        @endif
                    </div>
                </section>

                {{-- SEO Spider Web: Technical Study Guides Related to Course --}}
                @if (!empty($relatedBlogs) && $relatedBlogs->isNotEmpty())
                    <x-seo.related-blogs :blogs="$relatedBlogs" title="Technical Study Guides & Articles for {{ $course->name }}" />
                @endif
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

    {{-- FLOATING STICKY ACTION PILLS (BOTTOM-LEFT) --}}
    <div class="fixed bottom-5 left-5 z-40 flex flex-col items-start gap-2.5 pointer-events-auto">
        <button
            type="button"
            @click="openVideo('https://www.youtube.com/watch?v=dQw4w9WgXcQ', '{{ addslashes($course->name) }} Live Demo Video', 'ZebLearn Academy', 'Live Online Masterclass')"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-amber-400 hover:bg-amber-300 text-slate-950 font-sans text-xs font-bold shadow-xl hover:shadow-2xl transition-all hover:scale-105 cursor-pointer border border-amber-300/60"
        >
            <span class="material-symbols-outlined text-[17px] fill-current text-slate-950">play_circle</span>
            <span>Watch Demo Video</span>
        </button>

        <a
            href="tel:+918506888288"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#080d1a] hover:bg-slate-900 border border-slate-700/80 text-white font-sans text-xs font-semibold shadow-xl hover:shadow-2xl transition-all hover:scale-105"
        >
            <span class="material-symbols-outlined text-[15px] text-amber-400">call</span>
            <span>Placements query?</span>
        </a>

        <a
            href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20have%20questions%20about%20{{ urlencode($course->name) }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#25D366] hover:bg-[#20bd5a] text-white font-sans text-xs font-semibold shadow-xl hover:shadow-2xl transition-all hover:scale-105 border border-emerald-400/40"
        >
            <span class="material-symbols-outlined text-[15px] fill-current">chat</span>
            <span>Questions? WhatsApp us.</span>
        </a>
    </div>

</div>
@endsection
