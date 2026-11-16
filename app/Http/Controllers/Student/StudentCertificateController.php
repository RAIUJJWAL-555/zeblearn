<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StudentCertificateController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService
    ) {}

    /**
     * Display student's earned certificates.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $certificates = $user->certificates()
            ->with(['course', 'enrollment'])
            ->latest('issued_at')
            ->paginate(10);

        return view('student.certificates.index', [
            'certificates' => $certificates,
            'totalCertificates' => $certificates->total(),
        ]);
    }

    /**
     * View detailed credential summary.
     */
    public function show(Request $request, Certificate $certificate): View
    {
        $user = $request->user();

        // Enforce strict student ownership
        if ((int) $certificate->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized access to certificate.');
        }

        $certificate->load(['course', 'enrollment']);

        return view('student.certificates.show', [
            'certificate' => $certificate,
            'student' => $user,
            'course' => $certificate->course,
        ]);
    }

    /**
     * Download the official landscape PDF certificate.
     */
    public function download(Request $request, Certificate $certificate): Response
    {
        $user = $request->user();

        // Enforce strict student ownership
        if ((int) $certificate->user_id !== (int) $user->id) {
            abort(403, 'Unauthorized access to certificate download.');
        }

        $pdf = $this->certificateService->generatePdf($certificate);

        $filename = "ZebLearn_Certificate_{$certificate->certificate_number}.pdf";

        return $pdf->download($filename);
    }
}
