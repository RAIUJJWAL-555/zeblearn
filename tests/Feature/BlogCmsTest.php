<?php

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use App\Services\BlogSanitizerService;

test('guests are redirected from admin blog management', function () {
    $response = $this->get(route('admin.blogs.dashboard'));
    $response->assertRedirect(route('login'));

    $response = $this->get(route('admin.blogs.index'));
    $response->assertRedirect(route('login'));
});

test('admin can access blog dashboard with summary statistics', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    Blog::factory()->published()->count(3)->create();
    Blog::factory()->draft()->count(2)->create();
    Blog::factory()->incomplete()->count(1)->create();

    $response = $this->actingAs($admin)->get(route('admin.blogs.dashboard'));

    $response->assertOk();
    $response->assertSee('Blog Overview &amp; Content Hub', false);
    $response->assertViewHas('stats');
    $response->assertViewHas('recentlyUpdated');
});

test('admin can view blog listing with search and status filter', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $blogA = Blog::factory()->published()->create([
        'title' => 'SAP S4 HANA Finance Complete Guide',
        'slug' => 'sap-s4-hana-finance-guide',
    ]);
    $blogB = Blog::factory()->draft()->create([
        'title' => 'SAP Material Management Procurement Flow',
        'slug' => 'sap-mm-procurement-flow',
    ]);

    // Search query
    $searchResponse = $this->actingAs($admin)->get(route('admin.blogs.index', ['search' => 'HANA']));
    $searchResponse->assertOk();
    $searchResponse->assertSee('SAP S4 HANA Finance Complete Guide');
    $searchResponse->assertDontSee('SAP Material Management Procurement Flow');

    // Filter by status
    $statusResponse = $this->actingAs($admin)->get(route('admin.blogs.index', ['status' => 'draft']));
    $statusResponse->assertOk();
    $statusResponse->assertSee('SAP Material Management Procurement Flow');
    $statusResponse->assertDontSee('SAP S4 HANA Finance Complete Guide');
});

test('admin can create a new blog with seo fields, category, and tags', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $category = BlogCategory::factory()->create(['name' => 'SAP Blogs', 'slug' => 'sap-blogs']);
    $tag = BlogTag::factory()->create(['name' => 'SAP FICO', 'slug' => 'sap-fico']);

    $payload = [
        'title' => 'How to Master SAP FICO Configuration in 2026',
        'slug' => 'master-sap-fico-configuration-2026',
        'excerpt' => 'A step-by-step introduction to General Ledger and Accounts Payable setup.',
        'content' => '<h2>Introduction</h2><p>Here is full sanitized article content.</p>',
        'status' => 'published',
        'published_at' => now()->format('Y-m-d\TH:i'),
        'author_name' => 'Senior SAP Architect',
        'seo_title' => 'Master SAP FICO Configuration (2026 Guide)',
        'meta_description' => 'Comprehensive enterprise overview of SAP FICO ledger structures.',
        'meta_keywords' => 'sap fico, general ledger, accounts payable, sap erp',
        'focus_keyword' => 'SAP FICO configuration',
        'schema_type' => 'BlogPosting',
        'categories' => [$category->id],
        'tags' => [$tag->id],
    ];

    $response = $this->actingAs($admin)->post(route('admin.blogs.store'), $payload);

    $blog = Blog::where('slug', 'master-sap-fico-configuration-2026')->first();
    expect($blog)->not->toBeNull()
        ->and($blog->title)->toBe('How to Master SAP FICO Configuration in 2026')
        ->and($blog->status)->toBe('published')
        ->and($blog->meta_keywords)->toBe('sap fico, general ledger, accounts payable, sap erp')
        ->and($blog->categories->first()->id)->toBe($category->id)
        ->and($blog->tags->first()->id)->toBe($tag->id);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $response->assertSessionHas('success');
});

test('admin can edit and update a blog', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->draft()->create([
        'title' => 'Original Draft Title',
        'slug' => 'original-draft-title',
    ]);

    $editResponse = $this->actingAs($admin)->get(route('admin.blogs.edit', $blog));
    $editResponse->assertOk();
    $editResponse->assertSee('Original Draft Title');

    $updateResponse = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => 'Updated Published Title',
        'slug' => 'updated-published-title',
        'excerpt' => 'Updated short summary.',
        'content' => '<p>Updated full body content.</p>',
        'status' => 'published',
        'author_name' => 'Lead Consultant',
    ]);

    $updateResponse->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();
    expect($blog->title)->toBe('Updated Published Title')
        ->and($blog->slug)->toBe('updated-published-title')
        ->and($blog->status)->toBe('published');
});

test('admin can quickly toggle publish and unpublish status', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->draft()->create();

    // Publish
    $publishResponse = $this->actingAs($admin)->patch(route('admin.blogs.publish', $blog));
    $publishResponse->assertRedirect();
    $blog->refresh();
    expect($blog->status)->toBe(Blog::STATUS_PUBLISHED)
        ->and($blog->published_at)->not->toBeNull();

    // Unpublish
    $unpublishResponse = $this->actingAs($admin)->patch(route('admin.blogs.unpublish', $blog));
    $unpublishResponse->assertRedirect();
    $blog->refresh();
    expect($blog->status)->toBe(Blog::STATUS_DRAFT);
});

test('admin can delete a blog', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->create();

    $response = $this->actingAs($admin)->delete(route('admin.blogs.destroy', $blog));
    $response->assertRedirect(route('admin.blogs.index'));

    expect(Blog::find($blog->id))->toBeNull();
});

test('admin can manage blog categories and tags', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    // Create Category
    $catResponse = $this->actingAs($admin)->post(route('admin.blogs.categories.store'), [
        'name' => 'Cloud ERP Insights',
        'slug' => 'cloud-erp-insights',
        'description' => 'Articles covering S/4HANA Public and Private Cloud.',
    ]);
    $catResponse->assertRedirect(route('admin.blogs.categories.index'));
    expect(BlogCategory::where('slug', 'cloud-erp-insights')->exists())->toBeTrue();

    // Create Tag
    $tagResponse = $this->actingAs($admin)->post(route('admin.blogs.tags.store'), [
        'name' => 'ABAP on Cloud',
        'slug' => 'abap-on-cloud',
    ]);
    $tagResponse->assertRedirect(route('admin.blogs.tags.index'));
    expect(BlogTag::where('slug', 'abap-on-cloud')->exists())->toBeTrue();
});

test('admin can view legacy migration dashboard and audit logs', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $response = $this->actingAs($admin)->get(route('admin.blogs.migration.index'));
    $response->assertOk();
    $response->assertSee('Legacy WordPress Blog Migration');

    $logsResponse = $this->actingAs($admin)->get(route('admin.blogs.migration.logs'));
    $logsResponse->assertOk();
    $logsResponse->assertSee('Migration Audit Logs');
});

test('guests can browse public blog directory and filter by search', function () {
    $publishedBlog = Blog::factory()->published()->create([
        'title' => 'Top 25 SAP SD Interview Questions and Answers',
        'slug' => 'top-25-sap-sd-interview-questions',
    ]);

    $draftBlog = Blog::factory()->draft()->create([
        'title' => 'Secret Unpublished Draft on SAP SuccessFactors',
        'slug' => 'secret-draft-sf',
    ]);

    $response = $this->get(route('blogs'));
    $response->assertOk();
    $response->assertSee('Top 25 SAP SD Interview Questions and Answers');
    $response->assertDontSee('Secret Unpublished Draft on SAP SuccessFactors');

    // Search query
    $searchResponse = $this->get(route('blogs', ['search' => 'Interview']));
    $searchResponse->assertOk();
    $searchResponse->assertSee('Top 25 SAP SD Interview Questions and Answers');
});

test('guests can view category archive page at legacy path', function () {
    $category = BlogCategory::factory()->create([
        'name' => 'SAP Blogs',
        'slug' => 'sap-blogs',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'Comprehensive Guide to SAP MM Enterprise Structure',
    ]);
    $blog->categories()->attach($category);

    // Exact legacy URL: /category/sap-blogs/
    $response = $this->get('/category/sap-blogs');
    $response->assertOk();
    $response->assertSee('SAP Blogs');
    $response->assertSee('Comprehensive Guide to SAP MM Enterprise Structure');
});

test('guests can view published blog at exact legacy root path', function () {
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP SD Power User Certification Guide',
        'slug' => 'sap-sd-power-user-certificate',
        'content' => '<p>Complete step-by-step syllabus and real exam practice.</p>',
    ]);

    // Exact legacy URL: /sap-sd-power-user-certificate/
    $response = $this->get('/sap-sd-power-user-certificate');
    $response->assertOk();
    $response->assertSee('SAP SD Power User Certification Guide');
    $response->assertSee('Complete step-by-step syllabus and real exam practice.');
    $response->assertSee('"@type": "BlogPosting"', false);
});

test('guests receive 404 for draft blogs but admins can preview', function () {
    $draftBlog = Blog::factory()->draft()->create([
        'title' => 'WIP Draft Article',
        'slug' => 'wip-draft-article',
    ]);

    // Guest gets 404
    $guestResponse = $this->get('/wip-draft-article');
    $guestResponse->assertNotFound();

    // Authenticated Admin can view preview
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $adminResponse = $this->actingAs($admin)->get('/wip-draft-article');
    $adminResponse->assertOk();
    $adminResponse->assertSee('WIP Draft Article');
    $adminResponse->assertSee('Admin Article Preview Mode');
});

test('sitemap xml includes published blogs and excludes draft blogs', function () {
    $publishedBlog = Blog::factory()->published()->create([
        'title' => 'Published Article For Sitemap',
        'slug' => 'published-article-for-sitemap',
    ]);

    $draftBlog = Blog::factory()->draft()->create([
        'title' => 'Draft Article Not In Sitemap',
        'slug' => 'draft-article-not-in-sitemap',
    ]);

    $response = $this->get('/sitemap.xml');
    $response->assertOk();
    $response->assertSee('/published-article-for-sitemap');
    $response->assertDontSee('/draft-article-not-in-sitemap');
});

test('prefix route blogs slash slug issues 301 permanent redirect to canonical legacy path', function () {
    $blog = Blog::factory()->published()->create([
        'slug' => 'sap-co-configuration-guide',
    ]);

    $response = $this->get('/blogs/sap-co-configuration-guide');
    $response->assertRedirect('/sap-co-configuration-guide');
    $response->assertStatus(301);
});

test('effective canonical url sanitizes foreign and staging domains', function () {
    $blog = Blog::factory()->create([
        'slug' => 'sap-staging-test-article',
        'canonical_url' => 'https://test.techavera.com/sap-staging-test-article/',
    ]);

    expect($blog->effective_canonical_url)->toBe(url('/sap-staging-test-article'));
});

test('blog sanitizer service aggressively strips xss vectors and upgrades insecure http', function () {
    $sanitizer = new BlogSanitizerService;

    $xssInput = '<script>alert("hack")</script><p>Hello <img src="x" onerror="alert(1)"> world</p><a href="javascript:alert(1)">link</a><iframe src="https://evil.com"></iframe><img src="http://sap.zeblearn.com/assets/pic.jpg">';
    $cleaned = $sanitizer->sanitize($xssInput);

    expect($cleaned)->not->toContain('<script>')
        ->and($cleaned)->not->toContain('onerror')
        ->and($cleaned)->not->toContain('javascript:')
        ->and($cleaned)->not->toContain('evil.com')
        ->and($cleaned)->toContain('https://sap.zeblearn.com/assets/pic.jpg')
        ->and($cleaned)->toContain('<p>Hello');
});
