@extends('layouts.app')

@section('title', 'SAP Courses Online | Multi-Module SAP Academy | ZebLearn')
@section('description', 'Explore SAP courses at ZebLearn Academy. Learn SAP FICO, MM, SD, PP, ABAP and Basis with live online classes, real SAP system sandbox access and placement support.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')
@section('og_title', 'SAP Courses Online | Multi-Module SAP Academy | ZebLearn')
@section('og_description', 'Explore SAP courses at ZebLearn Academy. Learn SAP FICO, MM, SD, PP, ABAP and Basis with live online classes, real SAP system sandbox access and placement support.')
@section('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')
@section('twitter_title', 'SAP Courses Online | Multi-Module SAP Academy | ZebLearn')
@section('twitter_description', 'Explore SAP courses at ZebLearn Academy. Learn SAP FICO, MM, SD, PP, ABAP and Basis with live online classes, real SAP system sandbox access and placement support.')
@section('robots', 'index,follow,max-image-preview:large')

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "ZebLearn SAP Academy Courses",
  "description": "Comprehensive multi-module SAP curriculum catalog for freshers, domain professionals and IT career transitions.",
  "url": "https://sap.zeblearn.com/courses",
  "numberOfItems": 11,
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
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-36 sm:pt-40 lg:pt-44 pb-12 w-full">
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
                ZebLearn delivers hands-on, multi-module SAP education. From core functional accounting to technical development and cloud extensions, our curriculum is engineered for career outcomes, real system proficiency, and corporate placements.
            </p>
        </div>

        <!-- Three Audience Pathways Banner (Hero Matrix) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 animate-hero-assembly">
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
                        For domain professionals in Finance, SCM, or Operations wanting to become certified SAP lead consultants.
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
                        For software engineers, DBAs, and sysadmins transitioning into high-growth SAP technical roles.
                    </p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-500">
                    <span>Key Track:</span>
                    <span class="font-bold text-[#0A0A0A]">ABAP on HANA · Basis</span>
                </div>
            </div>
        </div>
    </section>

    <!-- COURSE CATALOG SECTION -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20 w-full" id="catalog">
        
        <!-- Interactive Category and Status Filters -->
        <x-course.filters :activeCategory="$activeCategory" :activeStatus="$activeStatus" />

        <!-- 1. AVAILABLE COURSES SECTION (LIVE ADMISSIONS) -->
        <div class="mb-14" id="section-available">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-[#0A0A0A]">
                        AVAILABLE NOW · ADMISSIONS OPEN
                    </h2>
                </div>
                <span class="font-mono text-xs text-slate-500 font-semibold hidden sm:inline-block">
                    CONFIRMED BATCH SCHEDULE
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="available-course-grid">
                @foreach($allCourses->where('status', 'available') as $course)
                    <div class="course-card-wrapper" data-category="{{ $course->category }}" data-status="{{ $course->status }}">
                        <x-course.card :course="$course" :featured="true" />
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. UPCOMING ACADEMY BATCHES SECTION -->
        <div class="mb-14" id="section-upcoming">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-[#0A0A0A]">
                        UPCOMING BATCHES · ENROL INTEREST
                    </h2>
                </div>
                <span class="font-mono text-xs text-orange-600 font-bold uppercase tracking-wider hidden sm:inline-block">
                    PRE-REGISTRATION OPEN
                </span>
            </div>

            <p class="font-sans text-xs sm:text-sm text-slate-600 mb-6 max-w-3xl leading-relaxed">
                These modules represent confirmed academy cohorts launching in upcoming batches. You can attend a complimentary demo session and reserve your seat before standard batch schedules fill up.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="upcoming-course-grid">
                @foreach($allCourses->where('status', 'upcoming') as $course)
                    <div class="course-card-wrapper" data-category="{{ $course->category }}" data-status="{{ $course->status }}">
                        <x-course.card :course="$course" />
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. PLANNED ROADMAP CANDIDATES SECTION -->
        <div class="mb-14" id="section-planned">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-slate-700">
                        ACADEMY ROADMAP CANDIDATES
                    </h2>
                </div>
                <span class="font-mono text-xs text-slate-400 font-semibold hidden sm:inline-block">
                    CURRICULUM IN DEVELOPMENT
                </span>
            </div>

            <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4 sm:p-5 mb-6 text-amber-950 font-sans text-xs sm:text-sm leading-relaxed flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-amber-600 shrink-0 mt-0.5">info</span>
                <p>
                    <strong>Academy Transparency Notice:</strong> The courses below are roadmap candidates undergoing faculty curriculum design and enterprise sandbox provisioning. They are not yet open for paid enrollment. You can register your email or phone to receive syllabus brochures once batches are announced.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="planned-course-grid">
                @foreach($allCourses->where('status', 'planned') as $course)
                    <div class="course-card-wrapper" data-category="{{ $course->category }}" data-status="{{ $course->status }}">
                        <x-course.card :course="$course" />
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ACADEMY ADVISORY CTA BANNER -->
        <x-course.cta />

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
                            <span>Which SAP course should I choose for my career background?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        If you have a background in accounting, commerce, or corporate finance (B.Com, M.Com, MBA Finance, CA), <strong>SAP FICO</strong> is the most direct functional match. For supply chain, procurement, or warehouse operations, <strong>SAP MM</strong> is recommended. For marketing, billing, and order management, <strong>SAP SD</strong> is ideal. Programmers, computer science graduates, and DBAs typically excel in <strong>SAP ABAP on HANA</strong> or <strong>SAP Basis</strong>.
                    </div>
                </details>

                <details class="group py-4 cursor-pointer">
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">02</span>
                            <span>What is the difference between Available, Upcoming, and Roadmap courses?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        <strong>Available Now</strong> indicates active live cohorts currently enrolling with published tuition fees and guaranteed batch dates (currently SAP FICO). <strong>Upcoming Batches</strong> are confirmed offerings where curriculum and trainers are set, and pre-registrations for demo classes are open. <strong>Roadmap Candidates</strong> are advanced modules under academic development as ZebLearn scales into a complete multi-module SAP academy.
                    </div>
                </details>

                <details class="group py-4 cursor-pointer">
                    <summary class="flex items-center justify-between font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">03</span>
                            <span>Will I receive real SAP GUI server practice access?</span>
                        </div>
                        <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                    </summary>
                    <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Yes. Practical system exposure is the foundational pillar of ZebLearn training. All enrolled students receive dedicated SAP server login credentials to practice configuration, execute transactions, and solve real business blueprint assignments.
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
                        ZebLearn partners with <strong>PlacementBaba</strong>, an active job portal connecting SAP consultants with hiring employers. Toward the conclusion of your course, our faculty conducts dedicated mock interviews, optimizes your technical resume, and routes your profile to relevant open job listings.
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
                        Absolutely. We believe every prospective consultant should evaluate our live online teaching style and instructor depth before financial commitment. Simply book a complimentary demo class on our website or message us on WhatsApp.
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
    const catButtons = document.querySelectorAll('#category-filter-group button');
    const statButtons = document.querySelectorAll('#status-filter-group button');
    const cards = document.querySelectorAll('.course-card-wrapper');
    const countDisplay = document.getElementById('filter-result-count');

    let currentCat = '{{ $activeCategory }}';
    let currentStat = '{{ $activeStatus }}';

    function applyFilters() {
        let visibleCount = 0;

        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            const cardStat = card.getAttribute('data-status');

            const catMatch = currentCat === 'all' || cardCat === currentCat;
            const statMatch = currentStat === 'all' || cardStat === currentStat;

            if (catMatch && statMatch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (countDisplay) {
            countDisplay.textContent = `Showing ${visibleCount} of ${cards.length} modules`;
        }

        // Toggle section headers if all cards inside are hidden
        ['available', 'upcoming', 'planned'].forEach(sec => {
            const secEl = document.getElementById(`section-${sec}`);
            if (secEl) {
                const hasVisible = secEl.querySelectorAll('.course-card-wrapper:not([style*="display: none"])').length > 0;
                secEl.style.display = hasVisible ? '' : 'none';
            }
        });
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

    statButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            statButtons.forEach(b => {
                b.classList.remove('bg-orange-50', 'text-[#FB6221]', 'border', 'border-orange-200', 'font-bold');
                b.classList.add('text-slate-600');
            });
            btn.classList.remove('text-slate-600');
            btn.classList.add('bg-orange-50', 'text-[#FB6221]', 'border', 'border-orange-200', 'font-bold');

            currentStat = btn.getAttribute('data-filter-status');
            applyFilters();
        });
    });

    // Run initial filter check
    if (currentCat !== 'all' || currentStat !== 'all') {
        applyFilters();
    }
});
</script>
@endpush

