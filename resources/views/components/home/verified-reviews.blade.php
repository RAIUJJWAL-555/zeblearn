@php
    $studentReviews = [
        [
            'id' => 'rev-ayesha',
            'name' => 'Ayesha Khan',
            'role' => 'Data Analyst',
            'initial' => 'A',
            'color' => 'bg-amber-500',
            'rating' => 5,
            'is_featured' => false,
            'quote' => "I joined ZebLearn's Data Science program to upgrade my skills. The Python, SQL, Power BI, and Machine Learning sessions were practical and beginner-friendly. I got placed as a Data Analyst with continuous mentor doubt solving.",
        ],
        [
            'id' => 'rev-rohit',
            'name' => 'Rohit Gupta',
            'role' => 'Data Scientist',
            'initial' => 'R',
            'color' => 'bg-amber-600',
            'rating' => 5,
            'is_featured' => false,
            'quote' => "The Data Science course at ZebLearn provided hands-on projects, real datasets, and expert mentorship. Their career guidance and interview preparation helped me transition into a top tech role with confidence.",
        ],
        [
            'id' => 'rev-nikita',
            'name' => 'Nikita Joshi',
            'role' => 'Machine Learning Engineer',
            'initial' => 'N',
            'color' => 'bg-amber-500',
            'rating' => 5,
            'is_featured' => false,
            'quote' => "ZebLearn's Machine Learning and AI training was very practical. The capstone projects and mentorship sessions improved my portfolio and helped me secure a high-paying tech role through their placement drive.",
        ],
        [
            'id' => 'rev-ganesh',
            'name' => 'Ganesh',
            'role' => 'SAP Functional Consultant',
            'initial' => 'G',
            'color' => 'bg-[#FB6221]',
            'rating' => 5,
            'is_featured' => true,
            'quote' => 'SAP Training with Real-Time Projects. I searched for ZebLearn reviews complaints, but my experience was excellent. Their SAP MM and FICO training is designed for real-world projects and direct system practice.',
        ],
        [
            'id' => 'rev-sumit',
            'name' => 'Sumit Singh',
            'role' => 'SAP FICO · Placed at Accenture',
            'initial' => 'S',
            'color' => 'bg-amber-600',
            'rating' => 5,
            'is_featured' => false,
            'quote' => 'It is due to the quality training provided here that I got placed in Accenture. The atmosphere is brilliant and everyone is studying hard. The trainers emphasize real system business workflows rather than just theoretical concepts.',
        ],
        [
            'id' => 'rev-deeksha',
            'name' => 'Deeksha',
            'role' => 'Fast Track SAP FICO',
            'initial' => 'D',
            'color' => 'bg-amber-500',
            'rating' => 5,
            'is_featured' => false,
            'quote' => 'I found ZebLearn the best place if you are searching for career growth. The trainers are very supportive and you will get ample opportunities here. The resume assistance helped me prepare for enterprise hiring rounds.',
        ],
        [
            'id' => 'rev-sneha',
            'name' => 'Sneha',
            'role' => 'SAP MM Specialist',
            'initial' => 'S',
            'color' => 'bg-amber-600',
            'rating' => 5,
            'is_featured' => false,
            'quote' => 'Detailed feedback on SAP MM training with trainer Amir, clearing complex procurement scenarios and building career confidence. The hands-on purchase order and inventory clearing practice was directly relevant.',
        ],
    ];

    $platforms = [
        ['name' => 'Sulekha', 'pct' => '96%', 'rating' => '4.8'],
        ['name' => 'Urbanpro', 'pct' => '96%', 'rating' => '4.8'],
        ['name' => 'JustDial', 'pct' => '98%', 'rating' => '4.9'],
        ['name' => 'Facebook', 'pct' => '96%', 'rating' => '4.8'],
    ];
@endphp

<!-- =========================================================================
     SECTION 4: VERIFIED STUDENT REVIEWS (INFINITE MARQUEE)
     ========================================================================= -->
<section id="verified-reviews" class="w-full py-16 lg:py-24 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto border-t border-slate-200 overflow-hidden" aria-labelledby="reviews-heading">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-10 lg:mb-12" data-reveal>
        <div class="inline-flex items-center gap-3 text-xs font-mono font-bold text-[#FB6221] uppercase tracking-widest mb-3">
            <span class="w-8 h-px bg-[#FB6221]/50"></span>
            <span>VERIFIED REVIEWS</span>
            <span class="w-8 h-px bg-[#FB6221]/50"></span>
        </div>
        <h2 id="reviews-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-[#0A0A0A] tracking-tight leading-tight mb-4">
            WHAT OUR <span class="text-[#FB6221]">STUDENTS SAY</span>
        </h2>
        <p class="font-sans text-sm sm:text-base text-slate-600 font-normal leading-relaxed">
            Read genuine reviews from learners who gained certifications and career growth with ZebLearn India.
        </p>
    </div>

    <!-- Multi-Platform Rating Summary Box -->
    <div class="bg-white border border-slate-200/90 rounded-[28px] p-6 sm:p-8 max-w-4xl mx-auto mb-10 shadow-xs" data-reveal>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            <!-- Left: Google Overall Rating -->
            <div class="md:col-span-5 flex flex-col items-center justify-center text-center">
                <div class="inline-flex items-center gap-1.5 mb-1">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                        <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                    </svg>
                    <span class="font-sans font-bold text-slate-800 text-sm">Google</span>
                </div>

                <div class="font-heading text-5xl sm:text-6xl font-black text-[#0A0A0A] leading-none my-1">
                    4.5
                </div>

                <div class="flex text-amber-400 gap-0.5 my-1" aria-label="4.5 out of 5 stars">
                    @for($s = 0; $s < 5; $s++)
                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">star</span>
                    @endfor
                </div>

                <span class="font-mono text-xs text-slate-400">
                    58,765+ Google reviews
                </span>
            </div>

            <!-- Divider -->
            <div class="hidden md:flex justify-center md:col-span-1">
                <div class="w-px h-28 bg-slate-200"></div>
            </div>

            <!-- Right: Platform Breakdown Progress Bars -->
            <div class="md:col-span-6 space-y-3">
                @foreach($platforms as $p)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold text-slate-700 w-20 shrink-0">
                            {{ $p['name'] }}
                        </span>
                        <div class="h-2 bg-slate-100 rounded-full flex-1 overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $p['pct'] }};"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-700 shrink-0 flex items-center gap-1 font-mono">
                            <span class="text-amber-400">★</span> {{ $p['rating'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Controls Bar -->
    <div class="flex items-center justify-between gap-4 mb-6 px-1">
        <span class="font-mono text-xs text-slate-500 uppercase font-bold tracking-wider">
            Verified Reviews (Google &amp; Partner Channels)
        </span>

        <div class="flex items-center gap-2">
            <button
                type="button"
                id="reviews-prev-btn"
                aria-label="Scroll reviews backward"
                class="w-10 h-10 rounded-full bg-white border border-slate-300 text-[#0A0A0A] hover:bg-[#FB6221] hover:text-white hover:border-[#FB6221] transition-all duration-200 shadow-xs flex items-center justify-center group"
                title="Scroll backward"
            >
                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:-translate-x-0.5">arrow_back</span>
            </button>
            <button
                type="button"
                id="reviews-next-btn"
                aria-label="Scroll reviews forward"
                class="w-10 h-10 rounded-full bg-white border border-slate-300 text-[#0A0A0A] hover:bg-[#FB6221] hover:text-white hover:border-[#FB6221] transition-all duration-200 shadow-xs flex items-center justify-center group"
                title="Scroll forward"
            >
                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-0.5">arrow_forward</span>
            </button>
        </div>
    </div>

    <!-- Infinite Seamless Marquee of Review Cards -->
    <div class="zeb-marquee-container -mx-4 px-4 sm:-mx-6 sm:px-6 lg:-mx-12 lg:px-12 py-3" id="reviews-marquee-wrapper">
        <div
            id="reviews-carousel-track"
            class="zeb-marquee-track zeb-marquee-left flex gap-5 sm:gap-6 items-stretch focus:outline-none"
            style="--zeb-marquee-duration: 48s;"
            tabindex="0"
            role="region"
            aria-label="Student reviews infinite marquee"
        >
            <!-- Primary Set -->
            @foreach($studentReviews as $review)
                <div class="shrink-0 w-[285px] sm:w-[325px] lg:w-[340px] bg-white rounded-3xl p-6 sm:p-7 {{ $review['is_featured'] ? 'border-2 border-[#FB6221] ring-4 ring-orange-50 shadow-md' : 'border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md' }} transition-all duration-300 flex flex-col justify-between group zeb-card-interactive select-none">
                    <div>
                        <!-- Card Top Row: Avatar + Name/Role + Google G Icon -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Circular Avatar with Initial -->
                                <div class="w-10 h-10 rounded-full {{ $review['color'] }} text-white font-bold flex items-center justify-center shrink-0 text-sm shadow-xs">
                                    {{ $review['initial'] }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-heading text-sm font-bold text-[#0A0A0A] block truncate">
                                        {{ $review['name'] }}
                                    </span>
                                    <span class="text-xs text-slate-500 block truncate">
                                        {{ $review['role'] }}
                                    </span>
                                    <!-- Star Rating -->
                                    <div class="flex text-amber-400 gap-0.5 mt-0.5" aria-label="5 stars">
                                        @for($s = 0; $s < 5; $s++)
                                            <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Google Icon (Top Right) -->
                            <div class="shrink-0 p-1 rounded-full bg-slate-50 border border-slate-100">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Quote Text -->
                        <p class="font-sans text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                            {{ $review['quote'] }}
                        </p>
                    </div>

                    <!-- Footer: Verified Status + Google Review Tag -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="font-semibold text-emerald-600 flex items-center gap-1">
                            <span class="text-sm">✓</span> Verified
                        </span>
                        <span class="text-slate-400 text-[11px] font-sans">
                            Google Review
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Duplicate Set for Seamless Continuous Loop -->
            @foreach($studentReviews as $review)
                <div class="shrink-0 w-[285px] sm:w-[325px] lg:w-[340px] bg-white rounded-3xl p-6 sm:p-7 {{ $review['is_featured'] ? 'border-2 border-[#FB6221] ring-4 ring-orange-50 shadow-md' : 'border border-slate-200 shadow-xs hover:border-slate-300 hover:shadow-md' }} transition-all duration-300 flex flex-col justify-between group zeb-card-interactive select-none" aria-hidden="true">
                    <div>
                        <!-- Card Top Row: Avatar + Name/Role + Google G Icon -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Circular Avatar with Initial -->
                                <div class="w-10 h-10 rounded-full {{ $review['color'] }} text-white font-bold flex items-center justify-center shrink-0 text-sm shadow-xs">
                                    {{ $review['initial'] }}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-heading text-sm font-bold text-[#0A0A0A] block truncate">
                                        {{ $review['name'] }}
                                    </span>
                                    <span class="text-xs text-slate-500 block truncate">
                                        {{ $review['role'] }}
                                    </span>
                                    <!-- Star Rating -->
                                    <div class="flex text-amber-400 gap-0.5 mt-0.5">
                                        @for($s = 0; $s < 5; $s++)
                                            <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Google Icon (Top Right) -->
                            <div class="shrink-0 p-1 rounded-full bg-slate-50 border border-slate-100">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.66-5.17 3.66-9.17z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24z"/>
                                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.13-1.55.38-2.27V6.58H1.25C.45 8.18 0 10.03 0 12s.45 3.82 1.25 5.42l4.03-3.15z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Quote Text -->
                        <p class="font-sans text-xs sm:text-[13px] text-slate-600 leading-relaxed font-normal mb-6">
                            {{ $review['quote'] }}
                        </p>
                    </div>

                    <!-- Footer: Verified Status + Google Review Tag -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="font-semibold text-emerald-600 flex items-center gap-1">
                            <span class="text-sm">✓</span> Verified
                        </span>
                        <span class="text-slate-400 text-[11px] font-sans">
                            Google Review
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
    const track = document.getElementById('reviews-carousel-track');
    const prevBtn = document.getElementById('reviews-prev-btn');
    const nextBtn = document.getElementById('reviews-next-btn');

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
