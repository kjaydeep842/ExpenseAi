<x-app-layout>
    <div x-data="{ bankModal: false, walletModal: false }" class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Bank Accounts, Cards & Wallets</h1>
                <p class="text-xs text-slate-400">Manage checking accounts, credit cards, savings vaults, and digital crypto wallets.</p>
            </div>

            <div class="flex items-center gap-3">
                <button @click="bankModal = true" class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-200 hover:border-slate-700">
                    <i class="fa-solid fa-building-columns text-indigo-400"></i> Add Bank
                </button>
                <button @click="walletModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 font-bold text-xs text-white hover:bg-indigo-500">
                    <i class="fa-solid fa-wallet"></i> Add Wallet
                </button>
            </div>
        </div>

        <!-- Bank Accounts Cards -->
        <div class="space-y-3">
            <h3 class="text-sm font-extrabold text-white uppercase tracking-wider text-[11px] text-slate-400">Connected Bank Accounts</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($bankAccounts as $acc)
                    <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 hover:border-indigo-500/40 transition relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-lg">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-white text-sm">{{ $acc->account_name }}</h4>
                                    <p class="text-[11px] text-slate-400 font-mono">{{ $acc->account_number }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-indigo-500/20 text-indigo-300">{{ $acc->account_type }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="text-xs text-slate-400">Current Balance</span>
                            <h2 class="text-3xl font-extrabold text-white">${{ number_format($acc->balance, 2) }}</h2>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 col-span-3">No bank accounts linked yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Digital Wallets Cards -->
        <div class="space-y-3 pt-4">
            <h3 class="text-sm font-extrabold text-white uppercase tracking-wider text-[11px] text-slate-400">Digital & Crypto Wallets</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($wallets as $wallet)
                    <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 hover:border-emerald-500/40 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold text-lg">
                                    <i class="fa-solid fa-wallet"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-white text-sm">{{ $wallet->name }}</h4>
                                    <p class="text-[11px] text-slate-400">{{ strtoupper($wallet->type) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="pt-2">
                            <span class="text-xs text-slate-400">Available Funds</span>
                            <h2 class="text-3xl font-extrabold text-emerald-400">${{ number_format($wallet->balance, 2) }}</h2>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 col-span-3">No wallets added yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Add Bank Modal -->
        <div x-show="bankModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="bankModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Connect Bank Account</h3>
                    <button @click="bankModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('accounts.bank.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Account Label</label>
                        <input type="text" name="account_name" required placeholder="e.g. Chase Premier Savings" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Account Type</label>
                        <select name="account_type" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                            <option value="savings">Savings</option>
                            <option value="checking">Checking</option>
                            <option value="credit">Credit Card</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Opening Balance ($)</label>
                        <input type="number" step="0.01" name="balance" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white">Save Bank Account</button>
                </form>
            </div>
        </div>

        <!-- Add Wallet Modal -->
        <div x-show="walletModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
            <div class="w-full max-w-md glass-card rounded-3xl p-6 border border-slate-700 shadow-2xl space-y-4" @click.away="walletModal = false">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="font-bold text-white text-base">Add Digital Wallet</h3>
                    <button @click="walletModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <form method="POST" action="{{ route('accounts.wallet.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Wallet Name</label>
                        <input type="text" name="name" required placeholder="e.g. Coinbase Vault" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Current Balance ($)</label>
                        <input type="number" step="0.01" name="balance" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-white text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 font-bold text-xs text-white">Save Wallet</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
