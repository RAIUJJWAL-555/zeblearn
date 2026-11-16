<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SettingAuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingService
{
    public const CACHE_PREFIX = 'zeblearn_setting:';

    public const SECRET_MASK = '••••••••••••••••';

    /**
     * In-memory cache for fast repeated reads within the same request lifecycle.
     *
     * @var array<string, mixed>
     */
    protected static array $runtimeCache = [];

    /**
     * Authoritative default configuration dictionary.
     *
     * @var array<string, array{value: mixed, group: string, type: string, is_secret: bool, description: string}>
     */
    protected static array $defaults = [
        // 1. General Platform Settings
        'site_name' => ['value' => 'ZebLearn LMS', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Platform name'],
        'site_tagline' => ['value' => 'Enterprise Cloud & Software Training Academy', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Platform tagline'],
        'contact_email' => ['value' => 'enquiry@zeblearn.com', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Primary contact email'],
        'contact_phone' => ['value' => '+91 72778 77778', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Primary contact phone'],
        'support_url' => ['value' => 'https://zeblearn.com/support', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Student helpdesk URL'],
        'official_website_url' => ['value' => 'https://zeblearn.com', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Official website base URL'],
        'footer_copyright' => ['value' => '© 2026 ZebLearn Academy. All rights reserved.', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Footer copyright text'],
        'default_timezone' => ['value' => 'Asia/Kolkata', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Default platform timezone'],
        'default_currency' => ['value' => 'INR', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Default transaction currency'],
        'logo_path' => ['value' => 'logo/logo2-400.webp', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Brand logo asset path'],
        'favicon_path' => ['value' => 'favicon.ico', 'group' => 'general', 'type' => 'string', 'is_secret' => false, 'description' => 'Favicon asset path'],

        // 2. Branding Management
        'brand_primary_color' => ['value' => '#FB6221', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'Primary brand color'],
        'brand_secondary_color' => ['value' => '#0A0A0A', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'Dark neutral background color'],
        'brand_accent_color' => ['value' => '#F8F9FA', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'Light neutral background color'],
        'social_linkedin' => ['value' => 'https://linkedin.com/company/zeblearn', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'LinkedIn profile URL'],
        'social_twitter' => ['value' => 'https://twitter.com/zeblearn', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'Twitter/X profile URL'],
        'social_instagram' => ['value' => 'https://instagram.com/zeblearn', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'Instagram profile URL'],
        'social_youtube' => ['value' => 'https://youtube.com/@zeblearn', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'YouTube channel URL'],
        'social_facebook' => ['value' => 'https://facebook.com/zeblearn', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'Facebook page URL'],
        'social_whatsapp' => ['value' => 'https://wa.me/917277877778', 'group' => 'branding', 'type' => 'string', 'is_secret' => false, 'description' => 'WhatsApp direct business link'],

        // 3. Payment Gateway (Razorpay)
        'razorpay_enabled' => ['value' => true, 'group' => 'payments', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Razorpay gateway enabled'],
        'razorpay_mode' => ['value' => 'test', 'group' => 'payments', 'type' => 'string', 'is_secret' => false, 'description' => 'Gateway environment (test or live)'],
        'razorpay_key_id' => ['value' => '', 'group' => 'payments', 'type' => 'string', 'is_secret' => false, 'description' => 'Razorpay public Key ID'],
        'razorpay_key_secret' => ['value' => '', 'group' => 'payments', 'type' => 'string', 'is_secret' => true, 'description' => 'Razorpay secret key'],
        'razorpay_webhook_secret' => ['value' => '', 'group' => 'payments', 'type' => 'string', 'is_secret' => true, 'description' => 'Razorpay webhook signature secret'],

        // 4. Email & Notification Preferences
        'mail_from_name' => ['value' => 'ZebLearn Academy', 'group' => 'email', 'type' => 'string', 'is_secret' => false, 'description' => 'System mailer sender name'],
        'mail_from_address' => ['value' => 'enquiry@zeblearn.com', 'group' => 'email', 'type' => 'string', 'is_secret' => false, 'description' => 'System mailer sender email'],
        'notify_student_enrollment_approved' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify students when enrollment is approved'],
        'notify_student_enrollment_rejected' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify students when enrollment is rejected'],
        'notify_student_assignment_graded' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify students when assignment is evaluated'],
        'notify_student_certificate_issued' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify students when certificate is issued'],
        'notify_admin_new_enrollment' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify administrators of new student enrollment requests'],
        'notify_admin_payment_received' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify administrators when verified payments occur'],
        'notify_instructor_course_assigned' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify instructors when assigned to a course'],
        'notify_instructor_assignment_submitted' => ['value' => true, 'group' => 'email', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Notify instructors when new assignment submissions are placed'],

        // 5. Course & Enrollment Settings
        'enrollment_enabled' => ['value' => true, 'group' => 'courses', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Public enrollment applications enabled'],
        'enrollment_require_admin_approval' => ['value' => true, 'group' => 'courses', 'type' => 'boolean', 'is_secret' => false, 'description' => 'All enrollments require manual admin approval'],
        'student_registration_enabled' => ['value' => true, 'group' => 'courses', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Allow new student accounts to self-register'],
        'default_course_visibility' => ['value' => 'available', 'group' => 'courses', 'type' => 'string', 'is_secret' => false, 'description' => 'Default status for newly drafted courses'],

        // 6. Certificate Settings
        'certificate_organization_name' => ['value' => 'ZebLearn Academy', 'group' => 'certificates', 'type' => 'string', 'is_secret' => false, 'description' => 'Accrediting organization name on certificates'],
        'certificate_signatory_name' => ['value' => 'Dr. Vikram Malhotra', 'group' => 'certificates', 'type' => 'string', 'is_secret' => false, 'description' => 'Official signatory / registrar full name'],
        'certificate_signatory_title' => ['value' => 'Academic Director & Registrar', 'group' => 'certificates', 'type' => 'string', 'is_secret' => false, 'description' => 'Official signatory designation'],
        'certificate_prefix' => ['value' => 'ZEB', 'group' => 'certificates', 'type' => 'string', 'is_secret' => false, 'description' => 'Certificate serial number prefix (e.g. ZEB)'],

        // 7. SEO & Public Website Settings
        'seo_default_title' => ['value' => 'ZebLearn LMS — Enterprise IT & Cloud Learning Platform', 'group' => 'seo', 'type' => 'string', 'is_secret' => false, 'description' => 'Default page meta title'],
        'seo_default_description' => ['value' => 'Master SAP ERP, Cloud Architecture, Full-Stack Engineering, and enterprise certifications with ZebLearn LMS.', 'group' => 'seo', 'type' => 'string', 'is_secret' => false, 'description' => 'Default meta description'],
        'seo_default_og_image' => ['value' => 'logo/logo2-400.webp', 'group' => 'seo', 'type' => 'string', 'is_secret' => false, 'description' => 'Default Open Graph banner image'],
        'seo_canonical_base_url' => ['value' => 'https://zeblearn.com', 'group' => 'seo', 'type' => 'string', 'is_secret' => false, 'description' => 'Canonical public base URL'],
        'seo_allow_indexing' => ['value' => true, 'group' => 'seo', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Allow search engine indexing (robots index/follow)'],

        // 8. Maintenance & Status
        'maintenance_mode_enabled' => ['value' => false, 'group' => 'maintenance', 'type' => 'boolean', 'is_secret' => false, 'description' => 'Enable platform maintenance mode'],
        'maintenance_message' => ['value' => 'ZebLearn is undergoing scheduled platform maintenance and upgrade. Our student learning systems will resume shortly.', 'group' => 'maintenance', 'type' => 'string', 'is_secret' => false, 'description' => 'Public maintenance notice message'],
    ];

    /**
     * Retrieve a setting by key with type safety and fallback to defaults.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$runtimeCache)) {
            return self::$runtimeCache[$key];
        }

        $cacheKey = self::CACHE_PREFIX.$key;

        try {
            $val = Cache::rememberForever($cacheKey, function () use ($key, $default) {
                try {
                    if (! Schema::hasTable('settings')) {
                        return $default ?? ($this->getDefault($key)['value'] ?? null);
                    }

                    $setting = Setting::where('key', $key)->first();
                    if ($setting) {
                        return $setting->getTypedValue();
                    }
                } catch (\Throwable) {
                    return $default ?? ($this->getDefault($key)['value'] ?? null);
                }

                return $default ?? ($this->getDefault($key)['value'] ?? null);
            });
        } catch (\Throwable) {
            $val = $default ?? ($this->getDefault($key)['value'] ?? null);
        }

        self::$runtimeCache[$key] = $val;

        return $val;
    }

    /**
     * Get a setting definition from defaults.
     *
     * @return array{value: mixed, group: string, type: string, is_secret: bool, description: string}|null
     */
    public function getDefault(string $key): ?array
    {
        return self::$defaults[$key] ?? null;
    }

    /**
     * Retrieve all defaults.
     *
     * @return array<string, array{value: mixed, group: string, type: string, is_secret: bool, description: string}>
     */
    public function getDefaults(): array
    {
        return self::$defaults;
    }

    /**
     * Get all settings in a specific group.
     *
     * @return array<string, mixed>
     */
    public function getGroup(string $group): array
    {
        $groupDefaults = collect(self::$defaults)->filter(fn ($item) => $item['group'] === $group);
        $result = [];

        foreach ($groupDefaults as $key => $meta) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    /**
     * Check if a given string matches the secret masking format.
     */
    public function isSecretMask(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return str_contains($value, '••••') || $value === self::SECRET_MASK;
    }

    /**
     * Mask a secret value for safe display in UI forms.
     */
    public function maskSecret(?string $secret): string
    {
        return ! empty($secret) ? self::SECRET_MASK : '';
    }

    /**
     * Update or create a setting securely.
     * Handles secret masking, empty submission protection, and audit logging.
     */
    public function set(
        string $key,
        mixed $value,
        ?string $group = null,
        ?string $type = null,
        ?bool $isSecret = null,
        ?User $actor = null,
        ?string $ipAddress = null
    ): Setting {
        $meta = $this->getDefault($key);
        $resolvedGroup = $group ?? ($meta['group'] ?? Setting::GROUP_GENERAL);
        $resolvedType = $type ?? ($meta['type'] ?? 'string');
        $resolvedIsSecret = $isSecret ?? ($meta['is_secret'] ?? false);

        $existing = Setting::where('key', $key)->first();

        // Security check: if secret and submitted value is empty or mask, preserve existing
        if ($resolvedIsSecret && ($value === null || $value === '' || $this->isSecretMask((string) $value))) {
            if ($existing) {
                return $existing; // Keep existing secret unchanged
            }
        }

        // Format value for storage
        $storedValue = match ($resolvedType) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0',
            'json', 'array' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };

        $oldValue = $existing?->value;

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storedValue,
                'group' => $resolvedGroup,
                'type' => $resolvedType,
                'is_secret' => $resolvedIsSecret,
                'description' => $meta['description'] ?? null,
            ]
        );

        // Record audit trail if changed
        if ($oldValue !== $storedValue) {
            SettingAuditLog::create([
                'user_id' => $actor?->id,
                'setting_key' => $key,
                'old_value' => $resolvedIsSecret ? '[REDACTED]' : $oldValue,
                'new_value' => $resolvedIsSecret ? '[REDACTED]' : $storedValue,
                'ip_address' => $ipAddress,
            ]);
        }

        $this->clearCache($key);

        return $setting;
    }

    /**
     * Clear settings cache.
     */
    public function clearCache(?string $key = null): void
    {
        if ($key) {
            Cache::forget(self::CACHE_PREFIX.$key);
            unset(self::$runtimeCache[$key]);
        } else {
            foreach (array_keys(self::$defaults) as $k) {
                Cache::forget(self::CACHE_PREFIX.$k);
            }
            self::$runtimeCache = [];
        }
    }
}
