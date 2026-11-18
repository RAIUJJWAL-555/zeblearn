<?php

use App\Models\Blog;
use App\Models\Course;
use App\Models\CourseModule;
use Carbon\Carbon;

test('courses catalog defaults to showing recently added courses first', function () {
    $olderCourse = Course::factory()->published()->create([
        'name' => 'SAP Alpha Foundation',
        'status' => 'available',
        'created_at' => Carbon::now()->subDays(20),
        'published_at' => Carbon::now()->subDays(20),
    ]);

    $newerCourse = Course::factory()->published()->create([
        'name' => 'SAP Omega Advanced Cloud',
        'status' => 'available',
        'created_at' => Carbon::now()->subMinute(),
        'published_at' => Carbon::now()->subMinute(),
    ]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee('Recently Added');

    // Newer course should appear before older course in the response
    $content = $response->getContent();
    $newerPos = strpos($content, 'SAP Omega Advanced Cloud');
    $olderPos = strpos($content, 'SAP Alpha Foundation');

    expect($newerPos)->not->toBeFalse()
        ->and($olderPos)->not->toBeFalse()
        ->and($newerPos)->toBeLessThan($olderPos);
});

test('public course search by keyword filters and displays matching results', function () {
    $fico = Course::factory()->published()->create([
        'name' => 'SAP FICO Financial Controlling',
        'short_description' => 'General ledger, accounts payable, accounts receivable.',
    ]);

    $mm = Course::factory()->published()->create([
        'name' => 'SAP MM Materials Management',
        'short_description' => 'Procurement, inventory, logistics.',
    ]);

    $response = $this->get(route('courses.index', ['q' => 'Controlling']));

    $response->assertOk();
    $response->assertSee('SEARCH RESULTS FOR');
    $response->assertSee('Controlling');
    $response->assertSee('SAP FICO Financial Controlling');
    $response->assertDontSee('SAP MM Materials Management');
});

test('course search finds courses by module content', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP Extended Warehouse Track',
    ]);

    CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Inbound Logistics Wave Processing',
        'description' => 'Configuring wave templates and storage types.',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $response = $this->get(route('courses.index', ['q' => 'Wave Processing']));

    $response->assertOk();
    $response->assertSee('SAP Extended Warehouse Track');
});

test('course search api returns json with matching courses ordered by newest first', function () {
    $older = Course::factory()->published()->create([
        'name' => 'SAP Basis Architecture v1',
        'created_at' => Carbon::now()->subDays(10),
    ]);

    $newer = Course::factory()->published()->create([
        'name' => 'SAP Basis Architecture v2',
        'created_at' => Carbon::now()->subMinutes(5),
    ]);

    $response = $this->get(route('courses.search.api', ['q' => 'Basis Architecture']));

    $response->assertOk()
        ->assertJsonStructure([
            'query',
            'count',
            'courses' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'url',
                    'category_label',
                    'display_price',
                    'duration',
                    'delivery_mode',
                    'is_recently_added',
                ],
            ],
        ]);

    $data = $response->json('courses');
    expect($data)->toHaveCount(2)
        ->and($data[0]['name'])->toBe('SAP Basis Architecture v2')
        ->and($data[1]['name'])->toBe('SAP Basis Architecture v1');
});

test('recently added course is identified by isRecentlyAdded', function () {
    $recent = Course::factory()->published()->create([
        'created_at' => Carbon::now()->subDays(5),
    ]);

    $old = Course::factory()->published()->create([
        'created_at' => Carbon::now()->subDays(60),
    ]);

    expect($recent->isRecentlyAdded())->toBeTrue()
        ->and($old->isRecentlyAdded())->toBeFalse();
});

test('course search api returns both courses and blogs when matching keyword', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP Extended Warehouse Management EWM Specialist',
    ]);

    $blog = Blog::factory()->published()->create([
        'title' => 'Top 50 SAP EWM Interview Questions and Answers',
        'slug' => 'top-50-sap-ewm-interview-questions-and-answers-test',
    ]);

    $response = $this->get(route('courses.search.api', ['q' => 'EWM']));

    $response->assertOk()
        ->assertJsonStructure([
            'query',
            'count',
            'courses',
            'blogs',
        ]);

    $courses = $response->json('courses');
    $blogs = $response->json('blogs');

    expect(collect($courses)->pluck('name'))->toContain('SAP Extended Warehouse Management EWM Specialist')
        ->and(collect($blogs)->pluck('title'))->toContain('Top 50 SAP EWM Interview Questions and Answers');
});
