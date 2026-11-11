@extends('layouts.app')

@section('title', 'SAP Interview Questions, Notes and Career Guides | ZebLearn Blog')
@section('description', 'Explore SAP interview questions, answers, learning notes and career guidance across SAP Basis, FICO, MM, SD, PP and EWM.')
@section('og_type', 'website')


@push('schema')
@verbatim
<script type="application/ld+json">{"@context":"https://schema.org","@type":"Blog","name":"ZebLearn SAP Blog","description":"SAP interview questions, answers and career guides from ZebLearn.","publisher":{"@type":"EducationalOrganization","name":"ZebLearn","url":"https://sap.zeblearn.com"}}</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#E4E6E9] text-[#0A0A0A] font-['Inter'] antialiased selection:bg-[#FB6221] selection:text-white">
<!-- Subtle Top Atmospheric Gradient Mesh -->
<div class="w-full bg-gradient-to-b from-[#F0F1F3] via-[#E4E6E9] to-[#E4E6E9] pb-10">
<!-- 1. HEADER SECTION -->
<section class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-12 pt-8 lg:pt-14 pb-10">
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-8 border-b border-[#D3D6DA]">
<div class="space-y-3">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-[#D3D6DA] shadow-xs">
<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
<span class="text-[12px] font-semibold tracking-wider uppercase text-[#0A0A0A]">Blog &amp; Knowledge Base</span>
</div>
<h1 class="font-['Anton'] text-[64px] sm:text-[88px] lg:text-[104px] uppercase tracking-tight leading-[0.9] text-[#0A0A0A] select-none">
            SAP NOTES.<br/>
<span class="text-transparent bg-clip-text bg-gradient-to-r from-[#0A0A0A] via-[#0A0A0A] to-[#FB6221]">INTERVIEW PREP.</span>
</h1>
</div>
<div class="lg:max-w-xs xl:max-w-sm flex flex-col justify-end">
<div class="border-l-2 border-[#FB6221] pl-4">
<p class="text-[15px] sm:text-[16px] text-[#0A0A0A]/80 leading-relaxed font-medium">
              SAP interview questions and answers, notes and career guidance from ZebLearn.
            </p>
</div>
</div>
</div>
</section>
<!-- 2. STICKY FLOATING FILTER BAR -->
<section class="sticky top-24 z-40 max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-12 my-2 pointer-events-none">
<div class="pointer-events-auto bg-white/95 backdrop-blur-md p-2 rounded-full border border-[#D3D6DA] shadow-[0_12px_32px_rgba(10,10,10,0.06)] flex flex-col md:flex-row items-center justify-between gap-3 transition-all duration-300">
<!-- Filter Tabs -->
<div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto scrollbar-none py-1 px-1">
<button class="filter-btn active shrink-0 px-5 py-2.5 rounded-full text-[13px] font-semibold tracking-wide bg-[#0A0A0A] text-white hover:bg-black transition-all">
            All
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            SAP Basis
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            FICO
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            MM
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            SD
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            PP
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            EWM
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            Interview Questions
          </button>
<button class="filter-btn shrink-0 px-4 py-2.5 rounded-full text-[13px] font-semibold tracking-wide text-[#0A0A0A]/70 hover:text-[#0A0A0A] hover:bg-[#F0F1F3] transition-all">
            Career
          </button>
</div>
<!-- Search Bar -->
<div class="relative w-full md:w-80 shrink-0 pr-1">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-[18px] text-[#0A0A0A]/40">search</span>
<input class="w-full bg-[#F0F1F3] text-[#0A0A0A] text-[13px] rounded-full pl-10 pr-4 py-2.5 border border-transparent focus:border-[#FB6221] focus:bg-white focus:outline-none transition-all placeholder-[#0A0A0A]/40" placeholder="Search SAP notes, t-codes, questions..." type="text"/>
</div>
</div>
</section>
<!-- 3. FEATURED POST (12-Col Bento Hero) -->
<section class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-12 mt-6">
<article class="relative group rounded-[24px] overflow-hidden border border-[#D3D6DA] bg-white grid grid-cols-1 lg:grid-cols-12 shadow-[0_8px_30px_rgba(10,10,10,0.04)] hover:shadow-[0_16px_40px_rgba(10,10,10,0.08)] transition-all duration-300"><a aria-label="Read article" class="absolute inset-0 z-10" href="/blogs/top-50-sap-ewm-interview-question-answer"></a>
<!-- Left 7 cols: Image with live tag -->
<div class="lg:col-span-7 relative min-h-[340px] lg:min-h-[460px] overflow-hidden bg-[#0A0A0A]">

<div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent lg:hidden"></div>
<div class="absolute top-6 left-6 flex items-center gap-2">
<span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-[#FB6221] text-white text-[12px] font-semibold tracking-wider uppercase shadow-md">
<span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
              Featured
            </span>
<span class="px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white/90 text-[11px] font-mono font-medium uppercase border border-white/10">
              SAP EWM
            </span>
</div>
<div class="absolute bottom-6 left-6 hidden lg:flex items-center gap-2 text-[11px] font-mono text-white/80 bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
<span class="text-[#FB6221] font-bold">MODULE:</span> SAP EWM
          </div>
</img></div>
<!-- Right 5 cols: Pitch Black Editorial Container -->
<div class="lg:col-span-5 bg-[#0A0A0A] p-8 lg:p-12 flex flex-col justify-between text-white border-t lg:border-t-0 lg:border-l border-white/10">
<div class="space-y-4">

<h2 class="font-['Anton'] text-[38px] sm:text-[46px] lg:text-[50px] uppercase leading-[0.95] tracking-wide text-white group-hover:text-[#FB6221] transition-colors pt-2">
              TOP 50 SAP EWM INTERVIEW QUESTION ANSWER
            </h2>
<p class="text-[15px] text-[#D3D6DA] leading-relaxed font-normal pt-1">
              Warehouse management questions and answers to prepare for SAP EWM interviews.
            </p>
</div>
<!-- Footer of featured card -->
<div class="pt-8 mt-6 border-t border-white/10 flex items-center justify-between gap-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-[#FB6221]/20 border border-[#FB6221]/40 flex items-center justify-center font-bold text-white text-sm">
                ZB
              </div>
<div class="flex flex-col">
<span class="text-[13px] font-medium text-white">ZebLearn Team</span>
<span class="text-[11px] text-white/50 font-mono">Interview Questions</span>
</div>
</div>
<a aria-label="Read featured article" class="relative z-20 w-12 h-12 rounded-full bg-[#FB6221] text-white flex items-center justify-center group-hover:scale-110 group-hover:bg-white group-hover:text-[#0A0A0A] transition-all shadow-[0_4px_16px_rgba(251,98,33,0.4)] shrink-0" href="/blogs/top-50-sap-ewm-interview-question-answer">
<span class="material-symbols-outlined text-[20px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</a>
</div>
</div>
</article>
</section>
<!-- 4. BENTO POST GRID (12-Col, 16px Gaps, Mixed Cards) -->
<section class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-12 mt-8">
<div class="grid grid-cols-1 md:grid-cols-12 gap-4">
<!-- POST 1: Tall image card, 4 cols, white bg -->
<article class="relative md:col-span-12 lg:col-span-4 rounded-[24px] bg-white border border-[#D3D6DA] overflow-hidden flex flex-col justify-between p-6 shadow-xs hover:shadow-[0_12px_28px_rgba(10,10,10,0.06)] group transition-all duration-300"><a aria-label="Read article" class="absolute inset-0 z-10" href="https://sap.zeblearn.com/top-interview-question-answer-on-sap-basic/" rel="noopener" target="_blank"></a>
<div>
<div class="w-full h-52 rounded-[16px] overflow-hidden bg-[#D3D6DA] relative mb-5">
<div class="w-full h-full flex items-center justify-center font-['Anton'] text-[64px] uppercase text-[#0A0A0A]/10 select-none">BASIS</div>
<span class="absolute top-3 left-3 px-3 py-1 rounded-full bg-white/95 backdrop-blur-xs text-[11px] font-semibold uppercase tracking-wider text-[#0A0A0A]">
                SAP BASIS
              </span>
</div>
<h3 class="font-['Anton'] text-[28px] leading-[1] uppercase tracking-wide text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
              TOP INTERVIEW QUESTION ANSWER ON SAP BASIC
            </h3>
<p class="text-[14px] text-[#0A0A0A]/70 mt-3 leading-relaxed">
              Common SAP Basis interview questions with clear, practical answers.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-[#D3D6DA]/60 flex items-center justify-between">
<span class="text-[12px] font-mono text-[#0A0A0A]/50">READ ARTICLE</span>
<div class="w-9 h-9 rounded-full bg-[#F0F1F3] text-[#0A0A0A] flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[16px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
<!-- POST 2: Text-only soft gray card #D3D6DA, 4 cols -->
<article class="relative md:col-span-12 lg:col-span-4 rounded-[24px] bg-[#D3D6DA] border border-[#D3D6DA] p-7 flex flex-col justify-between group shadow-xs hover:shadow-[0_12px_28px_rgba(10,10,10,0.06)] transition-all duration-300"><a aria-label="Read article" class="absolute inset-0 z-10" href="https://sap.zeblearn.com/top-50-question-answer-on-sap-ecchana-sap-power-user/" rel="noopener" target="_blank"></a>
<div>
<div class="flex items-center justify-between mb-6">
<span class="px-3.5 py-1 rounded-full bg-white text-[#0A0A0A] text-[11px] font-semibold uppercase tracking-wider">
                CORE ERP
              </span>
<span class="text-[11px] font-mono text-[#0A0A0A]/60">ECC · HANA</span>
</div>
<h3 class="font-['Anton'] text-[32px] leading-[0.98] uppercase tracking-wide text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
              TOP 20 QUESTION ANSWER ON SAP ECC + HANA + SAP POWER USER
            </h3>
<p class="text-[14px] text-[#0A0A0A]/75 mt-4 leading-relaxed">
              Frequently asked questions on SAP ECC, HANA and the SAP Power User certification.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-black/10 flex items-center justify-between">
<span class="text-[12px] font-mono text-[#0A0A0A]/60">READ ARTICLE</span>
<div class="w-9 h-9 rounded-full bg-white text-[#0A0A0A] flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[16px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
<!-- POST 3: Tall dark card #0A0A0A, 4 cols -->
<article class="md:col-span-12 lg:col-span-4 rounded-[24px] bg-[#0A0A0A] border border-white/10 p-7 flex flex-col justify-between group shadow-[0_12px_32px_rgba(0,0,0,0.18)] hover:shadow-[0_16px_36px_rgba(0,0,0,0.28)] transition-all duration-300">
<div>
<div class="flex items-center justify-between mb-6">
<span class="px-3.5 py-1 rounded-full bg-white/10 text-white border border-white/10 text-[11px] font-semibold uppercase tracking-wider">
                SAP BASIS
              </span>
<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
</div>
<h3 class="font-['Anton'] text-[34px] leading-[0.96] uppercase tracking-wide text-white group-hover:text-[#FB6221] transition-colors">
              HOW SYSTEM COPY WORKS IN SAP
            </h3>
<p class="text-[14px] text-[#D3D6DA] mt-4 leading-relaxed">
              A practical explainer on system copy in SAP, for Basis learners.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-white/10 flex items-center justify-between">
<span class="text-[12px] font-mono text-white/50">COMING SOON</span>
<div class="w-10 h-10 rounded-full bg-[#FB6221] text-white flex items-center justify-center group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[18px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
<!-- POST 4: Wide white card, 6 cols with student thumbnail -->
<article class="md:col-span-12 lg:col-span-6 rounded-[24px] bg-white border border-[#D3D6DA] p-6 lg:p-7 flex flex-col sm:flex-row gap-6 justify-between group shadow-xs hover:shadow-[0_12px_28px_rgba(10,10,10,0.06)] transition-all duration-300">
<div class="flex-1 flex flex-col justify-between">
<div>
<span class="inline-block px-3 py-1 rounded-full bg-[#F0F1F3] text-[#0A0A0A] text-[11px] font-semibold uppercase tracking-wider mb-4">
                FINANCE / FICO
              </span>
<h3 class="font-['Anton'] text-[30px] sm:text-[34px] leading-[0.98] uppercase tracking-wide text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
                FICO MONTH-END CLOSING, STEP BY STEP
              </h3>
<p class="text-[14px] text-[#0A0A0A]/70 mt-3 leading-relaxed">
                A step-by-step explainer on month-end closing activities in SAP FICO.
              </p>
</div>
<div class="pt-6 mt-4 flex items-center justify-between border-t border-[#D3D6DA]/60">
<span class="text-[12px] font-mono text-[#0A0A0A]/50">COMING SOON</span>
<div class="w-9 h-9 rounded-full bg-[#F0F1F3] text-[#0A0A0A] flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[16px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</div>
<div class="w-full sm:w-44 h-48 sm:h-auto rounded-[16px] overflow-hidden bg-[#D3D6DA] shrink-0">
<div class="w-full h-full flex items-center justify-center font-['Anton'] text-[64px] uppercase text-[#0A0A0A]/10 select-none">FICO</div>
</div>
</article>
<!-- POST 5: Vivid orange card #FB6221, 6 cols -->
<article class="md:col-span-12 lg:col-span-6 rounded-[24px] bg-[#FB6221] border border-[#FB6221] p-6 lg:p-8 flex flex-col justify-between group shadow-[0_12px_32px_rgba(251,98,33,0.22)] hover:shadow-[0_16px_38px_rgba(251,98,33,0.32)] transition-all duration-300">
<div>
<div class="flex items-center justify-between mb-4">
<span class="px-3.5 py-1 rounded-full bg-white text-[#0A0A0A] text-[11px] font-bold uppercase tracking-wider shadow-xs">
                CAREER ROADMAP
              </span>
<span class="text-[12px] font-mono font-bold text-[#0A0A0A] uppercase tracking-wider">
                GUIDE
              </span>
</div>
<h3 class="font-['Anton'] text-[34px] sm:text-[40px] leading-[0.95] uppercase tracking-wide text-white group-hover:text-black transition-colors">
              SAP MM VS SD: WHICH TO LEARN FIRST
            </h3>
<!-- Rule strictly adhered: On orange surfaces, large text is white, small text is crisp black -->
<p class="text-[15px] text-[#0A0A0A] mt-4 leading-relaxed font-medium">
              A simple comparison of the MM and SD modules to help you pick your starting point.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-black/20 flex items-center justify-between">
<span class="text-[12px] font-mono font-bold text-[#0A0A0A]">COMING SOON</span>
<div class="w-10 h-10 rounded-full bg-[#0A0A0A] text-white flex items-center justify-center group-hover:scale-110 transition-transform">
<span class="material-symbols-outlined text-[18px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
<!-- POST 6: White card, 4 cols -->
<article class="md:col-span-12 lg:col-span-4 rounded-[24px] bg-white border border-[#D3D6DA] p-7 flex flex-col justify-between group shadow-xs hover:shadow-[0_12px_28px_rgba(10,10,10,0.06)] transition-all duration-300">
<div>
<div class="flex items-center justify-between mb-5">
<span class="px-3 py-1 rounded-full bg-[#F0F1F3] text-[#0A0A0A] text-[11px] font-semibold uppercase tracking-wider">
                INTERVIEW GUIDE
              </span>
<span class="text-[11px] font-mono text-[#0A0A0A]/50">INTERVIEW</span>
</div>
<h3 class="font-['Anton'] text-[30px] leading-[0.98] uppercase tracking-wide text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
              HOW TO PREPARE FOR YOUR FIRST SAP INTERVIEW
            </h3>
<p class="text-[14px] text-[#0A0A0A]/70 mt-3 leading-relaxed">
              How to prepare, what to revise and how to answer in your first SAP interview.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-[#D3D6DA]/60 flex items-center justify-between">
<span class="text-[12px] font-mono text-[#0A0A0A]/50">COMING SOON</span>
<div class="w-9 h-9 rounded-full bg-[#F0F1F3] text-[#0A0A0A] flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[16px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
<!-- POST 7: Soft gray card #D3D6DA, 4 cols -->
<article class="md:col-span-12 lg:col-span-4 rounded-[24px] bg-[#D3D6DA] border border-[#D3D6DA] p-7 flex flex-col justify-between group shadow-xs hover:shadow-[0_12px_28px_rgba(10,10,10,0.06)] transition-all duration-300">
<div>
<div class="flex items-center justify-between mb-5">
<span class="px-3 py-1 rounded-full bg-white text-[#0A0A0A] text-[11px] font-semibold uppercase tracking-wider">
                CAREER
              </span>
<span class="text-[11px] font-mono text-[#0A0A0A]/60">CAREER</span>
</div>
<h3 class="font-['Anton'] text-[30px] leading-[0.98] uppercase tracking-wide text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
              IS SAP A GOOD CAREER FOR COMMERCE GRADUATES
            </h3>
<p class="text-[14px] text-[#0A0A0A]/75 mt-3 leading-relaxed">
              How commerce and finance graduates can build a career in SAP.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-black/10 flex items-center justify-between">
<span class="text-[12px] font-mono text-[#0A0A0A]/60">COMING SOON</span>
<div class="w-9 h-9 rounded-full bg-white text-[#0A0A0A] flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[16px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
<!-- POST 8: White card, 4 cols -->
<article class="md:col-span-12 lg:col-span-4 rounded-[24px] bg-white border border-[#D3D6DA] p-7 flex flex-col justify-between group shadow-xs hover:shadow-[0_12px_28px_rgba(10,10,10,0.06)] transition-all duration-300">
<div>
<div class="flex items-center justify-between mb-5">
<span class="px-3 py-1 rounded-full bg-[#F0F1F3] text-[#0A0A0A] text-[11px] font-semibold uppercase tracking-wider">
                SAP SD
              </span>
<span class="text-[11px] font-mono text-[#0A0A0A]/50">SALES &amp; DISTRIBUTION</span>
</div>
<h3 class="font-['Anton'] text-[30px] leading-[0.98] uppercase tracking-wide text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
              TOP SAP SD INTERVIEW QUESTION ANSWER
            </h3>
<p class="text-[14px] text-[#0A0A0A]/70 mt-3 leading-relaxed">
              Questions and answers for SAP SD to help you prepare for interviews.
            </p>
</div>
<div class="pt-6 mt-6 border-t border-[#D3D6DA]/60 flex items-center justify-between">
<span class="text-[12px] font-mono text-[#0A0A0A]/50">COMING SOON</span>
<div class="w-9 h-9 rounded-full bg-[#F0F1F3] text-[#0A0A0A] flex items-center justify-center group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
<span class="material-symbols-outlined text-[16px] -rotate-45 group-hover:rotate-0 transition-transform">arrow_forward</span>
</div>
</div>
</article>
</div>
</section>
<!-- 6. WEEKLY NEWSLETTER BANNER -->
<section class="max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-12 mt-10">
<div class="rounded-[24px] bg-[#FB6221] p-8 sm:p-12 lg:p-14 shadow-[0_16px_40px_rgba(251,98,33,0.25)] relative overflow-hidden border border-[#FB6221]">
<!-- Ambient decorative background element -->
<div class="absolute right-0 top-0 translate-x-12 -translate-y-12 w-96 h-96 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
<div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
<!-- Left: Big typography -->
<div class="lg:col-span-7 space-y-3">
<div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-[#0A0A0A] text-[11px] font-bold tracking-widest uppercase">
<span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
              FREE DEMO
            </div>
<h2 class="font-['Anton'] text-[42px] sm:text-[54px] lg:text-[62px] uppercase leading-[0.92] tracking-wide text-white">
              NOT SURE WHERE<br/>TO START?
            </h2>
<!-- On orange surfaces, large text is white, small text is crisp black -->
<p class="text-[16px] text-[#0A0A0A] max-w-xl font-medium leading-relaxed pt-1">
              Talk to our team and find the right SAP module for your background.
            </p>
</div>
<!-- Right: Subscription Input Box -->
<div class="lg:col-span-5">
<div class="flex flex-col sm:flex-row gap-3">
<a class="px-7 py-4 rounded-full bg-[#0A0A0A] text-white font-semibold text-[13px] tracking-wider uppercase hover:scale-[1.02] transition-all text-center" href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo." rel="noopener" target="_blank">Book a Free Demo</a>
<a class="px-7 py-4 rounded-full bg-white text-[#0A0A0A] font-semibold text-[13px] tracking-wider uppercase hover:scale-[1.02] transition-all text-center" href="tel:+918506888288">Call +91 85068 88288</a>
</div>
</div>
</div>
</div>
</section>
</div>
<!-- Interactive JavaScript for Client-side Tab Switch Demo -->
<script>
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
          b.classList.remove('bg-[#0A0A0A]', 'text-white', 'hover:bg-black');
          b.classList.add('text-[#0A0A0A]/70', 'hover:text-[#0A0A0A]', 'hover:bg-[#F0F1F3]');
        });
        this.classList.remove('text-[#0A0A0A]/70', 'hover:text-[#0A0A0A]', 'hover:bg-[#F0F1F3]');
        this.classList.add('bg-[#0A0A0A]', 'text-white', 'hover:bg-black');
      });
    });
  </script>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
          b.classList.remove('bg-[#0A0A0A]', 'text-white', 'hover:bg-black');
          b.classList.add('text-[#0A0A0A]/70', 'hover:text-[#0A0A0A]', 'hover:bg-[#F0F1F3]');
        });
        this.classList.remove('text-[#0A0A0A]/70', 'hover:text-[#0A0A0A]', 'hover:bg-[#F0F1F3]');
        this.classList.add('bg-[#0A0A0A]', 'text-white', 'hover:bg-black');
      });
    });
</script>
@endpush

