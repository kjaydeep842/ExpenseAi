<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Goal;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BudgetAlertService;
use App\Services\GeminiAiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // 1. Calculate Today's Expense & Payment App Breakdown
        $todayExpense = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('amount');

        $paymentAppsToday = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereDate('transaction_date', Carbon::today())
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        // 2. Balances & Monthly Stats
        $bankBalance = BankAccount::where('user_id', $userId)->where('is_active', true)->sum('balance');
        $walletBalance = Wallet::where('user_id', $userId)->sum('balance');
        $totalBalance = $bankBalance + $walletBalance;

        $incomeThisMonth = (float) Transaction::where('user_id', $userId)
            ->whereIn('type', ['income', 'salary'])
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $expenseThisMonth = (float) Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $netSaved = $incomeThisMonth - $expenseThisMonth;
        $savingsRate = ($incomeThisMonth > 0) ? round(($netSaved / $incomeThisMonth) * 100, 1) : 0;

        // 3. Fetch Recent Transactions
        $recentTransactions = Transaction::where('user_id', $userId)
            ->with(['category', 'merchant', 'bankAccount'])
            ->latest('transaction_date')
            ->take(10)
            ->get();

        // 4. Category Spending Breakdown for ApexCharts
        $categoryBreakdown = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // 5. Budgets & Goals
        (new BudgetAlertService())->checkUserBudgets($userId);
        $budgets = Budget::where('user_id', $userId)->with('category')->take(4)->get();
        $goals = Goal::where('user_id', $userId)->where('status', 'active')->take(3)->get();

        // 6. AI Intelligence Tip
        $aiService = new GeminiAiService();
        $aiInsight = $aiService->askFinancialAssistant($userId, "Give me a quick 2-sentence summary of my budget health this month.");

        return view('dashboard', [
            'todayExpense' => $todayExpense,
            'paymentAppsToday' => $paymentAppsToday,
            'totalBalance' => $totalBalance,
            'incomeThisMonth' => $incomeThisMonth,
            'expenseThisMonth' => $expenseThisMonth,
            'netSaved' => $netSaved,
            'savingsRate' => $savingsRate,
            'recentTransactions' => $recentTransactions,
            'categoryBreakdown' => $categoryBreakdown,
            'budgets' => $budgets,
            'goals' => $goals,
            'aiInsight' => $aiInsight['answer'],
        ]);
    }
}
