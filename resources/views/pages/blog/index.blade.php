@extends('layouts.app')

@section('title', 'SAP Interview Questions, Notes and Career Guides | ZebLearn Blog')
@section('description', 'Explore SAP interview questions, answers, study notes and career guides across SAP FICO, MM, SD, PP, EWM and Basis from experienced mentors at ZebLearn.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "ZebLearn SAP Blog",
  "description": "SAP interview questions, answers and career guides from ZebLearn.",
  "url": "https://sap.zeblearn.com/blogs",
  "publisher": {
    "@type": "EducationalOrganization",
    "name": "ZebLearn",
    "url": "https://sap.zeblearn.com"
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
      "name": "Blog",
      "item": "https://sap.zeblearn.com/blogs"
    }
  ]
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <!-- 1. HEADER SECTION -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-28 sm:pt-32 pb-8 sm:pb-12">
        <!-- Breadcrumb & Telemetry -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200 animate-hero-badge">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="/" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">Blog</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">KNOWLEDGE BASE &amp; INTERVIEW NOTES</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end">
            <div class="lg:col-span-8">
                <h1 class="font-heading text-5xl sm:text-7xl md:text-8xl lg:text-[96px] font-black uppercase tracking-tight leading-[0.95] text-[#0A0A0A] select-none animate-hero-title">
                    SAP NOTES.<br/>
                    <span class="text-[#FB6221]">INTERVIEW PREP.</span>
                </h1>
            </div>
            <div class="lg:col-span-4 flex flex-col justify-end animate-hero-desc">
                <div class="border-l-2 border-[#FB6221] pl-4">
                    <p class="font-sans text-sm sm:text-base text-slate-600 leading-relaxed font-medium">
                        Real-world SAP interview questions, answers, architectural study notes, and career guidance written by practicing consultants at ZebLearn.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. STICKY FLOATING FILTER & SEARCH BAR -->
    <section class="sticky top-24 z-30 max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 my-2 w-full" data-reveal>
        <div class="bg-white/95 backdrop-blur-md p-2 rounded-2xl border border-slate-200/80 shadow-md flex flex-col md:flex-row items-center justify-between gap-3 transition-all">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto scrollbar-none py-1 px-1">
                <button type="button" class="filter-btn active shrink-0 px-4 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider bg-[#0A0A0A] text-white hover:bg-black transition-all">
                    All
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    SAP Basis
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    FICO
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    MM
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    SD
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    PP
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    EWM
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    Interview Q&amp;A
                </button>
                <button type="button" class="filter-btn shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100 transition-all">
                    Career
                </button>
            </div>
            <!-- Search Bar -->
            <div class="relative w-full md:w-72 shrink-0">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                <input class="w-full bg-slate-50 text-[#0A0A0A] text-xs font-sans rounded-xl pl-9 pr-4 py-2 border border-slate-200 focus:border-[#FB6221] focus:bg-white focus:outline-none transition-all placeholder-slate-400" placeholder="Search SAP notes, t-codes, questions..." type="text"/>
            </div>
        </div>
    </section>

    <!-- 3. FEATURED POST (12-Col Bento Hero) -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 mt-6 w-full" data-reveal>
        <article class="relative group rounded-2xl overflow-hidden border border-slate-200/80 bg-white grid grid-cols-1 lg:grid-cols-12 shadow-sm hover:shadow-md transition-all duration-300 zeb-card-interactive">
            <a aria-label="Read article" class="absolute inset-0 z-10" href="/blogs/top-50-sap-ewm-interview-question-answer"></a>
            
            <!-- Left 7 cols: Editorial Visual Block -->
            <div class="lg:col-span-7 relative min-h-[300px] lg:min-h-[420px] bg-[#0A0A0A] p-8 sm:p-12 flex flex-col justify-between overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-tr from-black via-black/80 to-[#FB6221]/20"></div>
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FB6221] text-white text-[11px] font-mono font-bold tracking-wider uppercase shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                        Featured Post
                    </span>
                    <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-white font-mono text-[11px] uppercase border border-white/10">
                        SAP EWM
                    </span>
                </div>

                <div class="relative z-10">
                    <span class="font-heading font-extrabold text-7xl sm:text-8xl text-white/10 block leading-none select-none">
                        EWM
                    </span>
                    <p class="font-mono text-xs text-slate-300 uppercase tracking-widest mt-2">
                        50 COMPREHENSIVE ARCHITECTURAL &amp; SCENARIO QUESTIONS
                    </p>
                </div>
            </div>

            <!-- Right 5 cols: Editorial Details -->
            <div class="lg:col-span-5 bg-white p-6 sm:p-10 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-slate-200/80">
                <div class="space-y-4">
                    <span class="inline-block px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] font-mono text-[10px] font-bold tracking-widest uppercase">
                        INTERVIEW PREPARATION
                    </span>
                    <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold uppercase leading-tight tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
                        TOP 50 SAP EWM INTERVIEW QUESTION ANSWER
                    </h2>
                    <p class="font-sans text-sm text-slate-600 leading-relaxed">
                        Warehouse management questions, storage bin architecture, RF frameworks, handling units and inbound/outbound flows to prepare for SAP EWM interviews.
                    </p>
                </div>

                <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] font-heading font-bold text-sm flex items-center justify-center">
                            ZB
                        </div>
                        <div>
                            <span class="font-sans text-xs font-bold text-[#0A0A0A] block">ZebLearn Team</span>
                            <span class="font-mono text-[10px] text-slate-400 uppercase">SAP Practice Lead</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#FB6221] text-white flex items-center justify-center group-hover:scale-105 transition-transform shadow-xs">
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <!-- 4. ARTICLE BENTO GRID -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 mt-8 mb-16 w-full" data-reveal>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-6">

            <!-- POST 1: SAP Basis (4 cols) -->
            <article class="relative md:col-span-6 lg:col-span-4 rounded-2xl bg-white border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-[#FB6221]/50 hover:shadow-md group transition-all zeb-card-interactive" data-reveal data-reveal-delay="1">
                <a aria-label="Read article" class="absolute inset-0 z-10" href="https://sap.zeblearn.com/top-interview-question-answer-on-sap-basic/" rel="noopener" target="_blank"></a>
                <div>
                    <div class="w-full h-40 rounded-xl bg-slate-100 flex items-center justify-center relative mb-5 overflow-hidden">
                        <span class="font-heading font-extrabold text-6xl text-slate-200 select-none">BASIS</span>
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-white/90 text-[#0A0A0A] font-mono text-[10px] font-bold uppercase tracking-wider border border-slate-200">
                            SAP BASIS
                        </span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                        TOP INTERVIEW QUESTION ANSWER ON SAP BASIC
                    </h3>
                    <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed">
                        Common SAP Basis interview questions covering client copies, TMS, background jobs, kernel upgrades, and user management.
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase font-bold">READ ARTICLE</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-[#FB6221] group-hover:translate-x-1 transition-all">arrow_forward</span>
                </div>
            </article>

            <!-- POST 2: ECC + HANA (4 cols) -->
            <article class="relative md:col-span-6 lg:col-span-4 rounded-2xl bg-white border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs hover:border-[#FB6221]/50 hover:shadow-md group transition-all">
                <a aria-label="Read article" class="absolute inset-0 z-10" href="https://sap.zeblearn.com/top-50-question-answer-on-sap-ecchana-sap-power-user/" rel="noopener" target="_blank"></a>
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider">
                            CORE ERP
                        </span>
                        <span class="font-mono text-[11px] text-slate-400 uppercase">ECC · HANA</span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                        TOP 20 QUESTION ANSWER ON SAP ECC + HANA + SAP POWER USER
                    </h3>
                    <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed">
                        Frequently asked questions on SAP ECC, in-memory computing with HANA, and the official SAP Power User certification track.
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase font-bold">READ ARTICLE</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-[#FB6221] group-hover:translate-x-1 transition-all">arrow_forward</span>
                </div>
            </article>

            <!-- POST 3: System Copy (4 cols) -->
            <article class="md:col-span-12 lg:col-span-4 rounded-2xl bg-[#0A0A0A] text-white border border-slate-800 p-6 flex flex-col justify-between shadow-sm group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-white/10 text-white font-mono text-[10px] font-bold uppercase tracking-wider border border-white/10">
                            SAP BASIS
                        </span>
                        <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-white group-hover:text-[#FB6221] transition-colors leading-snug">
                        HOW SYSTEM COPY WORKS IN SAP
                    </h3>
                    <p class="font-sans text-xs text-slate-300 mt-2.5 leading-relaxed">
                        A practical explainer on homogeneous vs heterogeneous system copies in SAP, database backups, and post-copy steps (BDLS).
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-white/10 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-500 uppercase">COMING SOON</span>
                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">lock_clock</span>
                </div>
            </article>

            <!-- POST 4: FICO Month-End Closing (6 cols) -->
            <article class="md:col-span-12 lg:col-span-6 rounded-2xl bg-white border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs group">
                <div>
                    <span class="inline-block px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider mb-4">
                        FINANCE / FICO
                    </span>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                        FICO MONTH-END CLOSING, STEP BY STEP
                    </h3>
                    <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed">
                        A step-by-step explainer on posting period locks (OB52), GR/IR clearing (F.13), foreign currency revaluation (FAGL_FCV), and balance sheet version generation.
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase">COMING SOON</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                </div>
            </article>

            <!-- POST 5: MM vs SD (6 cols) -->
            <article class="md:col-span-12 lg:col-span-6 rounded-2xl bg-[#0A0A0A] text-white border border-slate-800 p-6 sm:p-8 flex flex-col justify-between shadow-sm relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-[#FB6221]/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-[#FB6221] text-white font-mono text-[10px] font-bold uppercase tracking-wider">
                            CAREER ROADMAP
                        </span>
                        <span class="font-mono text-[11px] text-[#FB6221] font-bold uppercase">MODULE COMPARISON</span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-white group-hover:text-[#FB6221] transition-colors leading-snug">
                        SAP MM VS SD: WHICH TO LEARN FIRST
                    </h3>
                    <p class="font-sans text-xs text-slate-300 mt-2.5 leading-relaxed">
                        A clear comparison between Materials Management (Procure to Pay) and Sales &amp; Distribution (Order to Cash) to help you choose the right starting point for your background.
                    </p>
                </div>
                <div class="relative z-10 pt-5 mt-5 border-t border-white/10 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase">COMING SOON</span>
                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">schedule</span>
                </div>
            </article>

            <!-- POST 6: First Interview Prep (4 cols) -->
            <article class="md:col-span-6 lg:col-span-4 rounded-2xl bg-white border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider">
                            INTERVIEW GUIDE
                        </span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                        HOW TO PREPARE FOR YOUR FIRST SAP INTERVIEW
                    </h3>
                    <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed">
                        How to articulate project configurations, handle scenario questions, explain business processes, and discuss integration touchpoints.
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase">COMING SOON</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                </div>
            </article>

            <!-- POST 7: Commerce Careers (4 cols) -->
            <article class="md:col-span-6 lg:col-span-4 rounded-2xl bg-white border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider">
                            CAREER
                        </span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                        IS SAP A GOOD CAREER FOR COMMERCE GRADUATES
                    </h3>
                    <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed">
                        How B.Com, M.Com, MBA Finance, and CA/CMA aspirants can transition into functional consulting roles with high salary trajectories.
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase">COMING SOON</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                </div>
            </article>

            <!-- POST 8: SAP SD Q&A (4 cols) -->
            <article class="md:col-span-12 lg:col-span-4 rounded-2xl bg-white border border-slate-200/80 p-6 flex flex-col justify-between shadow-xs group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider">
                            SAP SD
                        </span>
                    </div>
                    <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                        TOP SAP SD INTERVIEW QUESTION ANSWER
                    </h3>
                    <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed">
                        Enterprise structure in SD, partner determination, pricing condition techniques (VK11), and billing document types.
                    </p>
                </div>
                <div class="pt-5 mt-5 border-t border-slate-100 flex items-center justify-between font-mono text-[11px]">
                    <span class="text-slate-400 uppercase">COMING SOON</span>
                    <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                </div>
            </article>

        </div>
    </section>

    <!-- 5. CALL TO ACTION BANNER -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 mb-20 w-full">
        <div class="rounded-2xl bg-[#0A0A0A] text-white p-8 sm:p-12 border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="absolute right-0 top-0 w-80 h-80 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white font-mono text-[10px] font-bold tracking-widest uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                        FREE CAREER COUNSELING
                    </div>
                    <h2 class="font-heading text-2xl sm:text-4xl lg:text-5xl font-extrabold uppercase leading-tight tracking-tight text-white">
                        NOT SURE WHERE TO START?
                    </h2>
                    <p class="font-sans text-sm sm:text-base text-slate-300 max-w-xl leading-relaxed">
                        Speak directly with our senior mentors to map your background to the most in-demand SAP module and book a live trial session.
                    </p>
                </div>
                <div class="lg:col-span-5 flex flex-col sm:flex-row gap-3 justify-end">
                    <a class="px-6 py-3.5 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-all text-center shadow-md flex items-center justify-center gap-2" href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo." rel="noopener" target="_blank">
                        <span>Book a Free Demo</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                    <a class="px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white font-sans text-xs font-semibold uppercase tracking-wider transition-all text-center" href="tel:+918506888288">
                        Call +91 85068 88288
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-[#0A0A0A]', 'text-white', 'hover:bg-black');
            b.classList.add('text-slate-600', 'hover:text-[#0A0A0A]', 'hover:bg-slate-100');
        });
        this.classList.remove('text-slate-600', 'hover:text-[#0A0A0A]', 'hover:bg-slate-100');
        this.classList.add('bg-[#0A0A0A]', 'text-white', 'hover:bg-black');
    });
});
</script>
@endpush
