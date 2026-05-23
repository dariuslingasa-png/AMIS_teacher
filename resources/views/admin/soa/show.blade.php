<x-admin-layout title="Student SOA">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Statement of Account">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase text-gray-500">Student</dt><dd>{{ trim(($account->student->applicant->first_name ?? '').' '.($account->student->applicant->last_name ?? '')) ?: '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-gray-500">Balance</dt><dd>{{ isset($account->remaining_balance) ? 'PHP '.number_format((float) $account->remaining_balance, 2) : '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-gray-500">Status</dt><dd>{{ $account->status ?? '-' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-gray-500">School Year</dt><dd>{{ $account->school_year ?? '-' }}</dd></div>
            </dl>
        </x-card>
        <x-card title="Payments">
            <div class="space-y-2 text-sm">
                @forelse ($account->payments ?? [] as $payment)
                    <div class="flex justify-between rounded-lg bg-gray-50 p-3">
                        <span>{{ $payment->method ?? 'Payment' }}</span>
                        <span class="font-medium">{{ isset($payment->amount) ? 'PHP '.number_format((float) $payment->amount, 2) : '-' }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">No payments recorded.</p>
                @endforelse
            </div>
        </x-card>
    </div>
</x-admin-layout>
