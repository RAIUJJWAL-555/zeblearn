<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Performance Report - ZebLearn LMS</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        .header { border-bottom: 2px solid #7c3aed; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 9px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 9px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 8px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ZebLearn LMS — Course Performance & Completion Dossier</div>
        <div class="subtitle">Generated on {{ $generated_at->format('d M Y, H:i') }} | Platform Average Completion: {{ $avg_completion }}%</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Course Title</th>
                <th class="text-center">Enrolled (App/Tot)</th>
                <th class="text-center">Completed</th>
                <th class="text-right">Completion Rate</th>
                <th class="text-right">Avg Progress</th>
                <th class="text-center">Quiz Avg Score</th>
                <th class="text-center">Assignments Graded</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $c)
                <tr>
                    <td><strong>{{ $c['title'] }}</strong><br><span style="color: #64748b;">{{ $c['category'] ?? 'General' }} • {{ $c['published_lessons'] }} lessons</span></td>
                    <td class="text-center">{{ $c['approved_enrolled'] }} / {{ $c['total_enrolled'] }}</td>
                    <td class="text-center"><strong>{{ $c['completed_students'] }}</strong></td>
                    <td class="text-right" style="color: #7c3aed; font-weight: bold;">{{ $c['completion_rate'] }}%</td>
                    <td class="text-right">{{ $c['average_progress'] }}%</td>
                    <td class="text-center">{{ $c['quiz_stats']['avg_score'] !== null ? $c['quiz_stats']['avg_score'].'%' : '-' }}</td>
                    <td class="text-center">{{ $c['assignment_stats']['graded'] }} / {{ $c['assignment_stats']['submissions'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">No course records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Tracks Analyzed: {{ $reports->count() }} • ZebLearn LMS Curriculum Analytics
    </div>
</body>
</html>

