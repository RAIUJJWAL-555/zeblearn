@extends('layouts.app')

@section('title', 'SAP FICO Course Online with Live Training and Placement Support | ZebLearn')
@section('description', 'Learn SAP FICO online through live classes, practical SAP system sessions, assignments and interview preparation. Explore the 3-month training program at ZebLearn.')
@section('og_type', 'website')


@push('schema')
@verbatim
<script type="application/ld+json">{"@context":"https://schema.org","@type":"Course","name":"SAP FICO Course Online","description":"Live online SAP FICO training covering financial accounting and controlling, with real system practice and placement support.","provider":{"@type":"EducationalOrganization","name":"ZebLearn","url":"https://sap.zeblearn.com"},"offers":{"@type":"Offer","price":"25000","priceCurrency":"INR"},"hasCourseInstance":{"@type":"CourseInstance","courseMode":"online","courseWorkload":"P3M"}}</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full">
<!-- Radial Glow Ambiance Behind Canvas -->
<div class="relative w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin pb-space-3xl overflow-hidden">
<div class="pointer-events-none absolute -top-40 left-1/2 -translate-x-1/2 w-[850px] h-[550px] bg-gradient-to-b from-[#FB6221]/15 via-white/40 to-transparent blur-3xl -z-10 rounded-full"></div>
<!-- 1. HERO SECTION -->
<section class="pt-24 md:pt-28 pb-space-2xl">
<!-- Breadcrumb -->
<nav class="flex items-center gap-space-xs text-body-md font-body-md text-on-surface-variant mb-space-lg">
<a class="hover:text-ink-primary transition-colors" href="/">Home</a>
<span class="text-outline">/</span>
<a class="hover:text-ink-primary transition-colors" href="/#course-deck">Courses</a>
<span class="text-outline">/</span>
<span class="text-ink-primary font-semibold">SAP FICO</span>
</nav>
<!-- Edge-to-Edge Architectural Display Heading -->
<div class="w-full mb-space-md">
<h1 class="font-display-xl text-display-xl text-ink-primary tracking-tighter leading-[0.88] select-none uppercase">
          SAP FICO
        </h1>
</div>
<!-- Split Hero Subtitle & Visual Bento Tile -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center mb-space-xl">
<div class="lg:col-span-7">
<p class="font-headline-lg text-headline-lg uppercase text-ink-primary leading-tight max-w-2xl">
            Financial Accounting and Controlling, from first principles to interview-ready.
          </p>
<p class="font-body-lg text-body-lg text-on-surface-variant mt-space-md max-w-xl">
            Live online SAP FICO training with real system practice, assignments and interview preparation.
          </p>
</div>
<div class="lg:col-span-5 flex justify-end">
<!-- Vivid Orange Bento Tile with 3D Render -->
<div class="relative w-full max-w-md h-48 sm:h-52 bg-[#FB6221] rounded-[24px] p-6 shadow-xl flex items-center justify-between overflow-hidden group">
<div class="z-10 flex flex-col justify-between h-full">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-white/90">LIVE ONLINE</span>
<button class="inline-flex items-center gap-space-xs px-space-md py-space-sm bg-white text-ink-primary font-body-md text-body-md font-semibold rounded-full shadow-md hover:scale-105 transition-all" type="button">
<span class="material-symbols-outlined text-[18px] text-[#FB6221]" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
<span class="">Watch demo</span>
</button>
</div>
<!-- Render Crop -->
<div class="absolute -right-6 -bottom-6 w-52 h-52 group-hover:scale-105 transition-transform duration-500">

</div>
</div>
</div>
</div>
<!-- Meta Chips Row -->
<div class="flex flex-wrap items-center gap-space-sm pt-space-xs">
<div class="flex items-center gap-2 px-space-md py-space-xs bg-surface-white rounded-full shadow-sm text-ink-primary font-body-md text-body-md font-medium">
<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
<span class="">3 months</span>
</div>
<div class="flex items-center gap-2 px-space-md py-space-xs bg-surface-white rounded-full shadow-sm text-ink-primary font-body-md text-body-md font-medium">
<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
<span class="">Live online</span>
</div>
<div class="flex items-center gap-2 px-space-md py-space-xs bg-surface-white rounded-full shadow-sm text-ink-primary font-body-md text-body-md font-medium">
<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
<span class="">1,850+ learners</span>
</div>
<div class="flex items-center gap-2 px-space-md py-space-xs bg-surface-white rounded-full shadow-sm text-ink-primary font-body-md text-body-md font-medium">
<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
<span class="">Placement support</span>
</div>
</div>
</section>
<!-- 2. TWO-COLUMN GRID: MAIN CONTENT (8 Cols) vs ENROLMENT SIDEBAR (4 Cols) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">
<!-- LEFT COLUMN (8 Columns) -->
<div class="lg:col-span-8 flex flex-col gap-space-2xl">
<!-- A. OVERVIEW BENTO -->
<section class="grid grid-cols-1 md:grid-cols-12 gap-gutter">
<!-- Card 1: Large White Card -->
<div class="md:col-span-7 bg-surface-white rounded-[24px] p-8 shadow-sm flex flex-col justify-between">
<div>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221] mb-space-sm block">• OVERVIEW</span>
<h2 class="font-headline-sm text-headline-sm uppercase text-ink-primary mb-space-md leading-snug">
                Learn SAP financial accounting and controlling on a real SAP system.
              </h2>
<div class="space-y-space-md font-body-md text-body-md text-on-surface-variant">
<p class="">
                  Many accountants know journal entries but have not worked with the enterprise structure, fiscal year variants or field status groups that decide how SAP posts a transaction. This course covers exactly that.
                </p>
<p class="">
                  At ZebLearn, you work through General Ledger, Accounts Payable and Receivable, Asset Accounting and Controlling in live classes with hands-on practice.
                </p>
</div>
</div>
<div class="pt-space-lg mt-space-md border-t border-hairline-border flex items-center justify-between text-ink-muted font-label-mono text-label-mono uppercase">
<span class="">DURATION: 3 MONTHS</span>
<span class="">MODE: LIVE ONLINE</span>
</div>
</div>
<!-- Card 2: Vivid Orange Card -->
<div class="md:col-span-5 bg-[#FB6221] rounded-[24px] p-8 shadow-md flex flex-col justify-between text-white">
<div>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-black/80 mb-space-sm block">• ADMISSIONS PROFILE</span>
<h3 class="font-headline-lg text-headline-lg uppercase text-white mb-space-md">WHO IS IT FOR?</h3>
<ul class="space-y-space-sm">
<li class="bg-white/10 backdrop-blur-sm p-3 rounded-xl flex items-center gap-space-sm">
<span class="w-2 h-2 rounded-full bg-white shrink-0"></span>
<span class="font-body-md text-body-md font-medium text-white">Commerce &amp; B.Com graduates</span>
</li>
<li class="bg-white/10 backdrop-blur-sm p-3 rounded-xl flex items-center gap-space-sm">
<span class="w-2 h-2 rounded-full bg-white shrink-0"></span>
<span class="font-body-md text-body-md font-medium text-white">Corporate accountants &amp; Tally users</span>
</li>
<li class="bg-white/10 backdrop-blur-sm p-3 rounded-xl flex items-center gap-space-sm">
<span class="w-2 h-2 rounded-full bg-white shrink-0"></span>
<span class="font-body-md text-body-md font-medium text-white">CA / CMA / CS aspirants</span>
</li>
<li class="bg-white/10 backdrop-blur-sm p-3 rounded-xl flex items-center gap-space-sm">
<span class="w-2 h-2 rounded-full bg-white shrink-0"></span>
<span class="font-body-md text-body-md font-medium text-white">Consulting career switchers</span>
</li>
</ul>
</div>
<p class="font-label-mono text-label-mono text-black uppercase tracking-wider mt-space-lg font-semibold">
              PRE-REQUISITE: BASIC ACCOUNTING INTUITION
            </p>
</div>
</section>
<!-- B. WHAT YOU'LL LEARN: 2x3 TACTILE BENTO TILES -->
<section>
<div class="flex items-center justify-between mb-space-lg">
<div>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221]">• LEARNING OUTCOMES</span>
<h2 class="font-headline-xl text-headline-xl uppercase text-ink-primary tracking-tight">WHAT YOU'LL LEARN</h2>
</div>
<span class="font-label-mono text-label-mono text-ink-muted uppercase">6 CORE MODULE PHASES</span>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
<!-- 01: White Tile -->
<div class="bg-surface-white rounded-[24px] p-6 shadow-sm flex flex-col justify-between hover:-translate-y-1 transition-transform">
<span class="font-headline-xl text-headline-xl text-[#FB6221] leading-none mb-4">01</span>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-ink-primary mb-2">Enterprise Structure</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Company code setup, business areas, and chart of accounts hierarchy configuration.</p>
</div>
</div>
<!-- 02: Soft Gray Tile -->
<div class="bg-[#D3D6DA] rounded-[24px] p-6 shadow-sm flex flex-col justify-between hover:-translate-y-1 transition-transform">
<span class="font-headline-xl text-headline-xl text-ink-primary leading-none mb-4">02</span>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-ink-primary mb-2">Universal Journal</h4>
<p class="font-body-md text-body-md text-on-surface-variant">FI-GL architecture, posting periods, field status variants, and ACDOCA single source of truth.</p>
</div>
</div>
<!-- 03: Vivid Orange Tile -->
<div class="bg-[#FB6221] text-white rounded-[24px] p-6 shadow-md flex flex-col justify-between hover:-translate-y-1 transition-transform">
<span class="font-headline-xl text-headline-xl text-white leading-none mb-4">03</span>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-white mb-2">AP &amp; AR Automation</h4>
<p class="font-body-md text-body-md text-black font-medium">Vendor &amp; customer subledgers, automatic payment program (F110), and dunning parameters.</p>
</div>
</div>
<!-- 04: Pitch Black Tile -->
<div class="bg-surface-dark text-white rounded-[24px] p-6 shadow-md flex flex-col justify-between hover:-translate-y-1 transition-transform">
<span class="font-headline-xl text-headline-xl text-[#FB6221] leading-none mb-4">04</span>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-white mb-2">Asset Accounting (FI-AA)</h4>
<p class="font-body-md text-body-md text-outline-variant">Asset classes, depreciation key logic, asset acquisitions, retirement &amp; year-end closing.</p>
</div>
</div>
<!-- 05: White Tile -->
<div class="bg-surface-white rounded-[24px] p-6 shadow-sm flex flex-col justify-between hover:-translate-y-1 transition-transform">
<span class="font-headline-xl text-headline-xl text-[#FB6221] leading-none mb-4">05</span>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-ink-primary mb-2">Controlling (CO Core)</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Cost Center Accounting (CCA), Profit Centers (PCA), and Internal Orders cost allocations.</p>
</div>
</div>
<!-- 06: Soft Gray Tile -->
<div class="bg-[#D3D6DA] rounded-[24px] p-6 shadow-sm flex flex-col justify-between hover:-translate-y-1 transition-transform">
<span class="font-headline-xl text-headline-xl text-ink-primary leading-none mb-4">06</span>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-ink-primary mb-2">Financial Closing</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Foreign currency revaluation, balance sheet version creation, and S/4HANA migration frameworks.</p>
</div>
</div>
</div>
</section>
<!-- C. SYLLABUS ACCORDION -->
<section class="bg-surface-white rounded-[24px] p-8 shadow-sm">
<div class="flex items-center justify-between pb-space-lg border-b border-hairline-border">
<div>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221]">• CURRICULUM</span>
<h2 class="font-headline-xl text-headline-xl uppercase text-ink-primary">SYLLABUS</h2>
</div>
<span class="font-label-mono text-label-mono uppercase px-space-md py-space-xs bg-canvas-base rounded-full font-semibold text-ink-primary">
              6 MODULES · 3 MONTHS
            </span>
</div>
<div class="divide-y divide-hairline-border">
<!-- Module 01 (Expanded by default) -->
<div class="py-space-lg">
<div class="flex items-center justify-between cursor-pointer">
<div class="flex items-center gap-space-md">
<span class="font-label-mono text-label-mono text-[#FB6221] font-bold">01</span>
<span class="font-headline-sm text-headline-sm uppercase text-ink-primary">
                    Enterprise Structure &amp; General Ledger Setup
                  </span>
</div>
<div class="flex items-center gap-space-md">
<span class="hidden sm:inline-block font-label-mono text-label-mono uppercase text-ink-muted bg-surface-container px-3 py-1 rounded-full">
                    MODULE 01
                  </span>
<div class="w-8 h-8 rounded-full bg-[#FB6221] text-white flex items-center justify-center">
<span class="material-symbols-outlined text-[18px]">remove</span>
</div>
</div>
</div>
<!-- Content Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter pt-space-lg text-body-md text-on-surface-variant">
<div class="space-y-2">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#FB6221]">check_circle</span>
<span class="">Company code, Credit Control, and Business Area definition</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#FB6221]">check_circle</span>
<span class="">Fiscal Year Variants (K4, V3) &amp; Posting Period variants</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#FB6221]">check_circle</span>
<span class="">Field status variant &amp; assignment to general ledger master</span>
</div>
</div>
<div class="space-y-2">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#FB6221]">check_circle</span>
<span class="">Document splitting rules &amp; Real-time zero-balance clearing</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#FB6221]">check_circle</span>
<span class="">Foreign currency translation and valuation configurations</span>
</div>
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-[16px] text-[#FB6221]">check_circle</span>
<span class="">ACDOCA table mapping and Universal Journal reconciliation</span>
</div>
</div>
</div>
</div>
<!-- Module 02 (Collapsed) -->
<div class="py-space-md flex items-center justify-between group cursor-pointer hover:bg-canvas-glow/50 px-2 rounded-xl transition-colors">
<div class="flex items-center gap-space-md">
<span class="font-label-mono text-label-mono text-ink-muted">02</span>
<span class="font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors">
                  Accounts Payable &amp; Automatic Payment Run (F110)
                </span>
</div>
<div class="flex items-center gap-space-md">
<span class="hidden sm:inline-block font-label-mono text-label-mono uppercase text-ink-muted"></span>
<div class="w-8 h-8 rounded-full bg-canvas-base flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[18px]">add</span>
</div>
</div>
</div>
<!-- Module 03 (Collapsed) -->
<div class="py-space-md flex items-center justify-between group cursor-pointer hover:bg-canvas-glow/50 px-2 rounded-xl transition-colors">
<div class="flex items-center gap-space-md">
<span class="font-label-mono text-label-mono text-ink-muted">03</span>
<span class="font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors">
                  Accounts Receivable, Dunning &amp; Customer Statements
                </span>
</div>
<div class="flex items-center gap-space-md">
<span class="hidden sm:inline-block font-label-mono text-label-mono uppercase text-ink-muted"></span>
<div class="w-8 h-8 rounded-full bg-canvas-base flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[18px]">add</span>
</div>
</div>
</div>
<!-- Module 04 (Collapsed) -->
<div class="py-space-md flex items-center justify-between group cursor-pointer hover:bg-canvas-glow/50 px-2 rounded-xl transition-colors">
<div class="flex items-center gap-space-md">
<span class="font-label-mono text-label-mono text-ink-muted">04</span>
<span class="font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors">
                  Fixed Asset Accounting &amp; Depreciation Runs
                </span>
</div>
<div class="flex items-center gap-space-md">
<span class="hidden sm:inline-block font-label-mono text-label-mono uppercase text-ink-muted"></span>
<div class="w-8 h-8 rounded-full bg-canvas-base flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[18px]">add</span>
</div>
</div>
</div>
<!-- Module 05 (Collapsed) -->
<div class="py-space-md flex items-center justify-between group cursor-pointer hover:bg-canvas-glow/50 px-2 rounded-xl transition-colors">
<div class="flex items-center gap-space-md">
<span class="font-label-mono text-label-mono text-ink-muted">05</span>
<span class="font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors">
                  Controlling (CO-CCA, CO-PCA &amp; CO-IO)
                </span>
</div>
<div class="flex items-center gap-space-md">
<span class="hidden sm:inline-block font-label-mono text-label-mono uppercase text-ink-muted"></span>
<div class="w-8 h-8 rounded-full bg-canvas-base flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[18px]">add</span>
</div>
</div>
</div>
<!-- Module 06 (Collapsed) -->
<div class="py-space-md flex items-center justify-between group cursor-pointer hover:bg-canvas-glow/50 px-2 rounded-xl transition-colors">
<div class="flex items-center gap-space-md">
<span class="font-label-mono text-label-mono text-ink-muted">06</span>
<span class="font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors">
                  Integration with MM/SD &amp; S/4HANA Migration Blueprint
                </span>
</div>
<div class="flex items-center gap-space-md">
<span class="hidden sm:inline-block font-label-mono text-label-mono uppercase text-ink-muted"></span>
<div class="w-8 h-8 rounded-full bg-canvas-base flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[18px]">add</span>
</div>
</div>
</div>
</div>
</section>
<!-- D. TOOLS & INFRASTRUCTURE STRIP -->
<section class="bg-[#D3D6DA] rounded-[24px] p-6 shadow-sm">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-ink-primary font-bold">
              • TOOLS &amp; PRACTICE
            </span>
<span class="font-label-mono text-label-mono text-ink-muted uppercase">REAL SAP SYSTEM PRACTICE</span>
</div>
<div class="flex flex-wrap gap-2">
<span class="px-4 py-2 bg-surface-dark text-white rounded-full font-label-mono text-label-mono font-medium">SAP ECC 6.0</span>
<span class="px-4 py-2 bg-surface-dark text-white rounded-full font-label-mono text-label-mono font-medium">SAP S/4HANA</span>
<span class="px-4 py-2 bg-surface-white text-ink-primary rounded-full font-label-mono text-label-mono font-semibold">Practice Server Access</span>
<span class="px-4 py-2 bg-surface-white text-ink-primary rounded-full font-label-mono text-label-mono font-semibold">Real-world Case Assignments</span>
<span class="px-4 py-2 bg-surface-dark text-white rounded-full font-label-mono text-label-mono font-medium">Interview Preparation</span>
</div>
</section>
<!-- E. LEAD MENTOR CARD -->
<section class="bg-surface-white rounded-[24px] p-8 shadow-sm">
<div class="grid grid-cols-1 md:grid-cols-12 gap-gutter items-center">
<div class="md:col-span-4 relative">
<div class="aspect-square rounded-2xl overflow-hidden shadow-md">

</div>
<div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-max px-3 py-1 bg-[#FB6221] text-white rounded-full font-label-mono text-label-mono uppercase font-semibold shadow-md whitespace-nowrap">
                ZebLearn SAP Trainer
              </div>
</div>
<div class="md:col-span-8 pl-0 md:pl-space-md pt-space-lg md:pt-0">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221] mb-2 block">• YOUR TRAINER</span>
<h3 class="font-headline-sm text-headline-sm uppercase text-ink-primary leading-tight mb-space-sm">
                "WE DON'T JUST RUN SLIDES. YOU PRACTISE ON A REAL SAP SYSTEM FROM THE START."
              </h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
                Live classes led by ZebLearn's SAP trainers, with doubts solved as you practise.
              </p>
<div class="flex items-center gap-space-lg text-ink-primary font-label-mono text-label-mono uppercase">
<div>
<span class="font-headline-sm text-headline-sm text-[#FB6221] block leading-none">1,850+</span>
<span class="text-ink-muted">Learners Trained</span>
</div>
<div>
<span class="font-headline-sm text-headline-sm text-[#FB6221] block leading-none">3 Months</span>
<span class="text-ink-muted">Live Online</span>
</div>
<div>
<span class="font-headline-sm text-headline-sm text-[#FB6221] block leading-none">₹25,000</span>
<span class="text-ink-muted">+ GST</span>
</div>
</div>
</div>
</div>
</section>
</div>
<!-- RIGHT COLUMN: STICKY ENROLMENT CARD (4 Columns) -->
<div class="lg:col-span-4 sticky top-24">
<div class="bg-surface-dark text-white rounded-[24px] p-8 shadow-2xl relative overflow-hidden">
<!-- Subtle Accent Line -->
<div class="absolute top-0 left-0 right-0 h-1.5 bg-[#FB6221]"></div>
<div class="flex items-center justify-between mb-space-md">
<span class="font-label-mono text-label-mono uppercase text-[#FB6221] tracking-widest">ENROLMENT OPEN</span>
<span class="px-2.5 py-0.5 rounded-full bg-white/10 text-white font-label-mono text-label-mono uppercase">LIVE ONLINE</span>
</div>
<!-- Tuition Display -->
<div class="mb-space-lg">
<div class="flex items-baseline gap-2">
<span class="font-display-lg text-display-lg leading-none tracking-tight text-white">₹25,000</span>
<span class="font-label-mono text-label-mono uppercase text-outline-variant font-semibold">+ GST</span>
</div>
<p class="font-body-md text-body-md text-outline-variant mt-1">3-month live online course. GST extra.</p>
</div>
<!-- Value Rows -->
<div class="divide-y divide-hairline-dark mb-space-lg font-body-md text-body-md">
<div class="py-space-sm flex items-center justify-between">
<span class="text-outline-variant">Next batch:</span>
<span class="font-medium text-white">Starts soon</span>
</div>
<div class="py-space-sm flex items-center justify-between">
<span class="text-outline-variant">Format:</span>
<span class="font-medium text-white">Live online</span>
</div>
<div class="py-space-sm flex items-center justify-between">
<span class="text-outline-variant">Practice:</span>
<span class="font-medium text-white">Real SAP system</span>
</div>
<div class="py-space-sm flex items-center justify-between">
<span class="text-outline-variant">Support:</span>
<span class="font-medium text-white">Mock interviews &amp; placement</span>
</div>
</div>
<!-- CTA Actions -->
<div class="space-y-space-sm mb-space-lg">
<button class="w-full py-4 px-6 bg-[#FB6221] hover:brightness-110 active:scale-[0.99] text-white font-body-lg text-body-lg font-bold rounded-full transition-all shadow-[0_8px_24px_rgba(251,98,33,0.35)] flex items-center justify-center gap-2" onclick="window.open('https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo%20for%20SAP%20FICO.','_blank')" type="button">
<span class="">Book a free demo</span>
<span class="material-symbols-outlined text-[20px]">arrow_forward</span>
</button>
<button class="w-full py-3.5 px-6 border border-white/20 hover:bg-white/10 text-white font-body-md text-body-md font-semibold rounded-full transition-all text-center" onclick="location.href='tel:+918506888288'" type="button">
              Get a call back from our team
            </button>
<a class="w-full py-2.5 px-4 flex items-center justify-center gap-2 text-white/80 hover:text-white font-body-md text-body-md transition-colors" href="https://wa.me/917277877778" target="_blank">
<span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
<span class="">Chat with us on WhatsApp</span>
</a>
</div>
<!-- Reassurance Stamp -->
<div class="pt-space-md border-t border-hairline-dark text-center">
<p class="font-label-mono text-label-mono text-outline-variant">
              Refund policy applies. Please read the terms before enrolling.
            </p>
</div>
</div>
</div>
</div>
<!-- 5. POWER USER PROMO BLOCK (Full-Width 12 Cols) -->
<section class="mt-space-3xl bg-surface-dark text-white rounded-[24px] p-8 md:p-12 shadow-xl relative overflow-hidden">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">
<div class="lg:col-span-8 space-y-space-md">
<div class="inline-flex items-center gap-2 px-space-md py-space-xs bg-white/10 rounded-full font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221]">
<span class="">NEW ADDITION</span>
</div>
<h2 class="font-headline-xl text-headline-xl uppercase text-white tracking-tight leading-none">
            POWER USER CERTIFICATION BY SAP
          </h2>
<p class="font-body-xl text-body-xl text-outline-variant max-w-xl">
            80 hours of self-paced training by SAP, 3 months access, ₹40,000 + GST. Available for FICO, MM, SD and PP.
          </p>
<div class="flex flex-wrap items-center gap-2 pt-space-xs">
<span class="px-4 py-1.5 bg-white/5 border border-hairline-dark rounded-full font-label-mono text-label-mono uppercase">SAP FICO</span>
<span class="px-4 py-1.5 bg-white/5 border border-hairline-dark rounded-full font-label-mono text-label-mono uppercase">SAP MM</span>
<span class="px-4 py-1.5 bg-white/5 border border-hairline-dark rounded-full font-label-mono text-label-mono uppercase">SAP SD</span>
<span class="px-4 py-1.5 bg-white/5 border border-hairline-dark rounded-full font-label-mono text-label-mono uppercase">SAP PP</span>
</div>
<div class="pt-space-md">
<button class="px-8 py-3.5 bg-[#FB6221] hover:brightness-110 text-white font-body-md text-body-md font-bold rounded-full transition-all" onclick="window.open('https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo%20for%20SAP%20FICO.','_blank')" type="button">
              View details
            </button>
</div>
</div>
<div class="lg:col-span-4 flex justify-center lg:justify-end">
<div class="w-64 h-64 relative group">

</div>
</div>
</div>
</section>
<!-- 6. CAREER OUTCOMES & PLACEMENTS (12 Cols Split Card) -->
<section class="mt-space-2xl">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-stretch">
<!-- Left: 4 Cols Soft Gray Card -->
<div class="lg:col-span-4 bg-[#D3D6DA] rounded-[24px] p-8 shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center gap-2 font-label-mono text-label-mono uppercase text-ink-muted mb-space-sm">
<span class="material-symbols-outlined text-[18px] text-[#FB6221]">domain</span>
<span class="">PLACEMENT PARTNER</span>
</div>
<h3 class="font-headline-lg text-headline-lg uppercase text-ink-primary leading-tight mb-space-md">
              SAP JOBS, ONE CLICK AWAY
            </h3>
<div class="space-y-space-md">
<div>
<span class="font-display-lg text-display-lg text-ink-primary block leading-none">SAP JOBS</span>
<span class="font-body-md text-body-md text-on-surface-variant font-medium">Browse SAP job openings on our placement partner PlacementBaba.</span>
</div>
</div>
</div>
<div class="pt-24 md:pt-28">
<button class="w-full py-3.5 bg-surface-dark hover:bg-black text-white font-body-md text-body-md font-semibold rounded-full transition-all flex items-center justify-center gap-2" onclick="window.open('https://www.placementbaba.com/','_blank')" type="button">
<span class="">View SAP jobs</span>
<span class="material-symbols-outlined text-[18px]">arrow_forward</span>
</button>
</div>
</div>
<!-- Right: 8 Cols Crisp White Card -->
<div class="lg:col-span-8 bg-surface-white rounded-[24px] p-8 shadow-sm flex flex-col justify-between">
<div>
<div class="flex items-center justify-between mb-space-md">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221]">• STUDENT STORY</span>
<div class="flex items-center gap-1 text-[#FB6221]">
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
</div>
</div>
<p class="font-headline-sm text-headline-sm uppercase text-ink-primary leading-snug mb-space-lg">
              "IT IS DUE TO THE QUALITY TRAINING PROVIDED HERE THAT I GOT PLACED IN ACCENTURE. THE ATMOSPHERE IS BRILLIANT AND EVERYONE IS STUDYING HARD."
            </p>
</div>
<div class="pt-space-md border-t border-hairline-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
<div class="flex items-center gap-space-md">
<div class="w-12 h-12 rounded-full bg-canvas-base flex items-center justify-center font-headline-sm text-headline-sm text-ink-primary">
                SS
              </div>
<div>
<h4 class="font-body-lg text-body-lg font-bold text-ink-primary">Sumit Singh</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Placed at Accenture</p>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- 7. QUESTIONS (FAQ) ACCORDION -->
<section class="mt-space-2xl bg-surface-white rounded-[24px] p-8 shadow-sm">
<div class="flex items-center justify-between pb-space-lg border-b border-hairline-border mb-space-md">
<div>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221]">• FREQUENTLY ASKED</span>
<h2 class="font-headline-xl text-headline-xl uppercase text-ink-primary">QUESTIONS</h2>
</div>
<span class="font-label-mono text-label-mono text-ink-muted uppercase">ANSWERS &amp; ENROLMENT GUIDELINES</span>
</div>
<div class="divide-y divide-hairline-border">
<!-- Q1 -->
<details class="group py-space-md cursor-pointer" open="">
<summary class="flex items-center justify-between font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors list-none">
<span class="">01. Do I need prior coding or technical SAP knowledge to learn FICO?</span>
<div class="w-8 h-8 rounded-full bg-canvas-base group-open:bg-[#FB6221] group-open:text-white flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
</div>
</summary>
<p class="mt-space-md font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">No programming or ABAP knowledge is required. SAP FICO is a functional module. Basic accounting knowledge (debit, credit, ledgers, P&amp;L, balance sheet) is enough.</p>
</details>
<!-- Q2 -->
<details class="group py-space-md cursor-pointer">
<summary class="flex items-center justify-between font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors list-none">
<span class="">02. How long is the course and what are the batch timings?</span>
<div class="w-8 h-8 rounded-full bg-canvas-base group-open:bg-[#FB6221] group-open:text-white flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
</div>
</summary>
<p class="mt-space-md font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">The instructor-led SAP FICO course runs for 3 months in live online classes. Call +91 8506 888 288 to ask about the next batch and timings.</p>
</details>
<!-- Q3 -->
<details class="group py-space-md cursor-pointer">
<summary class="flex items-center justify-between font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors list-none">
<span class="">03. Will I practise on a real SAP system?</span>
<div class="w-8 h-8 rounded-full bg-canvas-base group-open:bg-[#FB6221] group-open:text-white flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
</div>
</summary>
<p class="mt-space-md font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">Yes. Classes are live and you practise on a real SAP system. Ask our team about access details when you book a demo.</p>
</details>
<!-- Q4 -->
<details class="group py-space-md cursor-pointer">
<summary class="flex items-center justify-between font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors list-none">
<span class="">04. Does ZebLearn provide placement support?</span>
<div class="w-8 h-8 rounded-full bg-canvas-base group-open:bg-[#FB6221] group-open:text-white flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
</div>
</summary>
<p class="mt-space-md font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">Yes. Placement support is available through our partner PlacementBaba, along with interview preparation.</p>
</details>
<!-- Q5 -->
<details class="group py-space-md cursor-pointer">
<summary class="flex items-center justify-between font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors list-none">
<span class="">05. What is the fee for SAP FICO?</span>
<div class="w-8 h-8 rounded-full bg-canvas-base group-open:bg-[#FB6221] group-open:text-white flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
</div>
</summary>
<p class="mt-space-md font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">The 3-month instructor-led course is ₹25,000 + GST. The SAP Power User Certification course (80 hours, self-paced, by SAP) is ₹40,000 + GST.</p>
</details>
<!-- Q6 -->
<details class="group py-space-md cursor-pointer">
<summary class="flex items-center justify-between font-headline-sm text-headline-sm uppercase text-ink-primary group-hover:text-[#FB6221] transition-colors list-none">
<span class="">06. Can I attend a demo class first?</span>
<div class="w-8 h-8 rounded-full bg-canvas-base group-open:bg-[#FB6221] group-open:text-white flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[18px] group-open:rotate-180 transition-transform">expand_more</span>
</div>
</summary>
<p class="mt-space-md font-body-md text-body-md text-on-surface-variant max-w-3xl leading-relaxed">Yes. Book a free demo, or call or WhatsApp us, and our team will share the next demo session.</p>
</details>
</div>
</section>
<!-- 8. RELATED MODULES STRIP -->
<section class="mt-space-2xl">
<div class="flex items-center justify-between mb-space-lg">
<div>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221]">• ENTERPRISE SPECTRUM</span>
<h2 class="font-headline-xl text-headline-xl uppercase text-ink-primary">EXPLORE OTHER MODULES</h2>
</div>
<a class="font-label-mono text-label-mono uppercase text-ink-primary hover:text-[#FB6221] transition-colors flex items-center gap-1" href="/#course-deck">
<span class="">All Courses</span>
<span class="material-symbols-outlined text-[16px]">arrow_forward</span>
</a>
</div>
<div class="flex flex-col gap-space-sm">
<!-- Module Row 1: MM -->
<a class="group bg-surface-white hover:bg-[#FB6221] rounded-[24px] p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-300" href="/#course-deck">
<div class="flex items-center gap-space-lg">
<span class="font-headline-lg text-headline-lg text-ink-primary group-hover:text-white transition-colors">SAP MM</span>
<span class="font-body-md text-body-md text-on-surface-variant group-hover:text-white/90 transition-colors">Materials Management, Procurement &amp; Inventory</span>
</div>
<div class="flex items-center gap-space-xl">
<span class="font-label-mono text-label-mono uppercase text-ink-muted group-hover:text-white/80 transition-colors">1,650+ LEARNERS</span>
<span class="font-label-mono text-label-mono uppercase text-ink-muted group-hover:text-white/80 transition-colors">3 MONTHS</span>
<span class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-white transition-colors">₹25,000</span>
<div class="w-10 h-10 rounded-full bg-canvas-base group-hover:bg-white text-ink-primary group-hover:text-[#FB6221] flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[20px]">arrow_outward</span>
</div>
</div>
</a>
<!-- Module Row 2: SD -->
<a class="group bg-surface-white hover:bg-[#FB6221] rounded-[24px] p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-300" href="/#course-deck">
<div class="flex items-center gap-space-lg">
<span class="font-headline-lg text-headline-lg text-ink-primary group-hover:text-white transition-colors">SAP SD</span>
<span class="font-body-md text-body-md text-on-surface-variant group-hover:text-white/90 transition-colors">Sales &amp; Distribution, Order to Cash (OTC) Lifecycle</span>
</div>
<div class="flex items-center gap-space-xl">
<span class="font-label-mono text-label-mono uppercase text-ink-muted group-hover:text-white/80 transition-colors">1,057+ LEARNERS</span>
<span class="font-label-mono text-label-mono uppercase text-ink-muted group-hover:text-white/80 transition-colors">3 MONTHS</span>
<span class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-white transition-colors">₹25,000</span>
<div class="w-10 h-10 rounded-full bg-canvas-base group-hover:bg-white text-ink-primary group-hover:text-[#FB6221] flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[20px]">arrow_outward</span>
</div>
</div>
</a>
<!-- Module Row 3: PP -->
<a class="group bg-surface-white hover:bg-[#FB6221] rounded-[24px] p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all duration-300" href="/#course-deck">
<div class="flex items-center gap-space-lg">
<span class="font-headline-lg text-headline-lg text-ink-primary group-hover:text-white transition-colors">SAP PP</span>
<span class="font-body-md text-body-md text-on-surface-variant group-hover:text-white/90 transition-colors">Production Planning, Bill of Materials (BOM) &amp; Routing</span>
</div>
<div class="flex items-center gap-space-xl">
<span class="font-label-mono text-label-mono uppercase text-ink-muted group-hover:text-white/80 transition-colors">974+ LEARNERS</span>
<span class="font-label-mono text-label-mono uppercase text-ink-muted group-hover:text-white/80 transition-colors">3 MONTHS</span>
<span class="font-headline-sm text-headline-sm text-ink-primary group-hover:text-white transition-colors">₹25,000</span>
<div class="w-10 h-10 rounded-full bg-canvas-base group-hover:bg-white text-ink-primary group-hover:text-[#FB6221] flex items-center justify-center transition-all">
<span class="material-symbols-outlined text-[20px]">arrow_outward</span>
</div>
</div>
</a>
</div>
</section>
<!-- 9. FINAL HIGH-VOLTAGE CTA SECTION -->
<section class="mt-space-3xl pt-space-2xl pb-space-xl text-center flex flex-col items-center">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#FB6221] mb-space-sm font-bold">
        • FREE DEMO CLASS
      </span>
<h2 class="font-display-xl text-display-xl uppercase text-ink-primary tracking-tighter leading-[0.88] select-none mb-space-md">
        TRY ONE CLASS FIRST
      </h2>
<p class="font-body-xl text-body-xl text-on-surface-variant max-w-2xl mb-space-xl">
        Experience a live class before you decide. Speak with our team to find the right SAP module for your career.
      </p>
<div class="flex flex-wrap items-center justify-center gap-space-md">
<button class="px-10 py-4 bg-[#FB6221] hover:brightness-110 active:scale-95 text-white font-body-lg text-body-lg font-bold rounded-full transition-all shadow-[0_10px_30px_rgba(251,98,33,0.35)] flex items-center gap-2" onclick="window.open('https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo%20for%20SAP%20FICO.','_blank')" type="button">
<span class="">Book a Demo</span>
<span class="material-symbols-outlined text-[20px]">arrow_forward</span>
</button>
<a class="px-8 py-4 bg-surface-white hover:bg-canvas-glow border border-hairline-border text-ink-primary font-body-lg text-body-lg font-semibold rounded-full transition-all flex items-center gap-3 shadow-sm" href="tel:+918506888288">
<span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
<span class="">+91 8506 888 288</span>
</a>
</div>
</section>
</div>
</div>
@endsection

