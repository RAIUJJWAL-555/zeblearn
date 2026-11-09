@php
  $items = [
    ['01', 'Live classes on a real SAP system', 'Practise on a real system, not slides.', 'bg-white text-ink'],
    ['02', 'Assignments after every module', 'Apply what you learn straight away.', 'bg-soft text-ink'],
    ['03', 'Mock interviews and resume support', 'Get ready for the real interview.', 'bg-brand text-white'],
    ['04', 'Placement support through PlacementBaba', 'Job support until you are placed.', 'bg-ink text-white'],
  ];
@endphp
<section class="mx-auto max-w-[1280px] px-4 md:px-8 pt-20">
  <span class="dot-label">The method</span>
  <h2 class="display mt-3 text-5xl md:text-7xl">What you get with our SAP training</h2>
  <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @foreach($items as [$n, $t, $d, $tone])
      <div class="flex min-h-[280px] flex-col justify-between rounded-card p-7 {{ $tone }}">
        <div class="display text-6xl opacity-90">{{ $n }}</div>
        <div>
          <h3 class="text-xl font-bold leading-snug">{{ $t }}</h3>
          <p class="mt-2 text-sm opacity-80">{{ $d }}</p>
        </div>
      </div>
    @endforeach
  </div>
</section>
