<?php

use App\Models\Blog;
use App\Services\BlogContentProcessor;

test('blog content processor converts markdown headings and populates table of contents', function () {
    $markdown = <<<'MD'
# Complete Guide to SAP FICO

Here is the introduction to SAP FICO.

## What is SAP FICO?

SAP FICO stands for **Financial Accounting** and **Controlling**.

### Financial Accounting (FI) Overview

FI handles external statutory reporting.

### Controlling (CO) Overview

CO handles internal managerial reporting and cost centers.

## Configuration Steps in S/4 HANA

Follow these steps for baseline configuration.
MD;

    $processor = new BlogContentProcessor;
    $result = $processor->process($markdown, [], 'Complete Guide to SAP FICO');

    // Duplicate H1 stripped
    expect($result['content'])->not->toContain('<h1>Complete Guide to SAP FICO</h1>');
    expect($result['content'])->not->toContain('# Complete Guide to SAP FICO');

    // H2 and H3 converted with IDs
    expect($result['content'])->toContain('<h2');
    expect($result['content'])->toContain('id="what-is-sap-fico"');
    expect($result['content'])->toContain('<h3');
    expect($result['content'])->toContain('id="financial-accounting-fi-overview"');

    // TOC properly populated
    expect($result['toc'])->toHaveCount(4);
    expect($result['toc'][0]['title'])->toBe('What is SAP FICO?');
    expect($result['toc'][0]['level'])->toBe(2);
    expect($result['toc'][1]['title'])->toBe('Financial Accounting (FI) Overview');
    expect($result['toc'][1]['level'])->toBe(3);
    expect($result['toc'][2]['title'])->toBe('Controlling (CO) Overview');
    expect($result['toc'][2]['level'])->toBe(3);
    expect($result['toc'][3]['title'])->toBe('Configuration Steps in S/4 HANA');
    expect($result['toc'][3]['level'])->toBe(2);
});

test('markdown tables are converted and wrapped in responsive containers', function () {
    $markdown = <<<'MD'
## Comparison of Modules

| Module Code | Module Name | Key Function |
| --- | --- | --- |
| FI | Financials | Balance Sheet & P&L |
| CO | Controlling | Profit & Cost Centers |
| MM | Materials | Procurement & Inventory |
MD;

    $processor = new BlogContentProcessor;
    $result = $processor->process($markdown, [], 'Test Blog');

    expect($result['content'])->toContain('table-wrapper');
    expect($result['content'])->toContain('<table');
    expect($result['content'])->toContain('<th>Module Code</th>');
    expect($result['content'])->toContain('<td>Balance Sheet &amp; P&amp;L</td>');
});

test('lists blockquotes code blocks and formatting convert correctly', function () {
    $markdown = <<<'MD'
## Overview of Features

Here are key items:
- Bullet item one
- Bullet item two with **bold** and *italic*

Ordered steps:
1. First step
2. Second step

> This is an important enterprise note for consultants.

```sql
SELECT BUKRS, BUTXT FROM T001;
```
MD;

    $processor = new BlogContentProcessor;
    $result = $processor->process($markdown, [], 'Test Blog');

    expect($result['content'])->toContain('<ul>');
    expect($result['content'])->toContain('<li>Bullet item one</li>');
    expect($result['content'])->toContain('<strong>bold</strong>');
    expect($result['content'])->toContain('<em>italic</em>');
    expect($result['content'])->toContain('<ol>');
    expect($result['content'])->toContain('<li>First step</li>');
    expect($result['content'])->toContain('<blockquote>');
    expect($result['content'])->toContain('<pre><code');
    expect($result['content'])->toContain('SELECT BUKRS, BUTXT FROM T001;');
});

test('mixed markdown and html preserves existing html without double escaping', function () {
    $mixed = <<<'HTML'
<h2>Existing HTML Heading</h2>
<p>This is existing HTML with <strong>bold</strong> text.</p>

### Markdown Heading 3

Here is a list in markdown:
- Markdown item 1
- Markdown item 2

<div class="note-box">
    <p>Custom HTML div block</p>
</div>
HTML;

    $processor = new BlogContentProcessor;
    $result = $processor->process($mixed, [], 'Test Blog');

    expect($result['content'])->toContain('id="existing-html-heading"');
    expect($result['content'])->toContain('Existing HTML Heading');
    expect($result['content'])->toContain('<strong>bold</strong>');
    expect($result['content'])->toContain('<h3');
    expect($result['content'])->toContain('Markdown Heading 3');
    expect($result['content'])->toContain('<ul>');
    expect($result['content'])->toContain('<li>Markdown item 1</li>');
    expect($result['content'])->toContain('<div class="note-box">');
});

test('unsafe scripts and event handlers are sanitized', function () {
    $malicious = <<<'HTML'
## Safe Section
<script>alert("XSS Attack");</script>
<p onclick="alert('hack')">Click here</p>
<a href="javascript:alert(1)">Dangerous link</a>
<iframe src="http://evil.com"></iframe>
HTML;

    $processor = new BlogContentProcessor;
    $result = $processor->process($malicious, [], 'Test Blog');

    expect($result['content'])->not->toContain('<script>');
    expect($result['content'])->not->toContain('alert(');
    expect($result['content'])->not->toContain('onclick');
    expect($result['content'])->not->toContain('javascript:');
    expect($result['content'])->not->toContain('evil.com');
});

test('public blog detail page renders markdown blog with 3-column layout and toc', function () {
    $blog = Blog::factory()->published()->create([
        'title' => 'SAP FICO Complete Training Guide',
        'slug' => 'sap-fico-complete-training-guide',
        'content' => <<<'MD'
# SAP FICO Complete Training Guide

Introduction paragraph about SAP FICO consulting careers.

## Understanding Financial Accounting

SAP FI focuses on statutory financial records.

### General Ledger Configuration

Configuring chart of accounts and posting periods.

### Accounts Payable and Receivable

Managing vendor invoices and customer collections.

## Controlling Module Architecture

Cost center accounting and internal orders overview.
MD
    ]);

    $response = $this->get('/sap-fico-complete-training-guide');

    $response->assertOk();

    // Table of contents rendered on page
    $response->assertSee('Table of Contents');
    $response->assertSee('Understanding Financial Accounting');
    $response->assertSee('General Ledger Configuration');
    $response->assertSee('Controlling Module Architecture');

    // Raw markdown symbols are NOT visible as raw text
    $response->assertDontSee('# SAP FICO Complete Training Guide');
    $response->assertDontSee('## Understanding Financial Accounting');

    // Check 3-column components
    $response->assertSee('SHARE ARTICLE');
    $response->assertSee('NEED GUIDANCE?');
    $response->assertSee('Talk with an SAP Career Counselor');
});
