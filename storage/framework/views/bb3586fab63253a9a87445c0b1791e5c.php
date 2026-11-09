<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => 'NOT SURE WHICH SAP TRACK FITS YOUR EXPERIENCE?',
    'description' => 'Speak directly with our senior instructors. We review your academic and work background to recommend whether Functional (FICO, MM, SD, PP), Technical (ABAP, Basis), or Cloud (EWM, BTP) offers your best path to enterprise employment.',
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
    'title' => 'NOT SURE WHICH SAP TRACK FITS YOUR EXPERIENCE?',
    'description' => 'Speak directly with our senior instructors. We review your academic and work background to recommend whether Functional (FICO, MM, SD, PP), Technical (ABAP, Basis), or Cloud (EWM, BTP) offers your best path to enterprise employment.',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<section class="bg-[#0A0A0A] text-white rounded-3xl p-8 sm:p-12 lg:p-14 border border-slate-800 shadow-xl relative overflow-hidden my-14" data-reveal>
    <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-[#FB6221]/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <div class="max-w-2xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white font-mono text-[10px] font-bold uppercase tracking-widest border border-white/10">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                ACADEMY ADVISORY HELPLINE
            </div>

            <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-bold uppercase tracking-tight text-white leading-tight">
                <?php echo e($title); ?>

            </h2>

            <p class="font-sans text-xs sm:text-sm text-slate-300 leading-relaxed">
                <?php echo e($description); ?>

            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
            <a href="<?php echo e(route('book-demo')); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-all shadow-md zeb-btn-cta">
                <span>Book a Free Demo</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
            <a href="https://wa.me/917277877778?text=<?php echo e(urlencode('Hello ZebLearn, I need guidance choosing the right SAP course for my background.')); ?>" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors border border-white/15">
                <span class="material-symbols-outlined text-[16px] text-emerald-400">forum</span>
                <span>Chat on WhatsApp</span>
            </a>
        </div>
    </div>
</section>

<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/course/cta.blade.php ENDPATH**/ ?>