<x-guest-layout>
    <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl space-y-6">
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 mb-2">
                    <i class="fa-solid fa-lock text-xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-white">Welcome Back</h2>
                <p class="text-xs text-slate-400">Enter your credentials to access your financial intelligence vault</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', 'alex@expenseai.test') }}" required autofocus class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                    @error('email')
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
                    <label for="remember" class="text-xs text-slate-400">Remember this device</label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-600 font-bold text-sm text-white shadow-lg shadow-indigo-500/25 hover:brightness-110 transition">
                    Sign In to ExpenseAI
                </button>
            </form>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-4 text-[10px] text-slate-500 font-semibold uppercase">Or Continue With</span>
                <div class="flex-grow border-t border-slate-800"></div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" class="py-2.5 px-4 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white hover:border-slate-700 flex items-center justify-center gap-2 transition">
                    <i class="fa-brands fa-google text-red-400"></i> Google
                </button>
                <button type="button" class="py-2.5 px-4 rounded-xl bg-slate-900 border border-slate-800 text-xs font-semibold text-slate-300 hover:text-white hover:border-slate-700 flex items-center justify-center gap-2 transition">
                    <i class="fa-brands fa-apple text-slate-200"></i> Apple
                </button>
            </div>

            <p class="text-center text-xs text-slate-400 pt-2">
                Don't have an account? <a href="{{ route('register') }}" class="text-indigo-400 font-semibold hover:underline">Create One Free</a>
            </p>
        </div>
    </div>
</x-guest-layout>
