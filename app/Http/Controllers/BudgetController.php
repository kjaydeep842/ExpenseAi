<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetAlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        (new BudgetAlertService())->checkUserBudgets($userId);

        $budgets = Budget::where('user_id', $userId)->with('category')->get();
        $categories = Category::where('type', 'expense')->get();

        return view('budgets.index', compact('budgets', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'period' => ['required', 'string'],
        ]);

        Budget::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'period' => $request->period,
            'amount' => $request->amount,
            'spent' => 0.00,
            'threshold_percentage' => $request->threshold_percentage ?? 80,
            'is_alert_enabled' => true,
        ]);

        return redirect()->back()->with('success', 'Smart Budget cap configured!');
    }
}
