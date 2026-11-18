<?php

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;

test('blog detail renders hero with breadcrumbs, category pills, author, and reading time', function () {
    $category = BlogCategory::factory()->create([
        'name' => 'SAP HANA',
        'slug' => 'sap-hana',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'Top 20 SAP HANA Architecture Questions',
        'slug' => 'top-20-sap-hana-architecture-questions',
        'author_name' => 'Rajesh Sharma',
        'view_count' => 1250,
    ]);
    $blog->categories()->attach($category);

    $response = $this->get('/top-20-sap-hana-architecture-questions');

    $response->assertOk();
    $response->assertSee('Top 20 SAP HANA Architecture Questions');
    $response->assertSee('Rajesh Sharma');
    $response->assertSee('SAP HANA');
    $response->assertSee('1,251 views');
    $response->assertSee('Copy Article URL');
    $response->assertSee('Share on LinkedIn');
    $response->assertSee('Share on X');
    $response->assertSee('Share on WhatsApp');
});

test('blog detail dynamically generates table of contents and injects unique heading ids', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'sap-hana-core-interview-guide',
        'content' => '<h2>1. Core In-Memory Architecture</h2><p>Answer text</p><h2>2. Row Store vs Column Store</h2><p>More explanation</p><h3>2.1 Compression Methods</h3><p>Detailed compression answer</p>',
    ]);

    $response = $this->get('/sap-hana-core-interview-guide');

    $response->assertOk();
    $response->assertSee('Table of Contents');
    $response->assertSee('1. Core In-Memory Architecture');
    $response->assertSee('2. Row Store vs Column Store');
    $response->assertSee('2.1 Compression Methods');
    // Ensure IDs were injected with tabindex="-1"
    $response->assertSee('id="1-core-in-memory-architecture"', false);
    $response->assertSee('tabindex="-1"', false);
    $response->assertSee('#1-core-in-memory-architecture');
});

test('table of contents is hidden cleanly if article has fewer than two headings', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'short-sap-tip',
        'content' => '<h2>Single Topic</h2><p>Just a quick tip without multiple subheadings.</p>',
    ]);

    $response = $this->get('/short-sap-tip');

    $response->assertOk();
    $response->assertDontSee('Table of Contents');
});

test('blog detail wraps data tables in responsive accessible container', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'sap-tcodes-table-reference',
        'content' => '<p>Intro</p><table class="my-table"><thead><tr><th>T-Code</th><th>Module</th></tr></thead><tbody><tr><td>VA01</td><td>SD</td></tr></tbody></table>',
    ]);

    $response = $this->get('/sap-tcodes-table-reference');

    $response->assertOk();
    $response->assertSee('table-wrapper');
    $response->assertSee('role="region"', false);
    $response->assertSee('tabindex="0"', false);
    $response->assertSee('VA01');
});

test('blog detail displays high-converting zeblearn course conversion section', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'sap-career-guide-2026',
    ]);

    $response = $this->get('/sap-career-guide-2026');

    $response->assertOk();
    $response->assertSee('Ready to Turn SAP Theory into Enterprise Practice?');
    $response->assertSee('Book Free Demo Class');
    $response->assertSee('Explore All Courses');
    $response->assertSee('24/7 Sandbox Practice');
    $response->assertSee('Placement Support');
});

test('related articles display 3 to 4 cards with reading time and branded fallback when image is missing', function () {
    $category = BlogCategory::factory()->create(['name' => 'SAP Finance', 'slug' => 'sap-finance']);

    $mainBlog = Blog::factory()->published()->create(['slug' => 'sap-fico-main']);
    $mainBlog->categories()->attach($category);

    $relatedA = Blog::factory()->published()->create(['title' => 'SAP General Ledger Masterclass', 'slug' => 'sap-gl-masterclass', 'featured_image' => null]);
    $relatedB = Blog::factory()->published()->create(['title' => 'SAP Accounts Payable Setup', 'slug' => 'sap-ap-setup', 'featured_image' => null]);
    $relatedC = Blog::factory()->published()->create(['title' => 'SAP Asset Accounting Guide', 'slug' => 'sap-aa-guide', 'featured_image' => null]);
    $relatedA->categories()->attach($category);
    $relatedB->categories()->attach($category);
    $relatedC->categories()->attach($category);

    $response = $this->get('/sap-fico-main');

    $response->assertOk();
    $response->assertSee('Related Articles &amp; Study Notes', false);
    $response->assertSee('SAP General Ledger Masterclass');
    $response->assertSee('SAP Accounts Payable Setup');
    $response->assertSee('SAP Asset Accounting Guide');
    $response->assertSee('View All Articles');
});

test('blog detail renders meta keywords tag when meta_keywords is explicitly provided', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'sap-abap-syntax-guide',
        'meta_keywords' => 'sap abap, abap 7.5 syntax, sap cds views',
    ]);

    $response = $this->get('/sap-abap-syntax-guide');

    $response->assertOk();
    $response->assertSee('name="keywords"', false);
    $response->assertSee('content="sap abap, abap 7.5 syntax, sap cds views"', false);
});

test('blog detail renders meta keywords falling back to tags when meta_keywords is null', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'sap-basis-daily-tasks',
        'meta_keywords' => null,
        'focus_keyword' => 'sap basis administration',
    ]);

    $tag1 = BlogTag::factory()->create(['name' => 'SAP Basis', 'slug' => 'sap-basis']);
    $tag2 = BlogTag::factory()->create(['name' => 'System Monitoring', 'slug' => 'system-monitoring']);
    $blog->tags()->attach([$tag1->id, $tag2->id]);

    $response = $this->get('/sap-basis-daily-tasks');

    $response->assertOk();
    $response->assertSee('name="keywords"', false);
    $response->assertSee('content="SAP Basis, System Monitoring"', false);
});
