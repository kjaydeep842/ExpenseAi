<?php

namespace App\Http\Controllers;

use App\Models\ReceiptScan;
use App\Services\ReceiptOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptScanController extends Controller
{
    public function index()
    {
        $scans = ReceiptScan::where('user_id', Auth::id())->with('transaction')->latest()->get();
        return view('receipts.index', compact('scans'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'receipt' => ['required', 'image', 'max:10240'],
        ]);

        $file = $request->file('receipt');
        $path = $file->store('receipts', 'public');

        $ocrService = new ReceiptOcrService();
        $scanRecord = $ocrService->processReceiptScan(Auth::id(), '/storage/' . $path);

        return redirect()->back()->with('success', "Receipt Scanned! Auto-created transaction for {$scanRecord->merchant} (\${$scanRecord->amount}).");
    }
}
