<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-extrabold text-white">Receipt & Invoice OCR Engine</h1>
            <p class="text-xs text-slate-400">Scan physical bills, restaurant checks, or store receipts to auto-extract line items & taxes.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Upload Box -->
            <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
                <h3 class="font-extrabold text-white text-base">Scan New Receipt</h3>

                <form method="POST" action="{{ route('receipts.scan') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="border-2 border-dashed border-slate-800 rounded-2xl p-6 text-center space-y-3 hover:border-indigo-500 transition">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-indigo-400"></i>
                        <div class="text-xs text-slate-400">
                            <span class="font-bold text-slate-200">Click to upload</span> or drag and drop image
                        </div>
                        <input type="file" name="receipt" accept="image/*" required class="w-full text-xs text-slate-400 border border-slate-800 rounded-xl p-2 bg-slate-900">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 font-bold text-xs text-white shadow-lg shadow-purple-500/25">
                        Run OCR Extraction
                    </button>
                </form>
            </div>

            <!-- Scanned Receipts History -->
            <div class="md:col-span-2 glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
                <h3 class="font-extrabold text-white text-base">OCR History & Extracted Line Items</h3>

                <div class="space-y-3">
                    @forelse($scans as $scan)
                        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-12 h-12 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center text-purple-400 font-bold shrink-0">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-200 text-sm">{{ $scan->merchant }}</h4>
                                    <p class="text-[11px] text-slate-400">Date: {{ $scan->date?->format('M d, Y') ?? 'Today' }} | GST/Tax: ${{ number_format($scan->gst, 2) }}</p>
                                    <div class="text-[10px] font-mono text-slate-500 bg-slate-950 p-2 rounded-lg mt-2">
                                        {{ $scan->extracted_text }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-base font-extrabold text-emerald-400">${{ number_format($scan->amount, 2) }}</span>
                                <span class="block px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-emerald-500/20 text-emerald-400 mt-1">Confirmed</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 py-6 text-center">No receipts scanned yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
