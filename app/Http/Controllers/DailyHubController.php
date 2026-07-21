<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Transaction;
use App\Models\User;
use App\Services\GeminiAiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DailyHubController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 1. Get Daily Limit
        $profile = Profile::firstOrCreate(
            ['user_id' => $user->id],
            ['daily_expense_limit' => 500.00]
        );
        $dailyLimit = (float) ($profile->daily_expense_limit ?? 500.00);

        // 2. Today's Transactions & Expense Outflow
        $todayTransactions = Transaction::where('user_id', $user->id)
            ->whereDate('transaction_date', $today)
            ->with(['category', 'merchant', 'bankAccount'])
            ->latest('transaction_date')
            ->get();

        $todaySpent = (float) $todayTransactions->where('type', 'expense')->sum('amount');
        $todayIncome = (float) $todayTransactions->whereIn('type', ['income', 'salary'])->sum('amount');

        // 3. Payment Apps Grouping Breakdown
        $paymentAppBreakdown = $todayTransactions->where('type', 'expense')
            ->groupBy('payment_method')
            ->map(function ($group, $method) {
                return [
                    'method' => $method ?? 'Other',
                    'total' => $group->sum('amount'),
                    'count' => $group->count(),
                ];
            });

        // 4. Budget Alert Calculations
        $percentage = ($dailyLimit > 0) ? round(($todaySpent / $dailyLimit) * 100, 1) : 0;
        $isExceeded = $todaySpent > $dailyLimit;
        $overAmount = max(0, $todaySpent - $dailyLimit);

        // 5. Gemini AI Remediation Tip if limit exceeded or approaching threshold
        $aiService = new GeminiAiService();
        $aiPrompt = "User's daily expense limit is \${$dailyLimit}. Today's total spent is \${$todaySpent} (Over by \${$overAmount}). Provide 2 urgent micro-budgeting actions for today.";
        $aiTip = $aiService->askFinancialAssistant($user->id, $aiPrompt);

        return view('daily.index', compact(
            'today',
            'dailyLimit',
            'todaySpent',
            'todayIncome',
            'todayTransactions',
            'paymentAppBreakdown',
            'percentage',
            'isExceeded',
            'overAmount',
            'aiTip'
        ));
    }

    public function getTodaySummaryApi(Request $request)
    {
        $user = Auth::user();
        if (!$user && $request->has('phone')) {
            $phoneInput = $request->phone;
            $cleanPhone = preg_replace('/[^0-9]/', '', $phoneInput);
            $user = User::where('phone', $phoneInput)
                ->orWhere('phone', 'LIKE', "%{$cleanPhone}%")
                ->first();
        }

        if (!$user) {
            return response()->json(['error' => 'Unauthorized or User not found'], 401);
        }

        $today = Carbon::today();
        $profile = Profile::where('user_id', $user->id)->first();
        $dailyLimit = (float) ($profile->daily_expense_limit ?? 500.00);

        $todayTransactions = Transaction::where('user_id', $user->id)
            ->whereDate('transaction_date', $today)
            ->with(['category', 'merchant'])
            ->latest('transaction_date')
            ->get();

        $todaySpent = (float) $todayTransactions->where('type', 'expense')->sum('amount');
        $isExceeded = $todaySpent > $dailyLimit;
        $overAmount = max(0, $todaySpent - $dailyLimit);
        $percentage = ($dailyLimit > 0) ? round(($todaySpent / $dailyLimit) * 100, 1) : 0;

        return response()->json([
            'status' => 'success',
            'phone' => $user->phone,
            'today_spent' => $todaySpent,
            'daily_limit' => $dailyLimit,
            'is_exceeded' => $isExceeded,
            'over_amount' => $overAmount,
            'percentage' => $percentage,
            'transactions_count' => $todayTransactions->count(),
            'recent' => $todayTransactions->take(10)->map(fn($t) => [
                'merchant' => $t->merchant?->name ?? $t->notes,
                'amount' => $t->amount,
                'payment_method' => $t->payment_method ?? 'Payment App',
                'time' => $t->transaction_date->format('H:i:s'),
            ]),
        ]);
    }

    public function updateLimit(Request $request)
    {
        $request->validate([
            'daily_expense_limit' => ['required', 'numeric', 'min:1'],
        ]);

        $user = Auth::user();
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            ['daily_expense_limit' => $request->daily_expense_limit]
        );

        return redirect()->back()->with('success', "Daily Expense Limit updated to \${$request->daily_expense_limit}!");
    }

    public function exportTodayPdf()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $todayTransactions = Transaction::where('user_id', $user->id)
            ->whereDate('transaction_date', $today)
            ->with(['category', 'merchant', 'bankAccount'])
            ->get();

        $todaySpent = $todayTransactions->where('type', 'expense')->sum('amount');

        $pdf = Pdf::loadView('reports.daily_pdf', [
            'user' => $user,
            'today' => $today,
            'transactions' => $todayTransactions,
            'todaySpent' => $todaySpent,
        ]);

        return $pdf->download('Today_Expenses_Report_' . $today->format('Y_m_d') . '.pdf');
    }

    public function scanTodaySms(Request $request, \App\Services\AppNotificationParserService $parser)
    {
        $request->validate([
            'sms_text' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $smsDump = $request->sms_text;
        
        // Split by lines or double newlines to process multiple SMS messages
        $lines = array_filter(explode("\n", $smsDump), fn($line) => trim($line) !== '');

        $processedCount = 0;
        $totalAmount = 0;

        foreach ($lines as $line) {
            $result = $parser->processPaymentAppAlert($user->id, trim($line));
            if ($result['success']) {
                $processedCount++;
                $totalAmount += $result['amount'];
            }
        }

        if ($processedCount > 0) {
            return redirect()->back()->with('success', "Successfully scanned Today's SMS Inbox! Auto-captured {$processedCount} live transactions totalling \${$totalAmount}.");
        }

        return redirect()->back()->with('error', "No valid transaction amounts detected from the provided SMS text.");
    }

    public function exportTodayCsv()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $transactions = Transaction::where('user_id', $user->id)
            ->whereDate('transaction_date', $today)
            ->with(['category', 'merchant'])
            ->get();

        $filename = "Today_Expenses_" . $today->format('Y_m_d') . ".csv";
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        fputcsv($handle, ['ID', 'Date', 'Merchant / Notes', 'Category', 'Payment Method', 'Type', 'Amount ($)']);

        foreach ($transactions as $tx) {
            fputcsv($handle, [
                $tx->id,
                $tx->transaction_date->format('Y-m-d H:i:s'),
                $tx->merchant?->name ?? $tx->notes,
                $tx->category?->name ?? 'General',
                $tx->payment_method ?? 'Payment App',
                strtoupper($tx->type),
                $tx->amount,
            ]);
        }

        fclose($handle);
        exit;
    }
}
