@php
  $stories = [
    ['Sumit Singh', 'Placed at Accenture', 'I completed my SAP training and got placed in Accenture. The learning environment is brilliant, and everyone around you is studying hard.', 'bg-white'],
    ['Deeksha', 'SAP FICO fast track', 'The trainers are very supportive and there are ample opportunities here. Gagan sir helped me improve my skills and use them fully.', 'bg-soft'],
    ['Shyam Singh', 'ERP & Servers', 'The technical sessions were given in a very simplified manner. Thanks ZebLearn.', 'bg-soft'],
  ];
@endphp
<section id="stories" class="mx-auto max-w-[1280px] px-4 md:px-8 pt-20">
  <span class="dot-label">Alumni</span>
  <h2 class="display mt-3 text-5xl md:text-7xl">SAP student success stories</h2>
  <div class="mt-10 grid gap-4 md:grid-cols-12">
    @php [$n, $r, $q, $bg] = $stories[0]; @endphp
    <figure class="flex flex-col justify-between rounded-card p-8 md:col-span-7 md:p-12 {{ $bg }}">
      <blockquote class="display text-3xl md:text-5xl leading-tight">“{{ $q }}”</blockquote>
      <figcaption class="mt-8 flex items-center gap-3">
        <span class="grid h-12 w-12 place-items-center rounded-full bg-brand font-bold text-white">{{ collect(explode(' ', $n))->map(fn($w) => $w[0])->join('') }}</span>
        <span><strong>{{ $n }}</strong><br><span class="text-sm text-muted">{{ $r }}</span></span>
      </figcaption>
    </figure>
    <div class="grid gap-4 md:col-span-5">
      @foreach(array_slice($stories, 1) as [$n, $r, $q, $bg])
        <figure class="rounded-card p-7 {{ $bg }}">
          <blockquote class="text-base leading-relaxed">“{{ $q }}”</blockquote>
          <figcaption class="mt-4 text-sm"><strong>{{ $n }}</strong> · <span class="text-muted">{{ $r }}</span></figcaption>
        </figure>
      @endforeach
    </div>
  </div>
</section>
