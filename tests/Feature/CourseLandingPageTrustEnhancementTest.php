<?php

use App\Models\Course;
use App\Services\TrustProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    app(TrustProofService::class)->seedInitialTrustRecords();
});

test('1. SAP MM course landing page renders with HTTP 200 and SEO structured data', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();
    if (! $course) {
        $course = Course::factory()->create([
            'name' => 'SAP MM Training – Materials Management',
            'slug' => 'sap-mm-training',
            'category' => 'sap-erp',
            'published_at' => now()->subDay(),
        ]);
    }

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('SAP MM Training');
    $response->assertSee('schema.org', false);
    $response->assertSee('BreadcrumbList', false);
});

test('2. verified partners section renders with categorized badges and enterprise logos', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('HIRING PARTNERS');
    $response->assertSee('GLOBAL SYSTEMS INTEGRATORS');
    $response->assertSee('Accenture');
    $response->assertSee('Deloitte');
    $response->assertSee('Capgemini');
    $response->assertSee('TCS');
});

test('3. verified student placements render with roles, companies, packages, and verification pills', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Recent Verified Student Placements');
    $response->assertSee('Sneha Nair');
    $response->assertSee('7.8 LPA');
    $response->assertSee('Supply Chain Solutions');
    $response->assertSee('Verified');
});

test('4. expert faculty and mentors showcase renders with specialization, experience, and qualifications', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('PRACTITIONER FACULTY');
    $response->assertSee('Rajesh Sharma');
    $response->assertSee('Neha Kulkarni');
    $response->assertSee('Vikram Malhotra');
    $response->assertSee('14+ Yrs Exp');
    $response->assertSee('B.Tech (Mechanical), MBA (Supply Chain Management)');
    $response->assertSee('LinkedIn Profile');
});

test('5. enterprise capstone and implementation projects render all 6 SAP MM scenarios with business tasks', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Enterprise Capstone &amp; Implementation Projects', false);
    $response->assertSee('End-to-End Procure-to-Pay (P2P) Implementation &amp; Integration', false);
    $response->assertSee('Enterprise Material Master &amp; Vendor Master Governance', false);
    $response->assertSee('Purchase Requisition (PR), RFQ &amp; Dynamic Release Strategies', false);
    $response->assertSee('Inventory Management, Physical Inventory &amp; Multi-Tier Stock Transfers', false);
    $response->assertSee('Goods Receipt (MIGO) and Logistic Invoice Verification (MIRO)', false);
    $response->assertSee('Material Valuation, Split Valuation &amp; Automatic Account Determination (OBYC)', false);
    $response->assertSee('Automotive Manufacturing');
    $response->assertSee('Simulated Enterprise Capstone');
});

test('6. two-tier certification architecture cleanly differentiates ZebLearn certificate and SAP vendor exam prep', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Two-Tier Certification Architecture');
    $response->assertSee('ZebLearn Course Completion Certificate');
    $response->assertSee('85%+ Live Cohort Attendance');
    $response->assertSee('External Professional Certification Alignment');
    $response->assertSee('SAP Certified Associate – Procurement with SAP S/4HANA');
    $response->assertSee('Exam Code: C_TS452');
    $response->assertSee('ZebLearn does not claim to issue official SAP-authorized vendor certifications');
});

test('7. certificate gallery renders sample credentials and verification portal link', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('VERIFIED CERTIFICATE SAMPLES &amp; CREDENTIAL GALLERY', false);
    $response->assertSee('ZebLearn Certified SAP Materials Management Professional');
    $response->assertSee('ZEB-MM-2026-8941');
});

test('8. student video stories render with video modal triggers, quotes, and consent badges', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Watch Genuine Student Success Stories');
    $response->assertSee('Pooja Verma');
    $response->assertSee('Arup Ghosh');
    $response->assertSee('Divya Shree');
    $response->assertSee('Consented Story');
    $response->assertSee('Watch Video Story');
});

test('9. awards and institutional recognition section renders verified honors with source details', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Awards &amp; Institutional Recognition', false);
    $response->assertSee('Excellence in Enterprise ERP Training 2026');
    $response->assertSee('EduTech National Council');
    $response->assertSee('Top Professional Upskilling Academy 2025');
});

test('10. public certificate verification portal validates sample credentials', function () {
    $response = $this->get(route('certificates.verify', ['certificate_number' => 'ZEB-MM-2026-8941']));
    $response->assertOk();
    $response->assertSee('ZEB-MM-2026-8941');
    $response->assertSee('Ankit Sharma');
    $response->assertSee('SAP MM Training – Materials Management');
    $response->assertSee('Verified Official Certificate');
});

test('11. landing page section ordering and visibility can be dynamically configured', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    // Disable projects section
    $course->section_config = [
        'projects' => ['visible' => false, 'order' => 80],
    ];
    $course->save();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertDontSee('Enterprise Capstone &amp; Implementation Projects', false);

    // Re-enable projects section
    $course->section_config = null;
    $course->save();

    $response2 = $this->get(route('courses.show', $course));
    $response2->assertOk();
    $response2->assertSee('Enterprise Capstone &amp; Implementation Projects', false);
});
