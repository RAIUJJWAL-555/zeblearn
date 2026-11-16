<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SettingAuditLog;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    /**
     * Display the centralized admin system settings console.
     */
    public function index(Request $request): View
    {
        $activeTab = $request->query('group', Setting::GROUP_GENERAL);
        if (! in_array($activeTab, Setting::GROUPS, true)) {
            $activeTab = Setting::GROUP_GENERAL;
        }

        // Fetch settings for active group and mask secrets
        $groupSettings = $this->settingService->getGroup($activeTab);

        // Mask any secrets for UI display
        $maskedSettings = [];
        foreach ($groupSettings as $key => $val) {
            $meta = $this->settingService->getDefault($key);
            if ($meta && $meta['is_secret']) {
                $maskedSettings[$key] = $this->settingService->maskSecret((string) $val);
            } else {
                $maskedSettings[$key] = $val;
            }
        }

        // Recent audit logs
        $recentAuditLogs = SettingAuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Platform status
        $isMaintenance = (bool) $this->settingService->get('maintenance_mode_enabled', false);

        return view('admin.settings.index', [
            'activeTab' => $activeTab,
            'groups' => Setting::GROUPS,
            'settings' => $maskedSettings,
            'rawSettings' => $groupSettings,
            'recentAuditLogs' => $recentAuditLogs,
            'isMaintenance' => $isMaintenance,
        ]);
    }

    /**
     * Update settings for a specific group securely.
     */
    public function update(Request $request): RedirectResponse
    {
        $group = $request->input('_group', Setting::GROUP_GENERAL);
        abort_unless(in_array($group, Setting::GROUPS, true), 400, 'Invalid settings group.');

        $actor = $request->user();
        $ip = $request->ip();

        switch ($group) {
            case Setting::GROUP_GENERAL:
                $validated = $request->validate([
                    'site_name' => ['required', 'string', 'max:100'],
                    'site_tagline' => ['nullable', 'string', 'max:255'],
                    'contact_email' => ['required', 'email', 'max:150'],
                    'contact_phone' => ['nullable', 'string', 'max:50'],
                    'support_url' => ['nullable', 'url', 'max:255'],
                    'official_website_url' => ['nullable', 'url', 'max:255'],
                    'footer_copyright' => ['nullable', 'string', 'max:255'],
                    'default_timezone' => ['required', 'string', 'timezone'],
                    'default_currency' => ['required', 'string', 'size:3'],
                ]);

                foreach ($validated as $key => $val) {
                    $this->settingService->set($key, $val, $group, 'string', false, $actor, $ip);
                }
                break;

            case Setting::GROUP_BRANDING:
                $validated = $request->validate([
                    'brand_primary_color' => ['required', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
                    'brand_secondary_color' => ['required', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
                    'brand_accent_color' => ['required', 'string', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
                    'social_linkedin' => ['nullable', 'url', 'max:255'],
                    'social_twitter' => ['nullable', 'url', 'max:255'],
                    'social_instagram' => ['nullable', 'url', 'max:255'],
                    'social_youtube' => ['nullable', 'url', 'max:255'],
                    'social_facebook' => ['nullable', 'url', 'max:255'],
                    'social_whatsapp' => ['nullable', 'url', 'max:255'],
                ]);

                foreach ($validated as $key => $val) {
                    $this->settingService->set($key, $val, $group, 'string', false, $actor, $ip);
                }
                break;

            case Setting::GROUP_PAYMENTS:
                $validated = $request->validate([
                    'razorpay_enabled' => ['nullable', 'boolean'],
                    'razorpay_mode' => ['required', Rule::in(['test', 'live'])],
                    'razorpay_key_id' => ['nullable', 'string', 'max:255'],
                    'razorpay_key_secret' => ['nullable', 'string', 'max:255'],
                    'razorpay_webhook_secret' => ['nullable', 'string', 'max:255'],
                ]);

                $this->settingService->set('razorpay_enabled', $request->boolean('razorpay_enabled'), $group, 'boolean', false, $actor, $ip);
                $this->settingService->set('razorpay_mode', $validated['razorpay_mode'], $group, 'string', false, $actor, $ip);
                $this->settingService->set('razorpay_key_id', $validated['razorpay_key_id'] ?? '', $group, 'string', false, $actor, $ip);
                $this->settingService->set('razorpay_key_secret', $validated['razorpay_key_secret'] ?? '', $group, 'string', true, $actor, $ip);
                $this->settingService->set('razorpay_webhook_secret', $validated['razorpay_webhook_secret'] ?? '', $group, 'string', true, $actor, $ip);
                break;

            case Setting::GROUP_EMAIL:
                $validated = $request->validate([
                    'mail_from_name' => ['required', 'string', 'max:100'],
                    'mail_from_address' => ['required', 'email', 'max:150'],
                ]);

                $this->settingService->set('mail_from_name', $validated['mail_from_name'], $group, 'string', false, $actor, $ip);
                $this->settingService->set('mail_from_address', $validated['mail_from_address'], $group, 'string', false, $actor, $ip);

                $booleanKeys = [
                    'notify_student_enrollment_approved',
                    'notify_student_enrollment_rejected',
                    'notify_student_assignment_graded',
                    'notify_student_certificate_issued',
                    'notify_admin_new_enrollment',
                    'notify_admin_payment_received',
                    'notify_instructor_course_assigned',
                    'notify_instructor_assignment_submitted',
                ];

                foreach ($booleanKeys as $bKey) {
                    $this->settingService->set($bKey, $request->boolean($bKey), $group, 'boolean', false, $actor, $ip);
                }
                break;

            case Setting::GROUP_COURSES:
                $validated = $request->validate([
                    'default_course_visibility' => ['required', Rule::in(['available', 'upcoming', 'planned'])],
                ]);

                $this->settingService->set('enrollment_enabled', $request->boolean('enrollment_enabled'), $group, 'boolean', false, $actor, $ip);
                $this->settingService->set('enrollment_require_admin_approval', $request->boolean('enrollment_require_admin_approval'), $group, 'boolean', false, $actor, $ip);
                $this->settingService->set('student_registration_enabled', $request->boolean('student_registration_enabled'), $group, 'boolean', false, $actor, $ip);
                $this->settingService->set('default_course_visibility', $validated['default_course_visibility'], $group, 'string', false, $actor, $ip);
                break;

            case Setting::GROUP_CERTIFICATES:
                $validated = $request->validate([
                    'certificate_organization_name' => ['required', 'string', 'max:150'],
                    'certificate_signatory_name' => ['required', 'string', 'max:100'],
                    'certificate_signatory_title' => ['required', 'string', 'max:100'],
                    'certificate_prefix' => ['required', 'string', 'alpha_dash', 'max:20'],
                ]);

                foreach ($validated as $key => $val) {
                    $this->settingService->set($key, $val, $group, 'string', false, $actor, $ip);
                }
                break;

            case Setting::GROUP_SEO:
                $validated = $request->validate([
                    'seo_default_title' => ['required', 'string', 'max:120'],
                    'seo_default_description' => ['required', 'string', 'max:300'],
                    'seo_canonical_base_url' => ['required', 'url', 'max:255'],
                ]);

                $this->settingService->set('seo_default_title', $validated['seo_default_title'], $group, 'string', false, $actor, $ip);
                $this->settingService->set('seo_default_description', $validated['seo_default_description'], $group, 'string', false, $actor, $ip);
                $this->settingService->set('seo_canonical_base_url', rtrim($validated['seo_canonical_base_url'], '/'), $group, 'string', false, $actor, $ip);
                $this->settingService->set('seo_allow_indexing', $request->boolean('seo_allow_indexing'), $group, 'boolean', false, $actor, $ip);
                break;

            case Setting::GROUP_MAINTENANCE:
                $validated = $request->validate([
                    'maintenance_message' => ['required', 'string', 'max:500'],
                ]);

                $this->settingService->set('maintenance_mode_enabled', $request->boolean('maintenance_mode_enabled'), $group, 'boolean', false, $actor, $ip);
                $this->settingService->set('maintenance_message', $validated['maintenance_message'], $group, 'string', false, $actor, $ip);
                break;
        }

        return redirect()->route('admin.settings.index', ['group' => $group])
            ->with('status', 'Platform configuration settings updated successfully.');
    }
}
