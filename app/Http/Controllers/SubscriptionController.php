<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::where('user_id', Auth::id())->with(['merchant', 'category'])->latest()->get();
        $totalMonthlyCost = $subscriptions->where('status', 'active')->sum('amount');
        $categories = Category::all();
        $merchants = Merchant::all();

        return view('subscriptions.index', compact('subscriptions', 'totalMonthlyCost', 'categories', 'merchants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'billing_cycle' => ['required', 'string'],
            'next_billing_date' => ['required', 'date'],
        ]);

        Subscription::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'amount' => $request->amount,
            'billing_cycle' => $request->billing_cycle,
            'next_billing_date' => $request->next_billing_date,
            'auto_renew' => true,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Subscription logged for auto-tracking!');
    }
}
