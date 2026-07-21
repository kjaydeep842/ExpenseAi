<?php

namespace App\Http\Controllers;

use App\Services\AppNotificationParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentAppWebhookController extends Controller
{
    public function simulate(Request $request)
    {
        $request->validate([
            'notification_text' => ['required', 'string'],
            'app_name' => ['nullable', 'string'],
        ]);

        $userId = Auth::id();
        $parser = new AppNotificationParserService();
        $result = $parser->processPaymentAppAlert($userId, $request->notification_text, $request->app_name);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'] ?? 'Unable to parse payment app notification.');
    }

    public function apiWebhook(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'],
            'notification_text' => ['required', 'string'],
            'app_name' => ['nullable', 'string'],
        ]);

        $parser = new AppNotificationParserService();
        $result = $parser->processPaymentAppAlert($request->phone, $request->notification_text, $request->app_name);

        if ($result['success']) {
            return response()->json([
                'status' => 'success',
                'message' => $result['message'],
                'data' => [
                    'amount' => $result['amount'],
                    'merchant' => $result['merchant'],
                    'app_name' => $result['app_name'],
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => $result['message']
        ], 400);
    }
}
