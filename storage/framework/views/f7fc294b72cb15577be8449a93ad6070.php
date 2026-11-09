<?php
    $links = [
        ['label' => 'Courses', 'href' => url('/#course-deck')],
        ['label' => 'Placements', 'href' => url('/#placements')],
        ['label' => 'Stories', 'href' => url('/#stories')],
        ['label' => 'Blogs', 'href' => route('blogs')],
        ['label' => 'About', 'href' => route('about')],
        ['label' => 'Contact', 'href' => route('contact')],
    ];
?>

<header class="fixed inset-x-0 top-6 z-50 px-4 sm:px-6 pointer-events-none">
    <div class="max-w-6xl mx-auto pointer-events-auto rounded-full px-5 py-2.5 sm:px-6 sm:py-3 flex items-center justify-between transition-all shadow-[0_8px_32px_rgba(10,10,10,0.08)] border border-white/60" style="background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(20px) saturate(180%); -webkit-backdrop-filter: blur(20px) saturate(180%);">
        <a href="<?php echo e(url('/')); ?>" aria-label="ZebLearn home" class="flex shrink-0 items-center gap-2 text-xl font-bold tracking-tight text-[#0A0A0A]">
            <span class="font-['Anton'] uppercase">ZebLearn</span>
            <span class="rounded bg-[#0A0A0A] px-2 py-1 text-[10px] font-semibold tracking-widest text-white">SAP</span>
        </a>

        <nav aria-label="Main navigation" class="hidden items-center gap-1 lg:flex">
            <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($link['href']); ?>" class="rounded-full px-3 py-2 text-sm font-medium text-[#292929] transition hover:bg-white hover:text-[#FB6221]">
                    <?php echo e($link['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <a href="<?php echo e(route('book-demo')); ?>" class="rounded-full bg-[#FB6221] px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#e94f12] sm:px-5 sm:text-sm">
                Book a Demo
            </a>
            <details class="group relative lg:hidden">
                <summary aria-label="Open navigation menu" class="grid h-10 w-10 cursor-pointer list-none place-items-center rounded-full border border-black/10 bg-white text-[#0A0A0A] [&::-webkit-details-marker]:hidden">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <nav aria-label="Mobile navigation" class="absolute right-0 top-12 z-50 w-64 rounded-3xl border border-white/70 bg-white/95 p-3 shadow-xl backdrop-blur-xl">
                    <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e($link['href']); ?>" class="block rounded-2xl px-4 py-3 text-sm font-medium text-[#0A0A0A] transition hover:bg-[#F4F4F4] hover:text-[#FB6221]">
                            <?php echo e($link['label']); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </nav>
            </details>
        </div>
    </div>
</header>
<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/layout/header.blade.php ENDPATH**/ ?>