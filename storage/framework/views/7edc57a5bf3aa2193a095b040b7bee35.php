<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'category' => 'functional',
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
    'category' => 'functional',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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
?>

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 font-mono text-[10px] font-bold uppercase tracking-wider">
    <span class="material-symbols-outlined text-[13px] text-slate-500"><?php echo e($icon); ?></span>
    <span><?php echo e($label); ?></span>
</span>

<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/course/category.blade.php ENDPATH**/ ?>