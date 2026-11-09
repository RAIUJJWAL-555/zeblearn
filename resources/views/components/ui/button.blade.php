@props([
    'type' => 'button',
    'variant' => 'primary',
])

@php
    $variants = [
        'primary' => 'inline-flex items-center justify-center rounded-full bg-[#FB6221] px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#e94f12] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FB6221]',
        'secondary' => 'inline-flex items-center justify-center rounded-full border border-black/15 bg-white px-5 py-3 text-sm font-semibold text-[#0A0A0A] transition hover:bg-[#F4F4F4] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FB6221]',
        'dark' => 'inline-flex items-center justify-center rounded-full bg-[#0A0A0A] px-5 py-3 text-sm font-semibold text-white transition hover:bg-black/80 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FB6221]',
        'ghost' => 'inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-semibold text-[#0A0A0A] transition hover:bg-black/5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#FB6221]',
    ];
    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
