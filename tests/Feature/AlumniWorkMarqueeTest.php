<?php

use App\Models\Course;
use App\Services\TrustProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    app(TrustProofService::class)->seedInitialTrustRecords();
});

test('course landing page renders Where Our Certified Alumni Work continuous marquee with authentic logos', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();
    if (! $course) {
        $course = Course::factory()->published()->create([
            'name' => 'SAP MM Training',
            'slug' => 'sap-mm-training',
        ]);
    }

    $response = $this->get(route('courses.show', $course));

    $response->assertOk();

    // 1. Heading verification
    $response->assertSee('Where Our Certified Alumni Work');
    $response->assertSee('HIRING PARTNERS');

    // 2. Marquee container and track
    $response->assertSee('alumni-marquee-container');
    $response->assertSee('alumni-marquee-track');

    // 3. Authentic SVG logo files rendered
    $expectedLogos = [
        'accenture.svg',
        'deloitte.svg',
        'capgemini.svg',
        'tcs.svg',
        'infosys.svg',
        'wipro.svg',
        'pwc.svg',
        'ibm.svg',
    ];

    foreach ($expectedLogos as $logo) {
        $response->assertSee($logo);
    }

    // 4. Duplicate set accessibility attributes
    $response->assertSee('aria-hidden="true"', false);
    $response->assertSee('tabindex="-1"', false);

    // 5. Official corporate links
    $response->assertSee('https://www.accenture.com/');
    $response->assertSee('https://www.deloitte.com/');
    $response->assertSee('https://www.capgemini.com/');
    $response->assertSee('https://www.tcs.com/');
    $response->assertSee('https://www.infosys.com/');
    $response->assertSee('https://www.wipro.com/');
    $response->assertSee('https://www.pwc.com/');
    $response->assertSee('https://www.ibm.com/');

    // 6. Descriptive alt text
    $response->assertSee('alt="Accenture authentic logo"', false);
    $response->assertSee('alt="Deloitte authentic logo"', false);

    // 7. Compliance and Accredited Institutional Partners with authentic logos
    $response->assertSee('ACCREDITED INSTITUTIONAL PARTNERS &amp; COMPLIANCE', false);
    $response->assertSee('images/compliance/iso.webp');
    $response->assertSee('images/compliance/nsdc.webp');
    $response->assertSee('images/compliance/mca.webp');
    $response->assertSee('images/compliance/skill-india.webp');
    $response->assertSee('alt="ISO - International Organization for Standardization"', false);
    $response->assertSee('alt="NSDC - National Skill Development Corporation"', false);
    $response->assertSee('alt="Ministry of Corporate Affairs - Government of India"', false);
    $response->assertSee('alt="Skill India - कौशल भारत कुशल भारत"', false);
});
