<?php $__env->startSection('title', 'Demo Request | ZebLearn'); ?>
<?php $__env->startSection('description', 'Complete your free SAP demo request with ZebLearn.'); ?>
<?php $__env->startSection('body-class', 'bg-surface text-on-surface font-body-md text-body-md antialiased min-h-screen selection:bg-primary-container selection:text-on-primary zeb-page'); ?>
<?php $__env->startSection('og_type', 'website'); ?>
<?php $__env->startSection('og_title', 'Demo Request | ZebLearn'); ?>
<?php $__env->startSection('og_description', 'Complete your free SAP demo request with ZebLearn.'); ?>
<?php $__env->startSection('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp'); ?>
<?php $__env->startSection('twitter_title', 'Demo Request | ZebLearn'); ?>
<?php $__env->startSection('twitter_description', 'Complete your free SAP demo request with ZebLearn.'); ?>
<?php $__env->startSection('robots', 'noindex,nofollow'); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function(){var q=new URLSearchParams(location.search),m=q.get('module'),d=q.get('date'),t=q.get('time');
if(m)document.getElementById('t-module').textContent=m;
if(d)document.getElementById('t-when').textContent=d+(t?' · '+t:'');})();
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col w-full">
<!-- Visual Ambient Substrate Overlay -->
<section class="relative w-full py-space-3xl px-margin-mobile md:px-margin flex flex-col items-center justify-center overflow-hidden">
<!-- Editorial Architectural Micro-Watermark in Background -->
<div class="pointer-events-none select-none absolute top-10 left-1/2 -translate-x-1/2 font-display-xl text-[160px] md:text-[240px] text-outline-variant/10 uppercase tracking-tighter leading-none whitespace-nowrap z-0">
      ZEBLEARN
    </div>
<!-- Live Telemetry Capsule Above Modal -->
<div class="relative z-10 flex items-center gap-space-sm px-space-md py-1.5 rounded-full bg-surface-container-lowest/80 backdrop-blur-md shadow-sm mb-space-lg">
<span class="inline-block w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-on-surface-variant">FREE DEMO REQUEST</span>
</div>
<!-- Centered Modal / Focus Card Layout (~760px wide) -->
<div class="relative z-10 w-full max-w-[760px] bg-surface-container-lowest rounded-[2rem] p-8 md:p-12 shadow-[0_24px_64px_rgba(36,25,18,0.08),0_2px_6px_rgba(36,25,18,0.04)] flex flex-col items-center text-center">
<!-- Top Visual: Glossy 3D Vivid Orange Checkmark Seal Medallion -->
<div class="relative mb-space-md flex items-center justify-center">
<!-- Diffuse radial amber bloom behind badge -->
<div class="absolute inset-0 bg-primary-container/20 rounded-full blur-2xl transform scale-125"></div>
<div class="relative w-28 h-28 md:w-32 md:h-32 flex items-center justify-center transition-transform hover:scale-105 duration-300">
<div class="w-full h-full rounded-full bg-[#FB6221] text-white flex items-center justify-center drop-shadow-[0_16px_24px_rgba(231,120,1,0.3)]"><span class="material-symbols-outlined text-[56px]" style="font-variation-settings: 'FILL' 1;">check</span></div>
</div>
</div>
<!-- Dot Label Category Chip -->
<div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-surface-container-low mb-space-md">
<span class="w-2 h-2 rounded-full bg-primary-container"></span>
<span class="font-label-mono text-label-mono uppercase tracking-widest text-on-surface font-semibold">Next Step</span>
</div>
<!-- Giant Condensed Headline -->
<h1 class="font-display-xl text-[54px] md:text-[80px] leading-[0.92] tracking-tight text-inverse-surface uppercase mb-space-md">
        ALMOST THERE.
      </h1>
<!-- Subtitle Description -->
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-[580px] mb-space-xl">
        Send your details to our team on WhatsApp so they can confirm your free demo class. If WhatsApp did not open, use the button below.
      </p>
<!-- Summary Bento Box (Inner Muted Surface Card) -->
<div class="w-full bg-surface-container-low rounded-2xl p-6 text-left mb-space-xl">
<!-- 3-Column Data Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
<!-- Col 1 -->
<div class="flex flex-col space-y-1">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-outline font-semibold">MODULE</span>
<span class="font-body-md text-body-md font-bold text-inverse-surface leading-tight">
<span id="t-module">Your chosen course</span>
</span>
</div>
<!-- Col 2 -->
<div class="flex flex-col space-y-1">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-outline font-semibold">PREFERRED DATE &amp; TIME</span>
<span class="font-body-md text-body-md font-bold text-inverse-surface leading-tight">
<span id="t-when">To be confirmed</span>
</span>
</div>
<!-- Col 3 -->
<div class="flex flex-col space-y-1">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-outline font-semibold">MODE</span>
<span class="font-body-md text-body-md font-bold text-inverse-surface leading-tight">
              Live online
            </span>
</div>
</div>
<!-- Terminal Code Badge / Telemetry Footer -->
<div class="pt-4 bg-surface-container-highest/60 -mx-6 -mb-6 px-6 pb-4 rounded-b-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
<div class="font-label-mono text-label-mono text-on-surface-variant flex items-center gap-2">
<span class="material-symbols-outlined text-[15px] text-primary">schedule</span>
<span>Our team will confirm the final slot with you</span>
</div>
<span class="inline-flex items-center gap-1 font-label-mono text-[10px] uppercase tracking-wider text-primary font-bold bg-primary-fixed px-2 py-0.5 rounded">
            PENDING CONFIRMATION
          </span>
</div>
</div>
<!-- Next Steps Checklist Container -->
<div class="w-full text-left mb-space-xl">
<div class="flex items-center justify-between mb-space-md">
<span class="font-label-mono text-label-mono uppercase tracking-widest text-outline font-semibold">
            WHAT HAPPENS NEXT
          </span>
<span class="font-label-mono text-label-mono text-primary font-semibold">3 STEPS</span>
</div>
<div class="flex flex-col space-y-3">
<!-- Step 1 -->
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container/50 hover:bg-surface-container transition-colors">
<div class="w-7 h-7 rounded-full bg-inverse-surface text-on-primary font-label-mono text-label-mono flex items-center justify-center shrink-0 mt-0.5">
              01
            </div>
<div class="flex-1">
<p class="font-body-md text-body-md font-medium text-inverse-surface">
                Send your details on WhatsApp.
              </p>
<p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">
                Our team will confirm your demo slot after they receive your message.
              </p>
</div>
<span class="material-symbols-outlined text-outline-variant text-[20px] shrink-0 mt-1">chat</span>
</div>
<!-- Step 2 -->
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container/50 hover:bg-surface-container transition-colors">
<div class="w-7 h-7 rounded-full bg-inverse-surface text-on-primary font-label-mono text-label-mono flex items-center justify-center shrink-0 mt-0.5">
              02
            </div>
<div class="flex-1">
<p class="font-body-md text-body-md font-medium text-inverse-surface">
                Keep your phone handy.
              </p>
<p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">
                Our team may call you to confirm the details.
              </p>
</div>
<span class="material-symbols-outlined text-outline-variant text-[20px] shrink-0 mt-1">call</span>
</div>
<!-- Step 3 -->
<div class="flex items-start gap-4 p-4 rounded-xl bg-surface-container/50 hover:bg-surface-container transition-colors">
<div class="w-7 h-7 rounded-full bg-inverse-surface text-on-primary font-label-mono text-label-mono flex items-center justify-center shrink-0 mt-0.5">
              03
            </div>
<div class="flex-1">
<p class="font-body-md text-body-md font-medium text-inverse-surface">
                Prepare your questions.
              </p>
<p class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">
                Note down your doubts about SAP modules, fees and placements to ask in the demo.
              </p>
</div>
<span class="material-symbols-outlined text-outline-variant text-[20px] shrink-0 mt-1">help</span>
</div>
</div>
</div>
<!-- Action Button Pills -->
<div class="w-full flex flex-col sm:flex-row items-center justify-center gap-space-md mb-space-lg">
<!-- Primary Solid Pill: Add to Calendar -->
<a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-inverse-surface text-on-primary font-body-md text-body-md font-semibold px-8 py-3.5 rounded-full hover:bg-on-surface transition-all duration-200 hover:scale-[1.02] shadow-sm" href="https://wa.me/917277877778?text=Hello%20ZebLearn%2C%20I%20want%20to%20book%20a%20free%20demo." rel="noopener" target="_blank"><span class="material-symbols-outlined text-[18px]">chat</span><span>Chat on WhatsApp</span></a>
<!-- Secondary Pill: Chat on WhatsApp -->
<a class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-surface-container-lowest text-inverse-surface font-body-md text-body-md font-semibold px-7 py-3.5 rounded-full hover:bg-surface-container transition-all duration-200 shadow-sm" href="tel:+918506888288"><span class="material-symbols-outlined text-[20px] text-primary">call</span><span>Call +91 8506 888 288</span></a>
</div>
<!-- Tertiary Navigation Links -->
<div class="flex items-center justify-center gap-space-lg pt-2 text-on-surface-variant font-body-md text-body-md">
<a class="hover:text-primary transition-colors flex items-center gap-1 group" href="/">
<span class="material-symbols-outlined text-[16px] group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
<span>Return to Homepage</span>
</a>
<span class="text-outline-variant">/</span>
<a class="hover:text-primary transition-colors flex items-center gap-1 group" href="/courses/sap-fico-training">
<span>Explore SAP FICO Course</span>
<span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
</a>
</div>
</div>
<!-- Secondary Context Bento Section (Architect & Live System Preview) -->
<div class="relative z-10 w-full max-w-[760px] grid grid-cols-1 sm:grid-cols-2 gap-space-md mt-space-lg">
<!-- Session Lead Preview Card -->
<div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex items-center gap-4">
<div class="w-14 h-14 rounded-full bg-surface-container flex items-center justify-center text-primary shrink-0 relative overflow-hidden">
<span class="material-symbols-outlined text-[28px]">engineering</span>
</div>
<div class="flex flex-col min-w-0">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-outline">NEED HELP?</span>
<span class="font-body-md text-body-md font-bold text-inverse-surface truncate">Call our team</span>
<span class="font-label-sm text-label-sm text-on-surface-variant truncate">+91 8506 888 288</span>
</div>
</div>
<!-- System Diagnostic Readiness Card -->
<div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex items-center justify-between">
<div class="flex flex-col min-w-0">
<span class="font-label-mono text-label-mono uppercase tracking-wider text-outline">FORMAT</span>
<span class="font-body-md text-body-md font-bold text-inverse-surface">Live Online Classes</span>
<span class="font-label-sm text-label-sm text-primary flex items-center gap-1 mt-0.5">
<span class="w-1.5 h-1.5 rounded-full bg-primary-container"></span>
            FICO · MM · SD · PP
          </span>
</div>
<div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-inverse-surface shrink-0">
<span class="material-symbols-outlined text-[20px]">videocam</span>
</div>
</div>
</div>
</section>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/pages/book-demo-thank-you.blade.php ENDPATH**/ ?>