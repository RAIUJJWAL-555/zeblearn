@php
    $videoTestimonials = [
        [
            'id' => 'sneha-mm',
            'name' => 'Sneha',
            'course' => 'SAP MM (Materials Management)',
            'description' => 'Shared feedback on SAP MM training with trainer Amir, clearing complex procurement scenarios and building career confidence.',
            'has_video' => true,
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Alumni Review',
            'location' => 'Nagpur, Maharashtra',
            'format' => 'Video Review',
            'initial' => 'S',
            'rating' => 5,
        ],
        [
            'id' => 'ganesh-core',
            'name' => 'Ganesh',
            'course' => 'SAP Core & ERP Fundamentals',
            'description' => 'Detailed feedback on trainer expertise, step-by-step doubt clearing and hands-on system access across business modules.',
            'has_video' => true,
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Alumni Review',
            'location' => 'Verified Alum',
            'format' => 'Video Review',
            'initial' => 'G',
            'rating' => 5,
        ],
        [
            'id' => 'damini-online',
            'name' => 'Damini',
            'course' => 'Online SAP Training',
            'description' => 'Walked through her experience with live online classes, instructor interaction, and real-time business assignments.',
            'has_video' => true,
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Learning Journey',
            'location' => 'Online Batch',
            'format' => 'Video Review',
            'initial' => 'D',
            'rating' => 5,
        ],
        [
            'id' => 'sumit-fico',
            'name' => 'Sumit Singh',
            'course' => 'SAP FICO Training',
            'description' => 'Trained on general ledger, accounts payable, and asset accounting before cracking his interview at Accenture.',
            'has_video' => true,
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Alumni Story',
            'location' => 'Placed at Accenture',
            'format' => 'Video Review',
            'initial' => 'S',
            'rating' => 5,
        ],
        [
            'id' => 'deeksha-fico',
            'name' => 'Deeksha',
            'course' => 'SAP FICO Fast Track',
            'description' => 'Shared how trainer mentorship and resume assistance helped her prepare for enterprise hiring rounds with confidence.',
            'has_video' => true,
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Interview Prep',
            'location' => 'Online Fast Track',
            'format' => 'Video Review',
            'initial' => 'D',
            'rating' => 5,
        ],
        [
            'id' => 'placeholder-story',
            'name' => 'Student Video Story',
            'course' => 'SAP Module Testimonial',
            'description' => 'Watch authentic student feedback, placement journeys, and learning experiences directly on our official YouTube channel.',
            'has_video' => true,
            'youtube_url' => 'https://www.youtube.com/@ZEBLEARN',
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'YouTube Stories',
            'location' => 'ZebLearn Channel',
            'format' => 'Video Story',
            'initial' => 'Z',
            'rating' => 5,
        ],
    ];
@endphp

<!-- =========================================================================
     SECTION 5: VIDEO STORIES & FEEDBACK FROM STUDENTS (INFINITE MARQUEE)
     ========================================================================= -->
<section id="stories" class="w-full bg-[#0A0A0A] text-white py-16 lg:py-24 border-t border-slate-800 relative overflow-hidden" aria-labelledby="video-testimonials-heading">
    <!-- Subtle Ambient Radial Glow -->
    <div class="absolute -left-32 top-1/2 -translate-y-1/2 w-96 h-96 bg-[#FB6221]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 relative z-10">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10" data-reveal>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FB6221]/15 border border-[#FB6221]/30 text-[11px] font-mono font-bold uppercase tracking-wider text-[#FB6221] mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221]"></span>
                    <span>HEAR FROM OUR STUDENTS</span>
                </div>
                <h2 id="video-testimonials-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-black uppercase text-white tracking-tight leading-tight">
                  FEEDBACK FROM <span class="text-[#FB6221]">OUR STUDENTS</span>
                </h2>
                <p class="text-sm sm:text-base text-slate-300 max-w-xl font-normal leading-relaxed mt-2">
                    Real learning experiences and career journeys from ZebLearn students.
                </p>
            </div>

            <!-- Controls (Prev / Next Buttons) -->
            <div class="flex items-center gap-2 shrink-0">
                <button
                    id="carousel-prev-btn"
                    type="button"
                    aria-label="Previous student feedback"
                    class="w-11 h-11 rounded-full border border-white/20 bg-white/5 hover:bg-[#FB6221] hover:border-[#FB6221] hover:text-white text-white flex items-center justify-center transition-all duration-200 cursor-pointer shadow-xs group"
                    title="Scroll backward"
                >
                    <span class="material-symbols-outlined text-[20px] transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                </button>
                <button
                    id="carousel-next-btn"
                    type="button"
                    aria-label="Next student feedback"
                    class="w-11 h-11 rounded-full border border-white/20 bg-white/5 hover:bg-[#FB6221] hover:border-[#FB6221] hover:text-white text-white flex items-center justify-center transition-all duration-200 cursor-pointer shadow-xs group"
                    title="Scroll forward"
                >
                    <span class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-0.5">arrow_forward</span>
                </button>
            </div>
        </div>

        <!-- Normal Marquee for Student Feedback Cards -->
        <div class="zeb-marquee-container -mx-4 px-4 sm:-mx-6 sm:px-6 lg:-mx-12 lg:px-12 py-3" id="stories-marquee-wrapper">
            <div
                id="video-carousel"
                class="zeb-marquee-track zeb-marquee-left flex gap-6 items-stretch focus:outline-none"
                style="--zeb-marquee-duration: 45s;"
                tabindex="0"
                role="region"
                aria-label="Student Video Testimonials Carousel"
            >
                <!-- Primary Set -->
                @foreach($videoTestimonials as $item)
                    <article class="w-[300px] sm:w-[340px] md:w-[360px] shrink-0 flex flex-col justify-between rounded-3xl bg-white/[0.04] border border-white/10 hover:border-[#FB6221]/50 p-6 sm:p-7 transition-all duration-300 shadow-xl group zeb-card-interactive select-none">
                        <div>
                            <!-- Header with Initial Avatar and Stars -->
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FB6221] to-[#EA580C] text-white flex items-center justify-center font-heading font-black text-sm shrink-0 shadow-sm">
                                        {{ $item['initial'] }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-heading text-base font-bold text-white block truncate">
                                            {{ $item['name'] }}
                                        </h3>
                                        <span class="font-mono text-[11px] text-[#FB6221] block truncate">
                                            {{ $item['course'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex text-amber-400 gap-0.5 shrink-0" aria-label="5 stars">
                                    @for($s = 0; $s < 5; $s++)
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    @endfor
                                </div>
                            </div>

                            <!-- Feedback Quote -->
                            <p class="font-sans text-xs sm:text-sm text-slate-300 leading-relaxed font-normal mb-6">
                                &ldquo;{{ $item['description'] }}&rdquo;
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-3 border-t border-white/10 flex items-center justify-between font-mono text-[11px] text-slate-400">
                            <span class="inline-flex items-center gap-1.5 text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>{{ $item['tag'] }}</span>
                            </span>
                            <span class="text-slate-500">
                                {{ $item['location'] }}
                            </span>
                        </div>
                    </article>
                @endforeach

                <!-- Duplicate Set for Seamless Continuous Loop -->
                @foreach($videoTestimonials as $item)
                    <article class="w-[300px] sm:w-[340px] md:w-[360px] shrink-0 flex flex-col justify-between rounded-3xl bg-white/[0.04] border border-white/10 hover:border-[#FB6221]/50 p-6 sm:p-7 transition-all duration-300 shadow-xl group zeb-card-interactive select-none" aria-hidden="true">
                        <div>
                            <!-- Header with Initial Avatar and Stars -->
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FB6221] to-[#EA580C] text-white flex items-center justify-center font-heading font-black text-sm shrink-0 shadow-sm">
                                        {{ $item['initial'] }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-heading text-base font-bold text-white block truncate">
                                            {{ $item['name'] }}
                                        </h3>
                                        <span class="font-mono text-[11px] text-[#FB6221] block truncate">
                                            {{ $item['course'] }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex text-amber-400 gap-0.5 shrink-0">
                                    @for($s = 0; $s < 5; $s++)
                                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    @endfor
                                </div>
                            </div>

                            <!-- Feedback Quote -->
                            <p class="font-sans text-xs sm:text-sm text-slate-300 leading-relaxed font-normal mb-6">
                                &ldquo;{{ $item['description'] }}&rdquo;
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-3 border-t border-white/10 flex items-center justify-between font-mono text-[11px] text-slate-400">
                            <span class="inline-flex items-center gap-1.5 text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>{{ $item['tag'] }}</span>
                            </span>
                            <span class="text-slate-500">
                                {{ $item['location'] }}
                            </span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
            </div>
        </div>
    </div>
</section>

<!-- Accessible Video Modal (Preserved for tests & future embed) -->
<div
    id="yt-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="yt-modal-title"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4 hidden">
    <div class="relative w-full max-w-3xl rounded-2xl bg-slate-900 border border-white/10 overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between p-4 border-b border-white/10">
            <div>
                <h3 id="yt-modal-title" class="font-heading text-base font-bold text-white">Student Review</h3>
                <p id="yt-modal-desc" class="text-xs text-slate-400">Verified SAP Training Journey</p>
            </div>
            <button
                id="yt-modal-close"
                type="button"
                aria-label="Close video player modal"
                class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>
        <div class="relative aspect-video bg-black">
            <iframe
                id="yt-modal-iframe"
                src=""
                title="Verified Student Review"
                class="w-full h-full border-0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const track = document.getElementById('video-carousel');
    const prevBtn = document.getElementById('carousel-prev-btn');
    const nextBtn = document.getElementById('carousel-next-btn');

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
