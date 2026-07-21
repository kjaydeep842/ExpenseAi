<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\ReceiptScan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReceiptOcrService
{
    /**
     * Parse receipt image, extract metadata, and auto create transaction draft
     */
    public function processReceiptScan(string $userId, string $imagePath): ReceiptScan
    {
        // Simulated OCR extraction engine parsing store name, tax/GST, amount, and items
        $merchantName = 'Supermart Store';
        $amount = 89.99;
        $gst = 8.18;
        $date = Carbon::now()->format('Y-m-d');
        $extractedItems = [
            ['item' => 'Organic Milk 1L', 'price' => 4.50],
            ['item' => 'Whole Wheat Bread', 'price' => 3.20],
            ['item' => 'Fresh Espresso Coffee', 'price' => 12.00],
            ['item' => 'Kitchen Utilities', 'price' => 70.29],
        ];

        // Match image keywords if file name contains clues
        if (str_contains(strtolower($imagePath), 'fuel') || str_contains(strtolower($imagePath), 'gas')) {
            $merchantName = 'Shell Oil Station';
            $amount = 45.00;
            $gst = 4.09;
        } elseif (str_contains(strtolower($imagePath), 'apple') || str_contains(strtolower($imagePath), 'store')) {
            $merchantName = 'Apple Store Digital';
            $amount = 199.00;
            $gst = 18.00;
        }

        $categoryName = (new GeminiAiService())->autoCategorize($merchantName);
        $category = Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            ['name' => $categoryName, 'type' => 'expense', 'icon' => 'tag', 'color' => '#10b981']
        );

        $merchant = Merchant::firstOrCreate(
            ['name' => $merchantName],
            ['category_id' => $category->id]
        );

        // Auto Create Transaction
        $transaction = Transaction::create([
            'user_id' => $userId,
            'category_id' => $category->id,
            'merchant_id' => $merchant->id,
            'type' => 'expense',
            'amount' => $amount,
            'tax_amount' => $gst,
            'net_amount' => $amount - $gst,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_date' => $date,
            'notes' => 'OCR Receipt Scan Auto Entry (' . count($extractedItems) . ' line items)',
            'attachment_url' => $imagePath,
            'payment_method' => 'Credit Card',
        ]);

        return ReceiptScan::create([
            'user_id' => $userId,
            'image_url' => $imagePath,
            'extracted_text' => "Store: {$merchantName}\nDate: {$date}\nAmount: \${$amount}\nGST: \${$gst}",
            'extracted_json' => [
                'merchant' => $merchantName,
                'items' => $extractedItems,
                'total' => $amount,
                'gst' => $gst,
            ],
            'merchant' => $merchantName,
            'amount' => $amount,
            'gst' => $gst,
            'date' => $date,
            'status' => 'confirmed',
            'transaction_id' => $transaction->id,
        ]);
    }
}
