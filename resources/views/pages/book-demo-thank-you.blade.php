@extends('layouts.app')

@section('title', 'Demo Request | ZebLearn')
@section('description', 'Complete your free SAP demo request with ZebLearn.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')
@section('og_title', 'Demo Request | ZebLearn')
@section('og_description', 'Complete your free SAP demo request with ZebLearn.')
@section('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')
@section('twitter_title', 'Demo Request | ZebLearn')
@section('twitter_description', 'Complete your free SAP demo request with ZebLearn.')
@section('robots', 'noindex,nofollow')

@push('scripts')
<script>
(function(){
  var q = new URLSearchParams(location.search),
      m = q.get('module'),
      d = q.get('date'),
      t = q.get('time');
  if (m) document.getElementById('t-module').textContent = m;
  if (d) document.getElementById('t-when').textContent = d + (t ? ' · ' + t : '');
})();
</script>
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <section class="relative w-full pt-28 sm:pt-32 pb-20 px-4 sm:px-6 lg:px-12 flex flex-col items-center justify-center overflow-hidden">
        
        <!-- Live Telemetry Capsule -->
        <div class="relative z-10 flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-slate-200 shadow-xs mb-8 animate-hero-badge">
            <span class="inline-block w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
            <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">DEMO REQUEST RECEIVED</span>
        </div>

        <!-- Main Confirmation Card (Max ~760px) -->
        <div class="relative z-10 w-full max-w-[760px] bg-white rounded-3xl p-6 sm:p-12 border border-slate-200/80 shadow-lg flex flex-col items-center text-center animate-hero-assembly">
            
            <!-- Checkmark Icon -->
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-orange-50 border border-orange-200 flex items-center justify-center text-[#FB6221] mb-6 shadow-sm animate-hero-badge">
                <span class="material-symbols-outlined text-[42px] sm:text-[48px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>

            <!-- Category Chip -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-widest mb-4">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                FINAL STEP
            </div>

            <!-- Headline -->
            <h1 class="font-heading text-4xl sm:text-6xl font-extrabold uppercase tracking-tight text-[#0A0A0A] leading-tight mb-4 animate-hero-title">
                ALMOST THERE.
            </h1>

            <p class="font-sans text-sm sm:text-base text-slate-600 max-w-lg mb-8 leading-relaxed animate-hero-desc">
                Send your details to our team on WhatsApp so they can confirm your free demo class slot. If WhatsApp did not open automatically, click the button below.
            </p>

            <!-- Summary Bento Box -->
            <div class="w-full bg-slate-50 border border-slate-200/80 rounded-2xl p-6 text-left mb-8 animate-hero-cta">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 font-bold block mb-1">MODULE</span>
                        <span class="font-['Inter'] text-xs sm:text-sm font-bold text-[#0A0A0A]" id="t-module">Your chosen course</span>
                    </div>
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 font-bold block mb-1">PREFERRED DATE &amp; TIME</span>
                        <span class="font-['Inter'] text-xs sm:text-sm font-bold text-[#0A0A0A]" id="t-when">To be confirmed</span>
                    </div>
                    <div>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 font-bold block mb-1">MODE</span>
                        <span class="font-['Inter'] text-xs sm:text-sm font-bold text-[#0A0A0A]">Live Online + SAP GUI</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500">
                        <span class="material-symbols-outlined text-[15px] text-[#FB6221]">schedule</span>
                        <span>Our team will confirm the final slot with you</span>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-orange-100 text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider">
                        PENDING WHATSAPP CONFIRMATION
                    </span>
                </div>
            </div>

            <!-- Next Steps 3-Row Checklist -->
            <div class="w-full text-left mb-8 space-y-2.5" data-reveal>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">
                    WHAT HAPPENS NEXT
                </span>

                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <div class="w-6 h-6 rounded-full bg-[#0A0A0A] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                        01
                    </div>
                    <div>
                        <p class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">Send your details on WhatsApp</p>
                        <p class="font-['Inter'] text-xs text-slate-500 mt-0.5">Our academic team will confirm your demo schedule once they receive your WhatsApp message.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <div class="w-6 h-6 rounded-full bg-[#0A0A0A] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                        02
                    </div>
                    <div>
                        <p class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">Keep your phone handy</p>
                        <p class="font-['Inter'] text-xs text-slate-500 mt-0.5">Our senior academic counselor may call you to verify your background and preferred batch timings.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/60">
                    <div class="w-6 h-6 rounded-full bg-[#0A0A0A] text-white font-mono text-[11px] font-bold flex items-center justify-center shrink-0 mt-0.5">
                        03
                    </div>
                    <div>
                        <p class="font-['Inter'] text-xs font-bold text-[#0A0A0A]">Prepare your questions</p>
                        <p class="font-['Inter'] text-xs text-slate-500 mt-0.5">Jot down questions regarding certification, real-system assignments, and placement opportunities.</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="w-full flex flex-col sm:flex-row items-center justify-center gap-3 mb-6">
                <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider px-8 py-3.5 rounded-full shadow-md transition-all hover:scale-[1.02] zeb-btn-cta" href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo." rel="noopener" target="_blank">
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                    <span>Confirm on WhatsApp</span>
                </a>
                <a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-[#0A0A0A] font-['Inter'] text-xs font-bold uppercase tracking-wider px-6 py-3.5 rounded-full transition-colors active:scale-95" href="tel:+918506888288">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">call</span>
                    <span>Call +91 8506 888 288</span>
                </a>
            </div>

            <!-- Return Navigation Links -->
            <div class="flex items-center justify-center gap-4 text-xs font-mono text-slate-500">
                <a class="hover:text-[#FB6221] transition-colors flex items-center gap-1" href="/">
                    <span class="material-symbols-outlined text-[14px]">arrow_back</span>
                    <span>Return to Home</span>
                </a>
                <span class="text-slate-300">/</span>
                <a class="hover:text-[#FB6221] transition-colors flex items-center gap-1" href="/courses/sap-fico-training">
                    <span>Explore SAP FICO</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>

        </div>

    </section>

</div>
@endsection
