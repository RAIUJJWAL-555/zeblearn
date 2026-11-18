<?php

namespace App\Services;

use App\Models\Course;
use App\Models\FormSubmission;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FormSubmissionService
{
    /**
     * Known standard fields that map directly to the table columns.
     */
    protected array $standardFields = [
        'form_type',
        'form_name',
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'course_id',
        'course_name',
        'course',
        'company_name',
        'message',
        'page_url',
        'page_title',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'status',
        'admin_notes',
        '_token',
        'website_url_hp',
        'b_fax_hp',
    ];

    /**
     * Process and store a form submission securely.
     *
     * @param  array<string, mixed>  $data
     */
    public function submit(array $data, ?Request $request = null): FormSubmission
    {
        // 1. Honeypot Anti-Spam Check
        if ($this->isSpamHoneypotTriggered($data, $request)) {
            Log::info('Spam honeypot triggered on form submission', [
                'ip' => $request?->ip(),
                'form_type' => $data['form_type'] ?? 'unknown',
            ]);

            // Save quietly with rejected status for auditing without alerting the spammer
            $data['status'] = FormSubmission::STATUS_REJECTED;
            $data['admin_notes'] = 'Flagged automatically by honeypot anti-spam trap.';
        }

        // 2. Resolve Course Info
        $courseId = ! empty($data['course_id']) ? (int) $data['course_id'] : null;
        $courseName = ! empty($data['course_name']) ? trim((string) $data['course_name']) : (! empty($data['course']) ? trim((string) $data['course']) : null);

        if (! $courseId && ! empty($courseName)) {
            $matchedCourse = $this->resolveCourse($courseName);
            if ($matchedCourse) {
                $courseId = $matchedCourse->id;
                $courseName = $matchedCourse->name;
            }
        } elseif ($courseId && empty($courseName)) {
            $course = Course::find($courseId);
            $courseName = $course?->name;
        }

        // 3. Extract Tracking & Attribution
        $pageUrl = $data['page_url'] ?? ($request?->input('page_url') ?: $request?->fullUrl());
        $pageTitle = $data['page_title'] ?? $request?->input('page_title');
        $source = $data['source'] ?? ($request?->input('source') ?: ($data['form_type'] ?? 'direct_web'));
        $utmSource = $data['utm_source'] ?? ($request?->input('utm_source') ?: $request?->query('utm_source'));
        $utmMedium = $data['utm_medium'] ?? ($request?->input('utm_medium') ?: $request?->query('utm_medium'));
        $utmCampaign = $data['utm_campaign'] ?? ($request?->input('utm_campaign') ?: $request?->query('utm_campaign'));
        $utmTerm = $data['utm_term'] ?? ($request?->input('utm_term') ?: $request?->query('utm_term'));
        $utmContent = $data['utm_content'] ?? ($request?->input('utm_content') ?: $request?->query('utm_content'));

        // 4. Sanitize Core Identity Fields
        $name = isset($data['name']) ? trim(strip_tags((string) $data['name'])) : null;
        $email = isset($data['email']) ? strtolower(trim((string) $data['email'])) : null;
        $phone = isset($data['phone']) ? $this->sanitizePhone((string) $data['phone']) : null;
        $whatsappNumber = isset($data['whatsapp_number']) ? $this->sanitizePhone((string) $data['whatsapp_number']) : null;
        $companyName = isset($data['company_name']) ? trim(strip_tags((string) $data['company_name'])) : null;
        $message = isset($data['message']) ? trim(strip_tags((string) $data['message'])) : null;

        // 5. Gather Additional Data (Preserve every non-standard field)
        $additionalData = [];
        if (isset($data['additional_data']) && is_array($data['additional_data'])) {
            $additionalData = $data['additional_data'];
        }

        foreach ($data as $key => $value) {
            if (! in_array($key, $this->standardFields, true) && ! isset($additionalData[$key])) {
                $additionalData[$key] = is_string($value) ? trim(strip_tags($value)) : $value;
            }
        }

        // 6. Deduplication Check (Avoid duplicate rapid submissions within 30 seconds)
        $existing = null;
        if (($email || $phone) && empty($data['allow_duplicate'])) {
            $existing = FormSubmission::query()
                ->where('form_type', $data['form_type'] ?? 'contact')
                ->where(function ($q) use ($email, $phone) {
                    if ($email && $phone) {
                        $q->where('email', $email)->orWhere('phone', $phone);
                    } elseif ($email) {
                        $q->where('email', $email);
                    } elseif ($phone) {
                        $q->where('phone', $phone);
                    }
                })
                ->where('submitted_at', '>=', now()->subSeconds(30))
                ->first();
        }

        if ($existing) {
            // Update additional data if provided and return existing
            if (! empty($additionalData)) {
                $existing->additional_data = array_merge($existing->additional_data ?? [], $additionalData);
                $existing->save();
            }

            return $existing;
        }

        // 7. Persist Form Submission & Synchronize Inbound Lead
        return DB::transaction(function () use (
            $data, $name, $email, $phone, $whatsappNumber, $courseId, $courseName,
            $companyName, $message, $additionalData, $pageUrl, $pageTitle, $source,
            $utmSource, $utmMedium, $utmCampaign, $utmTerm, $utmContent, $request
        ) {
            $submission = FormSubmission::create([
                'form_type' => $data['form_type'] ?? 'contact',
                'form_name' => $data['form_name'] ?? (FormSubmission::FORM_TYPES[$data['form_type'] ?? 'contact'] ?? 'Website Form'),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'whatsapp_number' => $whatsappNumber,
                'course_id' => $courseId,
                'course_name' => $courseName,
                'company_name' => $companyName,
                'message' => $message,
                'additional_data' => ! empty($additionalData) ? $additionalData : null,
                'page_url' => $pageUrl ? substr($pageUrl, 0, 1000) : null,
                'page_title' => $pageTitle ? substr($pageTitle, 0, 255) : null,
                'source' => $source ? substr($source, 0, 100) : null,
                'utm_source' => $utmSource ? substr($utmSource, 0, 100) : null,
                'utm_medium' => $utmMedium ? substr($utmMedium, 0, 100) : null,
                'utm_campaign' => $utmCampaign ? substr($utmCampaign, 0, 100) : null,
                'utm_term' => $utmTerm ? substr($utmTerm, 0, 150) : null,
                'utm_content' => $utmContent ? substr($utmContent, 0, 150) : null,
                'status' => $data['status'] ?? FormSubmission::STATUS_NEW,
                'admin_notes' => $data['admin_notes'] ?? null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent() ? substr($request->userAgent(), 0, 500) : null,
                'submitted_at' => now(),
            ]);

            // Synchronize into legacy Lead table for unified reporting
            try {
                Lead::create([
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'course_name' => $courseName,
                    'course_id' => $courseId,
                    'source_url' => $pageUrl ? substr($pageUrl, 0, 500) : null,
                    'cta_type' => $data['form_type'] ?? 'form_submission',
                    'referrer' => $request?->header('referer'),
                    'utm_source' => $utmSource,
                    'utm_medium' => $utmMedium,
                    'utm_campaign' => $utmCampaign,
                    'status' => 'new',
                ]);
            } catch (\Throwable $e) {
                // Log non-fatal synchronization error
                Log::warning('Lead record synchronization notice: '.$e->getMessage());
            }

            return $submission;
        });
    }

    /**
     * Check if spam honeypot fields were filled out.
     */
    protected function isSpamHoneypotTriggered(array $data, ?Request $request): bool
    {
        // Check standard trap fields
        $trapFields = ['website_url_hp', 'b_fax_hp', 'honeypot_field'];
        foreach ($trapFields as $field) {
            if (! empty($data[$field]) || ($request && ! empty($request->input($field)))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize and normalize phone number.
     */
    public function sanitizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $cleaned = preg_replace('/[^\d+]/', '', trim($phone));
        if (strlen($cleaned) > 50) {
            $cleaned = substr($cleaned, 0, 50);
        }

        return $cleaned ?: null;
    }

    /**
     * Fuzzy resolve course name or code to a course model.
     */
    protected function resolveCourse(string $term): ?Course
    {
        $clean = trim($term);

        // Exact match by slug or name
        $course = Course::where('name', $clean)
            ->orWhere('slug', $clean)
            ->first();

        if ($course) {
            return $course;
        }

        // Fuzzy matches (e.g. 'fico' => 'SAP FICO on S/4 HANA')
        $mappings = [
            'fico' => ['sap-fico-on-s-4-hana', 'sap-fico'],
            'mm' => ['mm-s4-hana-simple-logistics', 'sap-mm'],
            'sd' => ['sap-sd-2', 'sap-sd'],
            'hcm' => ['sap-hcm-on-s-4-hana', 'sap-hcm'],
            'pp' => ['sap-pp'],
            'abap' => ['sap-abap'],
            'basis' => ['sap-basis'],
            'ewm' => ['sap-ewm'],
        ];

        $lower = strtolower($clean);
        foreach ($mappings as $code => $slugs) {
            if (str_contains($lower, $code)) {
                foreach ($slugs as $s) {
                    $c = Course::where('slug', $s)->orWhere('slug', 'like', "%{$s}%")->first();
                    if ($c) {
                        return $c;
                    }
                }
            }
        }

        // Last fallback: search like name
        return Course::where('name', 'like', "%{$clean}%")->first();
    }
}
