@extends('layouts.app')

@section('title', 'Contact ZebLearn | SAP Training Enquiries')

@section('description', 'Call, WhatsApp or email ZebLearn for SAP FICO, MM, SD and PP course details, fees and demo classes.')

@section('canonical', url()->current())

@section('og_type', 'website')

@section('og_title', 'Contact ZebLearn | SAP Training Enquiries')

@section('og_description', 'Call, WhatsApp or email ZebLearn for SAP FICO, MM, SD and PP course details, fees and demo classes.')

@section('og_image', asset('images/zeblearn-home-og.webp'))

@section('twitter_title', 'Contact ZebLearn | SAP Training Enquiries')

@section('twitter_description', 'Call, WhatsApp or email ZebLearn for SAP FICO, MM, SD and PP course details, fees and demo classes.')

@section('body-class', 'bg-surface text-on-surface font-body-md text-body-md antialiased min-h-screen selection:bg-primary-container selection:text-on-primary zeb-page')

@push('head')
    @verbatim
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ContactPage",
      "name": "Contact ZebLearn",
      "publisher": {
        "@type": "EducationalOrganization",
        "name": "ZebLearn",
        "url": "https://sap.zeblearn.com",
        "telephone": "+918506888288",
        "email": "enquiry@zeblearn.com"
      }
    }
    </script>
    @endverbatim
@endpush

@section('content')
<div class="w-full pt-24 bg-surface min-h-screen">
<div class="flex flex-col w-full bg-[#E4E6E9] text-[#0A0A0A]">

<!-- TOP HERO & TACTILE CENTERPIECE -->

<section class="w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin pt-24 md:pt-28 pb-space-3xl relative overflow-hidden">

<!-- Micro Meta Bar -->

<div class="flex items-center justify-between pb-space-md mb-space-lg border-b border-[#0A0A0A]/10">

<div class="flex items-center gap-space-xs font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A]">

<span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>

<span>Contact</span>

</div>

</div>

<!-- Editorial Giant Headline & 3D Centerpiece Stack -->

<div class="relative w-full">

<div class="flex flex-col select-none">

<h1 class="font-headline-xl text-[100px] sm:text-[140px] lg:text-[185px] leading-[0.88] tracking-[-0.03em] uppercase text-[#0A0A0A] m-0 p-0 font-normal">

&#x20;         TALK TO A

&#x20;       </h1>

<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg">

<div class="font-headline-xl text-[100px] sm:text-[140px] lg:text-[185px] leading-[0.88] tracking-[-0.03em] uppercase text-[#0A0A0A] m-0 p-0 font-normal">

&#x20;           REAL PERSON.

&#x20;         </div>

<div class="max-w-md pb-space-xs">

<p class="font-body-xl text-body-xl text-[#0A0A0A]/80 font-normal leading-snug">

&#x20;             Call or message us on WhatsApp. Our team will help you pick the right SAP course.

&#x20;           </p>

</div>

</div>

</div>

<!-- Tactile Centerpiece Composition Overlay -->

<div class="relative mt-space-xl lg:-mt-14 flex items-center justify-center pointer-events-none">

<div class="relative w-72 sm:w-88 md:w-96 aspect-square flex items-center justify-center">

<!-- Radial studio ambient aura -->

<div class="absolute inset-0 bg-gradient-to-tr from-[#FB6221]/20 via-[#FB6221]/5 to-transparent blur-3xl rounded-full scale-125"></div>

<!-- Base 3D Phone Button Render -->

<div class="relative z-10 w-full h-full p-2 flex items-center justify-center">



</div>

<!-- Human Hand Cut-out Reaching Down to Press -->

<div class="absolute -top-16 sm:-top-20 z-20 w-44 sm:w-56 h-56 sm:h-72 pointer-events-none opacity-95 transition-transform duration-500 ease-out hover:translate-y-2">



</div>

<!-- Floating Micro Badge -->

<div class="absolute -bottom-2 sm:bottom-2 bg-[#FFFFFF] px-space-md py-space-xs rounded-full shadow-[0_8px_24px_rgba(10,10,10,0.08)] border border-[#0A0A0A]/10 flex items-center gap-space-xs z-30">

<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>

<span class="font-label-mono text-label-mono text-[#0A0A0A] uppercase tracking-wider">Call or WhatsApp</span>

</div>

</div>

</div>

</div>

</section>

<!-- 12-COLUMN BENTO CONTACT GRID -->

<section class="w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin pb-space-3xl">

<div class="grid grid-cols-1 md:grid-cols-12 gap-4">

<!-- Card 1: Orange Card (4 Columns) -->

<div class="md:col-span-4 bg-[#FB6221] rounded-[24px] p-8 flex flex-col justify-between min-h-[340px] text-white transition-transform duration-300 hover:-translate-y-1 shadow-[0_12px_32px_rgba(251,98,33,0.25)] relative overflow-hidden group">

<div class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full bg-white/10 pointer-events-none blur-xl"></div>

<div>

<div class="flex items-center justify-between mb-space-md">

<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#0A0A0A] font-semibold bg-white/20 px-3 py-1 rounded-full">

&#x20;             Direct Line

&#x20;           </span>

<span class="material-symbols-outlined text-white text-[22px]">call</span>

</div>

<h2 class="font-headline-lg text-headline-lg uppercase text-white tracking-tight mb-2">CALL US</h2>

<div class="font-headline-xl text-3xl sm:text-4xl text-white tracking-tight font-normal my-2">

&#x20;           +91 8506 888 288

&#x20;         </div>

<p class="font-body-md text-body-md text-[#0A0A0A] font-medium mt-1">

&#x20;           Call our team to ask about courses, fees and batches.

&#x20;         </p>

</div>

<div class="pt-6 mt-4 border-t border-white/20 flex items-center justify-between">

<a class="inline-flex items-center justify-center bg-[#0A0A0A] text-white px-8 py-3.5 rounded-full font-label-mono text-label-mono uppercase tracking-wider font-semibold transition-transform hover:scale-[1.03]" href="tel:+918506888288">

&#x20;           Call now

&#x20;         </a>

</div>

</div>

<!-- Card 2: Black Card (4 Columns) -->

<div class="md:col-span-4 bg-[#0A0A0A] rounded-[24px] p-8 flex flex-col justify-between min-h-[340px] text-white transition-transform duration-300 hover:-translate-y-1 shadow-[0_16px_36px_rgba(10,10,10,0.18)] border border-white/10 relative overflow-hidden">

<div>

<div class="flex items-center justify-between mb-space-md">

<span class="font-label-mono text-label-mono uppercase tracking-widest text-white/70 bg-white/10 px-3 py-1 rounded-full">

&#x20;             Instant Messaging

&#x20;           </span>

<span class="material-symbols-outlined text-[#FB6221] text-[22px]">chat</span>

</div>

<h2 class="font-headline-lg text-headline-lg uppercase text-white tracking-tight mb-2">WHATSAPP</h2>

<div class="font-headline-xl text-3xl sm:text-4xl text-white tracking-tight font-normal my-2">

&#x20;           +91 72778 77778

&#x20;         </div>

<p class="font-body-md text-body-md text-[#D3D6DA]/70 mt-1">

&#x20;           Message us for demo, fees and batch details.

&#x20;         </p>

</div>

<div class="pt-6 mt-4 border-t border-white/10 flex items-center justify-between">

<a class="inline-flex items-center justify-center bg-[#FB6221] text-white px-8 py-3.5 rounded-full font-label-mono text-label-mono uppercase tracking-wider font-semibold transition-transform hover:scale-[1.03] shadow-[0_6px_20px_rgba(251,98,33,0.35)]" href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo." rel="noopener noreferrer" target="_blank">

&#x20;           Start chat

&#x20;         </a>

</div>

</div>

<!-- Card 3: Soft Gray Card (4 Columns) -->

<div class="md:col-span-4 bg-[#D3D6DA] rounded-[24px] p-8 flex flex-col justify-between min-h-[340px] text-[#0A0A0A] transition-transform duration-300 hover:-translate-y-1 border border-[#0A0A0A]/5">

<div>

<div class="flex items-center justify-between mb-space-md">

<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#0A0A0A]/80 bg-[#FFFFFF]/60 px-3 py-1 rounded-full">

&#x20;             Written Enquiries

&#x20;           </span>

<span class="material-symbols-outlined text-[#0A0A0A] text-[22px]">alternate_email</span>

</div>

<h2 class="font-headline-lg text-headline-lg uppercase text-[#0A0A0A] tracking-tight mb-2">EMAIL</h2>

<div class="font-headline-sm text-2xl sm:text-[26px] text-[#0A0A0A] tracking-tight font-normal my-2 break-all">

&#x20;           enquiry@zeblearn.com

&#x20;         </div>

<p class="font-body-md text-body-md text-[#0A0A0A]/70 mt-1">

&#x20;           Write to us for syllabus and course details.

&#x20;         </p>

</div>

<div class="pt-6 mt-4 border-t border-[#0A0A0A]/10 flex items-center justify-between">

<button class="inline-flex items-center justify-center bg-[#FFFFFF] text-[#0A0A0A] border border-[#0A0A0A]/15 px-8 py-3.5 rounded-full font-label-mono text-label-mono uppercase tracking-wider font-semibold transition-all hover:bg-[#0A0A0A] hover:text-white cursor-pointer" id="copy-email-btn" onclick="navigator.clipboard.writeText('enquiry@zeblearn.com'); this.innerText='COPIED!';">

&#x20;           Copy email

&#x20;         </button>

</div>

</div>

<!-- Card 4: Office Hours (4 Columns White) -->

<div class="md:col-span-4 bg-[#FFFFFF] rounded-[24px] p-8 flex flex-col justify-between min-h-[300px] text-[#0A0A0A] border border-[#0A0A0A]/5 shadow-[0_4px_16px_rgba(10,10,10,0.03)]">

<div>

<div class="flex items-center justify-between mb-space-md">

<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#0A0A0A]/70 bg-[#E4E6E9] px-3 py-1 rounded-full">

&#x20;             Format

&#x20;           </span>

<span class="material-symbols-outlined text-[#FB6221] text-[22px]">language</span>

</div>

<h2 class="font-headline-lg text-headline-lg uppercase text-[#0A0A0A] tracking-tight mb-2">LIVE ONLINE</h2>

<div class="font-body-xl text-body-xl font-medium text-[#0A0A0A] mt-2">

&#x20;           SAP FICO, MM, SD and PP

&#x20;         </div>

<p class="font-body-md text-body-md text-[#0A0A0A]/70 mt-3 leading-relaxed">

&#x20;           All ZebLearn courses run as live online classes, so you can join from anywhere.

&#x20;         </p>

</div>

<div class="pt-6 border-t border-[#0A0A0A]/10 flex items-center gap-2 font-label-mono text-label-mono text-[#0A0A0A]/60">

<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>

<span>3 MONTH COURSES</span>

</div>

</div>

<!-- Card 5: Free demo -->

<div class="md:col-span-8 bg-[#FFFFFF] rounded-[24px] p-8 flex flex-col lg:flex-row gap-6 border border-[#0A0A0A]/5 shadow-[0_4px_16px_rgba(10,10,10,0.03)] justify-between">

<div class="flex flex-col justify-between lg:w-1/2">

<div>

<div class="flex items-center gap-2 mb-space-md">

<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#0A0A0A]/70 bg-[#E4E6E9] px-3 py-1 rounded-full">Free Demo</span>

<span class="font-label-mono text-label-mono uppercase text-[#FB6221] font-semibold">• Live Online</span>

</div>

<h2 class="font-headline-lg text-headline-lg uppercase text-[#0A0A0A] tracking-tight mb-2">BOOK A FREE DEMO</h2>

<p class="font-body-md text-body-md text-[#0A0A0A]/90 mt-2 font-medium">Join a live demo class and see how our online SAP classes work.</p>

<p class="font-body-md text-body-md text-[#0A0A0A]/60 mt-2">Courses: SAP FICO, MM, SD and PP · 3 months · ₹25,000 + GST</p>

</div>

<div class="pt-6 mt-4 border-t border-[#0A0A0A]/10 flex items-center gap-4">

<a class="inline-flex items-center justify-center bg-[#0A0A0A] text-white px-8 py-3.5 rounded-full font-label-mono text-label-mono uppercase tracking-wider font-semibold transition-transform hover:scale-[1.03]" href="/book-demo">Book a demo</a>

</div>

</div>

<div class="lg:w-1/2 min-h-[220px] rounded-[18px] bg-[#0A0A0A] text-white p-6 flex flex-col justify-between">

<span class="font-label-mono text-label-mono uppercase tracking-widest text-white/60">Learners trained</span>

<div class="grid grid-cols-2 gap-4 mt-4">

<div><div class="font-headline-lg text-headline-lg text-[#FB6221]">1,850+</div><div class="font-label-mono text-label-mono uppercase text-white/70">SAP FICO</div></div>

<div><div class="font-headline-lg text-headline-lg text-[#FB6221]">1,650+</div><div class="font-label-mono text-label-mono uppercase text-white/70">SAP MM</div></div>

<div><div class="font-headline-lg text-headline-lg text-[#FB6221]">1,057+</div><div class="font-label-mono text-label-mono uppercase text-white/70">SAP SD</div></div>

<div><div class="font-headline-lg text-headline-lg text-[#FB6221]">974+</div><div class="font-label-mono text-label-mono uppercase text-white/70">SAP PP</div></div>

</div>

</div>

</div>

</div>

</section>

<!-- FORM CARD SECTION -->

<section class="w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin pb-space-3xl">

<div class="bg-[#FFFFFF] rounded-[24px] p-8 md:p-12 border border-[#0A0A0A]/5 shadow-[0_8px_30px_rgba(10,10,10,0.04)] grid grid-cols-1 lg:grid-cols-12 gap-10">

<!-- Left Column: Title & Reassurance list -->

<div class="lg:col-span-5 flex flex-col justify-between">

<div>

<div class="inline-flex items-center gap-2 mb-space-md">

<span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>

<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#0A0A0A]/70">

&#x20;             Get In Touch

&#x20;           </span>

</div>

<h2 class="font-headline-xl text-4xl sm:text-5xl lg:text-6xl uppercase tracking-tight text-[#0A0A0A] leading-none mb-space-md">

&#x20;           SEND US A<br/>MESSAGE

&#x20;         </h2>

<p class="font-body-lg text-body-lg text-[#0A0A0A]/75 mb-space-xl leading-relaxed">

&#x20;           Fill in your details and our team will get back to you about courses, fees and the next demo class.

&#x20;         </p>

<!-- Reassurance check lines -->

<div class="space-y-space-md pt-space-md border-t border-[#0A0A0A]/10">

<div class="flex items-start gap-3">

<div class="w-5 h-5 rounded-full bg-[#FB6221]/15 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5">

<span class="material-symbols-outlined text-[14px] font-bold">check</span>

</div>

<div>

<p class="font-body-md text-body-md font-semibold text-[#0A0A0A]">Talk to our team</p>

<p class="font-label-mono text-label-mono text-[#0A0A0A]/60">Get answers about courses, fees and batches.</p>

</div>

</div>

<div class="flex items-start gap-3">

<div class="w-5 h-5 rounded-full bg-[#FB6221]/15 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5">

<span class="material-symbols-outlined text-[14px] font-bold">check</span>

</div>

<div>

<p class="font-body-md text-body-md font-semibold text-[#0A0A0A]">Clear course fees</p>

<p class="font-label-mono text-label-mono text-[#0A0A0A]/60">SAP FICO, MM, SD and PP: ₹25,000 + GST.</p>

</div>

</div>

<div class="flex items-start gap-3">

<div class="w-5 h-5 rounded-full bg-[#FB6221]/15 text-[#FB6221] flex items-center justify-center shrink-0 mt-0.5">

<span class="material-symbols-outlined text-[14px] font-bold">check</span>

</div>

<div>

<p class="font-body-md text-body-md font-semibold text-[#0A0A0A]">Free demo class</p>

<p class="font-label-mono text-label-mono text-[#0A0A0A]/60">Ask us about the next demo session.</p>

</div>

</div>

</div>

</div>

</div>

<!-- Right Column: Interactive Form -->

<div class="lg:col-span-7">

<form class="space-y-5" id="contactForm">

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

<!-- Full Name -->

<div class="space-y-1.5">

<label class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider block">Full Name</label>

<input autocomplete="name" class="w-full bg-[#FFFFFF] border border-[#0A0A0A]/15 rounded-xl px-5 py-4 text-[#0A0A0A] font-body-md text-body-md placeholder-[#0A0A0A]/40 focus:outline-none focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all" name="name" placeholder="Your name" required="required" type="text"/>

</div>

<!-- Phone (+91) -->

<div class="space-y-1.5">

<label class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider block">Phone Number (+91) \*</label>

<div class="relative">

<span class="absolute left-4 top-1/2 -translate-y-1/2 font-label-mono text-label-mono text-[#0A0A0A]/60">+91</span>

<input autocomplete="tel" class="w-full bg-[#FFFFFF] border border-[#0A0A0A]/15 rounded-xl pl-14 pr-5 py-4 text-[#0A0A0A] font-body-md text-body-md placeholder-[#0A0A0A]/40 focus:outline-none focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all" inputmode="tel" maxlength="15" name="phone" pattern="[0-9]{10}" placeholder="98765 43210" required="required" type="tel"/>

</div>

<span class="font-label-mono text-[10px] text-red-600 block">Required so our team can call you back</span>

</div>

</div>

<!-- Email Address -->

<div class="space-y-1.5">

<label class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider block">Email Address</label>

<input autocomplete="email" class="w-full bg-[#FFFFFF] border border-[#0A0A0A]/15 rounded-xl px-5 py-4 text-[#0A0A0A] font-body-md text-body-md placeholder-[#0A0A0A]/40 focus:outline-none focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all" name="email" placeholder="you@example.com" required="required" type="email"/>

</div>

<!-- Dropdown Course -->

<div class="space-y-1.5">

<label class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider block">Course Interested In \*</label>

<div class="relative">

<select class="w-full bg-[#FFFFFF] border border-[#0A0A0A]/15 rounded-xl px-5 py-4 text-[#0A0A0A] font-body-md text-body-md focus:outline-none focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] appearance-none transition-all cursor-pointer" name="course" required="required">

<option disabled="" selected="" value="">Select a course...</option>

<option value="SAP FICO">SAP FICO</option>

<option value="SAP MM">SAP MM</option>

<option value="SAP SD">SAP SD</option>

<option value="SAP PP">SAP PP</option>

<option value="SAP Power User Certification">SAP Power User Certification</option>

<option value="Not sure yet">Not sure yet</option>

</select>

<span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-[#0A0A0A]/60">

&#x20;               unfold_more

&#x20;             </span>

</div>

</div>

<!-- Message -->

<div class="space-y-1.5">

<label class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider block">Your Message (Optional)</label>

<textarea class="w-full bg-[#FFFFFF] border border-[#0A0A0A]/15 rounded-xl px-5 py-4 text-[#0A0A0A] font-body-md text-body-md placeholder-[#0A0A0A]/40 focus:outline-none focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] transition-all resize-none" name="message" placeholder="Tell us about your background or any question you have..." rows="3"></textarea>

</div>

<!-- Submit Bar -->

<div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">

<button class="w-full sm:w-auto inline-flex items-center justify-center bg-[#0A0A0A] text-white px-10 py-4 rounded-full font-label-mono text-label-mono uppercase tracking-wider font-semibold transition-all hover:bg-[#FB6221] hover:scale-[1.02] cursor-pointer shadow-md" type="submit">

&#x20;             Send on WhatsApp

&#x20;           </button>

<span class="font-label-mono text-label-mono text-[#0A0A0A]/50 text-center sm:text-right">

&#x20;             Your details open in WhatsApp so our team can reply.

&#x20;           </span>

</div>

</form>

</div>

</div>

</section>

<!-- QUICK ANSWERS ACCORDION -->

<section class="w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin pb-space-3xl">

<div class="mb-space-lg flex items-center justify-between">

<div>

<span class="font-label-mono text-label-mono uppercase text-[#FB6221] tracking-widest block mb-1">FAQ</span>

<h2 class="font-headline-xl text-4xl sm:text-5xl uppercase tracking-tight text-[#0A0A0A]">

&#x20;         QUICK ANSWERS

&#x20;       </h2>

</div>

</div>

<!-- Accordion Wrapper Card -->

<div class="bg-[#FFFFFF] rounded-[24px] border border-[#0A0A0A]/5 shadow-[0_4px_20px_rgba(10,10,10,0.03)] overflow-hidden">

<!-- Row 1: Expanded -->

<div class="border-b border-[#0A0A0A]/10 p-6 sm:p-8 transition-colors">

<details class="group" open="">

<summary class="flex items-center justify-between cursor-pointer list-none select-none">

<div class="flex items-center gap-4">

<span class="font-label-mono text-label-mono text-[#0A0A0A]/40 font-semibold">01</span>

<h3 class="font-body-xl text-body-xl font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">

&#x20;               What are the course fees?

&#x20;             </h3>

</div>

<div class="w-9 h-9 rounded-full bg-[#FB6221] text-white flex items-center justify-center shrink-0 transition-transform group-open:rotate-45">

<span class="material-symbols-outlined text-[20px]">add</span>

</div>

</summary>

<div class="mt-4 pt-4 border-t border-[#0A0A0A]/5 pl-8 sm:pl-10 max-w-4xl text-[#0A0A0A]/80 font-body-md text-body-md leading-relaxed">

<p>

&#x20;             The instructor-led SAP FICO, MM, SD and PP courses are <strong>₹25,000 + GST</strong> each. The SAP Power User Certification course is <strong>₹40,000 + GST</strong>. Call or WhatsApp us for payment details.

&#x20;           </p>

</div>

</details>

</div>

<!-- Row 2 -->

<div class="border-b border-[#0A0A0A]/10 p-6 sm:p-8 transition-colors">

<details class="group">

<summary class="flex items-center justify-between cursor-pointer list-none select-none">

<div class="flex items-center gap-4">

<span class="font-label-mono text-label-mono text-[#0A0A0A]/40 font-semibold">02</span>

<h3 class="font-body-xl text-body-xl font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">

&#x20;               Can I attend a free demo session before paying?

&#x20;             </h3>

</div>

<div class="w-9 h-9 rounded-full bg-[#FB6221] text-white flex items-center justify-center shrink-0 transition-transform group-open:rotate-45">

<span class="material-symbols-outlined text-[20px]">add</span>

</div>

</summary>

<div class="mt-4 pt-4 border-t border-[#0A0A0A]/5 pl-8 sm:pl-10 max-w-4xl text-[#0A0A0A]/80 font-body-md text-body-md leading-relaxed">

<p>

&#x20;             Yes. Book a free demo class and our team will share the next demo session with you.

&#x20;           </p>

</div>

</details>

</div>

<!-- Row 3 -->

<div class="border-b border-[#0A0A0A]/10 p-6 sm:p-8 transition-colors">

<details class="group">

<summary class="flex items-center justify-between cursor-pointer list-none select-none">

<div class="flex items-center gap-4">

<span class="font-label-mono text-label-mono text-[#0A0A0A]/40 font-semibold">03</span>

<h3 class="font-body-xl text-body-xl font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">

&#x20;               What are the batch timings?

&#x20;             </h3>

</div>

<div class="w-9 h-9 rounded-full bg-[#FB6221] text-white flex items-center justify-center shrink-0 transition-transform group-open:rotate-45">

<span class="material-symbols-outlined text-[20px]">add</span>

</div>

</summary>

<div class="mt-4 pt-4 border-t border-[#0A0A0A]/5 pl-8 sm:pl-10 max-w-4xl text-[#0A0A0A]/80 font-body-md text-body-md leading-relaxed">

<p>

&#x20;             Batch timings change from batch to batch. Call +91 8506 888 288 or message us on WhatsApp to ask about the next batch.

&#x20;           </p>

</div>

</details>

</div>

<!-- Row 4 -->

<div class="p-6 sm:p-8 transition-colors">

<details class="group">

<summary class="flex items-center justify-between cursor-pointer list-none select-none">

<div class="flex items-center gap-4">

<span class="font-label-mono text-label-mono text-[#0A0A0A]/40 font-semibold">04</span>

<h3 class="font-body-xl text-body-xl font-semibold text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">

&#x20;               How long are the courses?

&#x20;             </h3>

</div>

<div class="w-9 h-9 rounded-full bg-[#FB6221] text-white flex items-center justify-center shrink-0 transition-transform group-open:rotate-45">

<span class="material-symbols-outlined text-[20px]">add</span>

</div>

</summary>

<div class="mt-4 pt-4 border-t border-[#0A0A0A]/5 pl-8 sm:pl-10 max-w-4xl text-[#0A0A0A]/80 font-body-md text-body-md leading-relaxed">

<p>

&#x20;             The instructor-led SAP FICO, MM, SD and PP courses run for 3 months in live online classes.

&#x20;           </p>

</div>

</details>

</div>

</div>

</section>

<!-- INFO STRIP -->

<section class="w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin pb-space-3xl">

<div class="bg-[#D3D6DA] rounded-[24px] p-6 sm:p-8 border border-[#0A0A0A]/5 flex flex-col lg:flex-row items-center justify-between gap-6">

<div class="flex flex-wrap items-center gap-4 sm:gap-6 justify-center lg:justify-start">

<div class="flex items-center gap-2 bg-[#FFFFFF]/70 px-3.5 py-2 rounded-xl border border-[#0A0A0A]/10"><span class="material-symbols-outlined text-[18px] text-[#0A0A0A]">videocam</span><span class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider font-semibold">Live Online</span></div><div class="flex items-center gap-2 bg-[#FFFFFF]/70 px-3.5 py-2 rounded-xl border border-[#0A0A0A]/10"><span class="material-symbols-outlined text-[18px] text-[#0A0A0A]">schedule</span><span class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider font-semibold">3 Month Courses</span></div><div class="flex items-center gap-2 bg-[#FFFFFF]/70 px-3.5 py-2 rounded-xl border border-[#0A0A0A]/10"><span class="material-symbols-outlined text-[18px] text-[#FB6221]">work</span><span class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider font-semibold">Placement Support</span></div>

</div>

<div class="flex items-center gap-6 font-label-mono text-label-mono uppercase text-[#0A0A0A]/70">

<a class="hover:text-[#0A0A0A] transition-colors underline decoration-[#0A0A0A]/30 underline-offset-4" href="https://sap.zeblearn.com/refund-policy/" rel="noopener" target="_blank">Refund Policy</a>

<a class="hover:text-[#0A0A0A] transition-colors underline decoration-[#0A0A0A]/30 underline-offset-4" href="https://sap.zeblearn.com/disclaimer-policy-trademark/" rel="noopener" target="_blank">Privacy Policy &amp; Terms</a>

</div>

</div>

</section>



</div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
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
@endsection
