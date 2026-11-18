<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CourseCsvImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class CourseCsvImportController extends Controller
{
    public function __construct(
        protected CourseCsvImportService $importService
    ) {}

    /**
     * Show the CSV import console.
     */
    public function index(): View
    {
        return view('admin.courses.import.index', [
            'columns' => $this->importService->getColumns(),
            'validCategories' => $this->importService->getValidCategorySlugs(),
        ]);
    }

    /**
     * Download the complete, standardized CSV template.
     */
    public function downloadTemplate(): Response
    {
        $csv = $this->importService->generateTemplateCsv();
        $filename = 'zeblearn-course-import-template-'.now()->format('Y-m-d').'.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Download or view the CSV Format Specification Guide.
     */
    public function guide(Request $request): View|Response
    {
        $markdown = $this->importService->generateGuideMarkdown();

        if ($request->query('download') === '1') {
            return response($markdown, 200, [
                'Content-Type' => 'text/markdown; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="zeblearn-course-import-guide.md"',
            ]);
        }

        return view('admin.courses.import.guide', [
            'markdown' => $markdown,
            'columns' => $this->importService->getColumns(),
            'validCategories' => $this->importService->getValidCategorySlugs(),
        ]);
    }

    /**
     * Upload and validate CSV, generating a full course preview before committing.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'max:10240'], // 10MB limit
            'update_existing' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('csv_file');
        if (! $file || ! $file->isValid()) {
            return back()->with('error', 'Uploaded file could not be read. Please upload a valid CSV file.');
        }

        // Validate extension / content type with cross-platform tolerance
        $ext = strtolower($file->getClientOriginalExtension());
        $mime = strtolower((string) $file->getMimeType());

        $isCsv = in_array($ext, ['csv', 'txt', 'tsv'], true)
            || str_contains($mime, 'csv')
            || str_contains($mime, 'text')
            || str_contains($mime, 'excel')
            || str_contains($mime, 'comma-separated');

        if (! $isCsv) {
            return back()->with('error', 'Invalid file type. Please upload a CSV file (.csv).');
        }

        $content = file_get_contents($file->getRealPath());
        if ($content === false || trim($content) === '') {
            return back()->with('error', 'Uploaded file is empty.');
        }

        $updateExisting = $request->boolean('update_existing');

        $previewResult = $this->importService->preview($content, [
            'update_existing' => $updateExisting,
        ]);

        if (! $previewResult['success']) {
            return back()->with('error', implode(' ', $previewResult['global_errors']));
        }

        // Store preview data in session for confirmation
        Session::put('zeblearn_csv_import_courses', $previewResult['courses']);
        Session::put('zeblearn_csv_import_filename', $file->getClientOriginalName());
        Session::put('zeblearn_csv_import_options', [
            'update_existing' => $updateExisting,
        ]);

        return view('admin.courses.import.preview', [
            'preview' => $previewResult,
            'filename' => $file->getClientOriginalName(),
            'updateExisting' => $updateExisting,
        ]);
    }

    /**
     * Execute the validated import after user review and confirmation.
     */
    public function process(Request $request): RedirectResponse
    {
        $courses = Session::get('zeblearn_csv_import_courses');

        if (empty($courses) || ! is_array($courses)) {
            return redirect()->route('admin.courses.import.index')
                ->with('error', 'Import session expired or no courses found. Please upload your CSV file again.');
        }

        $publishOverride = $request->input('publish_override', 'default');
        $updateExisting = $request->boolean('update_existing');

        // Filter valid courses only
        $validCourses = array_filter($courses, fn ($c) => ! empty($c['is_valid']));

        if (empty($validCourses)) {
            return back()->with('error', 'There are no valid courses to import. Please review validation errors.');
        }

        $result = $this->importService->import($validCourses, [
            'publish_override' => $publishOverride,
            'update_existing' => $updateExisting,
        ]);

        // Clean up session
        Session::forget([
            'zeblearn_csv_import_courses',
            'zeblearn_csv_import_filename',
            'zeblearn_csv_import_options',
        ]);

        $message = "Import complete: {$result['imported']} new course(s) created";
        if ($result['updated'] > 0) {
            $message .= ", {$result['updated']} course(s) updated";
        }
        $message .= '.';

        if ($result['failed'] > 0) {
            $message .= " {$result['failed']} course(s) failed.";

            return redirect()->route('admin.courses.index')
                ->with('warning', $message)
                ->with('import_errors', $result['errors']);
        }

        return redirect()->route('admin.courses.index')->with('success', $message);
    }
}
