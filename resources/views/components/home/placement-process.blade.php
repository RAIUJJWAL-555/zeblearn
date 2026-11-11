@php
    $placementSteps = [
        [
            'step' => '01',
            'title' => 'CAREER COUNSELLING',
            'full_title' => 'Career Counselling & Profile Evaluation',
            'description' => 'Our experts analyse your background and goals to map the perfect program — SAP, Workday or Data Science.',
            'card_bg' => 'bg-[#0B1320]',
            'card_border' => 'border-slate-800/80',
            'badge_bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/20',
            'glow_color' => 'rgba(56, 189, 248, 0.15)',
        ],
        [
            'step' => '02',
            'title' => 'EXPERT-LED TRAINING',
            'full_title' => 'Expert-Led Live Training',
            'description' => 'Learn from MNC consultants with 10+ years experience. Live SAP S/4HANA & cloud access from day one.',
            'card_bg' => 'bg-[#1C130D]',
            'card_border' => 'border-orange-950/70',
            'badge_bg' => 'bg-orange-500/10 text-orange-400 border-orange-500/20',
            'glow_color' => 'rgba(251, 98, 33, 0.15)',
        ],
        [
            'step' => '03',
            'title' => 'REAL PROJECTS',
            'full_title' => 'Real Projects & Sandbox Practice',
            'description' => 'End-to-end implementations on live business scenarios that mirror real MNC environments.',
            'card_bg' => 'bg-[#062419]',
            'card_border' => 'border-emerald-950/70',
            'badge_bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            'glow_color' => 'rgba(16, 185, 129, 0.15)',
        ],
        [
            'step' => '04',
            'title' => 'RESUME BUILDING',
            'full_title' => 'Resume Building',
            'description' => 'ATS-optimised resume and LinkedIn profile crafted by our placement experts to attract top recruiters.',
            'card_bg' => 'bg-[#1A102E]',
            'card_border' => 'border-purple-950/70',
            'badge_bg' => 'bg-purple-500/10 text-purple-400 border-purple-500/20',
            'glow_color' => 'rgba(168, 85, 247, 0.15)',
        ],
        [
            'step' => '05',
            'title' => 'MOCK INTERVIEWS',
            'full_title' => 'Mock Interviews',
            'description' => '1-on-1 mock interviews with industry consultants. Role-specific questions and detailed feedback sessions.',
            'card_bg' => 'bg-[#280C14]',
            'card_border' => 'border-rose-950/70',
            'badge_bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
            'glow_color' => 'rgba(244, 63, 94, 0.15)',
        ],
        [
            'step' => '06',
            'title' => 'INTERVIEW CALLS',
            'full_title' => 'Interview Preparation & Referrals',
            'description' => 'Direct referrals to 500+ hiring partners. Our cell schedules and tracks every interview for you.',
            'card_bg' => 'bg-[#072027]',
            'card_border' => 'border-cyan-950/70',
            'badge_bg' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
            'glow_color' => 'rgba(6, 182, 212, 0.15)',
        ],
        [
            'step' => '07',
            'title' => "YOU'RE PLACED",
            'full_title' => 'Career Opportunities via PlacementBaba',
            'description' => 'Offer letter released, salary onboarding support and career transition to top MNC roles.',
            'card_bg' => 'bg-[#211707]',
            'card_border' => 'border-amber-950/70',
            'badge_bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
            'glow_color' => 'rgba(245, 158, 11, 0.15)',
        ],
    ];
@endphp

<!-- =========================================================================
     SECTION 3: OUR PROVEN 7-STEP PLACEMENT PROCESS (ALTERNATING PIPELINE)
     ========================================================================= -->
<section id="placement-process" class="w-full py-16 lg:py-24 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto border-t border-slate-200 overflow-hidden" aria-labelledby="placement-process-heading">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-12 lg:mb-14" data-reveal>
        <div class="inline-flex items-center gap-3 text-xs font-mono font-bold text-[#FB6221] uppercase tracking-widest mb-3">
            <span class="w-8 h-px bg-[#FB6221]/50"></span>
            <span>PLACEMENT JOURNEY</span>
            <span class="w-8 h-px bg-[#FB6221]/50"></span>
        </div>
        <h2 id="placement-process-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-[#0A0A0A] tracking-tight leading-tight mb-4">
            OUR PROVEN <span class="text-[#FB6221]">7-STEP</span> PLACEMENT PROCESS
        </h2>
        <p class="font-sans text-sm sm:text-base text-slate-600 font-normal leading-relaxed">
            From career counselling to your offer letter — every step is designed to get you placed at a top MNC.
        </p>
    </div>

    <!-- Stepped Undulating Horizontal Track (Exact Reference Alignment) -->
    <div class="relative py-2">
        <div
            id="process-carousel-track"
            class="relative flex items-center gap-5 sm:gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory no-scrollbar pb-6 pt-2 px-4 sm:px-6 lg:px-12 focus:outline-none min-h-[460px]"
            tabindex="0"
            role="region"
            aria-label="7-Step placement process horizontal track"
        >
            @foreach($placementSteps as $index => $item)
                @php
                    $isTop = ($index % 2 === 0);
                @endphp

                <div class="snap-start shrink-0 w-[260px] sm:w-[280px] lg:w-[295px] h-[450px] flex flex-col items-center justify-between relative z-10 select-none">
                    @if($isTop)
                        <!-- Card (Top Position) -->
                        <div class="w-full h-[215px] rounded-2xl sm:rounded-3xl p-5 sm:p-6 {{ $item['card_bg'] }} border {{ $item['card_border'] }} shadow-lg flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
                            <!-- Subtle Ambient Glow -->
                            <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full blur-2xl pointer-events-none opacity-40" style="background-color: {{ $item['glow_color'] }};"></div>

                            <!-- Huge Faint Watermark Number -->
                            <span class="absolute right-3 -bottom-2 font-heading text-7xl font-black text-white/[0.04] pointer-events-none select-none">
                                {{ $item['step'] }}
                            </span>

                            <div class="relative z-10">
                                <span class="inline-block px-2.5 py-0.5 rounded-md border text-[10px] font-mono font-bold uppercase tracking-wider mb-2.5 {{ $item['badge_bg'] }}">
                                    STEP {{ $item['step'] }}
                                </span>
                                <h3 class="font-heading text-sm sm:text-base font-bold uppercase text-white tracking-wide leading-snug mb-2">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="font-sans text-xs text-slate-300 leading-relaxed font-normal">
                                    {{ $item['description'] }}
                                </p>
                                <span class="sr-only">{{ $item['full_title'] }}</span>
                            </div>
                        </div>

                        <!-- Connecting Pin Down to Circle -->
                        <div class="w-px h-9 bg-amber-400/70 my-0.5"></div>

                        <!-- Glowing Amber Circle Node sitting in the lower level -->
                        <div class="relative flex items-center justify-center my-0.5 group-hover:scale-110 transition-transform">
                            <div class="absolute w-16 h-16 rounded-full bg-amber-400/25 blur-lg pointer-events-none"></div>
                            <div class="relative w-11 h-11 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 text-[#0A0A0A] font-mono text-xs font-black flex items-center justify-center shadow-[0_0_20px_rgba(245,158,11,0.5)] ring-2 ring-amber-300/40">
                                {{ $item['step'] }}
                            </div>
                        </div>

                        <!-- Empty Bottom Spacer to balance column height -->
                        <div class="w-full h-[155px] pointer-events-none"></div>

                    @else
                        <!-- Empty Top Spacer to position circle vertically centered beside top cards -->
                        <div class="w-full h-[95px] pointer-events-none"></div>

                        <!-- Glowing Amber Circle Node sitting in the upper level -->
                        <div class="relative flex items-center justify-center my-0.5 group-hover:scale-110 transition-transform">
                            <div class="absolute w-16 h-16 rounded-full bg-amber-400/25 blur-lg pointer-events-none"></div>
                            <div class="relative w-11 h-11 rounded-full bg-gradient-to-br from-amber-400 to-amber-500 text-[#0A0A0A] font-mono text-xs font-black flex items-center justify-center shadow-[0_0_20px_rgba(245,158,11,0.5)] ring-2 ring-amber-300/40">
                                {{ $item['step'] }}
                            </div>
                        </div>

                        <!-- Connecting Pin Down to Card -->
                        <div class="w-px h-9 bg-amber-400/70 my-0.5"></div>

                        <!-- Card (Bottom Position) -->
                        <div class="w-full h-[215px] rounded-2xl sm:rounded-3xl p-5 sm:p-6 {{ $item['card_bg'] }} border {{ $item['card_border'] }} shadow-lg flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
                            <!-- Subtle Ambient Glow -->
                            <div class="absolute -top-10 -right-10 w-28 h-28 rounded-full blur-2xl pointer-events-none opacity-40" style="background-color: {{ $item['glow_color'] }};"></div>

                            <!-- Huge Faint Watermark Number -->
                            <span class="absolute right-3 -bottom-2 font-heading text-7xl font-black text-white/[0.04] pointer-events-none select-none">
                                {{ $item['step'] }}
                            </span>

                            <div class="relative z-10">
                                <span class="inline-block px-2.5 py-0.5 rounded-md border text-[10px] font-mono font-bold uppercase tracking-wider mb-2.5 {{ $item['badge_bg'] }}">
                                    STEP {{ $item['step'] }}
                                </span>
                                <h3 class="font-heading text-sm sm:text-base font-bold uppercase text-white tracking-wide leading-snug mb-2">
                                    {{ $item['title'] }}
                                </h3>
                                <p class="font-sans text-xs text-slate-300 leading-relaxed font-normal">
                                    {{ $item['description'] }}
                                </p>
                                <span class="sr-only">{{ $item['full_title'] }}</span>
                            </div>
                        </div>

                        <!-- Empty Bottom Balance Spacer -->
                        <div class="w-full h-[30px] pointer-events-none"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Centered Scroll Cue with Subtle Controls -->
    <div class="flex items-center justify-center gap-4 mt-8">
        <button
            type="button"
            id="process-prev-btn"
            aria-label="Previous placement process step"
            class="w-8 h-8 rounded-full border border-slate-300 bg-white text-slate-600 hover:text-[#FB6221] hover:border-[#FB6221] flex items-center justify-center transition-all duration-200 shadow-2xs group"
        >
            <span class="material-symbols-outlined text-[16px] transition-transform group-hover:-translate-x-0.5">arrow_back</span>
        </button>
        <span class="font-mono text-xs uppercase tracking-widest text-slate-500 font-bold">
            Scroll to Explore &rarr;
        </span>
        <button
            type="button"
            id="process-next-btn"
            aria-label="Next placement process step"
            class="w-8 h-8 rounded-full border border-slate-300 bg-white text-slate-600 hover:text-[#FB6221] hover:border-[#FB6221] flex items-center justify-center transition-all duration-200 shadow-2xs group"
        >
            <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-0.5">arrow_forward</span>
        </button>
    </div>
</section>

@push('scripts')
<script>
(function() {
    const track = document.getElementById('process-carousel-track');
    const prevBtn = document.getElementById('process-prev-btn');
    const nextBtn = document.getElementById('process-next-btn');

    if (!track || !prevBtn || !nextBtn) return;

    function getScrollStep() {
        return track.offsetWidth > 640 ? 320 : 270;
    }

    prevBtn.addEventListener('click', function() {
        track.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', function() {
        track.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
    });

    track.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            track.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
            e.preventDefault();
        } else if (e.key === 'ArrowRight') {
            track.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
            e.preventDefault();
        }
    });
})();
</script>
@endpush
