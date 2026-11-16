<?php

use App\Models\Setting;
use App\Models\SettingAuditLog;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Support\Facades\Cache;

function setupSettingsTestAdmin(): User
{
    return User::factory()->create([
        'name' => 'System Admin',
        'role' => User::ROLE_ADMIN,
        'status' => User::STATUS_ACTIVE,
        'email' => 'admin_settings_'.uniqid().'@example.com',
    ]);
}

function setupSettingsTestStudent(): User
{
    return User::factory()->student()->create([
        'name' => 'Student Learner',
        'status' => User::STATUS_ACTIVE,
        'email' => 'student_settings_'.uniqid().'@example.com',
    ]);
}

function setupSettingsTestInstructor(): User
{
    return User::factory()->instructor()->create([
        'name' => 'Faculty Member',
        'status' => User::STATUS_ACTIVE,
        'email' => 'instructor_settings_'.uniqid().'@example.com',
    ]);
}

beforeEach(function () {
    Cache::flush();
});

test('unauthenticated guests are redirected to login when accessing settings console', function () {
    $response = $this->get(route('admin.settings.index'));
    $response->assertRedirect(route('login'));

    $updateResponse = $this->put(route('admin.settings.update'), [
        '_group' => 'general',
        'site_name' => 'Hacked Site',
        'contact_email' => 'hack@example.com',
    ]);
    $updateResponse->assertRedirect(route('login'));
});

test('students and instructors cannot access settings console or update settings', function () {
    $student = setupSettingsTestStudent();
    $instructor = setupSettingsTestInstructor();

    $this->actingAs($student)
        ->get(route('admin.settings.index'))
        ->assertForbidden();

    $this->actingAs($student)
        ->put(route('admin.settings.update'), ['_group' => 'general'])
        ->assertForbidden();

    $this->actingAs($instructor)
        ->get(route('admin.settings.index'))
        ->assertForbidden();

    $this->actingAs($instructor)
        ->put(route('admin.settings.update'), ['_group' => 'general'])
        ->assertForbidden();
});

test('admin can access settings console and load all configuration tabs', function () {
    $admin = setupSettingsTestAdmin();

    foreach (Setting::GROUPS as $group) {
        $response = $this->actingAs($admin)->get(route('admin.settings.index', ['group' => $group]));
        $response->assertOk();
        $response->assertViewHas('activeTab', $group);
        $response->assertViewHas('settings');
        $response->assertViewHas('recentAuditLogs');
    }
});

test('admin can update general platform settings and audit log is recorded', function () {
    $admin = setupSettingsTestAdmin();

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'general',
        'site_name' => 'ZebLearn Global Enterprise',
        'site_tagline' => 'Accelerating Digital Leadership',
        'contact_email' => 'support.enterprise@zeblearn.com',
        'contact_phone' => '+91 99999 88888',
        'support_url' => 'https://help.zeblearn.com',
        'official_website_url' => 'https://zeblearn.com',
        'footer_copyright' => '© 2026 ZebLearn International. All rights reserved.',
        'default_timezone' => 'Asia/Kolkata',
        'default_currency' => 'INR',
    ]);

    $response->assertRedirect(route('admin.settings.index', ['group' => 'general']));
    $response->assertSessionHas('status');

    $settingService = app(SettingService::class);
    expect($settingService->get('site_name'))->toBe('ZebLearn Global Enterprise')
        ->and($settingService->get('contact_email'))->toBe('support.enterprise@zeblearn.com')
        ->and($settingService->get('default_currency'))->toBe('INR');

    $this->assertDatabaseHas('setting_audit_logs', [
        'user_id' => $admin->id,
        'setting_key' => 'site_name',
        'new_value' => 'ZebLearn Global Enterprise',
    ]);
});

test('branding settings validate hex color format properly', function () {
    $admin = setupSettingsTestAdmin();

    // Invalid hex color should fail
    $invalidResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'branding',
        'brand_primary_color' => 'invalid-color',
        'brand_secondary_color' => '#0A0A0A',
        'brand_accent_color' => '#F8F9FA',
    ]);
    $invalidResponse->assertSessionHasErrors(['brand_primary_color']);

    // Valid hex color should succeed
    $validResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'branding',
        'brand_primary_color' => '#FB6221',
        'brand_secondary_color' => '#1A1A1A',
        'brand_accent_color' => '#FFFFFF',
        'social_twitter' => 'https://x.com/zeblearn',
        'social_linkedin' => 'https://linkedin.com/company/zeblearn',
        'social_youtube' => 'https://youtube.com/@zeblearn',
        'social_instagram' => 'https://instagram.com/zeblearn',
    ]);
    $validResponse->assertRedirect(route('admin.settings.index', ['group' => 'branding']));

    $settingService = app(SettingService::class);
    expect($settingService->get('brand_primary_color'))->toBe('#FB6221')
        ->and($settingService->get('brand_secondary_color'))->toBe('#1A1A1A')
        ->and($settingService->get('social_twitter'))->toBe('https://x.com/zeblearn');
});

test('admin can update course policies and default visibility', function () {
    $admin = setupSettingsTestAdmin();

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'courses',
        'enrollment_enabled' => '1',
        'enrollment_require_admin_approval' => '0',
        'student_registration_enabled' => '1',
        'default_course_visibility' => 'available',
    ]);

    $response->assertRedirect(route('admin.settings.index', ['group' => 'courses']));

    $service = app(SettingService::class);
    expect($service->get('enrollment_enabled'))->toBeTrue()
        ->and($service->get('enrollment_require_admin_approval'))->toBeFalse()
        ->and($service->get('student_registration_enabled'))->toBeTrue()
        ->and($service->get('default_course_visibility'))->toBe('available');
});

test('admin can update certificate issuing authority metadata', function () {
    $admin = setupSettingsTestAdmin();

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'certificates',
        'certificate_organization_name' => 'ZebLearn Institute of Executive Technology',
        'certificate_signatory_name' => 'Prof. Vikramaditya Sen',
        'certificate_signatory_title' => 'Dean of Academic Affairs & Accreditation',
        'certificate_prefix' => 'ZEB-ACAD',
    ]);

    $response->assertRedirect(route('admin.settings.index', ['group' => 'certificates']));

    $service = app(SettingService::class);
    expect($service->get('certificate_organization_name'))->toBe('ZebLearn Institute of Executive Technology')
        ->and($service->get('certificate_signatory_name'))->toBe('Prof. Vikramaditya Sen')
        ->and($service->get('certificate_prefix'))->toBe('ZEB-ACAD');
});

test('admin can update SEO and indexing parameters', function () {
    $admin = setupSettingsTestAdmin();

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'seo',
        'seo_default_title' => 'ZebLearn — Enterprise Upskilling',
        'seo_default_description' => 'World class engineering courses.',
        'seo_canonical_base_url' => 'https://zeblearn.com',
        'seo_allow_indexing' => '1',
    ]);

    $response->assertRedirect(route('admin.settings.index', ['group' => 'seo']));

    $service = app(SettingService::class);
    expect($service->get('seo_default_title'))->toBe('ZebLearn — Enterprise Upskilling')
        ->and($service->get('seo_allow_indexing'))->toBeTrue();
});

test('admin can update email settings and notification toggles', function () {
    $admin = setupSettingsTestAdmin();

    $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'email',
        'mail_from_name' => 'ZebLearn Notifications',
        'mail_from_address' => 'notifications@zeblearn.com',
        'notify_student_enrollment_approved' => '1',
        'notify_student_enrollment_rejected' => '0',
        'notify_student_assignment_graded' => '1',
        'notify_student_certificate_issued' => '1',
        'notify_instructor_course_assigned' => '1',
        'notify_instructor_assignment_submitted' => '0',
        'notify_admin_payment_received' => '1',
        'notify_admin_new_enrollment' => '1',
    ]);

    $response->assertRedirect(route('admin.settings.index', ['group' => 'email']));

    $service = app(SettingService::class);
    expect($service->get('mail_from_name'))->toBe('ZebLearn Notifications')
        ->and($service->get('notify_student_enrollment_rejected'))->toBeFalse()
        ->and($service->get('notify_instructor_assignment_submitted'))->toBeFalse()
        ->and($service->get('notify_admin_payment_received'))->toBeTrue();
});

test('payment secrets are masked in UI and not overwritten by blank or mask submissions', function () {
    $admin = setupSettingsTestAdmin();
    $service = app(SettingService::class);

    // Explicitly set an initial secret key
    $service->set('razorpay_key_secret', 'original_rzp_secret_998877', 'payments', 'string', true);
    $service->set('razorpay_webhook_secret', 'original_webhook_secret_112233', 'payments', 'string', true);

    // 1. Verify secrets are masked in index view
    $indexResponse = $this->actingAs($admin)->get(route('admin.settings.index', ['group' => 'payments']));
    $indexResponse->assertOk();
    $indexSettings = $indexResponse->viewData('settings');
    expect($indexSettings['razorpay_key_secret'])->toBe('••••••••••••••••')
        ->and($indexSettings['razorpay_webhook_secret'])->toBe('••••••••••••••••');

    // 2. Submit form with empty secrets or mask strings (simulating user saving without changing secrets)
    $updateResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'payments',
        'razorpay_enabled' => '1',
        'razorpay_mode' => 'live',
        'razorpay_key_id' => 'rzp_live_new_key_id_4455',
        'razorpay_key_secret' => '••••••••••••••••',
        'razorpay_webhook_secret' => '',
    ]);

    $updateResponse->assertRedirect(route('admin.settings.index', ['group' => 'payments']));

    // Assert that the original secrets were preserved!
    expect($service->get('razorpay_key_secret'))->toBe('original_rzp_secret_998877')
        ->and($service->get('razorpay_webhook_secret'))->toBe('original_webhook_secret_112233')
        ->and($service->get('razorpay_key_id'))->toBe('rzp_live_new_key_id_4455')
        ->and($service->get('razorpay_mode'))->toBe('live');

    // 3. Submit with an actual new secret
    $updateNewSecretResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'payments',
        'razorpay_enabled' => '1',
        'razorpay_mode' => 'live',
        'razorpay_key_id' => 'rzp_live_new_key_id_4455',
        'razorpay_key_secret' => 'brand_new_secret_778899',
        'razorpay_webhook_secret' => '••••••••••••••••',
    ]);

    $updateNewSecretResponse->assertRedirect(route('admin.settings.index', ['group' => 'payments']));
    expect($service->get('razorpay_key_secret'))->toBe('brand_new_secret_778899');
});

test('audit logs redact secret values to prevent credential leakage', function () {
    $admin = setupSettingsTestAdmin();
    $service = app(SettingService::class);

    // Initial value
    $service->set('razorpay_key_secret', 'secret_initial_123', 'payments', 'string', true);

    // Update with new secret
    $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'payments',
        'razorpay_enabled' => '1',
        'razorpay_mode' => 'test',
        'razorpay_key_id' => 'rzp_test_testkey',
        'razorpay_key_secret' => 'super_sensitive_api_key_xyz',
        'razorpay_webhook_secret' => '',
    ]);

    $log = SettingAuditLog::where('setting_key', 'razorpay_key_secret')->latest()->first();
    expect($log)->not->toBeNull()
        ->and($log->old_value)->toBe('[REDACTED]')
        ->and($log->new_value)->toBe('[REDACTED]');
});

test('maintenance mode blocks students and guests with 503 while allowing admin access and login', function () {
    $admin = setupSettingsTestAdmin();
    $student = setupSettingsTestStudent();
    $service = app(SettingService::class);

    // Enable maintenance mode
    $service->set('maintenance_mode_enabled', true, 'maintenance', 'boolean');
    $service->set('maintenance_message', 'Platform upgrade in progress. Check back in 15 minutes.', 'maintenance', 'string');

    // 1. Guest accessing root gets 503 Service Unavailable
    $guestResponse = $this->get('/');
    $guestResponse->assertStatus(503);
    $guestResponse->assertSee('Platform upgrade in progress');

    // 2. Student accessing /student/dashboard gets 503
    $studentResponse = $this->actingAs($student)->get('/student/dashboard');
    $studentResponse->assertStatus(503);

    // 3. Guest accessing /login is ALLOWED (so admins can log in!)
    auth()->logout();
    $loginResponse = $this->get('/login');
    $loginResponse->assertOk();

    // 4. Admin accessing /admin/settings is ALLOWED (bypasses maintenance mode)
    $adminResponse = $this->actingAs($admin)->get(route('admin.settings.index'));
    $adminResponse->assertOk();
    $adminResponse->assertSee('MAINTENANCE MODE ACTIVE');

    // 5. Admin can disable maintenance mode via PUT request
    $disableResponse = $this->actingAs($admin)->put(route('admin.settings.update'), [
        '_group' => 'maintenance',
        'maintenance_mode_enabled' => '0',
        'maintenance_message' => 'System online',
    ]);
    $disableResponse->assertRedirect(route('admin.settings.index', ['group' => 'maintenance']));

    // Assert that maintenance mode is turned off
    expect($service->get('maintenance_mode_enabled'))->toBeFalse();

    // Now guest accessing / gets 200
    $restoredGuestResponse = $this->get('/');
    $restoredGuestResponse->assertOk();
});
