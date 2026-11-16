<?php

use App\Models\Course;
use App\Models\TrustItem;
use App\Models\User;
use App\Services\TrustProofService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

test('1. reusable proof record mapped across multiple courses appears on each landing page', function () {
    $courseA = Course::factory()->create([
        'name' => 'SAP FICO Deep Dive',
        'slug' => 'sap-fico-deep-dive',
        'category' => 'sap-erp',
        'published_at' => now()->subDay(),
    ]);

    $courseB = Course::factory()->create([
        'name' => 'SAP MM Supply Chain Mastery',
        'slug' => 'sap-mm-supply-chain-mastery',
        'category' => 'sap-erp',
        'published_at' => now()->subDay(),
    ]);

    $item = TrustItem::create([
        'type' => TrustItem::TYPE_PLACEMENT,
        'title' => 'Vikas Malhotra Placement',
        'subtitle' => 'Associate Consultant',
        'description' => 'Successfully hired at Deloitte with a 14 LPA package.',
        'content_category' => 'SAP ERP',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'metadata' => [
            'student_name' => 'Vikas Malhotra',
            'company' => 'Deloitte',
            'package' => '14 LPA',
        ],
        'is_verified' => true,
        'is_published' => true,
        'display_priority' => 10,
    ]);

    $item->courses()->sync([
        $courseA->id => ['sort_order' => 10],
        $courseB->id => ['sort_order' => 20],
    ]);

    // Check Course A
    $resA = $this->get(route('courses.show', $courseA));
    $resA->assertOk();
    $resA->assertSee('Vikas Malhotra Placement');
    $resA->assertSee('14 LPA');

    // Check Course B
    $resB = $this->get(route('courses.show', $courseB));
    $resB->assertOk();
    $resB->assertSee('Vikas Malhotra Placement');
    $resB->assertSee('14 LPA');
});

test('2. course-specific proof record appears only on its assigned course', function () {
    $courseA = Course::factory()->create([
        'name' => 'AWS Cloud Solutions Architect Track',
        'slug' => 'aws-cloud-solutions-architect-track',
        'category' => 'cloud-computing',
        'published_at' => now()->subDay(),
    ]);

    $courseB = Course::factory()->create([
        'name' => 'Salesforce Admin Course',
        'slug' => 'salesforce-admin-course',
        'category' => 'salesforce',
        'published_at' => now()->subDay(),
    ]);

    $awsProject = TrustItem::create([
        'type' => TrustItem::TYPE_PROJECT,
        'title' => 'Multi-Region High Availability AWS Architecture',
        'subtitle' => 'Enterprise Cloud Capstone',
        'description' => 'Deployed automated VPC peering, Terraform IAC, and Aurora Serverless clusters.',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'metadata' => [
            'domain' => 'Cloud Infrastructure',
            'tech_stack' => 'AWS, Terraform, Kubernetes',
            'duration' => '6 Weeks',
        ],
        'is_verified' => true,
        'is_published' => true,
        'display_priority' => 50,
    ]);
    $awsProject->courses()->sync([$courseA->id => ['sort_order' => 10]]);

    // Appears on Course A
    $resA = $this->get(route('courses.show', $courseA));
    $resA->assertOk();
    $resA->assertSee('Multi-Region High Availability AWS Architecture');

    // Does NOT appear on Course B
    $resB = $this->get(route('courses.show', $courseB));
    $resB->assertOk();
    $resB->assertDontSee('Multi-Region High Availability AWS Architecture');
});

test('3. global records appear across courses when include_global is true and disappear when false', function () {
    $courseA = Course::factory()->create([
        'name' => 'Global Inheriting Course',
        'slug' => 'global-inheriting-course',
        'category' => 'sap-erp',
        'published_at' => now()->subDay(),
        'trust_config' => [
            'include_global' => true,
        ],
    ]);

    $courseB = Course::factory()->create([
        'name' => 'Global Opt-Out Course',
        'slug' => 'global-opt-out-course',
        'category' => 'sap-erp',
        'published_at' => now()->subDay(),
        'trust_config' => [
            'include_global' => false,
        ],
    ]);

    $globalAward = TrustItem::create([
        'type' => TrustItem::TYPE_AWARD,
        'title' => 'National Enterprise Training Excellence Trophy 2026',
        'subtitle' => 'EdTech Federation of India',
        'description' => 'Awarded for exceptional learner placement rates.',
        'scope_type' => TrustItem::SCOPE_GLOBAL,
        'metadata' => [
            'issuing_organization' => 'EdTech Federation of India',
            'year' => '2026',
        ],
        'is_verified' => true,
        'is_published' => true,
        'display_priority' => 90,
    ]);

    // Course A includes global
    $resA = $this->get(route('courses.show', $courseA));
    $resA->assertOk();
    $resA->assertSee('National Enterprise Training Excellence Trophy 2026');

    // Course B excludes global
    $resB = $this->get(route('courses.show', $courseB));
    $resB->assertOk();
    $resB->assertDontSee('National Enterprise Training Excellence Trophy 2026');
});

test('4. disabled section in trust_config does not appear publicly', function () {
    $course = Course::factory()->create([
        'name' => 'Workday HCM Certification Prep',
        'slug' => 'workday-hcm-certification-prep',
        'category' => 'workday',
        'published_at' => now()->subDay(),
        'trust_config' => [
            'include_global' => true,
            'sections' => [
                'placements' => ['enabled' => false, 'limit' => 6, 'mode' => 'auto'],
            ],
        ],
    ]);

    $placement = TrustItem::create([
        'type' => TrustItem::TYPE_PLACEMENT,
        'title' => 'Pooja Hegde Workday Hire',
        'subtitle' => 'Workday Analyst',
        'description' => 'Hired at Accenture.',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'is_verified' => true,
        'is_published' => true,
    ]);
    $placement->courses()->sync([$course->id => ['sort_order' => 10]]);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    // Neither the placement student name nor the section header should be visible
    $response->assertDontSee('Pooja Hegde Workday Hire');
    $response->assertDontSee('CAREER IMPACT & PLACEMENTS');
});

test('5. empty section without eligible records is completely hidden', function () {
    $course = Course::factory()->create([
        'name' => 'Data Science Analytics Masterclass',
        'slug' => 'data-science-analytics-masterclass',
        'category' => 'data-science-analytics',
        'published_at' => now()->subDay(),
        'trust_config' => [
            'include_global' => false, // No global fallback
        ],
    ]);

    // Ensure no video stories are linked
    TrustItem::where('type', TrustItem::TYPE_VIDEO_STORY)->delete();

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertDontSee('VIDEO TESTIMONIALS');
    $response->assertDontSee('Watch Real Student Stories');
});

test('6. updating central trust record propagates automatically to all mapped courses with cache invalidation', function () {
    $course = Course::factory()->create([
        'name' => 'Cyber Security Defense Specialization',
        'slug' => 'cyber-security-defense-specialization',
        'category' => 'cyber-security',
        'published_at' => now()->subDay(),
    ]);

    $partner = TrustItem::create([
        'type' => TrustItem::TYPE_PARTNER,
        'title' => 'Original Fortinet Partner 100',
        'subtitle' => 'Global Security Partner',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'display_priority' => 200,
        'is_verified' => true,
        'is_published' => true,
    ]);
    $partner->courses()->sync([$course->id => ['sort_order' => 10]]);

    // 1st request caches the response
    $res1 = $this->get(route('courses.show', $course));
    $res1->assertOk();
    $res1->assertSee('Original Fortinet Partner 100');

    // Admin updates the partner
    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->put(route('admin.trust.update', $partner), [
        'type' => TrustItem::TYPE_PARTNER,
        'title' => 'Updated Fortinet Elite Global Security 2026',
        'subtitle' => 'Strategic Alliance Partner',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'course_ids' => [$course->id],
        'display_priority' => 200,
        'is_verified' => true,
        'is_published' => true,
    ]);

    // 2nd request sees updated title immediately
    $res2 = $this->get(route('courses.show', $course));
    $res2->assertOk();
    $res2->assertSee('Updated Fortinet Elite Global Security 2026');
    $res2->assertDontSee('Original Fortinet Partner 100');
});

test('7. unpublished or unverified records never appear publicly', function () {
    $course = Course::factory()->create([
        'name' => 'HR Management Specialist Track',
        'slug' => 'hr-management-specialist-track',
        'category' => 'hr-management',
        'published_at' => now()->subDay(),
    ]);

    $unpublished = TrustItem::create([
        'type' => TrustItem::TYPE_TESTIMONIAL,
        'title' => 'Secret Unpublished Reviewer',
        'description' => 'Draft review that should not leak.',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'is_verified' => true,
        'is_published' => false,
    ]);
    $unpublished->courses()->sync([$course->id => ['sort_order' => 10]]);

    $unverified = TrustItem::create([
        'type' => TrustItem::TYPE_TESTIMONIAL,
        'title' => 'Unverified Reviewer Claim',
        'description' => 'Unverified review that should not show.',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'is_verified' => false,
        'is_published' => true,
    ]);
    $unverified->courses()->sync([$course->id => ['sort_order' => 20]]);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertDontSee('Secret Unpublished Reviewer');
    $response->assertDontSee('Unverified Reviewer Claim');
});

test('8. course-level section limits restrict output count', function () {
    $course = Course::factory()->create([
        'name' => 'Foreign Languages German A1 A2',
        'slug' => 'foreign-languages-german-a1-a2',
        'category' => 'foreign-languages',
        'published_at' => now()->subDay(),
        'trust_config' => [
            'include_global' => false,
            'sections' => [
                'placements' => ['enabled' => true, 'limit' => 2, 'mode' => 'auto'],
            ],
        ],
    ]);

    for ($i = 1; $i <= 5; $i++) {
        $p = TrustItem::create([
            'type' => TrustItem::TYPE_PLACEMENT,
            'title' => "German Translator Placement #{$i}",
            'subtitle' => 'Language Specialist',
            'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
            'display_priority' => 100 - $i,
            'is_verified' => true,
            'is_published' => true,
        ]);
        $p->courses()->sync([$course->id => ['sort_order' => $i]]);
    }

    $records = app(TrustProofService::class)->getRecordsForCourse($course);
    expect($records['placements'])->toHaveCount(2);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('German Translator Placement #1');
    $response->assertSee('German Translator Placement #2');
    $response->assertDontSee('German Translator Placement #3');
    $response->assertDontSee('German Translator Placement #4');
});

test('9. manual override mapping displays specified items only', function () {
    $course = Course::factory()->create([
        'name' => 'Digital Marketing Growth Course',
        'slug' => 'digital-marketing-growth-course',
        'category' => 'digital-marketing',
        'published_at' => now()->subDay(),
    ]);

    $itemA = TrustItem::create([
        'type' => TrustItem::TYPE_TESTIMONIAL,
        'title' => 'Item A Testimonial',
        'scope_type' => TrustItem::SCOPE_GLOBAL,
        'is_verified' => true,
        'is_published' => true,
    ]);

    $itemB = TrustItem::create([
        'type' => TrustItem::TYPE_TESTIMONIAL,
        'title' => 'Item B Handpicked Testimonial',
        'scope_type' => TrustItem::SCOPE_GLOBAL,
        'is_verified' => true,
        'is_published' => true,
    ]);

    // Force manual override on course
    $course->update([
        'trust_config' => [
            'include_global' => true,
            'sections' => [
                'testimonials' => ['enabled' => true, 'limit' => 10, 'mode' => 'manual'],
            ],
            'manual_overrides' => [
                'testimonials' => [$itemB->id],
            ],
        ],
    ]);

    $records = app(TrustProofService::class)->getRecordsForCourse($course);
    expect($records['testimonials'])->toHaveCount(1);
    expect($records['testimonials'][0]->id)->toBe($itemB->id);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Item B Handpicked Testimonial');
    $response->assertDontSee('Item A Testimonial');
});

test('10. admin can CRUD trust items, filter by type/status, and toggle status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $course = Course::first();

    // Index & filter
    $this->actingAs($admin)
        ->get(route('admin.trust.index', ['type' => TrustItem::TYPE_PLACEMENT]))
        ->assertOk()
        ->assertSee('Trust &amp; Proof Library', false);

    // Create view
    $this->actingAs($admin)
        ->get(route('admin.trust.create'))
        ->assertOk()
        ->assertSee('Add Trust &amp; Proof Record', false);

    // Store
    $storeRes = $this->actingAs($admin)->post(route('admin.trust.store'), [
        'type' => TrustItem::TYPE_TRAINER,
        'title' => 'Dr. Arvind Swaminathan',
        'subtitle' => 'Lead Enterprise Advisor',
        'description' => '20+ years of SAP ERP transformation experience.',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'course_ids' => [$course->id],
        'display_priority' => 95,
        'is_verified' => '1',
        'is_published' => '1',
        'metadata' => [
            'designation' => 'Principal Enterprise Advisor @ IBM',
            'experience_years' => '20+ Years',
        ],
    ]);
    $storeRes->assertRedirect(route('admin.trust.index', ['type' => TrustItem::TYPE_TRAINER]));

    $created = TrustItem::where('title', 'Dr. Arvind Swaminathan')->first();
    expect($created)->not->toBeNull();
    expect($created->courses->pluck('id')->toArray())->toContain($course->id);

    // Edit view
    $this->actingAs($admin)
        ->get(route('admin.trust.edit', $created))
        ->assertOk()
        ->assertSee('Edit Expert Trainers');

    // Toggle status
    $this->actingAs($admin)
        ->patch(route('admin.trust.toggle-status', $created), ['field' => 'is_published'])
        ->assertRedirect();
    expect($created->fresh()->is_published)->toBeFalse();

    // Destroy
    $delRes = $this->actingAs($admin)->delete(route('admin.trust.destroy', $created));
    $delRes->assertRedirect(route('admin.trust.index'));
    expect(TrustItem::where('title', 'Dr. Arvind Swaminathan')->exists())->toBeFalse();
});

test('15. cache serialization safety prevents __PHP_Incomplete_Class and 500 errors on landing page', function () {
    $course = Course::factory()->create([
        'name' => 'SAP MM Materials Management',
        'slug' => 'sap-mm-materials-management-test',
        'category' => 'sap-erp',
        'published_at' => now()->subDay(),
    ]);

    $service = app(TrustProofService::class);

    // Create a partner trust item with high priority
    $partner = TrustItem::create([
        'type' => TrustItem::TYPE_PARTNER,
        'title' => 'Accenture Global',
        'subtitle' => 'Hiring Partner',
        'scope_type' => TrustItem::SCOPE_SELECTED_COURSES,
        'is_verified' => true,
        'is_published' => true,
        'display_priority' => 100,
    ]);
    $partner->courses()->sync([$course->id]);

    // 1. Fetching records should populate the cache with raw arrays (safe for serializable_classes = false)
    $records1 = $service->getRecordsForCourse($course);
    expect($records1['partners'])->toBeInstanceOf(Collection::class);
    expect($records1['partners']->count())->toBeGreaterThanOrEqual(1);
    expect($records1['partners']->pluck('title'))->toContain('Accenture Global');

    // 2. Second fetch should hit cache safely
    $records2 = $service->getRecordsForCourse($course);
    expect($records2['partners'])->toBeInstanceOf(Collection::class);
    expect($records2['partners']->count())->toBeGreaterThanOrEqual(1);
    expect($records2['partners']->pluck('title'))->toContain('Accenture Global');

    // 3. Directly poison the cache with a fake __PHP_Incomplete_Class (simulating old incompatible cache)
    $incompleteObj = unserialize('O:22:"NonExistentLegacyClass":0:{}', ['allowed_classes' => false]);
    Cache::put($service->getCacheKey($course->id), [
        'partners' => [$incompleteObj],
        'placements' => [$incompleteObj],
        'testimonials' => 'invalid_string',
    ], 3600);

    // 4. getRecordsForCourse should detect corruption, bypass, and return valid fresh normalized records
    $recordsRecovered = $service->getRecordsForCourse($course);
    expect($recordsRecovered['partners'])->toBeInstanceOf(Collection::class);
    expect($recordsRecovered['partners']->count())->toBeGreaterThanOrEqual(1);
    expect($recordsRecovered['partners']->pluck('title'))->toContain('Accenture Global');
    expect($recordsRecovered['testimonials'])->toBeInstanceOf(Collection::class);

    // 5. Landing page must render HTTP 200 without TypeError: count()
    $response = $this->get(route('courses.show', $course));
    $response->assertOk();
    $response->assertSee('Accenture Global');

    // 6. Test course with NO trust records: must render HTTP 200 and not throw
    $emptyCourse = Course::factory()->create([
        'name' => 'Empty Trust Course',
        'slug' => 'empty-trust-course-test',
        'category' => 'sap-erp',
        'published_at' => now()->subDay(),
    ]);
    $emptyResponse = $this->get(route('courses.show', $emptyCourse));
    $emptyResponse->assertOk();

    // 7. Verify /courses/sap-mm-training returns HTTP 200
    $sapMmCourse = Course::where('slug', 'sap-mm-training')->first();
    if ($sapMmCourse) {
        $mmResponse = $this->get('/courses/sap-mm-training');
        $mmResponse->assertOk();
    }
});
