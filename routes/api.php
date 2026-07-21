<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\MobileBankSyncApiController;
use App\Http\Controllers\Api\TransactionApiController;
use App\Http\Controllers\DailyHubController;
use App\Http\Controllers\PaymentAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/auth/login', [AuthApiController::class, 'login']);

// Webhook & Mobile Real-Time API for payment app notification listening
Route::post('/v1/payment-app/notification', [PaymentAppWebhookController::class, 'apiWebhook']);
Route::get('/v1/today-summary', [DailyHubController::class, 'getTodaySummaryApi']);

// 5-Minute Automated Mobile Banking Sync API Endpoint
Route::post('/v1/payment-app/auto-sync-5min', [MobileBankSyncApiController::class, 'autoSync5Min']);

// Direct Google Pay OAuth 2.0 Real-Time Webhook & Authorization
Route::post('/v1/gpay/webhook', [\App\Http\Controllers\GooglePayConnectController::class, 'gpayWebhook']);
Route::post('/v1/gpay/authorize', [\App\Http\Controllers\GooglePayConnectController::class, 'authorizeGPay']);

// Native Direct Mobile Banking Fetch API (Zero 3rd Party Apps Required)
Route::post('/v1/native-sync/fetch', [\App\Http\Controllers\NativeBankSyncController::class, 'fetchLiveTransactions']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/transactions', [TransactionApiController::class, 'index']);
});
