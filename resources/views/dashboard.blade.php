<x-app-layout>
    <!-- Welcome Header & Quick Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                Good day, <span class="text-gradient">{{ auth()->user()->name }}</span>
                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 font-mono border border-emerald-500/30">
                    <i class="fa-solid fa-phone text-[10px] mr-1"></i>{{ auth()->user()->phone ?? '+18005550122' }}
                </span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Real-time payment app auto-detection & daily expense budget intelligence.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('import.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs font-semibold hover:border-slate-700 flex items-center gap-2 transition">
                <i class="fa-solid fa-file-import text-indigo-400"></i> Import Statement
            </a>
            <a href="{{ route('receipts.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-slate-200 text-xs font-semibold hover:border-slate-700 flex items-center gap-2 transition">
                <i class="fa-solid fa-camera text-purple-400"></i> OCR Receipt
            </a>
            <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 hover:brightness-110 flex items-center gap-2 transition">
                <i class="fa-solid fa-plus"></i> New Transaction
            </a>
        </div>
    </div>

    <!-- Real-Time Today's Expense & Payment Apps Detection Bar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Today's Expense Card -->
        <div class="glass-card rounded-3xl p-6 border border-indigo-500/40 bg-gradient-to-br from-slate-900 via-indigo-950/30 to-slate-900 space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between text-xs font-extrabold text-indigo-300 uppercase tracking-wider">
                <span>Today's Live Expenses</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-mono animate-pulse">● Auto-Listening</span>
            </div>

            <div class="flex items-baseline gap-3">
                <h2 class="text-4xl font-extrabold text-white">${{ number_format($todayExpense, 2) }}</h2>
                <span class="text-xs text-slate-400">Logged Today</span>
            </div>

            <!-- Payment Apps Breakdown Chips for Today -->
            <div class="pt-2 border-t border-slate-800/80 flex flex-wrap items-center gap-2">
                @forelse($paymentAppsToday as $pa)
                    <span class="px-2.5 py-1 rounded-xl bg-slate-900/90 border border-indigo-500/30 text-[11px] text-slate-200 font-semibold flex items-center gap-1.5">
                        <i class="fa-solid fa-mobile-screen text-indigo-400"></i>
                        <span>{{ $pa->payment_method }}:</span>
                        <strong class="text-emerald-400">${{ number_format($pa->total, 2) }}</strong>
                    </span>
                @empty
                    <span class="text-[11px] text-slate-500">No payment app transactions detected yet today.</span>
                @endforelse
            </div>
        </div>

        <!-- Live Payment App Notification Simulator -->
        <div class="lg:col-span-2 glass-card rounded-3xl p-6 border border-emerald-500/30 bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-white flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-amber-400"></i>
                        Simulate Payment App Auto-Detection (GPay, PhonePe, Paytm, Apple Pay)
                    </h3>
                    <p class="text-xs text-slate-400">Test sending a payment notification to auto-add to Today's Expenses under your Mobile # {{ auth()->user()->phone ?? '' }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('paymentApp.simulate') }}" class="space-y-3">
                @csrf
                <div class="flex flex-col sm:flex-row gap-2">
                    <input type="text" name="notification_text" id="simNotification" placeholder="e.g. Paid Rs. 500 to Starbucks via Google Pay" required class="flex-1 px-4 py-2.5 rounded-2xl bg-slate-900 border border-slate-700/80 text-white font-mono text-xs focus:outline-none focus:border-emerald-500 transition">
                    
                    <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 font-bold text-xs text-white shadow-lg shadow-emerald-500/20 hover:brightness-110 transition shrink-0 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-down-to-bracket"></i> Auto-Capture Expense
                    </button>
                </div>
            </form>

            <!-- Quick Preset Simulator Buttons -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase mr-1">Quick Presets:</span>
                <button type="button" onclick="document.getElementById('simNotification').value = 'Paid Rs. 500 to Starbucks via Google Pay'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-brands fa-google text-indigo-400"></i> Google Pay $500
                </button>
                <button type="button" onclick="document.getElementById('simNotification').value = 'Paid ₹ 350 to Swiggy via PhonePe UPI'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-solid fa-mobile-screen text-purple-400"></i> PhonePe $350
                </button>
                <button type="button" onclick="document.getElementById('simNotification').value = 'Sent Rs 120 to Metro via Paytm Wallet'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-solid fa-wallet text-sky-400"></i> Paytm $120
                </button>
                <button type="button" onclick="document.getElementById('simNotification').value = 'Charged $45.00 at Uber via Apple Pay'" class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 hover:border-emerald-500 text-[11px] text-slate-300 font-mono transition">
                    <i class="fa-brands fa-apple text-slate-300"></i> Apple Pay $45
                </button>
            </div>
        </div>
    </div>

    <!-- AI Intelligence Insight Card -->
    <div class="glass-card rounded-3xl p-5 border border-indigo-500/30 bg-gradient-to-r from-indigo-950/40 via-purple-950/20 to-slate-950 relative overflow-hidden">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 border border-indigo-500/40 text-indigo-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-brain text-lg animate-pulse"></i>
            </div>
            <div class="flex-1 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-indigo-300 uppercase tracking-widest text-[10px]">Gemini AI Assistant Insight</span>
                    <a href="{{ route('ai.index') }}" class="text-indigo-400 hover:underline text-[11px] font-semibold">Ask Gemini More →</a>
                </div>
                <div class="text-slate-300 leading-relaxed font-medium">
                    {!! Str::markdown($aiInsight) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Top Key Financial Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Net Worth -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 space-y-2 hover:border-indigo-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Total Net Worth</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-vault"></i>
                </div>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-white">${{ number_format($totalBalance, 2) }}</h2>
            <p class="text-[11px] text-slate-500">Combined Bank & Crypto Wallets</p>
        </div>

        <!-- Monthly Income -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 space-y-2 hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Income (This Month)</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-arrow-down-left text-emerald-400"></i>
                </div>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-emerald-400">+${{ number_format($incomeThisMonth, 2) }}</h2>
            <p class="text-[11px] text-slate-500">Payroll & Refunds</p>
        </div>

        <!-- Monthly Expenses -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 space-y-2 hover:border-red-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Expenses (This Month)</span>
                <div class="w-8 h-8 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-arrow-up-right text-red-400"></i>
                </div>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-red-400">-${{ number_format($expenseThisMonth, 2) }}</h2>
            <p class="text-[11px] text-slate-500">Outflows & Bill Payments</p>
        </div>

        <!-- Savings Rate -->
        <div class="glass-card rounded-2xl p-5 border border-slate-800/80 space-y-2 hover:border-purple-500/40 transition">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-xs font-semibold uppercase tracking-wider">Savings Rate</span>
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-piggy-bank"></i>
                </div>
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-purple-400">{{ $savingsRate }}%</h2>
            <p class="text-[11px] text-slate-500">${{ number_format($netSaved, 2) }} Net Retained</p>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Cashflow Trend (ApexCharts) -->
        <div class="lg:col-span-2 glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-white">Cash Flow & Expense Trends</h3>
                    <p class="text-xs text-slate-400">Monthly breakdown of income vs expenditure</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-slate-900 text-slate-300 text-xs font-medium border border-slate-800">2026 Analytics</span>
            </div>
            <div id="cashflowChart" class="w-full h-72"></div>
        </div>

        <!-- Category Donut Breakdown -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
            <div>
                <h3 class="text-base font-extrabold text-white">Spending by Category</h3>
                <p class="text-xs text-slate-400">Top allocation areas this month</p>
            </div>
            <div id="categoryChart" class="w-full h-64"></div>
        </div>
    </div>

    <!-- Recent Transactions Modern Table -->
    <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-white">Recent Transactions</h3>
                <p class="text-xs text-slate-400">Latest activity across bank accounts and payment apps</p>
            </div>
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white transition">View All Activity</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Transaction / Merchant</th>
                        <th class="py-3 px-4">Category</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4">Payment App</th>
                        <th class="py-3 px-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentTransactions as $txn)
                        <tr class="hover:bg-slate-900/40 transition">
                            <td class="py-3.5 px-4 font-semibold text-white flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-sm text-indigo-400">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-200">{{ $txn->merchant?->name ?? $txn->notes ?? 'Transaction' }}</p>
                                    <p class="text-[10px] text-slate-500 font-mono">{{ $txn->reference_number ?? 'TXN-' . substr($txn->id, 0, 8) }}</p>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] font-medium text-slate-300">
                                    {{ $txn->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-400">
                                {{ $txn->transaction_date->format('M d, Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-300 font-mono">
                                <span class="px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-indigo-400 font-bold text-[11px]">
                                    {{ $txn->payment_method ?? 'Payment App' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-sm {{ $txn->type === 'income' ? 'text-emerald-400' : 'text-slate-100' }}">
                                {{ $txn->type === 'income' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-slate-500">No transactions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ApexCharts Script Initialization -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Cash Flow Area Chart
            var cashflowOptions = {
                chart: { type: 'area', height: 280, toolbar: { show: false }, background: 'transparent' },
                theme: { mode: 'dark' },
                stroke: { curve: 'smooth', width: 3 },
                colors: ['#10b981', '#ef4444'],
                series: [
                    { name: 'Income', data: [4200, 5100, 6800, 7500] },
                    { name: 'Expenses', data: [2800, 3200, 3100, 3410] }
                ],
                xaxis: { categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'] },
                grid: { borderColor: '#1e293b' },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } }
            };
            new ApexCharts(document.querySelector("#cashflowChart"), cashflowOptions).render();

            // Donut Category Chart
            var categoryOptions = {
                chart: { type: 'donut', height: 260, background: 'transparent' },
                theme: { mode: 'dark' },
                colors: ['#ef4444', '#ec4899', '#3b82f6', '#f59e0b', '#8b5cf6'],
                series: [420, 310, 240, 180, 150],
                labels: ['Food & Dining', 'Shopping', 'Travel', 'Utilities', 'Others'],
                legend: { position: 'bottom' }
            };
            new ApexCharts(document.querySelector("#categoryChart"), categoryOptions).render();
        });
    </script>
</x-app-layout>
