@props([
    'text' => 'Loading...',
    'size' => 'md',
])

@php
    $spinnerSizes = [
        'sm' => 'sm',
        'md' => 'md',
        'lg' => 'lg',
    ];
    $iconSize = $spinnerSizes[$size] ?? 'md';

    $textSizes = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base',
    ];
    $textSizeClass = $textSizes[$size] ?? 'text-sm';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 text-center text-slate-500 gap-3 font-sans']) }} role="status">
    <x-ui.icon name="spinner" :size="$iconSize" class="text-[#FB6221] animate-spin" />
    @if($text)
        <span class="{{ $textSizeClass }} font-medium text-slate-600">
            {{ $text }}
        </span>
    @endif
    <span class="sr-only">Loading content, please wait.</span>
</div>

