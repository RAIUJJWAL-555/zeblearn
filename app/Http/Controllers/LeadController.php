<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Store an inbound lead with attribution data.
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

        return response()->json([
            'status' => 'success',
            'lead_id' => $lead->id,
            'message' => 'Lead attribution recorded successfully.',
        ], 201);
    }
}
