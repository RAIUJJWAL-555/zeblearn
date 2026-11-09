<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'status' => 'available',
    'label' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'status' => 'available',
    'label' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $status = strtolower($status);
    $displayLabel = $label ?? match ($status) {
        'available' => 'AVAILABLE NOW',
        'upcoming' => 'UPCOMING BATCH',
        'planned' => 'ROADMAP CANDIDATE',
        default => strtoupper($status),
    };
?>

<?php if($status === 'available'): ?>
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span><?php echo e($displayLabel); ?></span>
    </span>
<?php elseif($status === 'upcoming'): ?>
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-[#FB6221] border border-orange-200/80 font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">
        <span class="material-symbols-outlined text-[13px] text-[#FB6221]">event_available</span>
        <span><?php echo e($displayLabel); ?></span>
    </span>
<?php else: ?>
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 border border-slate-200 font-mono text-[10px] font-bold uppercase tracking-wider shadow-xs">
        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
        <span><?php echo e($displayLabel); ?></span>
    </span>
<?php endif; ?>

<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/course/badge.blade.php ENDPATH**/ ?>