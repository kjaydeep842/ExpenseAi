<x-guest-layout>
    <div class="min-h-[85vh] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md glass-card rounded-3xl p-8 border border-slate-800 shadow-2xl space-y-6">
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 mb-2">
                    <i class="fa-solid fa-mobile-screen-button text-xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-white">Create Mobile Account</h2>
                <p class="text-xs text-slate-400">Track GPay, PhonePe, Paytm & Bank expenses by Mobile Number</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition" placeholder="e.g. Alex Morgan">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Mobile Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white font-mono text-sm focus:border-indigo-500 focus:outline-none transition" placeholder="e.g. +18005550199 or +919876543210">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition" placeholder="alex@company.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Select Primary Mobile Bank / App</label>
                    <select name="primary_bank" class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white font-medium text-sm focus:border-indigo-500 focus:outline-none transition">
                        <option value="Kotak Mahindra Bank">🏦 Kotak Mahindra Bank</option>
                        <option value="HDFC Bank">🏦 HDFC Bank</option>
                        <option value="ICICI Bank">🏦 ICICI Bank</option>
                        <option value="State Bank of India (SBI)">🏦 State Bank of India (SBI)</option>
                        <option value="Axis Bank">🏦 Axis Bank</option>
                        <option value="Google Pay / UPI Universal">💳 Google Pay / UPI Universal</option>
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Mobile number will be verified & connected with selected Bank API instantly.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border border-slate-800 text-white text-sm focus:border-indigo-500 focus:outline-none transition">
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 via-teal-600 to-indigo-600 font-bold text-sm text-white shadow-lg shadow-emerald-500/25 hover:brightness-110 transition">
                    Register Mobile Account
                </button>
            </form>

            <p class="text-center text-xs text-slate-400 pt-2">
                Already have an account? <a href="{{ route('login') }}" class="text-indigo-400 font-semibold hover:underline">Sign In</a>
            </p>
        </div>
    </div>
</x-guest-layout>
