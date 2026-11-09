@extends('layouts.app')

@section('title', 'Book a Free SAP Demo Class | ZebLearn')
@section('description', 'Book a free live demo class for SAP FICO, MM, SD or PP. See how ZebLearn live online SAP classes work before you decide.')
@section('body-class', 'bg-surface text-on-surface font-body-md text-body-md antialiased min-h-screen selection:bg-primary-container selection:text-on-primary zeb-page')
@section('og_type', 'website')
@section('og_title', 'Book a Free SAP Demo Class | ZebLearn')
@section('og_description', 'Book a free live demo class for SAP FICO, MM, SD or PP. See how ZebLearn live online SAP classes work before you decide.')
@section('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')
@section('twitter_title', 'Book a Free SAP Demo Class | ZebLearn')
@section('twitter_description', 'Book a free live demo class for SAP FICO, MM, SD or PP. See how ZebLearn live online SAP classes work before you decide.')
@section('robots', 'index,follow,max-image-preview:large')

@push('head')
<script type="application/ld+json">{"@@context":"https://schema.org","@@type":"WebPage","name":"Book a Free SAP Demo Class","publisher":{"@@type":"EducationalOrganization","name":"ZebLearn","url":"https://sap.zeblearn.com"}}</script>
@endpush

@push('scripts')
<script>
(function(){
  var bgVal='Working professional';
  document.querySelectorAll('#bg-selector button').forEach(function(pill){
    pill.addEventListener('click',function(){
      document.querySelectorAll('#bg-selector button').forEach(function(p){p.classList.remove('bg-[#0A0A0A]','text-white','shadow-sm');p.classList.add('bg-[#E4E6E9]','text-[#0A0A0A]');});
      pill.classList.remove('bg-[#E4E6E9]','text-[#0A0A0A]');pill.classList.add('bg-[#0A0A0A]','text-white','shadow-sm');
      bgVal=pill.textContent.trim();
    });
  });
  var days=['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],mons=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var dateWrap=document.getElementById('date-selector'),dateVals=[],selDate=0;
  for(var i=1;i<=6;i++){
    var d=new Date();d.setDate(d.getDate()+i);
    dateVals.push(days[d.getDay()]+' '+d.getDate()+' '+mons[d.getMonth()]);
    var b=document.createElement('button');b.type='button';
    b.className='date-chip flex flex-col items-center justify-center p-2.5 rounded-xl cursor-pointer transition-all '+(i===1?'bg-[#0A0A0A] text-white shadow-sm':'bg-white text-[#0A0A0A] hover:bg-[#D3D6DA]');
    b.innerHTML='<span class="font-label-mono text-[10px] uppercase font-medium '+(i===1?'opacity-80':'text-[#0A0A0A]/60')+'">'+days[d.getDay()]+'</span><span class="font-headline-sm text-headline-sm leading-none my-0.5">'+d.getDate()+'</span><span class="font-label-mono text-[10px] uppercase font-bold">'+mons[d.getMonth()]+'</span>';
    (function(idx,el){el.addEventListener('click',function(){
      selDate=idx;
      dateWrap.querySelectorAll('.date-chip').forEach(function(c,j){
        var on=j===idx;
        c.classList.toggle('bg-[#0A0A0A]',on);c.classList.toggle('text-white',on);c.classList.toggle('shadow-sm',on);
        c.classList.toggle('bg-white',!on);c.classList.toggle('text-[#0A0A0A]',!on);
        var s=c.querySelector('span');s.classList.toggle('opacity-80',on);s.classList.toggle('text-[#0A0A0A]/60',!on);
      });
    });})(i-1,b);
    dateWrap.appendChild(b);
  }
  var slotChips=document.querySelectorAll('#slot-selector .slot-chip'),selSlot='07:00 PM';
  slotChips.forEach(function(chip){chip.addEventListener('click',function(){
    slotChips.forEach(function(s){s.classList.remove('bg-[#FB6221]','text-white','shadow-md');s.classList.add('bg-white','text-[#0A0A0A]');});
    chip.classList.remove('bg-white','text-[#0A0A0A]');chip.classList.add('bg-[#FB6221]','text-white','shadow-md');
    selSlot=chip.textContent.trim();
  });});
  document.getElementById('demoForm').addEventListener('submit',function(e){
    e.preventDefault();
    var sel=document.getElementById('course-select'),course=sel.options[sel.selectedIndex].text;
    var name=document.getElementById('full-name').value.trim(),phone=document.getElementById('phone-number').value.trim(),email=document.getElementById('email-address').value.trim();
    var msg='Hello ZebLearn, I want to book a free demo.\nName: '+name+'\nPhone: +91 '+phone+(email?'\nEmail: '+email:'')+'\nCourse: '+course+'\nBackground: '+bgVal+'\nPreferred date: '+dateVals[selDate]+'\nPreferred time: '+selSlot;
    window.open('https://wa.me/917277877778?text='+encodeURIComponent(msg),'_blank');
    setTimeout(function(){location.href='/book-demo/thank-you?module='+encodeURIComponent(course)+'&date='+encodeURIComponent(dateVals[selDate])+'&time='+encodeURIComponent(selSlot);},600);
  });
})();
</script>
<script>document.addEventListener('DOMContentLoaded', function () {
  const menuButton = document.getElementById('menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  if (!menuButton || !mobileMenu) return;
  const closeMenu = () => {
    mobileMenu.classList.add('hidden');
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Open navigation menu');
  };
  menuButton.addEventListener('click', () => {
    const isOpen = !mobileMenu.classList.contains('hidden');
    mobileMenu.classList.toggle('hidden');
    menuButton.setAttribute('aria-expanded', String(!isOpen));
    menuButton.setAttribute('aria-label', isOpen ? 'Open navigation menu' : 'Close navigation menu');
  });
  mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
  document.addEventListener('click', event => {
    if (!mobileMenu.contains(event.target) && !menuButton.contains(event.target)) closeMenu();
  });
});</script>
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#E4E6E9] text-[#0A0A0A]">
<!-- TOP SECTION / EDITORIAL HEADER -->
<section class="w-full pt-10 pb-12 px-margin-mobile md:px-margin max-w-[1440px] mx-auto overflow-hidden">
<!-- Dot Meta Tracker -->
<div class="flex items-center gap-space-sm mb-6">
<span class="inline-block w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
<span class="font-label-mono text-label-mono uppercase tracking-[0.2em] text-[#0A0A0A] font-semibold">Free demo</span>
</div>
<!-- Massive Condensed Editorial Display Headline -->
<div class="w-full select-none">
<h1 class="font-display-xl text-[64px] sm:text-[104px] md:text-[130px] lg:text-[150px] leading-[0.88] tracking-[-0.03em] uppercase text-[#0A0A0A] font-normal m-0 whitespace-normal break-words">
        SIT IN ON A LIVE CLASS. FREE.
      </h1>
</div>
<!-- Sub-Headline Statement & Operational Stamp -->
<div class="mt-8 pt-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
<p class="font-body-xl text-body-xl text-[#0A0A0A] max-w-2xl font-normal leading-snug">
        Join a free demo class and see how our live online SAP classes work before you decide.
      </p>
<div class="flex items-center gap-4 bg-[#D3D6DA] px-5 py-2.5 rounded-full self-start md:self-auto shadow-sm">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-[#0A0A0A] font-semibold">LIVE ONLINE CLASSES</span>
<span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
</div>
</div>
</section>
<!-- SPLIT BOOKING INTERFACE (12 COLUMNS, 16PX GAP) -->
<section class="w-full pb-16 px-margin-mobile md:px-margin max-w-[1440px] mx-auto">
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
<!-- LEFT 6 COLUMNS: ORANGE MONOLITH MODULE (#FB6221) -->
<div class="lg:col-span-6 bg-[#FB6221] rounded-3xl p-8 sm:p-10 flex flex-col justify-between shadow-lg text-[#0A0A0A] relative overflow-hidden">
<!-- Structural Backdrop Watermark -->
<div class="absolute -right-8 -bottom-10 pointer-events-none select-none opacity-10 font-display-xl text-[180px] leading-none text-white tracking-tighter">
          SAP
        </div>
<div>
<!-- Header Inside Card: Large text white -->
<div class="flex items-center justify-between gap-4 mb-8">
<span class="font-label-mono text-label-mono uppercase tracking-[0.2em] text-[#0A0A0A] font-bold bg-white/20 px-3 py-1 rounded-full">DEMO CLASS</span>
<span class="font-label-mono text-label-mono uppercase text-[#0A0A0A] tracking-wider font-semibold">NO OBLIGATION</span>
</div>
<h2 class="font-headline-xl text-[38px] sm:text-[46px] leading-[0.95] tracking-tight uppercase text-white font-normal mb-8">
            WHAT HAPPENS IN YOUR DEMO
          </h2>
<!-- Numbered Technical Breakdown: Large text white, small text black -->
<div class="space-y-6">
<!-- 01 -->
<div class="bg-white/10 rounded-2xl p-5 backdrop-blur-sm shadow-sm transition-transform hover:translate-x-1">
<div class="flex items-baseline gap-3 mb-1.5">
<span class="font-label-mono text-label-mono font-bold text-white tracking-widest">01 //</span>
<h3 class="font-headline-sm text-headline-sm uppercase text-white tracking-wide">LIVE DEMO SESSION</h3>
</div>
<p class="font-body-md text-body-md text-[#0A0A0A] font-medium leading-relaxed">
                See how a live SAP class works, with a trainer and practice on a real SAP system.
              </p>
</div>
<!-- 02 -->
<div class="bg-white/10 rounded-2xl p-5 backdrop-blur-sm shadow-sm transition-transform hover:translate-x-1">
<div class="flex items-baseline gap-3 mb-1.5">
<span class="font-label-mono text-label-mono font-bold text-white tracking-widest">02 //</span>
<h3 class="font-headline-sm text-headline-sm uppercase text-white tracking-wide">MEET THE TRAINER</h3>
</div>
<p class="font-body-md text-body-md text-[#0A0A0A] font-medium leading-relaxed">
                Ask your questions directly to the trainer and get your doubts cleared.
              </p>
</div>
<!-- 03 -->
<div class="bg-white/10 rounded-2xl p-5 backdrop-blur-sm shadow-sm transition-transform hover:translate-x-1">
<div class="flex items-baseline gap-3 mb-1.5">
<span class="font-label-mono text-label-mono font-bold text-white tracking-widest">03 //</span>
<h3 class="font-headline-sm text-headline-sm uppercase text-white tracking-wide">PERSONAL ROADMAP</h3>
</div>
<p class="font-body-md text-body-md text-[#0A0A0A] font-medium leading-relaxed">
                Our team helps you pick the right module (FICO, MM, SD or PP) based on your background.
              </p>
</div>
</div>
</div>
<!-- Authentic Photo Card & Verified Testimonial -->
<div class="mt-8 space-y-4">
<div class="relative rounded-2xl overflow-hidden shadow-md bg-[#0A0A0A] h-40 sm:h-48 flex items-center justify-center">
<span class="font-display-xl text-[64px] sm:text-[84px] text-white/90 leading-none uppercase select-none">LIVE ONLINE</span>
<div class="absolute bottom-3 left-3"><span class="bg-white text-[#0A0A0A] font-label-mono text-label-mono uppercase tracking-wider px-3 py-1.5 rounded-full shadow-sm flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>FICO · MM · SD · PP</span></div>
</div>
<!-- Alumni Endorsement Quote Block -->
<div class="bg-white p-5 rounded-2xl shadow-sm text-[#0A0A0A]">
<div class="flex items-center gap-1.5 mb-2"><span class="font-label-mono text-[10px] uppercase font-bold text-[#0A0A0A]/60">STUDENT STORY</span></div>
<blockquote class="font-body-md text-body-md text-[#0A0A0A] font-medium italic mb-2 leading-snug">
              “It is due to the quality training provided here that I got placed in Accenture. The atmosphere is brilliant and everyone is studying hard.”
            </blockquote>
<div class="flex items-center justify-between font-label-sm text-label-sm">
<span class="font-bold text-[#0A0A0A]">Sumit Singh</span>
<span class="text-[#0A0A0A]/70 font-label-mono text-label-mono uppercase">Placed at Accenture</span>
</div>
</div>
</div>
</div>
<!-- RIGHT 6 COLUMNS: WHITE BOOKING ENGINE (#FFFFFF) -->
<form class="lg:col-span-6 bg-white rounded-3xl p-8 sm:p-10 shadow-lg text-[#0A0A0A] flex flex-col justify-between" id="demoForm">
<div>
<!-- Header and Progress Status -->
<div class="flex items-center justify-between pb-6 mb-6">
<span class="inline-flex items-center gap-2 bg-[#E4E6E9] px-4 py-1.5 rounded-full font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold">
<span class="w-2 h-2 rounded-full bg-[#0A0A0A]"></span>
              Your Details
            </span>
</div>
<!-- STEP 1: CANDIDATE PROFILE FORM -->
<div class="space-y-4 mb-8">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Full Name -->
<div class="flex flex-col gap-1.5">
<label class="font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold" for="full-name">
                  Full Name <span class="text-[#FB6221]">*</span>
</label>
<input autocomplete="name" class="w-full bg-[#E4E6E9]/60 hover:bg-[#E4E6E9] focus:bg-white text-[#0A0A0A] font-body-md text-body-md px-4 py-3.5 rounded-xl outline-none transition-colors placeholder-[#0A0A0A]/35" id="full-name" name="name" placeholder="Your name" required="required" type="text"/>
</div>
<!-- Phone Number (+91) -->
<div class="flex flex-col gap-1.5">
<label class="font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold" for="phone-number">
                  Phone (+91) <span class="text-[#FB6221]">*</span>
</label>
<div class="flex items-center bg-[#E4E6E9]/60 rounded-xl px-4 py-3.5 focus-within:bg-white transition-colors">
<span class="font-label-mono text-label-mono font-bold text-[#0A0A0A] mr-2">+91</span>
<input autocomplete="tel" class="w-full bg-transparent text-[#0A0A0A] font-body-md text-body-md outline-none placeholder-[#0A0A0A]/35" id="phone-number" inputmode="tel" maxlength="15" name="phone" pattern="[0-9]{10}" placeholder="98765 43210" required="required" type="tel"/>
</div>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<!-- Email Address -->
<div class="flex flex-col gap-1.5">
<label class="font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold" for="email-address">
                  Email Address
</label>
<input autocomplete="email" class="w-full bg-[#E4E6E9]/60 hover:bg-[#E4E6E9] focus:bg-white text-[#0A0A0A] font-body-md text-body-md px-4 py-3.5 rounded-xl outline-none transition-colors placeholder-[#0A0A0A]/35" id="email-address" name="email" placeholder="you@example.com" type="email"/>
</div>
<!-- Course Interested In Dropdown -->
<div class="flex flex-col gap-1.5">
<label class="font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold" for="course-select">
                  Course Interested In <span class="text-[#FB6221]">*</span>
</label>
<div class="relative">
<select class="w-full appearance-none bg-[#E4E6E9]/60 hover:bg-[#E4E6E9] focus:bg-white text-[#0A0A0A] font-body-md text-body-md px-4 py-3.5 rounded-xl outline-none cursor-pointer transition-colors pr-10 font-medium" id="course-select" name="course" required="required">
<option value="fico">SAP FICO</option>
<option value="mm">SAP MM</option>
<option value="sd">SAP SD</option>
<option value="pp">SAP PP</option>
</select>
<span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#0A0A0A]/60 text-[20px]">expand_more</span>
</div>
</div>
</div>
<!-- Current Background Selector -->
<div class="pt-2">
<label class="block font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold mb-2">
                Current Background
              </label>
<div class="flex flex-wrap gap-2" id="bg-selector">
<button class="bg-[#E4E6E9] text-[#0A0A0A] hover:bg-[#D3D6DA] font-body-md text-body-md font-medium px-4 py-2 rounded-full transition-all cursor-pointer" data-val="student" type="button">
                  Student
                </button>
<button class="bg-[#0A0A0A] text-white font-body-md text-body-md font-medium px-4 py-2 rounded-full transition-all shadow-sm cursor-pointer" data-val="professional" type="button">
                  Working professional
                </button>
<button class="bg-[#E4E6E9] text-[#0A0A0A] hover:bg-[#D3D6DA] font-body-md text-body-md font-medium px-4 py-2 rounded-full transition-all cursor-pointer" data-val="switcher" type="button">
                  Career switcher
                </button>
</div>
</div>
</div>
<!-- STEP 2: DATE & SLOT SELECTION -->
<div class="pt-6 bg-[#E4E6E9]/40 p-6 rounded-2xl mb-8">
<!-- Select Date Label & Chips -->
<div class="mb-5">
<div class="flex items-center justify-between mb-3">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold">
                  Preferred Date
                </span>
</div>
<div class="grid grid-cols-3 sm:grid-cols-6 gap-2" id="date-selector"></div>
</div>
<!-- Preferred Time Label & Chips -->
<div>
<div class="flex items-center justify-between mb-2">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-[#0A0A0A] font-semibold">
                  Preferred Time
                </span>
<span class="font-label-mono text-[10px] text-[#FB6221] font-bold uppercase tracking-wider">TEAM WILL CONFIRM</span>
</div>
<div class="grid grid-cols-3 gap-2" id="slot-selector">
<button class="slot-chip bg-white text-[#0A0A0A] hover:bg-[#D3D6DA] font-body-md text-body-md font-semibold py-2.5 rounded-xl transition-all cursor-pointer text-center" type="button">
                  11:00 AM
                </button>
<button class="slot-chip bg-white text-[#0A0A0A] hover:bg-[#D3D6DA] font-body-md text-body-md font-semibold py-2.5 rounded-xl transition-all cursor-pointer text-center" type="button">
                  04:00 PM
                </button>
<!-- Selected in vivid orange #FB6221 with white text -->
<button class="slot-chip bg-[#FB6221] text-white shadow-md font-body-md text-body-md font-semibold py-2.5 rounded-xl transition-all cursor-pointer text-center" type="button">
                  07:00 PM
                </button>
</div>
</div>
</div>
</div>
<!-- ACTIONS ROW -->
<div class="pt-2 flex flex-col gap-3">
<button class="w-full bg-[#0A0A0A] hover:bg-[#241912] text-white font-body-md text-body-md font-semibold py-4 px-8 rounded-full flex items-center justify-center gap-3 transition-transform hover:scale-[1.01] shadow-md cursor-pointer group" id="confirm-demo-btn" type="submit">
<span>Request my demo</span>
<span class="material-symbols-outlined text-[18px] transform transition-transform group-hover:translate-x-1">arrow_forward</span>
</button>
<div class="text-center pt-1">
<a class="font-body-md text-body-md text-[#0A0A0A]/70 hover:text-[#FB6221] transition-colors font-medium" href="tel:+918506888288">Prefer a direct call? <span class="underline underline-offset-4 font-semibold text-[#0A0A0A]">Call +91 8506 888 288</span></a>
</div>
</div>
</form>
</div>
</section>
<!-- REASSURANCE ROW (THREE ROUNDED-2XL CARDS IN SOFT GRAY #D3D6DA / WHITE) -->
<section class="w-full pb-20 px-margin-mobile md:px-margin max-w-[1440px] mx-auto">
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<!-- Card 1 -->
<div class="bg-white rounded-2xl p-6 sm:p-8 flex items-start gap-4 shadow-sm hover:shadow-md transition-shadow">
<div class="w-12 h-12 rounded-full bg-[#E4E6E9] flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[#0A0A0A] text-[24px]">credit_card_off</span>
</div>
<div>
<div class="flex items-center gap-2 mb-1">
<h4 class="font-headline-sm text-headline-sm uppercase text-[#0A0A0A]">No payment needed</h4>
<span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
</div>
<p class="font-body-md text-body-md text-[#0A0A0A]/80 leading-relaxed font-normal">
            No payment is needed to attend the demo class.
          </p>
</div>
</div>
<!-- Card 2 -->
<div class="bg-[#D3D6DA] rounded-2xl p-6 sm:p-8 flex items-start gap-4 shadow-sm hover:shadow-md transition-shadow">
<div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[#0A0A0A] text-[24px]">chat</span>
</div>
<div>
<div class="flex items-center gap-2 mb-1">
<h4 class="font-headline-sm text-headline-sm uppercase text-[#0A0A0A]">Ask us anything</h4>
<span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
</div>
<p class="font-body-md text-body-md text-[#0A0A0A]/80 leading-relaxed font-normal">
            Message us on WhatsApp or call our team to clear your doubts about courses and fees.
          </p>
</div>
</div>
<!-- Card 3 -->
<div class="bg-white rounded-2xl p-6 sm:p-8 flex items-start gap-4 shadow-sm hover:shadow-md transition-shadow">
<div class="w-12 h-12 rounded-full bg-[#E4E6E9] flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[#0A0A0A] text-[24px]">verified_user</span>
</div>
<div>
<div class="flex items-center gap-2 mb-1">
<h4 class="font-headline-sm text-headline-sm uppercase text-[#0A0A0A]">No obligation to enrol</h4>
<span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
</div>
<p class="font-body-md text-body-md text-[#0A0A0A]/80 leading-relaxed font-normal">
            Attend the demo first, then decide if the course is right for you.
          </p>
</div>
</div>
</div>
</section>

</div>
@endsection
