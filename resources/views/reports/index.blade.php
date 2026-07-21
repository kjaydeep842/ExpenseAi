<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Reports & Financial Analytics Export</h1>
            <p class="text-xs text-slate-400">Export audited transaction statements in PDF or CSV format for accounting and tax returns.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- PDF Statement -->
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 text-center">
                <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-3xl mx-auto">
                    <i class="fa-solid fa-file-pdf"></i>
                </div>
                <h3 class="font-extrabold text-white text-lg">Download PDF Tax Statement</h3>
                <p class="text-xs text-slate-400 max-w-xs mx-auto">Formatted formal PDF containing receipt summaries, merchant records, and tax breakdowns.</p>
                <a href="{{ route('reports.pdf') }}" class="inline-block px-6 py-3 rounded-xl bg-indigo-600 font-bold text-xs text-white shadow-lg shadow-indigo-500/25 hover:bg-indigo-500 transition">
                    Export PDF Document
                </a>
            </div>

            <!-- CSV Statement -->
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-3xl mx-auto">
                    <i class="fa-solid fa-file-excel"></i>
                </div>
                <h3 class="font-extrabold text-white text-lg">Export Raw CSV Data</h3>
                <p class="text-xs text-slate-400 max-w-xs mx-auto">Compatible with Microsoft Excel, Google Sheets, QuickBooks, and Xero accounting software.</p>
                <a href="{{ route('reports.csv') }}" class="inline-block px-6 py-3 rounded-xl bg-emerald-600 font-bold text-xs text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-500 transition">
                    Export CSV Spreadsheet
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
