@extends('layouts.app')

@section('title', 'Contact ZebLearn | SAP Training Enquiries')
@section('description', 'Contact ZebLearn for SAP course enquiries, batch timings, fees and syllabus. Reach our training counselors via phone, WhatsApp or email for FICO, MM, SD and PP.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')
@section('og_title', 'Contact ZebLearn | SAP Training Enquiries')
@section('og_description', 'Contact ZebLearn for SAP course enquiries, batch timings, fees and syllabus. Reach our training counselors via phone, WhatsApp or email for FICO, MM, SD and PP.')
@section('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')
@section('twitter_title', 'Contact ZebLearn | SAP Training Enquiries')
@section('twitter_description', 'Contact ZebLearn for SAP course enquiries, batch timings, fees and syllabus. Reach our training counselors via phone, WhatsApp or email for FICO, MM, SD and PP.')

@push('head')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact ZebLearn",
  "url": "https://sap.zeblearn.com/contact",
  "description": "Contact ZebLearn for SAP course enquiries, batch timings, fees and syllabus.",
  "publisher": {
    "@type": "EducationalOrganization",
    "name": "ZebLearn",
    "url": "https://sap.zeblearn.com",
    "telephone": "+918506888288",
    "email": "enquiry@zeblearn.com"
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
      "name": "Contact",
      "item": "https://sap.zeblearn.com/contact"
    }
  ]
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <!-- HERO SECTION -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-36 sm:pt-40 lg:pt-44 pb-8 sm:pb-12 w-full">
        <!-- Breadcrumb & Telemetry -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200 animate-hero-badge">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="/" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">Contact</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">DIRECT COUNSELOR HELPLINE</span>
            </div>
        </div>

        <!-- Big Display Headline -->
        <div class="mb-8">
            <h1 class="font-heading text-5xl sm:text-7xl md:text-8xl lg:text-[96px] font-black uppercase tracking-tight leading-[0.95] text-[#0A0A0A] select-none animate-hero-title">
                TALK TO A <span class="sr-only">REAL PERSON</span>
            </h1>
            <div class="flex flex-wrap items-baseline justify-between -mt-2 sm:-mt-4">
                <span class="font-heading text-5xl sm:text-7xl md:text-8xl lg:text-[96px] font-black uppercase tracking-tight leading-[0.95] text-[#FB6221] select-none" aria-hidden="true">
                    REAL PERSON.
                </span>
                <span class="font-mono text-xs sm:text-sm text-slate-500 uppercase tracking-widest hidden md:block">
                    [ RESPONSE WITHIN 15 MINUTES DURING OPERATIONAL HOURS ]
                </span>
            </div>
        </div>

        <p class="font-sans text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed animate-hero-desc">
            Call or message our academic counselors on WhatsApp. We will help you understand module prerequisites, system access, batch schedules, and career roadmaps.
        </p>
    </section>

    <!-- 3 DIRECT ACTION CHANNELS (CALL, WHATSAPP, EMAIL) -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-12 w-full" data-reveal>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Channel 1: Phone (Call Us) -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-[#FB6221]/50 hover:shadow-md transition-all zeb-card-interactive" data-reveal data-reveal-delay="1">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider">
                            Direct Line
                        </span>
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                            <span class="material-symbols-outlined text-[18px]">call</span>
                        </div>
                    </div>
                    <h2 class="font-heading text-lg font-bold uppercase text-[#0A0A0A] mb-1 tracking-tight">CALL US</h2>
                    <div class="font-heading text-2xl font-extrabold text-[#0A0A0A] tracking-tight my-2">
                        +91 8506 888 288
                    </div>
                    <p class="font-sans text-xs text-slate-600 leading-relaxed">
                        Speak directly with an advisor regarding batch timings, module depth, and fees.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t border-slate-100">
                    <a class="w-full inline-flex items-center justify-center gap-2 bg-[#0A0A0A] hover:bg-black text-white px-6 py-3 rounded-xl font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs zeb-btn-cta" href="tel:+918506888288">
                        <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                        <span>Call Now</span>
                    </a>
                </div>
            </div>

            <!-- Channel 2: WhatsApp -->
            <div class="bg-[#0A0A0A] text-white rounded-2xl p-6 sm:p-8 border border-slate-800 shadow-md flex flex-col justify-between relative overflow-hidden group zeb-card-interactive" data-reveal data-reveal-delay="2">
                <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-[#FB6221]/15 rounded-full blur-xl pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-white/10 border border-white/10 text-white font-mono text-[10px] font-bold uppercase tracking-wider">
                            Instant Chat
                        </span>
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-[#FB6221]">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                        </div>
                    </div>
                    <h2 class="font-heading text-lg font-bold uppercase text-white mb-1 tracking-tight">WHATSAPP</h2>
                    <div class="font-heading text-2xl font-extrabold text-white tracking-tight my-2">
                        +91 72778 77778
                    </div>
                    <p class="font-sans text-xs text-slate-300 leading-relaxed">
                        Message us for demo confirmations, fee breakdowns, and syllabus brochures.
                    </p>
                </div>
                <div class="relative z-10 pt-6 mt-6 border-t border-white/10">
                    <a class="w-full inline-flex items-center justify-center gap-2 bg-[#FB6221] hover:bg-[#e05214] text-white px-6 py-3 rounded-xl font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-md zeb-btn-cta" href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo." rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[16px]">forum</span>
                        <span>Start WhatsApp Chat</span>
                    </a>
                </div>
            </div>

            <!-- Channel 3: Email -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-[#FB6221]/50 hover:shadow-md transition-all zeb-card-interactive" data-reveal data-reveal-delay="3">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider">
                            Written Enquiries
                        </span>
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-700">
                            <span class="material-symbols-outlined text-[18px]">alternate_email</span>
                        </div>
                    </div>
                    <h2 class="font-heading text-lg font-bold uppercase text-[#0A0A0A] mb-1 tracking-tight">EMAIL</h2>
                    <div class="font-heading text-xl font-extrabold text-[#0A0A0A] tracking-tight my-2 break-all">
                        enquiry@zeblearn.com
                    </div>
                    <p class="font-sans text-xs text-slate-600 leading-relaxed">
                        Write to our team for official syllabus documents and corporate training proposals.
                    </p>
                </div>
                <div class="pt-6 mt-6 border-t border-slate-100">
                    <button type="button" class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-[#0A0A0A] px-6 py-3 rounded-xl font-sans text-xs font-semibold uppercase tracking-wider transition-all duration-200 cursor-pointer active:scale-95" id="copy-email-btn">
                        <span class="material-symbols-outlined text-[16px]" id="copy-icon">content_copy</span>
                        <span id="copy-text">Copy Email Address</span>
                    </button>
                </div>
            </div>

        </div>
    </section>

    <!-- TWO-COLUMN SECTION: METRICS & INTERACTIVE CONTACT FORM -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-16 w-full" data-reveal>
        <div class="bg-white rounded-3xl p-6 sm:p-12 border border-slate-200/80 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            <!-- Left Column: Reassurance & Program Summary (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                   
                    <h2 class="font-heading text-3xl sm:text-4xl font-bold uppercase tracking-tight text-[#0A0A0A] leading-tight mb-4">
                        SEND US A MESSAGE
                    </h2>
                    <p class="font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Fill in your details and our team will get back to you regarding courses, fee structures, batch schedules, and free demo class access.
                    </p>
                </div>

                <!-- 3 Points Checklist -->
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5 font-bold">
                            <span class="material-symbols-outlined text-[13px]">check</span>
                        </div>
                        <div>
                            <p class="font-sans text-xs font-bold text-[#0A0A0A]">Talk to our senior team</p>
                            <p class="font-sans text-[11px] text-slate-500">Get answers about courses, prerequisites, and live batch timings.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5 font-bold">
                            <span class="material-symbols-outlined text-[13px]">check</span>
                        </div>
                        <div>
                            <p class="font-sans text-xs font-bold text-[#0A0A0A]">Transparent tuition</p>
                            <p class="font-sans text-[11px] text-slate-500">SAP FICO, MM, SD, and PP: ₹25,000 + GST. Power User: ₹40,000 + GST.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-orange-50 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5 font-bold">
                            <span class="material-symbols-outlined text-[13px]">check</span>
                        </div>
                        <div>
                            <p class="font-sans text-xs font-bold text-[#0A0A0A]">Free trial class</p>
                            <p class="font-sans text-[11px] text-slate-500">Experience a live session before deciding on your enrollment.</p>
                        </div>
                    </div>
                </div>

                <!-- Alumni Metric Box -->
                <div class="bg-[#0A0A0A] text-white p-5 rounded-2xl border border-slate-800">
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-widest block mb-3">
                        LEARNERS TRAINED IN LIVE BATCHES
                    </span>
                    <div class="grid grid-cols-2 gap-3 font-mono text-xs">
                        <div><span class="font-heading text-xl font-extrabold text-[#FB6221] block leading-none">1,850+</span><span class="text-slate-400">SAP FICO</span></div>
                        <div><span class="font-heading text-xl font-extrabold text-[#FB6221] block leading-none">1,650+</span><span class="text-slate-400">SAP MM</span></div>
                        <div><span class="font-heading text-xl font-extrabold text-[#FB6221] block leading-none">1,057+</span><span class="text-slate-400">SAP SD</span></div>
                        <div><span class="font-heading text-xl font-extrabold text-[#FB6221] block leading-none">974+</span><span class="text-slate-400">SAP PP</span></div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Interactive Form (7 cols) -->
            <div class="lg:col-span-7">
                <form class="space-y-4" id="contactForm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Full Name -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold">
                                Full Name 
                            </label>
                            <input autocomplete="name" class="w-full bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none transition-all placeholder-slate-400" name="name" placeholder="Your name" required type="text"/>
                        </div>

                        <!-- Phone (+91) -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold">
                                Phone Number (+91) <span class="text-[#FB6221]">*</span>
                            </label>
                            <div class="flex items-center bg-slate-50 rounded-xl border border-slate-200 focus-within:border-[#FB6221] focus-within:bg-white transition-all px-3 py-2.5">
                                <span class="font-mono text-xs font-bold text-slate-500 mr-2">+91</span>
                                <input autocomplete="tel" class="w-full bg-transparent text-[#0A0A0A] font-['Inter'] text-xs outline-none placeholder-slate-400" inputmode="tel" maxlength="15" name="phone" pattern="[0-9]{10}" placeholder="98765 43210" required type="tel"/>
                            </div>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold">
                            Email Address <span class="text-[#FB6221]">*</span>
                        </label>
                        <input autocomplete="email" class="w-full bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none transition-all placeholder-slate-400" name="email" placeholder="you@example.com" required type="email"/>
                    </div>

                    <!-- Course Interested In -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold">
                            Course Interested In <span class="text-[#FB6221]">*</span>
                        </label>
                        <div class="relative">
                            <select class="w-full appearance-none bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none cursor-pointer transition-all pr-10 font-medium" name="course" required>
                                <option disabled selected value="">Select a course...</option>
                                <option value="SAP FICO">SAP FICO</option>
                                <option value="SAP MM">SAP MM</option>
                                <option value="SAP SD">SAP SD</option>
                                <option value="SAP PP">SAP PP</option>
                                <option value="SAP Power User Certification">SAP Power User Certification</option>
                                <option value="Not sure yet">Not sure yet (Need Guidance)</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 text-[18px]">expand_more</span>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold">
                            Your Message (Optional)
                        </label>
                        <textarea class="w-full bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none transition-all placeholder-slate-400 resize-none" name="message" placeholder="Tell us about your background or any specific questions..." rows="3"></textarea>
                    </div>

                    <!-- Submit Bar -->
                    <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#FB6221] hover:bg-[#e05214] text-white px-8 py-3.5 rounded-full font-['Inter'] text-xs font-bold uppercase tracking-wider transition-all shadow-md cursor-pointer hover:scale-[1.01] zeb-btn-cta" type="submit">
                            <span>Send On WhatsApp</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                        <span class="font-mono text-[10px] text-slate-400 text-center sm:text-right">
                            Opens WhatsApp with formatted message
                        </span>
                    </div>
                </form>
            </div>

        </div>
    </section>

    <!-- QUICK ANSWERS ACCORDION (FAQ) -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-16 w-full" data-reveal>
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="font-mono text-xs font-bold uppercase text-[#FB6221] tracking-widest block mb-1">FREQUENTLY ASKED</span>
                <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase text-[#0A0A0A] tracking-tight">
                    QUICK ANSWERS
                </h2>
            </div>
            <span class="font-mono text-xs text-slate-400 uppercase tracking-wider">ENQUIRY GUIDANCE</span>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs divide-y divide-slate-100 p-6 sm:p-8">
            <!-- Q1 -->
            <details class="group py-4 cursor-pointer" open>
                <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-[#FB6221]">01</span>
                        <span>What are the course fees?</span>
                    </div>
                    <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                </summary>
                <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                    The instructor-led SAP FICO, MM, SD, and PP courses are <strong>₹25,000 + GST</strong> each. The SAP Power User Certification course is <strong>₹40,000 + GST</strong>. Call or WhatsApp us for installment and payment details.
                </div>
            </details>

            <!-- Q2 -->
            <details class="group py-4 cursor-pointer">
                <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">02</span>
                        <span>Can I attend a free demo session before paying?</span>
                    </div>
                    <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                </summary>
                <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Yes. Book a free demo class through our website or on WhatsApp, and our team will share the schedule for the next live demo session.
                </div>
            </details>

            <!-- Q3 -->
            <details class="group py-4 cursor-pointer">
                <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">03</span>
                        <span>What are the batch timings?</span>
                    </div>
                    <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                </summary>
                <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                    We offer weekday morning/evening batches as well as dedicated weekend batches for working professionals. Call +91 8506 888 288 or WhatsApp us to get upcoming batch timings.
                </div>
            </details>

            <!-- Q4 -->
            <details class="group py-4 cursor-pointer">
                <summary class="flex items-center justify-between font-heading text-base font-bold uppercase text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors list-none tracking-tight">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-slate-400 group-open:text-[#FB6221]">04</span>
                        <span>How long are the courses?</span>
                    </div>
                    <span class="material-symbols-outlined text-[20px] text-slate-400 group-open:rotate-180 group-open:text-[#FB6221] transition-transform">expand_more</span>
                </summary>
                <div class="mt-3 pl-7 max-w-4xl font-sans text-xs sm:text-sm text-slate-600 leading-relaxed">
                    The instructor-led SAP FICO, MM, SD, and PP courses run for 3 months with live online classes and server practice access.
                </div>
            </details>
        </div>
    </section>

    <!-- INFO STRIP WITH POLICIES -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20 w-full" data-reveal>
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col lg:flex-row items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3 justify-center lg:justify-start">
                <div class="flex items-center gap-2 bg-slate-50 px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-mono text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">videocam</span>
                    <span>Live Online</span>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-mono text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">schedule</span>
                    <span>3 Month Courses</span>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-700 font-mono text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px] text-[#FB6221]">work</span>
                    <span>Placement Support</span>
                </div>
            </div>

            <div class="flex items-center gap-6 font-mono text-xs text-slate-500 uppercase">
                <a class="hover:text-[#FB6221] transition-colors underline underline-offset-4" href="https://sap.zeblearn.com/refund-policy/" rel="noopener" target="_blank">Refund Policy</a>
                <a class="hover:text-[#FB6221] transition-colors underline underline-offset-4" href="https://sap.zeblearn.com/disclaimer-policy-trademark/" rel="noopener" target="_blank">Privacy Policy &amp; Terms</a>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Email Copy Button with Robust Fallback & State Restoration
    const copyBtn = document.getElementById('copy-email-btn');
    const copyText = document.getElementById('copy-text');
    const copyIcon = document.getElementById('copy-icon');

    if (copyBtn) {
        copyBtn.addEventListener('click', function () {
            const email = 'enquiry@zeblearn.com';

            function showSuccess() {
                if (copyText) copyText.textContent = 'COPIED TO CLIPBOARD!';
                if (copyIcon) copyIcon.textContent = 'check';
                copyBtn.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-[#0A0A0A]');
                copyBtn.classList.add('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');

                setTimeout(function () {
                    if (copyText) copyText.textContent = 'Copy Email Address';
                    if (copyIcon) copyIcon.textContent = 'content_copy';
                    copyBtn.classList.remove('bg-emerald-50', 'text-emerald-700', 'border', 'border-emerald-200');
                    copyBtn.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-[#0A0A0A]');
                }, 2500);
            }

            function showFailure() {
                if (copyText) copyText.textContent = 'FAILED TO COPY';
                if (copyIcon) copyIcon.textContent = 'close';
                copyBtn.classList.remove('bg-slate-100', 'hover:bg-slate-200', 'text-[#0A0A0A]');
                copyBtn.classList.add('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');

                setTimeout(function () {
                    if (copyText) copyText.textContent = 'Copy Email Address';
                    if (copyIcon) copyIcon.textContent = 'content_copy';
                    copyBtn.classList.remove('bg-rose-50', 'text-rose-700', 'border', 'border-rose-200');
                    copyBtn.classList.add('bg-slate-100', 'hover:bg-slate-200', 'text-[#0A0A0A]');
                }, 2500);
            }

            function fallbackCopy() {
                try {
                    const tempInput = document.createElement('textarea');
                    tempInput.value = email;
                    tempInput.setAttribute('readonly', '');
                    tempInput.style.position = 'fixed';
                    tempInput.style.opacity = '0';
                    tempInput.style.top = '-9999px';
                    document.body.appendChild(tempInput);
                    tempInput.focus();
                    tempInput.select();
                    const successful = document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    if (successful) {
                        showSuccess();
                    } else {
                        showFailure();
                    }
                } catch (err) {
                    showFailure();
                }
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(email)
                    .then(showSuccess)
                    .catch(fallbackCopy);
            } else {
                fallbackCopy();
            }
        });
    }

    // 2. Contact Form WhatsApp Submission
    const contactForm = document.getElementById('contactForm');
    if (!contactForm) return;

    contactForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const name = contactForm.elements.name.value.trim();
        const phone = contactForm.elements.phone.value.trim();
        const email = contactForm.elements.email.value.trim();
        const course = contactForm.elements.course.value;
        const message = contactForm.elements.message.value.trim();

        const enquiry = [
            'Hello ZebLearn, I have an enquiry.',
            `Name: ${name}`,
            `Phone: +91 ${phone}`,
            email ? `Email: ${email}` : '',
            `Course: ${course}`,
            message ? `Message: ${message}` : ''
        ].filter(Boolean).join('\n');

        const whatsappUrl = `https://wa.me/917277877778?text=${encodeURIComponent(enquiry)}`;
        window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
    });
});
</script>
@endpush
