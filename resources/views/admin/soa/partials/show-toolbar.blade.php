    <div class="space-y-6 print:space-y-0">
        <!-- Top Toolbar with Print & Record Commands -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm print:hidden">
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 uppercase tracking-wider">Official Template Preview</span>
                <h1 class="text-lg font-black text-slate-900">Official SOA Document View</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="openPaymentModal()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-emerald-700 transition shadow-lg shadow-emerald-700/10">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    Record Payment
                </button>
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-slate-800 transition">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    Print SOA (Ctrl+P)
                </button>
                <a href="{{ route('admin.finance.export-family', $account) }}" class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-black uppercase tracking-wider text-emerald-800 hover:bg-emerald-100 transition">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    Export CSV
                </a>
                <a href="{{ route('admin.soa.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-350 bg-slate-50 px-5 py-3 text-xs font-black uppercase tracking-wider text-slate-800 hover:bg-slate-150 transition">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Back to SOA List
                </a>
                <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-amber-700 transition">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    Finance Dashboard
                </a>
            </div>
        </div>
