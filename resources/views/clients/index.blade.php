<x-app-layout>
    <div x-data="{ newClientModal: false, addExpenseModal: false, addBudgetModal: false }" class="space-y-8">
        
        <!-- Header Banner & Title -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-lg text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </span>
                    Client Financial Management Engine
                </h1>
                <p class="text-xs text-slate-400 mt-1">Lookup client records by Mobile Phone Number to manage daily spending, balances & budget allocations.</p>
            </div>

            <button @click="newClientModal = true" class="px-5 py-3 rounded-2xl bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500 font-bold text-xs text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus"></i> Register New Client
            </button>
        </div>

        <!-- Phone Number Search Bar -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800/80 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <form method="GET" action="{{ route('clients.index') }}" class="space-y-4">
                <label class="block text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                    Enter Client Mobile Number / Phone
                </label>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-400">
                            <i class="fa-solid fa-phone text-sm"></i>
                        </div>
                        <input type="text" name="phone" value="{{ $searchPhone }}" placeholder="e.g. +18005550122 or +19876543210" required class="w-full pl-11 pr-4 py-3.5 rounded-2xl bg-slate-900/90 border border-slate-700/80 text-white font-mono text-sm placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition">
                    </div>

                    <button type="submit" class="px-8 py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 font-extrabold text-xs text-white shadow-lg shadow-indigo-600/30 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i> Lookup Financial Record
                    </button>
                </div>
            </form>

            <!-- Quick Client Chips -->
            <div class="mt-5 pt-4 border-t border-slate-800/80 flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase mr-2">Sample Client Phone Numbers:</span>
                @foreach($clientsList as $c)
                    <a href="{{ route('clients.index', ['phone' => $c->phone]) }}" class="px-3 py-1.5 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-indigo-500/60 text-xs text-slate-300 hover:text-white transition flex items-center gap-2 font-mono">
                        <i class="fa-solid fa-user text-[10px] text-indigo-400"></i>
                        <span>{{ $c->name }}</span>
                        <span class="text-indigo-400">({{ $c->phone }})</span>
                    </a>
                @endforeach
            </div>
        </div>

        @if($selectedClient && $clientData)
            <!-- Client Financial Header & Profile Card -->
            <div class="glass-card rounded-3xl p-6 border border-indigo-500/30 bg-gradient-to-r from-slate-900/90 via-indigo-950/20 to-slate-900/90 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($selectedClient->name) }}&background=6366f1&color=fff&size=128" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-indigo-500/30 shadow-xl" alt="Client Avatar">
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 ring-2 ring-slate-950"></span>
                    </div>

                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-xl font-extrabold text-white">{{ $selectedClient->name }}</h2>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active Client</span>
                        </div>
                        <p class="text-xs text-slate-400 font-mono mt-1">
                            <i class="fa-solid fa-phone text-indigo-400 mr-1"></i> {{ $selectedClient->phone }} | 
                            <i class="fa-solid fa-envelope text-slate-400 ml-2 mr-1"></i> {{ $selectedClient->email }}
                        </p>
                    </div>
                </div>

                <!-- Client Quick Action Buttons -->
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button @click="addExpenseModal = true" class="flex-1 md:flex-none px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 font-bold text-xs text-white shadow-lg shadow-emerald-500/20 hover:brightness-110 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus-circle"></i> + Add Client Expense
                    </button>
                    <button @click="addBudgetModal = true" class="flex-1 md:flex-none px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 font-bold text-xs text-slate-200 hover:text-white hover:border-slate-600 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-sliders"></i> Set Budget Cap
                    </button>
                </div>
            </div>

            <!-- Client Financial Metrics (4 Stats) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Today's Daily Spend -->
                <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-3 relative overflow-hidden group hover:border-red-500/40 transition">
                    <div class="flex items-center justify-between text-xs font-extrabold text-slate-400 uppercase">
                        <span>Today's Daily Spend</span>
                        <div class="w-8 h-8 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-white">${{ number_format($clientData['today_expenses'], 2) }}</h2>
                    <p class="text-[11px] text-slate-400">Daily expenses recorded for today</p>
                </div>

                <!-- Total Liquid Balance -->
                <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-3 relative overflow-hidden group hover:border-emerald-500/40 transition">
                    <div class="flex items-center justify-between text-xs font-extrabold text-slate-400 uppercase">
                        <span>Total Bank Balance</span>
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-vault"></i>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-emerald-400">${{ number_format($clientData['total_balance'], 2) }}</h2>
                    <p class="text-[11px] text-slate-400">Total available funds across accounts</p>
                </div>

                <!-- Monthly Expense Outflow -->
                <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-3 relative overflow-hidden group hover:border-purple-500/40 transition">
                    <div class="flex items-center justify-between text-xs font-extrabold text-slate-400 uppercase">
                        <span>Monthly Expenses</span>
                        <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-arrow-trend-down"></i>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-white">${{ number_format($clientData['monthly_expenses'], 2) }}</h2>
                    <p class="text-[11px] text-slate-400">Total outflow in current cycle</p>
                </div>

                <!-- Monthly Income Inflow -->
                <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-3 relative overflow-hidden group hover:border-indigo-500/40 transition">
                    <div class="flex items-center justify-between text-xs font-extrabold text-slate-400 uppercase">
                        <span>Monthly Earnings</span>
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-arrow-trend-up"></i>
                        </div>
                    </div>
                    <h2 class="text-3xl font-extrabold text-indigo-400">${{ number_format($clientData['monthly_income'], 2) }}</h2>
                    <p class="text-[11px] text-slate-400">Income & salary credits</p>
                </div>
            </div>

            <!-- Daily Expense Chart & Gemini AI Advice -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- ApexChart Daily Spending Trend -->
                <div class="lg:col-span-2 glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-white text-base">7-Day Daily Expense Trend</h3>
                            <p class="text-xs text-slate-400">Daily spending breakdown for {{ $selectedClient->name }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 text-[11px] font-semibold text-slate-300">Daily History</span>
                    </div>

                    <div id="clientDailyChart" class="h-64"></div>
                </div>

                <!-- AI Financial Advisor Card for Client -->
                <div class="glass-card rounded-3xl p-6 border border-amber-500/30 bg-gradient-to-b from-amber-950/20 to-slate-950 space-y-4">
                    <div class="flex items-center gap-2 text-amber-400 font-extrabold text-sm uppercase">
                        <i class="fa-solid fa-brain"></i> Gemini AI Client Assessment
                    </div>

                    <div class="text-xs text-slate-300 leading-relaxed font-medium">
                        {!! Str::markdown($clientData['ai_advice']) !!}
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-400 space-y-1">
                        <p class="font-bold text-slate-200">Client Account Summary:</p>
                        <p>Linked Accounts: <strong class="text-white">{{ count($clientData['accounts']) }}</strong></p>
                        <p>Active Budgets: <strong class="text-white">{{ count($clientData['budgets']) }}</strong></p>
                    </div>
                </div>
            </div>

            <!-- Client Daily Transaction History Table -->
            <div class="glass-card rounded-3xl p-6 border border-slate-800/80 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-white text-base">Client Transaction Ledger</h3>
                        <p class="text-xs text-slate-400">Financial records associated with mobile number {{ $selectedClient->phone }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                            <tr>
                                <th class="py-3 px-4">Date</th>
                                <th class="py-3 px-4">Merchant / Note</th>
                                <th class="py-3 px-4">Category</th>
                                <th class="py-3 px-4">Type</th>
                                <th class="py-3 px-4 text-right">Amount ($)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @forelse($clientData['recent_transactions'] as $tx)
                                <tr class="hover:bg-slate-900/40 transition">
                                    <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $tx->transaction_date->format('M d, Y H:i') }}</td>
                                    <td class="py-3.5 px-4 font-bold text-white">
                                        {{ $tx->merchant?->name ?? $tx->notes }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 font-semibold">
                                            {{ $tx->category?->name ?? 'General' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 uppercase font-bold text-[10px]">
                                        <span class="px-2 py-0.5 rounded {{ $tx->type === 'expense' ? 'bg-red-500/20 text-red-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right font-extrabold text-sm {{ $tx->type === 'expense' ? 'text-red-400' : 'text-emerald-400' }}">
                                        {{ $tx->type === 'expense' ? '-' : '+' }}${{ number_format($tx->amount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-slate-500">No transactions recorded for this client yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ApexCharts JS Rendering script -->
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var options = {
                        chart: {
                            type: 'bar',
                            height: 250,
                            toolbar: { show: false },
                            background: 'transparent'
                        },
                        theme: { mode: 'dark' },
                        colors: ['#6366f1'],
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                columnWidth: '40%',
                            }
                        },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        series: [{
                            name: "Daily Expense ($)",
                            data: @json($clientData['daily_chart_data'])
                        }],
                        xaxis: {
                            categories: @json($clientData['daily_chart_categories']),
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                        },
                        yaxis: {
                            labels: { style: { colors: '#94a3b8', fontSize: '11px' } }
                        },
                        grid: { borderColor: '#1e293b', strokeDashArray: 4 }
                    };

                    var chart = new ApexCharts(document.querySelector("#clientDailyChart"), options);
                    chart.render();
                });
            </script>

        @elseif($searchPhone)
            <div class="glass-card rounded-3xl p-12 text-center space-y-4 border border-slate-800">
                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-3xl mx-auto">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <h3 class="text-lg font-extrabold text-white">No Client Record Found</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">No client user exists with mobile phone number "<strong class="text-white">{{ $searchPhone }}</strong>".</p>
                <button @click="newClientModal = true" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-xs text-white shadow-lg">
                    Register Client with Mobile # {{ $searchPhone }}
                </button>
            </div>
        @else
            <div class="glass-card rounded-3xl p-12 text-center space-y-4 border border-slate-800/80">
                <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-3xl mx-auto">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <h3 class="text-xl font-extrabold text-white">Enter a Client Mobile Number Above</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">Search any client by mobile phone number to view their daily expense log, total net worth, and set daily spending budgets.</p>
            </div>
        @endif

        <!-- 1. Register New Client Modal -->
        <div x-show="newClientModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="newClientModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Register Client Mobile Account</h3>
                    <button @click="newClientModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('clients.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Client Full Name</label>
                        <input type="text" name="name" required placeholder="e.g. David Miller" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Mobile Phone Number</label>
                        <input type="text" name="phone" value="{{ $searchPhone }}" required placeholder="e.g. +18005550199" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address (Optional)</label>
                        <input type="email" name="email" placeholder="client@example.com" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Opening Account Balance ($)</label>
                        <input type="number" step="0.01" name="initial_balance" placeholder="1000.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white shadow-lg">Create Client Profile</button>
                </form>
            </div>
        </div>

        @if($selectedClient)
            <!-- 2. Add Client Expense Modal -->
            <div x-show="addExpenseModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
                <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="addExpenseModal = false">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="font-bold text-white text-base">Log Expense for {{ $selectedClient->name }}</h3>
                        <button @click="addExpenseModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form method="POST" action="{{ route('clients.expense', ['clientId' => $selectedClient->id]) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Transaction Type</label>
                            <select name="type" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                                <option value="expense">Expense (Outflow)</option>
                                <option value="income">Income (Inflow)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Amount ($)</label>
                            <input type="number" step="0.01" name="amount" required placeholder="45.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Category</label>
                            <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Merchant / Notes</label>
                            <input type="text" name="notes" placeholder="e.g. Daily Groceries / Fuel" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                        </div>
                        <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 font-bold text-xs text-white shadow-lg">Save Client Entry</button>
                    </form>
                </div>
            </div>

            <!-- 3. Add Client Budget Modal -->
            <div x-show="addBudgetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
                <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="addBudgetModal = false">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <h3 class="font-bold text-white text-base">Set Daily / Monthly Budget Cap</h3>
                        <button @click="addBudgetModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form method="POST" action="{{ route('clients.budget', ['clientId' => $selectedClient->id]) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Category Cap</label>
                            <select name="category_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Budget Limit ($)</label>
                            <input type="number" step="1" name="amount" required placeholder="500" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Cycle Period</label>
                            <select name="period" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                                <option value="monthly">Monthly Cap</option>
                                <option value="daily">Daily Cap</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white shadow-lg">Save Client Budget Rule</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
