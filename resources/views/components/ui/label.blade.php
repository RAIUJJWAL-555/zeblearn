@props([
    'for' => null,
    'required' => false,
    'optional' => false,
    'hint' => null,
])

<div class="flex items-center justify-between gap-2 mb-1.5">
    <label
        @if($for) for="{{ $for }}" @endif
        {{ $attributes->merge(['class' => 'block text-xs font-semibold uppercase tracking-wider text-slate-700 font-mono select-none']) }}
    >
        {{ $slot }}
        @if($required)
            <span class="text-rose-500 font-sans ml-0.5" aria-hidden="true">*</span>
        @endif
        @if($optional)
            <span class="text-slate-400 font-sans normal-case font-normal text-[11px] ml-1">(optional)</span>
        @endif
    </label>

    @if($hint)
        <span class="text-[11px] text-slate-400 font-sans">{{ $hint }}</span>
    @endif
</div>

