<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminCertificateController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService
    ) {}

    /**
     * Display a filterable, paginated listing of all issued certificates.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search');
        $courseId = $request->query('course_id');

        $query = Certificate::query()->with(['user', 'course', 'enrollment']);

        if ($status === 'valid') {
            $query->valid();
        } elseif ($status === 'revoked') {
            $query->revoked();
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($search) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('certificate_number', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uQ) use ($term) {
                        $uQ->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    })
                    ->orWhereHas('course', function ($cQ) use ($term) {
                        $cQ->where('name', 'like', "%{$term}%");
                    });
            });
        }

        $certificates = $query->latest('issued_at')->paginate(15)->withQueryString();

        // Telemetry counts
        $totalCount = Certificate::count();
        $validCount = Certificate::valid()->count();
        $revokedCount = Certificate::revoked()->count();

        return view('admin.certificates.index', [
            'certificates' => $certificates,
            'totalCount' => $totalCount,
            'validCount' => $validCount,
            'revokedCount' => $revokedCount,
            'activeStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Display full details of a specific certificate.
     */
    public function show(Certificate $certificate): View
    {
        $certificate->load(['user', 'course', 'enrollment']);

        return view('admin.certificates.show', [
            'certificate' => $certificate,
        ]);
    }

    /**
     * Revoke a certificate with a mandatory reason.
     */
    public function revoke(Request $request, Certificate $certificate): RedirectResponse
    {
        $validated = $request->validate([
            'revocation_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $this->certificateService->revokeCertificate(
            $certificate,
            $validated['revocation_reason'],
            $request->user()
        );

        return redirect()->route('admin.certificates.show', $certificate)->with(
            'success',
            "Certificate {$certificate->certificate_number} was successfully revoked."
        );
    }

    /**
     * Download the certificate PDF as an administrator.
     */
    public function download(Certificate $certificate): Response
    {
        $pdf = $this->certificateService->generatePdf($certificate);

        $filename = "ZebLearn_Certificate_{$certificate->certificate_number}.pdf";

        return $pdf->download($filename);
    }
}
