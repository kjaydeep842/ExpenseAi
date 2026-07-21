<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        
        <!-- Header Banner -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-12 h-12 rounded-2xl bg-white p-2 shadow-xl shadow-indigo-500/20 flex items-center justify-center">
                        <svg class="w-8 h-8" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                    </span>
                    Google Pay OAuth 2.0 Direct Connect
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Link your Google Pay account directly to ExpenseAI. Instantly capture live transaction debits the moment money is deducted from your bank.
                </p>
            </div>

            <div>
                @if($user->gpay_connected)
                    <span class="px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span> Google Pay OAuth: ACTIVE
                    </span>
                @else
                    <span class="px-4 py-2 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-bold flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> OAuth Disconnected
                    </span>
                @endif
            </div>
        </div>

        <!-- Main OAuth Connection Card -->
        <div class="glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl relative overflow-hidden space-y-6">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-6 border-b border-slate-800 pb-6">
                <div class="space-y-1 text-center md:text-left">
                    <h3 class="text-lg font-bold text-white">Google Pay Real-Time Integration Status</h3>
                    <p class="text-xs text-slate-400">Registered Phone: <strong class="text-indigo-400 font-mono">{{ $user->phone }}</strong></p>
                </div>

                @if($user->gpay_connected)
                    <form method="POST" action="{{ route('gpay.disconnect') }}">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 font-bold text-xs hover:bg-red-500/20 transition">
                            <i class="fa-solid fa-link-slash"></i> Revoke & Disconnect Google Pay
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('gpay.authorize') }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-600/30 hover:brightness-110 transition flex items-center gap-2">
                            <i class="fa-brands fa-google"></i> Connect & Authorize Google Pay Account
                        </button>
                    </form>
                @endif
            </div>

            @if($user->gpay_connected)
                <!-- Connected Details & Live Webhook Bridge -->
                <div class="space-y-4 pt-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">OAuth Access Token</span>
                            <p class="text-xs font-mono text-emerald-400 break-all">{{ $user->gpay_oauth_token }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1">
                            <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Authorized Timestamp</span>
                            <p class="text-xs font-mono text-slate-200">{{ $user->gpay_linked_at ? $user->gpay_linked_at->format('M d, Y - H:i:s') : 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-white flex items-center gap-2">
                                <i class="fa-solid fa-satellite-dish text-indigo-400"></i> Live Instant Bank Debit Webhook URL
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono">Real-Time Ingestion Active</span>
                        </div>
                        <pre class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-indigo-300 font-mono text-xs overflow-x-auto">{{ url('/api/v1/gpay/webhook') }}</pre>
                        <p class="text-[11px] text-slate-400">When any payment is debited on Google Pay, your bank sends instant webhook payloads directly to this endpoint with 0-second latency.</p>
                    </div>

                    <!-- Live Instant Test Simulation -->
                    <div class="pt-4 border-t border-slate-800">
                        <h4 class="text-sm font-bold text-white mb-2">Instant Test Simulation</h4>
                        <p class="text-xs text-slate-400 mb-4">Click below to simulate a real-time Google Pay payment of ₹1.00 or ₹500.00 directly into your Today's Expenses.</p>
                        
                        <div class="flex flex-wrap gap-3">
                            <button onclick="simulateGPayDebit(1.00, 'Kotak UPI / ahirv003@okicici')" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs font-bold text-emerald-400 hover:bg-slate-800 transition flex items-center gap-2">
                                <i class="fa-solid fa-bolt"></i> Test GPay Debit ₹1.00
                            </button>
                            <button onclick="simulateGPayDebit(500.00, 'Starbucks Coffee')" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs font-bold text-indigo-400 hover:bg-slate-800 transition flex items-center gap-2">
                                <i class="fa-solid fa-bolt"></i> Test GPay Debit ₹500.00
                            </button>
                            <a href="{{ route('daily.index') }}" class="px-4 py-2.5 rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-lg shadow-indigo-600/30 hover:brightness-110 transition flex items-center gap-2">
                                View Daily Hub <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6 rounded-2xl bg-slate-900/50 border border-slate-800 text-center space-y-3">
                    <p class="text-xs text-slate-300">Click the button above to authorize Google Pay. Once linked, any payment made from your registered mobile phone number (<strong class="text-white">{{ $user->phone }}</strong>) will instantly reflect on your ExpenseAI dashboard.</p>
                </div>
            @endif
        </div>

    </div>

    <script>
        function simulateGPayDebit(amount, merchant) {
            fetch('/api/v1/gpay/webhook', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    oauth_token: '{{ $user->gpay_oauth_token }}',
                    phone: '{{ $user->phone }}',
                    amount: amount,
                    merchant: merchant,
                    notification_text: 'Paid Rs. ' + amount + ' to ' + merchant + ' via Google Pay'
                })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Transaction captured!');
                window.location.href = '{{ route("daily.index") }}';
            })
            .catch(err => { alert('Failed to simulate transaction.'); });
        }
    </script>
</x-app-layout>
