@props([
    'name' => null,
    'message' => null,
])

@php
    $errorMessage = $message;

    if (! $errorMessage && $name && isset($errors) && $errors->has($name)) {
        $errorMessage = $errors->first($name);
    }
@endphp

@if($errorMessage || trim($slot))
    <p {{ $attributes->merge(['class' => 'mt-1.5 text-xs font-medium text-rose-600 flex items-center gap-1.5 font-sans']) }} role="alert">
        <x-ui.icon name="alert-circle" size="xs" class="shrink-0 text-rose-500" />
        <span>{{ $errorMessage ?? $slot }}</span>
    </p>
@endif

