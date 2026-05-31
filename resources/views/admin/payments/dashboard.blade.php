<x-admin-layout title="Finance Management">
    <script type="application/json" id="finance-dashboard-chart-data">
        @json($financeCharts ?? [])
    </script>

    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-amber-900/10" style="background: linear-gradient(135deg, #111827 0%, #92400e 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">Finance Management</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Sir Cabel Finance Workspace</h1>
                    <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-amber-50/90">
                        Review enrollment payment proofs, monitor missing receipts, and open SOA records from one finance dashboard.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                        <i data-lucide="credit-card" class="h-4 w-4"></i>
                        Enrollment Payment Approval
                    </a>
                    <a href="{{ route('admin.finance.fees') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        <i data-lucide="table" class="h-4 w-4"></i>
                        Schedule of Fees
                    </a>
                    <a href="{{ route('admin.soa.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        <i data-lucide="scroll-text" class="h-4 w-4"></i>
                        SOA
                    </a>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Pending Proofs</span>
                <p class="mt-2 text-3xl font-black text-amber-700">{{ $stats['pending'] }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Approved</span>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $stats['verified'] }}</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Rejected</span>
                <p class="mt-2 text-3xl font-black text-rose-700">{{ $stats['rejected'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Missing Proof</span>
                <p class="mt-2 text-3xl font-black text-slate-950">{{ $stats['missing'] }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">SOA Balance</span>
                <p class="mt-2 text-2xl font-black text-slate-950">{{ number_format((float) $stats['soa_balance'], 2) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">SOA Paid</span>
                <p class="mt-2 text-2xl font-black text-emerald-700">{{ number_format((float) $stats['soa_paid'], 2) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Partial Accounts</span>
                <p class="mt-2 text-3xl font-black text-amber-700">{{ $stats['soa_partial'] }}</p>
            </div>
            <div class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Unpaid Accounts</span>
                <p class="mt-2 text-3xl font-black text-rose-700">{{ $stats['soa_unpaid'] }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-12">
            <x-dashboard.chart-card class="xl:col-span-4" title="Payment Proof Status" subtitle="Pending, approved, rejected, and missing receipts" chart="financePaymentStatusChart" />
            <x-dashboard.chart-card class="xl:col-span-4" title="SOA Account Status" subtitle="Paid, partial, and unpaid accounts" chart="financeSoaStatusChart" />
            <x-dashboard.chart-card class="xl:col-span-4" title="SOA Money Overview" subtitle="Paid amount against remaining balance" chart="financeSoaMoneyChart" />
            <x-dashboard.chart-card class="xl:col-span-12" title="7-Day Collection Trend" subtitle="Enrollment proofs and SOA payments by upload/record date" chart="financeCollectionTrendChart" />
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <x-card title="Recent Enrollment Payments" subtitle="Latest payment proof submissions">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-3 py-3">Applicant</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Method</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentPayments as $payment)
                            @php
                                $children = $familyChildrenByPayment[$payment->id] ?? collect([$payment->applicant])->filter();
                                $applicant = $payment->applicant;
                                $familyNo = $applicant?->family_application_id ?: $applicant?->id;
                                $familyLabel = $familyLabelsByPayment[$payment->id] ?? 'FAMILY';
                            @endphp
                            <tr>
                                <td class="px-3 py-4">
                                    <div class="font-black text-slate-950">{{ $familyLabel }}</div>
                                    <div class="mt-1 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                        FAMILY #{{ str_pad((string) $familyNo, 4, '0', STR_PAD_LEFT) }}
                                        @if ($children->count() > 1)
                                            &middot; {{ $children->count() }} CHILDREN
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-4 font-semibold text-slate-700">{{ isset($payment->amount) ? number_format((float) $payment->amount, 2) : '-' }}</td>
                                <td class="px-3 py-4 font-semibold text-slate-700">{{ $payment->method_label ?? $payment->method ?? '-' }}</td>
                                <td class="px-3 py-4"><x-badge color="{{ ($payment->status ?? '') === 'verified' ? 'green' : (($payment->status ?? '') === 'rejected' ? 'red' : 'yellow') }}">{{ Str::upper($payment->status ?? 'pending') }}</x-badge></td>
                                <td class="px-3 py-4 text-right">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-amber-700 hover:bg-amber-100">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-10 text-center text-sm font-bold text-slate-400">No payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </x-card>

            <x-card title="Open SOA Balances" subtitle="Student accounts with remaining balances">
                <div class="space-y-3">
                    @forelse ($openAccounts as $account)
                        <a href="{{ route('admin.soa.show', $account) }}" class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-amber-200 hover:bg-amber-50/30">
                            <span>
                                <span class="block text-sm font-black text-slate-950">{{ $account->student?->applicant?->full_name ?: 'Student' }}</span>
                                <span class="mt-1 block text-xs font-semibold uppercase text-slate-500">{{ $account->grade_level ?? $account->student?->grade_level ?? '-' }} &middot; {{ $account->school_year ?? '-' }}</span>
                            </span>
                            <span class="text-right">
                                <span class="block text-sm font-black text-amber-700">{{ number_format((float) $account->remaining_balance, 2) }}</span>
                                <span class="mt-1 block text-[10px] font-black uppercase tracking-wider text-slate-400">{{ $account->status ?? 'unpaid' }}</span>
                            </span>
                        </a>
                    @empty
                        <p class="py-8 text-center text-sm font-bold text-slate-400">No open SOA balances.</p>
                    @endforelse
                </div>
            </x-card>
        </div>

        <x-card title="Recent SOA Payments" subtitle="Latest recorded tuition and billing payments">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-3 py-3">Student</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Method</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentSoaPayments as $payment)
                            <tr>
                                <td class="px-3 py-4 font-black text-slate-950">{{ $payment->student?->applicant?->full_name ?: 'Student' }}</td>
                                <td class="px-3 py-4 font-semibold text-slate-700">{{ number_format((float) $payment->amount, 2) }}</td>
                                <td class="px-3 py-4 font-semibold text-slate-700">{{ Str::upper($payment->method ?? '-') }}</td>
                                <td class="px-3 py-4"><x-badge color="{{ ($payment->status ?? '') === 'verified' ? 'green' : (($payment->status ?? '') === 'rejected' ? 'red' : 'yellow') }}">{{ Str::upper($payment->status ?? 'pending') }}</x-badge></td>
                                <td class="px-3 py-4 text-sm font-semibold text-slate-500">{{ optional($payment->paid_at ?? $payment->created_at)->format('M d, Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-10 text-center text-sm font-bold text-slate-400">No SOA payments recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-admin-layout>
