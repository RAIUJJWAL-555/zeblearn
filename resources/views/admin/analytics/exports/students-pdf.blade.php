<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Engagement Report - ZebLearn LMS</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 9px; color: #64748b; margin-top: 4px; }
        .summary { background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; margin-bottom: 15px; font-size: 9px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 9px; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 8px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ZebLearn LMS — Student Engagement & Activity Report</div>
        <div class="subtitle">Generated on {{ $generated_at->format('d M Y, H:i') }} | Activity Window: Past {{ $engagement['activity_window_days'] }} Days</div>
    </div>

    <div class="summary">
        <strong>Engagement Summary:</strong>
        Total Students: {{ $engagement['total_students'] }} |
        Active Students: {{ $engagement['active_students'] }} ({{ $engagement['activity_rate'] }}%) |
        Inactive: {{ $engagement['inactive_students'] }} |
        Total Quiz Attempts: {{ $engagement['quiz_participation']['total_attempts'] }} (Pass Rate: {{ $engagement['quiz_participation']['pass_rate'] }}%)
    </div>

    <table>
        <thead>
            <tr>
                <th>Student</th>
                <th>Email</th>
                <th>Status</th>
                <th class="text-center">Approved Courses</th>
                <th class="text-center">Quiz Attempts</th>
                <th class="text-center">Assignments</th>
                <th class="text-center">Certificates</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $st)
                <tr>
                    <td><strong>{{ $st->name }}</strong></td>
                    <td>{{ $st->email }}</td>
                    <td>{{ ucfirst($st->status) }}</td>
                    <td class="text-center">{{ $st->approved_enrollments }}</td>
                    <td class="text-center">{{ $st->quiz_attempts_count }}</td>
                    <td class="text-center">{{ $st->assignments_count }}</td>
                    <td class="text-center"><strong>{{ $st->certificates_count }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">No students found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        ZebLearn LMS Student Progress & Engagement Record
    </div>
</body>
</html>

