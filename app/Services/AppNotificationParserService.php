<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\TransactionSms;
use App\Models\User;
use Carbon\Carbon;

class AppNotificationParserService
{
    /**
     * Universal Multi-Bank & Payment App Alert Parser Engine
     * Supports ALL Indian & Global Banks (Kotak, HDFC, ICICI, SBI, Axis, PNB, BOB, YES, IndusInd, SCB)
     * and Payment Apps (Google Pay, PhonePe, Paytm, CRED, WhatsApp Pay, Apple Pay)
     */
    public function processPaymentAppAlert($userIdOrPhone, string $notificationText, ?string $appName = null): array
    {
        // 1. Resolve User by phone or ID
        $user = null;
        if (is_string($userIdOrPhone) && (str_contains($userIdOrPhone, '+') || is_numeric($userIdOrPhone))) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $userIdOrPhone);
            $user = User::where('phone', $userIdOrPhone)
                ->orWhere('phone', 'LIKE', "%{$cleanPhone}%")
                ->first();
        }

        if (!$user) {
            $user = User::find($userIdOrPhone);
        }

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found for the given mobile phone number or ID.',
            ];
        }

        // Clean out fraud warning links
        $cleanText = preg_replace('/https?:\/\/\S+/i', '', $notificationText);
        $textLower = strtolower($cleanText);

        // 2. Determine Transaction Type (Expense vs Income/Refund)
        $txType = 'expense';
        if (preg_match('/(?:credited|received|deposited|added|refunded|cashback|salary)/i', $cleanText)) {
            $txType = 'income';
        }

        // 3. Robust Amount Extraction (Handles Rs.1.00, Rs 1.00, ₹1,000, INR 500, $50, Sent Rs.1.00, Debited Rs 100)
        $amount = 0.00;
        if (preg_match('/(?:Rs\.?|INR|₹|\$)\s*([\d,]+(?:\.\d{1,2})?)/i', $cleanText, $matches)) {
            $amount = (float) str_replace(',', '', $matches[1]);
        } elseif (preg_match('/(?:paid|sent|debited|charged|spent|withdrawn|credited|received)\s+(?:for|by|of)?\s*(?:Rs\.?|INR|₹|\$)?\s*([\d,]+(?:\.\d{1,2})?)/i', $cleanText, $matches)) {
            $amount = (float) str_replace(',', '', $matches[1]);
        } else {
            // Fallback digit pattern matching
            if (preg_match('/([\d]+(?:\.\d{1,2})?)/', $cleanText, $matches)) {
                $amount = (float) $matches[1];
            }
        }

        if ($amount <= 0) {
            TransactionSms::create([
                'user_id' => $user->id,
                'raw_body' => $notificationText,
                'sender' => $appName ?: 'MOBILE_LISTENER',
                'parsed_status' => 'unparsed',
            ]);

            return [
                'success' => false,
                'message' => 'Unable to detect a valid transaction amount from notification.',
            ];
        }

        // 4. Universal Payment Source Detection
        $detectedApp = $appName ?: 'Google Pay';

        if (str_contains($textLower, 'google pay') || str_contains($textLower, 'gpay') || str_contains($textLower, 'g pay') || str_contains($textLower, 'googlepay') || str_contains($textLower, '@okicici') || str_contains($textLower, '@okaxis') || str_contains($textLower, '@okhdfcbank') || str_contains($textLower, '@oksbi')) {
            $detectedApp = 'Google Pay';
        } elseif (str_contains($textLower, 'phonepe') || str_contains($textLower, 'phone pe') || str_contains($textLower, '@ybl') || str_contains($textLower, '@ibl')) {
            $detectedApp = 'PhonePe';
        } elseif (str_contains($textLower, 'paytm') || str_contains($textLower, '@paytm')) {
            $detectedApp = 'Paytm';
        } elseif (str_contains($textLower, 'cred')) {
            $detectedApp = 'CRED UPI';
        } elseif (str_contains($textLower, 'apple pay')) {
            $detectedApp = 'Apple Pay';
        } elseif (str_contains($textLower, 'kotak')) {
            $detectedApp = 'Kotak Bank / GPay';
        } elseif (str_contains($textLower, 'hdfc')) {
            $detectedApp = 'HDFC Bank';
        } elseif (str_contains($textLower, 'icici')) {
            $detectedApp = 'ICICI Bank';
        } elseif (str_contains($textLower, 'sbi') || str_contains($textLower, 'state bank')) {
            $detectedApp = 'SBI';
        } elseif (str_contains($textLower, 'axis')) {
            $detectedApp = 'Axis Bank';
        } elseif (str_contains($textLower, 'upi')) {
            $detectedApp = $appName ?: 'Google Pay';
        }

        // 5. Merchant & Beneficiary Extraction
        $merchantName = 'UPI Beneficiary / Merchant';
        $categorySlug = 'utilities-bills';

        if (preg_match('/(?:to|at|info:|vpa|towards)\s+([A-Za-z0-9\._\-@\s]+?)(?:\s+via|\s+using|\s+ref|\s+on|\s+from|\.|$)/i', $cleanText, $merchantMatches)) {
            $extracted = trim($merchantMatches[1]);
            if (strlen($extracted) > 2) {
                $merchantName = $extracted;
            }
        }

        // Auto Category Mapping
        if (str_contains($textLower, 'swiggy') || str_contains($textLower, 'zomato') || str_contains($textLower, 'starbucks') || str_contains($textLower, 'food') || str_contains($textLower, 'dining') || str_contains($textLower, 'coffee') || str_contains($textLower, 'restaurant')) {
            $categorySlug = 'food-dining';
        } elseif (str_contains($textLower, 'uber') || str_contains($textLower, 'ola') || str_contains($textLower, 'travel') || str_contains($textLower, 'metro') || str_contains($textLower, 'flight') || str_contains($textLower, 'petrol') || str_contains($textLower, 'fuel')) {
            $categorySlug = 'travel-transport';
        } elseif (str_contains($textLower, 'amazon') || str_contains($textLower, 'flipkart') || str_contains($textLower, 'store') || str_contains($textLower, 'shopping') || str_contains($textLower, 'myntra')) {
            $categorySlug = 'shopping';
        }

        $category = Category::where('slug', $categorySlug)->first() ?? Category::first();
        $merchant = Merchant::where('name', 'LIKE', "%{$merchantName}%")->first();

        if (!$merchant && strlen($merchantName) > 2) {
            $merchant = Merchant::create([
                'name' => ucfirst($merchantName),
                'category_id' => $category?->id,
                'is_verified' => false,
            ]);
        }

        // 6. Save Transaction under TODAY'S DATE
        $account = BankAccount::where('user_id', $user->id)->first();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'bank_account_id' => $account?->id,
            'category_id' => $category?->id,
            'merchant_id' => $merchant?->id,
            'type' => $txType,
            'amount' => $amount,
            'net_amount' => $amount,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => Carbon::now(),
            'notes' => "Auto-detected via {$detectedApp}: " . substr($notificationText, 0, 100),
            'payment_method' => $detectedApp,
        ]);

        // 7. Record Audit Trail
        TransactionSms::create([
            'user_id' => $user->id,
            'raw_body' => $notificationText,
            'sender' => $detectedApp,
            'amount' => $amount,
            'merchant' => $merchantName,
            'bank' => $detectedApp,
            'parsed_status' => 'transaction_created',
            'transaction_id' => $transaction->id,
        ]);

        // Update Account balance
        if ($account) {
            if ($txType === 'expense') {
                $account->decrement('balance', $amount);
            } else {
                $account->increment('balance', $amount);
            }
        }

        return [
            'success' => true,
            'transaction' => $transaction,
            'user' => $user,
            'app_name' => $detectedApp,
            'amount' => $amount,
            'merchant' => $merchantName,
            'message' => "Success! Auto-captured {$detectedApp} {$txType} of \${$amount} under Today's Expenses for {$user->name} ({$user->phone}).",
        ];
    }
}
