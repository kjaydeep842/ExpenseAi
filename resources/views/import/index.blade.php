<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Import Bank Statements & Android SMS XML</h1>
            <p class="text-xs text-slate-400">Automate transaction parsing from Android SMS Backup & Restore XML files or CSV bank statements.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Android SMS XML Backup Parser -->
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-message"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-white text-base">Android SMS Backup XML Import</h3>
                        <p class="text-[11px] text-slate-400">Parse debit, credit, UPI, and bank alerts</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('import.sms') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Upload .xml Backup File</label>
                        <input type="file" name="xml_file" accept=".xml" class="w-full text-xs text-slate-400 border border-slate-800 rounded-xl p-2.5 bg-slate-900 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Or Paste Raw XML Content</label>
                        <textarea name="raw_xml" rows="4" class="w-full p-3 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 font-mono focus:outline-none" placeholder='<sms address="+18005550199" body="Paid Rs.450 to Swiggy at 14:20 Ref: 849204" date="1721540000000" />'></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 font-bold text-xs text-white shadow-lg shadow-indigo-500/25 transition">
                        Parse & Create Transactions
                    </button>
                </form>
            </div>

            <!-- CSV / Excel Statement Import -->
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-file-csv"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-white text-base">CSV / Excel Bank Statement</h3>
                        <p class="text-[11px] text-slate-400">Auto column mapping & duplicate prevention</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('import.csv') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Upload Statement (.csv, .xlsx)</label>
                        <input type="file" name="statement_file" accept=".csv,.xlsx,.xls" required class="w-full text-xs text-slate-400 border border-slate-800 rounded-xl p-2.5 bg-slate-900 focus:outline-none">
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 space-y-1">
                        <p class="font-bold text-slate-300">Expected Columns:</p>
                        <p>Date, Description / Merchant, Amount, Type (Expense/Income)</p>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 font-bold text-xs text-white shadow-lg shadow-emerald-500/25 transition">
                        Import Statement Entries
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
