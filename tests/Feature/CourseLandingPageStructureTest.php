<?php

use App\Models\Course;
use App\Services\TrustProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    app(TrustProofService::class)->seedInitialTrustRecords();
});

test('certificate showcase appears exactly once on sap-mobility course page', function () {
    $course = Course::where('slug', 'sap-mobility')->first();
    if (! $course) {
        $course = Course::factory()->published()->create([
            'name' => 'SAP Mobility',
            'slug' => 'sap-mobility',
        ]);
    }

    $response = $this->get('/courses/'.$course->slug);
    $response->assertOk();

    $html = $response->getContent();

    // 1. Certificate showcase section ID must appear EXACTLY ONCE
    expect(substr_count($html, 'id="certificate-showcase"'))->toBe(1);

    // 2. Heading "Earn a Recognized ... Training Certificate" must appear EXACTLY ONCE
    expect(substr_count($html, 'Earn a Recognized '.$course->name.' Training Certificate'))->toBe(1);

    // 3. The 3 certificate images must each appear in the showcase
    expect(substr_count($html, '/images/certificates/newc1.webp'))->toBeGreaterThanOrEqual(1)
        ->and(substr_count($html, '/images/certificates/newc2.webp'))->toBeGreaterThanOrEqual(1)
        ->and(substr_count($html, '/images/certificates/newc3.webp'))->toBeGreaterThanOrEqual(1);

    // 4. Candidate names and credential descriptions must be present
    expect($html)->toContain('Santosh Kirdat')
        ->and($html)->toContain('Tapaswini Sahoo')
        ->and($html)->toContain('SAP Certified Associate &ndash; Back-End Developer, ABAP Cloud');
});

test('industry partnerships section appears exactly once on course detail page', function () {
    $course = Course::where('slug', 'sap-mobility')->first();
    if (! $course) {
        $course = Course::factory()->published()->create([
            'name' => 'SAP Mobility',
            'slug' => 'sap-mobility',
        ]);
    }

    $response = $this->get('/courses/'.$course->slug);
    $response->assertOk();

    $html = $response->getContent();

    // Overline and heading must appear exactly once
    expect(substr_count($html, 'OUR PARTNERS'))->toBe(1)
        ->and(substr_count($html, 'INDUSTRY-ALIGNED '.strtoupper($course->name).' TRAINING PARTNERSHIPS'))->toBe(1);
});

test('critical section IDs in rendered DOM are strictly unique without duplication', function () {
    $course = Course::where('slug', 'sap-mobility')->first();
    if (! $course) {
        $course = Course::factory()->published()->create([
            'name' => 'SAP Mobility',
            'slug' => 'sap-mobility',
        ]);
    }

    $response = $this->get('/courses/'.$course->slug);
    $response->assertOk();

    $html = $response->getContent();

    // Critical section IDs must appear at most once, and never duplicated
    $criticalIds = [
        'certificate-showcase',
        'certification',
        'course-overview',
        'curriculum',
        'projects',
        'faculty',
        'reviews',
        'video-stories',
        'batches',
        'faqs',
    ];

    foreach ($criticalIds as $id) {
        $occurrences = substr_count($html, 'id="'.$id.'"');
        expect($occurrences)->toBeLessThanOrEqual(1, "ID '{$id}' is duplicated: found {$occurrences} times.");
    }
});

test('reordering or customizing visible CMS section keys does not cause certificate duplication', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP Extended Warehouse Management',
        'slug' => 'sap-ewm-warehouse-management-custom-config',
        'section_config' => [
            'hero' => ['visible' => true, 'order' => 10],
            'partners' => ['visible' => true, 'order' => 20],
            'overview' => ['visible' => true, 'order' => 30],
            'certification_partners' => ['visible' => true, 'order' => 40],
            'sap_global_certification' => ['visible' => true, 'order' => 50],
            'why_choose_us' => ['visible' => true, 'order' => 60],
            'curriculum' => ['visible' => true, 'order' => 70],
            'certification' => ['visible' => true, 'order' => 80],
            'faqs' => ['visible' => true, 'order' => 90],
            'cta' => ['visible' => true, 'order' => 100],
        ],
    ]);

    $response = $this->get('/courses/'.$course->slug);
    $response->assertOk();

    $html = $response->getContent();

    // Certificate showcase must appear exactly once despite presence of unmapped keys
    expect(substr_count($html, 'id="certificate-showcase"'))->toBe(1)
        ->and(substr_count($html, 'Earn a Recognized '.$course->name.' Training Certificate'))->toBe(1);
});

test('certificate modal lightbox and Alpine state interactions remain fully functional', function () {
    $course = Course::where('slug', 'sap-mobility')->first();
    if (! $course) {
        $course = Course::factory()->published()->create([
            'name' => 'SAP Mobility',
            'slug' => 'sap-mobility',
        ]);
    }

    $response = $this->get('/courses/'.$course->slug);
    $response->assertOk();

    $html = $response->getContent();

    // Modal state bindings and click triggers
    expect($html)->toContain('certModalOpen')
        ->and($html)->toContain('openCert(')
        ->and($html)->toContain('x-show="certModalOpen"')
        ->and($html)->toContain(':src="activeCertImg"');
});

test('multiple SAP courses all render certificate showcase exactly once without loop leakage', function () {
    $courses = Course::whereIn('slug', [
        'sap-mobility',
        'sap-fico-on-s-4-hana',
        'mm-s4-hana-simple-logistics',
    ])->get();

    foreach ($courses as $c) {
        $response = $this->get('/courses/'.$c->slug);
        $response->assertOk();

        $html = $response->getContent();

        // Exactly once on each course
        expect(substr_count($html, 'id="certificate-showcase"'))
            ->toBe(1, "Course '{$c->slug}' has unexpected count for #certificate-showcase");

        expect(substr_count($html, 'Earn a Recognized '.$c->name.' Training Certificate'))
            ->toBe(1, "Course '{$c->slug}' has unexpected heading count for certificate showcase");
    }
});
