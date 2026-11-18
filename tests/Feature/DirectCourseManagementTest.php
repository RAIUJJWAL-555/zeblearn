<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

test('admin can directly create a course as draft without approval workflow', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $payload = [
        'name' => 'SAP Extended Warehouse Management',
        'slug' => 'sap-ewm-training',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Comprehensive SAP EWM logistics and inventory management training.',
        'description' => 'Detailed full curriculum markdown and training modules.',
        'duration' => '3 Months',
        'modules_count' => 10,
        'delivery_mode' => 'Live Online Training',
        'fee' => 35000,
        'original_fee' => 50000,
        'discount_percent' => 30,
        'is_published' => '0',
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.courses.store'), $payload);

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('success');

    $course = Course::where('slug', 'sap-ewm-training')->first();
    expect($course)->not->toBeNull()
        ->and($course->name)->toBe('SAP Extended Warehouse Management')
        ->and($course->fee)->toBe(35000)
        ->and($course->published_at)->toBeNull()
        ->and($course->isPublished())->toBeFalse()
        ->and($course->isDraft())->toBeTrue();
});

test('admin can directly edit all course fields', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $course = Course::factory()->create([
        'name' => 'Original SAP Course',
        'slug' => 'original-sap-course',
        'fee' => 20000,
        'published_at' => null,
    ]);

    $updatePayload = [
        'name' => 'Updated SAP Supply Chain Track',
        'slug' => 'updated-sap-supply-chain-track',
        'category' => 'sap-erp',
        'status' => 'available',
        'short_description' => 'Updated description for supply chain logistics.',
        'description' => 'Updated full syllabus content.',
        'duration' => '4 Months',
        'modules_count' => 16,
        'delivery_mode' => 'Hybrid Classroom & Live',
        'fee' => 42000,
        'original_fee' => 60000,
        'discount_percent' => 30,
        'seo_title' => 'Best SAP Supply Chain Training | ZebLearn',
        'seo_description' => 'Master SAP supply chain operations with certification.',
        'is_published' => '0',
    ];

    $response = $this->actingAs($admin)
        ->put(route('admin.courses.update', $course), $updatePayload);

    $response->assertRedirect(route('admin.courses.index'));
    $response->assertSessionHas('success');

    $course->refresh();
    expect($course->name)->toBe('Updated SAP Supply Chain Track')
        ->and($course->slug)->toBe('updated-sap-supply-chain-track')
        ->and($course->fee)->toBe(42000)
        ->and($course->duration)->toBe('4 Months')
        ->and($course->modules_count)->toBe(16)
        ->and($course->seo_title)->toBe('Best SAP Supply Chain Training | ZebLearn');
});

test('admin can directly publish a course live and unpublish it back to draft', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $course = Course::factory()->create([
        'name' => 'Direct Publish Test Course',
        'slug' => 'direct-publish-test-course',
        'published_at' => null,
    ]);

    expect($course->isPublished())->toBeFalse();

    // 1. Direct Publish
    $publishResponse = $this->actingAs($admin)
        ->patch(route('admin.courses.publish', $course));

    $publishResponse->assertSessionHas('success');
    $course->refresh();

    expect($course->published_at)->not->toBeNull()
        ->and($course->isPublished())->toBeTrue()
        ->and($course->isDraft())->toBeFalse();

    // 2. Direct Unpublish
    $unpublishResponse = $this->actingAs($admin)
        ->patch(route('admin.courses.unpublish', $course));

    $unpublishResponse->assertSessionHas('success');
    $course->refresh();

    expect($course->published_at)->toBeNull()
        ->and($course->isPublished())->toBeFalse()
        ->and($course->isDraft())->toBeTrue();
});

test('public visitors can view published courses but receive 404 for draft courses', function () {
    $publishedCourse = Course::factory()->published()->create([
        'name' => 'Public Live Course',
        'slug' => 'public-live-course',
    ]);

    $draftCourse = Course::factory()->create([
        'name' => 'Private Draft Course',
        'slug' => 'private-draft-course',
        'published_at' => null,
    ]);

    // Public visitor views published course -> 200 OK
    $resPublished = $this->get(route('courses.show', $publishedCourse));
    $resPublished->assertOk();
    $resPublished->assertSee('Public Live Course');

    // Public visitor views draft course -> 404 Not Found
    $resDraft = $this->get(route('courses.show', $draftCourse));
    $resDraft->assertNotFound();

    // Admin can preview / view the draft course
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $resAdmin = $this->actingAs($admin)->get(route('courses.show', $draftCourse));
    $resAdmin->assertOk();
    $resAdmin->assertSee('Private Draft Course');
});

test('course editing preserves existing student enrollments and certificates', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $student = User::factory()->create(['role' => User::ROLE_STUDENT]);

    $course = Course::factory()->published()->create([
        'name' => 'Enrollment Safe Course',
        'slug' => 'enrollment-safe-course',
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'CERT-SAFE-12345',
        'issued_at' => now(),
        'verification_hash' => hash('sha256', 'CERT-SAFE-12345'),
        'status' => 'valid',
    ]);

    // Update course details
    $this->actingAs($admin)
        ->put(route('admin.courses.update', $course), [
            'name' => 'Enrollment Safe Course Renamed',
            'slug' => 'enrollment-safe-course',
            'category' => 'sap-erp',
            'status' => 'available',
            'short_description' => 'Updated short description.',
            'is_published' => '1',
        ]);

    expect(Enrollment::where('id', $enrollment->id)->exists())->toBeTrue()
        ->and(Certificate::where('id', $certificate->id)->exists())->toBeTrue()
        ->and(Enrollment::find($enrollment->id)->course_id)->toBe($course->id)
        ->and(Certificate::find($certificate->id)->course_id)->toBe($course->id);
});
