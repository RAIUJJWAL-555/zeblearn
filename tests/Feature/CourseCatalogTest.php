<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('course catalog page loads successfully and displays structured academy courses', function () {
    $response = $this->get('/courses');

    $response->assertStatus(200);

    // 1. Academy Title and Editorial Header
    $response->assertSee('MULTI-MODULE SAP ACADEMY');
    $response->assertSee('PRACTICAL SAP ACADEMY');
    $response->assertSee('BUILT FOR CONSULTANTS');

    // 2. Audience Pathways
    $response->assertSee('Start Your SAP Career');
    $response->assertSee('Upgrade Your Career');
    $response->assertSee('Transition Into SAP');

    // 3. Status Filters & Category Pills
    $response->assertSee('FILTER BY SPECIALIZATION');
    $response->assertSee('All Tracks');
    $response->assertSee('SAP Functional');
    $response->assertSee('SAP Technical');
    $response->assertSee('SAP Cloud &amp; Specialized', false);

    // 4. Confirmed Available Flagship Course (SAP FICO)
    $response->assertSee('SAP FICO Training');
    $response->assertSee('₹25,000 + GST');
    $response->assertSee('Available');

    // 5. Upcoming cohorts (MM, SD, PP, ABAP, Basis)
    $response->assertSee('SAP MM Training');
    $response->assertSee('SAP SD Training');
    $response->assertSee('SAP PP Training');
    $response->assertSee('SAP ABAP on HANA Training');
    $response->assertSee('SAP S/4HANA Basis &amp; Administration', false);
    $response->assertSee('UPCOMING BATCH');

    // 6. Planned Roadmap with transparency callout
    $response->assertSee('ACADEMY ROADMAP CANDIDATES');
    $response->assertSee('CURRICULUM IN DEVELOPMENT');
    $response->assertSee('SAP EWM (Extended Warehouse Management)');
    $response->assertSee('SAP BTP (Business Technology Platform)');

    // 7. Reusable Academy Advisory Helpline Banner
    $response->assertSee('ACADEMY ADVISORY HELPLINE');
    $response->assertSee('NOT SURE WHICH SAP TRACK FITS YOUR EXPERIENCE?');
});

test('course catalog page includes valid ItemList and BreadcrumbList JSON-LD schema', function () {
    $response = $this->get('/courses');

    $response->assertStatus(200);
    $html = $response->getContent();

    preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $matches);
    expect($matches[1])->not->toBeEmpty();

    $foundItemList = false;
    $foundBreadcrumbs = false;

    foreach ($matches[1] as $jsonString) {
        $data = json_decode(trim($jsonString), true);
        expect(json_last_error())->toBe(JSON_ERROR_NONE);
        expect($data)->toHaveKey('@context');
        expect($data['@context'])->toBe('https://schema.org');

        if (($data['@type'] ?? null) === 'ItemList') {
            $foundItemList = true;
            expect($data)->toHaveKey('itemListElement');
            expect($data['itemListElement'])->toBeArray();
            expect(count($data['itemListElement']))->toBeGreaterThanOrEqual(1);
        }

        if (($data['@type'] ?? null) === 'BreadcrumbList') {
            $foundBreadcrumbs = true;
            expect($data)->toHaveKey('itemListElement');
            expect(count($data['itemListElement']))->toBe(2);
        }
    }

    expect($foundItemList)->toBeTrue();
    expect($foundBreadcrumbs)->toBeTrue();
});

test('course catalog controller supports category and status filtering query parameters', function () {
    // Filter by functional category
    $responseFunctional = $this->get('/courses?category=functional');
    $responseFunctional->assertStatus(200);
    $responseFunctional->assertSee('SAP FICO Training');
    $responseFunctional->assertSee('SAP MM Training');

    // Filter by technical category
    $responseTechnical = $this->get('/courses?category=technical');
    $responseTechnical->assertStatus(200);
    $responseTechnical->assertSee('SAP ABAP on HANA Training');
    $responseTechnical->assertSee('SAP S/4HANA Basis &amp; Administration', false);

    // Filter by available status
    $responseAvailable = $this->get('/courses?status=available');
    $responseAvailable->assertStatus(200);
    $responseAvailable->assertSee('SAP FICO Training');
});
