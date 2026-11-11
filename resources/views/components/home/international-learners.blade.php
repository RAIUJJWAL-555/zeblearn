@php
    $internationalLearners = [
        [
            'id' => 'intl-uae',
            'name' => 'Fahad Al-Mansoori',
            'country' => 'United Arab Emirates',
            'flag' => '🇦🇪',
            'country_code' => 'UAE',
            'city' => 'Dubai',
            'course' => 'SAP FICO',
            'quote' => 'Joining from Dubai was seamless. The weekend schedule aligned with Gulf timing, and having direct sandbox access to configure general ledgers and asset accounting made complex enterprise processes clear.',
            'initials' => 'FA',
        ],
        [
            'id' => 'intl-ksa',
            'name' => 'Tariq Al-Harbi',
            'country' => 'Saudi Arabia',
            'flag' => '🇸🇦',
            'country_code' => 'Saudi Arabia',
            'city' => 'Riyadh',
            'course' => 'SAP MM',
            'quote' => 'Procurement and inventory management were explained with real corporate scenarios. The trainer accommodated doubts patiently, and the recorded sessions were invaluable for revising after work hours.',
            'initials' => 'TH',
        ],
        [
            'id' => 'intl-sa',
            'name' => 'Kagiso Ndlovu',
            'country' => 'South Africa',
            'flag' => '🇿🇦',
            'country_code' => 'South Africa',
            'city' => 'Johannesburg',
            'course' => 'SAP SD',
            'quote' => 'The time difference was only 3.5 hours, making live sessions very accessible from Joburg. Hands-on configuration of pricing procedures and billing flows gave me practical enterprise expertise.',
            'initials' => 'KN',
        ],
        [
            'id' => 'intl-oman',
            'name' => 'Salim Al-Busaidi',
            'country' => 'Oman',
            'flag' => '🇴🇲',
            'country_code' => 'Oman',
            'city' => 'Muscat',
            'course' => 'SAP FICO',
            'quote' => 'Excellent hands-on teaching style. Working on actual company code configurations and cost center accounting helped me upgrade my enterprise finance profile in Muscat.',
            'initials' => 'SA',
        ],
        [
            'id' => 'intl-my',
            'name' => 'Tan Wei Meng',
            'country' => 'Malaysia',
            'flag' => '🇲🇾',
            'country_code' => 'Malaysia',
            'city' => 'Kuala Lumpur',
            'course' => 'SAP PP',
            'quote' => 'The step-by-step guidance through Bill of Materials, routing, and MRP execution was thorough. Practical system access made a huge difference compared to theoretical courses.',
            'initials' => 'TW',
        ],
        [
            'id' => 'intl-in',
            'name' => 'Sneha Nair',
            'country' => 'India',
            'flag' => '🇮🇳',
            'country_code' => 'India',
            'city' => 'Bangalore',
            'course' => 'SAP MM',
            'quote' => 'Detailed sessions on vendor master, purchase orders, and inventory clearing with trainer Amir. The mock interviews and resume guidance gave me solid interview confidence.',
            'initials' => 'SN',
        ],
    ];

    $countryList = [
        ['name' => 'United Arab Emirates', 'flag' => '🇦🇪'],
        ['name' => 'Saudi Arabia', 'flag' => '🇸🇦'],
        ['name' => 'South Africa', 'flag' => '🇿🇦'],
        ['name' => 'Oman', 'flag' => '🇴🇲'],
        ['name' => 'Malaysia', 'flag' => '🇲🇾'],
        ['name' => 'India', 'flag' => '🇮🇳'],
    ];
@endphp

<!-- =========================================================================
     SECTION 1: LEARNERS FROM AROUND THE WORLD (INFINITE MARQUEE)
     ========================================================================= -->
<section id="international-learners" class="w-full py-16 lg:py-24 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto border-t border-slate-200 overflow-hidden" aria-labelledby="intl-heading">
    <!-- Header with Clean Title & Navigation Controls -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10" data-reveal>
        <div class="space-y-3 max-w-2xl">
            <h2 id="intl-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-[#0A0A0A] tracking-tight leading-tight">
                LEARNERS FROM AROUND THE WORLD
            </h2>
            <p class="font-sans text-sm sm:text-base text-slate-600 font-normal leading-relaxed">
                Live instructor-led online batches structured with timezone-friendly scheduling, enabling working professionals and graduates across India, the GCC, Africa, and Southeast Asia to master enterprise SAP S/4HANA systems directly from their home or office.
            </p>
            <!-- Clean Country Mention List -->
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-1 text-xs text-slate-600 font-medium">
                @foreach($countryList as $c)
                    <span class="inline-flex items-center gap-1.5">
                        <span aria-hidden="true">{{ $c['flag'] }}</span>
                        <span>{{ $c['name'] }}</span>
                    </span>
                @endforeach
            </div>
        </div>

        <!-- Marquee Navigation Controls -->
        <div class="flex items-center gap-2 self-start lg:self-end shrink-0">
            <button
                type="button"
                id="intl-prev-btn"
                aria-label="Reverse marquee direction"
                class="w-11 h-11 rounded-full bg-white border border-slate-300 text-[#0A0A0A] hover:bg-[#FB6221] hover:text-white hover:border-[#FB6221] transition-all duration-200 shadow-xs flex items-center justify-center group"
                title="Scroll backward"
            >
                <span class="material-symbols-outlined text-[20px] transition-transform group-hover:-translate-x-0.5">arrow_back</span>
            </button>
            <button
                type="button"
                id="intl-next-btn"
                aria-label="Forward marquee direction"
                class="w-11 h-11 rounded-full bg-white border border-slate-300 text-[#0A0A0A] hover:bg-[#FB6221] hover:text-white hover:border-[#FB6221] transition-all duration-200 shadow-xs flex items-center justify-center group"
                title="Scroll forward"
            >
                <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-0.5">arrow_forward</span>
            </button>
        </div>
    </div>

    <!-- Infinite Seamless Marquee Container -->
    <div class="zeb-marquee-container -mx-4 px-4 sm:-mx-6 sm:px-6 lg:-mx-12 lg:px-12 py-3" id="intl-marquee-wrapper">
        <div
            id="intl-carousel-track"
            class="zeb-marquee-track zeb-marquee-left flex gap-6 items-stretch focus:outline-none"
            style="--zeb-marquee-duration: 45s;"
            tabindex="0"
            role="region"
            aria-label="International student testimonials infinite marquee"
        >
            <!-- Primary Set -->
            @foreach($internationalLearners as $learner)
                <div class="shrink-0 w-[300px] sm:w-[360px] lg:w-[400px] bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:shadow-lg hover:border-[#FB6221]/50 transition-all duration-300 flex flex-col justify-between group zeb-card-interactive">
                    <!-- Top Row: Country Tag -->
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-[#0A0A0A]">
                                <span class="text-sm select-none" aria-hidden="true">{{ $learner['flag'] }}</span>
                                <span>{{ $learner['country'] }}</span>
                            </div>
                            <span class="font-mono text-xs text-slate-500">
                                {{ $learner['city'] }}
                            </span>
                        </div>

                        <!-- Quote -->
                        <p class="font-sans text-xs sm:text-sm text-slate-700 leading-relaxed font-normal mb-6">
                            &ldquo;{{ $learner['quote'] }}&rdquo;
                        </p>
                    </div>

                    <!-- Bottom Row: Learner Profile & Module -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-[#0A0A0A] text-white flex items-center justify-center font-mono text-xs font-bold shrink-0 border border-slate-800">
                                {{ $learner['initials'] }}
                            </div>
                            <div class="min-w-0">
                                <span class="font-heading text-sm font-bold text-[#0A0A0A] block truncate">
                                    {{ $learner['name'] }}
                                </span>
                                <span class="font-mono text-[10px] text-slate-500 block truncate">
                                    {{ $learner['country_code'] }}
                                </span>
                            </div>
                        </div>

                        <span class="font-mono text-xs font-bold text-[#FB6221] bg-orange-50 px-3 py-1 rounded-full border border-orange-200/60 shrink-0 text-right">
                            {{ $learner['course'] }}
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Duplicate Set for Seamless Continuous Loop -->
            @foreach($internationalLearners as $learner)
                <div class="shrink-0 w-[300px] sm:w-[360px] lg:w-[400px] bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xs hover:shadow-lg hover:border-[#FB6221]/50 transition-all duration-300 flex flex-col justify-between group zeb-card-interactive" aria-hidden="true">
                    <!-- Top Row: Country Tag -->
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-[#0A0A0A]">
                                <span class="text-sm select-none">{{ $learner['flag'] }}</span>
                                <span>{{ $learner['country'] }}</span>
                            </div>
                            <span class="font-mono text-xs text-slate-500">
                                {{ $learner['city'] }}
                            </span>
                        </div>

                        <!-- Quote -->
                        <p class="font-sans text-xs sm:text-sm text-slate-700 leading-relaxed font-normal mb-6">
                            &ldquo;{{ $learner['quote'] }}&rdquo;
                        </p>
                    </div>

                    <!-- Bottom Row: Learner Profile & Module -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-[#0A0A0A] text-white flex items-center justify-center font-mono text-xs font-bold shrink-0 border border-slate-800">
                                {{ $learner['initials'] }}
                            </div>
                            <div class="min-w-0">
                                <span class="font-heading text-sm font-bold text-[#0A0A0A] block truncate">
                                    {{ $learner['name'] }}
                                </span>
                                <span class="font-mono text-[10px] text-slate-500 block truncate">
                                    {{ $learner['country_code'] }}
                                </span>
                            </div>
                        </div>

                        <span class="font-mono text-xs font-bold text-[#FB6221] bg-orange-50 px-3 py-1 rounded-full border border-orange-200/60 shrink-0 text-right">
                            {{ $learner['course'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<script>
(function() {
    const track = document.getElementById('intl-carousel-track');
    const prevBtn = document.getElementById('intl-prev-btn');
    const nextBtn = document.getElementById('intl-next-btn');

    if (!track || !prevBtn || !nextBtn) return;

    prevBtn.addEventListener('click', function() {
        track.style.animationDirection = 'reverse';
        track.style.animationPlayState = 'running';
    });

    nextBtn.addEventListener('click', function() {
        track.style.animationDirection = 'normal';
        track.style.animationPlayState = 'running';
    });
})();
</script>
@endpush
