@props([
    'variant' => 'neutral',
    'size' => 'md',
    'dot' => false,
    'dotPulse' => false,
    'icon' => null,
])

@php
    // Map existing 'default' variant to 'neutral', and 'error' to 'danger' for backwards compatibility
    $actualVariant = $variant === 'default' ? 'neutral' : $variant;
    if ($actualVariant === 'error') {
        $actualVariant = 'danger';
    }

    $variants = [
        'neutral' => 'bg-slate-100 text-slate-700 border-slate-200/80',
        'primary' => 'bg-orange-50 text-[#FB6221] border-orange-200/80',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200/80',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200/80',
        'info' => 'bg-blue-50 text-blue-700 border-blue-200/80',
        'dark' => 'bg-[#0A0A0A] text-white border-transparent',
        'outline' => 'bg-transparent text-slate-700 border-slate-300',
    ];
    $variantClass = $variants[$actualVariant] ?? $variants['neutral'];

    $sizes = [
        'xs' => 'px-1.5 py-0.5 text-[10px] gap-1',
        'sm' => 'px-2 py-0.5 text-[11px] gap-1.5',
        'md' => 'px-2.5 py-1 text-xs gap-1.5',
        'lg' => 'px-3 py-1.5 text-sm gap-2',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    $dotColors = [
        'neutral' => 'bg-slate-400',
        'primary' => 'bg-[#FB6221]',
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-rose-500',
        'info' => 'bg-blue-500',
        'dark' => 'bg-emerald-400',
        'outline' => 'bg-slate-400',
    ];
    $dotColor = $dotColors[$actualVariant] ?? $dotColors['neutral'];

    $iconSizes = [
        'xs' => 'xs',
        'sm' => 'xs',
        'md' => 'xs',
        'lg' => 'sm',
    ];
    $iconSize = $iconSizes[$size] ?? 'xs';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full font-medium border font-sans tracking-tight shrink-0 select-none {$variantClass} {$sizeClass}"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} {{ $dotPulse ? 'animate-pulse' : '' }}" aria-hidden="true"></span>
    @endif

    @if($icon)
        <x-ui.icon :name="$icon" size="{{ $iconSize }}" class="shrink-0" />
    @endif

    {{ $slot }}
</span>
