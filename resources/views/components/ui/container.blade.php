@props([
    'size' => 'default',
])

@php
    $sizes = [
        'default' => 'max-w-6xl',
        'wide' => 'max-w-7xl',
        'narrow' => 'max-w-4xl',
        'full' => 'max-w-none',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'mx-auto w-full px-5 sm:px-8 ' . ($sizes[$size] ?? $sizes['default'])]) }}>
    {{ $slot }}
</div>
