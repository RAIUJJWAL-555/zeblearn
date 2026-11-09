<div id="laserflow-wrapper" class="relative w-full h-[520px] sm:h-[560px] lg:h-[600px] overflow-hidden rounded-3xl bg-[#08080a] border border-slate-800/90 shadow-2xl isolate select-none" style="position: relative; height: 600px; overflow: hidden;">
    <!-- LaserFlow WebGL2 Canvas Container -->
    <div id="laserflow-container" class="pointer-events-none absolute inset-0 h-full w-full">
        <canvas class="absolute inset-0 block h-full w-full" aria-hidden="true"></canvas>
    </div>

    <!-- The Surface Card that the Laser Beam pours over and wraps around (User Template Spec) -->
    <div
        id="laserflow-card"
        style="position: absolute; left: 18%; right: 28%; top: 52%; bottom: -24px; border-radius: 14px; background: #0b0b0d;"
        class="border border-white/15 shadow-2xl p-5 sm:p-6 flex flex-col justify-between overflow-hidden backdrop-blur-md z-10 transition-all duration-300 pointer-events-auto"
    >
        <!-- Card Top Bar: Status & Telemetry -->
        <div class="flex items-center justify-between border-b border-white/10 pb-3 font-mono text-[11px] text-slate-400">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FB6221] animate-pulse"></span>
                <span class="text-white font-bold tracking-wider uppercase">ZEBLEARN SAP S/4HANA</span>
            </div>
            <div class="flex items-center gap-1.5 text-emerald-400 text-[10px] font-semibold uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>SANDBOX ACTIVE</span>
            </div>
        </div>

        <!-- Card Center Showcase: 3D Sculpture & Course Credibility -->
        <div class="flex items-center justify-between gap-4 py-3">
            <div class="space-y-1.5 flex-1 min-w-0">
                <span class="font-mono text-[10px] uppercase font-bold tracking-widest text-[#FB6221] block">CAREER ACCELERATOR</span>
                <h3 class="font-heading text-base sm:text-lg font-bold uppercase tracking-tight text-white leading-tight">
                    ENTERPRISE SAP CONSULTING
                </h3>
                <p class="font-sans text-xs text-slate-300 line-clamp-2">
                    Live system configuration, real project scenarios, and placement support with PlacementBaba.
                </p>
            </div>
            <!-- Embedded 3D Artwork inside the card -->
            <div id="hero-3d-container" class="shrink-0 w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center relative">
                <div id="hero-3d-artwork" class="w-full h-full flex items-center justify-center animate-sculpture-float">
                    <picture class="w-full h-full flex items-center justify-center">
                        <source srcset="<?php echo e(asset('images/hero-3d-sculpture.webp')); ?>" type="image/webp">
                        <img
                            src="<?php echo e(asset('images/hero-3d-sculpture.png')); ?>"
                            alt="ZebLearn SAP Academy 3D Abstract Sculpture"
                            width="96"
                            height="96"
                            fetchpriority="high"
                            loading="eager"
                            decoding="async"
                            class="w-full h-full object-contain drop-shadow-[0_12px_24px_rgba(251,98,33,0.35)] select-none pointer-events-none"
                        >
                    </picture>
                </div>
            </div>
        </div>

        <!-- Card Bottom Modules & Action Track -->
        <div class="pt-3 border-t border-white/10 flex flex-wrap items-center justify-between gap-2 font-mono text-[10px] text-slate-400">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-white/10 text-white font-semibold">FICO</span>
                <span class="px-2 py-0.5 rounded bg-white/10 text-white font-semibold">MM</span>
                <span class="px-2 py-0.5 rounded bg-white/10 text-white font-semibold">SD</span>
                <span class="px-2 py-0.5 rounded bg-white/10 text-white font-semibold">PP</span>
            </div>
            <span class="text-[#FB6221] font-bold">2,500+ ALUMNI</span>
        </div>
    </div>
</div>
<?php /**PATH /Users/ujjwalrai/Desktop/Office/Zeblearn/resources/views/components/home/laserflow-hero.blade.php ENDPATH**/ ?>