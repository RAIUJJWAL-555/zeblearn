<?php

use App\Models\Course;
use App\Models\FormSubmission;
use App\Models\User;

test('public form submission successfully stores in database with json metadata', function () {
    $course = Course::factory()->published()->create([
        'name' => 'SAP FICO Financial Accounting',
        'slug' => 'sap-fico-financial-accounting',
    ]);

    $payload = [
        'form_type' => 'book_demo',
        'form_name' => 'Free Demo Booking',
        'name' => 'John Doe',
        'phone' => '9876543210',
        'email' => 'john.doe@example.com',
        'course_name' => $course->name,
        'message' => 'Looking for weekend batch.',
        'preferred_date' => 'Tomorrow',
        'preferred_time' => '11:00 AM',
        'background' => 'Non-IT',
        'source_url' => 'https://sap.zeblearn.com/book-demo',
    ];

    $response = $this->postJson(route('forms.submit'), $payload);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('form_submissions', [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'form_type' => 'book_demo',
        'status' => 'new',
    ]);

    $submission = FormSubmission::where('email', 'john.doe@example.com')->first();
    expect($submission)->not->toBeNull()
        ->and($submission->additional_data)->toBeArray()
        ->and($submission->additional_data['preferred_time'])->toBe('11:00 AM');
});

test('honeypot triggers rejection status', function () {
    $payload = [
        'form_type' => 'contact',
        'name' => 'Spam Bot',
        'phone' => '1234567890',
        'email' => 'bot@spammer.com',
        'website_url_hp' => 'http://spam-link.ru',
    ];

    $response = $this->postJson(route('forms.submit'), $payload);

    $response->assertCreated();

    $this->assertDatabaseHas('form_submissions', [
        'name' => 'Spam Bot',
        'status' => 'rejected',
    ]);
});

test('form submission fails validation without phone and email', function () {
    $payload = [
        'form_type' => 'contact',
        'name' => 'No Contact Person',
    ];

    $response = $this->postJson(route('forms.submit'), $payload);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['phone']);
});

test('duplicate submission within 30 seconds returns previous record without creating duplicate', function () {
    $payload = [
        'form_type' => 'contact',
        'name' => 'Double Clicker',
        'phone' => '9998887776',
        'email' => 'double@example.com',
    ];

    $response1 = $this->postJson(route('forms.submit'), $payload);
    $response1->assertCreated();

    $response2 = $this->postJson(route('forms.submit'), $payload);
    $response2->assertOk();

    $count = FormSubmission::where('phone', '9998887776')->count();
    expect($count)->toBe(1);
});

test('inbound lead attribution webhook syncs into form_submissions', function () {
    $payload = [
        'name' => 'Lead Attribution Test',
        'phone' => '9112233445',
        'email' => 'lead.attr@example.com',
        'cta_type' => 'hero_demo',
        'source_url' => 'https://sap.zeblearn.com/',
        'utm_source' => 'google_ads',
        'utm_campaign' => 'sap_fico_2026',
    ];

    $response = $this->postJson(route('leads.store'), $payload);
    $response->assertCreated();

    $this->assertDatabaseHas('form_submissions', [
        'name' => 'Lead Attribution Test',
        'form_type' => 'hero_demo',
        'utm_source' => 'google_ads',
    ]);
});

test('admin can access form submissions dashboard, filter, and view detail', function () {
    $admin = User::factory()->admin()->create();

    $sub = FormSubmission::create([
        'form_type' => 'course_enquiry',
        'form_name' => 'Fee Calculator',
        'name' => 'Candidate Alpha',
        'phone' => '9870000001',
        'email' => 'alpha@test.com',
        'status' => 'new',
        'submitted_at' => now(),
    ]);

    // Guest cannot view admin index
    $this->get(route('admin.form-submissions.index'))
        ->assertRedirect(route('login'));

    // Admin can view index and detail
    $this->actingAs($admin)
        ->get(route('admin.form-submissions.index'))
        ->assertOk()
        ->assertSee('Candidate Alpha');

    $this->actingAs($admin)
        ->get(route('admin.form-submissions.show', $sub))
        ->assertOk()
        ->assertSee('Candidate Alpha')
        ->assertSee('Fee Calculator');
});

test('admin can update status, add notes, and export csv', function () {
    $admin = User::factory()->admin()->create();

    $sub = FormSubmission::create([
        'form_type' => 'contact',
        'form_name' => 'Contact Us',
        'name' => 'Candidate Beta',
        'phone' => '9870000002',
        'email' => 'beta@test.com',
        'status' => 'new',
        'submitted_at' => now(),
    ]);

    // Update status
    $this->actingAs($admin)
        ->patch(route('admin.form-submissions.status', $sub), [
            'status' => 'qualified',
        ])
        ->assertRedirect();

    expect($sub->fresh()->status)->toBe('qualified');

    // Update notes
    $this->actingAs($admin)
        ->patch(route('admin.form-submissions.notes', $sub), [
            'notes' => 'Spoke to candidate, highly interested in SAP MM.',
        ])
        ->assertRedirect();

    expect($sub->fresh()->admin_notes)->toBe('Spoke to candidate, highly interested in SAP MM.');

    // Export CSV
    $csvResponse = $this->actingAs($admin)
        ->get(route('admin.form-submissions.export'));

    $csvResponse->assertOk()
        ->assertHeader('content-type', 'text/csv; charset=UTF-8');
});
