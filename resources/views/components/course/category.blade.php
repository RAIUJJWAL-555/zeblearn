@props([
    'category' => 'functional',
])

@php
    $label = match ($category) {
        'functional' => 'SAP Functional',
        'technical' => 'SAP Technical',
        'cloud_specialized' => 'SAP Cloud & Specialized',
        default => 'SAP Academy',
    };

    $icon = match ($category) {
        'functional' => 'account_tree',
        'technical' => 'terminal',
        'cloud_specialized' => 'cloud_sync',
        default => 'school',
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider">
    <span class="material-symbols-outlined text-[13px] text-slate-500">{{ $icon }}</span>
    <span>{{ $label }}</span>
</span>

