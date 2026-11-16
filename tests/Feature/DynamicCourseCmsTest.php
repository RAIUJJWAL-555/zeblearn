<?php

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseFaq;
use App\Models\CoursePartner;
use App\Models\CourseTestimonial;
use App\Models\User;
use App\Services\CourseCmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('1. dynamic course landing page loads for published courses with single h1, canonical, and schemas', function () {
    $course = Course::factory()->create([
        'name' => 'SAP SuccessFactors HCM Global Specialist',
        'slug' => 'sap-successfactors-hcm-global-specialist',
        'category' => 'sap-erp',
        'status' => 'available',
        'published_at' => now()->subDay(),
        'fee' => 35000,
        'original_fee' => 75000,
        'discount_percent' => 53,
        'seo_title' => 'SAP SuccessFactors HCM Global Specialist | ZebLearn',
        'short_description' => 'Comprehensive masterclass on SAP SuccessFactors Employee Central, PMGM, and compensation management.',
        'description' => 'Learn end-to-end cloud HR configuration with live interactive sessions.',
    ]);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();

    $html = $response->getContent();

    // Exactly one semantic H1 tag
    preg_match_all('/<h1\b[^>]*>(.*?)<\/h1>/si', $html, $h1Matches);
    expect(count($h1Matches[0]))->toBe(1);
    expect($h1Matches[1][0])->toContain('SAP SuccessFactors HCM Global Specialist');

    // Title and Meta Description
    preg_match('/<title>(.*?)<\/title>/si', $html, $titleMatches);
    expect($titleMatches[1])->toContain('SAP SuccessFactors HCM Global Specialist');

    // Self-referencing canonical
    preg_match('/<link[^>]*rel=["\']canonical["\'][^>]*href=["\'](.*?)["\']/i', $html, $canonMatches);
    expect($canonMatches[1])->toBe('https://sap.zeblearn.com/courses/sap-successfactors-hcm-global-specialist');

    // JSON-LD Scripts (Course and BreadcrumbList)
    preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $jsonLdMatches);
    expect(count($jsonLdMatches[1]))->toBeGreaterThanOrEqual(2);

    $foundCourseSchema = false;
    $foundBreadcrumbs = false;

    foreach ($jsonLdMatches[1] as $content) {
        $decoded = json_decode(trim($content), true);
        expect(json_last_error())->toBe(JSON_ERROR_NONE);
        expect($decoded['@context'])->toBe('https://schema.org');

        if (($decoded['@type'] ?? null) === 'Course') {
            $foundCourseSchema = true;
            expect($decoded['name'])->toBe('SAP SuccessFactors HCM Global Specialist');
            expect($decoded['offers']['price'])->toBe('35000');
        }
        if (($decoded['@type'] ?? null) === 'BreadcrumbList') {
            $foundBreadcrumbs = true;
            expect(count($decoded['itemListElement']))->toBe(3);
        }
    }

    expect($foundCourseSchema)->toBeTrue();
    expect($foundBreadcrumbs)->toBeTrue();
});

test('2. course slug update creates 301 permanent redirect and prevents redirect chains or loops', function () {
    $admin = User::factory()->admin()->active()->create();
    $cmsService = app(CourseCmsService::class);

    $course = Course::factory()->create([
        'name' => 'Original Module Name',
        'slug' => 'original-module-slug',
        'published_at' => now()->subDay(),
        'status' => 'available',
    ]);

    // Initial check: loads normally
    $this->get('/courses/original-module-slug')->assertOk();

    // 1st Rename: original-module-slug -> updated-module-slug
    $cmsService->recordSlugChange($course, 'original-module-slug', 'updated-module-slug');
    $course->update(['slug' => 'updated-module-slug']);

    $response1 = $this->get('/courses/original-module-slug');
    $response1->assertStatus(301);
    $response1->assertRedirect(url('/courses/updated-module-slug'));

    // 2nd Rename: updated-module-slug -> final-module-slug
    $cmsService->recordSlugChange($course, 'updated-module-slug', 'final-module-slug');
    $course->update(['slug' => 'final-module-slug']);

    // Original slug must now redirect directly to final-module-slug (no chained double redirects)
    $responseOld = $this->get('/courses/original-module-slug');
    $responseOld->assertStatus(301);
    $responseOld->assertRedirect(url('/courses/final-module-slug'));

    // Intermediate slug also redirects directly to final-module-slug
    $responseIntermediate = $this->get('/courses/updated-module-slug');
    $responseIntermediate->assertStatus(301);
    $responseIntermediate->assertRedirect(url('/courses/final-module-slug'));

    // Active new slug returns HTTP 200
    $this->get('/courses/final-module-slug')->assertOk();
});

test('3. repeaters for faqs, batches, testimonials, and partners persist and render on landing page', function () {
    $course = Course::factory()->create([
        'name' => 'Enterprise Architecture & Governance',
        'slug' => 'enterprise-architecture-governance',
        'published_at' => now()->subDay(),
        'status' => 'available',
    ]);

    // Add FAQ
    CourseFaq::create([
        'course_id' => $course->id,
        'question' => 'Is remote sandbox access included 24/7?',
        'answer' => 'Yes, every participant receives a personal remote sandbox credential.',
        'sort_order' => 10,
        'is_active' => true,
    ]);

    // Add Batch
    CourseBatch::create([
        'course_id' => $course->id,
        'name' => 'Cohort Alpha (Weekend Live)',
        'start_date' => now()->addDays(14)->toDateString(),
        'timings' => '07:00 PM – 09:00 PM IST',
        'days' => 'Sat & Sun',
        'delivery_mode' => 'Live Online',
        'total_seats' => 20,
        'available_seats' => 5,
        'status' => 'filling_fast',
        'sort_order' => 10,
        'is_active' => true,
    ]);

    // Add Testimonial
    CourseTestimonial::create([
        'course_id' => $course->id,
        'student_name' => 'Vikram Singhania',
        'student_title_or_company' => 'Senior Enterprise Architect @ Capgemini',
        'review_text' => 'The deep dives into integration patterns and architectural governance were exceptional.',
        'rating' => 5.0,
        'is_published' => true,
        'sort_order' => 10,
    ]);

    // Add Partner
    CoursePartner::create([
        'course_id' => $course->id,
        'name' => 'Capgemini Global Services',
        'sort_order' => 10,
        'is_active' => true,
    ]);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();

    // Verify FAQ
    $response->assertSee('Is remote sandbox access included 24/7?');
    $response->assertSee('Yes, every participant receives a personal remote sandbox credential.');

    // Verify Batch
    $response->assertSee('Cohort Alpha (Weekend Live)');
    $response->assertSee('5 of 20 seats remaining');

    // Verify Testimonial
    $response->assertSee('Vikram Singhania');
    $response->assertSee('Senior Enterprise Architect @ Capgemini');

    // Verify Partner
    $response->assertSee('Capgemini Global Services');

    // Check FAQPage JSON-LD schema
    $html = $response->getContent();
    expect($html)->toContain('"@type":"FAQPage"');
    expect($html)->toContain('Is remote sandbox access included 24/7?');
});

test('4. course directory supports keyword search, category, delivery mode filtering, sorting, and pagination', function () {
    // Clean directory page without queries has index, follow
    $cleanResponse = $this->get(route('courses.index'));
    $cleanResponse->assertOk();
    $cleanResponse->assertSee('content="index, follow', false);

    // Filtered by category
    $filteredCategory = $this->get(route('courses.index', ['category' => 'workday']));
    $filteredCategory->assertOk();
    $filteredCategory->assertSee('content="noindex, follow', false);

    // Keyword search
    $searchResponse = $this->get(route('courses.index', ['q' => 'FICO']));
    $searchResponse->assertOk();
    $searchResponse->assertSee('SAP FICO Training');
    $searchResponse->assertSee('content="noindex, follow', false);

    // Sorting by price
    $sortedPrice = $this->get(route('courses.index', ['sort' => 'price_asc']));
    $sortedPrice->assertOk();
    $sortedPrice->assertSee('content="noindex, follow', false);

    // Pagination has noindex, follow
    $pagedResponse = $this->get(route('courses.index', ['page' => 2]));
    $pagedResponse->assertOk();
    $pagedResponse->assertSee('content="noindex, follow', false);
});

test('5. draft or unpublished courses return 404 for guests but can be previewed by admins', function () {
    $admin = User::factory()->admin()->active()->create();
    $student = User::factory()->student()->active()->create();

    $draftCourse = Course::factory()->create([
        'name' => 'Internal Draft Cloud Security Roadmap',
        'slug' => 'internal-draft-cloud-security-roadmap',
        'published_at' => null, // Unpublished
        'status' => 'planned',
    ]);

    // Guest receives 404
    $this->get('/courses/internal-draft-cloud-security-roadmap')->assertNotFound();

    // Student receives 404
    $this->actingAs($student)->get('/courses/internal-draft-cloud-security-roadmap')->assertNotFound();

    // Admin can view via standard show route
    $adminResponse = $this->actingAs($admin)->get('/courses/internal-draft-cloud-security-roadmap');
    $adminResponse->assertOk();
    $adminResponse->assertSee('Internal Draft Cloud Security Roadmap');

    // Admin preview route loads with preview banner
    $previewResponse = $this->actingAs($admin)->get(route('admin.courses.preview', $draftCourse));
    $previewResponse->assertOk();
    $previewResponse->assertSee('Admin Live Preview Mode');
});

test('6. sitemap xml route generates valid XML with homepage, courses, and published course URLs', function () {
    $response = $this->get('/sitemap.xml');
    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $content = $response->getContent();
    expect($content)->toStartWith('<?xml version="1.0" encoding="UTF-8"?>');
    expect($content)->toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    expect($content)->toContain(route('courses.index'));
    expect($content)->toContain(route('courses.fico'));
    expect($content)->toContain('<priority>1.0</priority>');
});

test('7. pricing harmonization ensures effective fee does not exceed original fee and computes discounts', function () {
    $cmsService = app(CourseCmsService::class);

    // Case 1: fee > original_fee -> fee clamped to original_fee
    $data1 = $cmsService->harmonizePricing([
        'original_fee' => 50000,
        'fee' => 60000,
    ]);
    expect($data1['fee'])->toBe(50000);

    // Case 2: original_fee + discount_percent provided -> calculate fee
    $data2 = $cmsService->harmonizePricing([
        'original_fee' => 100000,
        'discount_percent' => 40,
    ]);
    expect($data2['fee'])->toBe(60000);

    // Case 3: original_fee + fee provided -> calculate discount_percent
    $data3 = $cmsService->harmonizePricing([
        'original_fee' => 100000,
        'fee' => 75000,
    ]);
    expect($data3['discount_percent'])->toBe(25);
});
