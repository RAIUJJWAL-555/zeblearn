@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'left',
])

<div {{ $attributes->merge(['class' => 'max-w-3xl ' . ($align === 'center' ? 'mx-auto text-center' : 'text-left')]) }}>
    @if($eyebrow)
        <p class="mb-3 text-xs font-bold uppercase tracking-[0.18em] text-[#FB6221]">{{ $eyebrow }}</p>
    @endif

    <h2 class="text-3xl font-bold tracking-tight text-[#0A0A0A] sm:text-4xl">{{ $title }}</h2>

    @if($description)
        <p class="mt-4 text-base leading-7 text-[#5B5B5B]">{{ $description }}</p>
    @endif

    {{ $slot }}
</div>
