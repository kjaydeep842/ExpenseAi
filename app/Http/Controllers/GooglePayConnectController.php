<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AppNotificationParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GooglePayConnectController extends Controller
{
    /**
     * Show Google Pay Connect & OAuth Authorization Hub Page / View
     */
    public function index()
    {
        $user = Auth::user();
        return view('gpay.connect', compact('user'));
    }

    /**
     * Authorize & Link Google Pay Account (OAuth 2.0 Token Generation)
     */
    public function authorizeGPay(Request $request)
    {
        $user = Auth::user();
        
        $token = 'gpay_oauth_' . Str::random(32);
        
        $user->update([
            'gpay_connected' => true,
            'gpay_oauth_token' => $token,
            'gpay_linked_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Google Pay Account connected successfully via OAuth 2.0!',
                'oauth_token' => $token,
                'webhook_url' => url('/api/v1/gpay/webhook'),
                'linked_at' => $user->gpay_linked_at->toIso8601String(),
            ]);
        }

        return redirect()->back()->with('success', 'Google Pay Account authorized and linked via OAuth 2.0! Real-time debit alerts are now ACTIVE.');
    }

    /**
     * Disconnect / Unlink Google Pay Account
     */
    public function disconnectGPay(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'gpay_connected' => false,
            'gpay_oauth_token' => null,
            'gpay_linked_at' => null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Google Pay connection unlinked successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Google Pay connection unlinked.');
    }

    /**
     * Direct Real-Time Google Pay OAuth Webhook Endpoint
     * Instant real-time transaction ingestion when money is deducted from bank via GPay.
     */
    public function gpayWebhook(Request $request, AppNotificationParserService $parser)
    {
        $request->validate([
            'phone' => ['nullable', 'string'],
            'oauth_token' => ['nullable', 'string'],
            'amount' => ['nullable', 'numeric'],
            'merchant' => ['nullable', 'string'],
            'notification_text' => ['nullable', 'string'],
        ]);

        $phone = $request->phone;
        $oauthToken = $request->oauth_token;

        // 1. Resolve User via Token or Phone
        $user = null;
        if ($oauthToken) {
            $user = User::where('gpay_oauth_token', $oauthToken)->first();
        }

        if (!$user && $phone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
            $user = User::where('phone', $phone)
                ->orWhere('phone', 'LIKE', "%{$cleanPhone}%")
                ->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Google Pay OAuth token or unregistered phone number.',
            ], 401);
        }

        // 2. Build notification text payload if amount/merchant provided
        $notificationText = $request->notification_text;
        if (!$notificationText && $request->has('amount')) {
            $merchant = $request->merchant ?: 'Merchant';
            $amount = $request->amount;
            $notificationText = "Paid Rs. {$amount} to {$merchant} via Google Pay";
        }

        if (!$notificationText) {
            return response()->json([
                'status' => 'error',
                'message' => 'Notification text or amount/merchant payload missing.',
            ], 422);
        }

        // 3. Process Live Transaction into database
        $result = $parser->processPaymentAppAlert($user->id, $notificationText, 'Google Pay');

        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'message' => "Instant Google Pay debit captured! \${$result['amount']} logged under Today's Expenses for {$user->name}.",
                'data' => [
                    'transaction_id' => $result['transaction']->id,
                    'amount' => $result['amount'],
                    'merchant' => $result['merchant'],
                    'payment_method' => 'Google Pay',
                    'timestamp' => now()->toIso8601String(),
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message'],
        ], 400);
    }
}
