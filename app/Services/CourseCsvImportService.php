<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\TrustItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CourseCsvImportService
{
    /**
     * Complete list of CSV columns with metadata, required status, and form mapping.
     */
    public const COLUMNS = [
        // A. Course Identity
        'course_title' => [
            'label' => 'Course Name / Title',
            'required' => true,
            'section' => 'Course Identity',
            'type' => 'string',
            'description' => 'Full official course title (e.g. SAP MM Training – Materials Management).',
            'example' => 'SAP MM Training – Materials Management',
        ],
        'course_slug' => [
            'label' => 'URL Slug',
            'required' => false,
            'section' => 'Course Identity',
            'type' => 'string',
            'description' => 'URL slug. Auto-generated from title if left empty.',
            'example' => 'sap-mm-training',
        ],
        'subject_category' => [
            'label' => 'Subject Category',
            'required' => true,
            'section' => 'Course Identity',
            'type' => 'string',
            'description' => 'Must match an existing category slug (e.g. sap-erp, workday, salesforce, data-science-analytics, cloud-computing).',
            'example' => 'sap-erp',
        ],
        'lifecycle_status' => [
            'label' => 'Lifecycle Status',
            'required' => true,
            'section' => 'Course Identity',
            'type' => 'enum',
            'accepted_values' => ['available', 'upcoming', 'planned'],
            'description' => 'available = Open for admission, upcoming = Advance booking, planned = In development.',
            'example' => 'available',
        ],
        'display_sort_order' => [
            'label' => 'Display Sort Order',
            'required' => false,
            'section' => 'Course Identity',
            'type' => 'integer',
            'description' => 'Integer sorting order in course catalog (0 = top).',
            'example' => '0',
        ],

        // B. Tuition & Delivery Logistics
        'effective_fee' => [
            'label' => 'Effective Fee (₹)',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'integer',
            'description' => 'Discounted tuition fee in INR (e.g. 25000).',
            'example' => '25000',
        ],
        'original_fee' => [
            'label' => 'Original Fee (₹)',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'integer',
            'description' => 'Standard fee before discount in INR (e.g. 65000).',
            'example' => '65000',
        ],
        'discount_rate' => [
            'label' => 'Discount Rate (%)',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'integer',
            'description' => 'Discount percentage (0-100). Importer validates against original and effective fee.',
            'example' => '62',
        ],
        'total_modules' => [
            'label' => 'Total Modules Count',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'integer',
            'description' => 'Nominal modules count displayed on course card (e.g. 12).',
            'example' => '12',
        ],
        'estimated_duration' => [
            'label' => 'Estimated Duration',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'string',
            'description' => 'Duration text (e.g. "3 Months", "6 to 9 Months").',
            'example' => '3 Months',
        ],
        'delivery_methodology' => [
            'label' => 'Delivery Methodology',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'string',
            'description' => 'Mode of instruction (e.g. "Live Online + Real System Practice").',
            'example' => 'Live Online + Real System Practice',
        ],
        'tag_badge' => [
            'label' => 'Tag Badge',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'string',
            'description' => 'Card badge tag (e.g. "HOT", "BESTSELLER", "ENTERPRISE").',
            'example' => 'HOT',
        ],
        'banner_label' => [
            'label' => 'Banner Label',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'string',
            'description' => 'Hero banner label (e.g. "AVAILABLE NOW", "NEW BATCH").',
            'example' => 'AVAILABLE NOW',
        ],
        'featured' => [
            'label' => 'Featured Course',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'boolean',
            'description' => 'true or false (or 1/0). Display in featured showcase on homepage.',
            'example' => 'true',
        ],
        'is_published' => [
            'label' => 'Published Live Online',
            'required' => false,
            'section' => 'Tuition & Delivery Logistics',
            'type' => 'boolean',
            'description' => 'true or false (or 1/0). Set published_at timestamp immediately.',
            'example' => 'true',
        ],

        // C. Syllabus & Curriculum Content
        'card_short_description' => [
            'label' => 'Card Short Description',
            'required' => true,
            'section' => 'Syllabus & Curriculum Content',
            'type' => 'string',
            'description' => 'Concise 2-sentence summary displayed on course catalog cards and search previews.',
            'example' => 'Master SAP MM procurement, inventory management, physical inventory, invoice verification and integration with FI/SD.',
        ],
        'detailed_course_overview' => [
            'label' => 'Detailed Course Overview (Markdown)',
            'required' => false,
            'section' => 'Syllabus & Curriculum Content',
            'type' => 'markdown',
            'description' => 'Comprehensive course overview supporting Markdown (H2, H3, bold, lists, links, paragraphs). Rendered safely as formatted HTML on landing page.',
            'example' => "## What You Will Learn\n\nBuild end-to-end expertise in SAP Materials Management.\n\n### Key Competencies\n- Procurement & Purchase Requisition\n- Inventory Management & Movement Types\n- Material Valuation & Account Determination",
        ],
        'curriculum_highlights' => [
            'label' => 'Curriculum Highlights',
            'required' => false,
            'section' => 'Syllabus & Curriculum Content',
            'type' => 'list',
            'description' => 'Key bullet points (newline-separated or pipe-separated or JSON array of strings).',
            'example' => "Procure-to-Pay (P2P) Lifecycle\nInventory & Goods Movements\nVendor Master & Purchase Orders\n24/7 Dedicated Server Access",
        ],

        // D. Upcoming Live Cohorts & Batches
        'cohorts_json' => [
            'label' => 'Upcoming Cohorts (JSON)',
            'required' => false,
            'section' => 'Upcoming Live Cohorts & Batches',
            'type' => 'json_array',
            'description' => 'JSON array of cohort objects: [{"name":"Weekend Cohort #1","start_date":"2026-12-05","end_date":"2027-03-05","timings":"08:00 AM – 10:00 AM IST","days":"Sat & Sun (Weekend)","delivery_mode":"Live Online","total_seats":25,"available_seats":8,"status":"filling_fast","is_active":true}]',
            'example' => '[{"name":"Weekend Executive Cohort #1","start_date":"2026-12-05","end_date":"2027-03-05","timings":"08:00 AM – 10:00 AM IST","days":"Sat & Sun (Weekend)","delivery_mode":"Live Online","total_seats":25,"available_seats":8,"status":"filling_fast","is_active":true}]',
        ],

        // E. Frequently Asked Questions
        'faqs_json' => [
            'label' => 'Course FAQs (JSON)',
            'required' => false,
            'section' => 'Frequently Asked Questions',
            'type' => 'json_array',
            'description' => 'JSON array of FAQ objects: [{"question":"Is 24/7 server access provided?","answer":"Yes, full sandbox credentials are provided for the entire 3 months.","sort_order":1,"is_active":true}]',
            'example' => '[{"question":"Is 24/7 server access provided?","answer":"Yes, full sandbox credentials are provided for the entire 3 months.","sort_order":1,"is_active":true},{"question":"Is placement assistance included?","answer":"Yes, placement preparation, mock interviews, and resume building are included.","sort_order":2,"is_active":true}]',
        ],

        // F. SEO Metadata
        'seo_meta_title' => [
            'label' => 'SEO Meta Title',
            'required' => false,
            'section' => 'SEO Metadata',
            'type' => 'string',
            'description' => 'Target SEO title for Google SERPs (under 60 characters recommended).',
            'example' => 'SAP MM Training Online with Live Classes & Placement | ZebLearn',
        ],
        'seo_meta_description' => [
            'label' => 'SEO Meta Description',
            'required' => false,
            'section' => 'SEO Metadata',
            'type' => 'string',
            'description' => 'Target SEO meta description for SERPs (under 160 characters recommended).',
            'example' => 'Learn SAP Materials Management (MM) online with live sessions, 24/7 system sandbox access, practical P2P projects, and verified placement support.',
        ],

        // G. Trust & Proof of Work CMS Controls
        'inherit_institutional_proof' => [
            'label' => 'Inherit Institutional Proof',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'boolean',
            'description' => 'true/false (default true). Automatically inherits global hiring partners, accreditation certificates, and institutional awards without duplicating data entry.',
            'example' => 'true',
        ],
        'placements_limit' => [
            'label' => 'Student Placements Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum placement records to display on course page (1-50, default 6).',
            'example' => '6',
        ],
        'testimonials_limit' => [
            'label' => 'Student Testimonials Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum student testimonials to display (1-50, default 6).',
            'example' => '6',
        ],
        'hiring_partners_limit' => [
            'label' => 'Hiring Partners Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum partner logos to display (1-50, default 8).',
            'example' => '8',
        ],
        'expert_faculty_limit' => [
            'label' => 'Expert Faculty Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum faculty mentor cards to display (1-50, default 3).',
            'example' => '3',
        ],
        'certification_badges_limit' => [
            'label' => 'Certification Badges Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum certification badges to display (1-50, default 6).',
            'example' => '6',
        ],
        'capstone_projects_limit' => [
            'label' => 'Capstone Projects Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum enterprise capstone projects to display (1-50, default 6).',
            'example' => '6',
        ],
        'video_stories_limit' => [
            'label' => 'Video Stories Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum student video stories to display (1-50, default 3).',
            'example' => '3',
        ],
        'awards_recognitions_limit' => [
            'label' => 'Awards & Recognitions Limit',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'integer',
            'description' => 'Maximum awards & recognitions to display (1-50, default 3).',
            'example' => '3',
        ],
        'selected_proof_ids_json' => [
            'label' => 'Selected Proof Item IDs (JSON)',
            'required' => false,
            'section' => 'Trust & Proof CMS Controls',
            'type' => 'json_array',
            'description' => 'Optional JSON array of valid existing Trust Library item IDs (e.g. [1, 5, 12]). Invalid IDs will be flagged and rejected.',
            'example' => '[]',
        ],

        // H. Curriculum Modules and Lessons
        'modules_json' => [
            'label' => 'Curriculum Modules (JSON)',
            'required' => false,
            'section' => 'Curriculum Modules & Lessons',
            'type' => 'json_array',
            'description' => 'JSON array of module objects: [{"title":"Module 1: Enterprise Structure","description":"Configuring company codes and plants.","sort_order":1,"status":"published"}]',
            'example' => '[{"title":"Module 1: Enterprise Structure & Master Data","description":"Overview of client, company code, plant, storage location, and purchasing organization.","sort_order":1,"status":"published"},{"title":"Module 2: Procurement & P2P Cycle","description":"Purchase requisition, RFQ, vendor evaluation, and purchase order workflow.","sort_order":2,"status":"published"}]',
        ],
        'lessons_json' => [
            'label' => 'Curriculum Lessons (JSON)',
            'required' => false,
            'section' => 'Curriculum Modules & Lessons',
            'type' => 'json_array',
            'description' => 'JSON array of lesson objects linked by module_title: [{"module_title":"Module 1: Enterprise Structure & Master Data","title":"Lesson 1.1: Organizational Units in SAP MM","description":"Define and assign plant to company code.","content_type":"text","content":"In this lesson we configure plants in SPRO.","duration_minutes":45,"video_url":"","resource_url":"","sort_order":1,"status":"published"}]',
            'example' => '[{"module_title":"Module 1: Enterprise Structure & Master Data","title":"Lesson 1.1: Organizational Units in SAP MM","description":"Define and assign plant to company code.","content_type":"text","content":"In this lesson we configure plants and storage locations in SPRO.","duration_minutes":45,"video_url":"","resource_url":"","sort_order":1,"status":"published"},{"module_title":"Module 2: Procurement & P2P Cycle","title":"Lesson 2.1: Purchase Order Execution","description":"Creating standard PO via transaction ME21N.","content_type":"text","content":"Step-by-step walkthrough of ME21N purchase order creation.","duration_minutes":60,"video_url":"","resource_url":"","sort_order":1,"status":"published"}]',
        ],
    ];

    public function __construct(
        protected CourseCmsService $cmsService,
        protected MarkdownSanitizerService $markdownSanitizer
    ) {}

    /**
     * Get all column definitions.
     */
    public function getColumns(): array
    {
        return self::COLUMNS;
    }

    /**
     * Generate the complete downloadable CSV template containing all headers and a valid sample row.
     */
    public function generateTemplateCsv(): string
    {
        $headers = array_keys(self::COLUMNS);
        $sampleRow = [];

        foreach (self::COLUMNS as $colKey => $meta) {
            $sampleRow[] = $this->sanitizeForCsvExport($meta['example'] ?? '');
        }

        $output = fopen('php://temp', 'r+');

        // Add UTF-8 BOM so Excel and other spreadsheet editors properly decode characters
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, $headers);
        fputcsv($output, $sampleRow);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Sanitize output values to prevent CSV formula injection (DDE attacks).
     */
    public function sanitizeForCsvExport(string $value): string
    {
        if (preg_match('/^[\=\+\-\@\t\r]/', $value)) {
            return "'".$value;
        }

        return $value;
    }

    /**
     * Generate the detailed Markdown CSV format guide.
     */
    public function generateGuideMarkdown(): string
    {
        $categories = $this->getValidCategorySlugs();
        $categoriesList = implode(', ', $categories);

        $markdown = <<<'MARKDOWN'
# ZebLearn LMS – Complete Course CSV Import Specification Guide

This guide details the exact format, validation constraints, and JSON structures required for importing courses via CSV into ZebLearn LMS with 100% field parity with the Admin Course Creator.

---

## 1. Golden Rules for CSV Import
1. **One Row Per Course**: Scalar fields, cohorts, FAQs, modules, and lessons are all stored in their designated columns on a single row.
2. **JSON Columns**: Columns ending in `_json` must contain valid JSON arrays. Ensure inner quotes are doubled if editing raw CSV (e.g. `"[{\"name\":\"Cohort 1\"}]"`).
3. **Markdown Preserved**: The `detailed_course_overview` column supports complete Markdown syntax (`##`, `###`, `**bold**`, `*italic*`, ordered lists `1. `, bullet lists `- `, and links `[text](url)`). It will be sanitized and converted into responsive HTML automatically.
4. **Valid Categories Only**: `subject_category` must match an existing category slug:
MARKDOWN;

        $markdown .= " `{$categoriesList}`\n\n";

        $markdown .= <<<'MARKDOWN'
---

## 2. Columns Reference by Section

### A. Course Identity
- `course_title` **(Required)**: Full official course name (e.g. `SAP MM Training – Materials Management`).
- `course_slug` *(Optional)*: URL slug. If left blank, automatically generated from title (e.g. `sap-mm-training`).
- `subject_category` **(Required)**: Must match an approved category slug.
- `lifecycle_status` **(Required)**: One of `available` (open), `upcoming` (advance booking), or `planned` (roadmap).
- `display_sort_order` *(Optional)*: Integer (e.g. `0` for default placement).

### B. Tuition & Delivery Logistics
- `effective_fee` *(Optional)*: Discounted admission price in INR (e.g. `25000`).
- `original_fee` *(Optional)*: Regular price in INR (e.g. `65000`).
- `discount_rate` *(Optional)*: Percentage discount (0-100). The importer will compare this with the actual fee discount and highlight any discrepancy in the preview.
- `total_modules` *(Optional)*: Integer nominal count (e.g. `12`).
- `estimated_duration` *(Optional)*: Text string (e.g. `3 Months`).
- `delivery_methodology` *(Optional)*: String (e.g. `Live Online + Real System Practice`).
- `tag_badge` *(Optional)*: Short badge (e.g. `HOT`, `BESTSELLER`).
- `banner_label` *(Optional)*: Banner tag (e.g. `AVAILABLE NOW`).
- `featured` *(Optional)*: `true` or `false`.
- `is_published` *(Optional)*: `true` (live immediately) or `false` (draft).

### C. Syllabus & Curriculum Content
- `card_short_description` **(Required)**: 2-3 sentence overview for catalog cards.
- `detailed_course_overview` *(Optional)*: Full Markdown course description with H2/H3 headers, bullet points, and learning outcomes.
- `curriculum_highlights` *(Optional)*: Bullet points separated by newlines or JSON array of strings.

### D. Upcoming Live Cohorts & Batches (`cohorts_json`)
JSON array of objects with the following schema:
```json
[
  {
    "name": "Weekend Executive Cohort #1",
    "start_date": "2026-12-05",
    "end_date": "2027-03-05",
    "timings": "08:00 AM – 10:00 AM IST",
    "days": "Sat & Sun (Weekend)",
    "delivery_mode": "Live Online",
    "total_seats": 25,
    "available_seats": 8,
    "status": "filling_fast",
    "is_active": true
  }
]
```
Status options: `filling_fast`, `open`, `almost_full`, `closed`.

### E. Frequently Asked Questions (`faqs_json`)
JSON array of FAQ objects:
```json
[
  {
    "question": "Is 24/7 dedicated server access provided?",
    "answer": "Yes, learners get 24/7 live sandbox credentials for practical system practice.",
    "sort_order": 1,
    "is_active": true
  }
]
```

### F. SEO Metadata
- `seo_meta_title` *(Optional)*: Search engine title tag (< 60 chars).
- `seo_meta_description` *(Optional)*: Search engine snippet (< 160 chars).

### G. Trust & Proof of Work CMS Controls
- `inherit_institutional_proof` *(Optional)*: `true` (default) or `false`. When true, global partner logos and awards are inherited automatically without duplicate database records.
- `placements_limit` *(Optional)*: Max student placement records (1-50, default 6).
- `testimonials_limit` *(Optional)*: Max testimonials (1-50, default 6).
- `hiring_partners_limit` *(Optional)*: Max partner logos (1-50, default 8).
- `expert_faculty_limit` *(Optional)*: Max faculty mentors (1-50, default 3).
- `certification_badges_limit` *(Optional)*: Max certificates (1-50, default 6).
- `capstone_projects_limit` *(Optional)*: Max capstone projects (1-50, default 6).
- `video_stories_limit` *(Optional)*: Max video stories (1-50, default 3).
- `awards_recognitions_limit` *(Optional)*: Max awards (1-50, default 3).
- `selected_proof_ids_json` *(Optional)*: JSON array of verified Central Trust Library IDs (e.g. `[1, 4]`). Invalid IDs are rejected.

### H. Curriculum Modules and Lessons (`modules_json`, `lessons_json`)
`modules_json`:
```json
[
  {
    "title": "Module 1: Enterprise Structure & Master Data",
    "description": "Foundational configuration in SPRO.",
    "sort_order": 1,
    "status": "published"
  }
]
```

`lessons_json`:
```json
[
  {
    "module_title": "Module 1: Enterprise Structure & Master Data",
    "title": "Lesson 1.1: Organizational Units in SAP MM",
    "description": "Define plants and storage locations.",
    "content_type": "text",
    "content": "Detailed configuration walkthrough.",
    "duration_minutes": 45,
    "video_url": "",
    "resource_url": "",
    "sort_order": 1,
    "status": "published"
  }
]
```

---

## 3. Instructions for AI Prompts (ChatGPT, Claude, etc.)
When asking an AI model to generate course CSV data for ZebLearn:
1. Provide the list of headers verbatim.
2. Ask the AI to output valid CSV where JSON cells are wrapped in standard double-quotes with internal quotes properly doubled (`""`).
3. Remind the AI to use actual category slugs from the approved list.
4. Tell the AI that `detailed_course_overview` should use rich Markdown starting from `## ` headings (never use raw `#` for title, as H1 is reserved for SEO).
MARKDOWN;

        return $markdown;
    }

    /**
     * Parse raw CSV content into array of rows with header keys.
     *
     * @return array{headers: list<string>, rows: list<array<string, string>>, errors: list<string>}
     */
    public function parseCsv(string $content): array
    {
        // Strip UTF-8 BOM if present
        if (str_starts_with($content, "\xEF\xBB\xBF")) {
            $content = substr($content, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return [
                'headers' => [],
                'rows' => [],
                'errors' => ['The uploaded CSV file is empty.'],
            ];
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $headers = fgetcsv($stream);
        if (! $headers || empty(array_filter($headers))) {
            fclose($stream);

            return [
                'headers' => [],
                'rows' => [],
                'errors' => ['Could not detect valid CSV headers in the uploaded file.'],
            ];
        }

        // Clean header names (trim whitespace, lower case, remove non-printable characters)
        $cleanHeaders = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x7F]/', '', $h)));
        }, $headers);

        $expectedHeaders = array_keys(self::COLUMNS);
        $missingRequiredHeaders = [];

        foreach (self::COLUMNS as $colKey => $meta) {
            if ($meta['required'] && ! in_array($colKey, $cleanHeaders, true)) {
                $missingRequiredHeaders[] = $colKey;
            }
        }

        if (! empty($missingRequiredHeaders)) {
            fclose($stream);

            return [
                'headers' => $cleanHeaders,
                'rows' => [],
                'errors' => [
                    'Missing required CSV column headers: '.implode(', ', $missingRequiredHeaders),
                ],
            ];
        }

        $rows = [];
        $lineNumber = 1;

        while (($row = fgetcsv($stream)) !== false) {
            $lineNumber++;

            // Skip blank lines
            if (empty(array_filter($row, fn ($val) => trim((string) $val) !== ''))) {
                continue;
            }

            $mapped = [];
            foreach ($cleanHeaders as $index => $colName) {
                $val = $row[$index] ?? '';
                // Strip formula injection prefix if present
                if (is_string($val) && str_starts_with($val, "'") && preg_match('/^\'[\=\+\-\@]/', $val)) {
                    $val = substr($val, 1);
                }
                $mapped[$colName] = is_string($val) ? trim($val) : $val;
            }

            $mapped['_line_number'] = $lineNumber;
            $rows[] = $mapped;
        }

        fclose($stream);

        return [
            'headers' => $cleanHeaders,
            'rows' => $rows,
            'errors' => [],
        ];
    }

    /**
     * Validate an individual row against form rules and return parsed data, warnings, and errors.
     *
     * @param  array<string, mixed>  $row
     * @param  array<string, mixed>  $options
     * @return array{is_valid: bool, errors: list<string>, warnings: list<string>, data: array<string, mixed>}
     */
    public function validateRow(array $row, int $rowIndex, array $options = []): array
    {
        $errors = [];
        $warnings = [];
        $validCategories = $this->getValidCategorySlugs();

        // 1. Course Title (Required)
        $title = trim($row['course_title'] ?? '');
        if (empty($title)) {
            $errors[] = 'Row #'.($rowIndex + 1).': "course_title" is required.';
        } elseif (mb_strlen($title) > 255) {
            $errors[] = 'Row #'.($rowIndex + 1).': "course_title" exceeds maximum 255 characters.';
        }

        // 2. Slug
        $slug = trim($row['course_slug'] ?? '');
        if (empty($slug) && ! empty($title)) {
            $baseSlug = Str::slug($title);
            $slug = $baseSlug;
            // Generate non-colliding slug preview
            $c = 1;
            while (Course::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$c}";
                $c++;
            }
        } else {
            $slug = Str::slug($slug);
        }

        // Check duplicate slug in DB
        $existingCourse = Course::where('slug', $slug)->first();
        if ($existingCourse) {
            if (! empty($options['update_existing'])) {
                $warnings[] = 'Row #'.($rowIndex + 1).": Course with slug '{$slug}' already exists (ID: {$existingCourse->id}). It will be updated because 'Update Existing' mode is enabled.";
            } else {
                $errors[] = 'Row #'.($rowIndex + 1).": Course with slug '{$slug}' already exists in database. Provide a unique slug or enable 'Update Existing Courses'.";
            }
        }

        // 3. Category (Required, must match valid category)
        $category = trim($row['subject_category'] ?? '');
        if (empty($category)) {
            $errors[] = 'Row #'.($rowIndex + 1).': "subject_category" is required.';
        } elseif (! in_array($category, $validCategories, true)) {
            $errors[] = 'Row #'.($rowIndex + 1).": Invalid category '{$category}'. Must be one of: ".implode(', ', $validCategories);
        }

        // 4. Lifecycle Status
        $status = strtolower(trim($row['lifecycle_status'] ?? 'available'));
        if (! in_array($status, ['available', 'upcoming', 'planned'], true)) {
            $errors[] = 'Row #'.($rowIndex + 1).": Invalid lifecycle_status '{$status}'. Must be available, upcoming, or planned.";
        }

        // 5. Display Sort Order
        $sortOrder = isset($row['display_sort_order']) && $row['display_sort_order'] !== '' ? (int) $row['display_sort_order'] : 0;

        // 6. Pricing & Discounts
        $effectiveFee = isset($row['effective_fee']) && $row['effective_fee'] !== '' ? (int) filter_var($row['effective_fee'], FILTER_SANITIZE_NUMBER_INT) : null;
        $originalFee = isset($row['original_fee']) && $row['original_fee'] !== '' ? (int) filter_var($row['original_fee'], FILTER_SANITIZE_NUMBER_INT) : null;
        $discountRate = isset($row['discount_rate']) && $row['discount_rate'] !== '' ? (int) filter_var($row['discount_rate'], FILTER_SANITIZE_NUMBER_INT) : null;

        if ($effectiveFee !== null && $effectiveFee < 0) {
            $errors[] = 'Row #'.($rowIndex + 1).': "effective_fee" cannot be negative.';
        }
        if ($originalFee !== null && $originalFee < 0) {
            $errors[] = 'Row #'.($rowIndex + 1).': "original_fee" cannot be negative.';
        }
        if ($effectiveFee !== null && $originalFee !== null && $effectiveFee > $originalFee) {
            $warnings[] = 'Row #'.($rowIndex + 1).": Effective fee (₹{$effectiveFee}) is higher than original fee (₹{$originalFee}). Original fee will be adjusted to match effective fee.";
            $originalFee = $effectiveFee;
        }

        // Calculate and verify discount percentage
        $calculatedDiscount = null;
        if ($originalFee !== null && $originalFee > 0 && $effectiveFee !== null) {
            $calculatedDiscount = (int) round((($originalFee - $effectiveFee) / $originalFee) * 100);

            if ($discountRate !== null && abs($discountRate - $calculatedDiscount) > 1) {
                $warnings[] = 'Row #'.($rowIndex + 1).": Specified discount_rate ({$discountRate}%) does not match mathematical discount between original fee (₹{$originalFee}) and effective fee (₹{$effectiveFee}), which is {$calculatedDiscount}%. The calculated discount will be used.";
            }
            $discountRate = $calculatedDiscount;
        }

        // 7. Modules count & Duration
        $modulesCount = isset($row['total_modules']) && $row['total_modules'] !== '' ? (int) $row['total_modules'] : 12;
        $duration = ! empty($row['estimated_duration']) ? trim($row['estimated_duration']) : '3 Months';
        $deliveryMode = ! empty($row['delivery_methodology']) ? trim($row['delivery_methodology']) : 'Live Online + Real System Practice';
        $tagBadge = ! empty($row['tag_badge']) ? trim($row['tag_badge']) : 'HOT';
        $bannerLabel = ! empty($row['banner_label']) ? trim($row['banner_label']) : 'AVAILABLE NOW';
        $featured = $this->parseBoolean($row['featured'] ?? false);
        $isPublished = $this->parseBoolean($row['is_published'] ?? ($options['publish_all'] ?? false));

        // 8. Descriptions & Markdown
        $shortDescription = trim($row['card_short_description'] ?? '');
        if (empty($shortDescription)) {
            $errors[] = 'Row #'.($rowIndex + 1).': "card_short_description" is required.';
        }

        $overviewMarkdown = trim($row['detailed_course_overview'] ?? '');
        $highlights = $this->parseHighlights($row['curriculum_highlights'] ?? '');

        // 9. Cohorts JSON
        $cohorts = [];
        $cohortsRaw = trim($row['cohorts_json'] ?? '');
        if (! empty($cohortsRaw)) {
            $decodedCohorts = json_decode($cohortsRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decodedCohorts)) {
                $errors[] = 'Row #'.($rowIndex + 1).': "cohorts_json" contains invalid JSON: '.json_last_error_msg();
            } else {
                foreach ($decodedCohorts as $bIdx => $bItem) {
                    if (empty($bItem['name']) || empty($bItem['start_date'])) {
                        $warnings[] = 'Row #'.($rowIndex + 1).', Cohort #'.($bIdx + 1).": Skipped because 'name' or 'start_date' is missing.";

                        continue;
                    }
                    $cohorts[] = [
                        'name' => trim($bItem['name']),
                        'start_date' => trim($bItem['start_date']),
                        'end_date' => ! empty($bItem['end_date']) ? trim($bItem['end_date']) : null,
                        'timings' => ! empty($bItem['timings']) ? trim($bItem['timings']) : '08:00 AM – 10:00 AM IST',
                        'days' => ! empty($bItem['days']) ? trim($bItem['days']) : 'Sat & Sun (Weekend)',
                        'delivery_mode' => ! empty($bItem['delivery_mode']) ? trim($bItem['delivery_mode']) : 'Live Online',
                        'timezone' => ! empty($bItem['timezone']) ? trim($bItem['timezone']) : 'Asia/Kolkata',
                        'total_seats' => isset($bItem['total_seats']) ? (int) $bItem['total_seats'] : 25,
                        'available_seats' => isset($bItem['available_seats']) ? (int) $bItem['available_seats'] : 8,
                        'status' => ! empty($bItem['status']) ? trim($bItem['status']) : 'filling_fast',
                        'sort_order' => isset($bItem['sort_order']) ? (int) $bItem['sort_order'] : ($bIdx + 1) * 10,
                        'is_active' => isset($bItem['is_active']) ? (bool) $bItem['is_active'] : true,
                    ];
                }
            }
        }

        // 10. FAQs JSON
        $faqs = [];
        $faqsRaw = trim($row['faqs_json'] ?? '');
        if (! empty($faqsRaw)) {
            $decodedFaqs = json_decode($faqsRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decodedFaqs)) {
                $errors[] = 'Row #'.($rowIndex + 1).': "faqs_json" contains invalid JSON: '.json_last_error_msg();
            } else {
                foreach ($decodedFaqs as $fIdx => $fItem) {
                    if (empty($fItem['question']) || empty($fItem['answer'])) {
                        $warnings[] = 'Row #'.($rowIndex + 1).', FAQ #'.($fIdx + 1).': Skipped because question or answer is blank.';

                        continue;
                    }
                    $faqs[] = [
                        'question' => trim($fItem['question']),
                        'answer' => trim($fItem['answer']),
                        'sort_order' => isset($fItem['sort_order']) ? (int) $fItem['sort_order'] : ($fIdx + 1) * 10,
                        'is_active' => isset($fItem['is_active']) ? (bool) $fItem['is_active'] : true,
                    ];
                }
            }
        }

        // 11. SEO Metadata
        $seoTitle = ! empty($row['seo_meta_title']) ? trim($row['seo_meta_title']) : "{$title} Online Training | ZebLearn";
        $seoDescription = ! empty($row['seo_meta_description']) ? trim($row['seo_meta_description']) : $shortDescription;

        // 12. Trust & Proof Controls
        $inheritGlobal = isset($row['inherit_institutional_proof'])
            ? $this->parseBoolean($row['inherit_institutional_proof'])
            : true;

        $trustConfig = [
            'include_global' => $inheritGlobal,
            'sections' => [
                'placements' => [
                    'enabled' => $this->parseBoolean($row['show_placements'] ?? true),
                    'limit' => $this->clampLimit($row['placements_limit'] ?? 6, 6),
                    'mode' => 'auto',
                ],
                'testimonials' => [
                    'enabled' => $this->parseBoolean($row['show_testimonials'] ?? true),
                    'limit' => $this->clampLimit($row['testimonials_limit'] ?? 6, 6),
                    'mode' => 'auto',
                ],
                'partners' => [
                    'enabled' => $this->parseBoolean($row['show_hiring_partners'] ?? true),
                    'limit' => $this->clampLimit($row['hiring_partners_limit'] ?? 8, 8),
                    'mode' => 'auto',
                ],
                'trainers' => [
                    'enabled' => $this->parseBoolean($row['show_expert_faculty'] ?? true),
                    'limit' => $this->clampLimit($row['expert_faculty_limit'] ?? 3, 3),
                    'mode' => 'auto',
                ],
                'certifications' => [
                    'enabled' => $this->parseBoolean($row['show_certification_badges'] ?? true),
                    'limit' => $this->clampLimit($row['certification_badges_limit'] ?? 6, 6),
                    'mode' => 'auto',
                ],
                'projects' => [
                    'enabled' => $this->parseBoolean($row['show_capstone_projects'] ?? true),
                    'limit' => $this->clampLimit($row['capstone_projects_limit'] ?? 6, 6),
                    'mode' => 'auto',
                ],
                'video_stories' => [
                    'enabled' => $this->parseBoolean($row['show_video_stories'] ?? true),
                    'limit' => $this->clampLimit($row['video_stories_limit'] ?? 3, 3),
                    'mode' => 'auto',
                ],
                'awards' => [
                    'enabled' => $this->parseBoolean($row['show_awards_recognitions'] ?? true),
                    'limit' => $this->clampLimit($row['awards_recognitions_limit'] ?? 3, 3),
                    'mode' => 'auto',
                ],
            ],
            'manual_overrides' => [],
        ];

        // Selected proof IDs validation
        $selectedProofIds = [];
        $proofRaw = trim($row['selected_proof_ids_json'] ?? '');
        if (! empty($proofRaw) && $proofRaw !== '[]') {
            $decodedProof = json_decode($proofRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decodedProof)) {
                $warnings[] = 'Row #'.($rowIndex + 1).': "selected_proof_ids_json" contains invalid JSON. Ignored.';
            } else {
                $validItemIds = TrustItem::whereIn('id', $decodedProof)->pluck('id')->all();
                $invalidIds = array_diff($decodedProof, $validItemIds);
                if (! empty($invalidIds)) {
                    $warnings[] = 'Row #'.($rowIndex + 1).': Ignored non-existent Trust Item IDs: '.implode(', ', $invalidIds).'. Only genuine library items are linked.';
                }
                $selectedProofIds = $validItemIds;
            }
        }

        // 13. Curriculum Modules & Lessons JSON
        $modules = [];
        $modulesRaw = trim($row['modules_json'] ?? '');
        if (! empty($modulesRaw)) {
            $decodedModules = json_decode($modulesRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decodedModules)) {
                $errors[] = 'Row #'.($rowIndex + 1).': "modules_json" contains invalid JSON: '.json_last_error_msg();
            } else {
                foreach ($decodedModules as $mIdx => $mItem) {
                    if (empty($mItem['title'])) {
                        $warnings[] = 'Row #'.($rowIndex + 1).', Module #'.($mIdx + 1).': Skipped because module title is empty.';

                        continue;
                    }
                    $modules[] = [
                        'title' => trim($mItem['title']),
                        'description' => ! empty($mItem['description']) ? trim($mItem['description']) : null,
                        'sort_order' => isset($mItem['sort_order']) ? (int) $mItem['sort_order'] : $mIdx + 1,
                        'status' => in_array($mItem['status'] ?? 'published', ['published', 'draft'], true) ? $mItem['status'] : 'published',
                    ];
                }
            }
        }

        $lessons = [];
        $lessonsRaw = trim($row['lessons_json'] ?? '');
        if (! empty($lessonsRaw)) {
            $decodedLessons = json_decode($lessonsRaw, true);
            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decodedLessons)) {
                $errors[] = 'Row #'.($rowIndex + 1).': "lessons_json" contains invalid JSON: '.json_last_error_msg();
            } else {
                foreach ($decodedLessons as $lIdx => $lItem) {
                    if (empty($lItem['title'])) {
                        $warnings[] = 'Row #'.($rowIndex + 1).', Lesson #'.($lIdx + 1).': Skipped because lesson title is empty.';

                        continue;
                    }
                    $contentType = in_array($lItem['content_type'] ?? 'text', ['text', 'video', 'resource'], true)
                        ? $lItem['content_type']
                        : 'text';

                    $lessons[] = [
                        'module_title' => trim($lItem['module_title'] ?? ''),
                        'title' => trim($lItem['title']),
                        'description' => ! empty($lItem['description']) ? trim($lItem['description']) : null,
                        'content_type' => $contentType,
                        'content' => ! empty($lItem['content']) ? trim($lItem['content']) : null,
                        'video_url' => ! empty($lItem['video_url']) ? trim($lItem['video_url']) : null,
                        'resource_url' => ! empty($lItem['resource_url']) ? trim($lItem['resource_url']) : null,
                        'duration_minutes' => isset($lItem['duration_minutes']) ? (int) $lItem['duration_minutes'] : 45,
                        'sort_order' => isset($lItem['sort_order']) ? (int) $lItem['sort_order'] : $lIdx + 1,
                        'status' => in_array($lItem['status'] ?? 'published', ['published', 'draft'], true) ? $lItem['status'] : 'published',
                    ];
                }
            }
        }

        $parsedData = [
            'name' => $title,
            'slug' => $slug,
            'category' => $category,
            'status' => $status,
            'sort_order' => $sortOrder,
            'fee' => $effectiveFee,
            'original_fee' => $originalFee,
            'discount_percent' => $discountRate,
            'modules_count' => $modulesCount,
            'duration' => $duration,
            'delivery_mode' => $deliveryMode,
            'tag_badge' => $tagBadge,
            'badge_label' => $bannerLabel,
            'featured' => $featured,
            'is_published' => $isPublished,
            'short_description' => $shortDescription,
            'description' => $overviewMarkdown,
            'rendered_description' => $this->markdownSanitizer->toHtml($overviewMarkdown),
            'highlights' => $highlights,
            'cohorts' => $cohorts,
            'faqs' => $faqs,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'trust_config' => $trustConfig,
            'selected_proof_ids' => $selectedProofIds,
            'modules' => $modules,
            'lessons' => $lessons,
            '_existing_id' => $existingCourse?->id,
            '_is_update' => (bool) $existingCourse,
            '_line_number' => $row['_line_number'] ?? ($rowIndex + 2),
        ];

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'data' => $parsedData,
        ];
    }

    /**
     * Preview all rows in the CSV and compile comprehensive summary.
     *
     * @param  array<string, mixed>  $options
     * @return array{success: bool, total_rows: int, valid_rows: int, invalid_rows: int, global_errors: list<string>, courses: list<array<string, mixed>>}
     */
    public function preview(string $csvContent, array $options = []): array
    {
        $parsed = $this->parseCsv($csvContent);

        if (! empty($parsed['errors'])) {
            return [
                'success' => false,
                'total_rows' => 0,
                'valid_rows' => 0,
                'invalid_rows' => 0,
                'global_errors' => $parsed['errors'],
                'courses' => [],
            ];
        }

        $courses = [];
        $validCount = 0;
        $invalidCount = 0;

        foreach ($parsed['rows'] as $idx => $row) {
            $validation = $this->validateRow($row, $idx, $options);

            if ($validation['is_valid']) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $courses[] = [
                'row_index' => $idx,
                'line_number' => $row['_line_number'] ?? ($idx + 2),
                'is_valid' => $validation['is_valid'],
                'errors' => $validation['errors'],
                'warnings' => $validation['warnings'],
                'data' => $validation['data'],
            ];
        }

        return [
            'success' => true,
            'total_rows' => count($parsed['rows']),
            'valid_rows' => $validCount,
            'invalid_rows' => $invalidCount,
            'global_errors' => [],
            'courses' => $courses,
        ];
    }

    /**
     * Execute the validated import in a safe database transaction.
     *
     * @param  list<array<string, mixed>>  $coursesData
     * @param  array<string, mixed>  $options
     * @return array{imported: int, updated: int, failed: int, errors: list<string>, created_courses: list<array{id: int, name: string, slug: string}>}
     */
    public function import(array $coursesData, array $options = []): array
    {
        $importedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $errors = [];
        $createdCourses = [];

        foreach ($coursesData as $cItem) {
            $data = $cItem['data'] ?? $cItem;

            try {
                DB::transaction(function () use ($data, $options, &$importedCount, &$updatedCount, &$createdCourses) {
                    $slug = $data['slug'];
                    $isUpdate = ! empty($data['_is_update']) && ! empty($options['update_existing']);

                    $coursePayload = [
                        'name' => $data['name'],
                        'slug' => $slug,
                        'category' => $data['category'],
                        'status' => $data['status'],
                        'sort_order' => $data['sort_order'],
                        'fee' => $data['fee'],
                        'original_fee' => $data['original_fee'],
                        'discount_percent' => $data['discount_percent'],
                        'modules_count' => $data['modules_count'],
                        'duration' => $data['duration'],
                        'delivery_mode' => $data['delivery_mode'],
                        'tag_badge' => $data['tag_badge'],
                        'badge_label' => $data['badge_label'],
                        'featured' => $data['featured'],
                        'short_description' => $data['short_description'],
                        'description' => $data['description'],
                        'highlights' => $data['highlights'],
                        'seo_title' => $data['seo_title'],
                        'seo_description' => $data['seo_description'],
                        'trust_config' => $data['trust_config'],
                    ];

                    $coursePayload['published_at'] = ! empty($data['is_published']) ? now() : null;

                    // Sync category_id if table exists
                    if (Schema::hasTable('categories')) {
                        $cat = Category::where('slug', $data['category'])->first();
                        if ($cat) {
                            $coursePayload['category_id'] = $cat->id;
                        }
                    }

                    if ($isUpdate) {
                        $course = Course::where('slug', $slug)->firstOrFail();
                        $course->update($coursePayload);
                        $updatedCount++;
                    } else {
                        $course = Course::create($coursePayload);
                        $importedCount++;
                    }

                    // 1. Sync Cohorts (Batches)
                    if (! empty($data['cohorts'])) {
                        $this->cmsService->syncBatches($course, $data['cohorts']);
                    }

                    // 2. Sync FAQs
                    if (! empty($data['faqs'])) {
                        $this->cmsService->syncFaqs($course, $data['faqs']);
                    }

                    // 3. Sync Modules and Lessons
                    if (! empty($data['modules'])) {
                        $this->syncCurriculum($course, $data['modules'], $data['lessons'] ?? []);
                    }

                    // 4. Link Selected Proof Items if specified
                    if (! empty($data['selected_proof_ids'])) {
                        $course->trustItems()->sync($data['selected_proof_ids']);
                    }

                    $this->cmsService->invalidateCourseCache($course);

                    $createdCourses[] = [
                        'id' => $course->id,
                        'name' => $course->name,
                        'slug' => $course->slug,
                    ];
                });
            } catch (\Throwable $e) {
                $failedCount++;
                $errors[] = "Failed to import course '{$data['name']}': ".$e->getMessage();
            }
        }

        return [
            'imported' => $importedCount,
            'updated' => $updatedCount,
            'failed' => $failedCount,
            'errors' => $errors,
            'created_courses' => $createdCourses,
        ];
    }

    /**
     * Synchronize curriculum modules and nested lessons cleanly.
     *
     * @param  list<array<string, mixed>>  $modules
     * @param  list<array<string, mixed>>  $lessons
     */
    protected function syncCurriculum(Course $course, array $modules, array $lessons): void
    {
        $moduleMap = [];

        foreach ($modules as $mIndex => $mItem) {
            $module = CourseModule::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $mItem['title'],
                ],
                [
                    'description' => $mItem['description'] ?? null,
                    'sort_order' => $mItem['sort_order'] ?? ($mIndex + 1),
                    'status' => $mItem['status'] ?? 'published',
                ]
            );

            $moduleMap[$module->title] = $module;
            $moduleMap['index_'.($mIndex + 1)] = $module;
        }

        // Attach lessons to their respective modules
        foreach ($lessons as $lIndex => $lItem) {
            $targetModule = null;
            $modTitle = $lItem['module_title'] ?? '';

            if (! empty($modTitle) && isset($moduleMap[$modTitle])) {
                $targetModule = $moduleMap[$modTitle];
            } elseif (! empty($moduleMap)) {
                // Fallback to first module if not specified or unmapped
                $targetModule = reset($moduleMap);
            }

            if (! $targetModule) {
                continue;
            }

            $targetModule->lessons()->updateOrCreate(
                [
                    'course_module_id' => $targetModule->id,
                    'title' => $lItem['title'],
                ],
                [
                    'description' => $lItem['description'] ?? null,
                    'content_type' => $lItem['content_type'] ?? 'text',
                    'content' => $lItem['content'] ?? null,
                    'video_url' => $lItem['video_url'] ?? null,
                    'resource_url' => $lItem['resource_url'] ?? null,
                    'duration_minutes' => $lItem['duration_minutes'] ?? 45,
                    'sort_order' => $lItem['sort_order'] ?? ($lIndex + 1),
                    'status' => $lItem['status'] ?? 'published',
                ]
            );
        }
    }

    /**
     * Retrieve all valid category slugs in the system.
     *
     * @return list<string>
     */
    public function getValidCategorySlugs(): array
    {
        if (Schema::hasTable('categories')) {
            $dbSlugs = Category::pluck('slug')->all();
            if (! empty($dbSlugs)) {
                return $dbSlugs;
            }
        }

        return array_keys(Course::CATEGORIES);
    }

    /**
     * Helper to parse flexible boolean values.
     */
    protected function parseBoolean(mixed $val): bool
    {
        if (is_bool($val)) {
            return $val;
        }
        if (is_numeric($val)) {
            return (int) $val === 1;
        }
        $str = strtolower(trim((string) $val));

        return in_array($str, ['1', 'true', 'yes', 'on', 'enabled'], true);
    }

    /**
     * Helper to clamp quota limit between 1 and 50.
     */
    protected function clampLimit(mixed $val, int $default = 6): int
    {
        $num = (int) $val;
        if ($num <= 0) {
            return $default;
        }

        return max(1, min(50, $num));
    }

    /**
     * Helper to parse multi-line or JSON highlights.
     *
     * @return list<string>
     */
    protected function parseHighlights(mixed $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (is_array($input)) {
            return array_values(array_filter(array_map('trim', $input)));
        }

        $str = trim((string) $input);

        // Check if JSON array
        if (str_starts_with($str, '[') && str_ends_with($str, ']')) {
            $decoded = json_decode($str, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }
        }

        // Check pipe separated or newline separated
        $delimiter = str_contains($str, "\n") ? "\n" : (str_contains($str, '|') ? '|' : "\n");
        $lines = explode($delimiter, $str);

        return array_values(array_filter(array_map('trim', $lines)));
    }
}
