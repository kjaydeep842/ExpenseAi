<x-app-layout>
    <div x-data="{ addModal: false }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Transaction Management</h1>
                <p class="text-xs text-slate-400">View, search, filter, and add transactions with auto AI categorization.</p>
            </div>

            <button @click="addModal = true" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 font-bold text-xs text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Entry
            </button>
        </div>

        <!-- Filters & Search Bar -->
        <div class="glass-card rounded-2xl p-4 border border-slate-800 my-6">
            <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search notes or merchant..." class="px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none">

                <select name="type" class="px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 focus:outline-none">
                    <option value="">All Types</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                    <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="salary" {{ request('type') === 'salary' ? 'selected' : '' }}>Salary</option>
                </select>

                <select name="category_id" class="px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 focus:outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="py-2.5 px-4 rounded-xl bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-500 transition">
                    Filter Results
                </button>
            </form>
        </div>

        <!-- Table View -->
        <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-slate-400 uppercase tracking-wider text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="py-3 px-4">Merchant / Notes</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4 text-right">Amount</th>
                            <th class="py-3 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($transactions as $txn)
                            <tr class="hover:bg-slate-900/40 transition">
                                <td class="py-3.5 px-4 font-semibold text-white flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center text-indigo-400">
                                        <i class="fa-solid fa-receipt"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-200">{{ $txn->merchant?->name ?? $txn->notes }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $txn->notes }}</p>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-[11px] text-slate-300">
                                        {{ $txn->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $txn->type === 'income' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                                        {{ $txn->type }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-400">
                                    {{ $txn->transaction_date->format('M d, Y H:i') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-extrabold text-sm {{ $txn->type === 'income' ? 'text-emerald-400' : 'text-slate-100' }}">
                                    {{ $txn->type === 'income' ? '+' : '-' }}${{ number_format($txn->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <form method="POST" action="{{ route('transactions.destroy', $txn->id) }}" onsubmit="return confirm('Delete transaction?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 p-1">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-6 text-slate-500">No transactions match your search criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-4">
                {{ $transactions->links() }}
            </div>
        </div>

        <!-- Add Transaction Modal -->
        <div x-show="addModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="addModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Record New Transaction</h3>
                    <button @click="addModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form method="POST" action="{{ route('transactions.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Amount ($)</label>
                        <input type="number" step="0.01" name="amount" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Type</label>
                        <select name="type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                            <option value="transfer">Transfer</option>
                            <option value="investment">Investment</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Bank Account</label>
                        <select name="bank_account_id" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                            <option value="">Default Account</option>
                            @foreach($bankAccounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->account_name }} (${{ number_format($acc->balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Date & Time</label>
                        <input type="datetime-local" name="transaction_date" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Notes / Merchant Name</label>
                        <input type="text" name="notes" placeholder="e.g. Swiggy Lunch / Starbucks" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm" placeholder="e.g. Starbucks Espresso">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 font-bold text-xs text-white shadow-lg shadow-indigo-500/25">
                        Log Transaction
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
