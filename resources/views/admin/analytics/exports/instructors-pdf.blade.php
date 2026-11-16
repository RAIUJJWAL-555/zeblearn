<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Instructor Performance Report - ZebLearn LMS</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 9px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 9px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; font-size: 8px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ZebLearn LMS — Faculty & Instructor Performance Dossier</div>
        <div class="subtitle">Generated on {{ $generated_at->format('d M Y, H:i') }} | Total Instructors Analyzed: {{ $summary['total_instructors_analyzed'] }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Instructor</th>
                <th>Assigned Courses</th>
                <th class="text-center">Student Reach</th>
                <th class="text-center">Graded Reviews</th>
                <th class="text-center">Pending Workload</th>
                <th class="text-right">Avg Turnaround</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($instructors as $inst)
                <tr>
                    <td><strong>{{ $inst['name'] }}</strong><br><span style="color: #64748b;">{{ $inst['designation'] }} • {{ $inst['specialization'] }}</span></td>
                    <td>
                        @foreach ($inst['assigned_courses'] as $c)
                            <div>• {{ $c['title'] }}</div>
                        @endforeach
                        @if ($inst['assigned_courses']->isEmpty())
                            <span style="color: #94a3b8; font-style: italic;">None assigned</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $inst['approved_students'] }} approved / {{ $inst['total_students'] }} total</td>
                    <td class="text-center"><strong>{{ $inst['submissions_graded'] }}</strong></td>
                    <td class="text-center" style="color: #d97706; font-weight: bold;">{{ $inst['pending_workload'] }}</td>
                    <td class="text-right">{{ $inst['avg_turnaround_hours'] !== null ? $inst['avg_turnaround_hours'].' hrs' : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 15px;">No instructors found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        ZebLearn LMS Faculty Management & Academic Operations
    </div>
</body>
</html>

