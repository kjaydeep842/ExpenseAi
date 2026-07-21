<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\TransactionSms;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SmsParserService
{
    /**
     * Parse XML content from Android SMS Backup & Restore app
     */
    public function parseSmsXml(string $userId, string $xmlContent): array
    {
        $parsedCount = 0;
        $createdCount = 0;
        $errors = [];

        try {
            $xml = simplexml_load_string($xmlContent);
            if (!$xml) {
                return ['success' => false, 'message' => 'Invalid XML structure'];
            }

            foreach ($xml->sms as $sms) {
                $body = (string) $sms['body'];
                $sender = (string) $sms['address'];
                $dateMs = (string) $sms['date'];

                $date = !empty($dateMs) ? Carbon::createFromTimestampMs((int) $dateMs) : now();

                $result = $this->parseSingleSms($userId, $body, $sender, $date);
                if ($result['status'] === 'created') {
                    $createdCount++;
                }
                $parsedCount++;
            }
        } catch (\Throwable $e) {
            $errors[] = $e->getMessage();
        }

        return [
            'success' => true,
            'parsed_count' => $parsedCount,
            'created_count' => $createdCount,
            'errors' => $errors,
        ];
    }

    /**
     * Parse a single SMS string and auto-create or update transaction SMS entries
     */
    public function parseSingleSms(string $userId, string $body, ?string $sender = null, ?Carbon $date = null): array
    {
        $date = $date ?? now();
        $bodyClean = trim($body);

        // Regex patterns for financial transactions (INR / USD / EUR)
        // Matches: debited by 500, spent Rs.120.00, credited with 25000, Paid Rs 450 to Swiggy, etc.
        $amount = 0.0;
        $type = 'expense';
        $merchantName = 'Unknown Merchant';
        $bankName = 'Bank/Wallet';
        $refNo = null;

        if (preg_match('/(?:rs\.?|inr|usd|\$)\s*([\d,]+(?:\.\d{1,2})?)/i', $bodyClean, $matches)) {
            $amount = (float) str_replace(',', '', $matches[1]);
        } elseif (preg_match('/(?:debited|spent|paid|credited|received)\s*(?:by|of)?\s*(?:rs\.?|inr|\$)?\s*([\d,]+(?:\.\d{1,2})?)/i', $bodyClean, $matches)) {
            $amount = (float) str_replace(',', '', $matches[1]);
        }

        if ($amount <= 0) {
            return ['status' => 'skipped', 'message' => 'No transaction amount detected in SMS'];
        }

        if (preg_match('/credited|received|salary|refund|cashback|deposited/i', $bodyClean)) {
            $type = 'income';
        } elseif (preg_match('/debited|spent|paid|purchase|withdrawn|transferred/i', $bodyClean)) {
            $type = 'expense';
        }

        // Extract merchant name
        if (preg_match('/at\s+([A-Za-z0-9\s\-]+?)(?:\.|\s+on|\s+ref|\s+vpa|\s+avail|\s+bal|$)/i', $bodyClean, $m)) {
            $merchantName = trim($m[1]);
        } elseif (preg_match('/to\s+([A-Za-z0-9\s\-]+?)(?:\.|\s+on|\s+ref|\s+vpa|\s+avail|\s+bal|$)/i', $bodyClean, $m)) {
            $merchantName = trim($m[1]);
        }

        // Extract reference number
        if (preg_match('/(?:ref|rrn|upi\s*ref|txn\s*id)[\s:-]*([a-zA-Z0-9]+)/i', $bodyClean, $m)) {
            $refNo = trim($m[1]);
        }

        // Prevent Duplicate Entry by Ref No or duplicate body
        $existing = TransactionSms::where('user_id', $userId)
            ->where(function ($q) use ($refNo, $bodyClean) {
                if ($refNo) {
                    $q->where('ref_no', $refNo);
                } else {
                    $q->where('raw_body', $bodyClean);
                }
            })->first();

        if ($existing) {
            return ['status' => 'duplicate', 'message' => 'SMS transaction already processed'];
        }

        return DB::transaction(function () use ($userId, $bodyClean, $sender, $amount, $type, $merchantName, $bankName, $refNo, $date) {
            // Find or create Category & Merchant
            $categoryName = (new GeminiAiService())->autoCategorize($merchantName . ' ' . $bodyClean);
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'type' => $type, 'icon' => 'tag', 'color' => '#6366f1']
            );

            $merchant = Merchant::firstOrCreate(
                ['name' => Str::limit($merchantName, 50)],
                ['category_id' => $category->id, 'is_verified' => true]
            );

            // Create Transaction
            $transaction = Transaction::create([
                'user_id' => $userId,
                'category_id' => $category->id,
                'merchant_id' => $merchant->id,
                'type' => $type,
                'amount' => $amount,
                'net_amount' => $amount,
                'currency' => 'USD',
                'status' => 'completed',
                'transaction_date' => $date,
                'reference_number' => $refNo,
                'payment_method' => 'SMS / Bank',
                'raw_sms' => $bodyClean,
                'notes' => "Auto-imported from SMS: " . Str::limit($bodyClean, 80),
            ]);

            // Create Transaction SMS Record
            TransactionSms::create([
                'user_id' => $userId,
                'raw_body' => $bodyClean,
                'sender' => $sender,
                'amount' => $amount,
                'type' => $type,
                'merchant' => $merchantName,
                'bank' => $bankName,
                'ref_no' => $refNo,
                'parsed_status' => 'transaction_created',
                'transaction_id' => $transaction->id,
            ]);

            return ['status' => 'created', 'transaction_id' => $transaction->id];
        });
    }
}
