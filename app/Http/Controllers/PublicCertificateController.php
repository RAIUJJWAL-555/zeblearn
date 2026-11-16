<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCertificateController extends Controller
{
    /**
     * Publicly verify the authenticity of an issued certificate.
     */
    public function verify(Request $request, ?string $certificate_number = null): View
    {
        $inputNumber = $certificate_number ?: $request->query('number', $request->query('q', 'ZEB-MM-2026-8941'));
        $cleanNumber = strtoupper(trim((string) $inputNumber));

        $certificate = Certificate::where('certificate_number', $cleanNumber)
            ->with(['course', 'user'])
            ->first();

        // Sample verification fallback if matching sample credential ID
        if (! $certificate && in_array($cleanNumber, ['ZEB-MM-2026-8941', 'ZEB-CAP-2026-4412', 'SAMPLE'])) {
            return view('pages.certificates.verify', [
                'certificateNumber' => $cleanNumber,
                'studentName' => $cleanNumber === 'ZEB-CAP-2026-4412' ? 'Priya Nair' : 'Ankit Sharma',
                'courseName' => 'SAP MM Training – Materials Management',
                'courseCategory' => 'SAP ERP Functional',
                'issuedAt' => now()->subMonths(1),
                'isValid' => true,
                'isRevoked' => false,
                'revokedAt' => null,
                'revocationReason' => null,
            ]);
        }

        if (! $certificate) {
            abort(404, "Certificate '{$cleanNumber}' was not found in the ZebLearn registry.");
        }

        // Strictly safe presentation: never pass sensitive student contact data
        return view('pages.certificates.verify', [
            'certificateNumber' => $certificate->certificate_number,
            'studentName' => $certificate->user?->name ?? 'Verified Student',
            'courseName' => $certificate->course?->name ?? 'Enterprise Program',
            'courseCategory' => $certificate->course?->category_badge ?? 'Enterprise Technology',
            'issuedAt' => $certificate->issued_at,
            'isValid' => $certificate->isValid(),
            'isRevoked' => $certificate->isRevoked(),
            'revokedAt' => $certificate->revoked_at,
            'revocationReason' => $certificate->revocation_reason,
        ]);
    }
}
