<x-guest-layout>
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl space-y-6">
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 mb-2">
                    <i class="fa-solid fa-mobile-screen-button text-xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-white">Sign In to ExpenseAI</h2>
                <p class="text-xs text-slate-400">Login with your Mobile Phone Number or Email</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mobile Phone Number or Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-indigo-400">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </div>
                        <input type="text" name="login" value="{{ old('login', '+18005550122') }}" placeholder="e.g. +18005550122 or alex@expenseai.test" required autofocus class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                    </div>
                    @error('login')
                        <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-300">Password</label>
                        <a href="#" class="text-[11px] text-indigo-400 hover:underline">Forgot password?</a>
                    </div>
                    <input type="password" name="password" value="password" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember" class="rounded bg-slate-900 border-slate-800 text-indigo-600 focus:ring-0">
                    <label for="remember" class="text-xs text-slate-400">Remember this mobile device</label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-600 font-bold text-sm text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 transition">
                    Access Mobile Vault <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </form>

            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs space-y-1">
                <p class="font-bold text-slate-300 text-[11px] uppercase">Demo Phone Accounts (Password: password)</p>
                <div class="flex flex-wrap gap-1.5 font-mono text-[10px] text-indigo-400 pt-1">
                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-800">+18005550122 (Alex)</span>
                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-800">+19876543210 (Sarah)</span>
                    <span class="px-2 py-0.5 rounded bg-slate-950 border border-slate-800">+919876543210 (Rahul)</span>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 pt-1">
                Don't have an account? <a href="{{ route('register') }}" class="text-indigo-400 font-semibold hover:underline">Register Phone Number</a>
            </p>
        </div>
    </div>
</x-guest-layout>
