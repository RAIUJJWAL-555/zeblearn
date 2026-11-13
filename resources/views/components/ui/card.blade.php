@props([
    'variant' => 'default',
    'padding' => 'md',
    'title' => null,
    'subtitle' => null,
    'header' => null,
    'footer' => null,
    'actions' => null,
])

@php
    $variants = [
        'default' => 'bg-white border border-slate-200/90 shadow-2xs',
        'flat' => 'bg-slate-50 border border-slate-200/80',
        'elevated' => 'bg-white border border-slate-200/80 shadow-md shadow-slate-900/5',
        'interactive' => 'bg-white border border-slate-200/90 shadow-2xs hover:shadow-lg hover:shadow-slate-900/5 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer',
        'dark' => 'bg-[#0A0A0A] border border-slate-800 text-white shadow-md',
    ];
    $variantClass = $variants[$variant] ?? $variants['default'];

    $paddings = [
        'none' => '',
        'sm' => 'p-4',
        'md' => 'p-6 sm:p-7',
        'lg' => 'p-6 sm:p-10',
    ];
    $paddingClass = $paddings[$padding] ?? $paddings['md'];

    $hasHeader = $header || $title || $actions;
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl sm:rounded-3xl overflow-hidden {$variantClass}"]) }}>
    @if($hasHeader)
        <div class="px-6 py-4.5 sm:px-7 sm:py-5 border-b border-slate-100 flex items-center justify-between gap-4 flex-wrap">
            @if($header)
                {{ $header }}
            @else
                <div class="space-y-0.5">
                    @if($title)
                        <h3 class="font-heading font-bold text-base sm:text-lg text-slate-900">
                            {{ $title }}
                        </h3>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-slate-500 font-sans">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>

                @if($actions)
                    <div class="flex items-center gap-2">
                        {{ $actions }}
                    </div>
                @endif
            @endif
        </div>
    @endif

    <div class="{{ $paddingClass }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-3.5 sm:px-7 sm:py-4 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-3 flex-wrap">
            {{ $footer }}
        </div>
    @endif
</div>

