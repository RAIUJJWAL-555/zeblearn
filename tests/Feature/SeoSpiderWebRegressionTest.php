<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\Course;
use App\Services\InternalLinkingService;
use App\Services\SitemapService;

test('new published blog automatically creates incoming and outgoing links', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP MM Simple Logistics',
        'slug' => 'mm-s4-hana-simple-logistics',
    ]);

    $newBlog = Blog::factory()->published()->create([
        'title' => 'Top 25 SAP MM Material Valuation and Sourcing Questions',
        'slug' => 'top-25-sap-mm-material-valuation-and-sourcing-questions',
        'robots_index' => true,
    ]);

    $service = app(InternalLinkingService::class);
    $blogRecs = $service->getRecommendationsForBlog($newBlog);

    // Blog automatically links to MM course
    expect($blogRecs['primaryCourse'])->not->toBeNull()
        ->and($blogRecs['primaryCourse']->id)->toBe($course->id);

    // Course automatically discovers this new blog in its related guides
    $courseRecs = $service->getRecommendationsForCourse($course);
    $relatedBlogIds = $courseRecs['relatedBlogs']->pluck('id')->merge($courseRecs['interviewQuestions']->pluck('id'));
    expect($relatedBlogIds)->toContain($newBlog->id);
});

test('updating an article recalculates relationships automatically', function () {
    $courseFico = Course::factory()->published()->create([
        'name' => 'SAP FICO on S/4 HANA',
        'slug' => 'sap-fico-on-s-4-hana',
    ]);

    $courseSd = Course::factory()->published()->create([
        'name' => 'SAP SD & S/4 HANA',
        'slug' => 'sap-sd-2',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'SAP SD Customer Master Configuration',
        'slug' => 'sap-sd-customer-master-configuration',
        'robots_index' => true,
    ]);

    $service = app(InternalLinkingService::class);
    $recs = $service->getRecommendationsForBlog($blog);
    expect($recs['primaryCourse']->id)->toBe($courseSd->id);

    // Update title to SAP FICO topic
    $blog->title = 'SAP FICO Asset Accounting Ledger Closing';
    $blog->slug = 'sap-fico-asset-accounting-ledger-closing';
    $blog->save();

    // Cache should be automatically invalidated and recomputed to FICO
    $recomputedRecs = $service->getRecommendationsForBlog($blog);
    expect($recomputedRecs['primaryCourse']->id)->toBe($courseFico->id);
});

test('existing urls, sitemap xml, and canonical structures remain intact', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP FICO on S/4 HANA',
        'slug' => 'sap-fico-on-s-4-hana',
        'category' => 'sap-functional',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'SAP SD Power User Certificate',
        'slug' => 'sap-sd-power-user-certificate',
        'robots_index' => true,
    ]);

    // Check course canonical url structure /{category}/{course}
    expect($course->url)->toBe(url('/sap-functional/sap-fico-on-s-4-hana'));

    // Check blog canonical url structure /{slug}/
    expect($blog->effective_canonical_url)->toBe(url('/sap-sd-power-user-certificate/'));

    // Check sitemap contains canonical URLs and valid XML
    $sitemapService = app(SitemapService::class);
    $xml = $sitemapService->generateXml();

    expect($xml)->toContain('<urlset')
        ->and($xml)->toContain(htmlspecialchars($course->url))
        ->and($xml)->toContain(htmlspecialchars($blog->effective_canonical_url));
});

test('real existing database blogs have high link coverage and zero broken destinations', function () {
    $service = app(InternalLinkingService::class);
    $stats = $service->getSpiderWebStats();

    expect($stats['health_score'])->toBeGreaterThanOrEqual(95);

    // Test on real published blog 2 ("SAP SD INTERVIEW QUESTION ANSWER") if running against populated database
    $realBlog = Blog::where('slug', 'sap-sd-interview-question-answer')->first();
    if ($realBlog) {
        $recs = $service->getRecommendationsForBlog($realBlog);
        expect($recs['primaryCourse'])->not->toBeNull()
            ->and($recs['primaryCourse']->slug)->toBe('sap-sd-2')
            ->and($recs['relatedBlogs'])->not->toBeEmpty();
    }
});
