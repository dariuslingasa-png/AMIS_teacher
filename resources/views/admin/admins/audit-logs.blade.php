<x-admin-layout title="Audit Logs">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-950 p-6 text-white shadow-xl shadow-slate-900/10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-slate-200">Security Audit</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Audit Logs</h1>
                    <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-slate-300">
                        Login, logout, failed login, and session security events.
                    </p>
                </div>
                <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-lg shadow-slate-900/20 transition hover:bg-slate-100">
                    <i data-lucide="users" class="h-4 w-4"></i>
                    Admin Accounts
                </a>
            </div>
        </section>

        <x-card title="Recent Security Events" subtitle="Latest portal authentication activity">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-3 py-3">Time</th>
                            <th class="px-3 py-3">Event</th>
                            <th class="px-3 py-3">Account</th>
                            <th class="px-3 py-3">Result</th>
                            <th class="px-3 py-3">IP Address</th>
                            <th class="px-3 py-3">Message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr>
                                <td class="px-3 py-4 text-xs font-bold text-slate-500">{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                                <td class="px-3 py-4 font-black uppercase text-slate-950">{{ Str::headline($log->event) }}</td>
                                <td class="px-3 py-4">
                                    <div class="font-bold text-slate-900">{{ $log->user?->name ?: 'Unknown User' }}</div>
                                    <div class="text-xs font-semibold text-slate-500">{{ $log->email ?: $log->user?->email ?: 'No email' }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <x-badge :color="$log->successful ? 'green' : 'red'">{{ $log->successful ? 'SUCCESS' : 'FAILED' }}</x-badge>
                                </td>
                                <td class="px-3 py-4 text-xs font-bold text-slate-600">{{ $log->ip_address ?: '-' }}</td>
                                <td class="px-3 py-4 text-xs font-semibold text-slate-500">{{ $log->message ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-10 text-center text-sm font-bold text-slate-400">No audit logs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $logs->links() }}</div>
        </x-card>
    </div>
</x-admin-layout>
