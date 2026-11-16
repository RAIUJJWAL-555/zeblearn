<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Enrollments Report - ZebLearn LMS</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        .header { border-bottom: 2px solid #FB6221; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 9px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 9px; }
        .status-approved { color: #059669; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .status-rejected { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 8px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ZebLearn LMS — Official Enrollments Report</div>
        <div class="subtitle">Generated on {{ $generated_at->format('d M Y, H:i') }} | Confidential Academic Record</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Course Title</th>
                <th>Status</th>
                <th>Source</th>
                <th>Requested At</th>
                <th>Approved At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($enrollments as $enr)
                <tr>
                    <td>#{{ $enr->id }}</td>
                    <td><strong>{{ $enr->user?->name ?? 'N/A' }}</strong></td>
                    <td>{{ $enr->user?->email ?? 'N/A' }}</td>
                    <td>{{ $enr->course?->title ?? 'N/A' }}</td>
                    <td class="status-{{ $enr->status }}">{{ ucfirst($enr->status) }}</td>
                    <td>{{ $enr->enrollment_source ?? 'web' }}</td>
                    <td>{{ $enr->requested_at?->format('Y-m-d H:i') ?? '-' }}</td>
                    <td>{{ $enr->approved_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px;">No enrollment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Records: {{ $enrollments->count() }} • ZebLearn LMS Administration & Analytics
    </div>
</body>
</html>

