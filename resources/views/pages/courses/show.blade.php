@extends('layouts.app')

@section('title', $course->name . ' | Professional Training | ZebLearn')
@section('description', $course->short_description ?? ('Learn ' . $course->name . ' online with live classes, real system practice, and placement assistance at ZebLearn.'))
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-['Inter'] antialiased min-h-screen")

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    <!-- HERO SECTION -->
    <section class="relative w-full pt-36 sm:pt-40 lg:pt-44 pb-10 px-4 sm:px-6 lg:px-12 max-w-[1440px] mx-auto overflow-hidden">
        <!-- Breadcrumb & Telemetry -->
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200 animate-hero-badge">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('courses.index') }}" class="hover:text-[#FB6221] transition-colors">Courses</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold truncate max-w-xs sm:max-w-md">{{ $course->name }}</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full {{ $course->isAvailable() ? 'bg-emerald-500 animate-pulse' : 'bg-[#FB6221]' }}"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">{{ $course->status_label }}</span>
            </div>
        </div>

        <!-- Headline & Badges -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-10">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-mono font-bold uppercase tracking-wider bg-orange-50 text-[#FB6221] border border-orange-200">
                        {{ $course->category_badge }}
                    </span>
                    @if($course->display_tag)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-mono font-black uppercase tracking-wider bg-amber-400 text-black shadow-2xs">
                            {{ $course->display_tag }}
                        </span>
                    @endif
                </div>

                <h1 class="font-heading text-3xl sm:text-5xl font-black uppercase tracking-tight text-[#0A0A0A] leading-tight">
                    {{ $course->name }}
                </h1>

                <p class="font-sans text-base sm:text-lg text-slate-600 leading-relaxed mt-4 max-w-3xl">
                    {{ $course->short_description }}
                </p>

                <!-- Meta Details Row -->
                <div class="flex flex-wrap items-center gap-4 mt-6 text-xs text-slate-600 font-medium">
                    <div class="flex items-center gap-1.5 text-amber-500">
                        <span class="material-symbols-outlined text-[18px] fill-current">star</span>
                        <strong class="text-slate-900 font-bold">{{ $course->display_rating }}</strong>
                        <span class="text-slate-500">({{ $course->display_ratings_count }} ratings)</span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">schedule</span>
                        <span>{{ $course->display_duration }}</span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">menu_book</span>
                        <span>{{ $course->display_modules }}</span>
                    </div>
                    <span class="text-slate-300">•</span>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-slate-400">group</span>
                        <span>{{ $course->display_students }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN BODY: 2-COLUMN SPLIT -->
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

            <!-- LEFT COLUMN: SYLLABUS & DETAILS (8 cols) -->
            <div class="lg:col-span-8 space-y-10">

                <!-- Course Description -->
                @if ($course->description)
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                        <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] mb-4">
                            Course Overview &amp; Curriculum Objectives
                        </h2>
                        <div class="font-sans text-sm sm:text-base text-slate-600 leading-relaxed space-y-4">
                            {!! nl2br(e($course->description)) !!}
                        </div>
                    </div>
                @endif

                <!-- Curriculum Modules Preview -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                        <div>
                            <h2 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A]">
                                Curriculum Modules
                            </h2>
                            <p class="text-xs text-slate-500 font-sans mt-0.5">
                                Practical, industry-standard training syllabus engineered for career readiness.
                            </p>
                        </div>
                        <span class="font-mono text-xs font-bold text-[#FB6221]">
                            {{ $course->modules->count() }} Modules
                        </span>
                    </div>

                    @if ($course->modules->isEmpty())
                        <div class="py-8 text-center text-slate-500 font-sans text-sm">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">library_books</span>
                            Curriculum details are being finalized for the next cohort. Speak with our admissions advisors for the complete syllabus PDF.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach ($course->modules as $index => $module)
                                <div class="py-4 flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold shrink-0 mt-0.5">
                                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div>
                                            <h3 class="font-heading text-base font-bold text-slate-900">
                                                {{ $module->title }}
                                            </h3>
                                            @if ($module->description)
                                                <p class="text-xs text-slate-500 font-sans mt-1">
                                                    {{ $module->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono text-slate-400 shrink-0">
                                        {{ $module->lessons_count }} Lessons
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Advisory CTA Card -->
                <x-course.cta />

            </div>

            <!-- RIGHT COLUMN: ENROLLMENT SIDEBAR (4 cols) -->
            <div class="lg:col-span-4 lg:sticky lg:top-28 space-y-6">
                <div class="bg-[#0A0A0A] text-white rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-xl relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <span class="font-mono text-[10px] font-bold uppercase text-[#FB6221] tracking-widest">
                            {{ $course->isAvailableForEnrollment() ? 'ENROLMENT OPEN' : 'NEXT BATCH' }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-white font-mono text-[10px] uppercase">
                            LIVE ONLINE
                        </span>
                    </div>

                    <!-- Tuition Fee Display -->
                    <div class="mb-6">
                        <div class="flex items-baseline gap-2">
                            <span class="font-heading text-4xl sm:text-5xl font-extrabold leading-none text-white tracking-tight">
                                {{ $course->formatted_fee ?? ('₹' . number_format($course->fee ?? 75000)) }}
                            </span>
                            <span class="font-mono text-xs uppercase text-slate-400 font-bold">+ GST</span>
                        </div>
                        <p class="font-sans text-xs text-slate-400 mt-1">3-month live online interactive classes. Placement support included.</p>
                    </div>

                    <!-- Highlights Table -->
                    <div class="divide-y divide-white/10 mb-6 font-['Inter'] text-xs">
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Duration:</span>
                            <span class="font-bold text-white">{{ $course->display_duration }}</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Format:</span>
                            <span class="font-bold text-white">Live Online + System Labs</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Certification:</span>
                            <span class="font-bold text-white">Course Completion Certificate</span>
                        </div>
                        <div class="py-2.5 flex items-center justify-between">
                            <span class="text-slate-400">Placement:</span>
                            <span class="font-bold text-white">PlacementBaba Network</span>
                        </div>
                    </div>

                    <!-- 5-STATE ENROLLMENT CTA ACTIONS -->
                    <div class="space-y-3 mb-6">
                        @if (! $course->isAvailableForEnrollment())
                            @if ($course->status === 'upcoming')
                                <a
                                    href="{{ route('book-demo', ['course' => $course->slug]) }}"
                                    class="w-full py-3.5 px-4 bg-amber-400 hover:bg-amber-500 text-black font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                >
                                    <span>Pre-Register for Next Batch</span>
                                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            @else
                                <div class="w-full py-3 px-4 bg-white/10 rounded-xl text-center font-mono text-xs text-slate-300">
                                    Curriculum in Development
                                </div>
                            @endif
                        @else
                            {{-- Course Available: 5-state machine --}}
                            @if ($enrollment && $enrollment->isApproved())
                                <a
                                    href="{{ route('student.courses.show', $course) }}"
                                    class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                >
                                    <span class="material-symbols-outlined text-[16px]">play_circle</span>
                                    <span>Continue Learning / View Course</span>
                                </a>
                            @elseif ($enrollment && $enrollment->isPending())
                                <div class="w-full py-3 px-4 bg-amber-500/20 border border-amber-500/50 text-amber-300 font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl text-center flex items-center justify-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span>Awaiting Approval</span>
                                </div>
                                <p class="text-[11px] text-center text-slate-400 font-sans">
                                    Your enrollment request is pending review by admissions.
                                </p>
                            @elseif ($enrollment && ($enrollment->isRejected() || $enrollment->isCancelled()))
                                <div class="space-y-2 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md bg-red-500/20 text-red-300 text-[10px] font-mono font-bold uppercase border border-red-500/30">
                                        Application Declined
                                    </span>
                                    <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="w-full py-3 px-4 bg-white/20 hover:bg-[#FB6221] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all text-center"
                                        >
                                            <span>Re-apply for Enrollment</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                {{-- Not Enrolled --}}
                                @auth
                                    @if (auth()->user()->isStudent())
                                        <form action="{{ route('courses.enroll', $course) }}" method="POST">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2"
                                            >
                                                <span>Enroll Now</span>
                                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="w-full py-2.5 px-4 bg-white/10 rounded-xl text-center text-xs text-slate-300">
                                            Staff View
                                        </div>
                                    @endif
                                @else
                                    <a
                                        href="{{ route('courses.enroll.get', $course) }}"
                                        class="w-full py-3.5 px-4 bg-[#FB6221] hover:bg-[#e05214] text-white font-['Inter'] text-xs font-bold uppercase tracking-wider rounded-xl transition-all shadow-md flex items-center justify-center gap-2 text-center"
                                    >
                                        <span>Enroll Now</span>
                                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                    </a>
                                @endauth
                            @endif
                        @endif

                        {{-- Secondary Public Demo CTA (Always functional) --}}
                        <a
                            href="{{ route('book-demo', ['course' => $course->slug]) }}"
                            class="w-full py-3 px-4 border border-white/20 hover:bg-white/10 text-white font-['Inter'] text-xs font-semibold uppercase tracking-wider rounded-xl transition-all flex items-center justify-center gap-2 text-center"
                        >
                            <span>Book a Free Demo</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </a>

                        <a
                            href="tel:+918506888288"
                            class="w-full py-2.5 px-4 text-slate-300 hover:text-white font-['Inter'] text-xs transition-colors flex items-center justify-center gap-2 text-center"
                        >
                            <span class="material-symbols-outlined text-[16px] text-[#FB6221]">call</span>
                            <span>+91 8506 888 288</span>
                        </a>
                    </div>

                    <!-- Refund Policy Reassurance -->
                    <div class="pt-4 border-t border-white/10 text-center">
                        <p class="font-mono text-[10px] text-slate-500 uppercase">
                            Refund policy applies. Speak with advisors for details.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>
@endsection

