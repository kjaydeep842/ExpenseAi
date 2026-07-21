<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use App\Models\Transaction;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalTransactions = Transaction::count();
        $totalAiQueries = AiLog::count();
        $recentUsers = User::latest()->take(10)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalTransactions', 'totalAiQueries', 'recentUsers'));
    }
}
