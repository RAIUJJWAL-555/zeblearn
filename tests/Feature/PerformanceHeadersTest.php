<?php

test('dynamic html responses have correct cache-control and vary headers', function () {
    $response = $this->get('/');

    $response->assertOk();

    // Must not cache HTML indefinitely; must revalidate to pick up Vite build hashes
    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)->toContain('no-cache');
    expect($cacheControl)->toContain('must-revalidate');

    // Vary header must be set for proxy cache correctness
    expect($response->headers->get('Vary'))->toContain('Accept-Encoding');
});

test('course pages have content-auto on below-the-fold sections', function () {
    $response = $this->get('/courses/sap-fico-training');

    if ($response->status() === 200) {
        $content = $response->getContent();
        expect($content)->toContain('content-auto');
    }
});

test('header logo does not have fetchpriority high to avoid stealing LCP priority', function () {
    $response = $this->get('/');

    $response->assertOk();
    $content = $response->getContent();

    // Verify logo image does not have fetchpriority="high"
    expect($content)->not->toMatch('/<img[^>]*logo1[^>]*fetchpriority="high"/i');
});

test('public .htaccess contains correct brotli, gzip, and immutable cache rules', function () {
    $htaccess = file_get_contents(public_path('.htaccess'));

    // 1. Deflate / Gzip compression rules
    expect($htaccess)->toContain('mod_deflate.c');
    expect($htaccess)->toContain('AddOutputFilterByType DEFLATE text/html');
    expect($htaccess)->toContain('AddOutputFilterByType DEFLATE text/css');
    expect($htaccess)->toContain('AddOutputFilterByType DEFLATE application/javascript');

    // 2. Brotli compression rules
    expect($htaccess)->toContain('mod_brotli.c');
    expect($htaccess)->toContain('AddOutputFilterByType BROTLI_COMPRESS');

    // 3. Immutable caching for /build/assets/
    expect($htaccess)->toContain('^/build/assets/');
    expect($htaccess)->toContain('public, max-age=31536000, immutable');

    // 4. Non-versioned static images must revalidate (not immutable)
    expect($htaccess)->toContain('max-age=604800, must-revalidate');

    // 5. HTML files must revalidate
    expect($htaccess)->toContain('no-cache, private, must-revalidate');
});

test('dockerfile enables deflate, expires, and headers apache modules', function () {
    $dockerfile = file_get_contents(base_path('Dockerfile'));

    expect($dockerfile)->toContain('a2enmod rewrite headers deflate expires');
});

test('root server.php exists for php artisan serve compression and cache headers', function () {
    expect(file_exists(base_path('server.php')))->toBeTrue();

    $serverCode = file_get_contents(base_path('server.php'));
    expect($serverCode)->toContain('Cache-Control: public, max-age=31536000, immutable');
    expect($serverCode)->toContain('gzencode');
});

test('marquee script eliminates forced synchronous reflow', function () {
    $marqueeCode = file_get_contents(resource_path('js/marquee.js'));

    // Must use ResizeObserver or cache halfWidth outside step()
    expect($marqueeCode)->toContain('ResizeObserver');

    // Must not query scrollWidth directly inside step()
    expect($marqueeCode)->not->toMatch('/function step[^{]*\{[^}]*\.scrollWidth/s');
});
