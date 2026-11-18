<?php

use App\Models\Blog;
use App\Models\User;

test('admin blog edit and create forms render editable live preview container with contenteditable and state indicators', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP S/4HANA Finance Guide',
        'content' => '<h2>Introduction</h2><p>Existing overview paragraph.</p>',
    ]);

    $editResponse = $this->actingAs($admin)->get(route('admin.blogs.edit', $blog));

    $editResponse->assertOk();
    $editResponse->assertSee('contenteditable="true"', false);
    $editResponse->assertSee('x-ref="previewArea"', false);
    $editResponse->assertSee('x-ref="contentArea"', false);
    $editResponse->assertSee('switchToPreview()', false);
    $editResponse->assertSee('switchToWrite()', false);
    $editResponse->assertSee('onPreviewInput()', false);
    $editResponse->assertSee('handlePreviewClick($event)', false);
    $editResponse->assertSee('Unsaved Edits', false);
    $editResponse->assertSee('Visual In-Place Editing Active', false);

    $createResponse = $this->actingAs($admin)->get(route('admin.blogs.create'));
    $createResponse->assertOk();
    $createResponse->assertSee('contenteditable="true"', false);
    $createResponse->assertSee('x-ref="previewArea"', false);
});

test('simple article edits from live preview update and save successfully', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP BTP Overview',
        'content' => '<h2>1. What is BTP?</h2><p>Original paragraph description.</p>',
    ]);

    $editedHtml = '<h2>1. What is SAP Business Technology Platform?</h2><p>Updated in-place paragraph with revised enterprise insights.</p><p>Newly inserted second paragraph.</p>';

    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => 'SAP BTP Overview',
        'slug' => $blog->slug,
        'content' => $editedHtml,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();

    expect($blog->content)->toContain('<h2>1. What is SAP Business Technology Platform?</h2>')
        ->and($blog->content)->toContain('Updated in-place paragraph with revised enterprise insights.')
        ->and($blog->content)->toContain('<p>Newly inserted second paragraph.</p>');
});

test('editing a long recovered wordpress article preserves complex structure, headings, and classes', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP EWM Architecture',
        'content' => '<div class="wp-block-group"><h2 class="wp-block-heading">Core Components</h2><p>Overview of decentralized EWM.</p><blockquote class="wp-block-quote"><p>Best practices for RF gun integration.</p></blockquote><pre class="wp-block-code"><code class="language-sql">SELECT * FROM /SCWM/AQUA;</code></pre></div>',
    ]);

    $editedLongHtml = '<div class="wp-block-group"><h2 class="wp-block-heading">Core Components (2026 Edition)</h2><p>Overview of decentralized EWM with S/4HANA 2023.</p><blockquote class="wp-block-quote"><p>Updated best practices for modern RF scanning.</p></blockquote><pre class="wp-block-code"><code class="language-sql">SELECT * FROM /SCWM/AQUA WHERE LGNUM = \'WM01\';</code></pre></div>';

    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => 'SAP EWM Architecture',
        'slug' => $blog->slug,
        'content' => $editedLongHtml,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();

    expect($blog->content)->toContain('class="wp-block-group"')
        ->and($blog->content)->toContain('class="wp-block-heading"')
        ->and($blog->content)->toContain('Core Components (2026 Edition)')
        ->and($blog->content)->toContain('class="wp-block-quote"')
        ->and($blog->content)->toContain('Updated best practices for modern RF scanning.')
        ->and($blog->content)->toContain('SELECT * FROM /SCWM/AQUA WHERE LGNUM = \'WM01\';');
});

test('editing an article with nested lists preserves list hierarchies and items', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP FICO Ledger Hierarchy',
    ]);

    $nestedListHtml = '<h2>Ledger Configuration Steps</h2><ul><li>Leading Ledger (0L)<ul><li>Standard fiscal year variant</li><li>Currency 10: Local company code currency</li><li>Currency 30: Group currency</li></ul></li><li>Non-Leading Ledgers (2L, 3L)<ul><li>IFRS accounting principles</li><li>Alternative fiscal periods</li></ul></li></ul>';

    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => $blog->title,
        'slug' => $blog->slug,
        'content' => $nestedListHtml,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();

    expect($blog->content)->toContain('<ul><li>Leading Ledger (0L)<ul><li>Standard fiscal year variant')
        ->and($blog->content)->toContain('<li>Currency 30: Group currency</li></ul></li>')
        ->and($blog->content)->toContain('<li>Non-Leading Ledgers (2L, 3L)<ul><li>IFRS accounting principles');
});

test('editing an article with tables preserves table headers, rows, columns, and data cells', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP Transaction Codes Comparison',
    ]);

    $tableHtml = '<h2>Top Logistics T-Codes</h2><table class="w-full border-collapse border border-slate-300"><thead><tr class="bg-slate-100"><th class="border border-slate-300 p-2">T-Code</th><th class="border border-slate-300 p-2">Module</th><th class="border border-slate-300 p-2">Function</th></tr></thead><tbody><tr><td class="border border-slate-300 p-2">ME21N</td><td class="border border-slate-300 p-2">MM</td><td class="border border-slate-300 p-2">Create Purchase Order</td></tr><tr><td class="border border-slate-300 p-2">MIGO</td><td class="border border-slate-300 p-2">MM / IM</td><td class="border border-slate-300 p-2">Goods Movement &amp; Receipts</td></tr><tr><td class="border border-slate-300 p-2">VL01N</td><td class="border border-slate-300 p-2">SD / LE</td><td class="border border-slate-300 p-2">Outbound Delivery with Order</td></tr></tbody></table>';

    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => $blog->title,
        'slug' => $blog->slug,
        'content' => $tableHtml,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();

    expect($blog->content)->toContain('<table class="w-full border-collapse border border-slate-300">')
        ->and($blog->content)->toContain('<thead><tr class="bg-slate-100">')
        ->and($blog->content)->toContain('<th class="border border-slate-300 p-2">T-Code</th>')
        ->and($blog->content)->toContain('<td class="border border-slate-300 p-2">ME21N</td>')
        ->and($blog->content)->toContain('<td class="border border-slate-300 p-2">MIGO</td>')
        ->and($blog->content)->toContain('<td class="border border-slate-300 p-2">VL01N</td>');
});

test('editing an article with images and links preserves attributes safely', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP Integration Landscape Diagram',
    ]);

    $richHtml = '<p>Check the architectural diagram below:</p><figure><img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71" alt="SAP Architecture Diagram" class="w-full rounded-2xl shadow-md" /><figcaption>Figure 1: High Level Integration Map</figcaption></figure><p>Visit our <a href="https://zeblearn.com/courses" class="text-orange-500 font-bold" target="_blank">Enterprise SAP Courses</a> for live system access.</p>';

    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => $blog->title,
        'slug' => $blog->slug,
        'content' => $richHtml,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();

    expect($blog->content)->toContain('src="https://images.unsplash.com/photo-1551288049-bebda4e38f71"')
        ->and($blog->content)->toContain('alt="SAP Architecture Diagram"')
        ->and($blog->content)->toContain('class="w-full rounded-2xl shadow-md"')
        ->and($blog->content)->toContain('<figcaption>Figure 1: High Level Integration Map</figcaption>')
        ->and($blog->content)->toContain('href="https://zeblearn.com/courses"');
});

test('malicious scripts and event handlers inserted in live preview are stripped safely by sanitizer', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $blog = Blog::factory()->published()->create([
        'title' => 'Security Audit Test Article',
    ]);

    $dirtyHtml = '<h2>Clean Section</h2><script>alert("hacked")</script><p onclick="doBadThing()">Clean readable paragraph text.</p><a href="javascript:stealCookie()">Dangerous link</a>';

    $response = $this->actingAs($admin)->put(route('admin.blogs.update', $blog), [
        'title' => $blog->title,
        'slug' => $blog->slug,
        'content' => $dirtyHtml,
        'status' => 'published',
    ]);

    $response->assertRedirect(route('admin.blogs.edit', $blog));
    $blog->refresh();

    expect($blog->content)->not->toContain('<script>')
        ->and($blog->content)->not->toContain('alert("hacked")')
        ->and($blog->content)->not->toContain('onclick')
        ->and($blog->content)->not->toContain('javascript:stealCookie')
        ->and($blog->content)->toContain('<h2>Clean Section</h2>')
        ->and($blog->content)->toContain('<p>Clean readable paragraph text.</p>');
});
