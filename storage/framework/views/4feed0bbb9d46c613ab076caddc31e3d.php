<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'course',
    'featured' => false,
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
    'course',
    'featured' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isAvailable = $course->isAvailable();
    $isUpcoming = $course->isUpcoming();
    $isPlanned = $course->isPlanned();
?>

<article class="group relative bg-white rounded-3xl p-6 sm:p-8 lg:p-9 border <?php echo e($isAvailable ? 'border-slate-300 shadow-sm hover:border-[#FB6221]' : 'border-slate-200/90 hover:border-slate-300'); ?> transition-all duration-300 shadow-xs hover:shadow-lg flex flex-col justify-between zeb-card-interactive" data-reveal>
    
    <!-- Top Meta Row -->
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-2 flex-wrap">
                <?php if (isset($component)) { $__componentOriginal568ae8e52332d9d545b728a7b9505ce5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal568ae8e52332d9d545b728a7b9505ce5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course.badge','data' => ['status' => $course->status,'label' => $course->badge_label]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->status),'label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->badge_label)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal568ae8e52332d9d545b728a7b9505ce5)): ?>
<?php $attributes = $__attributesOriginal568ae8e52332d9d545b728a7b9505ce5; ?>
<?php unset($__attributesOriginal568ae8e52332d9d545b728a7b9505ce5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal568ae8e52332d9d545b728a7b9505ce5)): ?>
<?php $component = $__componentOriginal568ae8e52332d9d545b728a7b9505ce5; ?>
<?php unset($__componentOriginal568ae8e52332d9d545b728a7b9505ce5); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal51d4227c7ba5abae90baed830ba211c9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal51d4227c7ba5abae90baed830ba211c9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course.category','data' => ['category' => $course->category]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course.category'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['category' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->category)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal51d4227c7ba5abae90baed830ba211c9)): ?>
<?php $attributes = $__attributesOriginal51d4227c7ba5abae90baed830ba211c9; ?>
<?php unset($__attributesOriginal51d4227c7ba5abae90baed830ba211c9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal51d4227c7ba5abae90baed830ba211c9)): ?>
<?php $component = $__componentOriginal51d4227c7ba5abae90baed830ba211c9; ?>
<?php unset($__componentOriginal51d4227c7ba5abae90baed830ba211c9); ?>
<?php endif; ?>
            </div>
            
            <?php if($course->learner_count): ?>
                <span class="inline-flex items-center gap-1.5 font-mono text-[11px] font-semibold text-slate-500">
                    <span class="material-symbols-outlined text-[15px] text-[#FB6221]">groups</span>
                    <span><?php echo e(number_format($course->learner_count)); ?>+ Trained</span>
                </span>
            <?php elseif($isPlanned): ?>
                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-widest font-semibold">
                    ROADMAP
                </span>
            <?php endif; ?>
        </div>

        <!-- Module Visual Motif & Title -->
        <div class="flex items-start gap-3.5 mb-3">
            <?php if (isset($component)) { $__componentOriginal590733e6c7107f06feece8fd8c92c087 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal590733e6c7107f06feece8fd8c92c087 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.course.module-art','data' => ['module' => $course->slug,'status' => $course->status,'class' => 'w-12 h-12 shrink-0 mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('course.module-art'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['module' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->slug),'status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($course->status),'class' => 'w-12 h-12 shrink-0 mt-0.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal590733e6c7107f06feece8fd8c92c087)): ?>
<?php $attributes = $__attributesOriginal590733e6c7107f06feece8fd8c92c087; ?>
<?php unset($__attributesOriginal590733e6c7107f06feece8fd8c92c087); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal590733e6c7107f06feece8fd8c92c087)): ?>
<?php $component = $__componentOriginal590733e6c7107f06feece8fd8c92c087; ?>
<?php unset($__componentOriginal590733e6c7107f06feece8fd8c92c087); ?>
<?php endif; ?>
            <div class="flex-1 min-w-0">
                <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                    <?php echo e($course->name); ?>

                </h3>
            </div>
        </div>

        <p class="font-sans text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
            <?php echo e($course->short_description); ?>

        </p>

        <!-- Highlights / Curriculum Modules -->
        <?php if(!empty($course->highlights)): ?>
            <div class="space-y-2 mb-6 pt-4 border-t border-slate-100">
                <span class="font-mono text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-2">
                    Key Topics &amp; Competencies
                </span>
                <ul class="space-y-1.5">
                    <?php $__currentLoopData = array_slice($course->highlights, 0, 3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-start gap-2 font-sans text-xs text-slate-700">
                            <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                            <span><?php echo e($highlight); ?></span>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Pricing & Action Section -->
    <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col gap-4">
        
        <!-- Metrics / Pricing Row -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <?php if($isAvailable && $course->fee): ?>
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block">TUITION FEE</span>
                    <span class="font-heading text-2xl font-extrabold text-[#0A0A0A] leading-none"><?php echo e($course->formatted_fee); ?></span>
                <?php elseif($isUpcoming): ?>
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block">BATCH STATUS</span>
                    <span class="font-mono text-xs font-bold text-orange-600 uppercase">Upcoming Batch · Enrol Open</span>
                <?php else: ?>
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block">ACADEMY STATUS</span>
                    <span class="font-mono text-xs font-bold text-slate-500 uppercase">Roadmap Candidate</span>
                <?php endif; ?>
            </div>

            <div class="font-mono text-[11px] text-slate-500 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-slate-400">schedule</span>
                <span><?php echo e($course->duration ?? 'Modular'); ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
            <?php if($isAvailable): ?>
                <a href="<?php echo e($course->url); ?>" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-[#0A0A0A] hover:bg-[#FB6221] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs zeb-btn-cta">
                    <span>View Curriculum</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
                <a href="<?php echo e(route('book-demo', ['course' => 'fico'])); ?>" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-orange-50 hover:bg-orange-100 text-[#FB6221] border border-orange-200 font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-[15px]">event</span>
                    <span>Book Free Demo</span>
                </a>
            <?php elseif($isUpcoming): ?>
                <a href="<?php echo e(route('book-demo', ['course' => $course->slug])); ?>" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs zeb-btn-cta">
                    <span>Reserve Demo Seat</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
                <a href="https://wa.me/917277877778?text=<?php echo e(urlencode('Hello ZebLearn, I am interested in joining the upcoming ' . $course->name . ' batch.')); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-emerald-600">chat</span>
                    <span>WhatsApp Inquiry</span>
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('contact', ['module' => $course->slug])); ?>" class="col-span-full inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-slate-500">notifications_active</span>
                    <span>Notify When Available</span>
                </a>
            <?php endif; ?>
        </div>

    </div>

</article>

<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/course/card.blade.php ENDPATH**/ ?>