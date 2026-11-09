@props([
    'course',
    'featured' => false,
])

@php
    $isAvailable = $course->isAvailable();
    $isUpcoming = $course->isUpcoming();
    $isPlanned = $course->isPlanned();
@endphp

<article class="group relative bg-white rounded-3xl p-6 sm:p-8 lg:p-9 border {{ $isAvailable ? 'border-slate-300 shadow-sm hover:border-[#FB6221]' : 'border-slate-200/90 hover:border-slate-300' }} transition-all duration-300 shadow-xs hover:shadow-lg flex flex-col justify-between zeb-card-interactive" data-reveal>
    
    <!-- Top Meta Row -->
    <div>
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-2 flex-wrap">
                <x-course.badge :status="$course->status" :label="$course->badge_label" />
                <x-course.category :category="$course->category" />
            </div>
            
            @if($course->learner_count)
                <span class="inline-flex items-center gap-1.5 font-mono text-[11px] font-semibold text-slate-500">
                    <span class="material-symbols-outlined text-[15px] text-[#FB6221]">groups</span>
                    <span>{{ number_format($course->learner_count) }}+ Trained</span>
                </span>
            @elseif($isPlanned)
                <span class="font-mono text-[10px] text-slate-400 uppercase tracking-widest font-semibold">
                    ROADMAP
                </span>
            @endif
        </div>

        <!-- Module Visual Motif & Title -->
        <div class="flex items-start gap-3.5 mb-3">
            <x-course.module-art :module="$course->slug" :status="$course->status" class="w-12 h-12 shrink-0 mt-0.5" />
            <div class="flex-1 min-w-0">
                <h3 class="font-heading text-xl sm:text-2xl font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug">
                    {{ $course->name }}
                </h3>
            </div>
        </div>

        <p class="font-sans text-xs sm:text-sm text-slate-600 leading-relaxed mb-6">
            {{ $course->short_description }}
        </p>

        <!-- Highlights / Curriculum Modules -->
        @if(!empty($course->highlights))
            <div class="space-y-2 mb-6 pt-4 border-t border-slate-100">
                <span class="font-mono text-[10px] uppercase font-bold tracking-wider text-slate-400 block mb-2">
                    Key Topics &amp; Competencies
                </span>
                <ul class="space-y-1.5">
                    @foreach(array_slice($course->highlights, 0, 3) as $highlight)
                        <li class="flex items-start gap-2 font-sans text-xs text-slate-700">
                            <span class="material-symbols-outlined text-[15px] text-[#FB6221] shrink-0 mt-0.5">check_circle</span>
                            <span>{{ $highlight }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Bottom Pricing & Action Section -->
    <div class="pt-6 mt-6 border-t border-slate-100 flex flex-col gap-4">
        
        <!-- Metrics / Pricing Row -->
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                @if($isAvailable && $course->fee)
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block">TUITION FEE</span>
                    <span class="font-heading text-2xl font-extrabold text-[#0A0A0A] leading-none">{{ $course->formatted_fee }}</span>
                @elseif($isUpcoming)
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block">BATCH STATUS</span>
                    <span class="font-mono text-xs font-bold text-orange-600 uppercase">Upcoming Batch · Enrol Open</span>
                @else
                    <span class="font-mono text-[10px] text-slate-400 uppercase tracking-wider block">ACADEMY STATUS</span>
                    <span class="font-mono text-xs font-bold text-slate-500 uppercase">Roadmap Candidate</span>
                @endif
            </div>

            <div class="font-mono text-[11px] text-slate-500 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[15px] text-slate-400">schedule</span>
                <span>{{ $course->duration ?? 'Modular' }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
            @if($isAvailable)
                <a href="{{ $course->url }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-[#0A0A0A] hover:bg-[#FB6221] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs zeb-btn-cta">
                    <span>View Curriculum</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
                <a href="{{ route('book-demo', ['course' => 'fico']) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-orange-50 hover:bg-orange-100 text-[#FB6221] border border-orange-200 font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-[15px]">event</span>
                    <span>Book Free Demo</span>
                </a>
            @elseif($isUpcoming)
                <a href="{{ route('book-demo', ['course' => $course->slug]) }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-[#FB6221] hover:bg-[#e05214] text-white font-sans text-xs font-semibold uppercase tracking-wider transition-colors shadow-xs zeb-btn-cta">
                    <span>Reserve Demo Seat</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
                <a href="https://wa.me/917277877778?text={{ urlencode('Hello ZebLearn, I am interested in joining the upcoming ' . $course->name . ' batch.') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-emerald-600">chat</span>
                    <span>WhatsApp Inquiry</span>
                </a>
            @else
                <a href="{{ route('contact', ['module' => $course->slug]) }}" class="col-span-full inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-sans text-xs font-semibold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-[15px] text-slate-500">notifications_active</span>
                    <span>Notify When Available</span>
                </a>
            @endif
        </div>

    </div>

</article>

