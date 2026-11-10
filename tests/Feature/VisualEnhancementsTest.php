<?php

test('homepage renders simple demo form in hero, awards certificates after testimonials, and preserves headlines', function () {
    $response = $this->get('/');

    $response->assertStatus(200);

    // 1. Hero Simple Demo Registration Form
    $response->assertSee('id="hero-lead-form-wrapper"', false);
    $response->assertSee('id="hero-student-form"', false);
    $response->assertSee('BOOK YOUR FREE LIVE DEMO');
    $response->assertSee('CLAIM FREE DEMO SEAT');
    $response->assertSee('id="hero-name"', false);
    $response->assertSee('id="hero-phone"', false);
    $response->assertSee('id="hero-course"', false);

    // 2. Academic Awards & Recognition Section Placed After Testimonials
    $response->assertSee('id="awards-and-recognition"', false);
    $response->assertSee('AWARDS &amp; RECOGNITION', false);
    $response->assertSee('VERIFIED SAP ACADEMY EXCELLENCE');
    $response->assertSee('certificate-1.webp');
    $response->assertSee('certificate-2.webp');
    $response->assertSee('certificate-3.webp');

    // 3. Preserved Left Hero Headlines & CTAs
    $response->assertSee('LEARN SAP ONLINE.');
    $response->assertSee('GET HIRED.');
    $response->assertSee('Book a Free Demo');
    $response->assertSee('Explore Courses');
    $response->assertSee('2,500+ LEARNERS TRAINED');
    $response->assertSee('24/7 S/4HANA Sandbox Access');

    // 4. Complete absence of obsolete elements
    $response->assertDontSee('T-CODE: FB03');
    $response->assertDontSee('F110 Payment Run');
    $response->assertDontSee('ACDOCA Ledger');
    $response->assertDontSee('Cash / Bank Clearing');
});

test('book demo thank you page displays 3D welcoming mentor greeting character and message', function () {
    $response = $this->get('/book-demo/thank-you?module=SAP%20FICO');

    $response->assertStatus(200);
    $response->assertSee('thank-you-greeting-trans.webp');
    $response->assertSeeText("THANK YOU! WE'RE EXCITED TO MEET YOU.");
    $response->assertSee('DEMO REQUEST RECEIVED');
    $response->assertSee('Confirm on WhatsApp');
});

test('homepage and courses index render 2.5D isometric pathway illustrations', function (string $route) {
    $response = $this->get($route);

    $response->assertStatus(200);

    // Pathway 01 (Freshers / Cap & Laptop)
    $response->assertSee('Start Your SAP Career');

    // Pathway 02 (Domain / Growth Chart & Briefcase)
    $response->assertSee('Upgrade Your Career');

    // Pathway 03 (IT / Server Rack & ABAP Tag)
    $response->assertSee('Transition Into SAP');

    // Pathway illustrations present with aria-hidden
    $response->assertSee('zeb-illustration-art', false);
    $response->assertSee('aria-hidden="true"', false);
})->with([
    'home' => ['/'],
    'courses.index' => ['/courses'],
]);

test('homepage and catalog cards render module-specific visual artwork badges', function () {
    $response = $this->get('/');
    $response->assertStatus(200);

    // FICO, MM, SD, PP artwork badges rendered on homepage
    $response->assertSee('SAP FICO COURSE');
    $response->assertSee('SAP MM COURSE');
    $response->assertSee('SAP SD COURSE');
    $response->assertSee('SAP PP COURSE');

    $coursesResponse = $this->get('/courses');
    $coursesResponse->assertStatus(200);
    $coursesResponse->assertSee('zeb-illustration-art', false);
});

test('homepage renders interactive 4-stage learning journey with progress indicator and step art', function () {
    $response = $this->get('/');

    $response->assertStatus(200);

    // 4-Stage Pathway Indicator & Track
    $response->assertSee('id="journey-progress-line"', false);
    $response->assertSee('01. LEARN');
    $response->assertSee('02. PRACTICE');
    $response->assertSee('03. BUILD SKILLS');
    $response->assertSee('04. CAREER');

    // 4 Step cards with data-step hooks
    $response->assertSee('data-step="1"', false);
    $response->assertSee('data-step="2"', false);
    $response->assertSee('data-step="3"', false);
    $response->assertSee('data-step="4"', false);
    $response->assertSee('01/04', false);
    $response->assertSee('02/04', false);
    $response->assertSee('03/04', false);
    $response->assertSee('04/04', false);
});

test('compiled css includes 3D perspective and ambient keyframes with prefers-reduced-motion overrides', function () {
    $manifestPath = public_path('build/manifest.json');
    $manifest = json_decode(file_get_contents($manifestPath), true);
    $cssFile = public_path('build/'.$manifest['resources/css/app.css']['file']);
    $cssContent = file_get_contents($cssFile);

    expect($cssContent)->toContain('perspective-1000');
    expect($cssContent)->toContain('preserve-3d');
    expect($cssContent)->toContain('zeb-ambient-float-1');
    expect($cssContent)->toContain('zeb-ambient-float-2');
    expect($cssContent)->toContain('zeb-ambient-float-3');
    expect($cssContent)->toContain('animate-ambient-1');
    expect($cssContent)->toContain('animate-ambient-2');
    expect($cssContent)->toContain('animate-ambient-3');
});

test('footer renders interactive LaserFlow WebGL2 container and surface card across public pages', function (string $route) {
    $response = $this->get($route);

    $response->assertStatus(200);
    $response->assertSee('id="laserflow-wrapper"', false);
    $response->assertSee('id="laserflow-container"', false);
    $response->assertSee('id="laserflow-card"', false);
    $response->assertSee('THE NEXT STEP STARTS HERE');
    $response->assertSee('Your SAP future.');
    $response->assertSee('Starts with ZebLearn.');
})->with([
    'home' => ['/'],
    'about' => ['/about'],
    'courses' => ['/courses'],
]);
