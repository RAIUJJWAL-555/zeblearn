@extends('layouts.app')

@section('title', 'SAP FICO Course Online with Live Training and Placement Support | ZebLearn')
@section('description', 'Learn SAP FICO online with live classes, real system practice, assignments and placement support. Join our 3-month training program. Book a free demo class.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')

@push('head')
<style>
@layer base {
    html, body { margin: 0; padding: 0; }
    body { overscroll-behavior: none; }
}
details summary::-webkit-details-marker { display: none; }
</style>
@endpush

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "SAP FICO Course Online",
  "description": "Live online SAP FICO training covering financial accounting and controlling, with real system practice and placement support.",
  "url": "https://sap.zeblearn.com/courses/sap-fico-training",
  "provider": {
    "@type": "EducationalOrganization",
    "name": "ZebLearn",
    "url": "https://sap.zeblearn.com"
  },
  "offers": {
    "@type": "Offer",
    "price": "25000",
    "priceCurrency": "INR"
  },
  "hasCourseInstance": {
    "@type": "CourseInstance",
    "courseMode": "online",
    "courseWorkload": "P3M"
  }
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
      "item": "https://sap.zeblearn.com/#course-deck"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "SAP FICO",
      "item": "https://sap.zeblearn.com/courses/sap-fico-training"
    }
  ]
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <!-- HERO SECTION -->
    <section class="relative w-full pt-28 sm:pt-32 pb-12 sm:pb-16 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto overflow-hidden">
        <!-- Breadcrumb & Telemetry -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200 animate-hero-badge">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="/" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <a href="/#course-deck" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">SAP FICO</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">Next Batch Starting Soon</span>
            </div>
        </div>

        <!-- Big Display Headline -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end mb-10">
            <div class="lg:col-span-8">
                <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] block mb-2 animate-hero-badge">3-MONTH LIVE PROFESSIONAL TRACK</span>
                <h1 class="font-heading text-5xl sm:text-7xl md:text-8xl lg:text-[96px] font-black uppercase tracking-tight text-[#0A0A0A] leading-[0.95] select-none animate-hero-title">
                    SAP FICO
                </h1>
                <p class="font-heading text-xl sm:text-2xl text-slate-800 font-bold uppercase mt-4 leading-snug animate-hero-desc tracking-tight">
                    Financial Accounting and Controlling, from first principles to interview-ready.
                </p>
            </div>
            <div class="lg:col-span-4 flex flex-col justify-end">
                <p class="font-sans text-base text-slate-600 leading-relaxed animate-hero-desc">
                    Live online SAP FICO training with real system practice, enterprise assignments, mock interviews, and dedicated placement support.
                </p>
                <div class="mt-4 pt-4 border-t border-slate-200 flex items-center gap-3 animate-hero-cta">
                    <a href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo%20for%20SAP%20FICO." target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider shadow-md hover:shadow-lg transition-all hover:scale-[1.02] zeb-btn-cta">
                        <span>Book a Free Demo</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                    <a href="tel:+918506888288" class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-white hover:bg-slate-100 border border-slate-300 text-slate-900 font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs zeb-btn-cta">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">call</span>
                        <span>Enquire</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Meta Telemetry Chips Row -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 animate-hero-assembly">
            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                </div>
                <div>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 block leading-tight">Duration</span>
                    <span class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">3 Months</span>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">cast_for_education</span>
                </div>
                <div>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 block leading-tight">Mode</span>
                    <span class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">Live Online + GUI</span>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">group</span>
                </div>
                <div>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 block leading-tight">Alumni</span>
                    <span class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">1,850+ Learners</span>
                </div>
            </div>
            <div class="bg-white rounded-xl p-3.5 border border-slate-200/80 shadow-xs flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                </div>
                <div>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 block leading-tight">Outcome</span>
                    <span class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">Placement Support</span>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN TWO-COLUMN CONTENT GRID -->
    <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            <!-- LEFT MAIN COLUMN (8 cols) -->
            <div class="lg:col-span-8 space-y-12">

                <!-- 1. OVERVIEW & ADMISSIONS BENTO -->
                <section class="grid grid-cols-1 md:grid-cols-12 gap-6" data-reveal>
                    <!-- Overview Card -->
                    <div class="md:col-span-7 bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221]">01 // OVERVIEW</span>
                            </div>
                            <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase text-[#0A0A0A] leading-tight mb-4 tracking-tight">
                                Learn SAP financial accounting and controlling on a real SAP system.
                            </h2>
                            <div class="space-y-3 font-sans text-sm text-slate-600 leading-relaxed">
                                <p>
                                    Many accountants know journal entries but have not worked with the enterprise structure, fiscal year variants or field status groups that decide how SAP posts a transaction. This course covers exactly that.
                                </p>
                                <p>
                                    At ZebLearn, you work through General Ledger, Accounts Payable and Receivable, Asset Accounting and Controlling in live classes with hands-on practice.
                                </p>
                            </div>
                        </div>
                        <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between font-mono text-[11px] text-slate-500 uppercase tracking-wider">
                            <span>DURATION: 3 MONTHS</span>
                            <span class="text-[#FB6221] font-bold">MODE: LIVE ONLINE</span>
                        </div>
                    </div>

                    <!-- Who Is It For Card -->
                    <div class="md:col-span-5 bg-[#0A0A0A] text-white rounded-2xl p-6 sm:p-8 border border-slate-800 shadow-md flex flex-col justify-between">
                        <div>
                            <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221] block mb-3">02 // ADMISSIONS PROFILE</span>
                            <h3 class="font-heading text-lg sm:text-xl font-bold uppercase text-white mb-4 tracking-tight">
                                WHO IS IT FOR?
                            </h3>
                            <ul class="space-y-2.5 font-sans text-xs">
                                <li class="p-2.5 rounded-lg bg-white/5 border border-white/10 flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] shrink-0"></span>
                                    <span class="text-slate-200">Commerce &amp; B.Com graduates</span>
                                </li>
                                <li class="p-2.5 rounded-lg bg-white/5 border border-white/10 flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] shrink-0"></span>
                                    <span class="text-slate-200">Corporate accountants &amp; Tally users</span>
                                </li>
                                <li class="p-2.5 rounded-lg bg-white/5 border border-white/10 flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] shrink-0"></span>
                                    <span class="text-slate-200">CA / CMA / CS aspirants</span>
                                </li>
                                <li class="p-2.5 rounded-lg bg-white/5 border border-white/10 flex items-center gap-2.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] shrink-0"></span>
                                    <span class="text-slate-200">Consulting career switchers</span>
                                </li>
                            </ul>
                        </div>
                        <p class="font-mono text-[10px] text-[#FB6221] uppercase tracking-wider mt-6 font-bold pt-4 border-t border-white/10">
                            PRE-REQUISITE: BASIC ACCOUNTING INTUITION
                        </p>
                    </div>
                </section>

                <!-- 2. WHAT YOU'LL LEARN: 6 CORE MODULE PHASES -->
                <section>
                    <div class="flex flex-wrap items-end justify-between gap-4 mb-6">
                        <div>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] block mb-1">03 // LEARNING OUTCOMES</span>
                            <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase text-[#0A0A0A] tracking-tight">
                                WHAT YOU'LL LEARN
                            </h2>
                        </div>
                        <span class="font-mono text-xs text-slate-500 uppercase tracking-wider">6 CORE ARCHITECTURAL PHASES</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- 01 -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all flex flex-col justify-between">
                            <span class="font-mono text-xs font-bold text-[#FB6221] mb-4 block">PHASE 01</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-[#0A0A0A] uppercase mb-2 tracking-tight">Enterprise Structure</h3>
                                <p class="font-sans text-xs text-slate-600 leading-relaxed">Company code setup, business areas, and chart of accounts hierarchy configuration.</p>
                            </div>
                        </div>
                        <!-- 02 -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all flex flex-col justify-between">
                            <span class="font-mono text-xs font-bold text-[#FB6221] mb-4 block">PHASE 02</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-[#0A0A0A] uppercase mb-2 tracking-tight">Universal Journal</h3>
                                <p class="font-sans text-xs text-slate-600 leading-relaxed">FI-GL architecture, posting periods, field status variants, and ACDOCA single source of truth.</p>
                            </div>
                        </div>
                        <!-- 03 -->
                        <div class="bg-[#0A0A0A] text-white rounded-2xl p-6 border border-slate-800 shadow-sm hover:border-[#FB6221]/50 transition-all flex flex-col justify-between">
                            <span class="font-mono text-xs font-bold text-[#FB6221] mb-4 block">PHASE 03</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-white uppercase mb-2 tracking-tight">AP &amp; AR Automation</h3>
                                <p class="font-sans text-xs text-slate-300 leading-relaxed">Vendor &amp; customer subledgers, automatic payment program (F110), and dunning parameters.</p>
                            </div>
                        </div>
                        <!-- 04 -->
                        <div class="bg-[#0A0A0A] text-white rounded-2xl p-6 border border-slate-800 shadow-sm hover:border-[#FB6221]/50 transition-all flex flex-col justify-between">
                            <span class="font-mono text-xs font-bold text-[#FB6221] mb-4 block">PHASE 04</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-white uppercase mb-2 tracking-tight">Asset Accounting (FI-AA)</h3>
                                <p class="font-sans text-xs text-slate-300 leading-relaxed">Asset classes, depreciation key logic, asset acquisitions, retirement &amp; year-end closing.</p>
                            </div>
                        </div>
                        <!-- 05 -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all flex flex-col justify-between">
                            <span class="font-mono text-xs font-bold text-[#FB6221] mb-4 block">PHASE 05</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-[#0A0A0A] uppercase mb-2 tracking-tight">Controlling (CO Core)</h3>
                                <p class="font-sans text-xs text-slate-600 leading-relaxed">Cost Center Accounting (CCA), Profit Centers (PCA), and Internal Orders cost allocations.</p>
                            </div>
                        </div>
                        <!-- 06 -->
                        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:border-[#FB6221]/50 hover:shadow-md transition-all flex flex-col justify-between">
                            <span class="font-mono text-xs font-bold text-[#FB6221] mb-4 block">PHASE 06</span>
                            <div>
                                <h3 class="font-heading text-base font-bold text-[#0A0A0A] uppercase mb-2 tracking-tight">Financial Closing</h3>
                                <p class="font-sans text-xs text-slate-600 leading-relaxed">Foreign currency revaluation, balance sheet version creation, and S/4HANA migration frameworks.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 3. SYLLABUS ACCORDION -->
                <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs" data-reveal>
                    <div class="flex flex-wrap items-center justify-between pb-6 border-b border-slate-200 gap-4 mb-6">
                        <div>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] block mb-1">04 // CURRICULUM</span>
                            <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase text-[#0A0A0A] tracking-tight">
                                DETAILED SYLLABUS
                            </h2>
                        </div>
                        <span class="font-mono text-xs uppercase px-3 py-1 bg-slate-100 rounded-full font-bold text-[#0A0A0A]">
                            6 MODULES · 3 MONTHS
                        </span>
                    </div>

                    <div class="space-y-4">
                        <!-- Module 01 (Open by default) -->
                        <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-5 open:bg-white open:shadow-xs transition-all" open>
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-[#FB6221]">01</span>
                                    <h3 class="font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors tracking-tight">
                                        Enterprise Structure &amp; General Ledger Setup
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-4 mt-4 border-t border-slate-200/60 font-sans text-xs text-slate-600">
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                        <span>Company code, Credit Control, and Business Area definition</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                        <span>Fiscal Year Variants (K4, V3) &amp; Posting Period variants</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                        <span>Field status variant &amp; assignment to general ledger master</span>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                        <span>Document splitting rules &amp; Real-time zero-balance clearing</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                        <span>Foreign currency translation and valuation configurations</span>
                                    </div>
                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                        <span>ACDOCA table mapping and Universal Journal reconciliation</span>
                                    </div>
                                </div>
                            </div>
                        </details>

                        <!-- Module 02 -->
                        <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-5 open:bg-white open:shadow-xs transition-all">
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">02</span>
                                    <h3 class="font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors tracking-tight">
                                        Accounts Payable &amp; Automatic Payment Run (F110)
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 mt-4 border-t border-slate-200/60 font-sans text-xs text-slate-600 space-y-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Vendor account groups, number ranges, and reconciliation accounts</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Automatic Payment Program (F110) end-to-end setup and bank determination</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Withholding tax (TDS) configurations and payment advice generation</span>
                                </div>
                            </div>
                        </details>

                        <!-- Module 03 -->
                        <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-5 open:bg-white open:shadow-xs transition-all">
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">03</span>
                                    <h3 class="font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors tracking-tight">
                                        Accounts Receivable, Dunning &amp; Customer Statements
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 mt-4 border-t border-slate-200/60 font-sans text-xs text-slate-600 space-y-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Customer master data, credit management, and billing document integration</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Dunning procedure (F150) configuration, letter forms, and interest calculation</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Customer advances, partial payments, and residual clearing mechanisms</span>
                                </div>
                            </div>
                        </details>

                        <!-- Module 04 -->
                        <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-5 open:bg-white open:shadow-xs transition-all">
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">04</span>
                                    <h3 class="font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors tracking-tight">
                                        Fixed Asset Accounting &amp; Depreciation Runs
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 mt-4 border-t border-slate-200/60 font-sans text-xs text-slate-600 space-y-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Chart of depreciation, asset classes, and account determination</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Depreciation keys (straight line, WDV), periodic run (AFAB), and capitalization</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Asset retirement, scrap posting, and asset fiscal year closing (AJAB)</span>
                                </div>
                            </div>
                        </details>

                        <!-- Module 05 -->
                        <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-5 open:bg-white open:shadow-xs transition-all">
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">05</span>
                                    <h3 class="font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors tracking-tight">
                                        Controlling (CO-CCA, CO-PCA &amp; CO-IO)
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 mt-4 border-t border-slate-200/60 font-sans text-xs text-slate-600 space-y-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Controlling area setup and assignment to company codes</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Cost Center Accounting (CCA) and Profit Center Accounting (PCA) hierarchy</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>Internal orders, assessment cycles, and primary/secondary cost element mapping</span>
                                </div>
                            </div>
                        </details>

                        <!-- Module 06 -->
                        <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-5 open:bg-white open:shadow-xs transition-all">
                            <summary class="flex items-center justify-between cursor-pointer list-none">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">06</span>
                                    <h3 class="font-heading text-base sm:text-lg font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors tracking-tight">
                                        Integration with MM/SD &amp; S/4HANA Migration Blueprint
                                    </h3>
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">
                                    expand_more
                                </span>
                            </summary>
                            <div class="pt-4 mt-4 border-t border-slate-200/60 font-sans text-xs text-slate-600 space-y-2">
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>FI-MM automatic account determination (OBYC) &amp; GR/IR clearing</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>FI-SD revenue recognition (VKOA) &amp; billing interface posting</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                                    <span>S/4HANA Finance architectural differences and interview case studies</span>
                                </div>
                            </div>
                        </details>
                    </div>
                </section>

                <!-- 4. TOOLS & INFRASTRUCTURE STRIP -->
                <section class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                        <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221]">
                            05 // PRACTICE INFRASTRUCTURE
                        </span>
                        <span class="font-mono text-xs text-slate-500 uppercase">REAL SYSTEM ENVIRONMENT</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3.5 py-1.5 bg-[#0A0A0A] text-white rounded-lg font-mono text-xs font-medium">SAP ECC 6.0</span>
                        <span class="px-3.5 py-1.5 bg-[#0A0A0A] text-white rounded-lg font-mono text-xs font-medium">SAP S/4HANA</span>
                        <span class="px-3.5 py-1.5 bg-orange-50 border border-orange-200 text-[#FB6221] rounded-lg font-mono text-xs font-bold">24/7 Practice Server Access</span>
                        <span class="px-3.5 py-1.5 bg-slate-100 text-slate-800 rounded-lg font-mono text-xs font-semibold">Real-world Case Assignments</span>
                        <span class="px-3.5 py-1.5 bg-slate-100 text-slate-800 rounded-lg font-mono text-xs font-semibold">Interview Preparation</span>
                    </div>
                </section>

                <!-- 5. LEAD TRAINER CREDIBILITY -->
                <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221]">06 // MENTORSHIP</span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase text-[#0A0A0A] leading-tight mb-3 tracking-tight">
                        "WE DON'T JUST RUN SLIDES. YOU PRACTISE ON A REAL SAP SYSTEM FROM THE START."
                    </h3>
                    <p class="font-sans text-sm text-slate-600 mb-6 leading-relaxed">
                        Live classes led by ZebLearn's dedicated SAP functional trainers, with errors debugged in real-time as you configure the system.
                    </p>
                    <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <span class="font-heading text-2xl sm:text-3xl font-extrabold text-[#FB6221] block leading-none">1,850+</span>
                            <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider">Learners Trained</span>
                        </div>
                        <div>
                            <span class="font-heading text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] block leading-none">3 Months</span>
                            <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider">Live Online</span>
                        </div>
                        <div>
                            <span class="font-heading text-2xl sm:text-3xl font-extrabold text-[#0A0A0A] block leading-none">₹25,000</span>
                            <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider">+ GST</span>
                        </div>
                    </div>
                </section>

                <!-- 6. SAP POWER USER CERTIFICATION PROMO -->
                <section class="bg-[#0A0A0A] text-white rounded-2xl p-6 sm:p-10 border border-slate-800 shadow-xl relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="relative z-10 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 border border-white/10 rounded-full font-mono text-[10px] uppercase tracking-widest text-[#FB6221]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                            <span>OFFICIAL SAP TRACK · 80 HOURS SELF-PACED</span>
                        </div>
                        <h2 class="font-heading text-2xl sm:text-4xl lg:text-5xl font-extrabold uppercase text-white tracking-tight leading-tight">
                            SAP POWER USER CERTIFICATION COURSE
                        </h2>
                        <p class="font-sans text-sm sm:text-base text-slate-300 max-w-xl leading-relaxed">
                            80 hours of self-paced certification training directly by SAP, 3 months access, ₹40,000 + GST. A separate self-paced certification offering distinct from our 3-month live instructor-led functional course (₹25,000 + GST). Available for FICO, MM, SD, and PP tracks.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full font-mono text-xs text-slate-300 uppercase">SAP FICO</span>
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full font-mono text-xs text-slate-300 uppercase">SAP MM</span>
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full font-mono text-xs text-slate-300 uppercase">SAP SD</span>
                            <span class="px-3 py-1 bg-white/5 border border-white/10 rounded-full font-mono text-xs text-slate-300 uppercase">SAP PP</span>
                        </div>
                        <div class="pt-2">
                            <a href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20know%20more%20about%20SAP%20Power%20User%20Certification." target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-md">
                                <span>Enquire About Certification</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- 7. PLACEMENT & ACCENTURE ALUMNI STORY -->
                <section class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                    <!-- Left: Placement Partner -->
                    <div class="md:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 font-mono text-[10px] uppercase text-slate-400 mb-3">
                                <span class="material-symbols-outlined text-[16px] text-[#FB6221]">domain</span>
                                <span>PLACEMENT PARTNER</span>
                            </div>
                            <h2 class="font-heading text-xl font-bold uppercase text-[#0A0A0A] leading-tight mb-3 tracking-tight">
                                SAP JOBS, ONE CLICK AWAY
                            </h2>
                            <p class="font-sans text-xs text-slate-600 leading-relaxed mb-4">
                                Browse active SAP job openings on our exclusive placement partner PlacementBaba.
                            </p>
                        </div>
                        <a href="https://www.placementbaba.com/" target="_blank" rel="noopener" class="w-full py-3 bg-[#0A0A0A] hover:bg-black text-white font-sans text-xs font-semibold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 shadow-xs">
                            <span>Browse SAP Jobs</span>
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        </a>
                    </div>

                    <!-- Right: Alumni Review -->
                    <div class="md:col-span-7 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221]">ALUMNI VERIFIED</span>
                                <div class="flex items-center gap-0.5 text-[#FB6221]">
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                </div>
                            </div>
                            <p class="font-sans text-sm sm:text-base text-slate-800 font-medium leading-relaxed mb-4">
                                "IT IS DUE TO THE QUALITY TRAINING PROVIDED HERE THAT I GOT PLACED IN ACCENTURE. THE ATMOSPHERE IS BRILLIANT AND EVERYONE IS STUDYING HARD."
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-50 border border-orange-200 flex items-center justify-center font-heading text-sm font-bold text-[#FB6221]">
                                SS
                            </div>
                            <div>
                                <h4 class="font-sans text-xs font-bold text-[#0A0A0A]">Sumit Singh</h4>
                                <p class="font-mono text-[10px] text-slate-500 uppercase">Placed at Accenture</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- 8. FREQUENTLY ASKED QUESTIONS -->
                <section class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between pb-6 border-b border-slate-200 gap-4 mb-6">
                        <div>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] block mb-1">07 // FAQ</span>
                            <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase text-[#0A0A0A] tracking-tight">
                                FREQUENTLY ASKED QUESTIONS
                            </h2>
                        </div>
                        <span class="font-mono text-xs text-slate-400 uppercase tracking-wider">ANSWERS &amp; ADMISSIONS</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <!-- Q1 -->
                        <details class="group py-4 cursor-pointer" open>
                            <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                                <span>01. Do I need prior coding or technical SAP knowledge to learn FICO?</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform shrink-0 ml-2">expand_more</span>
                            </summary>
                            <p class="mt-3 font-sans text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                                No programming or ABAP knowledge is required. SAP FICO is a functional module. Basic accounting knowledge (debit, credit, ledgers, P&amp;L, balance sheet) is enough.
                            </p>
                        </details>

                        <!-- Q2 -->
                        <details class="group py-4 cursor-pointer">
                            <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                                <span>02. How long is the course and what are the batch timings?</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform shrink-0 ml-2">expand_more</span>
                            </summary>
                            <p class="mt-3 font-sans text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                                The instructor-led SAP FICO course runs for 3 months in live online classes. Call +91 8506 888 288 to ask about the next batch and timings.
                            </p>
                        </details>

                        <!-- Q3 -->
                        <details class="group py-4 cursor-pointer">
                            <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                                <span>03. Will I practise on a real SAP system?</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform shrink-0 ml-2">expand_more</span>
                            </summary>
                            <p class="mt-3 font-sans text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                                Yes. Classes are live and you practise on a real SAP system. Ask our team about access details when you book a demo.
                            </p>
                        </details>

                        <!-- Q4 -->
                        <details class="group py-4 cursor-pointer">
                            <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                                <span>04. Does ZebLearn provide placement support?</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform shrink-0 ml-2">expand_more</span>
                            </summary>
                            <p class="mt-3 font-sans text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                                Yes. Placement support is available through our partner PlacementBaba, along with interview preparation.
                            </p>
                        </details>

                        <!-- Q5 -->
                        <details class="group py-4 cursor-pointer">
                            <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                                <span>05. What is the fee for SAP FICO?</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform shrink-0 ml-2">expand_more</span>
                            </summary>
                            <p class="mt-3 font-sans text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                                The 3-month instructor-led course is ₹25,000 + GST. The SAP Power User Certification course (80 hours, self-paced, by SAP) is ₹40,000 + GST.
                            </p>
                        </details>

                        <!-- Q6 -->
                        <details class="group py-4 cursor-pointer">
                            <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                                <span>06. Can I attend a demo class first?</span>
                                <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform shrink-0 ml-2">expand_more</span>
                            </summary>
                            <p class="mt-3 font-sans text-xs sm:text-sm text-slate-600 max-w-3xl leading-relaxed">
                                Yes. Book a free demo, or call or WhatsApp us, and our team will share the next demo session.
                            </p>
                        </details>
                    </div>
                </section>

                <!-- 9. EXPLORE OTHER MODULES (MM, SD, PP) -->
                <section>
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div>
                            <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] block mb-1">08 // MODULE SPECTRUM</span>
                            <h2 class="font-heading text-2xl font-bold uppercase text-[#0A0A0A] tracking-tight">
                                EXPLORE OTHER MODULES
                            </h2>
                        </div>
                        <a href="/#course-deck" class="font-mono text-xs uppercase text-[#FB6221] hover:underline flex items-center gap-1 font-bold">
                            <span>All Courses</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        <!-- MM -->
                        <a href="/#course-deck" class="group bg-white hover:bg-[#0A0A0A] rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <span class="font-heading text-xl font-bold text-[#0A0A0A] group-hover:text-white transition-colors">SAP MM</span>
                                <span class="font-sans text-xs text-slate-600 group-hover:text-slate-300 transition-colors">Materials Management, Procurement &amp; Inventory</span>
                            </div>
                            <div class="flex items-center gap-6">
                                <span class="font-mono text-[11px] text-slate-400 group-hover:text-slate-400 uppercase">1,650+ LEARNERS</span>
                                <span class="font-mono text-[11px] text-slate-400 group-hover:text-slate-400 uppercase">3 MONTHS</span>
                                <span class="font-heading text-lg font-extrabold text-[#FB6221] leading-none">₹25,000</span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-[#FB6221] transition-colors">arrow_forward</span>
                            </div>
                        </a>

                        <!-- SD -->
                        <a href="/#course-deck" class="group bg-white hover:bg-[#0A0A0A] rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <span class="font-heading text-xl font-bold text-[#0A0A0A] group-hover:text-white transition-colors">SAP SD</span>
                                <span class="font-sans text-xs text-slate-600 group-hover:text-slate-300 transition-colors">Sales &amp; Distribution, Order to Cash (OTC) Lifecycle</span>
                            </div>
                            <div class="flex items-center gap-6">
                                <span class="font-mono text-[11px] text-slate-400 group-hover:text-slate-400 uppercase">1,057+ LEARNERS</span>
                                <span class="font-mono text-[11px] text-slate-400 group-hover:text-slate-400 uppercase">3 MONTHS</span>
                                <span class="font-heading text-lg font-extrabold text-[#FB6221] leading-none">₹25,000</span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-[#FB6221] transition-colors">arrow_forward</span>
                            </div>
                        </a>

                        <!-- PP -->
                        <a href="/#course-deck" class="group bg-white hover:bg-[#0A0A0A] rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-200">
                            <div class="flex items-center gap-4">
                                <span class="font-heading text-xl font-bold text-[#0A0A0A] group-hover:text-white transition-colors">SAP PP</span>
                                <span class="font-sans text-xs text-slate-600 group-hover:text-slate-300 transition-colors">Production Planning, Bill of Materials (BOM) &amp; Routing</span>
                            </div>
                            <div class="flex items-center gap-6">
                                <span class="font-mono text-[11px] text-slate-400 group-hover:text-slate-400 uppercase">974+ LEARNERS</span>
                                <span class="font-mono text-[11px] text-slate-400 group-hover:text-slate-400 uppercase">3 MONTHS</span>
                                <span class="font-heading text-lg font-extrabold text-[#FB6221] leading-none">₹25,000</span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-[#FB6221] transition-colors">arrow_forward</span>
                            </div>
                        </a>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN: STICKY ENROLMENT CARD (4 cols) -->
            <div class="lg:col-span-4 lg:sticky lg:top-28">
                <div class="bg-[#0A0A0A] text-white rounded-2xl p-6 sm:p-8 border border-slate-800 shadow-xl relative overflow-hidden">
                    <!-- Accent Line -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-[#FB6221]"></div>

                    <div class="flex items-center justify-between mb-4">
                        <span class="font-mono text-[10px] font-bold uppercase text-[#FB6221] tracking-widest">ENROLMENT OPEN</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-white font-mono text-[10px] uppercase">LIVE ONLINE</span>
                    </div>

                    <!-- Tuition Display -->
                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="font-heading text-4xl sm:text-5xl font-extrabold leading-none text-white tracking-tight">₹25,000</span>
                            <span class="font-mono text-xs uppercase text-slate-400 font-bold">+ GST</span>
                        </div>
                        <p class="font-sans text-xs text-slate-400 mt-1">3-month live online course. GST extra.</p>
                    </div>

                    <!-- Value Breakdown Rows -->
                    <div class="divide-y divide-white/10 mb-6 font-['Inter'] text-xs">
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Next batch:</span>
                            <span class="font-bold text-white">Starts soon</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Format:</span>
                            <span class="font-bold text-white">Live online</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Practice:</span>
                            <span class="font-bold text-white">Real SAP system</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Support:</span>
                            <span class="font-bold text-white">Mock interviews &amp; placement</span>
                        </div>
                    </div>

                    <!-- CTA Actions -->
                    <div class="space-y-2.5 mb-6">
                        <a href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo%20for%20SAP%20FICO." target="_blank" rel="noopener" class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                            <span>Book a Free Demo</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>
                        <a href="tel:+918506888288" class="w-full py-3 px-4 border border-white/20 hover:bg-white/10 text-white font-['Inter'] text-xs font-semibold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 text-center">
                            <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                            <span>Get a Call Back</span>
                        </a>
                        <a href="https://wa.me/917277877778" target="_blank" rel="noopener" class="w-full py-2.5 px-4 flex items-center justify-center gap-2 text-slate-300 hover:text-white font-['Inter'] text-xs transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Chat with us on WhatsApp</span>
                        </a>
                    </div>

                    <!-- Reassurance Stamp -->
                    <div class="pt-4 border-t border-white/10 text-center">
                        <p class="font-mono text-[10px] text-slate-500 uppercase">
                            Refund policy applies. Please read terms before enrolling.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- FINAL CALL TO ACTION -->
    <section class="w-full bg-white border-t border-slate-200/80 py-16 sm:py-20 px-4 sm:px-6 lg:px-12 text-center">
        <div class="max-w-3xl mx-auto flex flex-col items-center">
            <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#FB6221] mb-2">
                09 // FREE DEMO CLASS
            </span>
            <h2 class="font-heading text-4xl sm:text-6xl uppercase text-[#0A0A0A] font-extrabold tracking-tight leading-tight mb-4">
                TRY ONE CLASS FIRST
            </h2>
            <p class="font-sans text-base text-slate-600 mb-8 max-w-xl leading-relaxed">
                Experience a live class before you decide. Speak with our team to find the right SAP module for your career.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo%20for%20SAP%20FICO." target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider shadow-md hover:shadow-lg transition-all hover:scale-[1.02]">
                    <span>Book a Free Demo</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
                <a href="tel:+918506888288" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-slate-100 hover:bg-slate-200 text-[#0A0A0A] font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>+91 8506 888 288</span>
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
