<x-admin-layout title="Tuition & Fees">
    <x-card title="Student Accounts" subtitle="Statement of account records">
        <table class="amis-table">
            <thead><tr><th>Student</th><th>Grade</th><th>Total</th><th>Paid</th><th>Balance</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr>
                        <td>{{ trim(($account->student->applicant->first_name ?? '').' '.($account->student->applicant->last_name ?? '')) ?: 'Student' }}</td>
                        <td>{{ $account->grade_level ?? $account->student->grade_level ?? '-' }}</td>
                        <td>{{ isset($account->total_amount) ? 'PHP '.number_format((float) $account->total_amount, 2) : '-' }}</td>
                        <td>{{ isset($account->paid_amount) ? 'PHP '.number_format((float) $account->paid_amount, 2) : '-' }}</td>
                        <td>{{ isset($account->remaining_balance) ? 'PHP '.number_format((float) $account->remaining_balance, 2) : '-' }}</td>
                        <td><x-badge color="blue">{{ $account->status ?? '-' }}</x-badge></td>
                        <td><a href="{{ route('admin.soa.show', $account) }}" class="font-medium text-primary-700 hover:underline">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-gray-500">No accounts found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $accounts->links() }}</div>
    </x-card>
</x-admin-layout>
