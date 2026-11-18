<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormSubmissionRequest;
use App\Services\FormSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class FormSubmissionController extends Controller
{
    public function __construct(
        protected FormSubmissionService $submissionService
    ) {}

    /**
     * Handle public form submissions (AJAX JSON or standard form post).
     */
    public function store(StoreFormSubmissionRequest $request): JsonResponse|RedirectResponse
    {
        $submission = $this->submissionService->submit($request->all(), $request);

        $successMessage = match ($submission->form_type) {
            'book_demo', 'hero_demo' => 'Thank you! Your demo class request has been received. Our senior counselor will confirm your session on WhatsApp.',
            'contact' => 'Thank you for reaching out! Our admissions team will review your query and get in touch shortly.',
            'brochure' => 'Thank you! The complete curriculum brochure and course roadmap have been requested.',
            'callback' => 'Callback requested successfully! Our team will call you within the scheduled hours.',
            default => 'Thank you! Your submission has been securely recorded. Our team will contact you shortly.',
        };

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => $successMessage,
                'submission_id' => $submission->id,
                'form_type' => $submission->form_type,
            ], $submission->wasRecentlyCreated ? 201 : 200);
        }

        return back()->with('success', $successMessage);
    }
}
