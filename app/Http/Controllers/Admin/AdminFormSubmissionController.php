<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminFormSubmissionController extends Controller
{
    /**
     * Display a listing of form submissions with search, filters and metrics.
     */
    public function index(Request $request): View
    {
        $query = FormSubmission::query()->with('course');

        // Search across name, email, phone, course, message
        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        // Filter by form type
        if ($request->filled('form_type') && $request->input('form_type') !== 'all') {
            $query->formType($request->input('form_type'));
        }

        // Filter by status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->status($request->input('status'));
        }

        // Filter by course
        if ($request->filled('course_id') && $request->input('course_id') !== 'all') {
            $query->courseFilter($request->input('course_id'));
        }

        // Filter by date range
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->input('date_from'), $request->input('date_to'));
        }

        // Order newest first
        $submissions = $query->newest()->paginate(20)->withQueryString();

        // Metrics KPI counters
        $counts = [
            'total' => FormSubmission::count(),
            'new' => FormSubmission::where('status', FormSubmission::STATUS_NEW)->count(),
            'contacted' => FormSubmission::where('status', FormSubmission::STATUS_CONTACTED)->count(),
            'qualified' => FormSubmission::where('status', FormSubmission::STATUS_QUALIFIED)->count(),
            'converted' => FormSubmission::where('status', FormSubmission::STATUS_CONVERTED)->count(),
            'rejected' => FormSubmission::where('status', FormSubmission::STATUS_REJECTED)->count(),
        ];

        $courses = Course::orderBy('name')->get();
        $formTypes = FormSubmission::FORM_TYPES;
        $statuses = FormSubmission::STATUSES;

        return view('admin.form-submissions.index', compact(
            'submissions',
            'counts',
            'courses',
            'formTypes',
            'statuses'
        ));
    }

    /**
     * Display detailed view of a single form submission.
     */
    public function show(FormSubmission $formSubmission): View
    {
        $formSubmission->load('course');
        $statuses = FormSubmission::STATUSES;

        return view('admin.form-submissions.show', compact('formSubmission', 'statuses'));
    }

    /**
     * Update the status of a form submission.
     */
    public function updateStatus(Request $request, FormSubmission $formSubmission): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(FormSubmission::STATUSES))],
        ]);

        $formSubmission->update(['status' => $validated['status']]);

        return back()->with('success', "Submission status updated to '{$formSubmission->status}' successfully.");
    }

    /**
     * Update internal admin notes.
     */
    public function updateNotes(Request $request, FormSubmission $formSubmission): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $notes = $validated['admin_notes'] ?? $validated['notes'] ?? null;
        $formSubmission->update(['admin_notes' => $notes]);

        return back()->with('success', 'Admin notes updated successfully.');
    }

    /**
     * Delete a submission permanently.
     */
    public function destroy(FormSubmission $formSubmission): RedirectResponse
    {
        $formSubmission->delete();

        return redirect()->route('admin.form-submissions.index')
            ->with('success', 'Form submission deleted successfully.');
    }

    /**
     * Export filtered submissions to a streaming CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = FormSubmission::query()->with('course');

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }
        if ($request->filled('form_type') && $request->input('form_type') !== 'all') {
            $query->formType($request->input('form_type'));
        }
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->status($request->input('status'));
        }
        if ($request->filled('course_id') && $request->input('course_id') !== 'all') {
            $query->courseFilter($request->input('course_id'));
        }
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $query->dateRange($request->input('date_from'), $request->input('date_to'));
        }

        $filename = 'form_submissions_'.date('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header Row
            fputcsv($handle, [
                'ID',
                'Submitted Date',
                'Form Type',
                'Form Name',
                'Name',
                'Email',
                'Phone',
                'WhatsApp',
                'Course',
                'Company',
                'Message',
                'Status',
                'Page URL',
                'Source',
                'UTM Source',
                'UTM Medium',
                'UTM Campaign',
                'Admin Notes',
                'Additional Data (JSON)',
            ]);

            $query->newest()->chunk(250, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->submitted_at ? $row->submitted_at->toDateTimeString() : '',
                        $row->form_type,
                        $row->form_name,
                        $row->name,
                        $row->email,
                        $row->phone,
                        $row->whatsapp_number,
                        $row->course_name ?: ($row->course?->name ?? ''),
                        $row->company_name,
                        $row->message,
                        $row->status,
                        $row->page_url,
                        $row->source,
                        $row->utm_source,
                        $row->utm_medium,
                        $row->utm_campaign,
                        $row->admin_notes,
                        $row->additional_data ? json_encode($row->additional_data, JSON_UNESCAPED_UNICODE) : '',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
