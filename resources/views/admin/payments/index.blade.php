<x-admin-layout title="Enrollment Payment Review">
    <x-card title="Enrollment Payment Review" subtitle="Finance Management by Sir Cabel">
        <form method="GET" class="mb-4 flex gap-3">
            <select name="status" class="rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
                <option value="">All statuses</option>
                @foreach (['pending', 'verified', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-primary-700 px-4 py-2 text-sm font-medium text-white">Filter</button>
        </form>
        <table class="amis-table">
            <thead><tr><th>Family / Applicant</th><th>Children</th><th>Amount</th><th>Method</th><th>Status</th><th>Updated</th><th></th></tr></thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $children = $familyChildrenByPayment[$payment->id] ?? collect([$payment->applicant])->filter();
                        $applicant = $payment->applicant;
                        $familyNo = $applicant?->family_application_id ?: $applicant?->id;
                        $familyLabel = $familyLabelsByPayment[$payment->id] ?? 'FAMILY';
                    @endphp
                    <tr>
                        <td>
                            <div class="font-black text-slate-950">{{ $familyLabel }}</div>
                            <div class="mt-1 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                FAMILY #{{ str_pad((string) $familyNo, 4, '0', STR_PAD_LEFT) }}
                                @if ($children->count() > 1)
                                    &middot; {{ $children->count() }} CHILDREN
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="flex max-w-sm flex-wrap gap-1.5">
                                @forelse ($children as $child)
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-slate-600">
                                        {{ $child->full_name ?: 'Applicant' }}
                                    </span>
                                @empty
                                    <span class="text-xs font-semibold text-slate-400">No child record</span>
                                @endforelse
                            </div>
                        </td>
                        <td>{{ isset($payment->amount) ? 'PHP '.number_format((float) $payment->amount, 2) : '-' }}</td>
                        <td>{{ Str::upper($payment->method ?? '-') }}</td>
                        <td><x-badge color="{{ ($payment->status ?? '') === 'verified' ? 'green' : (($payment->status ?? '') === 'rejected' ? 'red' : 'yellow') }}">{{ Str::upper($payment->status ?? 'pending') }}</x-badge></td>
                        <td>{{ optional($payment->updated_at)->format('M d, Y') }}</td>
                        <td>
                            <div class="flex flex-wrap items-center gap-2">
                                @if ($payment->applicant)
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-slate-700 hover:bg-slate-200">Review</a>
                                @endif
                                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-emerald-700 hover:bg-emerald-100">APPROVE</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payments.reject', $payment) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="remarks" value="Payment proof rejected by Sir Cabel.">
                                    <button class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-rose-700 hover:bg-rose-100">REJECT</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-gray-500">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $payments->links() }}</div>
    </x-card>
</x-admin-layout>
