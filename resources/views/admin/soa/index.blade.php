<x-admin-layout title="SOA Review">
    <div class="space-y-6">
        <!-- Title Banner -->
        <section class="overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-slate-950 via-amber-800 to-orange-700 p-6 text-white shadow-xl shadow-amber-900/10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">Finance Management</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Statement of Accounts</h1>
                    <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-amber-50/90">Track tuition balances, monthly billings, and verified SOA payments grouped by family account.</p>
                </div>
                <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    Finance Dashboard
                </a>
            </div>
        </section>

        <!-- Filters & Table Dashboard -->
        <x-card title="Student Accounts" subtitle="Grouped by family batch. Click on a child's name to open their individual Statement of Account (SOA).">
            <form method="GET" class="mb-5 flex gap-3">
                <input name="search" value="{{ request('search') }}" placeholder="Search parent name or child name..." class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold outline-none transition focus:border-amber-400 focus:ring-4 focus:ring-amber-100">
                <button class="rounded-xl bg-amber-600 px-6 py-3 text-sm font-black uppercase tracking-wider text-white hover:bg-amber-700">Search</button>
            </form>

            <div class="overflow-x-auto">
                <table class="amis-table w-full text-left">
                    <thead>
                        <tr>
                            <th class="px-3 py-3">Family / Parent</th>
                            <th class="px-3 py-3">Children (Click to Open SOA)</th>
                            <th class="px-3 py-3 text-right">Total Tuition</th>
                            <th class="px-3 py-3 text-right">Amount Paid</th>
                            <th class="px-3 py-3 text-right font-black">Remaining Balance</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groupedFamilies as $family)
                            <tr class="transition-colors hover:bg-slate-50">
                                <!-- Family / Parent Details -->
                                <td class="px-3 py-4">
                                    <div class="font-black text-slate-950 uppercase tracking-tight" style="font-size: 16px !important;">{{ $family['family_label'] }}</div>
                                    <div class="mt-1 flex items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                        <span>{{ $family['family_no'] ? 'FAMILY #'.str_pad($family['family_no'], 4, '0', STR_PAD_LEFT) : 'Single application' }}</span>
                                        <span>&middot;</span>
                                        <span>{{ $family['accounts']->count() }} {{ Str::plural('CHILD', $family['accounts']->count()) }}</span>
                                    </div>
                                </td>

                                <!-- Sibling horizontal clickable chips -->
                                <td class="px-3 py-4">
                                    <div class="flex max-w-xl flex-wrap gap-2">
                                        @foreach ($family['accounts'] as $childAcc)
                                            @php
                                                $childStatus = strtolower((string) ($childAcc->status ?? 'unpaid'));
                                                $childBadgeClasses = match ($childStatus) {
                                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-250 hover:bg-emerald-100 hover:text-emerald-800',
                                                    'partial' => 'bg-amber-50 text-amber-700 border-amber-250 hover:bg-amber-100 hover:text-amber-800',
                                                    default => 'bg-rose-50 text-rose-700 border-rose-250 hover:bg-rose-100 hover:text-rose-800',
                                                };
                                            @endphp
                                            <a href="{{ route('admin.soa.show', $childAcc) }}" 
                                               class="inline-flex items-center rounded-full border px-3 py-1.5 text-[11px] font-black uppercase tracking-wide transition shadow-sm {{ $childBadgeClasses }}" 
                                               title="Open Statement of Account for {{ $childAcc->student?->applicant?->full_name }}">
                                                {{ $childAcc->student?->applicant?->full_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Financial summary figures -->
                                <td class="px-3 py-4 text-right font-semibold text-slate-700" style="font-size: 15px !important;">
                                    PHP {{ number_format($family['total_amount'], 2) }}
                                </td>
                                <td class="px-3 py-4 text-right font-semibold text-emerald-700" style="font-size: 15px !important;">
                                    PHP {{ number_format($family['amount_paid'], 2) }}
                                </td>
                                <td class="px-3 py-4 text-right font-black text-amber-700" style="font-size: 16px !important;">
                                    PHP {{ number_format($family['remaining_balance'], 2) }}
                                </td>

                                <!-- Unified family badge -->
                                <td class="px-3 py-4 text-center">
                                    <x-badge color="{{ $family['status'] === 'paid' ? 'green' : ($family['status'] === 'partial' ? 'yellow' : 'red') }}">
                                        {{ Str::upper($family['status']) }}
                                    </x-badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-10 text-center text-sm font-bold text-slate-400">
                                    No Statement of Accounts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination links -->
            <div class="mt-5">{{ $groupedFamilies->links() }}</div>
        </x-card>
    </div>
</x-admin-layout>
