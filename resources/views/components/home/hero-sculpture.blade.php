<div id="hero-3d-container" class="relative w-full max-w-[460px] sm:max-w-[500px] lg:max-w-[560px] aspect-[713/728] mx-auto select-none flex items-center justify-center perspective-1200 preserve-3d" aria-hidden="true">
    <!-- Ambient Warm Orange Atmospheric Glow -->
    <div class="absolute inset-8 sm:inset-12 bg-gradient-to-tr from-[#FB6221]/22 via-[#FB6221]/14 to-amber-300/10 rounded-full blur-3xl -z-10 pointer-events-none transform-gpu animate-ambient-3"></div>
    <div class="absolute -top-6 -right-6 w-48 h-48 bg-[#FB6221]/12 rounded-full blur-2xl -z-10 pointer-events-none transform-gpu"></div>
    <div class="absolute -bottom-6 -left-6 w-48 h-48 bg-slate-300/20 rounded-full blur-2xl -z-10 pointer-events-none transform-gpu"></div>

    <!-- 3D Sculpture Interactive Artwork Container (ambient float + smooth desktop pointer tilt) -->
    <div id="hero-3d-artwork" class="relative z-10 w-full h-full flex items-center justify-center animate-sculpture-float preserve-3d will-change-transform">
        <picture class="w-full h-full flex items-center justify-center">
            <source srcset="{{ asset('images/hero-3d-sculpture.webp') }}" type="image/webp">
            <img
                src="{{ asset('images/hero-3d-sculpture.png') }}"
                alt="ZebLearn SAP Academy 3D Abstract Sculpture"
                width="713"
                height="728"
                fetchpriority="high"
                loading="eager"
                decoding="async"
                class="w-full h-auto max-h-[480px] lg:max-h-[540px] object-contain drop-shadow-[0_24px_48px_rgba(251,98,33,0.12)] transition-transform duration-300 pointer-events-none select-none"
            >
        </picture>
    </div>
</div>

