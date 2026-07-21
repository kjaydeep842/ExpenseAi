<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileSyncBridgeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('mobile_sync.index', compact('user'));
    }

    public function downloadMacroConfig()
    {
        $user = Auth::user();
        $webhookUrl = url('/api/v1/payment-app/notification');

        $configJson = [
            'name' => 'ExpenseAI Real-Time GPay & Bank Sync',
            'version' => '1.0',
            'user_phone' => $user->phone,
            'webhook_url' => $webhookUrl,
            'rules' => [
                [
                    'trigger' => 'Notification Received from Google Pay, PhonePe, Paytm, Bank SMS',
                    'action' => 'HTTP POST to ' . $webhookUrl,
                    'payload' => [
                        'phone' => $user->phone,
                        'notification_text' => '{notification_text}',
                        'app_name' => '{notification_title_or_package}'
                    ]
                ]
            ]
        ];

        return response()->streamDownload(function () use ($configJson) {
            echo json_encode($configJson, JSON_PRETTY_PRINT);
        }, 'ExpenseAI_GPay_Live_Sync_' . preg_replace('/[^0-9]/', '', $user->phone) . '.json', [
            'Content-Type' => 'application/json',
        ]);
    }
}
