<?php

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;

test('main blog listing sorts published articles newest published first', function () {
    // 3 blogs with distinct published_at dates
    $oldBlog = Blog::factory()->published()->create([
        'title' => 'Old Article 2024',
        'published_at' => now()->subMonths(6),
    ]);
    $midBlog = Blog::factory()->published()->create([
        'title' => 'Mid Article 2025',
        'published_at' => now()->subDays(10),
    ]);
    $newBlog = Blog::factory()->published()->create([
        'title' => 'Brand New Article Today',
        'published_at' => now()->subMinutes(5),
    ]);

    $response = $this->get('/blogs');

    $response->assertOk();

    // Verify ordering in rendered HTML: newest article appears before older ones
    $content = $response->getContent();
    $posNew = strpos($content, 'Brand New Article Today');
    $posMid = strpos($content, 'Mid Article 2025');
    $posOld = strpos($content, 'Old Article 2024');

    expect($posNew)->not->toBeFalse();
    expect($posMid)->not->toBeFalse();
    expect($posOld)->not->toBeFalse();

    expect($posNew)->toBeLessThan($posMid);
    expect($posMid)->toBeLessThan($posOld);
});

test('draft scheduled and unpublished articles are hidden from public listings', function () {
    $publishedBlog = Blog::factory()->published()->create([
        'title' => 'Active Live Published Article',
        'published_at' => now()->subHour(),
    ]);

    $draftBlog = Blog::factory()->create([
        'title' => 'Secret Draft In Progress',
        'status' => Blog::STATUS_DRAFT,
        'published_at' => null,
    ]);

    $scheduledBlog = Blog::factory()->create([
        'title' => 'Future Scheduled Article Tomorrow',
        'status' => Blog::STATUS_PUBLISHED,
        'published_at' => now()->addDays(2),
    ]);

    $response = $this->get('/blogs');

    $response->assertOk();
    $response->assertSee('Active Live Published Article');
    $response->assertDontSee('Secret Draft In Progress');
    $response->assertDontSee('Future Scheduled Article Tomorrow');
});

test('created_at is used as fallback when published_at is null for published status', function () {
    $blogWithDate = Blog::factory()->published()->create([
        'title' => 'Article With Explicit Date',
        'published_at' => now()->subDays(5),
    ]);

    // Published article without explicit published_at uses created_at
    $blogFallback = Blog::factory()->create([
        'title' => 'Article Relying On Created At Fallback',
        'status' => Blog::STATUS_PUBLISHED,
        'published_at' => null,
        'created_at' => now()->subMinutes(1),
    ]);

    $response = $this->get('/blogs');

    $response->assertOk();
    $content = $response->getContent();

    $posFallback = strpos($content, 'Article Relying On Created At Fallback');
    $posDate = strpos($content, 'Article With Explicit Date');

    expect($posFallback)->not->toBeFalse();
    expect($posDate)->not->toBeFalse();
    // Since created_at was 1 min ago and published_at was 5 days ago, fallback is newer
    expect($posFallback)->toBeLessThan($posDate);
});

test('category listing pages sort articles newest published first', function () {
    $category = BlogCategory::factory()->create([
        'name' => 'SAP Architecture',
        'slug' => 'sap-architecture',
    ]);

    $catOld = Blog::factory()->published()->create([
        'title' => 'SAP Architecture Old Post',
        'published_at' => now()->subMonths(3),
    ]);
    $catNew = Blog::factory()->published()->create([
        'title' => 'SAP Architecture New Post',
        'published_at' => now()->subHours(2),
    ]);

    $catOld->categories()->attach($category);
    $catNew->categories()->attach($category);

    $response = $this->get('/category/sap-architecture');

    $response->assertOk();
    $content = $response->getContent();

    $posNew = strpos($content, 'SAP Architecture New Post');
    $posOld = strpos($content, 'SAP Architecture Old Post');

    expect($posNew)->not->toBeFalse();
    expect($posOld)->not->toBeFalse();
    expect($posNew)->toBeLessThan($posOld);
});

test('tag archive pages sort articles newest published first', function () {
    $tag = BlogTag::factory()->create([
        'name' => 'HANA Migration',
        'slug' => 'hana-migration',
    ]);

    $tagOld = Blog::factory()->published()->create([
        'title' => 'HANA Migration 2024 Retrospective',
        'published_at' => now()->subDays(30),
    ]);
    $tagNew = Blog::factory()->published()->create([
        'title' => 'HANA Migration 2025 Best Practices',
        'published_at' => now()->subMinutes(30),
    ]);

    $tagOld->tags()->attach($tag);
    $tagNew->tags()->attach($tag);

    $response = $this->get('/tag/hana-migration');

    $response->assertOk();
    $content = $response->getContent();

    $posNew = strpos($content, 'HANA Migration 2025 Best Practices');
    $posOld = strpos($content, 'HANA Migration 2024 Retrospective');

    expect($posNew)->not->toBeFalse();
    expect($posOld)->not->toBeFalse();
    expect($posNew)->toBeLessThan($posOld);
});

test('pagination preserves newest-first ordering across pages without duplication or skipping', function () {
    $blogs = collect();
    // Create 15 blogs with strictly decreasing published_at timestamps
    for ($i = 15; $i >= 1; $i--) {
        $padded = sprintf('%02d', $i);
        $blogs->push(Blog::factory()->published()->create([
            'title' => "Chronological Article Alpha-{$padded}",
            'published_at' => now()->subMinutes($i * 10),
        ]));
    }

    // Page 1 should have newest 12 grid items + featured item: Alpha-01 through Alpha-13
    $page1Response = $this->get('/blogs?page=1');
    $page1Response->assertOk();

    // Alpha-01 (sub 10 mins) is newest in featured card
    $page1Response->assertSee('Chronological Article Alpha-01');
    $page1Response->assertSee('Chronological Article Alpha-13');
    $page1Response->assertDontSee('Chronological Article Alpha-14');
    $page1Response->assertDontSee('Chronological Article Alpha-15');

    // Page 2 should have older remaining items: Alpha-14 through Alpha-15
    $page2Response = $this->get('/blogs?page=2');
    $page2Response->assertOk();

    $page2Response->assertSee('Chronological Article Alpha-14');
    $page2Response->assertSee('Chronological Article Alpha-15');
    $page2Response->assertDontSee('Chronological Article Alpha-01');
    $page2Response->assertDontSee('Chronological Article Alpha-13');
});

test('newest published blog appears in featured position and older article moves below it', function () {
    // 1. Older article from 2023 with a featured image
    $olderSdBlog = Blog::factory()->published()->create([
        'title' => 'SAP SD Power User Certificate',
        'slug' => 'sap-sd-power-user-certificate',
        'featured_image' => 'images/blogs/sap-sd-power.webp',
        'published_at' => '2023-07-03 10:00:00',
    ]);

    // 2. An intermediate article from 2024
    $interBlog = Blog::factory()->published()->create([
        'title' => 'SAP MM S/4 HANA Procurement Guide',
        'slug' => 'sap-mm-procurement-guide',
        'featured_image' => null,
        'published_at' => '2024-05-15 12:00:00',
    ]);

    // 3. Newly published blog today without an image
    $newFicoBlog = Blog::factory()->published()->create([
        'title' => 'SAP FICO Interview Questions & Answers',
        'slug' => 'sap-fico-interview-questions-answers',
        'featured_image' => null,
        'published_at' => now()->subMinutes(15),
    ]);

    $response = $this->get('/blogs');

    $response->assertOk();
    $content = $response->getContent();

    // Verify Featured Section renders the newly published FICO blog
    $response->assertSee('Featured Post');
    $response->assertSee('EDITORIAL HIGHLIGHT');

    // Positional verification:
    // Featured FICO post is at the very top (in section 3)
    // MM guide is in the grid next
    // SD Power User Certificate is in the grid below
    $posFico = strpos($content, 'SAP FICO Interview Questions &amp; Answers');
    if ($posFico === false) {
        $posFico = strpos($content, 'SAP FICO Interview Questions & Answers');
    }

    $posMm = strpos($content, 'SAP MM S/4 HANA Procurement Guide');
    $posSd = strpos($content, 'SAP SD Power User Certificate');

    expect($posFico)->not->toBeFalse();
    expect($posMm)->not->toBeFalse();
    expect($posSd)->not->toBeFalse();

    // FICO is featured top, MM is middle, SD is below
    expect($posFico)->toBeLessThan($posMm);
    expect($posMm)->toBeLessThan($posSd);
});

test('admin blog management sorts articles newest published first by default', function () {
    $admin = User::factory()->admin()->active()->create();

    $olderBlog = Blog::factory()->published()->create([
        'title' => 'Admin Test: Older Blog 2022',
        'published_at' => '2022-05-14 10:00:00',
    ]);

    $middleBlog = Blog::factory()->published()->create([
        'title' => 'Admin Test: Middle Blog 2024',
        'published_at' => '2024-01-10 10:00:00',
    ]);

    $newestBlog = Blog::factory()->published()->create([
        'title' => 'Admin Test: Newest Blog 2026',
        'published_at' => '2026-11-18 12:01:00',
    ]);

    // Touch the older blog to ensure updated_at does NOT affect sorting
    $olderBlog->touch();

    $response = $this->actingAs($admin)->get(route('admin.blogs.index'));
    $response->assertOk();

    $content = $response->getContent();
    $posNew = strpos($content, 'Admin Test: Newest Blog 2026');
    $posMid = strpos($content, 'Admin Test: Middle Blog 2024');
    $posOld = strpos($content, 'Admin Test: Older Blog 2022');

    expect($posNew)->not->toBeFalse();
    expect($posMid)->not->toBeFalse();
    expect($posOld)->not->toBeFalse();

    expect($posNew)->toBeLessThan($posMid);
    expect($posMid)->toBeLessThan($posOld);
});

test('admin editing old blog preserves publication date and position', function () {
    $admin = User::factory()->admin()->active()->create();

    $oldBlog = Blog::factory()->published()->create([
        'title' => 'Original Old Title 2021',
        'published_at' => '2021-03-01 10:00:00',
    ]);

    $newBlog = Blog::factory()->published()->create([
        'title' => 'Recent Article 2026',
        'published_at' => '2026-11-18 10:00:00',
    ]);

    // Update old blog without modifying published_at
    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $oldBlog), [
        'title' => 'Updated Old Title 2021',
        'status' => Blog::STATUS_PUBLISHED,
        'published_at' => '2021-03-01 10:00:00',
    ]);

    $response->assertRedirect();
    $oldBlog->refresh();

    // Verify published_at was preserved exactly
    expect($oldBlog->published_at->format('Y-m-d H:i:s'))->toBe('2021-03-01 10:00:00');

    // On /admin/blogs, Recent Article 2026 must still appear BEFORE Updated Old Title 2021
    $adminResponse = $this->actingAs($admin)->get(route('admin.blogs.index'));
    $content = $adminResponse->getContent();
    $posRecent = strpos($content, 'Recent Article 2026');
    $posOld = strpos($content, 'Updated Old Title 2021');

    expect($posRecent)->not->toBeFalse();
    expect($posOld)->not->toBeFalse();
    expect($posRecent)->toBeLessThan($posOld);
});
