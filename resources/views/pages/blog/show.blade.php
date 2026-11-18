@extends('layouts.app')

@section('title', $blog->effective_seo_title)
@section('description', $blog->effective_meta_description)
@if($blog->effective_meta_keywords)
@section('keywords', $blog->effective_meta_keywords)
@endif
@section('body-class', "bg-[#FAFAF8] text-[#0A0A0A] font-sans antialiased min-h-screen selection:bg-[#FB6221]/20 selection:text-[#FB6221]")
@section('og_type', 'article')
@section('og_title', $blog->og_title ?: $blog->effective_seo_title)
@section('og_description', $blog->og_description ?: $blog->effective_meta_description)
@section('og_image', $blog->effective_og_image)
@section('canonical', $blog->effective_canonical_url)
@section('robots', ($blog->robots_index ? 'index, follow, max-image-preview:large' : 'noindex, nofollow'))

@push('schema')
@php
    $schemaType = $blog->schema_type ?: 'BlogPosting';
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => $schemaType,
        'headline' => $blog->title,
        'description' => $blog->effective_meta_description,
        'url' => $blog->effective_canonical_url,
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $blog->effective_canonical_url,
        ],
        'datePublished' => $blog->published_at ? $blog->published_at->toIso8601String() : now()->toIso8601String(),
        'dateModified' => $blog->updated_at ? $blog->updated_at->toIso8601String() : now()->toIso8601String(),
        'author' => [
            '@type' => 'Person',
            'name' => $blog->effective_author_name,
        ],
        'publisher' => [
            '@type' => 'EducationalOrganization',
            'name' => 'ZebLearn',
            'url' => 'https://sap.zeblearn.com',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'https://sap.zeblearn.com/logo/logo2-400.webp',
            ],
        ],
    ];

    if ($blog->featured_image) {
        $articleSchema['image'] = Str::startsWith($blog->featured_image, ['http://', 'https://'])
            ? $blog->featured_image
            : asset($blog->featured_image);
    }

    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => url('/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Blog',
                'item' => route('blogs'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $blog->title,
                'item' => $blog->effective_canonical_url,
            ],
        ],
    ];
@endphp
<script type="application/ld+json">
{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
<script type="application/ld+json">
{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

@section('content')
@php
    $toc = $tableOfContents ?? [];
    $bodyHtml = $processedContent ?? $blog->content;
    $timeToRead = $readingTime ?? ($blog->reading_time ?: 4);
    $hasToc = count($toc) >= 2;
    $primaryCategory = $blog->categories->first();
@endphp

{{-- 1. SCROLL READING PROGRESS INDICATOR --}}
<div id="reading-progress-bar" class="fixed top-0 left-0 right-0 h-1 z-[70] bg-transparent pointer-events-none" aria-hidden="true">
    <div id="reading-progress-fill" class="h-full bg-gradient-to-r from-[#FB6221] via-amber-500 to-[#FB6221] origin-left transition-all duration-75" style="width: 0%;"></div>
</div>

{{-- 2. ADMIN PREVIEW NOTICE --}}
@if(!empty($isPreview) && $isPreview)
    <aside aria-label="Admin Preview Notice" class="bg-amber-500 text-slate-950 px-4 py-2.5 text-center text-xs font-mono font-bold uppercase tracking-wider sticky top-0 z-[65] flex items-center justify-center gap-2 shadow-md">
        <span class="material-symbols-outlined text-[16px]">visibility</span>
        <span>Admin Article Preview Mode &bull; Status: {{ strtoupper($blog->status) }}</span>
    </aside>
@endif

<div class="flex flex-col w-full bg-[#FAFAF8] text-[#0A0A0A]">

    {{-- 3. ARTICLE HERO SECTION --}}
    <header class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 pt-32 sm:pt-36 lg:pt-40 pb-8 w-full">
        <div class="max-w-[960px]">
            {{-- Breadcrumb Navigation --}}
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 font-mono text-[11px] font-semibold uppercase tracking-wider text-slate-500 mb-5">
                <a href="{{ route('home') }}" class="hover:text-[#FB6221] transition-colors">Home</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('blogs') }}" class="hover:text-[#FB6221] transition-colors">Blog</a>
                @if($primaryCategory)
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('blogs.category', $primaryCategory->slug) }}" class="hover:text-[#FB6221] transition-colors">
                        {{ $primaryCategory->name }}
                    </a>
                @endif
                <span class="text-slate-300">/</span>
                <span class="text-slate-700 truncate max-w-[220px] sm:max-w-[360px]" title="{{ $blog->title }}">{{ $blog->title }}</span>
            </nav>

            {{-- Categories Pills --}}
            @if($blog->categories->isNotEmpty())
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @foreach($blog->categories as $category)
                        <a
                            href="{{ route('blogs.category', $category->slug) }}"
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-orange-50 border border-orange-200/80 text-[#FB6221] hover:bg-[#FB6221] hover:text-white transition-all shadow-2xs"
                        >
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Primary Article H1 Heading --}}
            <h1 class="font-heading text-3xl sm:text-4xl md:text-5xl lg:text-[44px] font-black uppercase tracking-tight text-slate-900 leading-[1.12] mb-6">
                {{ $blog->title }}
            </h1>

            {{-- Author, Dates & Metadata Row --}}
            <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-slate-200/80 text-xs font-mono text-slate-600">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FB6221] to-[#EA580C] text-white flex items-center justify-center font-heading font-black text-sm shadow-xs select-none">
                        {{ strtoupper(substr($blog->effective_author_name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="font-sans font-bold text-slate-900 block text-sm">{{ $blog->effective_author_name }}</span>
                        <span class="text-slate-400 text-[10px] uppercase tracking-wider">ZebLearn SAP Practice Lead</span>
                    </div>
                </div>

                {{-- Dates, Reading Time & View Count --}}
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-slate-500 text-[11px]">
                    @if($blog->published_at)
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">calendar_today</span>
                            <time datetime="{{ $blog->published_at->toIso8601String() }}">
                                {{ $blog->published_at->format('M d, Y') }}
                            </time>
                        </span>
                    @endif

                    @if($blog->updated_at && $blog->published_at && $blog->updated_at->diffInDays($blog->published_at) > 1)
                        <span class="text-slate-400 hidden sm:inline">&bull;</span>
                        <span class="text-slate-400 hidden sm:inline" title="Last updated">
                            Updated: {{ $blog->updated_at->format('M d, Y') }}
                        </span>
                    @endif

                    <span class="text-slate-400">&bull;</span>
                    <span class="inline-flex items-center gap-1 font-semibold text-slate-700">
                        <span class="material-symbols-outlined text-[15px] text-[#FB6221]">schedule</span>
                        {{ $timeToRead }} min read
                    </span>

                    @if($blog->view_count > 0)
                        <span class="text-slate-400">&bull;</span>
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">visibility</span>
                            {{ number_format($blog->view_count) }} views
                        </span>
                    @endif
                </div>

                {{-- Quick Social Share Bar --}}
                <div class="flex items-center gap-1.5 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1 hidden md:inline">Share:</span>

                    {{-- Copy Link Button --}}
                    <button
                        type="button"
                        id="copy-link-btn"
                        class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-[#FB6221] transition-all cursor-pointer relative group"
                        title="Copy Article URL"
                        aria-label="Copy Article URL"
                    >
                        <span class="material-symbols-outlined text-[17px] block">link</span>
                        <span id="copy-feedback" class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded bg-slate-900 text-white text-[10px] font-mono font-bold whitespace-nowrap opacity-0 pointer-events-none transition-opacity duration-150">Copied!</span>
                    </button>

                    {{-- LinkedIn --}}
                    <a
                        href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($blog->effective_canonical_url) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition-all cursor-pointer"
                        title="Share on LinkedIn"
                        aria-label="Share on LinkedIn"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                    </a>

                    {{-- X / Twitter --}}
                    <a
                        href="https://twitter.com/intent/tweet?text={{ urlencode($blog->title) }}&url={{ urlencode($blog->effective_canonical_url) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 hover:text-black transition-all cursor-pointer"
                        title="Share on X"
                        aria-label="Share on X"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>

                    {{-- WhatsApp --}}
                    <a
                        href="https://api.whatsapp.com/send?text={{ urlencode($blog->title . ' ' . $blog->effective_canonical_url) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="p-2 rounded-xl border border-slate-200 bg-white hover:bg-emerald-50 text-slate-600 hover:text-emerald-600 transition-all cursor-pointer"
                        title="Share on WhatsApp"
                        aria-label="Share on WhatsApp"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.41a8.173 8.173 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.51 11.66c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.54.07-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.84-.86 2.06 0 1.21.88 2.38 1.01 2.55.12.17 1.74 2.65 4.21 3.72.59.25 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.23-.18-.48-.3z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- 4. FEATURED IMAGE (If Available) --}}
    @if($blog->featured_image)
        <section aria-label="Featured Image" class="max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8 mb-10 w-full">
            <figure class="rounded-3xl overflow-hidden border border-slate-200/90 shadow-md aspect-video max-h-[520px] bg-slate-900">
                <img
                    src="{{ Str::startsWith($blog->featured_image, ['http://', 'https://']) ? $blog->featured_image : asset($blog->featured_image) }}"
                    alt="{{ $blog->featured_image_alt ?: $blog->title }}"
                    fetchpriority="high"
                    class="w-full h-full object-cover"
                />
                @if($blog->featured_image_alt && $blog->featured_image_alt !== $blog->title)
                    <figcaption class="p-3 bg-slate-50 border-t border-slate-200 text-slate-500 font-mono text-[11px] text-center">
                        {{ $blog->featured_image_alt }}
                    </figcaption>
                @endif
            </figure>
        </section>
    @endif

    {{-- 5. ARTICLE READING LAYOUT (Desktop 2-Column + Mobile Accordion) --}}
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 pb-16 w-full">
        <div class="flex flex-col xl:flex-row gap-8 lg:gap-10 2xl:gap-12 items-start">

            {{-- 5A. DESKTOP STICKY TABLE OF CONTENTS SIDEBAR --}}
            @if($hasToc)
                <aside
                    x-data="{ tocSearch: '' }"
                    class="w-64 xl:w-72 2xl:w-80 shrink-0 hidden xl:flex flex-col sticky top-28 overflow-hidden bg-white p-5 rounded-3xl border border-slate-200 shadow-sm self-start z-20"
                    style="background-color: #ffffff !important; max-height: calc(100vh - 130px) !important; height: auto; overflow: hidden !important;"
                >
                    <div class="pb-3 border-b border-slate-100 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-[#FB6221]">menu_book</span>
                            <h2 class="font-heading text-xs font-bold uppercase tracking-wider text-slate-900">
                                Table of Contents
                            </h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-orange-50 text-[#FB6221] border border-orange-200">
                            {{ count($toc) }}
                        </span>
                    </div>

                    @if(count($toc) > 15)
                        <div class="pt-2.5 pb-1 shrink-0">
                            <div class="relative">
                                <span class="material-symbols-outlined text-[15px] text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none">search</span>
                                <input
                                    type="text"
                                    x-model="tocSearch"
                                    placeholder="Filter {{ count($toc) }} topics..."
                                    class="w-full pl-8 pr-3 py-1.5 text-[11px] bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-[#FB6221] focus:ring-1 focus:ring-[#FB6221] outline-hidden text-slate-800 placeholder:text-slate-400 transition-all font-sans"
                                />
                            </div>
                        </div>
                    @endif

                    {{-- Scrollspy Navigation Links --}}
                    <nav
                        class="toc-nav flex-1 min-h-0 overflow-y-auto overscroll-contain pr-1 py-3 space-y-0.5 custom-scrollbar text-xs"
                        style="max-height: calc(100vh - 240px) !important; overflow-y: auto !important;"
                        aria-label="Table of contents"
                    >
                        @foreach($toc as $item)
                            <a
                                href="#{{ $item['id'] }}"
                                @if(count($toc) > 15)
                                    x-show="!tocSearch || {{ json_encode(strtolower($item['title'])) }}.includes(tocSearch.toLowerCase())"
                                @endif
                                class="toc-link block py-1.5 px-3 rounded-lg border-l-2 border-transparent text-slate-600 hover:text-[#FB6221] hover:border-[#FB6221] hover:bg-orange-50/40 font-medium transition-all leading-snug {{ $item['level'] === 3 ? 'pl-6 text-[11px]' : '' }}"
                            >
                                {{ $item['title'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-mono text-slate-400 shrink-0">
                        <span>Click to jump</span>
                        <a href="#main-article" class="text-[#FB6221] hover:underline">Top &uarr;</a>
                    </div>
                </aside>
            @endif

            {{-- 5B. MAIN ARTICLE COLUMN --}}
            <main id="main-article" class="w-full max-w-[760px] mx-auto xl:mx-0 flex-1 min-w-0">

                {{-- Mobile Collapsible Table of Contents --}}
                @if($hasToc)
                    <div class="xl:hidden mb-8">
                        <details class="group bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                            <summary class="p-4 flex items-center justify-between cursor-pointer font-heading font-bold text-xs uppercase tracking-wider text-slate-800 select-none hover:bg-slate-50 transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-[#FB6221]">list</span>
                                    <span>Table of Contents ({{ count($toc) }} sections)</span>
                                </span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400 transition-transform duration-200 group-open:rotate-180">expand_more</span>
                            </summary>
                            <nav class="px-4 pb-4 pt-1 max-h-72 overflow-y-auto overscroll-contain space-y-1 text-xs border-t border-slate-100 custom-scrollbar" aria-label="Mobile table of contents">
                                @foreach($toc as $item)
                                    <a
                                        href="#{{ $item['id'] }}"
                                        onclick="this.closest('details').removeAttribute('open')"
                                        class="block py-1.5 px-2 rounded-lg text-slate-600 hover:text-[#FB6221] hover:bg-orange-50 font-medium {{ $item['level'] === 3 ? 'pl-4 text-[11px]' : '' }}"
                                    >
                                        {{ $item['title'] }}
                                    </a>
                                @endforeach
                            </nav>
                        </details>
                    </div>
                @endif

                {{-- Short Excerpt / Lead-in Box --}}
                @if(!empty($blog->excerpt))
                    <div class="p-6 rounded-2xl bg-orange-50/50 border border-orange-200/70 mb-8 font-sans text-base sm:text-lg text-slate-800 leading-relaxed font-medium">
                        {!! nl2br(e($blog->excerpt)) !!}
                    </div>
                @endif

                {{-- Authentic Full Article Body --}}
                <article class="article-prose">
                    {!! $bodyHtml !!}
                </article>

                {{-- SEO Spider Web: Contextual Course Bridge --}}
                @if(!empty($primaryCourse))
                    <x-seo.course-bridge :course="$primaryCourse" source-type="blog_bridge" />
                @endif

                {{-- SEO Spider Web: Fast-Track Interview Preparation Strip --}}
                @if(!empty($interviewQuestions) && $interviewQuestions->isNotEmpty())
                    <x-seo.interview-guide-strip :questions="$interviewQuestions" />
                @endif

                {{-- Article Tags --}}
                @if($blog->tags->isNotEmpty())
                    <div class="mt-12 pt-6 border-t border-slate-200 flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs uppercase font-bold text-slate-400 mr-2">Tags:</span>
                        @foreach($blog->tags as $tag)
                            <a
                                href="{{ route('blogs.tag', $tag->slug) }}"
                                class="px-3 py-1 rounded-full text-xs font-mono bg-slate-100 hover:bg-[#FB6221] text-slate-700 hover:text-white border border-slate-200 transition-colors shadow-2xs"
                            >
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Author Profile Card --}}
                <div class="mt-12 p-6 rounded-3xl bg-white border border-slate-200/90 shadow-2xs flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#FB6221] to-[#EA580C] text-white flex items-center justify-center font-heading font-black text-xl shadow-xs shrink-0 select-none">
                        {{ strtoupper(substr($blog->effective_author_name, 0, 1)) }}
                    </div>
                    <div class="space-y-1.5 text-center sm:text-left">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#FB6221] block">ARTICLE AUTHOR</span>
                        <h3 class="font-heading text-base font-bold text-slate-900">{{ $blog->effective_author_name }}</h3>
                        <p class="font-sans text-xs text-slate-600 leading-relaxed max-w-xl">
                            Enterprise ERP practice lead at ZebLearn Academy. Mentoring students, SAP aspirants, and working professionals in real-world system configuration, architecture, and placement preparation.
                        </p>
                    </div>
                </div>

                {{-- 6. ZEBLEARN COURSE CONVERSION SECTION --}}
                <section aria-labelledby="conversion-heading" class="mt-12 p-8 sm:p-10 rounded-3xl bg-gradient-to-br from-[#0A0A0A] via-slate-900 to-[#141414] text-white relative overflow-hidden border border-white/10 shadow-2xl">
                    {{-- Decorative Glow --}}
                    <div class="absolute -top-24 -right-24 w-72 h-72 bg-[#FB6221]/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[10px] font-mono font-bold tracking-widest uppercase text-[#FB6221]">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FB6221] animate-pulse"></span>
                            <span>Enterprise SAP Training &amp; Placement</span>
                        </div>

                        <h2 id="conversion-heading" class="font-heading text-2xl sm:text-3xl font-black uppercase text-white tracking-tight leading-snug">
                            Ready to Turn SAP Theory into Enterprise Practice?
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-300 max-w-xl leading-relaxed">
                            Join live mentor-led batches with dedicated 24/7 sandbox servers, real business scenarios, mock technical interviews, and guaranteed placement bridges with Tier-1 IT consulting partners.
                        </p>

                        {{-- Trust Badges --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-2 pb-2 text-[11px] font-mono text-slate-300">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-emerald-400">check_circle</span>
                                <span>24/7 Sandbox Practice</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-emerald-400">check_circle</span>
                                <span>Placement Support</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-emerald-400">check_circle</span>
                                <span>ISO &amp; NSDC Aligned</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <a
                                href="{{ route('book-demo') }}"
                                class="px-6 py-3.5 rounded-full bg-[#FB6221] hover:bg-[#e05214] text-white font-mono text-xs font-bold uppercase tracking-wider transition-all shadow-md hover:scale-105 flex items-center gap-2"
                            >
                                <span>Book Free Demo Class</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </a>
                            <a
                                href="{{ route('courses.index') }}"
                                class="px-6 py-3.5 rounded-full bg-white/10 hover:bg-white/20 text-white font-mono text-xs font-bold uppercase tracking-wider transition-all border border-white/20 flex items-center gap-2"
                            >
                                <span>Explore All Courses</span>
                            </a>
                        </div>
                    </div>
                </section>
            </main>

            {{-- 5C. ULTRA-WIDE RIGHT ACTIONS WIDGET (2xl:block) --}}
            <aside class="w-64 shrink-0 hidden 2xl:block sticky top-28 space-y-4 self-start">
                <div class="p-5 rounded-3xl bg-white border border-slate-200/90 shadow-2xs space-y-3">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 block">SHARE ARTICLE</span>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            onclick="navigator.clipboard.writeText(window.location.href); alert('Article URL copied to clipboard!');"
                            class="flex-1 py-2 rounded-xl border border-slate-200 text-xs font-mono font-bold text-slate-700 hover:text-[#FB6221] hover:bg-orange-50 transition-colors flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            <span>Copy URL</span>
                        </button>
                    </div>
                </div>

                <div class="p-5 rounded-3xl bg-orange-50/60 border border-orange-200/80 shadow-2xs space-y-2">
                    <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-[#FB6221] block">NEED GUIDANCE?</span>
                    <h4 class="font-heading font-bold text-xs uppercase text-slate-900 leading-snug">
                        Talk with an SAP Career Counselor
                    </h4>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Free 1-on-1 profile evaluation and roadmap guidance.
                    </p>
                    <a
                        href="{{ route('contact') }}"
                        class="block text-center w-full py-2 rounded-xl bg-slate-900 hover:bg-black text-white text-[11px] font-mono font-bold uppercase tracking-wider transition-colors"
                    >
                        Contact Counselor &rarr;
                    </a>
                </div>

                {{-- Back to Top Button --}}
                <button
                    type="button"
                    onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="w-full py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-mono font-bold uppercase tracking-wider transition-colors flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs"
                >
                    <span class="material-symbols-outlined text-[16px]">arrow_upward</span>
                    <span>Back to Top</span>
                </button>
            </aside>

        </div>
    </div>

    {{-- 7. SEO SPIDER WEB: RELATED BLOGS & STUDY NOTES SECTION --}}
    <x-seo.related-blogs :blogs="$relatedBlogs" />

    {{-- 8. FLOATING BOTTOM BACK-TO-TOP TRIGGER (MOBILE & DESKTOP) --}}
    <button
        type="button"
        id="floating-back-to-top"
        onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        aria-label="Back to Top"
        class="fixed bottom-6 right-6 z-40 p-3 rounded-2xl bg-slate-900/90 backdrop-blur-xs text-white hover:bg-[#FB6221] transition-all shadow-xl opacity-0 pointer-events-none cursor-pointer group"
    >
        <span class="material-symbols-outlined text-[20px] block group-hover:-translate-y-0.5 transition-transform">arrow_upward</span>
    </button>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Reading Progress Bar & Back to Top visibility
    const progressBar = document.getElementById('reading-progress-fill');
    const backToTopBtn = document.getElementById('floating-back-to-top');

    function updateProgress() {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;

        if (progressBar) {
            progressBar.style.width = Math.min(100, Math.max(0, scrolled)) + '%';
        }

        if (backToTopBtn) {
            if (scrollTop > 400) {
                backToTopBtn.classList.remove('opacity-0', 'pointer-events-none');
                backToTopBtn.classList.add('opacity-100');
            } else {
                backToTopBtn.classList.add('opacity-0', 'pointer-events-none');
                backToTopBtn.classList.remove('opacity-100');
            }
        }
    }

    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();

    // 2. Copy Link Button with Tooltip
    const copyBtn = document.getElementById('copy-link-btn');
    const copyFeedback = document.getElementById('copy-feedback');
    if (copyBtn && copyFeedback) {
        copyBtn.addEventListener('click', function () {
            const url = window.location.href;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(showFeedback);
            } else {
                const tempInput = document.createElement('input');
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                showFeedback();
            }
        });

        function showFeedback() {
            copyFeedback.classList.remove('opacity-0');
            copyFeedback.classList.add('opacity-100');
            setTimeout(function () {
                copyFeedback.classList.add('opacity-0');
                copyFeedback.classList.remove('opacity-100');
            }, 2000);
        }
    }

    // 3. Scrollspy navigation tracking observer
    const tocLinks = document.querySelectorAll('.toc-link');
    if (tocLinks.length > 0) {
        const headings = [];
        tocLinks.forEach(function (link) {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const target = document.querySelector(href);
                if (target) {
                    headings.push(target);
                }
            }
        });

        if ('IntersectionObserver' in window && headings.length > 0) {
            const observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        tocLinks.forEach(function (link) {
                            const isMatch = link.getAttribute('href') === '#' + id;
                            link.classList.toggle('target-current', isMatch);
                            link.setAttribute('aria-current', isMatch ? 'true' : 'false');
                            if (isMatch) {
                                const nav = link.closest('.toc-nav');
                                if (nav) {
                                    const linkTop = link.offsetTop - nav.offsetTop;
                                    const navScroll = nav.scrollTop;
                                    const navHeight = nav.clientHeight;
                                    if (linkTop < navScroll || linkTop > navScroll + navHeight - 50) {
                                        nav.scrollTo({ top: Math.max(0, linkTop - 40), behavior: 'smooth' });
                                    }
                                }
                            }
                        });
                    }
                });
            }, {
                rootMargin: '-100px 0px -60% 0px',
                threshold: 0
            });

            headings.forEach(function (heading) {
                observer.observe(heading);
            });
        }
    }
});
</script>
@endpush
