<?php

test('all 8 routes return HTTP 200 and have valid SEO structure', function (string $route, string $expectedCanonical, bool $isNoindex) {
    $response = $this->get($route);

    $response->assertStatus(200);

    $html = $response->getContent();

    // 1. Exactly one semantic H1 tag
    preg_match_all('/<h1\b[^>]*>(.*?)<\/h1>/si', $html, $h1Matches);
    expect(count($h1Matches[0]))->toBe(1);

    // 2. Page title exists and is not empty
    preg_match('/<title>(.*?)<\/title>/si', $html, $titleMatches);
    expect($titleMatches)->not->toBeEmpty();
    expect(trim($titleMatches[1]))->not->toBeEmpty();

    // 3. Meta description exists and is not empty
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/i', $html, $descMatches);
    expect($descMatches)->not->toBeEmpty();
    expect(trim($descMatches[1]))->not->toBeEmpty();

    // 4. Self-referencing production canonical URL
    preg_match('/<link[^>]*rel=["\']canonical["\'][^>]*href=["\'](.*?)["\']/i', $html, $canonMatches);
    expect($canonMatches)->not->toBeEmpty();
    expect($canonMatches[1])->toBe($expectedCanonical);

    // 5. Robots indexing directive
    preg_match('/<meta[^>]*name=["\']robots["\'][^>]*content=["\'](.*?)["\']/i', $html, $robotMatches);
    expect($robotMatches)->not->toBeEmpty();
    if ($isNoindex) {
        expect($robotMatches[1])->toContain('noindex');
    } else {
        expect($robotMatches[1])->toContain('index');
        expect($robotMatches[1])->not->toContain('noindex');
    }

    // 6. JSON-LD scripts are valid JSON
    preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $jsonLdMatches);
    if (! $isNoindex) {
        expect(count($jsonLdMatches[1]))->toBeGreaterThanOrEqual(1);
        foreach ($jsonLdMatches[1] as $jsonLdContent) {
            $decoded = json_decode(trim($jsonLdContent), true);
            expect(json_last_error())->toBe(JSON_ERROR_NONE);
            expect($decoded)->toBeArray();
            expect($decoded)->toHaveKey('@context');
            expect($decoded['@context'])->toBe('https://schema.org');
        }
    }
})->with([
    'home' => ['/', 'https://sap.zeblearn.com/', false],
    'courses.index' => ['/courses', 'https://sap.zeblearn.com/courses', false],
    'courses.sap-fico' => ['/courses/sap-fico-training', 'https://sap.zeblearn.com/courses/sap-fico-training', false],
    'blog.index' => ['/blogs', 'https://sap.zeblearn.com/blogs', false],
    'blog.ewm' => ['/blogs/top-50-sap-ewm-interview-question-answer', 'https://sap.zeblearn.com/blogs/top-50-sap-ewm-interview-question-answer', false],
    'about' => ['/about', 'https://sap.zeblearn.com/about', false],
    'book-demo' => ['/book-demo', 'https://sap.zeblearn.com/book-demo', false],
    'book-demo.thank-you' => ['/book-demo/thank-you', 'https://sap.zeblearn.com/book-demo/thank-you', true],
    'contact' => ['/contact', 'https://sap.zeblearn.com/contact', false],
]);

test('homepage renders video testimonials and hiring partner sections with valid structure', function () {
    $response = $this->get('/');

    $response->assertStatus(200);

    // Section 1: Video Testimonials
    $response->assertSee('HEAR FROM OUR STUDENTS');
    $response->assertSee('Real learning experiences and career journeys from ZebLearn students.');
    $response->assertSee('id="video-carousel"', false);
    $response->assertSee('id="yt-modal"', false);

    // Section 2: Real Learners & Placements
    $response->assertSee('REAL LEARNERS. REAL CAREERS.');
    $response->assertSee('Explore verified learner journeys and the organizations where our students have progressed.');
    $response->assertSee('Sumit Singh');
    $response->assertSee('Placed at Accenture');

    // Section 3: Industry & Hiring Partners
    $response->assertSee('EMPLOYERS &amp; INDUSTRY PARTNERS', false);
    $response->assertSee('https://www.placementbaba.com/', false);
    $response->assertSee('https://www.accenture.com/', false);
    $response->assertSee('https://www.deloitte.com/', false);
});

test('footer includes verified social media channels across pages', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('https://in.linkedin.com/company/zeblearn', false);
    $response->assertSee('https://www.youtube.com/@ZEBLEARN', false);
    $response->assertSee('https://www.instagram.com/zeblearnindia/', false);
    $response->assertSee('https://www.facebook.com/zeblearn', false);
});
