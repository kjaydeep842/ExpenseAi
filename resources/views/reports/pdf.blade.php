<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ExpenseAI Statement Export</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; padding: 20px; font-size: 12px; }
        .header { border-bottom: 2px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
        .title { font-size: 22px; font-weight: bold; color: #4338ca; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; }
        .amount { text-align: right; font-weight: bold; }
        .income { color: #059669; }
        .expense { color: #dc2626; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ExpenseAI Enterprise Financial Statement</div>
        <p>Generated for: <strong>{{ $user->name }}</strong> ({{ $user->email }}) on {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Merchant / Details</th>
                <th>Type</th>
                <th class="amount">Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $t)
                <tr>
                    <td>{{ $t->transaction_date->format('Y-m-d') }}</td>
                    <td>{{ $t->category?->name ?? 'General' }}</td>
                    <td>{{ $t->merchant?->name ?? $t->notes }}</td>
                    <td style="text-transform: uppercase;">{{ $t->type }}</td>
                    <td class="amount {{ $t->type === 'income' ? 'income' : 'expense' }}">
                        {{ $t->type === 'income' ? '+' : '-' }}${{ number_format($t->amount, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
