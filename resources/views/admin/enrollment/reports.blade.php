<x-admin-layout title="Enrollment Reports">
    <x-card title="Enrollment Reports" subtitle="Filtered enrollment export">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.enrollment.reports.export', request()->query()) }}" class="rounded-lg bg-primary-700 px-4 py-2 text-sm font-medium text-white">Export CSV</a>
        </div>
        <table class="amis-table">
            <thead><tr><th>Applicant</th><th>Email</th><th>Grade</th><th>Status</th><th>Submitted</th></tr></thead>
            <tbody>
                @forelse ($reports as $applicant)
                    <tr>
                        <td>{{ trim(($applicant->first_name ?? '').' '.($applicant->last_name ?? '')) ?: 'Applicant' }}</td>
                        <td>{{ $applicant->user->email ?? $applicant->email ?? '-' }}</td>
                        <td>{{ $applicant->grade_level ?? '-' }}</td>
                        <td>{{ $statusLabels[$applicant->status] ?? $applicant->status ?? '-' }}</td>
                        <td>{{ optional($applicant->created_at)->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-gray-500">No report rows found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $reports->links() }}</div>
    </x-card>
</x-admin-layout>
