<?php

namespace App\Http\Controllers;

use App\Models\TransactionNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = TransactionNotification::where('user_id', Auth::id())->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    public function markAllAsRead()
    {
        TransactionNotification::where('user_id', Auth::id())->update(['status' => 'read', 'read_at' => now()]);
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
