@php
    $videoTestimonials = [
        [
            'id' => 'sneha-mm',
            'name' => 'Sneha',
            'course' => 'SAP MM (Materials Management)',
            'description' => 'Shared feedback on SAP MM training with trainer Amir, clearing complex procurement scenarios and building career confidence.',
            'has_video' => false, // Video ID pending verification from YouTube channel
            'youtube_url' => null, // Channel link not presented as video
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Alumni Review',
            'location' => 'Nagpur, Maharashtra',
            'format' => 'Written Story',
        ],
        [
            'id' => 'ganesh-core',
            'name' => 'Ganesh',
            'course' => 'SAP Core & ERP Fundamentals',
            'description' => 'Detailed feedback on trainer expertise, step-by-step doubt clearing and hands-on system access across business modules.',
            'has_video' => false,
            'youtube_url' => null,
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Alumni Review',
            'location' => 'Verified Alum',
            'format' => 'Written Story',
        ],
        [
            'id' => 'damini-online',
            'name' => 'Damini',
            'course' => 'Online SAP Training',
            'description' => 'Walked through her experience with live online classes, instructor interaction, and real-time business assignments.',
            'has_video' => false,
            'youtube_url' => null,
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Learning Journey',
            'location' => 'Online Batch',
            'format' => 'Written Story',
        ],
        [
            'id' => 'sumit-fico',
            'name' => 'Sumit Singh',
            'course' => 'SAP FICO Training',
            'description' => 'Trained on general ledger, accounts payable, and asset accounting before cracking his interview at Accenture.',
            'has_video' => false,
            'youtube_url' => null,
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Alumni Story',
            'location' => 'Placed at Accenture',
            'format' => 'Written Story',
        ],
        [
            'id' => 'deeksha-fico',
            'name' => 'Deeksha',
            'course' => 'SAP FICO Fast Track',
            'description' => 'Shared how trainer mentorship and resume assistance helped her prepare for enterprise hiring rounds with confidence.',
            'has_video' => false,
            'youtube_url' => null,
            'youtube_id' => null,
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => true,
            'tag' => 'Interview Prep',
            'location' => 'Online Fast Track',
            'format' => 'Written Story',
        ],
        [
            'id' => 'placeholder-story',
            'name' => 'Student Video Slot',
            'course' => 'SAP Module Testimonial',
            'description' => '[Video Placeholder] New verified student video reviews will be embedded here upon publication of individual YouTube video IDs.',
            'has_video' => false,
            'youtube_url' => null,
            'youtube_id' => null, // Placeholder: Client to supply exact YouTube video ID (e.g. dQw4w9WgXcQ)
            'thumbnail' => asset('images/video-thumbnail.webp'),
            'verified' => false,
            'tag' => 'Upcoming Video',
            'location' => 'Video Slot',
            'format' => 'Pending Video Embed',
        ],
    ];
@endphp

<section id="stories" class="w-full bg-[#0A0A0A] text-white py-16 lg:py-24 border-t border-slate-800 relative overflow-hidden" aria-labelledby="video-testimonials-heading">
    <!-- Subtle Ambient Radial Glow -->
    <div class="absolute -left-32 top-1/2 -translate-y-1/2 w-96 h-96 bg-[#FB6221]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 relative z-10">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10" data-reveal>
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-white font-mono text-[10px] font-semibold uppercase tracking-widest border border-white/10 mb-3">
                    <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                    <span>ALUMNI VOICES · VERIFIED EXPERIENCES</span>
                </div>
                <h2 id="video-testimonials-heading" class="font-heading text-3xl sm:text-4xl lg:text-5xl font-bold uppercase text-white tracking-tight leading-tight">
                    HEAR FROM OUR STUDENTS
                </h2>
                <p class="text-sm sm:text-base text-slate-300 max-w-xl font-normal leading-relaxed mt-2">
                    Real learning experiences and career journeys from ZebLearn students.
                </p>
            </div>

            <!-- Desktop Controls (Next / Prev Arrows) -->
            <div class="hidden sm:flex items-center gap-3">
                <button
                    id="carousel-prev-btn"
                    type="button"
                    aria-label="Previous video testimonial"
                    class="w-11 h-11 rounded-full border border-white/20 bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-all duration-200 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </button>
                <button
                    id="carousel-next-btn"
                    type="button"
                    aria-label="Next video testimonial"
                    class="w-11 h-11 rounded-full border border-white/20 bg-white/5 hover:bg-white/15 text-white flex items-center justify-center transition-all duration-200 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </button>
            </div>
        </div>

        <!-- Horizontal Scroll Carousel -->
        <div
            id="video-carousel"
            tabindex="0"
            role="region"
            aria-label="Student Video Testimonials Carousel"
            data-reveal
            class="flex gap-5 overflow-x-auto snap-x snap-mandatory scroll-smooth pb-4 outline-none focus-visible:ring-2 focus-visible:ring-[#FB6221]/50 rounded-2xl [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach($videoTestimonials as $item)
                <article
                    class="w-[300px] sm:w-[340px] md:w-[360px] shrink-0 snap-start flex flex-col justify-between rounded-2xl bg-white/[0.04] border border-white/10 hover:border-[#FB6221]/50 p-4 sm:p-5 transition-all duration-300 shadow-lg group focus-within:border-[#FB6221] zeb-card-interactive">
                    <div>
                        <!-- Media Card Container -->
                        @if($item['has_video'] && !empty($item['youtube_id']))
                            <!-- Verified Video Card with Play Button -->
                            <div
                                class="relative aspect-video rounded-xl overflow-hidden bg-slate-900 border border-white/10 cursor-pointer group/thumb"
                                onclick="handleVideoClick('{{ $item['youtube_id'] }}', '{{ addslashes($item['name']) }}', '{{ addslashes($item['course']) }}')"
                                role="button"
                                tabindex="0"
                                onkeydown="if(event.key==='Enter'||event.key===' ') { event.preventDefault(); handleVideoClick('{{ $item['youtube_id'] }}', '{{ addslashes($item['name']) }}', '{{ addslashes($item['course']) }}'); }"
                                aria-label="Play verified YouTube review by {{ $item['name'] }} for {{ $item['course'] }}">

                                <img
                                    src="{{ $item['thumbnail'] }}"
                                    alt="Video thumbnail for {{ $item['name'] }} - {{ $item['course'] }}"
                                    width="400"
                                    height="225"
                                    loading="lazy"
                                    decoding="async"
                                    style="aspect-ratio: 16/9;"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover/thumb:scale-105" />

                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/30"></div>

                                <!-- YouTube Watermark Badge -->
                                <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-black/75 backdrop-blur-sm text-[10px] font-mono text-white tracking-wider border border-white/10">
                                    <svg class="w-3.5 h-3.5 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    <span class="font-bold">YOUTUBE</span>
                                </span>

                                <span class="absolute bottom-2.5 right-2.5 px-2 py-0.5 rounded bg-black/80 text-[10px] font-mono text-slate-300">
                                    {{ $item['tag'] }}
                                </span>

                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-[#FB6221] text-white flex items-center justify-center shadow-[0_0_24px_rgba(251,98,33,0.5)] group-hover/thumb:scale-110 group-hover/thumb:bg-[#FF0000] transition-all duration-300">
                                        <span class="material-symbols-outlined text-[26px] ml-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Verified Student Story Card (No misleading play button) -->
                            <div class="relative aspect-video rounded-xl overflow-hidden bg-slate-900/90 border border-white/10 p-4 flex flex-col justify-between">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-white/10 text-[10px] font-mono text-slate-300 tracking-wider border border-white/10">
                                        <span class="material-symbols-outlined text-[13px] text-[#FB6221]">article</span>
                                        <span>{{ $item['format'] }}</span>
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-white/5 text-[10px] font-mono text-slate-400">
                                        {{ $item['tag'] }}
                                    </span>
                                </div>

                                <!-- Centered Quote / Status Graphic -->
                                <div class="flex items-center justify-center py-2">
                                    @if($item['verified'])
                                        <div class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 group-hover:text-[#FB6221] group-hover:border-[#FB6221]/30 transition-all">
                                            <span class="material-symbols-outlined text-[26px]">format_quote</span>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-full border border-dashed border-white/20 flex items-center justify-center text-slate-500">
                                            <span class="material-symbols-outlined text-[22px]">videocam</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <span class="font-mono text-[10px] text-slate-500 uppercase">
                                        {{ $item['verified'] ? 'Verified Record' : 'Slot Reserved' }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Card Metadata -->
                        <div class="pt-4">
                            <div class="flex items-center justify-between font-mono text-[11px] mb-1">
                                <span class="font-bold uppercase tracking-wider text-[#FB6221]">{{ $item['course'] }}</span>
                                <span class="text-slate-400 text-[10px] uppercase">{{ $item['location'] }}</span>
                            </div>

                            <h3 class="font-heading text-lg sm:text-xl font-bold uppercase text-white tracking-snug">
                                {{ $item['name'] }}
                            </h3>

                            <p class="font-sans text-xs sm:text-sm text-slate-300 line-clamp-3 leading-relaxed mt-2 font-normal">
                                {{ $item['description'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer Button -->
                    <div class="pt-4 mt-4 border-t border-white/10 flex items-center justify-between">
                        @if($item['has_video'] && !empty($item['youtube_id']))
                            <button
                                type="button"
                                onclick="handleVideoClick('{{ $item['youtube_id'] }}', '{{ addslashes($item['name']) }}', '{{ addslashes($item['course']) }}')"
                                class="inline-flex items-center gap-1.5 font-['Inter'] text-xs font-bold text-white group-hover:text-[#FB6221] transition-colors uppercase tracking-wider cursor-pointer">
                                <span>Watch Video Review</span>
                                <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </button>
                        @elseif($item['verified'])
                            <span class="inline-flex items-center gap-1.5 font-mono text-xs text-slate-300">
                                <span class="material-symbols-outlined text-[15px] text-[#FB6221]">verified</span>
                                <span>Verified Feedback</span>
                            </span>
                        @else
                            <span class="font-mono text-xs text-slate-500 italic">
                                [Pending Video ID]
                            </span>
                        @endif

                        <span class="font-mono text-[10px] text-slate-400 uppercase">
                            {{ $item['verified'] ? 'Alumni Record' : 'Placeholder' }}
                        </span>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Mobile Touch Swipe Hint -->
        <div class="flex sm:hidden items-center justify-between text-xs text-slate-400 font-mono mt-3">
            <span>← SWIPE TO EXPLORE →</span>
            <span>{{ count($videoTestimonials) }} STORIES</span>
        </div>
    </div>
</section>

<!-- Accessible Privacy-Enhanced YouTube Player Modal (Ready for verified video IDs) -->
<div
    id="yt-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/85 backdrop-blur-md"
    role="dialog"
    aria-modal="true"
    aria-labelledby="yt-modal-title">

    <div class="relative w-full max-w-4xl bg-[#0A0A0A] border border-white/15 rounded-3xl overflow-hidden shadow-2xl flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-white/10">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-[#FB6221]"></span>
                <h3 id="yt-modal-title" class="font-heading text-base sm:text-lg font-bold uppercase text-white tracking-snug">
                    Student Testimonial
                </h3>
            </div>
            <button
                id="yt-modal-close"
                type="button"
                aria-label="Close video player"
                class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Video Player Wrapper (16:9 Aspect Ratio) -->
        <div class="relative aspect-video w-full bg-black flex items-center justify-center">
            <iframe
                id="yt-modal-iframe"
                class="w-full h-full border-0"
                src=""
                title="Student Video Testimonial"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen></iframe>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-3.5 bg-white/[0.03] border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 font-mono text-xs text-slate-400">
            <span id="yt-modal-subtitle">ZebLearn SAP Training · Authentic Learner Experience</span>
            <a
                id="yt-modal-link"
                href="https://www.youtube.com/@ZEBLEARN"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 text-white hover:text-[#FB6221] transition-colors font-bold uppercase">
                <span>Open On YouTube</span>
                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
            </a>
        </div>
    </div>
</div>

<script>
function handleVideoClick(videoId, studentName, course) {
    if (videoId && videoId !== 'null' && videoId.trim() !== '') {
        const modal = document.getElementById('yt-modal');
        const iframe = document.getElementById('yt-modal-iframe');
        const title = document.getElementById('yt-modal-title');
        const subtitle = document.getElementById('yt-modal-subtitle');
        const ytLink = document.getElementById('yt-modal-link');

        if (modal && iframe) {
            title.textContent = studentName ? `${studentName} — ${course}` : 'Student Experience';
            subtitle.textContent = `ZebLearn · ${course}`;
            ytLink.href = `https://www.youtube.com/watch?v=${videoId}`;
            // Privacy-enhanced YouTube domain
            iframe.src = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            const closeBtn = document.getElementById('yt-modal-close');
            if (closeBtn) closeBtn.focus();
        }
    }
}

function closeVideoModal() {
    const modal = document.getElementById('yt-modal');
    const iframe = document.getElementById('yt-modal-iframe');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        if (iframe) iframe.src = ''; // Stops audio playback immediately
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Carousel Controls
    const carousel = document.getElementById('video-carousel');
    const prevBtn = document.getElementById('carousel-prev-btn');
    const nextBtn = document.getElementById('carousel-next-btn');

    if (carousel && prevBtn && nextBtn) {
        const scrollAmount = 360;

        prevBtn.addEventListener('click', function () {
            carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', function () {
            carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        // Keyboard arrow navigation on carousel focus
        carousel.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        });
    }

    // Modal Close Triggers
    const closeBtn = document.getElementById('yt-modal-close');
    const modal = document.getElementById('yt-modal');

    if (closeBtn) {
        closeBtn.addEventListener('click', closeVideoModal);
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) closeVideoModal();
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeVideoModal();
    });
});
</script>
