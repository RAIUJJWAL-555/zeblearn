@props([
    'title',
    'description' => null,
    'icon' => 'search',
    'bordered' => true,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl sm:rounded-3xl p-8 sm:p-12 text-center flex flex-col items-center justify-center ' . ($bordered ? 'bg-white border border-dashed border-slate-300' : 'bg-transparent')]) }}>
    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-[#FB6221] flex items-center justify-center mb-4 border border-orange-100 shadow-2xs">
        <x-ui.icon :name="$icon" size="lg" />
    </div>

    <h3 class="font-heading font-bold text-base sm:text-lg text-slate-900 tracking-tight">
        {{ $title }}
    </h3>

    @if($description)
        <p class="text-xs sm:text-sm text-slate-500 max-w-sm mt-1.5 leading-relaxed font-sans">
            {{ $description }}
        </p>
    @endif

    @if($slot->isNotEmpty() || $action)
        <div class="mt-6 flex items-center justify-center gap-3">
            {{ $action ?? $slot }}
        </div>
    @endif
</div>

