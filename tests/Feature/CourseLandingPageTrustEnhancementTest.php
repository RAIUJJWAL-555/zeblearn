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
    $response->assertSee('OUR PARTNERS');
    $response->assertSee('TRAINING PARTNERSHIPS');
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

test('5. enterprise capstone marquee renders corporate project scenarios', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Capstone Projects in '.$course->name.' Training');
    $response->assertSee('Flipkart SAP SD Order-to-Cash');
    $response->assertSee('TCS Global SAP ABAP Rollout');
    $response->assertSee('Aditya Birla SAP MM + Procurement');
    $response->assertSee('View More');
    $response->assertSee('PROJECT');
});

test('6. two-tier certification architecture cleanly differentiates ZebLearn certificate and SAP vendor exam prep', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Two-Tier Certification Architecture');
    $response->assertSee('ZebLearn Course Completion Certificate');
    $response->assertSee('QR Verify');
    $response->assertSee('External Professional Certification Alignment');
    $response->assertSee('SAP Certified Associate – Procurement with SAP S/4HANA');
    $response->assertSee('Exam Code: C_TS452');
    $response->assertSee('ZebLearn does not claim to issue official SAP-authorized vendor certifications');
});

test('7. certificate gallery renders certificate showcase and verified honors', function () {
    $course = Course::where('slug', 'sap-mm-training')->first();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('certificate-showcase');
    $response->assertSee('VERIFIED SAP ACADEMY EXCELLENCE');
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
    $response->assertDontSee('Capstone Projects in '.$course->name.' Training');

    // Re-enable projects section
    $course->section_config = null;
    $course->save();

    $response2 = $this->get(route('courses.show', $course));
    $response2->assertOk();
    $response2->assertSee('Capstone Projects in '.$course->name.' Training');
});
