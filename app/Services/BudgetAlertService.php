<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;

class BudgetAlertService
{
    /**
     * Recalculate spending against budgets and emit smart alerts
     */
    public function checkUserBudgets(string $userId): array
    {
        $budgets = Budget::where('user_id', $userId)->with(['category', 'merchant'])->get();
        $alertsGenerated = [];

        foreach ($budgets as $budget) {
            $query = Transaction::where('user_id', $userId)
                ->where('type', 'expense');

            if ($budget->category_id) {
                $query->where('category_id', $budget->category_id);
            }
            if ($budget->merchant_id) {
                $query->where('merchant_id', $budget->merchant_id);
            }

            if ($budget->period === 'monthly') {
                $query->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year);
            } elseif ($budget->period === 'daily') {
                $query->whereDate('transaction_date', now()->today());
            } elseif ($budget->period === 'weekly') {
                $query->whereBetween('transaction_date', [now()->startOfWeek(), now()->endOfWeek()]);
            }

            $spent = (float) $query->sum('amount');
            $budget->update(['spent' => $spent]);

            $percentageUsed = ($budget->amount > 0) ? ($spent / $budget->amount) * 100 : 0;

            if ($percentageUsed >= $budget->threshold_percentage && $budget->is_alert_enabled) {
                $categoryName = $budget->category?->name ?? $budget->merchant?->name ?? 'General Category';

                $alertsGenerated[] = [
                    'budget_id' => $budget->id,
                    'category' => $categoryName,
                    'percentage' => round($percentageUsed, 1),
                ];
            }
        }

        return $alertsGenerated;
    }
}
