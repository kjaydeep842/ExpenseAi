<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransactionSms;
use App\Models\User;
use App\Services\AppNotificationParserService;
use Illuminate\Http\Request;

class MobileBankSyncApiController extends Controller
{
    /**
     * Automated 5-Minute Mobile Banking & Payment App Sync Endpoint
     * Processes actual captured incoming SMS / Notification logs for the requested user.
     * Zero dummy mock records.
     */
    public function autoSync5Min(Request $request, AppNotificationParserService $parser)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'bank_transactions' => ['nullable', 'array'],
        ]);

        $phone = $request->phone;
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // 1. Fetch exact user matching the given phone number
        $user = User::where('phone', $phone)
            ->orWhere('phone', 'LIKE', "%{$cleanPhone}%")
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => "No user found registered with mobile number {$phone}.",
            ], 404);
        }

        // 2. Extract array of transactions if provided in request payload
        $rawTransactions = $request->input('bank_transactions', []);

        // 3. If array is empty, check for unparsed pending SMS logs in database
        if (empty($rawTransactions)) {
            $pendingLogs = TransactionSms::where('user_id', $user->id)
                ->where('parsed_status', 'unparsed')
                ->get();

            foreach ($pendingLogs as $log) {
                $rawTransactions[] = $log->raw_body;
            }
        }

        // If still no pending notifications exist
        if (empty($rawTransactions)) {
            return response()->json([
                'status' => 'success',
                'sync_interval' => '5_minutes',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                ],
                'synced_count' => 0,
                'synced_transactions' => [],
                'message' => "Mobile Device Listener is active for {$user->phone}. No pending unparsed transactions found.",
            ]);
        }

        $processedList = [];
        foreach ($rawTransactions as $text) {
            $result = $parser->processPaymentAppAlert($user->id, $text);
            if ($result['success']) {
                $processedList[] = [
                    'amount' => $result['amount'],
                    'merchant' => $result['merchant'],
                    'app_name' => $result['app_name'],
                    'transaction_id' => $result['transaction']->id,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'sync_interval' => '5_minutes',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
            ],
            'synced_count' => count($processedList),
            'synced_transactions' => $processedList,
            'message' => "Successfully synced " . count($processedList) . " real mobile banking transactions for {$user->name} ({$user->phone}).",
        ]);
    }
}
