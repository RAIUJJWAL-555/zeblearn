<?php

test('compiled css includes native view transitions and prefers-reduced-motion safety', function () {
    $manifestPath = public_path('build/manifest.json');
    expect(file_exists($manifestPath))->toBeTrue();

    $manifest = json_decode(file_get_contents($manifestPath), true);
    expect($manifest)->toHaveKey('resources/css/app.css');

    $cssFile = public_path('build/'.$manifest['resources/css/app.css']['file']);
    expect(file_exists($cssFile))->toBeTrue();

    $cssContent = file_get_contents($cssFile);

    // View Transitions
    expect($cssContent)->toContain('@view-transition');
    expect($cssContent)->toContain('navigation:auto');
    expect($cssContent)->toContain('translateY(-6px)');
    expect($cssContent)->toContain('translateY(6px)');
    expect($cssContent)->toContain('isolation:isolate');
    expect($cssContent)->toContain('mix-blend-mode:normal');

    // Reduced motion accessibility
    expect($cssContent)->toContain('prefers-reduced-motion:reduce');

    // Progressive reveal classes
    expect($cssContent)->toContain('[data-reveal]');
    expect($cssContent)->toContain('js-reveal-ready');
});

test('compiled javascript configures intersection observer and progressive reveal flag', function () {
    $manifestPath = public_path('build/manifest.json');
    $manifest = json_decode(file_get_contents($manifestPath), true);
    expect($manifest)->toHaveKey('resources/js/app.js');

    $jsFile = public_path('build/'.$manifest['resources/js/app.js']['file']);
    expect(file_exists($jsFile))->toBeTrue();

    $jsContent = file_get_contents($jsFile);

    // IntersectionObserver and progressive reveal class
    expect($jsContent)->toContain('IntersectionObserver');
    expect($jsContent)->toContain('js-reveal-ready');
    expect($jsContent)->toContain('revealed');
});

test('all 8 routes contain motion hooks and zero inline opacity zero', function (string $route) {
    $response = $this->get($route);

    $response->assertStatus(200);
    $html = $response->getContent();

    // 1. Must contain scroll reveal hooks
    expect($html)->toContain('data-reveal');

    // 2. Must NOT contain inline style="opacity: 0" which would hide content from SEO bots or no-JS users
    expect($html)->not->toMatch('/style\s*=\s*["\'][^"\']*opacity\s*:\s*0/i');

    // 3. Must link to the compiled Vite assets
    expect($html)->toContain('build/assets/app-');
})->with([
    'home' => ['/'],
    'courses.index' => ['/courses'],
    'courses.sap-fico' => ['/courses/sap-fico-training'],
    'blog.index' => ['/blogs'],
    'blog.ewm' => ['/blogs/top-50-sap-ewm-interview-question-answer'],
    'about' => ['/about'],
    'book-demo' => ['/book-demo'],
    'book-demo.thank-you' => ['/book-demo/thank-you'],
    'contact' => ['/contact'],
]);
