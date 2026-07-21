<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AiAssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReceiptScanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/pricing', [LandingController::class, 'pricing'])->name('pricing');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Application Vault Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Client Mobile Lookup & Daily Budget Management
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients/store', [ClientController::class, 'createClient'])->name('clients.store');
    Route::post('/clients/{clientId}/expense', [ClientController::class, 'storeExpense'])->name('clients.expense');
    Route::post('/clients/{clientId}/budget', [ClientController::class, 'storeBudget'])->name('clients.budget');

    // Daily Expense & Payment Apps Hub
    Route::get('/daily-hub', [\App\Http\Controllers\DailyHubController::class, 'index'])->name('daily.index');
    Route::post('/daily-hub/limit', [\App\Http\Controllers\DailyHubController::class, 'updateLimit'])->name('daily.limit');
    Route::post('/daily-hub/scan-today-sms', [\App\Http\Controllers\DailyHubController::class, 'scanTodaySms'])->name('daily.scanSms');
    Route::get('/daily-hub/pdf', [\App\Http\Controllers\DailyHubController::class, 'exportTodayPdf'])->name('daily.pdf');
    Route::get('/daily-hub/csv', [\App\Http\Controllers\DailyHubController::class, 'exportTodayCsv'])->name('daily.csv');

    // Google Pay Direct OAuth Connect & Link
    Route::get('/gpay/connect', [\App\Http\Controllers\GooglePayConnectController::class, 'index'])->name('gpay.connect');
    Route::post('/gpay/authorize', [\App\Http\Controllers\GooglePayConnectController::class, 'authorizeGPay'])->name('gpay.authorize');
    Route::post('/gpay/disconnect', [\App\Http\Controllers\GooglePayConnectController::class, 'disconnectGPay'])->name('gpay.disconnect');

    // Native Direct Bank Sync (Zero 3rd Party Apps Required)
    Route::get('/native-sync', [\App\Http\Controllers\NativeBankSyncController::class, 'index'])->name('nativeSync.index');
    Route::post('/native-sync/fetch', [\App\Http\Controllers\NativeBankSyncController::class, 'fetchLiveTransactions'])->name('nativeSync.fetch');

    // Mobile Listener Bridge (MacroDroid / Tasker Setup)
    Route::get('/mobile-sync', [\App\Http\Controllers\MobileSyncBridgeController::class, 'index'])->name('mobileSync.index');
    Route::get('/mobile-sync/download-config', [\App\Http\Controllers\MobileSyncBridgeController::class, 'downloadMacroConfig'])->name('mobileSync.download');

    // Payment App Notification Parser (Google Pay, PhonePe, Paytm, Apple Pay)
    Route::post('/payment-app/simulate', [\App\Http\Controllers\PaymentAppWebhookController::class, 'simulate'])->name('paymentApp.simulate');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Import & SMS Parsing
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/sms', [ImportController::class, 'importSmsXml'])->name('import.sms');
    Route::post('/import/csv', [ImportController::class, 'importCsv'])->name('import.csv');

    // Receipt OCR Scanning
    Route::get('/receipts', [ReceiptScanController::class, 'index'])->name('receipts.index');
    Route::post('/receipts/scan', [ReceiptScanController::class, 'scan'])->name('receipts.scan');

    // Bank Accounts & Wallets
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts/bank', [AccountController::class, 'storeBankAccount'])->name('accounts.bank.store');
    Route::post('/accounts/wallet', [AccountController::class, 'storeWallet'])->name('accounts.wallet.store');

    // Budgets
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');

    // Savings Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::post('/goals/{id}/deposit', [GoalController::class, 'deposit'])->name('goals.deposit');

    // Subscriptions
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');

    // Reports & Exports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/csv', [ReportController::class, 'exportCsv'])->name('reports.csv');

    // AI Intelligence Console
    Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai.index');
    Route::post('/ai-assistant/ask', [AiAssistantController::class, 'ask'])->name('ai.ask');

    // Notifications Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'updateProfile'])->name('settings.update');

    // Admin Panel
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});
