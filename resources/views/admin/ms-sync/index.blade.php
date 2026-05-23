<x-admin-layout title="Microsoft 365 Sync">
    @if ($azureError)
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ $azureError }}</div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach (($stats ?? []) as $label => $value)
            <div class="metric-card">
                <div class="text-2xl font-bold text-gray-900">{{ $value }}</div>
                <div class="text-sm text-gray-500">{{ str_replace('_', ' ', ucfirst($label)) }}</div>
            </div>
        @endforeach
    </div>

    <x-card title="Azure / Portal Accounts">
        <table class="amis-table">
            <thead><tr><th>UPN</th><th>Name</th><th>Type</th><th>Portal</th><th>Teams</th></tr></thead>
            <tbody>
                @forelse ($rows ?? [] as $row)
                    <tr>
                        <td>{{ $row['upn'] ?? '-' }}</td>
                        <td>{{ $row['display_name'] ?? '-' }}</td>
                        <td>{{ $row['azure_type'] ?? '-' }}</td>
                        <td>{{ !empty($row['in_portal']) ? 'Linked' : 'Missing' }}</td>
                        <td>{{ $row['teams_status'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-gray-500">No Azure rows loaded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</x-admin-layout>
