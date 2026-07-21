<x-app-layout>
    <div x-data="{ 
        limitModal: false, 
        codeModal: false, 
        scanSmsModal: false,
        activeTab: 'curl',
        todaySpent: {{ $todaySpent }},
        transactionsCount: {{ count($todayTransactions) }},
        syncing: false,
        sampleSms: `Sent Rs.1.00 from Kotak Bank AC X8006 to ahirv003@okicici on 21-07-26.UPI Ref 656856054828. Not you, https://kotak.com/KBANKT/Fraud`,
        trigger5MinSync() {
            this.syncing = true;
            fetch('/api/v1/payment-app/auto-sync-5min', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ phone: '{{ auth()->user()->phone }}' })
            })
            .then(res => res.json())
            .then(data => {
                this.syncing = false;
                // Silently refresh numbers without full page reload
                this.refreshSummary();
            })
            .catch(() => { this.syncing = false; });
        },
        refreshSummary() {
            fetch('/api/v1/today-summary?phone={{ urlencode(auth()->user()->phone) }}')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        this.todaySpent = data.today_spent || this.todaySpent;
                        this.transactionsCount = data.transactions_count || this.transactionsCount;
                    }
                })
                .catch(() => {});
        },
        init() {
            // Poll silently every 60s — NO page reload, just update numbers in-place
            setInterval(() => { this.refreshSummary(); }, 60000);
        }
    }" class="space-y-8">
        
        <!-- Header Banner & Action Buttons -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 via-purple-500 to-emerald-400 flex items-center justify-center text-lg text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-calendar-day"></i>
                    </span>
                    Today's Expenses & Payment Apps Hub
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Live tracking of all bank & payment app transactions (GPay, PhonePe, Paytm, Apple Pay) linked to Mobile # 
                    <strong class="text-indigo-400 font-mono">{{ auth()->user()->phone ?? 'N/A' }}</strong> for {{ $today->format('F d, Y') }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('nativeSync.index') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-500/25 hover:brightness-110 transition flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-sm"></i> Live Bank Sync
                </a>
                <a href="{{ route('gpay.connect') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 hover:brightness-110 transition flex items-center gap-2">
                    <i class="fa-brands fa-google text-sm"></i> Connect Google Pay
                </a>
                <button @click="scanSmsModal = true" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-500/25 hover:brightness-110 transition flex items-center gap-2">
                    <i class="fa-solid fa-comment-sms text-sm"></i> Scan Today's SMS Inbox
                </button>
                <button @click="trigger5MinSync()" :disabled="syncing" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 hover:brightness-110 transition flex items-center gap-2">
                    <i class="fa-solid fa-arrows-rotate" :class="syncing ? 'animate-spin' : ''"></i> 
                    <span x-text="syncing ? 'Syncing Banking Data...' : 'Trigger 5-Min Auto-Sync Now'"></span>
                </button>
                <button @click="codeModal = true" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold text-xs shadow-lg shadow-purple-500/20 hover:brightness-110 transition flex items-center gap-2">
                    <i class="fa-solid fa-code"></i> Mobile API & Webhook Setup
                </button>
                <button @click="limitModal = true" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs font-bold hover:border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-amber-400"></i> Set Daily Limit (${{ number_format($dailyLimit, 2) }})
                </button>
                <a href="{{ route('daily.csv') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs font-bold hover:border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-csv text-emerald-400"></i> CSV Export
                </a>
                <a href="{{ route('daily.pdf') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-pink-600 text-white font-extrabold text-xs shadow-lg shadow-red-500/25 hover:brightness-110 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i> Download Today's PDF
                </a>
            </div>
        </div>

        <!-- 5-MINUTE AUTOMATED MOBILE BANKING SYNC STATUS BADGE -->
        <div class="glass-card rounded-2xl p-4 border border-indigo-500/40 bg-gradient-to-r from-slate-950 via-indigo-950/30 to-slate-950 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-emerald-400 animate-ping"></span>
                <div class="text-xs">
                    <span class="font-extrabold text-white">5-Minute Automated Mobile Banking Sync: <span class="text-emerald-400 font-mono">ACTIVE</span></span>
                    <p class="text-[11px] text-slate-400">Fetching all GPay, PhonePe & Bank SMS transactions every 5 mins for <strong class="text-indigo-400 font-mono">{{ auth()->user()->phone }}</strong></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-xl bg-slate-900 border border-slate-800 text-[11px] font-mono text-indigo-300">
                    Next Cron Execution: ~5 mins
                </span>
            </div>
        </div>

        <!-- Dynamic Mobile API Live Simulator Card -->
        <div class="glass-card rounded-3xl p-6 border border-emerald-500/40 bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        Dynamic Mobile Transaction Webhook Listener
                    </h3>
                    <p class="text-xs text-slate-400">
                        Automatically routes GPay, PhonePe, Paytm & Bank SMS alerts to your dynamic Mobile # 
                        <strong class="text-emerald-400 font-mono">{{ auth()->user()->phone }}</strong>
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-mono">
                    ● Live Endpoint Ready
                </span>
            </div>

            <form method="POST" action="{{ route('paymentApp.simulate') }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Your Mobile Phone #</label>
                        <input type="text" value="{{ auth()->user()->phone }}" readonly class="w-full px-4 py-2.5 rounded-2xl bg-slate-950 border border-slate-800 text-indigo-400 font-mono text-xs font-bold">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Payment App / SMS Notification Text</label>
                        <input type="text" name="notification_text" id="simNotificationInput" placeholder="e.g. Paid Rs. 500 to Starbucks via Google Pay" required class="w-full px-4 py-2.5 rounded-2xl bg-slate-900 border border-slate-700/80 text-white font-mono text-xs focus:outline-none focus:border-emerald-500 transition">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 font-bold text-xs text-white shadow-lg shadow-emerald-500/20 hover:brightness-110 transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i> Post API Transaction
                        </button>
                    </div>
                </div>
            </form>

            <!-- Quick Presets -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase mr-1">Quick Dynamic Tests:</span>
                <button type="button" onclick="document.getElementById('simNotificationInput').value = 'Paid Rs. 500 to Starbucks via Google Pay UPI ref 9482'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-brands fa-google text-indigo-400"></i> Google Pay $500
                </button>
                <button type="button" onclick="document.getElementById('simNotificationInput').value = 'Paid ₹ 350 to Swiggy via PhonePe UPI'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-solid fa-mobile-screen text-purple-400"></i> PhonePe $350
                </button>
                <button type="button" onclick="document.getElementById('simNotificationInput').value = 'Sent Rs 120 to Metro via Paytm Wallet'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-solid fa-wallet text-sky-400"></i> Paytm $120
                </button>
                <button type="button" onclick="document.getElementById('simNotificationInput').value = 'Charged $45.00 at Uber via Apple Pay'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-brands fa-apple text-slate-300"></i> Apple Pay $45
                </button>
            </div>
        </div>

        <!-- 🚨 CRITICAL DAILY EXPENSE LIMIT ALERT BANNER -->
        @if($isExceeded)
            <div class="glass-card rounded-3xl p-6 border-2 border-red-500/80 bg-gradient-to-r from-red-950/80 via-slate-950 to-red-950/60 shadow-2xl relative overflow-hidden animate-pulse">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-red-500/20 border-2 border-red-500/60 text-red-400 flex items-center justify-center shrink-0 text-2xl shadow-lg shadow-red-500/30">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-3">
                                <h2 class="text-xl font-black text-white">Daily Expense Limit Exceeded!</h2>
                                <span class="px-3 py-0.5 rounded-full bg-red-500 text-white text-[10px] font-black uppercase tracking-wider">ALERT OVER LIMIT</span>
                            </div>
                            <p class="text-xs text-red-200 leading-relaxed font-semibold">
                                You have spent <strong class="text-white font-mono">${{ number_format($todaySpent, 2) }}</strong> today, which exceeds your set daily limit of <strong class="text-white font-mono">${{ number_format($dailyLimit, 2) }}</strong> by <span class="text-red-400 font-extrabold">${{ number_format($overAmount, 2) }}</span>!
                            </p>
                        </div>
                    </div>

                    <div class="w-full md:w-64 space-y-2">
                        <div class="flex justify-between text-xs font-bold text-slate-300">
                            <span>Daily Budget Gauge</span>
                            <span class="text-red-400 font-mono">{{ $percentage }}% Spent</span>
                        </div>
                        <div class="w-full h-3 rounded-full bg-slate-900 border border-slate-800 overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-500 to-red-500 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Normal Daily Budget Progress Gauge -->
            <div class="glass-card rounded-3xl p-6 border border-indigo-500/30 bg-gradient-to-r from-slate-900 via-indigo-950/20 to-slate-900 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-shield-check"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-white">Daily Expense Budget: ${{ number_format($dailyLimit, 2) }}</h3>
                        <p class="text-xs text-slate-400">Today's total spent: <strong class="text-white">${{ number_format($todaySpent, 2) }}</strong> | Remaining allowance: <strong class="text-emerald-400">${{ number_format(max(0, $dailyLimit - $todaySpent), 2) }}</strong></p>
                    </div>
                </div>

                <div class="w-full md:w-72 space-y-1.5">
                    <div class="flex justify-between text-xs font-bold text-slate-300">
                        <span>Daily Capacity</span>
                        <span class="text-indigo-400 font-mono">{{ $percentage }}%</span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-900 border border-slate-800 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-emerald-400 rounded-full transition-all duration-500" style="width: {{ min(100, $percentage) }}%"></div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Payment Apps Breakdown Grid (GPay, PhonePe, Paytm, Apple Pay, Bank SMS, UPI) -->
        <div class="space-y-4">
            <h3 class="text-base font-extrabold text-white flex items-center gap-2">
                <i class="fa-solid fa-mobile-screen text-indigo-400"></i> Today's Payment Apps & Banks Outflow Breakdown
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Google Pay -->
                @php
                    $gpay = $paymentAppBreakdown->get('Google Pay');
                @endphp
                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-2 hover:border-indigo-500/50 transition">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 text-white">
                            <i class="fa-brands fa-google text-indigo-400 text-sm"></i> Google Pay
                        </span>
                        <span class="px-2 py-0.5 rounded bg-slate-900 text-[10px] text-slate-400 font-mono">{{ $gpay['count'] ?? 0 }} Txns</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-white">${{ number_format($gpay['total'] ?? 0, 2) }}</h2>
                    <p class="text-[11px] text-slate-500">Google Pay UPI transactions today</p>
                </div>

                <!-- PhonePe -->
                @php
                    $phonepe = $paymentAppBreakdown->get('PhonePe');
                @endphp
                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-2 hover:border-purple-500/50 transition">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 text-white">
                            <i class="fa-solid fa-mobile-screen text-purple-400 text-sm"></i> PhonePe
                        </span>
                        <span class="px-2 py-0.5 rounded bg-slate-900 text-[10px] text-slate-400 font-mono">{{ $phonepe['count'] ?? 0 }} Txns</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-purple-400">${{ number_format($phonepe['total'] ?? 0, 2) }}</h2>
                    <p class="text-[11px] text-slate-500">PhonePe UPI transactions today</p>
                </div>

                <!-- Paytm -->
                @php
                    $paytm = $paymentAppBreakdown->get('Paytm');
                @endphp
                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-2 hover:border-sky-500/50 transition">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 text-white">
                            <i class="fa-solid fa-wallet text-sky-400 text-sm"></i> Paytm Wallet
                        </span>
                        <span class="px-2 py-0.5 rounded bg-slate-900 text-[10px] text-slate-400 font-mono">{{ $paytm['count'] ?? 0 }} Txns</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-sky-400">${{ number_format($paytm['total'] ?? 0, 2) }}</h2>
                    <p class="text-[11px] text-slate-500">Paytm wallet & UPI today</p>
                </div>

                <!-- Apple Pay & Card -->
                @php
                    $apple = $paymentAppBreakdown->get('Apple Pay');
                @endphp
                <div class="glass-card rounded-2xl p-5 border border-slate-800 space-y-2 hover:border-emerald-500/50 transition">
                    <div class="flex items-center justify-between text-slate-400">
                        <span class="text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 text-white">
                            <i class="fa-brands fa-apple text-slate-200 text-sm"></i> Apple Pay
                        </span>
                        <span class="px-2 py-0.5 rounded bg-slate-900 text-[10px] text-slate-400 font-mono">{{ $apple['count'] ?? 0 }} Txns</span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-emerald-400">${{ number_format($apple['total'] ?? 0, 2) }}</h2>
                    <p class="text-[11px] text-slate-500">Apple Pay & card payments</p>
                </div>
            </div>
        </div>

        <!-- Today's Full Detailed Transaction Ledger -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-white">Today's Transactions Ledger</h3>
                    <p class="text-xs text-slate-400">Line items captured across all payment apps today for {{ auth()->user()->phone }}</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-900 border border-slate-800 text-xs font-mono text-slate-300">
                    Total {{ count($todayTransactions) }} Items
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Time</th>
                            <th class="py-3 px-4">Merchant / Details</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Payment App</th>
                            <th class="py-3 px-4 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($todayTransactions as $tx)
                            <tr class="hover:bg-slate-900/40 transition">
                                <td class="py-3.5 px-4 font-mono text-slate-400">{{ $tx->transaction_date->format('H:i:s') }}</td>
                                <td class="py-3.5 px-4 font-bold text-white">
                                    {{ $tx->merchant?->name ?? $tx->notes }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 font-semibold">
                                        {{ $tx->category?->name ?? 'General' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-indigo-400 font-mono font-bold text-[11px]">
                                        {{ $tx->payment_method ?? 'Payment App' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-sm {{ $tx->type === 'expense' ? 'text-red-400' : 'text-emerald-400' }}">
                                    {{ $tx->type === 'expense' ? '-' : '+' }}${{ number_format($tx->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 text-slate-500">No transactions recorded for today yet. Use the GPay/PhonePe simulator above to test!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Dynamic Mobile API Integration Guide Modal -->
        <div x-show="codeModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-3xl glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-5" @click.away="codeModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div>
                        <h3 class="font-bold text-white text-lg flex items-center gap-2">
                            <i class="fa-solid fa-code text-indigo-400"></i> 5-Minute Auto-Sync Mobile API Setup
                        </h3>
                        <p class="text-xs text-slate-400">Automatically pre-populated for Mobile # <strong class="text-indigo-300 font-mono">{{ auth()->user()->phone }}</strong></p>
                    </div>
                    <button @click="codeModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <!-- Tabs -->
                <div class="flex border-b border-slate-800 text-xs font-bold">
                    <button @click="activeTab = 'curl'" :class="activeTab === 'curl' ? 'border-b-2 border-indigo-500 text-indigo-400' : 'text-slate-400 hover:text-white'" class="px-4 py-2">5-Min Sync cURL</button>
                    <button @click="activeTab = 'cron'" :class="activeTab === 'cron' ? 'border-b-2 border-indigo-500 text-indigo-400' : 'text-slate-400 hover:text-white'" class="px-4 py-2">Laravel Cron Job</button>
                    <button @click="activeTab = 'node'" :class="activeTab === 'node' ? 'border-b-2 border-indigo-500 text-indigo-400' : 'text-slate-400 hover:text-white'" class="px-4 py-2">Node.js 5-Min Timer</button>
                </div>

                <!-- Tab Content -->
                <div class="space-y-3">
                    <!-- 5-Min cURL Tab -->
                    <div x-show="activeTab === 'curl'" class="space-y-2">
                        <p class="text-xs text-slate-300">Call this API endpoint every 5 minutes to pull or sync banking transactions for your mobile number:</p>
                        <pre class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-emerald-400 font-mono text-xs overflow-x-auto">
curl -X POST http://127.0.0.1:8080/api/v1/payment-app/auto-sync-5min \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "phone": "{{ auth()->user()->phone }}"
  }'</pre>
                    </div>

                    <!-- Cron Tab -->
                    <div x-show="activeTab === 'cron'" class="space-y-2">
                        <p class="text-xs text-slate-300">Add this line to your server crontab (`crontab -e`) to execute automated 5-minute banking sync background worker:</p>
                        <pre class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-purple-300 font-mono text-xs overflow-x-auto">
*/5 * * * * cd /home/vikartr-dev-1/Downloads/JBL/ExpenseAi && php artisan expense:sync-mobile-banking --phone="{{ auth()->user()->phone }}" >> /dev/null 2>&1</pre>
                    </div>

                    <!-- Node.js Tab -->
                    <div x-show="activeTab === 'node'" class="space-y-2">
                        <p class="text-xs text-slate-300">Node.js automated 5-minute polling interval snippet:</p>
                        <pre class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-sky-300 font-mono text-xs overflow-x-auto">
const fetch = require('node-fetch');

// Automatically poll every 5 minutes (300,000 ms)
setInterval(async () => {
  const response = await fetch('http://127.0.0.1:8080/api/v1/payment-app/auto-sync-5min', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ phone: '{{ auth()->user()->phone }}' })
  });
  const data = await response.json();
  console.log('5-Min Auto-Sync Result:', data);
}, 5 * 60 * 1000);</pre>
                    </div>
                </div>

                <div class="pt-2 text-right">
                    <button @click="codeModal = false" class="px-5 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-300 hover:text-white">Close Guide</button>
                </div>
            </div>
        </div>

        <!-- Daily Expense Limit Configuration Modal -->
        <div x-show="limitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="limitModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Configure Daily Expense Cap</h3>
                    <button @click="limitModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('daily.limit') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Set Daily Expense Limit ($)</label>
                        <input type="number" step="1" name="daily_expense_limit" value="{{ $dailyLimit }}" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-800 text-white font-mono text-base focus:border-indigo-500">
                        <p class="text-[11px] text-slate-500 mt-1">If your total daily expenses across Google Pay, PhonePe, Paytm, or banks exceed this amount, an instant alert banner will trigger.</p>
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white shadow-lg shadow-indigo-600/30">Save Daily Limit Rule</button>
                </form>
            </div>
        </div>

        <!-- Scan Today's SMS Inbox Modal -->
        <div x-show="scanSmsModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-lg glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="scanSmsModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-comment-sms"></i>
                        </span>
                        <h3 class="font-bold text-white text-base">Scan Today's Mobile SMS Transactions</h3>
                    </div>
                    <button @click="scanSmsModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form method="POST" action="{{ route('daily.scanSms') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Paste or Auto-Detect Today's SMS Text Messages</label>
                        <textarea name="sms_text" x-model="sampleSms" rows="5" required placeholder="Paste Kotak, HDFC, SBI, GPay, PhonePe or Paytm SMS here..." class="w-full px-4 py-3 rounded-2xl bg-slate-900 border border-slate-800 text-slate-200 font-mono text-xs focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition"></textarea>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="sampleSms = 'Sent Rs.1.00 from Kotak Bank AC X8006 to ahirv003@okicici on 21-07-26.UPI Ref 656856054828. Not you, https://kotak.com/KBANKT/Fraud'" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-medium text-cyan-400 hover:bg-slate-800 transition">
                            + Sample Kotak SMS
                        </button>
                        <button type="button" @click="sampleSms = 'Paid Rs. 500 to Starbucks Coffee via Google Pay. UPI Ref 92837192'" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-medium text-emerald-400 hover:bg-slate-800 transition">
                            + Sample GPay SMS
                        </button>
                        <button type="button" @click="sampleSms = 'Paid Rs 350 to Swiggy via PhonePe. UPI Ref 736281928'" class="px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-medium text-purple-400 hover:bg-slate-800 transition">
                            + Sample PhonePe SMS
                        </button>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-3">
                        <button type="button" @click="scanSmsModal = false" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-bold text-slate-400 hover:text-white">Cancel</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 via-teal-500 to-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-500/25 hover:brightness-110 transition flex items-center gap-2">
                            <i class="fa-solid fa-bolt"></i> Scan & Extract Today's Transactions
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
