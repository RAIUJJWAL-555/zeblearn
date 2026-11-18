@props([
    'course' => null,
    'sourceType' => 'blog_bridge'
])

@if($course)
<section aria-label="Featured Course Gateway" class="my-10 p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-900 via-[#111827] to-slate-950 text-white relative overflow-hidden border border-slate-800 shadow-xl group">
    {{-- Glow background --}}
    <div class="absolute -right-16 -top-16 w-56 h-56 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none transition-transform duration-500 group-hover:scale-110"></div>
    <div class="absolute -left-16 -bottom-16 w-56 h-56 bg-orange-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2.5 max-w-xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold tracking-wider uppercase bg-[#FB6221]/20 text-[#FB6221] border border-[#FB6221]/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] animate-pulse"></span>
                    <span>RECOMMENDED ENTERPRISE PROGRAM</span>
                </span>
                @if($course->tag_badge)
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-white/10 text-slate-300">
                        {{ $course->tag_badge }}
                    </span>
                @endif
            </div>

            <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-white leading-snug">
                {{ $course->name }}
            </h3>

            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                {{ $course->short_description ?: 'Comprehensive, hands-on training with 24/7 dedicated system access, mentor guidance, and placement support.' }}
            </p>

            <div class="flex flex-wrap items-center gap-4 pt-1 text-[11px] font-mono text-slate-400">
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px] text-[#FB6221]">schedule</span>
                    <span>{{ $course->duration ?: '3 Months' }}</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px] text-[#FB6221]">layers</span>
                    <span>{{ $course->modules_count ?: 12 }} Modules</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[15px] text-amber-400">star</span>
                    <span class="text-white font-bold">{{ $course->rating ?: '4.9' }}</span>
                    <span>({{ number_format($course->ratings_count ?: 1280) }} reviews)</span>
                </div>
            </div>
        </div>

        {{-- Action Buttons with Lead Attribution --}}
        <div class="flex flex-col sm:flex-row md:flex-col shrink-0 gap-3">
            <a
                href="{{ $course->url }}?source={{ $sourceType }}&cta=view_course"
                class="px-5 py-3 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-mono text-xs font-bold uppercase tracking-wider text-center transition-all shadow-md hover:shadow-orange-500/20 flex items-center justify-center gap-2 group-hover:scale-[1.02]"
            >
                <span>Explore Full Curriculum</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>

            <a
                href="{{ route('book-demo') }}?course={{ urlencode($course->slug) }}&source={{ $sourceType }}&cta=free_demo"
                class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-mono text-xs font-bold uppercase tracking-wider text-center transition-all border border-white/15 flex items-center justify-center gap-2"
            >
                <span class="material-symbols-outlined text-[16px] text-emerald-400">event_available</span>
                <span>Claim Free Demo Seat</span>
            </a>
        </div>
    </div>
</section>
@endif
