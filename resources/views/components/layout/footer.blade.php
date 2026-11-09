
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

        <!-- Closing CTA -->
        <section class="zeb-glass zeb-footer-animate relative mb-20 overflow-hidden rounded-[28px] p-7 sm:p-10 lg:rounded-[36px] lg:p-14">

            <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-[#FB6221]/15 blur-[90px]"></div>
            <div class="absolute bottom-0 left-1/3 h-1 w-1/3 bg-[#FB6221]/40 blur-2xl"></div>

            <div class="relative z-10 flex flex-col justify-between gap-10 lg:flex-row lg:items-end">

                <div class="max-w-3xl">
                    <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/[0.06] px-4 py-2 text-[10px] font-semibold uppercase tracking-[0.22em] text-white/70">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-[#FB6221]"></span>
                        The next step starts here
                    </div>

                    <h2 class="font-['Anton'] text-5xl uppercase leading-[0.95] tracking-tight text-white sm:text-7xl lg:text-8xl">
                        Your SAP future.
                        <span class="block text-[#FB6221]">Starts with ZebLearn.</span>
                    </h2>

                    <p class="mt-6 max-w-xl text-sm leading-7 text-white/55 sm:text-base">
                        Build practical SAP skills with expert-led training, real system experience and career-focused guidance.
                    </p>
                </div>

                <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                    <a href="{{ route('book-demo') }}"
                       class="zeb-footer-cta inline-flex items-center justify-center gap-3 rounded-full bg-[#FB6221] px-7 py-4 text-sm font-bold text-white">
                        Book a Free Demo
                        <span aria-hidden="true">↗</span>
                    </a>

                    <a href="{{ route('courses.fico') }}"
                       class="inline-flex items-center justify-center gap-3 rounded-full border border-white/20 bg-white/[0.05] px-7 py-4 text-sm font-semibold text-white transition hover:border-white/40 hover:bg-white/10">
                        Explore Courses
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

            </div>
        </section>

        <!-- Main Footer Grid -->
        <div class="grid gap-12 border-b border-white/10 pb-14 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr] lg:gap-10">

            <!-- Brand -->
            <div class="zeb-footer-animate">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <span class="font-['Anton'] text-4xl uppercase tracking-tight text-white">
                        ZebLearn
                    </span>
                    <span class="rounded-md border border-white/20 bg-white/10 px-2 py-1 text-[9px] font-bold tracking-[0.18em] text-white/80">
                        SAP
                    </span>
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
            </div>

            <!-- Courses -->
            <nav aria-label="SAP training courses">
                <h3 class="mb-7 text-[11px] font-bold uppercase tracking-[0.2em] text-[#FB6221]">
                    SAP Programs
                </h3>

                <ul class="space-y-4 text-sm text-white/55">
                    <li><a href="{{ route('courses.fico') }}" class="zeb-footer-link">SAP FICO Training</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="zeb-footer-link">SAP MM Training</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="zeb-footer-link">SAP SD Training</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="zeb-footer-link">SAP PP Training</a></li>
                    <li><a href="{{ url('/#course-deck') }}" class="zeb-footer-link">SAP Power User</a></li>
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
               class="font-['Anton'] text-lg uppercase tracking-wider text-white/50 transition hover:text-[#FB6221]">
                ZebLearn <span class="text-[#FB6221]">↗</span>
            </a>

        </div>

    </div>
</footer>
