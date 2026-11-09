@php
  $posts = [
    ['Top Interview Question Answer on SAP Basic', 'Interview prep', 'https://sap.zeblearn.com/top-interview-question-answer-on-sap-basic/', 'bg-white md:mt-0'],
    ['Top 20 Question Answer on SAP ECC + HANA + SAP Power User', 'Interview prep', 'https://sap.zeblearn.com/top-50-question-answer-on-sap-ecchana-sap-power-user/', 'bg-brand text-white md:mt-10'],
    ['Top 50 SAP EWM Interview Question Answer', 'Interview prep', 'https://sap.zeblearn.com/top-50-sap-ewm-interview-question-answer/', 'bg-soft md:mt-20'],
  ];
@endphp
<section id="blog" class="mx-auto max-w-[1280px] px-4 md:px-8 pt-20">
  <span class="dot-label">Blog</span>
  <h2 class="display mt-3 text-5xl md:text-7xl">Latest SAP interview questions and career guides</h2>
  <div class="mt-10 grid gap-4 md:grid-cols-3">
    @foreach($posts as [$title, $cat, $url, $tone])
      <a href="{{ $url }}" class="group flex min-h-[260px] flex-col justify-between rounded-card p-7 transition hover:-translate-y-1 {{ $tone }}">
        <span class="w-fit rounded-full bg-black/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide">{{ $cat }}</span>
        <h3 class="display mt-10 text-3xl leading-tight">{{ $title }}</h3>
        <span class="mt-6 text-sm font-semibold">Read article →</span>
      </a>
    @endforeach
  </div>
</section>
