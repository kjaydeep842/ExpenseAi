<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $bankAccounts = BankAccount::where('user_id', $userId)->with('bank')->get();
        $wallets = Wallet::where('user_id', $userId)->get();
        $banks = Bank::all();

        return view('accounts.index', compact('bankAccounts', 'wallets', 'banks'));
    }

    public function storeBankAccount(Request $request)
    {
        $request->validate([
            'account_name' => ['required', 'string'],
            'balance' => ['required', 'numeric'],
            'account_type' => ['required', 'string'],
        ]);

        BankAccount::create([
            'user_id' => Auth::id(),
            'bank_id' => $request->bank_id,
            'account_number' => $request->account_number ?? '•••• ' . rand(1000, 9999),
            'account_name' => $request->account_name,
            'account_type' => $request->account_type,
            'balance' => $request->balance,
            'currency' => 'USD',
            'color' => $request->color ?? '#6366f1',
        ]);

        return redirect()->back()->with('success', 'Bank Account added successfully!');
    }

    public function storeWallet(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'balance' => ['required', 'numeric'],
        ]);

        Wallet::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type ?? 'digital',
            'balance' => $request->balance,
            'currency' => 'USD',
            'color' => '#10b981',
        ]);

        return redirect()->back()->with('success', 'Wallet added successfully!');
    }
}
