<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion - {{ $certificate->certificate_number }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FAFAF8;
            color: #0A0A0A;
            -webkit-print-color-adjust: exact;
        }
        .certificate-wrapper {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding: 24px;
            background: #FAFAF8;
        }
        .outer-border {
            border: 4px solid #0A0A0A;
            padding: 10px;
            height: 94%;
            box-sizing: border-box;
            background: #FFFFFF;
        }
        .inner-border {
            border: 2px solid #FB6221;
            padding: 26px 36px;
            height: 93%;
            box-sizing: border-box;
            position: relative;
            text-align: center;
        }
        .corner-accent {
            position: absolute;
            width: 24px;
            height: 24px;
            border-color: #D97706;
        }
        .corner-tl { top: -2px; left: -2px; border-top: 5px solid #D97706; border-left: 5px solid #D97706; }
        .corner-tr { top: -2px; right: -2px; border-top: 5px solid #D97706; border-right: 5px solid #D97706; }
        .corner-bl { bottom: -2px; left: -2px; border-bottom: 5px solid #D97706; border-left: 5px solid #D97706; }
        .corner-br { bottom: -2px; right: -2px; border-bottom: 5px solid #D97706; border-right: 5px solid #D97706; }

        .brand-header {
            margin-bottom: 8px;
        }
        .brand-logo {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: 4px;
            color: #0A0A0A;
            text-transform: uppercase;
        }
        .brand-logo span {
            color: #FB6221;
        }
        .brand-subtitle {
            font-size: 9px;
            letter-spacing: 2px;
            color: #71717A;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .title-divider {
            width: 120px;
            height: 2px;
            background: #FB6221;
            margin: 10px auto 14px auto;
        }
        .certificate-title {
            font-size: 26px;
            font-weight: 900;
            letter-spacing: 5px;
            color: #0A0A0A;
            text-transform: uppercase;
            margin: 0;
        }
        .certificate-lead {
            font-size: 11px;
            font-style: italic;
            color: #52525B;
            margin: 12px 0 6px 0;
            letter-spacing: 0.5px;
        }
        .student-name {
            font-size: 28px;
            font-weight: bold;
            font-family: Georgia, 'Times New Roman', serif;
            color: #0A0A0A;
            margin: 8px 0;
            border-bottom: 1.5px solid #E4E4E7;
            display: inline-block;
            padding: 0 40px 6px 40px;
        }
        .completion-statement {
            font-size: 11px;
            color: #52525B;
            max-width: 620px;
            margin: 10px auto 6px auto;
            line-height: 1.5;
        }
        .course-name {
            font-size: 20px;
            font-weight: 800;
            color: #FB6221;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 8px 0 16px 0;
        }

        /* Footer 3-column table */
        .footer-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: bottom;
            padding: 0 10px;
        }
        .col-left {
            width: 32%;
            text-align: left;
        }
        .col-center {
            width: 36%;
            text-align: center;
        }
        .col-right {
            width: 32%;
            text-align: right;
        }

        .meta-item {
            font-size: 10px;
            color: #71717A;
            margin-bottom: 4px;
        }
        .meta-item strong {
            color: #0A0A0A;
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
        }

        .qr-box {
            display: inline-block;
            text-align: center;
        }
        .qr-image {
            width: 80px;
            height: 80px;
            margin: 0 auto 4px auto;
        }
        .qr-label {
            font-size: 8px;
            color: #71717A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qr-url {
            font-size: 7.5px;
            color: #FB6221;
            font-family: 'Courier New', Courier, monospace;
            word-break: break-all;
        }

        .signature-line {
            display: inline-block;
            width: 160px;
            border-bottom: 1px solid #71717A;
            padding-bottom: 4px;
            margin-bottom: 4px;
            text-align: center;
        }
        .signature-cursive {
            font-family: 'Brush Script MT', cursive, Georgia, serif;
            font-size: 20px;
            color: #0A0A0A;
            display: block;
        }
        .signature-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #52525B;
        }
        .signature-org {
            font-size: 8px;
            color: #A1A1AA;
            text-transform: uppercase;
        }

        .revoked-watermark {
            position: absolute;
            top: 40%;
            left: 20%;
            width: 60%;
            text-align: center;
            font-size: 60px;
            font-weight: 900;
            color: rgba(220, 38, 38, 0.18);
            letter-spacing: 12px;
            border: 8px dashed rgba(220, 38, 38, 0.25);
            padding: 16px;
            transform: rotate(-12deg);
            text-transform: uppercase;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="outer-border">
            <div class="inner-border">
                <!-- Decorative Corner Accents -->
                <div class="corner-accent corner-tl"></div>
                <div class="corner-accent corner-tr"></div>
                <div class="corner-accent corner-bl"></div>
                <div class="corner-accent corner-br"></div>

                @if ($certificate->isRevoked())
                    <div class="revoked-watermark">REVOKED</div>
                @endif

                <!-- Brand Header -->
                <div class="brand-header">
                    <div class="brand-logo">ZEB<span>LEARN</span></div>
                    <div class="brand-subtitle">Enterprise Technology Institute · Professional Education</div>
                </div>

                <div class="title-divider"></div>

                <h1 class="certificate-title">CERTIFICATE OF COMPLETION</h1>

                <p class="certificate-lead">This is to certify that</p>

                <div class="student-name">
                    {{ $student->name }}
                </div>

                <p class="completion-statement">
                    has successfully demonstrated practical competency, completed all required curriculum modules and lessons, and satisfied the graduation criteria for the professional program:
                </p>

                <div class="course-name">
                    {{ $course->name }}
                </div>

                <!-- Footer Table -->
                <table class="footer-table">
                    <tr>
                        <!-- Left: Certificate Meta -->
                        <td class="col-left">
                            <div class="meta-item">Certificate ID: <strong>{{ $certificate->certificate_number }}</strong></div>
                            <div class="meta-item">Issued Date: <strong>{{ $certificate->issued_at->format('M d, Y') }}</strong></div>
                            <div class="meta-item">Status: <strong>{{ $certificate->isValid() ? 'Verified Active' : 'Revoked' }}</strong></div>
                        </td>

                        <!-- Center: QR Code Verification -->
                        <td class="col-center">
                            <div class="qr-box">
                                @if ($qrCodeBase64)
                                    <img src="{{ $qrCodeBase64 }}" class="qr-image" alt="Verification QR Code">
                                @endif
                                <div class="qr-label">Scan to verify authenticity</div>
                                <div class="qr-url">sap.zeblearn.com/verify</div>
                            </div>
                        </td>

                        <!-- Right: Signature Seal -->
                        <td class="col-right">
                            <div class="signature-line">
                                <span class="signature-cursive">Dr. K. S. Sharma</span>
                                <div class="signature-title">Academic Director</div>
                                <div class="signature-org">ZebLearn LMS Education Board</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

