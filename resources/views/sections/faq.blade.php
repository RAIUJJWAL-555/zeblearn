@php
  $faqs = [
    ['How long is the SAP course?', 'The instructor-led SAP FICO, MM, SD and PP courses run for 3 months.'],
    ['What is the fee?', 'The instructor-led course is ₹25,000 + GST. The SAP Power User Certification course is ₹40,000 + GST.'],
    ['Which module should I learn first?', 'Finance background: FICO. Procurement or inventory: MM. Sales and billing: SD. Manufacturing: PP.'],
    ['Do I get placement support?', 'Yes, placement support is provided through our partner PlacementBaba.'],
    ['Are the classes live?', 'Yes, classes are live and online with real system practice.'],
  ];
@endphp
<section id="demo" class="mx-auto max-w-[1280px] px-4 md:px-8 py-20">
  <span class="dot-label">FAQ</span>
  <h2 class="display mt-3 text-5xl md:text-7xl">SAP course FAQs</h2>

  <div class="mt-10 divide-y divide-ink/10 rounded-card bg-white px-6 md:px-10">
    @foreach($faqs as [$q, $a])
      <details class="group py-5">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-lg font-semibold">
          {{ $q }}
          <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-brand text-white transition group-open:rotate-45">+</span>
        </summary>
        <p class="mt-3 max-w-3xl text-muted">{{ $a }}</p>
      </details>
    @endforeach
  </div>

  <div class="mt-16 text-center">
    <h2 class="display text-6xl md:text-[9rem]">Start your SAP career</h2>
    <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
      <a href="tel:+918506888288" class="rounded-full bg-brand px-8 py-4 font-semibold text-white hover:bg-brand-dark transition">Book a Free Demo</a>
      <a href="https://wa.me/917277877778" class="rounded-full bg-ink px-8 py-4 font-semibold text-white">WhatsApp us</a>
    </div>
  </div>
</section>
