@props([
    'type' => 'info',
    'variant' => null,
    'title' => null,
    'dismissible' => false,
    'icon' => null,
])

@php
    $actualType = $variant ?? $type;
    if ($actualType === 'error') {
        $actualType = 'danger';
    }

    $variants = [
        'info' => [
            'container' => 'bg-blue-50/80 border-blue-200 text-blue-900',
            'icon' => 'info-circle',
            'iconColor' => 'text-blue-600',
            'titleColor' => 'text-blue-950',
            'buttonColor' => 'text-blue-600 hover:text-blue-800 hover:bg-blue-100/60',
        ],
        'success' => [
            'container' => 'bg-emerald-50/80 border-emerald-200 text-emerald-900',
            'icon' => 'check-circle',
            'iconColor' => 'text-emerald-600',
            'titleColor' => 'text-emerald-950',
            'buttonColor' => 'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-100/60',
        ],
        'warning' => [
            'container' => 'bg-amber-50/80 border-amber-200 text-amber-900',
            'icon' => 'warning',
            'iconColor' => 'text-amber-600',
            'titleColor' => 'text-amber-950',
            'buttonColor' => 'text-amber-600 hover:text-amber-800 hover:bg-amber-100/60',
        ],
        'danger' => [
            'container' => 'bg-rose-50/80 border-rose-200 text-rose-900',
            'icon' => 'alert-circle',
            'iconColor' => 'text-rose-600',
            'titleColor' => 'text-rose-950',
            'buttonColor' => 'text-rose-600 hover:text-rose-800 hover:bg-rose-100/60',
        ],
    ];

    $config = $variants[$actualType] ?? $variants['info'];
    $iconName = $icon ?? $config['icon'];
@endphp

<div
    @if($dismissible)
        x-data="{ show: true }"
        x-show="show"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    @endif
    {{ $attributes->merge(['class' => "rounded-2xl border p-4 sm:p-4.5 flex items-start gap-3.5 text-sm font-sans relative {$config['container']}"]) }}
    role="alert"
>
    <div class="shrink-0 mt-0.5 {{ $config['iconColor'] }}">
        <x-ui.icon :name="$iconName" size="md" />
    </div>

    <div class="flex-1 space-y-1 min-w-0">
        @if($title)
            <h4 class="font-bold text-sm leading-tight {{ $config['titleColor'] }}">
                {{ $title }}
            </h4>
        @endif
        <div class="leading-relaxed opacity-95">
            {{ $slot }}
        </div>
    </div>

    @if($dismissible)
        <button
            type="button"
            @click="show = false"
            class="shrink-0 -mr-1 -mt-1 p-1.5 rounded-lg transition-colors focus:outline-hidden {{ $config['buttonColor'] }}"
            aria-label="Dismiss alert"
        >
            <x-ui.icon name="x" size="xs" />
        </button>
    @endif
</div>

