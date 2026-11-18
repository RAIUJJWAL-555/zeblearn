@props([
    'questions' => collect(),
    'title' => 'Popular Interview Questions & System Answers'
])

@if(!empty($questions) && $questions->isNotEmpty())
<div class="my-8 p-6 rounded-2xl bg-orange-50/60 border border-orange-200/70">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-[#FB6221]">quiz</span>
            <h4 class="font-heading text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-900">
                {{ $title }}
            </h4>
        </div>
        <span class="text-[10px] font-mono font-bold uppercase px-2 py-0.5 rounded bg-orange-100 text-[#FB6221]">
            Interview Preparation
        </span>
    </div>

    <div class="space-y-2.5">
        @foreach($questions as $q)
            <a
                href="{{ $q->effective_canonical_url }}"
                class="group flex items-start gap-3 p-3 rounded-xl bg-white border border-slate-200 hover:border-[#FB6221]/60 hover:shadow-2xs transition-all"
            >
                <span class="w-6 h-6 rounded-lg bg-orange-50 text-[#FB6221] font-mono font-bold text-xs flex items-center justify-center shrink-0 border border-orange-100 group-hover:bg-[#FB6221] group-hover:text-white transition-colors">
                    Q
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs sm:text-sm font-semibold text-slate-800 group-hover:text-[#FB6221] transition-colors truncate">
                        {{ $q->title }}
                    </p>
                    <span class="text-[10px] font-mono text-slate-400">
                        {{ $q->reading_time ?: 5 }} min study guide &bull; Full technical explanation
                    </span>
                </div>
                <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:text-[#FB6221] group-hover:translate-x-0.5 transition-all shrink-0">
                    arrow_forward
                </span>
            </a>
        @endforeach
    </div>
</div>
@endif
