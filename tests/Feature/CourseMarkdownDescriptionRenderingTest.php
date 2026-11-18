<?php

use App\Models\Course;
use App\Services\MarkdownSanitizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('markdown sanitizer safely converts markdown to formatted html', function () {
    $service = app(MarkdownSanitizerService::class);

    $markdown = <<<'MARKDOWN'
# Main Heading Should Become H2

This is **bold** text and *italic* text.

## Section Two

- Item 1
- Item 2

1. First
2. Second

<script>alert('xss')</script>
[Safe Link](https://zeblearn.com)
MARKDOWN;

    $html = $service->toHtml($markdown);

    // Single H1 protection: H1 replaced by H2
    expect($html)->not->toContain('<h1')
        ->and($html)->toContain('<h2')
        ->and($html)->toContain('<strong>bold</strong>')
        ->and($html)->toContain('<em>italic</em>')
        ->and($html)->toContain('<ul>')
        ->and($html)->toContain('<li>Item 1</li>')
        ->and($html)->toContain('<ol>')
        ->and($html)->toContain('<li>First</li>')
        ->and($html)->not->toContain('<script')
        ->and($html)->not->toContain('alert(')
        ->and($html)->toContain('href="https://zeblearn.com"')
        ->and($html)->toContain('rel="noopener noreferrer"');
});

test('course model provides rendered_description and clean_hero_description', function () {
    $markdown = <<<'MARKDOWN'
# SAP FICO Training Online – Master Financial Accounting & Controlling

Build practical expertise in SAP Financial Accounting (FI) and Controlling (CO).

## What You Will Learn

### 1. SAP FI – Financial Accounting

- Enterprise Structure and Financial Organisation
- Company and Company Code Configuration
MARKDOWN;

    $course = Course::factory()->create([
        'name' => 'SAP FICO Training',
        'slug' => 'sap-fico-training',
        'description' => $markdown,
        'hero_description' => null,
        'published_at' => now()->subDay(),
    ]);

    expect($course->rendered_description)->toContain('<h2')
        ->and($course->rendered_description)->toContain('What You Will Learn')
        ->and($course->rendered_description)->toContain('<h3')
        ->and($course->rendered_description)->toContain('SAP FI – Financial Accounting')
        ->and($course->rendered_description)->toContain('<li>Enterprise Structure and Financial Organisation</li>')
        ->and($course->rendered_description)->not->toContain('## ')
        ->and($course->rendered_description)->not->toContain('### ');

    expect($course->clean_hero_description)->toBeString()
        ->and($course->clean_hero_description)->not->toContain('##')
        ->and($course->clean_hero_description)->not->toContain('**');
});

test('public course landing page renders formatted markdown description without literal tokens', function () {
    $markdown = <<<'MARKDOWN'
# SAP FICO Training Online – Master Financial Accounting & Controlling

Build practical expertise in SAP Financial Accounting (FI) and Controlling (CO).

## What You Will Learn

### 1. SAP FI – Financial Accounting

- Enterprise Structure and Financial Organisation
MARKDOWN;

    $course = Course::factory()->create([
        'name' => 'SAP FICO Training',
        'slug' => 'sap-fico-training',
        'description' => $markdown,
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get('/courses/sap-fico-training');

    $response->assertOk();
    $content = $response->getContent();

    expect($content)->toContain('course-prose')
        ->and($content)->toContain('What You Will Learn')
        ->and($content)->toContain('SAP FI – Financial Accounting')
        ->and($content)->toContain('Enterprise Structure and Financial Organisation');

    // Confirm raw markdown symbols aren't rendered literally in the course overview
    expect($content)->not->toContain('## What You Will Learn')
        ->and($content)->not->toContain('### 1. SAP FI');
});
