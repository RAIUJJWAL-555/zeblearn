@props([
    'blogs' => collect(),
    'title' => 'Related Articles & Study Notes',
    'viewAllUrl' => route('blogs')
])

@if(!empty($blogs) && $blogs->isNotEmpty())
<section aria-labelledby="related-blogs-heading" class="relative z-10 w-full bg-white py-14 px-4 sm:px-6 lg:px-12 border-t border-slate-200">
    <div class="max-w-[1400px] mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-mono font-bold uppercase text-[#FB6221] tracking-wider block mb-1">KNOWLEDGE BASE</span>
                <h2 id="related-blogs-heading" class="font-heading text-2xl sm:text-3xl font-black uppercase text-slate-900 tracking-tight">
                    {{ $title }}
                </h2>
            </div>
            @if($viewAllUrl)
                <a href="{{ $viewAllUrl }}" class="text-xs font-mono font-bold uppercase text-[#FB6221] hover:underline flex items-center gap-1">
                    <span>View All Articles</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($blogs as $rel)
                @php
                    $relCat = $rel->categories->first();
                @endphp
                <article class="relative rounded-3xl bg-[#FAFAF8] border border-slate-200/90 shadow-2xs hover:border-[#FB6221]/50 hover:shadow-lg group transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <a aria-label="Read article: {{ $rel->title }}" class="absolute inset-0 z-10" href="{{ $rel->effective_canonical_url }}"></a>

                    <div>
                        {{-- Thumbnail or Branded Gradient Fallback --}}
                        <div class="w-full aspect-[16/10] bg-slate-900 relative overflow-hidden flex items-center justify-center">
                            @if($rel->featured_image)
                                <img
                                    src="{{ Str::startsWith($rel->featured_image, ['http://', 'https://']) ? $rel->featured_image : asset($rel->featured_image) }}"
                                    alt="{{ $rel->featured_image_alt ?: $rel->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-800 to-black flex flex-col items-center justify-center p-6 text-center relative">
                                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white/60 mb-2 border border-white/10">
                                        <span class="material-symbols-outlined text-[20px]">menu_book</span>
                                    </div>
                                    <span class="font-heading font-black text-xs text-white/50 uppercase tracking-widest">
                                        {{ $relCat?->name ?: 'SAP GUIDE' }}
                                    </span>
                                </div>
                            @endif

                            {{-- Reading Time Badge --}}
                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded-md bg-black/80 backdrop-blur-xs text-white font-mono text-[10px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px] text-[#FB6221]">schedule</span>
                                <span>{{ $rel->reading_time ?: 4 }} min</span>
                            </span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5">
                            <span class="font-mono text-[10px] uppercase font-bold tracking-wider text-[#FB6221] block mb-2">
                                {{ $relCat?->name ?: 'SAP GUIDE' }}
                            </span>
                            <h3 class="font-heading font-bold text-sm sm:text-base text-slate-900 line-clamp-2 group-hover:text-[#FB6221] transition-colors leading-snug">
                                {{ $rel->title }}
                            </h3>
                            @if(!empty($rel->excerpt))
                                <p class="text-xs text-slate-500 line-clamp-2 mt-2 leading-relaxed">
                                    {{ $rel->excerpt }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="px-5 pb-5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs font-mono text-slate-400">
                        <span>{{ $rel->published_at?->format('M d, Y') ?? 'Recent' }}</span>
                        <span class="text-[#FB6221] group-hover:translate-x-1 transition-transform flex items-center">
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
