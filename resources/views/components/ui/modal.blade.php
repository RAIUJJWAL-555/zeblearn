@props([
    'name',
    'title' => null,
    'maxWidth' => 'lg',
    'header' => null,
    'footer' => null,
])

@php
    $maxWidths = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '4xl' => 'sm:max-w-4xl',
    ];
    $maxWidthClass = $maxWidths[$maxWidth] ?? $maxWidths['lg'];
@endphp

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
    role="dialog"
    aria-modal="true"
    {{ $attributes }}
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        @click="show = false"
        aria-hidden="true"
    ></div>

    {{-- Dialog Panel --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 shadow-xl overflow-hidden w-full mx-auto relative z-10 my-8 {{ $maxWidthClass }}"
        @click.stop
    >
        @if($header || $title)
            <div class="px-6 py-4.5 sm:px-7 sm:py-5 border-b border-slate-100 flex items-center justify-between gap-4">
                @if($header)
                    {{ $header }}
                @else
                    <h3 class="font-heading font-bold text-lg text-slate-900">
                        {{ $title }}
                    </h3>
                @endif

                <button
                    type="button"
                    @click="show = false"
                    class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors focus:outline-hidden"
                    aria-label="Close dialog"
                >
                    <x-ui.icon name="x" size="sm" />
                </button>
            </div>
        @endif

        <div class="p-6 sm:p-7">
            {{ $slot }}
        </div>

        @if($footer)
            <div class="px-6 py-4 sm:px-7 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 flex-wrap">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

