@props([
    'name',
    'id' => null,
    'type' => 'text',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'optional' => false,
    'disabled' => false,
    'readonly' => false,
    'size' => 'md',
    'icon' => null,
    'rightIcon' => null,
    'hint' => null,
    'help' => null,
    'error' => null,
])

@php
    $inputId = $id ?? $name;
    $computedValue = old($name, $value);

    $hasError = $error !== null || (isset($errors) && $errors->has($name));
    $errorId = "{$inputId}-error";
    $helpId = "{$inputId}-help";

    // Sizes
    $sizes = [
        'sm' => 'px-3 py-2 text-xs rounded-lg',
        'md' => 'px-4 py-2.5 text-sm rounded-xl',
        'lg' => 'px-4 py-3.5 text-base rounded-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    // Left/Right padding when icon is present
    $paddingClass = '';
    if ($icon) {
        $paddingClass .= $size === 'sm' ? ' pl-9' : ' pl-10';
    }
    if ($rightIcon) {
        $paddingClass .= $size === 'sm' ? ' pr-9' : ' pr-10';
    }

    // Border and state styling
    if ($hasError) {
        $stateClass = 'border-rose-400 bg-rose-50/20 text-slate-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/15';
    } elseif ($disabled) {
        $stateClass = 'border-slate-200 bg-slate-100/70 text-slate-400 cursor-not-allowed';
    } else {
        $stateClass = 'border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 hover:border-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15';
    }

    $inputClasses = "w-full transition-all outline-hidden font-sans shadow-2xs {$sizeClass} {$paddingClass} {$stateClass}";
@endphp

<div class="w-full">
    @if($label)
        <x-ui.label :for="$inputId" :required="$required" :optional="$optional" :hint="$hint">
            {{ $label }}
        </x-ui.label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <x-ui.icon :name="$icon" size="sm" />
            </div>
        @endif

        <input
            id="{{ $inputId }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $computedValue }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @elseif($help) aria-describedby="{{ $helpId }}" @endif
            {{ $attributes->merge(['class' => $inputClasses]) }}
        />

        @if($rightIcon)
            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                <x-ui.icon :name="$rightIcon" size="sm" />
            </div>
        @endif
    </div>

    @if($help && ! $hasError)
        <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500 font-sans">
            {{ $help }}
        </p>
    @endif

    <x-ui.error :name="$name" :message="$error" id="{{ $errorId }}" />
</div>
