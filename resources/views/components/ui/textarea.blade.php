@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'rows' => 4,
    'placeholder' => null,
    'required' => false,
    'optional' => false,
    'disabled' => false,
    'readonly' => false,
    'hint' => null,
    'help' => null,
    'error' => null,
])

@php
    $textareaId = $id ?? $name;
    $computedValue = old($name, $value ?? $slot);
    $hasError = $error !== null || (isset($errors) && $errors->has($name));
    $errorId = "{$textareaId}-error";
    $helpId = "{$textareaId}-help";

    if ($hasError) {
        $stateClass = 'border-rose-400 bg-rose-50/20 text-slate-900 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/15';
    } elseif ($disabled) {
        $stateClass = 'border-slate-200 bg-slate-100/70 text-slate-400 cursor-not-allowed';
    } else {
        $stateClass = 'border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 hover:border-slate-400 focus:border-[#FB6221] focus:ring-4 focus:ring-[#FB6221]/15';
    }

    $textareaClasses = "w-full rounded-xl px-4 py-3 text-sm transition-all outline-hidden font-sans shadow-2xs {$stateClass}";
@endphp

<div class="w-full">
    @if($label)
        <x-ui.label :for="$textareaId" :required="$required" :optional="$optional" :hint="$hint">
            {{ $label }}
        </x-ui.label>
    @endif

    <div class="relative">
        <textarea
            id="{{ $textareaId }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($hasError) aria-invalid="true" aria-describedby="{{ $errorId }}" @elseif($help) aria-describedby="{{ $helpId }}" @endif
            {{ $attributes->merge(['class' => $textareaClasses]) }}
        >{{ $computedValue }}</textarea>
    </div>

    @if($help && ! $hasError)
        <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500 font-sans">
            {{ $help }}
        </p>
    @endif

    <x-ui.error :name="$name" :message="$error" id="{{ $errorId }}" />
</div>
