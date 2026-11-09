
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#f7f7f5">

    @php
        $pageTitle = trim($__env->yieldContent('title', 'SAP Course Online: FICO, MM, SD, PP Training | ZebLearn'));
        $pageDesc = trim($__env->yieldContent('description', 'Learn SAP online with live classes, real system practice and placement support. Explore SAP FICO, MM, SD and PP courses at ZebLearn.'));
        $canonicalPath = request()->getPathInfo();
        $defaultCanonical = 'https://sap.zeblearn.com' . ($canonicalPath === '/' ? '/' : rtrim($canonicalPath, '/'));
    @endphp

    {{-- SEO: Title and Description --}}
    <title>{{ $pageTitle }}</title>

    <meta
        name="description"
        content="{{ $pageDesc }}"
    >

    <meta
        name="robots"
        content="@yield('robots', 'index, follow, max-image-preview:large')"
    >

    <link
        rel="canonical"
        href="@yield('canonical', $defaultCanonical)"
    >

    {{-- Open Graph SEO --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="ZebLearn">
    <meta property="og:locale" content="en_IN">

    <meta
        property="og:title"
        content="@yield('og_title', $pageTitle)"
    >

    <meta
        property="og:description"
        content="@yield('og_description', $pageDesc)"
    >

    <meta property="og:url" content="@yield('canonical', $defaultCanonical)">

    <meta
        property="og:image"
        content="@yield('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')"
    >

    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">

    <meta
        name="twitter:title"
        content="@yield('twitter_title', $pageTitle)"
    >

    <meta
        name="twitter:description"
        content="@yield('twitter_description', $pageDesc)"
    >

    <meta
        name="twitter:image"
        content="@yield('og_image', 'https://sap.zeblearn.com/images/zeblearn-home-og.webp')"
    >

    {{-- Google Fonts: Mohave & Manrope (Headings), Inter (UI & Body), IBM Plex Mono (Labels) & Material Symbols --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&family=Mohave:wght@600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&family=Mohave:wght@600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@500;600&family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&family=Mohave:wght@600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    </noscript>

    {{-- Vite: Tailwind CSS 4 + JavaScript --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Page-specific head content --}}
    @stack('head')
    @stack('schema')

</head>

<body class="@yield('body-class', 'bg-surface text-on-surface font-body-md antialiased min-h-screen')">

    {{-- Skip Navigation: Accessibility --}}
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-5 focus:py-3 focus:text-black focus:shadow-lg"
    >
        Skip to main content
    </a>

    {{-- Shared Header --}}
    <x-layout.header />

    {{-- Main Content --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Shared Footer --}}
    <x-layout.footer />

    {{-- Page-specific JavaScript --}}
    @stack('scripts')

</body>
</html>
