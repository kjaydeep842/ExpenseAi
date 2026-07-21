<x-app-layout>
    <!-- Welcome Header & Quick Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">
                Good day, <span class="text-gradient">{{ auth()->user()->name }}</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Here is your real-time financial intelligence overview for {{ date('F Y') }}.</p>
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

    <!-- Active Budgets & Goals Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Budgets -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-extrabold text-white">Active Budget Limits</h3>
                <a href="{{ route('budgets.index') }}" class="text-xs text-indigo-400 hover:underline">Manage Budgets →</a>
            </div>

            <div class="space-y-4">
                @forelse($budgets as $budget)
                    @php
                        $percent = ($budget->amount > 0) ? min(100, round(($budget->spent / $budget->amount) * 100)) : 0;
                        $colorClass = $percent >= 90 ? 'bg-red-500' : ($percent >= 75 ? 'bg-amber-500' : 'bg-indigo-500');
                    @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-200">{{ $budget->category?->name ?? 'General Budget' }}</span>
                            <span class="text-slate-400">${{ number_format($budget->spent, 2) }} / <span class="font-semibold text-slate-200">${{ number_format($budget->amount, 2) }}</span> ({{ $percent }}%)</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-900 overflow-hidden border border-slate-800">
                            <div class="h-full {{ $colorClass }} rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">No active budgets configured yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Savings Goals -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-extrabold text-white">Savings & Investment Goals</h3>
                <a href="{{ route('goals.index') }}" class="text-xs text-indigo-400 hover:underline">View All Goals →</a>
            </div>

            <div class="space-y-3">
                @forelse($goals as $goal)
                    @php
                        $goalPercent = ($goal->target_amount > 0) ? min(100, round(($goal->current_amount / $goal->target_amount) * 100)) : 0;
                    @endphp
                    <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-{{ $goal->icon ?? 'bullseye' }}"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">{{ $goal->title }}</h4>
                                <p class="text-[10px] text-slate-500">${{ number_format($goal->current_amount, 2) }} of ${{ number_format($goal->target_amount, 2) }}</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-extrabold">{{ $goalPercent }}%</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">No active goals yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Transactions Modern Table -->
    <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-white">Recent Transactions</h3>
                <p class="text-xs text-slate-400">Latest activity across bank accounts and wallets</p>
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
                        <th class="py-3 px-4">Payment Method</th>
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
                                {{ $txn->transaction_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400">
                                {{ $txn->payment_method ?? 'Direct Bank' }}
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
