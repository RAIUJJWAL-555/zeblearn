@extends('layouts.app')

@section('title', '#' . $tag->name . ' Articles & Interview Guides | ZebLearn')
@section('description', 'Explore all technical guides, study notes, and placement interview questions tagged #' . $tag->name . ' from ZebLearn.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-sans antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "#{{ $tag->name }} Articles",
  "description": "Explore all technical guides, study notes, and placement interview questions tagged #{{ $tag->name }} from ZebLearn.",
  "url": "{{ url('/tag/' . $tag->slug . '/') }}",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "{{ url('/') }}"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Blog",
        "item": "{{ route('blogs') }}"
      },
      {
        "@type": "ListItem",
        "position": 3,
        "name": "#{{ $tag->name }}",
        "item": "{{ url('/tag/' . $tag->slug . '/') }}"
      }
    ]
  }
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    {{-- HEADER --}}
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-36 sm:pt-40 lg:pt-44 pb-8 sm:pb-12">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('blogs') }}" class="hover:text-[#FB6221] transition-colors">Blog</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">#{{ $tag->name }}</span>
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">TAG ARCHIVE</span>
            </div>
        </div>

        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] text-xs font-mono font-bold uppercase mb-3">
                <span>Tag Topic</span>
            </div>
            <h1 class="font-heading text-4xl sm:text-6xl font-black uppercase tracking-tight text-[#0A0A0A] leading-tight">
                #{{ $tag->name }}
            </h1>
            <p class="font-sans text-base text-slate-600 mt-3 leading-relaxed">
                Showing all published tutorials, guides, and interview preparation notes filed under <strong class="text-slate-900">#{{ $tag->name }}</strong>. Sorted newest first.
            </p>
        </div>
    </section>

    {{-- ARTICLES GRID --}}
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pb-20 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($blogs as $blog)
                <article class="relative rounded-3xl bg-white border border-slate-200 shadow-xs hover:border-[#FB6221]/50 hover:shadow-xl group transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <a aria-label="Read article" class="absolute inset-0 z-10" href="{{ url('/' . $blog->slug . '/') }}"></a>

                    <div>
                        <div class="w-full aspect-[16/9] bg-slate-900 relative overflow-hidden flex items-center justify-center">
                            @if($blog->featured_image)
                                <img
                                    src="{{ Str::startsWith($blog->featured_image, ['http://', 'https://']) ? $blog->featured_image : asset($blog->featured_image) }}"
                                    alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-slate-900 via-slate-800 to-black flex items-center justify-center p-6 text-center">
                                    <span class="font-heading font-extrabold text-2xl text-white/20 uppercase tracking-widest">
                                        #{{ $tag->name }}
                                    </span>
                                </div>
                            @endif

                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded bg-black/80 text-white font-mono text-[10px]">
                                {{ $blog->reading_time }} min read
                            </span>
                        </div>

                        <div class="p-6">
                            <h2 class="font-heading text-lg font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug line-clamp-2">
                                {{ $blog->title }}
                            </h2>
                            <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed line-clamp-3">
                                {{ $blog->effective_meta_description }}
                            </p>
                        </div>
                    </div>

                    <div class="p-6 pt-0 mt-2">
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between font-mono text-[11px] text-slate-400">
                            <span>{{ $blog->published_at ? $blog->published_at->format('M d, Y') : 'Recent' }}</span>
                            <span class="inline-flex items-center gap-1 text-[#0A0A0A] font-bold group-hover:text-[#FB6221] transition-colors">
                                <span>Read</span>
                                <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 font-sans">
                    <p class="text-base font-bold text-slate-800">No articles currently published with this tag.</p>
                    <a href="{{ route('blogs') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#FB6221] text-white text-xs font-mono font-bold uppercase mt-4 shadow-xs">
                        Browse All Articles
                    </a>
                </div>
            @endforelse
        </div>

        @if($blogs->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $blogs->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
