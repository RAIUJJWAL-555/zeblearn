<section class="mx-auto max-w-[1280px] px-4 md:px-8 pt-20">
  <div class="rounded-card bg-ink p-8 text-white md:p-14">
    <span class="dot-label">New addition</span>
    <h2 class="display mt-3 text-5xl md:text-8xl">SAP Power User<br>Certification Course</h2>
    <p class="mt-5 max-w-xl text-lg text-white/80">80 hours of self-paced training by SAP, 3 months access, ₹40,000 + GST. Available for FICO, MM, SD and PP.</p>
    <div class="mt-6 flex flex-wrap gap-2">
      @foreach(['FICO', 'MM', 'SD', 'PP'] as $m)
        <span class="rounded-full border border-white/25 px-4 py-2 text-sm font-semibold">SAP {{ $m }}</span>
      @endforeach
    </div>
    <a href="#demo" class="mt-8 inline-block rounded-full bg-brand px-6 py-3 font-semibold text-white hover:bg-brand-dark transition">View details</a>
  </div>
</section>
