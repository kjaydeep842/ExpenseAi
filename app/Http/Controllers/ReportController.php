<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function exportPdf(Request $request)
    {
        $userId = Auth::id();
        $transactions = Transaction::where('user_id', $userId)->with(['category', 'merchant'])->latest('transaction_date')->take(50)->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'user' => Auth::user(),
            'transactions' => $transactions,
            'date' => now()->format('F d, Y'),
        ]);

        return $pdf->download('ExpenseAI_Statement_' . now()->format('Ymd') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $userId = Auth::id();
        $transactions = Transaction::where('user_id', $userId)->with(['category', 'merchant'])->get();

        $filename = "ExpenseAI_Export_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Type', 'Category', 'Merchant', 'Amount', 'Notes']);

            foreach ($transactions as $t) {
                fputcsv($file, [
                    $t->id,
                    $t->transaction_date->format('Y-m-d H:i'),
                    $t->type,
                    $t->category?->name ?? 'Uncategorized',
                    $t->merchant?->name ?? '',
                    $t->amount,
                    $t->notes
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
