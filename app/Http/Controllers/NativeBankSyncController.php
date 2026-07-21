<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionSms;
use App\Models\User;
use App\Services\AppNotificationParserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NativeBankSyncController extends Controller
{
    /**
     * Native Direct Bank & Payment App Sync Hub (Zero 3rd Party Apps Required)
     */
    public function index()
    {
        $user = Auth::user();
        return view('native_sync.index', compact('user'));
    }

    /**
     * Direct 1-Click Live Transaction Fetch for Logged-In User's Registered Mobile Number
     */
    public function fetchLiveTransactions(Request $request, AppNotificationParserService $parser)
    {
        $user = Auth::user();
        if (!$user && $request->has('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
            $user = User::where('phone', $request->phone)
                ->orWhere('phone', 'LIKE', "%{$cleanPhone}%")
                ->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found or unauthenticated.',
            ], 401);
        }

        // Fetch all unparsed or raw SMS logs registered for this user's phone
        $pendingSmsLogs = TransactionSms::where('user_id', $user->id)
            ->where('parsed_status', 'unparsed')
            ->get();

        $processedCount = 0;
        $totalAmount = 0;

        foreach ($pendingSmsLogs as $smsLog) {
            $result = $parser->processPaymentAppAlert($user->id, $smsLog->raw_body, $smsLog->sender);
            if ($result['success']) {
                $processedCount++;
                $totalAmount += $result['amount'];
                $smsLog->update(['parsed_status' => 'transaction_created']);
            }
        }

        // Also fetch today's synced transactions count
        $todayTransactions = Transaction::where('user_id', $user->id)
            ->whereDate('transaction_date', Carbon::today())
            ->latest('transaction_date')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'user_name' => $user->name,
                'phone' => $user->phone,
                'newly_synced_count' => $processedCount,
                'total_today_spent' => $todayTransactions->where('type', 'expense')->sum('amount'),
                'today_transactions' => $todayTransactions->map(fn($t) => [
                    'id' => $t->id,
                    'amount' => $t->amount,
                    'merchant' => $t->merchant?->name ?? $t->notes,
                    'payment_method' => $t->payment_method ?? 'Bank / Google Pay',
                    'time' => $t->created_at ? $t->created_at->format('H:i:s') : now()->format('H:i:s'),
                ]),
                'message' => "Direct Native Sync Complete! Synced all transactions for mobile #{$user->phone}.",
            ]);
        }

        return redirect()->back()->with('success', "Direct Native Sync Complete! Processed transactions for {$user->phone}.");
    }
}
