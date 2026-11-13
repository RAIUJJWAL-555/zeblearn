@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'tag' => null,
    'href' => null,
    'disabled' => false,
    'loading' => false,
    'loadingText' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'fullWidth' => false,
    'rounded' => 'default',
])

@php
    $element = $tag ?? ($href ? 'a' : 'button');

    // Radii
    $roundedClass = $rounded === 'pill' ? 'rounded-full' : 'rounded-xl';

    // Base layout & interaction
    $baseClasses = "inline-flex items-center justify-center font-semibold transition-all duration-200 select-none cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed disabled:pointer-events-none focus:outline-hidden focus-visible:ring-3 focus-visible:ring-[#FB6221]/30 focus-visible:ring-offset-1 active:scale-[0.99] {$roundedClass}";

    if ($fullWidth) {
        $baseClasses .= ' w-full';
    }

    // Sizes
    $sizes = [
        'xs' => 'px-2.5 py-1.5 text-xs gap-1.5',
        'sm' => 'px-3.5 py-2 text-xs gap-2',
        'md' => 'px-5 py-2.5 text-sm gap-2',
        'lg' => 'px-6 py-3.5 text-base gap-2.5',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    // Variants
    $actualVariant = $variant === 'error' ? 'danger' : $variant;
    $variants = [
        'primary' => 'bg-[#FB6221] hover:bg-[#e05419] active:bg-[#c94711] text-white shadow-xs hover:shadow-md hover:shadow-orange-500/15 border border-transparent',
        'secondary' => 'bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 shadow-2xs hover:border-slate-400',
        'outline' => 'bg-transparent hover:bg-orange-50/50 text-[#FB6221] border border-[#FB6221] hover:border-[#e05419]',
        'danger' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white shadow-xs hover:shadow-md hover:shadow-rose-600/15 border border-transparent',
        'danger-outline' => 'bg-transparent hover:bg-rose-50 text-rose-600 border border-rose-300 hover:border-rose-400',
        'dark' => 'bg-[#0A0A0A] hover:bg-black/90 active:bg-black text-white shadow-xs border border-transparent',
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-transparent',
        'link' => 'bg-transparent text-[#FB6221] hover:text-[#e05419] underline-offset-4 hover:underline p-0 border-0 shadow-none !inline !w-auto',
    ];
    $variantClass = $variants[$actualVariant] ?? $variants['primary'];

    $classes = "{$baseClasses} {$sizeClass} {$variantClass}";

    // Icon size mapping
    $iconSizes = [
        'xs' => 'xs',
        'sm' => 'xs',
        'md' => 'sm',
        'lg' => 'md',
    ];
    $computedIconSize = $iconSizes[$size] ?? 'sm';
@endphp

@if($element === 'a')
    <a
        href="{{ $disabled ? '#' : $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled) aria-disabled="true" tabindex="-1" @endif
    >
        @if($loading)
            <x-ui.icon name="spinner" size="{{ $computedIconSize }}" class="shrink-0 animate-spin" />
            <span>{{ $loadingText ?? $slot }}</span>
        @else
            @if($icon && $iconPosition === 'left')
                <x-ui.icon :name="$icon" size="{{ $computedIconSize }}" class="shrink-0" />
            @endif

            {{ $slot }}

            @if($icon && $iconPosition === 'right')
                <x-ui.icon :name="$icon" size="{{ $computedIconSize }}" class="shrink-0" />
            @endif
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
        @if($disabled || $loading) disabled @endif
        @if($loading) aria-busy="true" @endif
    >
        @if($loading)
            <x-ui.icon name="spinner" size="{{ $computedIconSize }}" class="shrink-0 animate-spin" />
            <span>{{ $loadingText ?? $slot }}</span>
        @else
            @if($icon && $iconPosition === 'left')
                <x-ui.icon :name="$icon" size="{{ $computedIconSize }}" class="shrink-0" />
            @endif

            {{ $slot }}

            @if($icon && $iconPosition === 'right')
                <x-ui.icon :name="$icon" size="{{ $computedIconSize }}" class="shrink-0" />
            @endif
        @endif
    </button>
@endif
