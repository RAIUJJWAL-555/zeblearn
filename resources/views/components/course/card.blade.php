@props([
    'course',
    'featured' => false,
    'enrollment' => null,
])

@php
    $enrollment = $enrollment ?? (auth()->check() ? auth()->user()->enrollments()->where('course_id', $course->id)->first() : null);
@endphp

<article class="group relative bg-white rounded-2xl sm:rounded-3xl border border-slate-200/90 hover:border-amber-400 p-5 sm:p-7 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-l-4 sm:border-l-[6px] border-l-amber-400 select-none zeb-card-interactive" data-reveal>
    
    <!-- Left Column: Main Course Information -->
    <div class="flex-1 min-w-0">
        <!-- Top Meta Row: Category Badge + Tag Badge + Know More Link -->
        <div class="flex items-center justify-between gap-3 mb-2.5">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-200/70">
                    {{ $course->category_badge }}
                </span>
                
                @if($course->display_tag)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-black uppercase tracking-wider bg-amber-400 text-black shadow-2xs">
                        {{ $course->display_tag }}
                    </span>
                @endif
            </div>

            <a href="{{ $course->url }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 hover:text-[#FB6221] hover:underline uppercase tracking-wide shrink-0">
                <span>Know More</span>
                <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
            </a>
        </div>

        <!-- Course Title -->
        <h3 class="font-heading text-xl sm:text-2xl font-black uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug mb-2">
            <a href="{{ $course->url }}">{{ $course->name }}</a>
        </h3>

        <!-- Short Description -->
        <p class="font-sans text-xs sm:text-sm text-slate-600 leading-relaxed mb-3.5 line-clamp-2 max-w-3xl">
            {{ $course->short_description }}
        </p>

        <!-- Star Rating Row -->
        <div class="flex items-center gap-2 text-xs text-slate-600 font-medium mb-4">
            <div class="flex items-center text-amber-400">
                <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                <span class="material-symbols-outlined text-[16px] fill-current">star</span>
                <span class="material-symbols-outlined text-[16px] fill-current">star</span>
            </div>
            <span class="font-bold text-[#0A0A0A]">{{ $course->display_rating }}</span>
            <span class="text-slate-500">({{ $course->display_ratings_count }} ratings)</span>
        </div>

        <!-- Bottom Detail Tags (Duration, Modules, Students) -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200/60 text-xs font-medium text-slate-700">
                {{ $course->display_duration }}
            </span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200/60 text-xs font-medium text-slate-700">
                {{ $course->display_modules }}
            </span>
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 border border-slate-200/60 text-xs font-medium text-slate-700">
                {{ $course->display_students }}
            </span>
        </div>
    </div>

    <!-- Right Column: Price & Enroll CTA -->
    <div class="lg:w-60 lg:border-l lg:border-slate-100 lg:pl-8 flex flex-col lg:items-center justify-center shrink-0 pt-4 lg:pt-0 border-t border-slate-100 lg:border-t-0">
        <div class="text-left lg:text-center mb-3">
            <div class="font-heading text-2xl sm:text-3xl font-black text-[#0A0A0A] tracking-tight">
                {{ $course->formatted_fee ?? ('₹' . number_format($course->fee ?? 75000)) }}
            </div>
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200">
                {{ $course->display_discount }}
            </span>
        </div>

        @if (! $course->isAvailableForEnrollment())
            @if ($course->status === 'upcoming')
                <a
                    href="{{ route('book-demo', ['course' => $course->slug]) }}"
                    class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-sans text-xs font-bold uppercase tracking-wider transition-colors"
                >
                    <span>Pre-Register</span>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-400 font-mono text-[11px] font-bold uppercase">
                    Roadmap Batch
                </span>
            @endif
        @else
            {{-- Course is available for enrollment: 5-state machine --}}
            @if ($enrollment && $enrollment->isApproved())
                <a
                    href="{{ route('student.courses.show', $course) }}"
                    class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-sans text-xs font-bold uppercase tracking-wider shadow-sm hover:shadow-md transition-all duration-200 hover:scale-[1.02]"
                >
                    <span class="material-symbols-outlined text-[16px] mr-1.5">play_circle</span>
                    <span>Continue Learning</span>
                </a>
            @elseif ($enrollment && $enrollment->isPending())
                <div class="w-full text-center">
                    <a
                        href="{{ route('student.dashboard') }}"
                        class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-900 font-sans text-xs font-bold uppercase tracking-wider transition-colors shadow-2xs"
                        title="View request on student dashboard"
                    >
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse mr-2"></span>
                        <span>Awaiting Approval</span>
                    </a>
                    <span class="block text-[10px] text-slate-500 mt-1 font-mono">Under Review</span>
                </div>
            @elseif ($enrollment && ($enrollment->isRejected() || $enrollment->isCancelled()))
                <div class="w-full space-y-1.5 text-center">
                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-md bg-red-50 text-red-700 text-[10px] font-mono font-bold uppercase border border-red-200">
                        Application Declined
                    </span>
                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center w-full px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-[#FB6221] text-white font-sans text-xs font-bold uppercase tracking-wider shadow-sm transition-all"
                        >
                            <span>Re-apply for Enrollment</span>
                        </button>
                    </form>
                </div>
            @else
                {{-- Not Enrolled --}}
                @auth
                    @if (auth()->user()->isStudent())
                        <form action="{{ route('courses.enroll', $course) }}" method="POST" class="w-full">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center w-full px-7 py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-black font-sans text-xs font-bold uppercase tracking-wider shadow-sm hover:shadow-md transition-all duration-200 hover:scale-[1.02]"
                            >
                                <span>Enroll Now</span>
                            </button>
                        </form>
                    @else
                        <span class="inline-flex items-center justify-center w-full px-4 py-2 rounded-xl bg-slate-100 text-slate-500 font-sans text-xs font-medium">
                            Staff View
                        </span>
                    @endif
                @else
                    {{-- Unauthenticated visitor: link triggers post-login redirect flow --}}
                    <a
                        href="{{ route('courses.enroll.get', $course) }}"
                        class="inline-flex items-center justify-center w-full px-7 py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-black font-sans text-xs font-bold uppercase tracking-wider shadow-sm hover:shadow-md transition-all duration-200 hover:scale-[1.02]"
                    >
                        <span>Enroll Now</span>
                    </a>
                @endauth
            @endif
        @endif
    </div>

</article>
