@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'required' => false,
    'optional' => false,
    'disabled' => false,
    'size' => 'md',
    'hint' => null,
    'help' => null,
    'error' => null,
])

@php
    $selectId = $id ?? $name;
    $hasError = $error !== null || (isset($errors) && $errors->has($name));
    $errorId = "{$selectId}-error";
    $helpId = "{$selectId}-help";

    $sizes = [
        'sm' => 'px-3 py-2 text-xs rounded-lg pr-9',
        'md' => 'px-4 py-2.5 text-sm rounded-xl pr-10',
        'lg' => 'px-4 py-3.5 text-base rounded-xl pr-10',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];

    if ($hasError) {
        $stateClass = 'border-rose-400 bg-rose-50/20 text-slate-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/15';
    } elseif ($disabled) {
        $stateClass = 'border-slate-200 bg-slate-100/70 text-slate-400 cursor-not-allowed';
    } else {
        $stateClass = 'border-slate-300 bg-white text-slate-900 hover:border-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15';
    }

    $selectClasses = "w-full appearance-none transition-all outline-hidden font-sans shadow-2xs cursor-pointer {$sizeClass} {$stateClass}";
@endphp

<div class="w-full">
    @if($label)
        <x-ui.label :for="$selectId" :required="$required" :optional="$optional" :hint="$hint">
            {{ $label }}
        </x-ui.label>
    @endif

    <div class="relative">
        <select
            id="{{ $selectId }}"
            name="{{ $name }}"
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @elseif($help) aria-describedby="{{ $helpId }}" @endif
            {{ $attributes->merge(['class' => $selectClasses]) }}
        >
            @if($placeholder)
                <option value="" disabled selected>{{ $placeholder }}</option>
            @endif

            {{ $slot }}
        </select>

        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
            <x-ui.icon name="chevron-down" size="sm" />
        </div>
    </div>

    @if($help && ! $hasError)
        <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500 font-sans">
            {{ $help }}
        </p>
    @endif

    <x-ui.error :name="$name" :message="$error" id="{{ $errorId }}" />
</div>
