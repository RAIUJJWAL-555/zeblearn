@extends('layouts.app')

@section('title', ($activeCategory ? $activeCategory->name . ' - ' : '') . 'SAP Interview Questions, Notes and Career Guides | ZebLearn Blog')
@section('description', $activeCategory ? ($activeCategory->meta_description ?: 'Explore ' . $activeCategory->name . ' guides, tutorials and interview notes from ZebLearn.') : 'Explore SAP interview questions, answers, study notes and career guides across SAP FICO, MM, SD, PP, EWM and Basis from experienced mentors at ZebLearn.')
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-sans antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'website')

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "ZebLearn SAP Blog & Knowledge Base",
  "description": "Comprehensive SAP interview notes, architecture guides, and career insights.",
  "url": "https://sap.zeblearn.com/blogs",
  "publisher": {
    "@type": "EducationalOrganization",
    "name": "ZebLearn",
    "url": "https://sap.zeblearn.com"
  }
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://sap.zeblearn.com/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Blog",
      "item": "https://sap.zeblearn.com/blogs"
    }
  ]
}
</script>
@endverbatim
@endpush

@section('content')
<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    {{-- 1. HEADER SECTION --}}
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 pt-36 sm:pt-40 lg:pt-44 pb-8 sm:pb-12">
        <div class="flex flex-wrap items-center justify-between gap-3 pb-6 mb-8 border-b border-slate-200">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <span class="text-[#0A0A0A] font-bold">Blog</span>
                @if($activeCategory)
                    <span class="text-slate-300">/</span>
                    <span class="text-[#FB6221] font-bold">{{ $activeCategory->name }}</span>
                @endif
                @if(!empty($activeTag))
                    <span class="text-slate-300">/</span>
                    <span class="text-[#FB6221] font-bold">#{{ $activeTag->name }}</span>
                @endif
            </nav>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-[#FB6221]"></span>
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-[#0A0A0A]">KNOWLEDGE BASE &amp; INTERVIEW NOTES</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-end">
            <div class="lg:col-span-8">
                <h1 class="font-heading text-5xl sm:text-7xl md:text-8xl lg:text-[92px] font-black uppercase tracking-tight leading-[0.95] text-[#0A0A0A] select-none">
                    SAP NOTES.<br/>
                    <span class="text-[#FB6221]">INTERVIEW PREP.</span>
                </h1>
            </div>
            <div class="lg:col-span-4 flex flex-col justify-end">
                <div class="border-l-2 border-[#FB6221] pl-4">
                    <p class="font-sans text-sm sm:text-base text-slate-600 leading-relaxed font-medium">
                        Real-world SAP interview questions, answers, architectural study notes, and career guidance written by practicing consultants at ZebLearn.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. STICKY FILTER & SEARCH BAR --}}
    <section class="sticky top-24 z-30 max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 my-2 w-full">
        <form method="GET" action="{{ route('blogs') }}" class="bg-white/95 backdrop-blur-md p-2 rounded-2xl border border-slate-200/80 shadow-md flex flex-col md:flex-row items-center justify-between gap-3 transition-all">
            {{-- Category Filter Tabs --}}
            <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto scrollbar-none py-1 px-1">
                <a
                    href="{{ route('blogs', array_filter(['search' => request('search')])) }}"
                    class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider transition-all {{ empty(request('category')) ? 'bg-[#0A0A0A] text-white' : 'text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100' }}"
                >
                    All
                </a>
                @foreach($categories as $cat)
                    <a
                        href="{{ route('blogs', array_filter(['category' => $cat->slug, 'search' => request('search')])) }}"
                        class="shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold font-mono uppercase tracking-wider transition-all {{ request('category') === $cat->slug ? 'bg-[#0A0A0A] text-white' : 'text-slate-600 hover:text-[#0A0A0A] hover:bg-slate-100' }}"
                    >
                        {{ $cat->name }} ({{ $cat->blogs_count }})
                    </a>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full md:w-72 shrink-0">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full bg-slate-50 text-[#0A0A0A] text-xs font-sans rounded-xl pl-9 pr-4 py-2 border border-slate-200 focus:border-[#FB6221] focus:bg-white focus:outline-none transition-all placeholder-slate-400"
                    placeholder="Search SAP notes, t-codes, questions..."
                />
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}" />
                @endif
                @if(request('tag'))
                    <input type="hidden" name="tag" value="{{ request('tag') }}" />
                @endif
            </div>
        </form>
    </section>

    {{-- 3. FEATURED POST (Dynamically displays newest published article on page 1) --}}
    @php
        $isFirstPage = ((int) request('page', 1)) <= 1;
    @endphp
    @if($featuredBlog && empty(request('search')) && empty(request('category')) && empty(request('tag')) && $isFirstPage)
        <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 mt-6 w-full">
            <article class="relative group rounded-3xl overflow-hidden border border-slate-200/80 bg-white grid grid-cols-1 lg:grid-cols-12 shadow-sm hover:shadow-md transition-all duration-300">
                <a aria-label="Read article" class="absolute inset-0 z-10" href="{{ url('/' . ltrim($featuredBlog->slug, '/')) }}"></a>

                {{-- Visual Image / Block --}}
                <div class="lg:col-span-7 relative min-h-[300px] lg:min-h-[420px] bg-[#0A0A0A] p-8 sm:p-12 flex flex-col justify-between overflow-hidden">
                    @if($featuredBlog->featured_image)
                        <img
                            src="{{ Str::startsWith($featuredBlog->featured_image, ['http://', 'https://']) ? $featuredBlog->featured_image : asset($featuredBlog->featured_image) }}"
                            alt="{{ $featuredBlog->featured_image_alt ?: $featuredBlog->title }}"
                            class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700"
                        />
                    @else
                        <div class="absolute inset-0 flex items-center justify-center p-8 pointer-events-none select-none">
                            <span class="font-heading font-black text-3xl sm:text-5xl text-white/10 uppercase tracking-widest text-center">
                                {{ $featuredBlog->categories->first()?->name ?: 'ZEBLEARN SAP' }}
                            </span>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-tr from-black via-black/80 to-[#FB6221]/20"></div>

                    <div class="relative z-10 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#FB6221] text-white text-[11px] font-mono font-bold tracking-wider uppercase shadow-xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            Featured Post
                        </span>
                        @if($featuredBlog->categories->isNotEmpty())
                            <span class="px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-white font-mono text-[11px] uppercase border border-white/10">
                                {{ $featuredBlog->categories->first()->name }}
                            </span>
                        @endif
                    </div>

                    <div class="relative z-10">
                        <p class="font-mono text-xs text-slate-300 uppercase tracking-widest mt-2">
                            {{ $featuredBlog->reading_time }} MIN READ &bull; PUBLISHED {{ $featuredBlog->published_at ? $featuredBlog->published_at->format('M d, Y') : '' }}
                        </p>
                    </div>
                </div>

                {{-- Details Block --}}
                <div class="lg:col-span-5 bg-white p-6 sm:p-10 flex flex-col justify-between border-t lg:border-t-0 lg:border-l border-slate-200/80">
                    <div class="space-y-4">
                        <span class="inline-block px-3 py-1 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] font-mono text-[10px] font-bold tracking-widest uppercase">
                            EDITORIAL HIGHLIGHT
                        </span>
                        <h2 class="font-heading text-2xl sm:text-3xl font-bold uppercase leading-tight tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors">
                            {{ $featuredBlog->title }}
                        </h2>
                        <p class="font-sans text-sm text-slate-600 leading-relaxed line-clamp-3">
                            {{ $featuredBlog->effective_meta_description }}
                        </p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-50 border border-orange-200 text-[#FB6221] font-heading font-bold text-sm flex items-center justify-center">
                                ZB
                            </div>
                            <div>
                                <span class="font-sans text-xs font-bold text-[#0A0A0A] block">{{ $featuredBlog->effective_author_name }}</span>
                                <span class="font-mono text-[10px] text-slate-400 uppercase">Consultant Guide</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-[#FB6221] text-white flex items-center justify-center group-hover:scale-105 transition-transform shadow-xs">
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </div>
                    </div>
                </div>
            </article>
        </section>
    @endif

    {{-- 4. ARTICLE GRID --}}
    <section class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-12 mt-8 mb-16 w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($blogs as $blog)
                <article class="relative rounded-3xl bg-white border border-slate-200 shadow-xs hover:border-[#FB6221]/50 hover:shadow-xl group transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <a aria-label="Read article" class="absolute inset-0 z-10" href="{{ url('/' . ltrim($blog->slug, '/')) }}"></a>

                    <div>
                        {{-- Image / Header Thumbnail --}}
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
                                        {{ $blog->categories->first()?->name ?: 'SAP ERP' }}
                                    </span>
                                </div>
                            @endif

                            @if($blog->categories->isNotEmpty())
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-black/75 backdrop-blur-md text-[#FB6221] font-mono text-[10px] font-bold uppercase tracking-wider border border-[#FB6221]/30">
                                    {{ $blog->categories->first()->name }}
                                </span>
                            @endif

                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded bg-black/80 text-white font-mono text-[10px]">
                                {{ $blog->reading_time }} min read
                            </span>
                        </div>

                        {{-- Body Info --}}
                        <div class="p-6">
                            <h3 class="font-heading text-lg font-bold uppercase tracking-tight text-[#0A0A0A] group-hover:text-[#FB6221] transition-colors leading-snug line-clamp-2">
                                {{ $blog->title }}
                            </h3>
                            <p class="font-sans text-xs text-slate-600 mt-2.5 leading-relaxed line-clamp-3">
                                {{ $blog->effective_meta_description }}
                            </p>
                        </div>
                    </div>

                    {{-- Card Footer --}}
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
                    <p class="text-base font-bold text-slate-800">No blog articles found matching your criteria.</p>
                    <p class="text-xs text-slate-500 mt-1">Try another search keyword or select "All" to browse the full archive.</p>
                </div>
            @endforelse
        </div>

        {{-- Crawlable Pagination --}}
        @if($blogs->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $blogs->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
