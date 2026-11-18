@extends('layouts.app')

@section('title', 'All Professional Courses | Multi-Module Enterprise Academy | ZebLearn')
@section('description', 'Explore industry-leading professional courses in SAP ERP, Workday, Salesforce, Data Science, Cloud Computing, Cyber Security, Digital Marketing and more at ZebLearn.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')
@section('og_title', 'All Professional Courses | Multi-Module Enterprise Academy | ZebLearn')
@section('og_description', 'Explore industry-leading professional courses in SAP ERP, Workday, Salesforce, Data Science, Cloud Computing, Cyber Security, Digital Marketing and more at ZebLearn.')
@section('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')
@section('twitter_title', 'All Professional Courses | Multi-Module Enterprise Academy | ZebLearn')
@section('twitter_description', 'Explore industry-leading professional courses in SAP ERP, Workday, Salesforce, Data Science, Cloud Computing, Cyber Security, Digital Marketing and more at ZebLearn.')
@section('robots', $robotsDirective ?? 'index,follow,max-image-preview:large')

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "ZebLearn Academy Professional Courses",
  "description": "Comprehensive multi-module industry curriculum catalog for freshers, domain professionals and enterprise career transitions.",
  "url": "https://sap.zeblearn.com/courses",
  "numberOfItems": 27,
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "item": {
        "@type": "Course",
        "name": "SAP FICO Training",
        "description": "Financial Accounting and Controlling course with live online classes and real SAP GUI practice.",
        "provider": {
          "@type": "EducationalOrganization",
          "name": "ZebLearn",
          "sameAs": "https://sap.zeblearn.com"
        },
        "url": "https://sap.zeblearn.com/courses/sap-fico-training"
      }
    },
    {
      "@type": "ListItem",
      "position": 2,
      "item": {
        "@type": "Course",
        "name": "SAP MM Training",
        "description": "Materials Management procurement lifecycle training.",
        "provider": {
          "@type": "EducationalOrganization",
          "name": "ZebLearn"
        }
      }
    },
    {
      "@type": "ListItem",
      "position": 3,
      "item": {
        "@type": "Course",
        "name": "SAP SD Training",
        "description": "Sales and Distribution Order-to-Cash cycle training.",
        "provider": {
          "@type": "EducationalOrganization",
          "name": "ZebLearn"
        }
      }
    },
    {
      "@type": "ListItem",
      "position": 4,
      "item": {
        "@type": "Course",
        "name": "SAP PP Training",
        "description": "Production Planning and Manufacturing operations training.",
        "provider": {
          "@type": "EducationalOrganization",
          "name": "ZebLearn"
        }
      }
    }
  ]
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://sap.zeblearn.com/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Courses",
      "item": "https://sap.zeblearn.com/courses"
    }
  ]
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <!-- HERO SECTION -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-36 sm:pt-40 lg:pt-44 pb-8 w-full">
        <!-- Breadcrumbs & Telemetry -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200 animate-hero-badge">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">Courses</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">MULTI-MODULE SAP ACADEMY</span>
            </div>
        </div>

        <!-- Headline & Editorial Lead -->
        <div class="mb-10">
            <h1 class="font-heading text-4xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight leading-[0.98] text-[#0A0A0A] select-none animate-hero-title">
                PRACTICAL SAP ACADEMY. <br/>
                <span class="text-[#FB6221]">BUILT FOR CONSULTANTS.</span>
            </h1>
            <p class="font-sans text-base sm:text-lg text-slate-600 max-w-3xl leading-relaxed mt-6 animate-hero-desc">
                ZebLearn delivers hands-on, multi-module enterprise education across SAP ERP, Workday, Salesforce, Cloud Computing, Data Science and modern technology stacks. Engineered for career outcomes, real system proficiency, and corporate placements.
            </p>
        </div>

        <!-- Three Audience Pathways Banner (Hero Matrix) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 mb-10 animate-hero-assembly">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between group zeb-card-interactive">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221]">PATHWAY 01</span>
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">school</span>
                    </div>

                    <!-- Bespoke 2.5D Isometric Pathway Illustration -->
                    <x-visual.pathway-art pathway="entry" />

                    <h2 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-[#0A0A0A] mb-1">Start Your SAP Career</h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        For freshers and college graduates seeking strong functional grounding in enterprise ERP systems.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                    <span>Key Track:</span>
                    <span class="font-bold text-[#0A0A0A]">SAP FICO · MM</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between group zeb-card-interactive">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221]">PATHWAY 02</span>
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">trending_up</span>
                    </div>

                    <!-- Bespoke 2.5D Isometric Pathway Illustration -->
                    <x-visual.pathway-art pathway="acceleration" />

                    <h2 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-[#0A0A0A] mb-1">Upgrade Your Career</h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        For domain professionals in Finance, SCM, or Operations wanting to become certified lead consultants.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                    <span>Key Track:</span>
                    <span class="font-bold text-[#0A0A0A]">FICO · SD · PP</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between group zeb-card-interactive">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221]">PATHWAY 03</span>
                        <span class="material-symbols-outlined text-slate-400 text-[20px]">sync_alt</span>
                    </div>

                    <!-- Bespoke 2.5D Isometric Pathway Illustration -->
                    <x-visual.pathway-art pathway="transition" />

                    <h2 class="font-heading text-lg sm:text-xl font-bold uppercase tracking-tight text-[#0A0A0A] mb-1">Transition Into SAP</h2>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        For software engineers, DBAs, and sysadmins transitioning into high-growth enterprise technical roles.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                    <span>Key Track:</span>
                    <span class="font-bold text-[#0A0A0A]">ABAP on HANA · Basis</span>
                </div>
            </div>
        </div>

        <!-- CATEGORY PROGRAM HEADER BANNER (EXACT TO USER SCREENSHOT) -->
        <div class="w-full bg-[#0A0A0A] text-white rounded-2xl sm:rounded-3xl p-6 sm:p-10 lg:p-12 mb-8 relative overflow-hidden shadow-2xl border border-slate-800" data-reveal id="category-banner-container">
            <!-- Ambient Glow Effect -->
            <div class="absolute -right-16 -bottom-16 w-80 h-80 rounded-full bg-[#FB6221]/15 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-4xl space-y-3">
                <h2 id="category-banner-title" class="font-heading text-2xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight text-white leading-tight">
                    {{ $categoryMeta['title'] ?? 'SAP Course Certification Training Program' }}
                </h2>
                <p id="category-banner-subtitle" class="font-sans text-xs sm:text-base text-slate-300 leading-relaxed max-w-3xl font-normal">
                    {{ $categoryMeta['subtitle'] ?? 'Comprehensive SAP training modules for finance, HR, logistics and more.' }}
                </p>
                <div class="flex items-center gap-2 pt-1 text-xs sm:text-sm text-slate-300 font-medium">
                    <div class="flex items-center text-amber-400">
                        <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                        <span class="material-symbols-outlined text-[18px] fill-current">star_half</span>
                    </div>
                    <span id="category-banner-rating" class="font-bold text-white">{{ $categoryMeta['rating'] ?? '4.8' }} out of 5</span>
                    <span id="category-banner-reviews" class="text-slate-400">based on {{ $categoryMeta['reviews_count'] ?? '85,000+' }} student reviews</span>
                </div>
            </div>
        </div>

    </section>

    <!-- COURSE CATALOG SECTION -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20 w-full" id="catalog">

        <!-- Search & Advanced Filter Bar -->
        <div class="bg-white rounded-2xl p-4 sm:p-6 border border-slate-200/90 shadow-xs mb-6">
            <form method="GET" action="{{ route('courses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
                <input type="hidden" name="category" value="{{ $activeCategory }}">
                <input type="hidden" name="status" value="{{ $activeStatus }}">

                <!-- Search Input -->
                <div class="lg:col-span-5 relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                    <input
                        type="text"
                        name="q"
                        value="{{ $searchQuery ?? '' }}"
                        placeholder="Search courses, skills, ERP modules (e.g. FICO, ABAP, Workday)..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all"
                    >
                </div>

                <!-- Delivery Mode Filter -->
                <div class="lg:col-span-3">
                    <select
                        name="delivery_mode"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all"
                    >
                        <option value="all" {{ ($activeDeliveryMode ?? 'all') === 'all' ? 'selected' : '' }}>All Delivery Modes</option>
                        <option value="Live Online" {{ ($activeDeliveryMode ?? '') === 'Live Online' ? 'selected' : '' }}>Live Online Cohort</option>
                        <option value="Self-Paced" {{ ($activeDeliveryMode ?? '') === 'Self-Paced' ? 'selected' : '' }}>Self-Paced</option>
                        <option value="Classroom" {{ ($activeDeliveryMode ?? '') === 'Classroom' ? 'selected' : '' }}>Classroom Hybrid</option>
                    </select>
                </div>

                <!-- Sort Selector -->
                <div class="lg:col-span-2">
                    <select
                        name="sort"
                        onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:bg-white focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all cursor-pointer"
                    >
                        <option value="newest" {{ ($activeSort ?? 'newest') === 'newest' ? 'selected' : '' }}>Recently Added</option>
                        <option value="popular" {{ ($activeSort ?? '') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                        <option value="price_asc" {{ ($activeSort ?? '') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ ($activeSort ?? '') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="name_asc" {{ ($activeSort ?? '') === 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                        <option value="oldest" {{ ($activeSort ?? '') === 'oldest' ? 'selected' : '' }}>Oldest Added</option>
                    </select>
                </div>

                <!-- Submit & Clear Buttons -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-mono text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-xs text-center"
                    >
                        Search
                    </button>
                    @if (!empty($searchQuery) || ($activeDeliveryMode ?? 'all') !== 'all' || ($activeSort ?? 'default') !== 'default' || $activeCategory !== 'all' || $activeStatus !== 'all')
                        <a
                            href="{{ route('courses.index') }}"
                            class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono text-xs font-semibold rounded-xl transition-all text-center"
                            title="Reset all filters"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Interactive Category and Status Filters -->
        <x-course.filters :activeCategory="$activeCategory" :activeStatus="$activeStatus" />

        @if (!empty($searchQuery))
            <!-- ACTIVE SEARCH RESULTS VIEW -->
            <div class="mb-14" id="section-search-results">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-[#0A0A0A]">
                            SEARCH RESULTS FOR "{{ $searchQuery }}" ({{ $courses->total() }})
                        </h2>
                    </div>
                    <span class="font-mono text-xs text-slate-500 font-semibold hidden sm:inline-block">
                        Showing {{ $courses->count() }} of {{ $courses->total() }} matching courses
                    </span>
                </div>

                @if ($courses->isEmpty())
                    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/90 shadow-xs max-w-xl mx-auto my-8 space-y-4">
                        <div class="w-16 h-16 rounded-full bg-orange-50 text-[#FB6221] flex items-center justify-center mx-auto">
                            <span class="material-symbols-outlined text-[32px]">search_off</span>
                        </div>
                        <h3 class="font-heading text-xl font-bold text-slate-900">No matching courses found</h3>
                        <p class="font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                            We couldn't find any courses matching "{{ $searchQuery }}". Try searching for FICO, MM, PP, ABAP, S/4HANA, or reset your search.
                        </p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#FB6221] text-white font-mono text-xs font-bold uppercase tracking-wider hover:bg-[#e05214] transition-colors">
                            <span>Browse All Courses</span>
                            <span class="material-symbols-outlined text-[16px]">refresh</span>
                        </a>
                    </div>
                @else
                    <div class="flex flex-col gap-5">
                        @foreach($courses as $course)
                            <div class="course-card-wrapper" data-category="{{ $course->category }}" data-status="{{ $course->status }}">
                                <x-course.card :course="$course" :featured="$course->featured" :enrollment="$userEnrollments->get($course->id)" />
                            </div>
                        @endforeach
                    </div>

                    @if ($courses->hasPages())
                        <div class="my-8 flex justify-center">
                            {{ $courses->links() }}
                        </div>
                    @endif
                @endif
            </div>
        @else
            <!-- UNIFIED COURSES DIRECTORY -->
            <div class="mb-14" id="section-all-courses">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-[#0A0A0A]">
                            ALL COURSES ({{ $allCourses->count() }})
                        </h2>
                    </div>
                    <span class="font-mono text-xs text-slate-500 font-semibold hidden sm:inline-block">
                        SORTED BY RECENTLY ADDED
                    </span>
                </div>

                <!-- Vertical Stack of Wide Course Cards -->
                <div class="flex flex-col gap-5" id="all-course-grid">
                    @foreach($allCourses->sortByDesc('created_at') as $course)
                        <div class="course-card-wrapper" data-category="{{ $course->category }}">
                            <x-course.card :course="$course" :featured="$course->featured" :enrollment="$userEnrollments->get($course->id)" />
                        </div>
                    @endforeach
                </div>
            </div>

            @if ($courses->hasPages())
                <div class="my-8 flex justify-center">
                    {{ $courses->links() }}
                </div>
            @endif
        @endif

        <!-- ACADEMY ADVISORY CTA BANNER -->
        <x-course.cta />

        <!-- VERIFIED STUDENT REVIEWS SECTION -->
        <div class="mt-16 pt-12 border-t border-slate-200" data-reveal id="reviews">
            <x-home.verified-reviews />
        </div>

        <!-- COURSE CATALOG FAQS -->
        <div class="mt-16 pt-12 border-t border-slate-200" data-reveal>
            <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                <div>
                    <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] block mb-1">FREQUENTLY ASKED</span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-[#0A0A0A]">
                        QUESTIONS ABOUT ZEBLEARN COURSES
                    </h2>
                </div>
                <span class="font-mono text-xs text-slate-400 uppercase tracking-wider">ACADEMY POLICIES</span>
            </div>

            <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/80 shadow-xs divide-y divide-slate-100">
                <details class="group py-4 cursor-pointer" open>
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-[#FB6221]">01</span>
                            <span>Which course should I choose for my career background?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        If you have a background in accounting or commerce, <strong>SAP FICO</strong> is the most direct functional match. For supply chain, procurement, or warehouse operations, <strong>SAP MM</strong> is recommended. For marketing, billing, and order management, <strong>SAP SD</strong> or <strong>Digital Marketing</strong> is ideal. Programmers and computer science graduates excel in <strong>SAP ABAP</strong>, <strong>Java Full Stack</strong>, <strong>Cloud Computing</strong>, or <strong>Data Science</strong>.
                    </div>
                </details>

                <details class="group py-4 cursor-pointer">
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">02</span>
                            <span>How are live cohorts structured and scheduled?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Cohorts are held in small, interactive batches of up to 25 students. Classes run on flexible weekend and weekday evening schedules, complete with instructor-led doubt clearing sessions, session recordings, and practical sandbox practice.
                    </div>
                </details>

                <details class="group py-4 cursor-pointer">
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">03</span>
                            <span>Will I receive real enterprise system practice access?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Yes. Practical system exposure is the foundational pillar of ZebLearn training. All enrolled students receive dedicated system login credentials to practice configuration, execute transactions, and solve real business blueprint assignments.
                    </div>
                </details>

                <details class="group py-4 cursor-pointer">
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">04</span>
                            <span>How does placement support work through PlacementBaba?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        ZebLearn partners with <strong>PlacementBaba</strong>, an active job portal connecting certified professionals with hiring corporate employers. Toward the conclusion of your course, our faculty conducts dedicated mock interviews, optimizes your technical resume, and routes your profile to relevant open corporate positions.
                    </div>
                </details>

                <details class="group py-4 cursor-pointer">
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">05</span>
                            <span>Can I attend a free demo before paying tuition?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Absolutely. We believe every prospective learner should evaluate our live online teaching style and instructor depth before financial commitment. Simply book a complimentary demo class on our website or message us on WhatsApp.
                    </div>
                </details>
            </div>
        </div>

    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const categoriesData = @json(\App\Models\Course::CATEGORIES);
    const catButtons = document.querySelectorAll('#category-filter-group button');
    const cards = document.querySelectorAll('.course-card-wrapper');
    const countDisplay = document.getElementById('filter-result-count');

    const bannerTitle = document.getElementById('category-banner-title');
    const bannerSubtitle = document.getElementById('category-banner-subtitle');
    const bannerRating = document.getElementById('category-banner-rating');
    const bannerReviews = document.getElementById('category-banner-reviews');

    let currentCat = '{{ $activeCategory }}';

    function updateBanner(catSlug) {
        if (!bannerTitle) return;

        const info = categoriesData[catSlug] || (catSlug === 'all' ? {
            title: 'Professional Certification Training Programs',
            subtitle: 'Explore multi-module industry curriculum across SAP ERP, Workday, Salesforce, Cloud, Cyber Security, and more.',
            rating: '4.8',
            reviews_count: '85,000+'
        } : (categoriesData['sap-erp'] || {}));

        if (info.title) bannerTitle.textContent = info.title;
        if (info.subtitle) bannerSubtitle.textContent = info.subtitle;
        if (info.rating && bannerRating) bannerRating.textContent = `${info.rating} out of 5`;
        if (info.reviews_count && bannerReviews) bannerReviews.textContent = `based on ${info.reviews_count} student reviews`;
    }

    function applyFilters() {
        let visibleCount = 0;

        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category');

            // Category match logic:
            const catMatch = (currentCat === 'all')
                || (cardCat === currentCat)
                || (currentCat === 'sap-erp' && ['functional', 'technical', 'cloud_specialized', 'sap-erp'].includes(cardCat));

            if (catMatch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countDisplay) {
            countDisplay.textContent = `Showing ${visibleCount} of ${cards.length} courses`;
        }

        updateBanner(currentCat);
    }

    catButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            catButtons.forEach(b => {
                b.classList.remove('bg-[#0A0A0A]', 'text-white', 'shadow-xs');
                b.classList.add('bg-slate-100', 'text-slate-700');
            });
            btn.classList.remove('bg-slate-100', 'text-slate-700');
            btn.classList.add('bg-[#0A0A0A]', 'text-white', 'shadow-xs');

            currentCat = btn.getAttribute('data-filter-category');
            applyFilters();
        });
    });

    // Run initial filter check
    if (currentCat !== 'all') {
        applyFilters();
    }
});
</script>
@endpush
