<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Profile;
use App\Models\Transaction;
use App\Models\User;
use App\Services\GeminiAiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $searchPhone = trim($request->get('phone', ''));
        $selectedClient = null;
        $clientData = null;
        $clientsList = User::where('role', 'user')->whereNotNull('phone')->take(10)->get();

        if (!empty($searchPhone)) {
            // Clean phone string for matching
            $cleanPhone = preg_replace('/[^0-9]/', '', $searchPhone);

            $selectedClient = User::where(function ($query) use ($searchPhone, $cleanPhone) {
                $query->where('phone', $searchPhone)
                    ->orWhere('phone', 'LIKE', '%' . $cleanPhone . '%')
                    ->orWhere('email', $searchPhone);
            })->first();

            if ($selectedClient) {
                $clientId = $selectedClient->id;

                // 1. Calculate Daily Spending (Today)
                $todayExpenses = Transaction::where('user_id', $clientId)
                    ->where('type', 'expense')
                    ->whereDate('transaction_date', Carbon::today())
                    ->sum('amount');

                // 2. Calculate Monthly Outflow & Inflow
                $monthlyExpenses = Transaction::where('user_id', $clientId)
                    ->where('type', 'expense')
                    ->whereMonth('transaction_date', Carbon::now()->month)
                    ->sum('amount');

                $monthlyIncome = Transaction::where('user_id', $clientId)
                    ->whereIn('type', ['income', 'salary'])
                    ->whereMonth('transaction_date', Carbon::now()->month)
                    ->sum('amount');

                // 3. Accounts & Total Liquid Net Worth
                $accounts = BankAccount::where('user_id', $clientId)->get();
                $totalBalance = $accounts->sum('balance');

                // 4. Budgets & Thresholds
                $budgets = Budget::where('user_id', $clientId)->with('category')->get();

                // 5. Daily Spending Breakdown (Last 7 Days)
                $dailyChartData = [];
                $dailyChartCategories = [];
                for ($i = 6; $i >= 0; $i--) {
                    $day = Carbon::today()->subDays($i);
                    $dailySum = Transaction::where('user_id', $clientId)
                        ->where('type', 'expense')
                        ->whereDate('transaction_date', $day)
                        ->sum('amount');

                    $dailyChartCategories[] = $day->format('M d');
                    $dailyChartData[] = round($dailySum, 2);
                }

                // 6. Recent Daily Ledger
                $recentTransactions = Transaction::where('user_id', $clientId)
                    ->with(['category', 'merchant'])
                    ->latest('transaction_date')
                    ->take(15)
                    ->get();

                // 7. AI Financial Assessment for Client
                $aiService = new GeminiAiService();
                $aiPrompt = "Evaluate financial budget for client {$selectedClient->name} (Mobile: {$selectedClient->phone}). Today's expense: \${$todayExpenses}, Monthly budget total: \${$monthlyExpenses}, Balance: \${$totalBalance}. Provide 2 concise actionable advice bullets.";
                $aiAdvice = $aiService->askFinancialAssistant($clientId, $aiPrompt);

                $clientData = [
                    'today_expenses' => $todayExpenses,
                    'monthly_expenses' => $monthlyExpenses,
                    'monthly_income' => $monthlyIncome,
                    'total_balance' => $totalBalance,
                    'accounts' => $accounts,
                    'budgets' => $budgets,
                    'daily_chart_categories' => $dailyChartCategories,
                    'daily_chart_data' => $dailyChartData,
                    'recent_transactions' => $recentTransactions,
                    'ai_advice' => $aiAdvice['answer'] ?? 'Client daily financial record healthy.',
                ];
            }
        }

        $categories = Category::all();
        $merchants = Merchant::all();

        return view('clients.index', compact('searchPhone', 'selectedClient', 'clientData', 'clientsList', 'categories', 'merchants'));
    }

    public function createClient(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'initial_balance' => ['nullable', 'numeric'],
        ]);

        $email = $request->email ?? 'client_' . Str::random(6) . '@expenseai.test';

        $client = User::create([
            'name' => $request->name,
            'email' => $email,
            'phone' => $request->phone,
            'password' => Hash::make('password123'),
            'role' => 'user',
            'status' => 'active',
        ]);

        Profile::create(['user_id' => $client->id]);

        if ($request->filled('initial_balance') && $request->initial_balance > 0) {
            BankAccount::create([
                'user_id' => $client->id,
                'account_name' => 'Default Bank Account',
                'account_number' => '•••• ' . rand(1000, 9999),
                'account_type' => 'savings',
                'balance' => $request->initial_balance,
                'currency' => 'USD',
                'color' => '#6366f1',
            ]);
        }

        return redirect()->route('clients.index', ['phone' => $client->phone])
            ->with('success', "New Client '{$client->name}' created successfully with Mobile # {$client->phone}!");
    }

    public function storeExpense(Request $request, $clientId)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $client = User::findOrFail($clientId);

        // Find primary account
        $account = BankAccount::where('user_id', $client->id)->first();

        Transaction::create([
            'user_id' => $client->id,
            'bank_account_id' => $account?->id,
            'category_id' => $request->category_id,
            'merchant_id' => $request->merchant_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'net_amount' => $request->amount,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => $request->date ? Carbon::parse($request->date) : Carbon::now(),
            'notes' => $request->notes ?? 'Daily ledger entry via Client Portal',
            'payment_method' => $request->payment_method ?? 'Cash/Card',
        ]);

        // Adjust balance
        if ($account) {
            if ($request->type === 'expense') {
                $account->decrement('balance', $request->amount);
            } else {
                $account->increment('balance', $request->amount);
            }
        }

        return redirect()->back()->with('success', "Daily transaction logged for client {$client->name} ({$client->phone})!");
    }

    public function storeBudget(Request $request, $clientId)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'period' => ['required', 'string'],
        ]);

        $client = User::findOrFail($clientId);

        Budget::updateOrCreate(
            ['user_id' => $client->id, 'category_id' => $request->category_id],
            [
                'period' => $request->period,
                'amount' => $request->amount,
                'spent' => 0.00,
                'threshold_percentage' => 80,
                'is_alert_enabled' => true,
            ]
        );

        return redirect()->back()->with('success', "Daily/Monthly Budget cap set for client {$client->name}!");
    }
}
