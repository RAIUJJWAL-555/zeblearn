<?php

use App\Models\Certificate;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Support\Facades\Artisan;

function setupCertificateTestEnvironment(): array
{
    $student = User::factory()->create([
        'role' => 'student',
        'status' => 'active',
        'email' => 'student_cert_'.uniqid().'@example.com',
    ]);

    $otherStudent = User::factory()->create([
        'role' => 'student',
        'status' => 'active',
        'email' => 'other_student_'.uniqid().'@example.com',
    ]);

    $admin = User::factory()->create([
        'role' => 'admin',
        'status' => 'active',
        'email' => 'admin_cert_'.uniqid().'@example.com',
    ]);

    $course = Course::create([
        'name' => 'Fullstack Laravel Mastery',
        'slug' => 'fullstack-laravel-mastery-'.uniqid(),
        'category' => 'software-development',
        'status' => 'available',
        'short_description' => 'Comprehensive enterprise Laravel development course.',
        'fee' => 35000,
        'published_at' => now(),
    ]);

    $module1 = CourseModule::create([
        'course_id' => $course->id,
        'title' => 'Module 1: Eloquent & Architecture',
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson1 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'Lesson 1.1: Database Relationships',
        'content_type' => 'text',
        'content' => 'Deep dive into Eloquent ORM relationships.',
        'duration_minutes' => 20,
        'sort_order' => 1,
        'status' => 'published',
    ]);

    $lesson2 = Lesson::create([
        'course_module_id' => $module1->id,
        'title' => 'Lesson 1.2: Advanced Query Performance',
        'content_type' => 'text',
        'content' => 'Preventing N+1 queries and index strategies.',
        'duration_minutes' => 25,
        'sort_order' => 2,
        'status' => 'published',
    ]);

    $enrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now()->subDays(5),
        'approved_at' => now()->subDays(4),
    ]);

    return compact('student', 'otherStudent', 'admin', 'course', 'module1', 'lesson1', 'lesson2', 'enrollment');
}

test('eligible student receives certificate automatically upon completing final published lesson', function () {
    extract(setupCertificateTestEnvironment());

    // Complete lesson 1
    $this->actingAs($student)
        ->post(route('student.courses.lesson.complete', [$course, $lesson1]))
        ->assertRedirect();

    expect(Certificate::where('enrollment_id', $enrollment->id)->exists())->toBeFalse();

    // Complete final lesson (lesson 2)
    $response = $this->actingAs($student)
        ->post(route('student.courses.lesson.complete', [$course, $lesson2]));

    $response->assertRedirect();
    $response->assertSessionHas('certificate_earned');

    $certificate = Certificate::where('enrollment_id', $enrollment->id)->first();
    expect($certificate)->not->toBeNull()
        ->and($certificate->user_id)->toBe($student->id)
        ->and($certificate->course_id)->toBe($course->id)
        ->and($certificate->certificate_number)->toStartWith('ZEB-'.now()->year.'-')
        ->and($certificate->isValid())->toBeTrue();
});

test('ineligible student with unapproved enrollment does not receive certificate', function () {
    extract(setupCertificateTestEnvironment());

    $enrollment->update(['status' => Enrollment::STATUS_PENDING]);

    $service = app(CertificateService::class);
    $issued = $service->issueCertificate($student, $course, $enrollment);

    expect($issued)->toBeNull()
        ->and(Certificate::where('enrollment_id', $enrollment->id)->exists())->toBeFalse();
});

test('course with zero published lessons does not automatically issue certificates', function () {
    extract(setupCertificateTestEnvironment());

    // Create empty course with 0 lessons
    $emptyCourse = Course::create([
        'name' => 'Empty Course Without Lessons',
        'slug' => 'empty-course-'.uniqid(),
        'category' => 'general',
        'status' => 'available',
        'short_description' => 'Empty course placeholder description.',
        'fee' => 10000,
        'published_at' => now(),
    ]);

    $emptyEnrollment = Enrollment::create([
        'user_id' => $student->id,
        'course_id' => $emptyCourse->id,
        'status' => Enrollment::STATUS_APPROVED,
        'requested_at' => now(),
        'approved_at' => now(),
    ]);

    $service = app(CertificateService::class);
    $isEligible = $service->isEligible($student, $emptyCourse, $emptyEnrollment);
    $issued = $service->issueCertificate($student, $emptyCourse, $emptyEnrollment);

    expect($isEligible)->toBeFalse()
        ->and($issued)->toBeNull()
        ->and(Certificate::where('enrollment_id', $emptyEnrollment->id)->exists())->toBeFalse();
});

test('student cannot receive duplicate certificates for same enrollment (idempotency)', function () {
    extract(setupCertificateTestEnvironment());

    // Mark all lessons completed
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $lesson1->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $lesson2->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    $service = app(CertificateService::class);
    $firstCert = $service->issueCertificate($student, $course, $enrollment);
    expect($firstCert)->not->toBeNull();

    // Call issuance a second time
    $secondCert = $service->issueCertificate($student, $course, $enrollment);
    expect($secondCert->id)->toBe($firstCert->id);

    expect(Certificate::where('enrollment_id', $enrollment->id)->count())->toBe(1);
});

test('student can view their certificates list on /student/certificates', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-TEST0001',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($student)
        ->get(route('student.certificates.index'));

    $response->assertOk()
        ->assertSee('ZEB-2026-TEST0001')
        ->assertSee($course->name);
});

test('student can view single certificate details on /student/certificates/{certificate}', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-TEST0002',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($student)
        ->get(route('student.certificates.show', $certificate));

    $response->assertOk()
        ->assertSee('ZEB-2026-TEST0002')
        ->assertSee($student->name)
        ->assertSee($course->name);
});

test('student cannot view another students certificate details on /student/certificates/{certificate}', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-TEST0003',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($otherStudent)
        ->get(route('student.certificates.show', $certificate));

    $response->assertForbidden();
});

test('student can download their own certificate PDF', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-TEST0004',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($student)
        ->get(route('student.certificates.download', $certificate));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toBe('application/pdf');
    expect($response->headers->get('content-disposition'))->toContain('attachment; filename=ZebLearn_Certificate_ZEB-2026-TEST0004.pdf');
});

test('student cannot download another students certificate PDF (403 forbidden)', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-TEST0005',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($otherStudent)
        ->get(route('student.certificates.download', $certificate));

    $response->assertForbidden();
});

test('public verification page displays certificate details without authentication', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-PUBLIC01',
        'issued_at' => now(),
    ]);

    // Unauthenticated public GET request
    $response = $this->get(route('certificates.verify', 'ZEB-2026-PUBLIC01'));

    $response->assertOk()
        ->assertSee('ZEB-2026-PUBLIC01')
        ->assertSee($student->name)
        ->assertSee($course->name)
        ->assertSee('Valid Credential');
});

test('public verification does not leak sensitive student contact details', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-PRIVACY1',
        'issued_at' => now(),
    ]);

    $response = $this->get(route('certificates.verify', 'ZEB-2026-PRIVACY1'));

    $response->assertOk()
        ->assertDontSee($student->email)
        ->assertDontSee($student->password);
});

test('public verification returns 404 for nonexistent certificate number', function () {
    $response = $this->get(route('certificates.verify', 'NON-EXISTENT-CERT-NUMBER'));

    $response->assertNotFound();
});

test('admin can view certificates list on /admin/certificates with search and filters', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-ADMIN001',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.certificates.index', ['search' => 'ZEB-2026-ADMIN001']));

    $response->assertOk()
        ->assertSee('ZEB-2026-ADMIN001')
        ->assertSee($student->name)
        ->assertSee($course->name);
});

test('admin can view certificate details and inspect completion on /admin/certificates/{certificate}', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-ADMIN002',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.certificates.show', $certificate));

    $response->assertOk()
        ->assertSee('ZEB-2026-ADMIN002')
        ->assertSee($student->name)
        ->assertSee($course->name);
});

test('admin can revoke a certificate with a mandatory revocation reason', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-REVOKE01',
        'issued_at' => now(),
    ]);

    // Validation failure when reason is missing
    $this->actingAs($admin)
        ->post(route('admin.certificates.revoke', $certificate), ['revocation_reason' => ''])
        ->assertSessionHasErrors('revocation_reason');

    expect($certificate->fresh()->isValid())->toBeTrue();

    // Successful revocation with reason
    $response = $this->actingAs($admin)
        ->post(route('admin.certificates.revoke', $certificate), [
            'revocation_reason' => 'Academic misconduct identified during audit.',
        ]);

    $response->assertRedirect(route('admin.certificates.show', $certificate));
    $response->assertSessionHas('success');

    $freshCert = $certificate->fresh();
    expect($freshCert->isRevoked())->toBeTrue()
        ->and($freshCert->revocation_reason)->toBe('Academic misconduct identified during audit.')
        ->and($freshCert->revoked_at)->not->toBeNull();
});

test('revoked certificate displays revoked status on public verification, student, and admin views', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-REVOKE02',
        'issued_at' => now(),
        'revoked_at' => now(),
        'revocation_reason' => 'Violation of certification code of conduct.',
    ]);

    // Public verification page
    $this->get(route('certificates.verify', 'ZEB-2026-REVOKE02'))
        ->assertOk()
        ->assertSee('Revoked Certificate')
        ->assertSee('Violation of certification code of conduct.');

    // Student view
    $this->actingAs($student)
        ->get(route('student.certificates.show', $certificate))
        ->assertOk()
        ->assertSee('Certificate Revocation Notice')
        ->assertSee('Violation of certification code of conduct.');

    // Admin view
    $this->actingAs($admin)
        ->get(route('admin.certificates.show', $certificate))
        ->assertOk()
        ->assertSee('Revoked');
});

test('admin can download any certificate PDF', function () {
    extract(setupCertificateTestEnvironment());

    $certificate = Certificate::create([
        'user_id' => $student->id,
        'course_id' => $course->id,
        'enrollment_id' => $enrollment->id,
        'certificate_number' => 'ZEB-2026-ADMINDL1',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.certificates.download', $certificate));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toBe('application/pdf');
});

test('artisan certificates:issue-eligible command retroactively issues certificates for eligible students', function () {
    extract(setupCertificateTestEnvironment());

    // Mark both lessons completed
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $lesson1->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);
    LessonProgress::create([
        'user_id' => $student->id,
        'lesson_id' => $lesson2->id,
        'status' => LessonProgress::STATUS_COMPLETED,
        'completed_at' => now(),
    ]);

    expect(Certificate::where('enrollment_id', $enrollment->id)->exists())->toBeFalse();

    // Run artisan command
    $exitCode = Artisan::call('certificates:issue-eligible');
    expect($exitCode)->toBe(0);

    expect(Certificate::where('enrollment_id', $enrollment->id)->exists())->toBeTrue();
});
