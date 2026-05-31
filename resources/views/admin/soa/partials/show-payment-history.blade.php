        <div class="print:hidden max-w-[900px] mx-auto bg-white p-8 border border-slate-200 rounded-3xl shadow-sm space-y-6">
            
            <!-- Ledger Title -->
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-black uppercase tracking-wider text-slate-800 flex items-center gap-2" style="font-size: 19.5px !important;">
                    <i data-lucide="history" class="h-5.5 w-5.5 text-slate-500"></i>
                    Payment Transaction
                </h3>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-[13.5px] font-bold text-slate-600">
                    {{ $account->payments->count() }} {{ Str::plural('record', $account->payments->count()) }}
                </span>
            </div>

            <!-- Ledger Table Container (No outer box borders!) -->
            <div class="overflow-x-auto">
                <table class="admin-ledger-table w-full text-left">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">OR Number</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3">Method & Ref</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Proof</th>
                        </tr>
                    </thead>
                    <tbody class="font-semibold text-black">
                        @forelse ($account->payments as $payment)
                            @php
                                $statusColor = match(strtolower($payment->status)) {
                                    'verified', 'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                                };
                            @endphp
                            <tr class="hover-row transition-colors">
                                <td class="px-4 py-4">
                                    @if (strtolower($payment->status) === 'verified')
                                        <div class="font-bold text-black" style="font-size: 19.5px !important;">{{ $payment->or_number }}</div>
                                        <div class="text-[13.5px] text-emerald-600 font-bold mt-1">Approved</div>
                                    @elseif (strtolower($payment->status) === 'rejected')
                                        <div class="font-bold text-slate-700" style="font-size: 19.5px !important;">Rejected</div>
                                        <div class="text-[13.5px] text-rose-600 font-bold mt-1">Rejected</div>
                                    @else
                                        <div class="font-bold text-slate-800" style="font-size: 19.5px !important;">Pending</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-black font-semibold" style="font-size: 19.5px !important;">{{ $payment->created_at?->format('M d, Y') ?? $payment->verified_at?->format('M d, Y') }}</div>
                                    <div class="text-[13.5px] text-slate-400 font-semibold mt-1">{{ $payment->created_at?->format('h:i A') }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-700 uppercase" style="font-size: 19.5px !important;">{{ $payment->method }}</div>
                                    <div class="text-[13.5px] text-slate-500 font-semibold mt-1">Ref: {{ $payment->reference_no ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-right font-black text-black" style="font-size: 19.5px !important;">
                                    PHP {{ number_format($payment->amount, 2) }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-[13.5px] font-bold uppercase tracking-wider {{ $statusColor }}">
                                        {{ $payment->status ?: 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    @if ($payment->receipt_url)
                                        <a href="{{ asset('storage/' . $payment->receipt_url) }}" target="_blank" class="btn-premium btn-view inline-flex items-center gap-2">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                            View
                                        </a>
                                    @else
                                        <span class="text-[13.5px] text-slate-400 font-semibold">No Proof</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-bold" style="font-size: 19.5px !important;">
                                    No payment logs recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
