<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Transaction::where('user_id', $userId)
            ->with(['category', 'merchant', 'bankAccount']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'LIKE', "%{$search}%")
                  ->orWhere('reference_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('merchant', function ($mq) use ($search) {
                      $mq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();
        $categories = Category::all();
        $bankAccounts = BankAccount::where('user_id', $userId)->get();

        return view('transactions.index', [
            'transactions' => $transactions,
            'categories' => $categories,
            'bankAccounts' => $bankAccounts,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'string'],
            'transaction_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $userId = Auth::id();
        $notes = $request->notes ?? 'Manual Entry';

        // Auto Category & Merchant mapping
        $aiService = new GeminiAiService();
        $categoryName = $aiService->autoCategorize($notes);

        $category = Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            ['name' => $categoryName, 'type' => $request->type, 'icon' => 'tag', 'color' => '#6366f1']
        );

        $merchant = Merchant::firstOrCreate(
            ['name' => Str::limit($notes, 30)],
            ['category_id' => $category->id]
        );

        Transaction::create([
            'user_id' => $userId,
            'bank_account_id' => $request->bank_account_id,
            'category_id' => $category->id,
            'merchant_id' => $merchant->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'net_amount' => $request->amount,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => $request->transaction_date,
            'notes' => $notes,
            'payment_method' => $request->payment_method ?? 'Cash/Card',
        ]);

        return redirect()->back()->with('success', 'Transaction successfully logged!');
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully.');
    }
}
