<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\TransactionImport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankStatementImportService
{
    /**
     * Import bank statement rows from array/CSV data
     */
    public function importStatementRows(string $userId, string $fileName, array $rows): array
    {
        $importRecord = TransactionImport::create([
            'user_id' => $userId,
            'file_name' => $fileName,
            'file_type' => pathinfo($fileName, PATHINFO_EXTENSION),
            'source' => 'bank_statement',
            'status' => 'processing',
            'total_count' => count($rows),
        ]);

        $processedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Expected headers or generic array keys: date, description, amount, type/credit_debit
                $dateStr = $row['date'] ?? $row[0] ?? null;
                $description = $row['description'] ?? $row[1] ?? 'Bank Transaction';
                $amountRaw = $row['amount'] ?? $row[2] ?? 0;
                $typeRaw = $row['type'] ?? $row[3] ?? 'expense';

                if (!$dateStr || !$amountRaw) {
                    continue;
                }

                $amount = abs((float) str_replace(['$', ',', 'Rs.'], '', $amountRaw));
                if ($amount <= 0) {
                    continue;
                }

                try {
                    $date = Carbon::parse($dateStr);
                } catch (\Throwable $e) {
                    $date = now();
                }

                $type = strtolower($typeRaw);
                if (!in_array($type, ['expense', 'income', 'transfer', 'refund', 'salary'])) {
                    $type = ((float) $amountRaw < 0 || str_contains(strtolower($description), 'debit')) ? 'expense' : 'income';
                }

                // Auto category & merchant
                $aiService = new GeminiAiService();
                $categoryName = $aiService->autoCategorize($description);
                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($categoryName)],
                    ['name' => $categoryName, 'type' => $type, 'icon' => 'tag', 'color' => '#3b82f6']
                );

                $merchant = Merchant::firstOrCreate(
                    ['name' => Str::limit($description, 40)],
                    ['category_id' => $category->id]
                );

                // Duplicate check
                $duplicate = Transaction::where('user_id', $userId)
                    ->where('amount', $amount)
                    ->where('transaction_date', $date->format('Y-m-d H:i:s'))
                    ->where('notes', 'LIKE', '%' . Str::limit($description, 20) . '%')
                    ->exists();

                if ($duplicate) {
                    continue;
                }

                Transaction::create([
                    'user_id' => $userId,
                    'category_id' => $category->id,
                    'merchant_id' => $merchant->id,
                    'type' => $type,
                    'amount' => $amount,
                    'net_amount' => $amount,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'transaction_date' => $date,
                    'notes' => 'Imported statement entry: ' . $description,
                    'payment_method' => 'Bank Statement',
                ]);

                $processedCount++;
            }

            $importRecord->update([
                'status' => 'completed',
                'processed_count' => $processedCount,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $errors[] = $e->getMessage();
            $importRecord->update([
                'status' => 'failed',
                'error_log' => $errors,
            ]);
        }

        return [
            'import_id' => $importRecord->id,
            'total' => count($rows),
            'processed' => $processedCount,
            'errors' => $errors,
        ];
    }
}
