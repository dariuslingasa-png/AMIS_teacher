<x-admin-layout title="Payments">
    <x-card title="Payments" subtitle="Enrollment payment review">
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
            <thead><tr><th>Applicant</th><th>Amount</th><th>Method</th><th>Status</th><th>Updated</th><th></th></tr></thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ trim(($payment->applicant->first_name ?? '').' '.($payment->applicant->last_name ?? '')) ?: 'Applicant' }}</td>
                        <td>{{ isset($payment->amount) ? 'PHP '.number_format((float) $payment->amount, 2) : '-' }}</td>
                        <td>{{ $payment->method ?? '-' }}</td>
                        <td><x-badge color="{{ ($payment->status ?? '') === 'verified' ? 'green' : (($payment->status ?? '') === 'rejected' ? 'red' : 'yellow') }}">{{ $payment->status ?? 'pending' }}</x-badge></td>
                        <td>{{ optional($payment->updated_at)->format('M d, Y') }}</td>
                        <td class="space-x-2">
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="inline">@csrf @method('PATCH')<button class="text-sm font-medium text-green-700">Verify</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-gray-500">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $payments->links() }}</div>
    </x-card>
</x-admin-layout>
