<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Today's Expenses & Payment Apps Statement - ExpenseAI</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 20px; }
        .header { border-b: 2px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; color: #0f172a; }
        .subtitle { font-size: 11px; color: #64748b; margin-top: 4px; }
        .summary-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .metric-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; }
        .metric-value { font-size: 24px; font-weight: bold; color: #ef4444; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #0f172a; color: #ffffff; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; }
        td { border-bottom: 1px solid #e2e8f0; padding: 8px 10px; font-size: 11px; }
        .footer { margin-top: 30px; border-t: 1px solid #e2e8f0; pt-10px; font-size: 10px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ExpenseAI - Daily Expenses Statement</div>
        <div class="subtitle">Client: {{ $user->name }} (Mobile: {{ $user->phone ?? 'N/A' }}) | Statement Date: {{ $today->format('F d, Y') }}</div>
    </div>

    <div class="summary-card">
        <div class="metric-title">Today's Total Outflow (All Banks & Payment Apps)</div>
        <div class="metric-value">${{ number_format($todaySpent, 2) }}</div>
    </div>

    <h3>Detailed Daily Transactions Ledger</h3>
    <table>
        <thead>
            <tr>
                <th>Time / Date</th>
                <th>Merchant / Details</th>
                <th>Category</th>
                <th>Payment App / Bank</th>
                <th>Type</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->transaction_date->format('H:i:s') }}</td>
                    <td><strong>{{ $tx->merchant?->name ?? $tx->notes }}</strong></td>
                    <td>{{ $tx->category?->name ?? 'General' }}</td>
                    <td>{{ $tx->payment_method ?? 'GPay / Bank' }}</td>
                    <td>{{ strtoupper($tx->type) }}</td>
                    <td style="text-align: right; font-weight: bold; color: {{ $tx->type === 'expense' ? '#ef4444' : '#10b981' }}">
                        {{ $tx->type === 'expense' ? '-' : '+' }}${{ number_format($tx->amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8;">No transactions recorded today.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated automatically by ExpenseAI Financial Intelligence Engine &copy; {{ date('Y') }}.
    </div>
</body>
</html>
