<x-admin-layout title="Admin Accounts">
    <div class="grid gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Admin Accounts">
            <table class="amis-table">
                <thead><tr><th>Name</th><th>Email</th><th>Created</th><th></th></tr></thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ optional($admin->created_at)->format('M d, Y') }}</td>
                            <td>
                                @if ($admin->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.admins.destroy', $admin) }}">@csrf @method('DELETE')<button class="text-sm font-medium text-red-600">Remove</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-gray-500">No admins found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
        <x-card title="Create Admin">
            <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-3">
                @csrf
                <input name="name" placeholder="Name" class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
                <input name="email" type="email" placeholder="Email" class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
                <input name="password" type="password" placeholder="Password" class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
                <input name="password_confirmation" type="password" placeholder="Confirm password" class="w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm">
                <button class="w-full rounded-lg bg-primary-700 px-4 py-2 text-sm font-medium text-white">Create</button>
            </form>
        </x-card>
    </div>
</x-admin-layout>
