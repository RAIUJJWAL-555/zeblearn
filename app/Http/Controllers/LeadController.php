<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lead;
use App\Services\FormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function __construct(
        protected FormSubmissionService $submissionService
    ) {}

    /**
     * Store an inbound lead with attribution data and sync to FormSubmission.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'course_name' => ['nullable', 'string', 'max:150'],
            'source_url' => ['nullable', 'string', 'max:500'],
            'destination_url' => ['nullable', 'string', 'max:500'],
            'cta_type' => ['nullable', 'string', 'max:50'],
            'referrer' => ['nullable', 'string', 'max:500'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
        ]);

        // Attempt course lookup if course_name or slug provided
        $courseId = null;
        if (! empty($validated['course_name'])) {
            $course = Course::where('name', $validated['course_name'])
                ->orWhere('slug', $validated['course_name'])
                ->first();
            $courseId = $course?->id;
        }

        $lead = Lead::create([
            ...$validated,
            'course_id' => $courseId,
            'referrer' => $validated['referrer'] ?? $request->header('referer'),
            'status' => 'new',
        ]);

        // Centralized Form Submission synchronization
        $this->submissionService->submit([
            'form_type' => $validated['cta_type'] ?? 'lead',
            'form_name' => 'Inbound Attribution Lead ('.($validated['cta_type'] ?? 'lead').')',
            'name' => $validated['name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'course_id' => $courseId,
            'course_name' => $validated['course_name'] ?? null,
            'page_url' => $validated['source_url'] ?? $request->fullUrl(),
            'source' => $validated['cta_type'] ?? 'api_lead',
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
            'additional_data' => [
                'destination_url' => $validated['destination_url'] ?? null,
                'referrer' => $validated['referrer'] ?? $request->header('referer'),
            ],
        ], $request);

        return response()->json([
            'status' => 'success',
            'lead_id' => $lead->id,
            'message' => 'Lead attribution recorded successfully.',
        ], 201);
    }
}
