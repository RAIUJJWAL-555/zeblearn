<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Revenue Report - ZebLearn LMS</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #1e293b; margin: 20px; }
        .header { border-bottom: 2px solid #059669; padding-bottom: 10px; margin-bottom: 15px; }
        .title { font-size: 16px; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 9px; color: #64748b; margin-top: 4px; }
        .summary-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 10px; margin-bottom: 15px; }
        .summary-title { font-size: 11px; font-weight: bold; color: #0f172a; margin-bottom: 6px; }
        .summary-stat { display: inline-block; width: 23%; font-size: 9px; }
        .summary-val { font-size: 13px; font-weight: bold; color: #059669; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; color: #334155; font-weight: bold; text-align: left; padding: 6px 8px; border: 1px solid #cbd5e1; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 9px; }
        .text-right { text-align: right; }
        .status-paid { color: #059669; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
        .status-failed { color: #dc2626; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 8px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ZebLearn LMS — Verified Revenue Reconciliation Report</div>
        <div class="subtitle">Generated on {{ $generated_at->format('d M Y, H:i') }} | Financial Audit Record</div>
    </div>

    <div class="summary-box">
        <div class="summary-title">Financial Performance Overview</div>
        <div class="summary-stat">
            Total Collected:<br>
            <span class="summary-val">{{ $financials['formatted_collected_revenue'] }}</span>
        </div>
        <div class="summary-stat">
            Pending Orders:<br>
            <span class="summary-val" style="color: #d97706;">{{ $financials['formatted_pending_amount'] }}</span>
        </div>
        <div class="summary-stat">
            Paid Orders Count:<br>
            <span class="summary-val" style="color: #0f172a;">{{ $financials['paid_count'] }}</span>
        </div>
        <div class="summary-stat">
            Payment Success Rate:<br>
            <span class="summary-val" style="color: #2563eb;">{{ $financials['success_rate'] }}%</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tx ID</th>
                <th>Student</th>
                <th>Course</th>
                <th>Order ID</th>
                <th class="text-right">Amount (INR)</th>
                <th>Status</th>
                <th>Paid At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $p)
                <tr>
                    <td>#{{ $p->id }}</td>
                    <td><strong>{{ $p->user?->name ?? 'N/A' }}</strong><br><span style="color: #64748b;">{{ $p->user?->email }}</span></td>
                    <td>{{ $p->course?->title ?? 'N/A' }}</td>
                    <td>{{ $p->gateway_order_id ?? 'N/A' }}</td>
                    <td class="text-right"><strong>{{ $p->formatted_amount }}</strong></td>
                    <td class="status-{{ $p->status }}">{{ ucfirst($p->status) }}</td>
                    <td>{{ $p->paid_at?->format('Y-m-d H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px;">No payment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Total Transactions: {{ $payments->count() }} • ZebLearn LMS Finance Reconciliation
    </div>
</body>
</html>

