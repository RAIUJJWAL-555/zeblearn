@props([
    'status' => 'available',
    'label' => null,
])

@php
    $status = strtolower($status);
    $displayLabel = $label ?? match ($status) {
        'available' => 'AVAILABLE NOW',
        'upcoming' => 'UPCOMING BATCH',
        'planned' => 'ROADMAP CANDIDATE',
        default => strtoupper($status),
    };
@endphp

@if($status === 'available')
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>{{ $displayLabel }}</span>
    </span>
@elseif($status === 'upcoming')
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-[#FB6221] border border-orange-200/80 font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">
        <span class="material-symbols-outlined text-[13px] text-[#FB6221]">event_available</span>
        <span>{{ $displayLabel }}</span>
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">
        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
        <span>{{ $displayLabel }}</span>
    </span>
@endif

