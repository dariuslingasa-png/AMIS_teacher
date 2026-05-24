<x-admin-layout title="SOA">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-slate-950 via-amber-800 to-orange-700 p-6 text-white shadow-xl shadow-amber-900/10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">Finance Management</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Statement of Accounts</h1>
                    <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-amber-50/90">Track tuition balances, monthly billings, and verified SOA payments.</p>
                </div>
                <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    Finance Dashboard
                </a>
            </div>
        </section>

        <x-card title="Student Accounts" subtitle="Search and open SOA records">
            <form method="GET" class="mb-5 grid gap-3 md:grid-cols-[1fr_180px_140px]">
                <input name="search" value="{{ request('search') }}" placeholder="Search student name or number" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                <select name="status" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                    <option value="">All statuses</option>
                    @foreach (['unpaid', 'partial', 'paid'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ Str::headline($status) }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-amber-600 px-4 py-3 text-sm font-black uppercase tracking-wider text-white hover:bg-amber-700">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                            <th class="px-3 py-3">Student</th>
                            <th class="px-3 py-3">Grade</th>
                            <th class="px-3 py-3">Total</th>
                            <th class="px-3 py-3">Paid</th>
                            <th class="px-3 py-3">Balance</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($accounts as $account)
                            <tr>
                                <td class="px-3 py-4">
                                    <div class="font-black text-slate-950">{{ $account->student?->applicant?->full_name ?: 'Student' }}</div>
                                    <div class="mt-1 text-xs font-semibold text-slate-500">{{ $account->student?->student_number ?? 'No student number' }}</div>
                                </td>
                                <td class="px-3 py-4 font-semibold text-slate-700">{{ $account->grade_level ?? $account->student?->grade_level ?? '-' }}</td>
                                <td class="px-3 py-4 font-semibold text-slate-700">PHP {{ number_format((float) ($account->total_amount ?? $account->total_balance ?? 0), 2) }}</td>
                                <td class="px-3 py-4 font-semibold text-emerald-700">PHP {{ number_format((float) ($account->paid_amount ?? $account->amount_paid ?? 0), 2) }}</td>
                                <td class="px-3 py-4 font-black text-amber-700">PHP {{ number_format((float) $account->remaining_balance, 2) }}</td>
                                <td class="px-3 py-4"><x-badge color="{{ ($account->status ?? '') === 'paid' ? 'green' : (($account->status ?? '') === 'partial' ? 'yellow' : 'red') }}">{{ Str::upper($account->status ?? 'unpaid') }}</x-badge></td>
                                <td class="px-3 py-4 text-right">
                                    <a href="{{ route('admin.soa.show', $account) }}" class="rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-black uppercase tracking-wider text-amber-700 hover:bg-amber-100">Open SOA</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-10 text-center text-sm font-bold text-slate-400">No accounts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $accounts->links() }}</div>
        </x-card>
    </div>
</x-admin-layout>
