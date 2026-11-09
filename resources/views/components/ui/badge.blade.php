@props([
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'bg-[#F4F4F4] text-[#292929]',
        'primary' => 'bg-[#FB6221]/10 text-[#D9470C]',
        'dark' => 'bg-[#0A0A0A] text-white',
        'success' => 'bg-emerald-50 text-emerald-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ' . ($variants[$variant] ?? $variants['default'])]) }}>
    {{ $slot }}
</span>
