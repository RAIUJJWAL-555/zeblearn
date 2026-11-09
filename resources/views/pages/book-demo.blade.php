@extends('layouts.app')

@section('title', 'Book a Free SAP Demo Class | ZebLearn')
@section('description', 'Book a free live SAP demo class for SAP FICO, MM, SD or PP. Experience our real-time system training, interactive sessions and placement support before enrolling.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')
@section('og_title', 'Book a Free SAP Demo Class | ZebLearn')
@section('og_description', 'Book a free live SAP demo class for SAP FICO, MM, SD or PP. Experience our real-time system training, interactive sessions and placement support before enrolling.')
@section('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')
@section('twitter_title', 'Book a Free SAP Demo Class | ZebLearn')
@section('twitter_description', 'Book a free live SAP demo class for SAP FICO, MM, SD or PP. Experience our real-time system training, interactive sessions and placement support before enrolling.')
@section('robots', 'index,follow,max-image-preview:large')

@push('head')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Book a Free SAP Demo Class",
  "url": "https://sap.zeblearn.com/book-demo",
  "description": "Book a free live demo class for SAP FICO, MM, SD or PP with live online classes and real system practice at ZebLearn.",
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
      "name": "Book Demo",
      "item": "https://sap.zeblearn.com/book-demo"
    }
  ]
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <!-- HERO / EDITORIAL HEADER -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-28 sm:pt-32 pb-8 sm:pb-12 w-full">
        <!-- Breadcrumb & Telemetry -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200 animate-hero-badge">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="/" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">Book Demo</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">COMPLIMENTARY ACCESS</span>
            </div>
        </div>

        <!-- Huge Display Headline -->
        <div class="mb-8">
            <h1 class="font-heading text-4xl sm:text-6xl md:text-7xl lg:text-[80px] font-extrabold uppercase tracking-tight text-[#0A0A0A] leading-[0.98] select-none animate-hero-title">
                SIT IN ON A LIVE CLASS. <span class="text-[#FB6221]">FREE.</span>
            </h1>
        </div>

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 animate-hero-desc">
            <p class="font-sans text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">
                Join a live demo class and see how our hands-on SAP training works before you make any commitment.
            </p>
            <div class="inline-flex items-center gap-2 bg-white border border-slate-200 px-4 py-2 rounded-full shadow-xs self-start md:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="font-mono text-xs uppercase tracking-wider text-slate-700 font-bold">DAILY LIVE SLOTS AVAILABLE</span>
            </div>
        </div>
    </section>

    <!-- SPLIT BOOKING INTERFACE (12 COLUMNS) -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-16 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT 5 COLUMNS: MONOLITH BREAKDOWN MODULE -->
            <div class="lg:col-span-5 bg-[#0A0A0A] text-white rounded-2xl p-6 sm:p-10 border border-slate-800 shadow-xl flex flex-col justify-between relative overflow-hidden" data-reveal>
                <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-widest text-[#FB6221] bg-white/10 px-3 py-1 rounded-full border border-white/10">
                            DEMO CLASS AGENDA
                        </span>
                        <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider">NO OBLIGATION</span>
                    </div>

                    <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase tracking-tight text-white mb-6">
                        WHAT HAPPENS IN YOUR DEMO
                    </h2>

                    <!-- Numbered Breakdown -->
                    <div class="space-y-4">
                        <!-- 01 -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 transition-transform hover:translate-x-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="font-mono text-xs font-bold text-[#FB6221]">01 //</span>
                                <h3 class="font-heading text-sm sm:text-base font-bold uppercase text-white tracking-tight">LIVE SYSTEM SESSION</h3>
                            </div>
                            <p class="font-sans text-xs text-slate-300 leading-relaxed">
                                See firsthand how live classes are conducted, navigating real SAP transaction screens rather than generic slides.
                            </p>
                        </div>

                        <!-- 02 -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 transition-transform hover:translate-x-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="font-mono text-xs font-bold text-[#FB6221]">02 //</span>
                                <h3 class="font-heading text-sm sm:text-base font-bold uppercase text-white tracking-tight">MEET THE TRAINER</h3>
                            </div>
                            <p class="font-sans text-xs text-slate-300 leading-relaxed">
                                Interact directly with our senior consultant trainer and ask questions about curriculum and real-world projects.
                            </p>
                        </div>

                        <!-- 03 -->
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4 transition-transform hover:translate-x-1">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="font-mono text-xs font-bold text-[#FB6221]">03 //</span>
                                <h3 class="font-heading text-sm sm:text-base font-bold uppercase text-white tracking-tight">PERSONAL ROADMAP</h3>
                            </div>
                            <p class="font-sans text-xs text-slate-300 leading-relaxed">
                                Our team helps you evaluate whether FICO, MM, SD, or PP best leverages your academic and professional background.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alumni Endorsement Quote -->
                <div class="relative z-10 mt-8 pt-6 border-t border-white/10">
                    <div class="flex items-center gap-1 text-[#FB6221] mb-2">
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                    </div>
                    <blockquote class="font-['Inter'] text-xs text-slate-300 italic mb-3 leading-relaxed">
                        “It is due to the quality training provided here that I got placed in Accenture. The atmosphere is brilliant and everyone is studying hard.”
                    </blockquote>
                    <div class="flex items-center justify-between font-mono text-[10px]">
                        <span class="text-white font-bold uppercase">Sumit Singh</span>
                        <span class="text-slate-400 uppercase">Placed at Accenture</span>
                    </div>
                </div>
            </div>

            <!-- RIGHT 7 COLUMNS: WHITE BOOKING ENGINE FORM -->
            <form class="lg:col-span-7 bg-white rounded-2xl p-6 sm:p-10 border border-slate-200/80 shadow-md text-[#0A0A0A] flex flex-col justify-between" id="demoForm" data-reveal>
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                        <span class="font-mono text-xs font-bold uppercase tracking-widest text-[#0A0A0A]">YOUR CONTACT DETAILS</span>
                        <span class="font-mono text-[10px] text-[#FB6221] uppercase font-bold">* MANDATORY FIELDS</span>
                    </div>

                    <!-- STEP 1: CONTACT FIELDS -->
                    <div class="space-y-4 mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Full Name -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold" for="full-name">
                                    Full Name <span class="text-[#FB6221]">*</span>
                                </label>
                                <input autocomplete="name" class="w-full bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none transition-all placeholder-slate-400" id="full-name" name="name" placeholder="Your full name" required type="text"/>
                            </div>

                            <!-- Phone Number (+91) -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold" for="phone-number">
                                    Phone (+91) <span class="text-[#FB6221]">*</span>
                                </label>
                                <div class="flex items-center bg-slate-50 rounded-xl border border-slate-200 focus-within:border-[#FB6221] focus-within:bg-white transition-all px-3 py-2.5">
                                    <span class="font-mono text-xs font-bold text-slate-500 mr-2">+91</span>
                                    <input autocomplete="tel" class="w-full bg-transparent text-[#0A0A0A] font-['Inter'] text-xs outline-none placeholder-slate-400" id="phone-number" inputmode="tel" maxlength="15" name="phone" pattern="[0-9]{10}" placeholder="98765 43210" required type="tel"/>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Email Address -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold" for="email-address">
                                    Email Address
                                </label>
                                <input autocomplete="email" class="w-full bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none transition-all placeholder-slate-400" id="email-address" name="email" placeholder="you@example.com" type="email"/>
                            </div>

                            <!-- Course Dropdown -->
                            <div class="flex flex-col gap-1.5">
                                <label class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold" for="course-select">
                                    Course Interested In <span class="text-[#FB6221]">*</span>
                                </label>
                                <div class="relative">
                                    <select class="w-full appearance-none bg-slate-50 focus:bg-white text-[#0A0A0A] font-['Inter'] text-xs px-4 py-3 rounded-xl border border-slate-200 focus:border-[#FB6221] outline-none cursor-pointer transition-all pr-10 font-medium" id="course-select" name="course" required>
                                        <option value="fico">SAP FICO</option>
                                        <option value="mm">SAP MM</option>
                                        <option value="sd">SAP SD</option>
                                        <option value="pp">SAP PP</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 text-[18px]">expand_more</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current Background Selector -->
                        <div class="pt-1">
                            <label class="block font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold mb-2">
                                Current Background
                            </label>
                            <div class="flex flex-wrap gap-2" id="bg-selector">
                                <button class="bg-slate-100 hover:bg-slate-200 text-[#0A0A0A] font-['Inter'] text-xs font-semibold px-4 py-2 rounded-full transition-all cursor-pointer" data-val="student" type="button">
                                    Student
                                </button>
                                <button class="bg-[#0A0A0A] text-white font-['Inter'] text-xs font-semibold px-4 py-2 rounded-full transition-all shadow-xs cursor-pointer" data-val="professional" type="button">
                                    Working professional
                                </button>
                                <button class="bg-slate-100 hover:bg-slate-200 text-[#0A0A0A] font-['Inter'] text-xs font-semibold px-4 py-2 rounded-full transition-all cursor-pointer" data-val="switcher" type="button">
                                    Career switcher
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: PREFERRED DATE & TIME -->
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 mb-6">
                        <!-- Preferred Date -->
                        <div class="mb-5">
                            <span class="block font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold mb-2.5">
                                Preferred Date
                            </span>
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2" id="date-selector"></div>
                        </div>

                        <!-- Preferred Time -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-[11px] uppercase tracking-wider text-slate-700 font-bold">
                                    Preferred Time Slot
                                </span>
                                <span class="font-mono text-[10px] text-[#FB6221] font-bold uppercase tracking-wider">TEAM CONFIRMS ON WHATSAPP</span>
                            </div>
                            <div class="grid grid-cols-3 gap-2" id="slot-selector">
                                <button class="slot-chip bg-white text-[#0A0A0A] border border-slate-200 hover:bg-slate-100 font-mono text-xs font-bold py-2.5 rounded-xl transition-all cursor-pointer text-center" type="button">
                                    11:00 AM
                                </button>
                                <button class="slot-chip bg-white text-[#0A0A0A] border border-slate-200 hover:bg-slate-100 font-mono text-xs font-bold py-2.5 rounded-xl transition-all cursor-pointer text-center" type="button">
                                    04:00 PM
                                </button>
                                <button class="slot-chip bg-[#FB6221] text-white shadow-xs font-mono text-xs font-bold py-2.5 rounded-xl transition-all cursor-pointer text-center" type="button">
                                    07:00 PM
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS ROW -->
                <div class="pt-2 flex flex-col gap-3">
                    <button class="w-full bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider py-4 px-6 rounded-full flex items-center justify-center gap-2 transition-all hover:scale-[1.01] shadow-md cursor-pointer group zeb-btn-cta" id="confirm-demo-btn" type="submit">
                        <span>Request My Demo Class</span>
                        <span class="material-symbols-outlined text-[18px] transform transition-transform group-hover:translate-x-1">arrow_forward</span>
                    </button>
                    <div class="text-center pt-1 font-['Inter'] text-xs text-slate-500">
                        Prefer an immediate phone conversation? <a class="underline underline-offset-4 font-bold text-[#0A0A0A] hover:text-[#FB6221] transition-colors" href="tel:+918506888288">Call +91 8506 888 288</a>
                    </div>
                </div>
            </form>

        </div>
    </section>

    <!-- REASSURANCE ROW (3 CARDS) -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20 w-full" data-reveal>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-start gap-4 zeb-card-interactive" data-reveal data-reveal-delay="1">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#FB6221] text-[20px]">credit_card_off</span>
                </div>
                <div>
                    <h3 class="font-heading text-base font-bold uppercase text-[#0A0A0A] mb-1 tracking-tight">
                        No Payment Needed
                    </h3>
                    <p class="font-sans text-xs text-slate-600 leading-relaxed">
                        No credit card or upfront payment is required to attend the trial class session.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-start gap-4 zeb-card-interactive" data-reveal data-reveal-delay="2">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#FB6221] text-[20px]">chat</span>
                </div>
                <div>
                    <h3 class="font-heading text-base font-bold uppercase text-[#0A0A0A] mb-1 tracking-tight">
                        Ask Us Anything
                    </h3>
                    <p class="font-sans text-xs text-slate-600 leading-relaxed">
                        Chat directly with our academic team on WhatsApp to clarify batch timings and system access.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex items-start gap-4 zeb-card-interactive" data-reveal data-reveal-delay="3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[#FB6221] text-[20px]">verified_user</span>
                </div>
                <div>
                    <h3 class="font-heading text-base font-bold uppercase text-[#0A0A0A] mb-1 tracking-tight">
                        No Obligation To Enrol
                    </h3>
                    <p class="font-sans text-xs text-slate-600 leading-relaxed">
                        Experience the live teaching methodology first, then decide if the program fits your goals.
                    </p>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
(function(){
  var bgVal = 'Working professional';
  document.querySelectorAll('#bg-selector button').forEach(function(pill){
    pill.addEventListener('click', function(){
      document.querySelectorAll('#bg-selector button').forEach(function(p){
        p.classList.remove('bg-[#0A0A0A]', 'text-white', 'shadow-xs');
        p.classList.add('bg-slate-100', 'text-[#0A0A0A]');
      });
      pill.classList.remove('bg-slate-100', 'text-[#0A0A0A]');
      pill.classList.add('bg-[#0A0A0A]', 'text-white', 'shadow-xs');
      bgVal = pill.textContent.trim();
    });
  });

  var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
      mons = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var dateWrap = document.getElementById('date-selector'),
      dateVals = [],
      selDate = 0;

  if (dateWrap) {
    for(var i=1; i<=6; i++){
      var d = new Date();
      d.setDate(d.getDate()+i);
      dateVals.push(days[d.getDay()]+' '+d.getDate()+' '+mons[d.getMonth()]);
      var b = document.createElement('button');
      b.type = 'button';
      b.className = 'date-chip flex flex-col items-center justify-center p-2 rounded-xl cursor-pointer border transition-all ' +
                    (i===1 ? 'bg-[#0A0A0A] text-white border-black shadow-xs' : 'bg-white text-[#0A0A0A] border-slate-200 hover:bg-slate-100');
      b.innerHTML = '<span class="font-mono text-[9px] uppercase '+(i===1?'text-slate-300':'text-slate-500')+'">'+days[d.getDay()]+'</span>' +
                    '<span class="font-heading font-extrabold text-base leading-none my-0.5">'+d.getDate()+'</span>' +
                    '<span class="font-mono text-[9px] uppercase font-bold '+(i===1?'text-[#FB6221]':'text-slate-600')+'">'+mons[d.getMonth()]+'</span>';

      (function(idx, el){
        el.addEventListener('click', function(){
          selDate = idx;
          dateWrap.querySelectorAll('.date-chip').forEach(function(c, j){
            var on = j === idx;
            c.classList.toggle('bg-[#0A0A0A]', on);
            c.classList.toggle('text-white', on);
            c.classList.toggle('border-black', on);
            c.classList.toggle('shadow-xs', on);
            c.classList.toggle('bg-white', !on);
            c.classList.toggle('text-[#0A0A0A]', !on);
            c.classList.toggle('border-slate-200', !on);

            var spans = c.querySelectorAll('span');
            if (spans.length >= 3) {
              spans[0].className = 'font-mono text-[9px] uppercase ' + (on ? 'text-slate-300' : 'text-slate-500');
              spans[2].className = 'font-mono text-[9px] uppercase font-bold ' + (on ? 'text-[#FB6221]' : 'text-slate-600');
            }
          });
        });
      })(i-1, b);

      dateWrap.appendChild(b);
    }
  }

  var slotChips = document.querySelectorAll('#slot-selector .slot-chip'),
      selSlot = '07:00 PM';

  slotChips.forEach(function(chip){
    chip.addEventListener('click', function(){
      slotChips.forEach(function(s){
        s.classList.remove('bg-[#FB6221]', 'text-white', 'shadow-xs');
        s.classList.add('bg-white', 'text-[#0A0A0A]', 'border', 'border-slate-200');
      });
      chip.classList.remove('bg-white', 'text-[#0A0A0A]', 'border', 'border-slate-200');
      chip.classList.add('bg-[#FB6221]', 'text-white', 'shadow-xs');
      selSlot = chip.textContent.trim();
    });
  });

  var form = document.getElementById('demoForm');
  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      var sel = document.getElementById('course-select'),
          course = sel.options[sel.selectedIndex].text;
      var name = document.getElementById('full-name').value.trim(),
          phone = document.getElementById('phone-number').value.trim(),
          email = document.getElementById('email-address').value.trim();
      var msg = 'Hello ZebLearn, I want to book a free demo.\nName: '+name+'\nPhone: +91 '+phone+(email?'\nEmail: '+email:'')+'\nCourse: '+course+'\nBackground: '+bgVal+'\nPreferred date: '+dateVals[selDate]+'\nPreferred time: '+selSlot;
      
      window.open('https://wa.me/917277877778?text=' + encodeURIComponent(msg), '_blank');
      setTimeout(function(){
        location.href = '/book-demo/thank-you?module=' + encodeURIComponent(course) + '&date=' + encodeURIComponent(dateVals[selDate]) + '&time=' + encodeURIComponent(selSlot);
      }, 600);
    });
  }
})();
</script>
@endpush
