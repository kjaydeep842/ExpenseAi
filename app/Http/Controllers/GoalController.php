<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\GoalTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->latest()->get();
        return view('goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string'],
            'target_amount' => ['required', 'numeric', 'min:1'],
        ]);

        Goal::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'target_amount' => $request->target_amount,
            'current_amount' => $request->current_amount ?? 0,
            'deadline' => $request->deadline,
            'category' => $request->category ?? 'savings',
            'status' => 'active',
            'icon' => 'bullseye',
            'color' => '#10b981',
        ]);

        return redirect()->back()->with('success', 'Savings goal created!');
    }

    public function deposit(Request $request, $id)
    {
        $request->validate(['amount' => ['required', 'numeric', 'min:1']]);
        $goal = Goal::where('user_id', Auth::id())->where('id', $id)->firstOrFail();

        $goal->increment('current_amount', $request->amount);

        GoalTransaction::create([
            'goal_id' => $goal->id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'note' => 'Manual Deposit',
            'type' => 'deposit',
        ]);

        return redirect()->back()->with('success', "Deposited \${$request->amount} into {$goal->title}!");
    }
}
