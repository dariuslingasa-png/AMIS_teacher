<x-admin-layout title="Student SOA">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-slate-950 via-amber-800 to-orange-700 p-6 text-white shadow-xl shadow-amber-900/10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">SOA</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">{{ $account->student?->applicant?->full_name ?: 'Student' }}</h1>
                    <p class="mt-2 text-sm font-semibold text-amber-50/90">{{ $account->student?->student_number ?? 'No student number' }} · {{ $account->grade_level ?? $account->student?->grade_level ?? '-' }} · {{ $account->school_year ?? '-' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.soa.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Back to SOA
                    </a>
                    <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                        <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                        Finance Dashboard
                    </a>
                </div>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Total Balance</span>
                <p class="mt-2 text-2xl font-black text-slate-950">PHP {{ number_format((float) $account->total_balance, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Paid</span>
                <p class="mt-2 text-2xl font-black text-emerald-700">PHP {{ number_format((float) $account->amount_paid, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Remaining</span>
                <p class="mt-2 text-2xl font-black text-amber-700">PHP {{ number_format((float) $account->remaining_balance, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Status</span>
                <p class="mt-2 text-2xl font-black uppercase text-slate-950">{{ $account->status ?? 'unpaid' }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1fr_380px]">
            <x-card title="Monthly Billing Schedule" subtitle="Payment allocation waterfall">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                                <th class="px-3 py-3">Month</th>
                                <th class="px-3 py-3">Description</th>
                                <th class="px-3 py-3">Due Date</th>
                                <th class="px-3 py-3">Amount</th>
                                <th class="px-3 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($account->monthlyBillings as $billing)
                                <tr>
                                    <td class="px-3 py-4 font-black text-slate-950">{{ $billing->month_name ?? 'Month '.$billing->month_number }}</td>
                                    <td class="px-3 py-4 font-semibold text-slate-600">{{ $billing->description ?? '-' }}</td>
                                    <td class="px-3 py-4 font-semibold text-slate-600">{{ optional($billing->due_date)->format('M d, Y') ?? '-' }}</td>
                                    <td class="px-3 py-4 font-black text-slate-950">PHP {{ number_format((float) $billing->amount_due, 2) }}</td>
                                    <td class="px-3 py-4"><x-badge color="{{ ($billing->status ?? '') === 'paid' ? 'green' : 'yellow' }}">{{ Str::upper($billing->status ?? 'unpaid') }}</x-badge></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-10 text-center text-sm font-bold text-slate-400">No monthly billings found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Record Payment" subtitle="Manual SOA payment entry">
                <form method="POST" action="{{ route('admin.soa.payments.add', $account) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Amount</label>
                        <input name="amount" type="number" min="1" max="{{ $account->remaining_balance }}" step="0.01" required class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Method</label>
                        <select name="method" required class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                            <option value="bdo">BDO</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Reference No.</label>
                        <input name="reference_no" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Checked By</label>
                        <input name="checked_by" value="Sir Cabel" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                    </div>
                    <button class="w-full rounded-2xl bg-amber-600 px-4 py-3 text-sm font-black uppercase tracking-wider text-white shadow-lg shadow-amber-700/20 transition hover:bg-amber-700">Record Payment</button>
                </form>
            </x-card>
        </div>

        <x-card title="SOA Payments" subtitle="Recorded tuition and billing payments">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-3 py-3">Method</th>
                            <th class="px-3 py-3">Reference</th>
                            <th class="px-3 py-3">Amount</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3">Checked By</th>
                            <th class="px-3 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($account->payments as $payment)
                            <tr>
                                <td class="px-3 py-4 font-black uppercase text-slate-950">{{ $payment->method ?? 'Payment' }}</td>
                                <td class="px-3 py-4 font-semibold text-slate-600">{{ $payment->reference_no ?? '-' }}</td>
                                <td class="px-3 py-4 font-black text-slate-950">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                                <td class="px-3 py-4"><x-badge color="{{ ($payment->status ?? '') === 'verified' ? 'green' : (($payment->status ?? '') === 'rejected' ? 'red' : 'yellow') }}">{{ Str::upper($payment->status ?? 'pending') }}</x-badge></td>
                                <td class="px-3 py-4 font-semibold text-slate-600">{{ $payment->checked_by ?? '-' }}</td>
                                <td class="px-3 py-4 text-right">
                                    @if (($payment->status ?? null) !== 'verified')
                                        <form method="POST" action="{{ route('admin.soa.payments.verify', $payment) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-emerald-700 hover:bg-emerald-100">Approve</button>
                                        </form>
                                    @else
                                        <span class="text-xs font-bold text-slate-400">Verified</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-10 text-center text-sm font-bold text-slate-400">No payments recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-admin-layout>
