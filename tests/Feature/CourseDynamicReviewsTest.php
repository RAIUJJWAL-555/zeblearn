<?php

use App\Models\Course;
use App\Models\CourseTestimonial;
use App\Models\User;

test('course model provides dynamic reviews count from database and formatted display', function () {
    $course = Course::factory()->create([
        'rating' => 4.9,
        'ratings_count' => 18500,
        'reviews_count' => 15200,
    ]);

    expect($course->dynamic_reviews_count)->toBe(15200)
        ->and($course->display_reviews_count)->toBe('15,200')
        ->and($course->display_rating)->toBe('4.9');
});

test('course dynamic reviews count falls back safely when reviews_count is not explicitly set', function () {
    $course = Course::factory()->create([
        'ratings_count' => 10000,
        'reviews_count' => null,
    ]);

    // Should compute ~82% of ratings_count
    expect($course->dynamic_reviews_count)->toBe(8200)
        ->and($course->display_reviews_count)->toBe('8,200');
});

test('course card on courses catalog renders dynamic review count from db', function () {
    $course = Course::factory()->available()->create([
        'name' => 'SAP Extended Warehouse Management Masterclass',
        'slug' => 'sap-ewm-masterclass',
        'rating' => 4.9,
        'reviews_count' => 6450,
    ]);

    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee('SAP Extended Warehouse Management Masterclass');
    $response->assertSee('(6,450 reviews)');
});

test('courses directory page renders verified student reviews section', function () {
    $response = $this->get(route('courses.index'));

    $response->assertOk();
    $response->assertSee('WHAT OUR', false);
    $response->assertSee('STUDENTS SAY', false);
    $response->assertSee('VERIFIED REVIEWS', false);
    $response->assertSee('Read genuine reviews from learners', false);
});

test('course landing page displays dynamic reviews in hero and reviews section', function () {
    $course = Course::factory()->available()->create([
        'name' => 'SAP FICO Deep Dive Certification',
        'slug' => 'sap-fico-deep-dive',
        'rating' => 4.9,
        'reviews_count' => 14850,
        'ratings_count' => 18200,
    ]);

    // Attach course-specific database testimonials
    CourseTestimonial::factory()->create([
        'course_id' => $course->id,
        'student_name' => 'Kunal Kashyap',
        'student_title_or_company' => 'Lead SAP Consultant @ PwC',
        'review_text' => 'Outstanding ledger reconciliation training. Direct sandbox practice made passing the SAP certification seamless.',
        'rating' => 5.0,
        'is_verified' => true,
        'is_published' => true,
    ]);

    $response = $this->get(route('courses.show', $course));

    $response->assertOk();
    // Hero metrics
    $response->assertSee('(14,850 verified reviews)');
    // Reviews section & dynamic metrics bar
    $response->assertSee('Verified Student Reviews');
    $response->assertSee('Over <strong>14,850 verified reviews</strong>', false);
    $response->assertSee('14,850+');
    $response->assertSee('Total Verified Reviews');
    $response->assertSee('98% Recommendation Rate');
    // Real student testimonial from DB
    $response->assertSee('Kunal Kashyap');
    $response->assertSee('Lead SAP Consultant @ PwC');
    $response->assertSee('Outstanding ledger reconciliation training.');
});

test('admin can update course rating, ratings_count, and reviews_count in database', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $course = Course::factory()->create([
        'name' => 'SAP Basis Administration',
        'slug' => 'sap-basis-administration',
        'rating' => 4.7,
        'ratings_count' => 5000,
        'reviews_count' => 4100,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.courses.update', $course), [
        'name' => 'SAP Basis Administration (Enterprise Edition)',
        'slug' => $course->slug,
        'category' => $course->category,
        'status' => 'available',
        'short_description' => 'Updated system administration curriculum.',
        'rating' => 4.9,
        'ratings_count' => 9500,
        'reviews_count' => 8200,
        'fee' => 35000,
    ]);

    $response->assertRedirect(route('admin.courses.index'));
    $course->refresh();

    expect((float) $course->rating)->toBe(4.9)
        ->and($course->ratings_count)->toBe(9500)
        ->and($course->reviews_count)->toBe(8200)
        ->and($course->display_reviews_count)->toBe('8,200');
});

test('learner can submit a review on a course which saves to database and increments reviews_count', function () {
    $course = Course::factory()->create([
        'name' => 'SAP MM S/4HANA Sourcing',
        'slug' => 'sap-mm-s4hana-sourcing',
        'reviews_count' => 120,
    ]);

    $response = $this->post(route('courses.reviews.store', $course), [
        'student_name' => 'Aakash Verma',
        'student_title_or_company' => 'Procurement Specialist @ Tata Motors',
        'rating' => 5,
        'review_text' => 'The purchase requisition to invoice workflow was taught thoroughly with practical enterprise scenarios.',
    ]);

    $response->assertRedirect(route('courses.show', $course).'#reviews');
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('course_testimonials', [
        'course_id' => $course->id,
        'student_name' => 'Aakash Verma',
        'student_title_or_company' => 'Procurement Specialist @ Tata Motors',
        'rating' => 5,
        'is_verified' => true,
        'is_published' => true,
    ]);

    $course->refresh();
    expect($course->reviews_count)->toBe(121);
});

test('review submission validates required fields', function () {
    $course = Course::factory()->create([
        'name' => 'SAP SD Order to Cash',
        'slug' => 'sap-sd-order-to-cash',
    ]);

    $response = $this->post(route('courses.reviews.store', $course), [
        'student_name' => '',
        'rating' => 6, // invalid rating
        'review_text' => 'Short', // below minimum 10 chars
    ]);

    $response->assertSessionHasErrors(['student_name', 'rating', 'review_text']);
});

test('course page displays partner marquee, clean certificates showcase, and video testimonials', function () {
    $course = Course::factory()->create([
        'name' => 'SAP FICO on S/4 HANA',
        'slug' => 'sap-fico-on-s-4-hana',
    ]);

    $response = $this->get(route('courses.show', $course));
    $response->assertOk();

    // 1. Hiring Partners Marquee
    $response->assertSee('HIRING PARTNERS');
    $response->assertSee('zeb-marquee-container');
    $response->assertSee('Deloitte');
    $response->assertSee('Accenture');

    // 2. Clean Certificate Showcase (like homepage)
    $response->assertSee('VERIFIED SAP ACADEMY EXCELLENCE');
    $response->assertSee('Inspiring Woman Tech Leader of the Year');
    $response->assertSee('certificate-1.webp');

    // 3. Student Reviews
    $response->assertSee('Verified Student Reviews');
    $response->assertSee('Sumit Singh');

    // 4. Video Testimonials
    $response->assertSee('Watch Genuine Student Success Stories');
    $response->assertSee('Corporate Placement at Accenture');

    // 5. Route /courses/sap-fico-training works cleanly
    $ficoResponse = $this->get(route('courses.fico'));
    $ficoResponse->assertOk();
    $ficoResponse->assertSee('VERIFIED SAP ACADEMY EXCELLENCE');
    $ficoResponse->assertSee('zeb-marquee-container');
});
