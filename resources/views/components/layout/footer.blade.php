
<style>
    @keyframes zeb-float {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(0, -12px, 0); }
    }

    @keyframes zeb-pulse {
        0%, 100% { opacity: .35; transform: scale(.9); }
        50% { opacity: .7; transform: scale(1.12); }
    }

    @keyframes zeb-shine {
        0% { transform: translateX(-120%) skewX(-15deg); }
        100% { transform: translateX(220%) skewX(-15deg); }
    }

    @keyframes zeb-reveal {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .zeb-footer {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background: #08090b;
        color: white;
    }

    .zeb-footer-grid {
        background-image:
            linear-gradient(rgba(255,255,255,.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,.025) 1px, transparent 1px);
        background-size: 48px 48px;
        mask-image: linear-gradient(to bottom, black, transparent 90%);
        pointer-events: none;
    }

    .zeb-glass {
        background: linear-gradient(
            135deg,
            rgba(255,255,255,.095),
            rgba(255,255,255,.025)
        );
        border: 1px solid rgba(255,255,255,.12);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        box-shadow:
            inset 0 1px 0 rgba(255,255,255,.08),
            0 24px 80px rgba(0,0,0,.2);
    }

    .zeb-glow {
        animation: zeb-pulse 7s ease-in-out infinite;
        pointer-events: none;
    }

    .zeb-footer-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color .3s ease, transform .3s ease;
    }

    .zeb-footer-link::before {
        content: "";
        width: 0;
        height: 1px;
        background: #fb6221;
        transition: width .3s ease;
    }

    .zeb-footer-link:hover {
        color: white;
        transform: translateX(4px);
    }

    .zeb-footer-link:hover::before {
        width: 12px;
    }

    .zeb-footer-cta {
        position: relative;
        overflow: hidden;
        transition: transform .3s ease, box-shadow .3s ease;
    }

    .zeb-footer-cta::after {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 35%;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,.24),
            transparent
        );
        animation: zeb-shine 5s ease-in-out infinite;
        pointer-events: none;
    }

    .zeb-footer-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(251,98,33,.3);
    }

    .zeb-footer-animate {
        animation: zeb-reveal .8s ease both;
    }

    @media (prefers-reduced-motion: reduce) {
        .zeb-footer *,
        .zeb-footer *::before,
        .zeb-footer *::after {
            animation: none !important;
            transition: none !important;
            scroll-behavior: auto !important;
        }
    }
</style>

<footer id="contact" class="zeb-footer">

    <!-- Ambient Background -->
    <div class="zeb-footer-grid absolute inset-0 -z-10"></div>

    <div class="zeb-glow absolute -top-40 -left-32 -z-10 h-96 w-96 rounded-full bg-[#FB6221]/15 blur-[110px]"></div>
    <div class="zeb-glow absolute top-1/2 -right-40 -z-10 h-[450px] w-[450px] rounded-full bg-orange-500/10 blur-[130px]" style="animation-delay:2s"></div>

    <div class="relative mx-auto max-w-[1440px] px-5 pb-7 pt-20 sm:px-8 lg:px-12">

        <!-- Closing CTA with Interactive LaserFlow Simulation -->
        <div id="laserflow-wrapper" class="relative mb-20 overflow-hidden rounded-[28px] lg:rounded-[36px] border border-white/10 bg-[#060709] isolate select-none" style="position: relative; height: 600px; overflow: hidden;">
            <!-- LaserFlow WebGL2 Canvas Container -->
            <div id="laserflow-container" class="pointer-events-none absolute inset-0 h-full w-full">
                <canvas class="absolute inset-0 block h-full w-full" aria-hidden="true"></canvas>
            </div>

            <!-- Top Laser Atmosphere Label -->
            <div class="absolute top-6 left-6 sm:top-8 sm:left-8 z-10 pointer-events-none">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/[0.06] backdrop-blur-md px-3.5 py-1.5 text-[10px] font-semibold uppercase tracking-[0.22em] text-white/70">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-[#FB6221]"></span>
                    THE NEXT STEP STARTS HERE
                </div>
            </div>

            <!-- The Surface Card that the Laser Beam pours over and wraps around (User Template Spec) -->
            <div
                id="laserflow-card"
                style="position: absolute; left: 18%; right: 28%; top: 52%; bottom: -24px; border-radius: 14px; background: #0b0b0d;"
                class="!left-[5%] !right-[5%] sm:!left-[10%] sm:!right-[12%] lg:!left-[18%] lg:!right-[28%] !top-[44%] sm:!top-[48%] lg:!top-[52%] border border-white/15 shadow-2xl p-6 sm:p-8 flex flex-col justify-between overflow-hidden backdrop-blur-md z-10 transition-all duration-300 pointer-events-auto"
            >
                <div class="space-y-3">
                    <div class="flex items-center justify-between border-b border-white/10 pb-2.5 font-mono text-[10px] text-white/60">
                        <span class="uppercase tracking-wider font-semibold text-[#FB6221]">ZEBLEARN SAP ACADEMY</span>
                        <span class="text-white/40">FICO • MM • SD • PP</span>
                    </div>

                    <h2 class="font-heading text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase leading-[1.05] tracking-tight text-white">
                        Your SAP future.
                        <span class="block text-[#FB6221]">Starts with ZebLearn.</span>
                    </h2>

                    <p class="text-xs sm:text-sm leading-relaxed text-white/60 max-w-xl font-sans">
                        Build practical SAP skills with expert-led live batches, real S/4HANA system practice and verified placement assistance.
                    </p>
                </div>

                <div class="pt-4 flex flex-wrap items-center gap-3">
                    <a href="{{ route('book-demo') }}"
                       class="zeb-footer-cta inline-flex items-center justify-center gap-2 rounded-full bg-[#FB6221] px-6 py-3 text-xs sm:text-sm font-bold text-white shadow-md hover:shadow-lg transition-transform hover:scale-[1.02]">
                        <span>Book a Free Demo</span>
                        <span aria-hidden="true">↗</span>
                    </a>

                    <a href="{{ route('courses.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full border border-white/20 bg-white/[0.05] hover:bg-white/10 px-5 py-3 text-xs sm:text-sm font-semibold text-white transition-colors">
                        <span>Explore All Courses</span>
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Footer Grid -->
        <div class="grid gap-12 border-b border-white/10 pb-14 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr] lg:gap-10">

            <!-- Brand -->
            <div class="zeb-footer-animate">
                <a href="{{ route('home') }}" aria-label="ZebLearn Home" class="inline-flex items-center group">
                    <picture>
                        <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                        <img
                            src="{{ asset('logo/logo2.png') }}"
                            alt="ZebLearn"
                            width="400"
                            height="126"
                            loading="lazy"
                            decoding="async"
                            style="aspect-ratio: 1000/315;"
                            class="h-10 sm:h-11 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02]"
                        />
                    </picture>
                </a>

                <p class="mt-6 max-w-xs text-sm leading-7 text-white/50">
                    Online SAP training with live classes, practical system exposure and career support. Learn SAP FICO, MM, SD and PP with ZebLearn.
                </p>

                <div class="mt-7 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.04] px-4 py-2.5">
                    <span class="h-2 w-2 rounded-full bg-[#FB6221] shadow-[0_0_12px_#FB6221]"></span>
                    <span class="text-[10px] font-medium uppercase tracking-[0.16em] text-white/60">
                        SAP Learning
                    </span>
                </div>

                <!-- Social Media Handles -->
                <div class="mt-6">
                    <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-white/40 mb-3">Connect With Us</span>
                    <div class="flex items-center gap-2.5">
                        <a href="https://in.linkedin.com/company/zeblearn"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="ZebLearn on LinkedIn"
                           title="LinkedIn"
                           class="w-9 h-9 rounded-full border border-white/10 bg-white/[0.04] hover:bg-[#0A66C2] hover:border-[#0A66C2] text-white/70 hover:text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                            </svg>
                        </a>

                        <a href="https://www.youtube.com/@ZEBLEARN"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="ZebLearn on YouTube"
                           title="YouTube"
                           class="w-9 h-9 rounded-full border border-white/10 bg-white/[0.04] hover:bg-[#FF0000] hover:border-[#FF0000] text-white/70 hover:text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M21.58 7.19a2.76 2.76 0 0 0-1.95-1.96C17.9 4.75 12 4.75 12 4.75s-5.9 0-7.63.48a2.76 2.76 0 0 0-1.95 1.96C2 8.94 2 12 2 12s0 3.06.42 4.81a2.76 2.76 0 0 0 1.95 1.96c1.73.48 7.63.48 7.63.48s5.9 0 7.63-.48a2.76 2.76 0 0 0 1.95-1.96C22 15.06 22 12 22 12s0-3.06-.42-4.81zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/>
                            </svg>
                        </a>

                        <a href="https://www.instagram.com/zeblearnindia/"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="ZebLearn on Instagram"
                           title="Instagram"
                           class="w-9 h-9 rounded-full border border-white/10 bg-white/[0.04] hover:bg-gradient-to-tr hover:from-[#f9ce34] hover:via-[#ee2a7b] hover:to-[#6228d7] hover:border-transparent text-white/70 hover:text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>

                        <a href="https://www.facebook.com/zeblearn"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="ZebLearn on Facebook"
                           title="Facebook"
                           class="w-9 h-9 rounded-full border border-white/10 bg-white/[0.04] hover:bg-[#1877F2] hover:border-[#1877F2] text-white/70 hover:text-white flex items-center justify-center transition-all duration-200 hover:scale-110 shadow-xs">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Courses -->
            <nav aria-label="SAP training courses">
                <h3 class="mb-7 text-[11px] font-bold uppercase tracking-[0.2em] text-[#FB6221]">
                    SAP Programs
                </h3>

                <ul class="space-y-4 text-sm text-white/55">
                    <li><a href="{{ route('courses.fico') }}" class="zeb-footer-link">SAP FICO Training</a></li>
                    <li><a href="{{ route('courses.index') }}#upcoming-courses" class="zeb-footer-link">SAP MM Training <span class="text-[10px] uppercase font-bold text-amber-400/90 ml-1">Upcoming</span></a></li>
                    <li><a href="{{ route('courses.index') }}#upcoming-courses" class="zeb-footer-link">SAP SD Training <span class="text-[10px] uppercase font-bold text-amber-400/90 ml-1">Upcoming</span></a></li>
                    <li><a href="{{ route('courses.index') }}#upcoming-courses" class="zeb-footer-link">SAP PP Training <span class="text-[10px] uppercase font-bold text-amber-400/90 ml-1">Upcoming</span></a></li>
                    <li><a href="{{ route('courses.index') }}" class="zeb-footer-link font-semibold text-[#FB6221]">All Academy Courses →</a></li>
                </ul>
            </nav>

            <!-- Explore -->
            <nav aria-label="Explore ZebLearn">
                <h3 class="mb-7 text-[11px] font-bold uppercase tracking-[0.2em] text-[#FB6221]">
                    Discover
                </h3>

                <ul class="space-y-4 text-sm text-white/55">
                    <li><a href="{{ route('about') }}" class="zeb-footer-link">About ZebLearn</a></li>
                    <li><a href="{{ route('blogs') }}" class="zeb-footer-link">Blogs & Interview Prep</a></li>
                    <li><a href="https://sap.zeblearn.com/category/sap-faq/" target="_blank" rel="noopener noreferrer" class="zeb-footer-link">SAP FAQs</a></li>
                    <li><a href="{{ route('contact') }}" class="zeb-footer-link">Contact</a></li>
                    <li><a href="{{ route('book-demo') }}" class="zeb-footer-link">Book a Free Demo</a></li>
                </ul>
            </nav>

            <!-- Contact -->
            <div>
                <h3 class="mb-7 text-[11px] font-bold uppercase tracking-[0.2em] text-[#FB6221]">
                    Connect
                </h3>

                <ul class="space-y-5 text-sm text-white/55">
                    <li>
                        <span class="mb-1 block text-[10px] uppercase tracking-widest text-white/30">Call our team</span>
                        <a href="tel:+918506888288" class="transition hover:text-white">+91 8506 888 288</a>
                    </li>
                    <li>
                        <span class="mb-1 block text-[10px] uppercase tracking-widest text-white/30">WhatsApp</span>
                        <a href="https://wa.me/917277877778" target="_blank" rel="noopener noreferrer" class="transition hover:text-[#FB6221]">+91 72778 77778 ↗</a>
                    </li>
                    <li>
                        <span class="mb-1 block text-[10px] uppercase tracking-widest text-white/30">Email</span>
                        <a href="mailto:enquiry@zeblearn.com" class="break-all transition hover:text-white">enquiry@zeblearn.com</a>
                    </li>
                    <li class="pt-1">
                        <span class="mb-2 block text-[10px] uppercase tracking-widest text-white/30">Social</span>
                        <div class="flex items-center gap-3 text-white/60">
                            <a href="https://in.linkedin.com/company/zeblearn" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="hover:text-[#FB6221] transition-colors" title="LinkedIn">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                            </a>
                            <a href="https://www.youtube.com/@ZEBLEARN" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="hover:text-[#FB6221] transition-colors" title="YouTube">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M21.58 7.19a2.76 2.76 0 0 0-1.95-1.96C17.9 4.75 12 4.75 12 4.75s-5.9 0-7.63.48a2.76 2.76 0 0 0-1.95 1.96C2 8.94 2 12 2 12s0 3.06.42 4.81a2.76 2.76 0 0 0 1.95 1.96c1.73.48 7.63.48 7.63.48s5.9 0 7.63-.48a2.76 2.76 0 0 0 1.95-1.96C22 15.06 22 12 22 12s0-3.06-.42-4.81zM9.75 15.02V8.98L15.5 12l-5.75 3.02z"/></svg>
                            </a>
                            <a href="https://www.instagram.com/zeblearnindia/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="hover:text-[#FB6221] transition-colors" title="Instagram">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="https://www.facebook.com/zeblearn" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="hover:text-[#FB6221] transition-colors" title="Facebook">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="flex flex-col gap-5 pt-7 text-xs text-white/35 sm:flex-row sm:items-center sm:justify-between">

            <p>
                &copy; {{ date('Y') }}
                <span class="font-semibold text-white/65">ZebLearn.</span>
                All rights reserved.
            </p>

            <div class="flex flex-wrap gap-x-6 gap-y-3">
                <a href="https://sap.zeblearn.com/disclaimer-policy-trademark/"
                   class="transition hover:text-white">
                    Privacy Policy & Terms
                </a>

                <a href="https://sap.zeblearn.com/refund-policy/"
                   class="transition hover:text-white">
                    Refund Policy
                </a>
            </div>

            <a href="{{ route('home') }}"
               aria-label="ZebLearn Home"
               class="inline-flex items-center gap-1.5 transition opacity-75 hover:opacity-100">
                <picture>
                    <source srcset="{{ asset('logo/logo2-400.webp') }}" type="image/webp">
                    <img
                        src="{{ asset('logo/logo2.png') }}"
                        alt="ZebLearn"
                        width="400"
                        height="126"
                        loading="lazy"
                        decoding="async"
                        style="aspect-ratio: 1000/315;"
                        class="h-5 w-auto object-contain"
                    />
                </picture>
                <span class="text-[#FB6221]">↗</span>
            </a>

        </div>

    </div>
</footer>
