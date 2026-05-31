    <div id="paymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-300 print:hidden" style="backdrop-filter: blur(8px); background-color: rgba(15, 23, 42, 0.45);">
        
        <!-- Modal Card Container -->
        <div class="relative w-full max-w-lg transform scale-95 opacity-0 transition-all duration-300 bg-white rounded-3xl border border-slate-100 shadow-2xl shadow-slate-900/20 overflow-hidden" id="modalCard">
            
            <!-- Sleek Gradient Header Banner -->
            <div class="bg-gradient-to-br from-slate-950 via-emerald-900 to-teal-800 p-6 text-white relative">
                <!-- Close Button -->
                <button onclick="closePaymentModal()" class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 transition duration-150" style="position: absolute !important; top: 16px !important; right: 16px !important; z-index: 50 !important;">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
                
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-teal-100">Finance Workspace</span>
                <h3 class="mt-3 text-2xl font-black tracking-tight text-white flex items-center gap-2">
                    <i data-lucide="credit-card" class="h-6 w-6 text-teal-300"></i>
                    Record Payment Receipt
                </h3>
                <p class="mt-1 text-xs font-semibold text-teal-100/85">SOA ledger receipt allocation and instant OR verification.</p>
            </div>

            <!-- Form Body -->
            <form method="POST" action="{{ route('admin.soa.payments.add', $account) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                
                <!-- Allocation Purpose -->
                <div>
                    <label class="mb-1.5 block text-[11px] font-black text-slate-500 uppercase tracking-wider">Allocation Purpose</label>
                    <div class="relative">
                        <select name="purpose" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-bold text-slate-950 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100 appearance-none">
                            <option value="Tuition Fee">Tuition Fee</option>
                            <option value="Other Academic Fees">Other Academic Fees</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i data-lucide="chevron-down" class="h-4 w-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Two-Column Fields: Amount & Payment Method -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-[11px] font-black text-slate-500 uppercase tracking-wider">Amount (PHP)</label>
                        <input name="amount" type="number" min="0.01" max="{{ $account->remaining_balance }}" step="0.01" required placeholder="0.00" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-black text-slate-950 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[11px] font-black text-slate-500 uppercase tracking-wider">Payment Method</label>
                        <div class="relative">
                            <select name="method" required class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-bold text-slate-950 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100 appearance-none">
                                <option value="cash">Cash Payment</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                <option value="bdo">BDO Bank Transfer</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <i data-lucide="chevron-down" class="h-4 w-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Two-Column Fields: Transaction Reference & OR Number -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-[11px] font-black text-slate-500 uppercase tracking-wider">Transaction / Reference No.</label>
                        <input name="reference_no" placeholder="e.g. REF109402" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-bold text-slate-950 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[11px] font-black text-slate-500 uppercase tracking-wider">Official Receipt (OR) Number</label>
                        <input name="or_number" required placeholder="e.g. 70105712" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3 text-sm font-bold text-slate-950 placeholder-slate-400 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>
                </div>

                <!-- Checked and Verified By -->
                <div>
                    <label class="mb-1.5 block text-[11px] font-black text-slate-500 uppercase tracking-wider">Checked & Verified By</label>
                    <input name="checked_by" value="Sir Cabel" readonly class="w-full rounded-xl border border-slate-200 bg-slate-100 p-3 text-sm font-bold text-slate-600 cursor-not-allowed outline-none">
                </div>

                <!-- Action Buttons: Cancel and Submit -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closePaymentModal()" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-black uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition duration-150">
                        Cancel
                    </button>
                    <button type="submit" class="flex items-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-xs font-black uppercase tracking-wider text-white shadow-lg shadow-emerald-700/20 hover:bg-emerald-700 transition duration-150">
                        <i data-lucide="check" class="h-4 w-4"></i>
                        Record & Verify
                    </button>
                </div>
            </form>
        </div>
    </div>
