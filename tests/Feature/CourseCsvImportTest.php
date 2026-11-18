<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseFaq;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseCsvImportService;
use App\Services\TrustProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'email' => 'admin@zeblearn.com',
    ]);

    // Ensure category exists in DB or Course constants
    if (Schema::hasTable('categories')) {
        Category::firstOrCreate(
            ['slug' => 'sap-erp'],
            ['name' => 'SAP ERP', 'description' => 'SAP Enterprise Resource Planning tracks']
        );
    }

    app(TrustProofService::class)->seedInitialTrustRecords();
});

test('admin can access csv import console', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.courses.import.index'));

    $response->assertOk();
    $response->assertSee('Course CSV Import Console');
    $response->assertSee('Download CSV Template');
    $response->assertSee('Complete CSV Columns Reference');
});

test('admin can download csv template with all standard columns and formula injection prevention', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.courses.import.template'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $content = $response->getContent();

    // Check BOM
    expect(str_starts_with($content, "\xEF\xBB\xBF"))->toBeTrue();

    // Check all headers present
    $headers = array_keys(CourseCsvImportService::COLUMNS);
    foreach ($headers as $header) {
        expect($content)->toContain($header);
    }

    // Verify sample course data is present
    expect($content)->toContain('SAP MM Training');
    expect($content)->toContain('sap-erp');
});

test('admin can view and download csv specification guide', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.courses.import.guide'));
    $response->assertOk();
    $response->assertSee('Course CSV Import Format Guide');

    $downloadResponse = $this->actingAs($this->admin)->get(route('admin.courses.import.guide', ['download' => 1]));
    $downloadResponse->assertOk();
    $downloadResponse->assertHeader('Content-Type', 'text/markdown; charset=UTF-8');
});

test('csv validation flags invalid categories and negative pricing', function () {
    $service = app(CourseCsvImportService::class);

    $row = [
        'course_title' => 'Test Course with Invalid Category',
        'course_slug' => 'test-invalid-category',
        'subject_category' => 'non-existent-category-slug',
        'lifecycle_status' => 'available',
        'card_short_description' => 'Short description here',
        'effective_fee' => '-500',
        'original_fee' => '1000',
    ];

    $result = $service->validateRow($row, 0);

    expect($result['is_valid'])->toBeFalse();
    expect($result['errors'])->toContain('Row #1: Invalid category \'non-existent-category-slug\'. Must be one of: '.implode(', ', $service->getValidCategorySlugs()));
    expect($result['errors'])->toContain('Row #1: "effective_fee" cannot be negative.');
});

test('csv validation detects mathematical discount discrepancy and issues warning', function () {
    $service = app(CourseCsvImportService::class);

    $row = [
        'course_title' => 'Test Course With Discount Mismatch',
        'course_slug' => 'test-discount-mismatch',
        'subject_category' => 'sap-erp',
        'lifecycle_status' => 'available',
        'card_short_description' => 'Short description',
        'effective_fee' => '25000',
        'original_fee' => '65000',
        'discount_rate' => '40', // 25k/65k is 62% discount, not 40%
    ];

    $result = $service->validateRow($row, 0);

    expect($result['is_valid'])->toBeTrue();
    expect($result['warnings'])->not->toBeEmpty();
    expect(implode(' ', $result['warnings']))->toContain('does not match mathematical discount');
    expect($result['data']['discount_percent'])->toBe(62);
});

test('csv validation detects malformed JSON structures in cohorts and faqs', function () {
    $service = app(CourseCsvImportService::class);

    $row = [
        'course_title' => 'Test Course Malformed JSON',
        'course_slug' => 'test-malformed-json',
        'subject_category' => 'sap-erp',
        'lifecycle_status' => 'available',
        'card_short_description' => 'Short description',
        'cohorts_json' => '{malformed json string}',
        'faqs_json' => '[{"question": "Unclosed quote}]',
    ];

    $result = $service->validateRow($row, 0);

    expect($result['is_valid'])->toBeFalse();
    expect(implode(' ', $result['errors']))->toContain('cohorts_json');
    expect(implode(' ', $result['errors']))->toContain('faqs_json');
});

test('complete course csv import with 100% field coverage creates course, cohorts, faqs, modules and lessons', function () {
    $headers = array_keys(CourseCsvImportService::COLUMNS);

    $sampleMarkdown = <<<'MD'
## What You Will Learn

Build complete practical expertise in SAP Production Planning.

### Core Modules
- Demand Management & PIR
- Material Requirements Planning (MRP) Run
- Capacity Planning & Scheduling
MD;

    $sampleHighlights = "Demand Management & Forecasting\nMRP Master Data & SPRO Configuration\nShop Floor Control & Confirmation\n24/7 Dedicated Server Access";

    $sampleCohorts = json_encode([
        [
            'name' => 'Weekend Executive Cohort #1 (Live Online)',
            'start_date' => '2026-12-05',
            'end_date' => '2027-03-05',
            'timings' => '08:00 AM – 10:00 AM IST',
            'days' => 'Sat & Sun (Weekend)',
            'delivery_mode' => 'Live Online',
            'total_seats' => 25,
            'available_seats' => 8,
            'status' => 'filling_fast',
            'is_active' => true,
        ],
        [
            'name' => 'Weekday Evening Cohort #2',
            'start_date' => '2026-12-15',
            'end_date' => '2027-03-15',
            'timings' => '07:30 PM – 09:30 PM IST',
            'days' => 'Mon to Thu (Weekday)',
            'delivery_mode' => 'Live Online',
            'total_seats' => 20,
            'available_seats' => 12,
            'status' => 'open',
            'is_active' => true,
        ],
    ]);

    $sampleFaqs = json_encode([
        [
            'question' => 'Is 24/7 dedicated server sandbox access provided?',
            'answer' => 'Yes, every learner receives 24/7 dedicated cloud SAP server credentials for practical exercises throughout the course duration.',
            'sort_order' => 1,
            'is_active' => true,
        ],
        [
            'question' => 'What are the prerequisites for this course?',
            'answer' => 'Basic knowledge of supply chain, manufacturing, or ERP concepts is helpful but not strictly required.',
            'sort_order' => 2,
            'is_active' => true,
        ],
    ]);

    $sampleModules = json_encode([
        [
            'title' => 'Module 1: Enterprise Structure & Master Data in SAP PP',
            'description' => 'Configuring plant, storage location, BOM, and work center.',
            'sort_order' => 1,
            'status' => 'published',
        ],
        [
            'title' => 'Module 2: Material Requirements Planning (MRP)',
            'description' => 'MRP types, lot sizing procedures, and procurement proposals.',
            'sort_order' => 2,
            'status' => 'published',
        ],
    ]);

    $sampleLessons = json_encode([
        [
            'module_title' => 'Module 1: Enterprise Structure & Master Data in SAP PP',
            'title' => 'Lesson 1.1: Bill of Materials (BOM) Setup',
            'description' => 'Creating single and multi-level BOM via CS01.',
            'content_type' => 'text',
            'content' => 'Comprehensive walkthrough of item categories and BOM explosion.',
            'duration_minutes' => 45,
            'sort_order' => 1,
            'status' => 'published',
        ],
        [
            'module_title' => 'Module 1: Enterprise Structure & Master Data in SAP PP',
            'title' => 'Lesson 1.2: Work Center and Routing Configuration',
            'description' => 'Standard values, capacity formulas, and scheduling.',
            'content_type' => 'text',
            'content' => 'Configuring work centers in CR01 and routings in CA01.',
            'duration_minutes' => 60,
            'sort_order' => 2,
            'status' => 'published',
        ],
        [
            'module_title' => 'Module 2: Material Requirements Planning (MRP)',
            'title' => 'Lesson 2.1: Executing Single-Item Multi-Level MRP',
            'description' => 'Running MD02 transaction and reviewing stock/requirements list in MD04.',
            'content_type' => 'text',
            'content' => 'Executing MRP run and evaluating planned orders.',
            'duration_minutes' => 50,
            'sort_order' => 1,
            'status' => 'published',
        ],
    ]);

    $row = [
        'course_title' => 'SAP PP Training – Production Planning Masterclass',
        'course_slug' => 'sap-pp-training',
        'subject_category' => 'sap-erp',
        'lifecycle_status' => 'available',
        'display_sort_order' => '1',
        'effective_fee' => '28000',
        'original_fee' => '70000',
        'discount_rate' => '60',
        'total_modules' => '10',
        'estimated_duration' => '3 Months',
        'delivery_methodology' => 'Live Online + Real System Practice',
        'tag_badge' => 'HOT',
        'banner_label' => 'AVAILABLE NOW',
        'featured' => 'true',
        'is_published' => 'true',
        'card_short_description' => 'Master SAP Production Planning (PP), BOM, routing, work centers, MRP, and shop floor control.',
        'detailed_course_overview' => $sampleMarkdown,
        'curriculum_highlights' => $sampleHighlights,
        'cohorts_json' => $sampleCohorts,
        'faqs_json' => $sampleFaqs,
        'seo_meta_title' => 'SAP PP Training Online with Live Classes & Placement | ZebLearn',
        'seo_meta_description' => 'Learn SAP Production Planning online with live classes, MRP simulations, and verified placement support.',
        'inherit_institutional_proof' => 'true',
        'placements_limit' => '6',
        'testimonials_limit' => '6',
        'hiring_partners_limit' => '8',
        'expert_faculty_limit' => '3',
        'certification_badges_limit' => '6',
        'capstone_projects_limit' => '6',
        'video_stories_limit' => '3',
        'awards_recognitions_limit' => '3',
        'selected_proof_ids_json' => '[]',
        'modules_json' => $sampleModules,
        'lessons_json' => $sampleLessons,
    ];

    $csvFile = fopen('php://temp', 'r+');
    fputcsv($csvFile, $headers);
    fputcsv($csvFile, array_values($row));
    rewind($csvFile);
    $csvContent = stream_get_contents($csvFile);
    fclose($csvFile);

    // 1. Preview upload
    $file = UploadedFile::fake()->createWithContent('courses.csv', $csvContent);

    $previewResponse = $this->actingAs($this->admin)->post(route('admin.courses.import.preview'), [
        'csv_file' => $file,
    ]);

    $previewResponse->assertOk();
    $previewResponse->assertSee('SAP PP Training – Production Planning Masterclass');
    $previewResponse->assertSee('Weekend Executive Cohort #1');
    $previewResponse->assertSee('What You Will Learn');

    // 2. Process import from session
    $previewCourses = session('zeblearn_csv_import_courses') ?? $previewResponse->getSession()->get('zeblearn_csv_import_courses');

    $processResponse = $this->actingAs($this->admin)->withSession([
        'zeblearn_csv_import_courses' => $previewCourses,
    ])->post(route('admin.courses.import.process'), [
        'publish_override' => 'default',
    ]);

    $processResponse->assertRedirect(route('admin.courses.index'));
    $processResponse->assertSessionHas('success');

    // 3. Verify Database Records
    $course = Course::where('slug', 'sap-pp-training')->first();
    expect($course)->not->toBeNull();
    expect($course->name)->toBe('SAP PP Training – Production Planning Masterclass');
    expect($course->category)->toBe('sap-erp');
    expect($course->fee)->toBe(28000);
    expect($course->original_fee)->toBe(70000);
    expect($course->discount_percent)->toBe(60);
    expect($course->published_at)->not->toBeNull();

    // Cohorts verified
    $batches = CourseBatch::where('course_id', $course->id)->get();
    expect($batches)->toHaveCount(2);
    expect($batches->first()->name)->toBe('Weekend Executive Cohort #1 (Live Online)');
    expect($batches->first()->available_seats)->toBe(8);

    // FAQs verified
    $faqs = CourseFaq::where('course_id', $course->id)->get();
    expect($faqs)->toHaveCount(2);
    expect($faqs->first()->question)->toBe('Is 24/7 dedicated server sandbox access provided?');

    // Modules & Lessons verified
    $modules = CourseModule::where('course_id', $course->id)->orderBy('sort_order')->get();
    expect($modules)->toHaveCount(2);
    expect($modules->first()->title)->toBe('Module 1: Enterprise Structure & Master Data in SAP PP');

    $lessonsMod1 = Lesson::where('course_module_id', $modules->first()->id)->get();
    expect($lessonsMod1)->toHaveCount(2);
    expect($lessonsMod1->first()->title)->toBe('Lesson 1.1: Bill of Materials (BOM) Setup');

    $lessonsMod2 = Lesson::where('course_module_id', $modules->last()->id)->get();
    expect($lessonsMod2)->toHaveCount(1);
    expect($lessonsMod2->first()->title)->toBe('Lesson 2.1: Executing Single-Item Multi-Level MRP');

    // 4. Verify Public Course Landing Page Renders Correctly
    $publicResponse = $this->get('/courses/sap-pp-training');
    $publicResponse->assertOk();
    $publicResponse->assertSee('SAP PP Training');
    $publicResponse->assertSee('What You Will Learn');
    $publicResponse->assertSee('Enterprise Structure & Master Data in SAP PP');
    $publicResponse->assertSee('Weekend Executive Cohort #1');
    $publicResponse->assertSee('Is 24/7 dedicated server sandbox access provided?');
});
