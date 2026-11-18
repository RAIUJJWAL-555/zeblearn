<?php

use App\Models\Blog;
use App\Models\Course;
use App\Models\InternalLinkOverride;
use App\Models\User;
use App\Services\InternalLinkingService;

test('internal linking service discovers matching course for a blog automatically', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP SD & S/4 HANA Masterclass',
        'slug' => 'sap-sd-2',
        'category' => 'sap-functional',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'Complete SAP SD Sales Order Configuration Guide',
        'slug' => 'complete-sap-sd-sales-order-configuration-guide',
        'robots_index' => true,
    ]);

    $service = app(InternalLinkingService::class);
    $recommendations = $service->getRecommendationsForBlog($blog);

    expect($recommendations['primaryCourse'])->not->toBeNull()
        ->and($recommendations['primaryCourse']->id)->toBe($course->id);
});

test('contextual inline link injection respects html boundaries and frequency limits', function () {
    $service = app(InternalLinkingService::class);

    $html = '<p>In this tutorial, we will learn about SAP FICO on S/4 HANA and how to configure general ledger accounts.</p>'
        .'<h2>Do not link in SAP FICO on S/4 HANA heading</h2>'
        .'<p><a href="/existing-link">Existing SAP FICO on S/4 HANA link</a> should not be modified.</p>'
        .'<pre>Code snippet SAP FICO on S/4 HANA</pre>'
        .'<p>Here is another paragraph discussing SAP FICO on S/4 HANA financial closing.</p>';

    $anchors = [
        'SAP FICO on S/4 HANA' => 'http://localhost:8000/sap-functional/sap-fico-on-s-4-hana',
    ];

    $result = $service->injectContextualLinks($html, $anchors, 2);

    // Assert link was injected in paragraph
    expect($result)->toContain('<a href="http://localhost:8000/sap-functional/sap-fico-on-s-4-hana"')
        // Assert heading remained unlinked
        ->and($result)->toContain('<h2>Do not link in SAP FICO on S/4 HANA heading</h2>')
        // Assert pre tag remained unlinked
        ->and($result)->toContain('<pre>Code snippet SAP FICO on S/4 HANA</pre>')
        // Assert existing link was untouched
        ->and($result)->toContain('<a href="/existing-link">Existing SAP FICO on S/4 HANA link</a>');
});

test('unpublished courses and noindex blogs are excluded from recommendations', function () {
    $draftCourse = Course::factory()->create([
        'name' => 'Draft Secret SAP FICO',
        'slug' => 'draft-secret-sap-fico',
        'published_at' => null,
    ]);

    $noindexBlog = Blog::factory()->published()->create([
        'title' => 'Noindex SAP FICO Internal Note',
        'slug' => 'noindex-sap-fico-internal-note',
        'robots_index' => false,
    ]);

    $activeBlog = Blog::factory()->published()->create([
        'title' => 'Public SAP FICO Overview',
        'slug' => 'public-sap-fico-overview',
        'robots_index' => true,
    ]);

    $service = app(InternalLinkingService::class);
    $recommendations = $service->getRecommendationsForBlog($activeBlog);

    // Draft course should not be primary course
    if ($recommendations['primaryCourse']) {
        expect($recommendations['primaryCourse']->id)->not->toBe($draftCourse->id);
    }

    // Noindex blog should not appear in related blogs
    expect($recommendations['relatedBlogs']->pluck('id'))->not->toContain($noindexBlog->id);
});

test('admin manual overrides pin or exclude links correctly', function () {
    $course1 = Course::factory()->published()->create([
        'name' => 'SAP MM Simple Logistics',
        'slug' => 'mm-s4-hana-simple-logistics',
    ]);

    $course2 = Course::factory()->published()->create([
        'name' => 'SAP FICO Financial Accounting',
        'slug' => 'sap-fico-on-s-4-hana',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'Procurement and Inventory Questions in SAP MM',
        'slug' => 'procurement-inventory-questions-sap-mm',
        'robots_index' => true,
    ]);

    // Admin pins Course 2 (FICO) manually to this Blog
    InternalLinkOverride::create([
        'source_type' => 'blog',
        'source_id' => $blog->id,
        'target_type' => 'course',
        'target_id' => $course2->id,
        'action' => 'pin',
    ]);

    $service = app(InternalLinkingService::class);
    $service->invalidateCache('blog', $blog->id);
    $recommendations = $service->getRecommendationsForBlog($blog);

    expect($recommendations['primaryCourse']->id)->toBe($course2->id);

    // Now admin excludes Course 2
    InternalLinkOverride::where('source_id', $blog->id)->delete();
    InternalLinkOverride::create([
        'source_type' => 'blog',
        'source_id' => $blog->id,
        'target_type' => 'course',
        'target_id' => $course2->id,
        'action' => 'exclude',
    ]);

    $service->invalidateCache('blog', $blog->id);
    $recommendationsAfterExclude = $service->getRecommendationsForBlog($blog);

    expect($recommendationsAfterExclude['primaryCourse']->id)->not->toBe($course2->id);
});

test('lead attribution endpoint records source url and campaign parameters', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP SD & S/4 HANA',
        'slug' => 'sap-sd-2',
    ]);

    $response = $this->postJson(route('leads.store'), [
        'name' => 'Rohan Sharma',
        'phone' => '9876543210',
        'email' => 'rohan@example.com',
        'course_name' => 'SAP SD & S/4 HANA',
        'source_url' => 'http://localhost:8000/sap-sd-interview-question-answer',
        'cta_type' => 'blog_bridge',
        'utm_source' => 'seo_spider',
    ]);

    $response->assertCreated();
    $response->assertJson([
        'status' => 'success',
        'message' => 'Lead attribution recorded successfully.',
    ]);

    $this->assertDatabaseHas('leads', [
        'name' => 'Rohan Sharma',
        'phone' => '9876543210',
        'email' => 'rohan@example.com',
        'course_name' => 'SAP SD & S/4 HANA',
        'course_id' => $course->id,
        'cta_type' => 'blog_bridge',
        'utm_source' => 'seo_spider',
    ]);
});

test('admin can access seo spider web dashboard and trigger recalculation', function () {
    $admin = User::factory()->admin()->active()->create();

    $response = $this->actingAs($admin)->get(route('admin.seo.spider'));
    $response->assertOk();
    $response->assertSee('SEO Spider Web Architecture');
    $response->assertSee('Total Internal Links');
    $response->assertSee('Reconcile Graph');

    // Test recalculate action
    $recalc = $this->actingAs($admin)->post(route('admin.seo.recalculate'));
    $recalc->assertRedirect(route('admin.seo.spider'));
    $recalc->assertSessionHas('success');
});
