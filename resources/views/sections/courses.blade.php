@php
  $courses = [
    ['no' => '01', 'code' => 'FICO', 'tag' => 'Financials', 'learners' => '1,850+', 'desc' => 'General Ledger, Accounts Payable and Receivable, Asset Accounting, Controlling.', 'tone' => 'bg-brand text-white', 'feat' => true],
    ['no' => '02', 'code' => 'MM',   'tag' => 'Materials Management', 'learners' => '1,650+', 'desc' => 'Master data, purchasing cycle, inventory management and invoice verification.', 'tone' => 'bg-soft text-ink'],
    ['no' => '03', 'code' => 'SD',   'tag' => 'Sales & Distribution', 'learners' => '1,057+', 'desc' => 'Enterprise structure, pricing, credit management, billing and shipping.', 'tone' => 'bg-white text-ink'],
    ['no' => '04', 'code' => 'PP',   'tag' => 'Production Planning', 'learners' => '974+', 'desc' => 'BOM, work centres, routing, MRP and capacity planning.', 'tone' => 'bg-ink text-white'],
  ];
@endphp
<section id="courses" class="mx-auto max-w-[1280px] px-4 md:px-8 pt-20">
  <span class="dot-label">Courses</span>
  <h2 class="display mt-3 text-5xl md:text-7xl">SAP courses online: pick your module</h2>

  <div class="mt-10 space-y-4">
    @foreach($courses as $c)
      <a href="https://sap.zeblearn.com/courses/" class="group grid items-center gap-6 rounded-card p-6 transition hover:-translate-y-1 hover:shadow-xl md:grid-cols-12 md:p-8 {{ $c['tone'] }}">
        <div class="md:col-span-5">
          <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-widest opacity-80">
            <span>{{ $c['no'] }} / {{ $c['tag'] }}</span>
            @if(!empty($c['feat']))<span class="rounded-full bg-white px-2 py-0.5 text-ink">Most chosen</span>@endif
          </div>
          <h3 class="display mt-2 text-6xl md:text-8xl">SAP {{ $c['code'] }}</h3>
        </div>
        <div class="md:col-span-4">
          <p class="text-sm opacity-90">{{ $c['desc'] }}</p>
          <p class="mt-3 text-sm font-semibold">{{ $c['learners'] }} learners · 3 months · Live online</p>
        </div>
        <div class="flex items-center justify-between gap-4 md:col-span-3 md:justify-end">
          <div class="text-right">
            <div class="display text-3xl">₹25,000</div>
            <div class="text-xs opacity-80">+ GST</div>
          </div>
          <span class="grid h-12 w-12 place-items-center rounded-full bg-white text-ink transition group-hover:rotate-45">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 17L17 7M8 7h9v9"/></svg>
          </span>
        </div>
      </a>
    @endforeach
  </div>
</section>
