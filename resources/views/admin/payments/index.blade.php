<x-admin-layout title="Enrollment Payment Approval">
    @php
        $sortLink = function (string $key) use ($sort, $direction) {
            $nextDirection = $sort === $key && $direction === 'asc' ? 'desc' : 'asc';

            return request()->fullUrlWithQuery([
                'sort' => $key,
                'direction' => $nextDirection,
                'page' => null,
            ]);
        };

        $sortIcon = fn (string $key) => $sort === $key
            ? ($direction === 'asc' ? 'arrow-up' : 'arrow-down')
            : 'arrow-up-down';
    @endphp

    <x-card title="Enrollment Payment Approval" subtitle="Finance Management by {{ config('services.school.finance_reviewer_name', 'Finance Office') }}">
        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-4">
            <form method="GET" class="grid gap-3 xl:grid-cols-[minmax(280px,1fr)_180px_150px_120px_auto]">
                <label class="relative block">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input name="search" value="{{ request('search') }}" placeholder="Search family, child, OR, reference, method..." class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                </label>

                <select name="status" class="h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'verified', 'rejected'] as $statusOption)
                        <option value="{{ $statusOption }}" @selected(request('status') === $statusOption)>{{ ucfirst($statusOption) }}</option>
                    @endforeach
                </select>

                <select name="sort" class="h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                    @foreach (['updated' => 'Latest update', 'family' => 'Family', 'children' => 'Children', 'amount' => 'Amount', 'method' => 'Method', 'status' => 'Status'] as $sortValue => $sortLabel)
                        <option value="{{ $sortValue }}" @selected($sort === $sortValue)>{{ $sortLabel }}</option>
                    @endforeach
                </select>

                <select name="per_page" class="h-11 rounded-xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                    @foreach ([10, 15, 25, 50] as $size)
                        <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }} rows</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button class="inline-flex h-11 items-center gap-2 rounded-xl bg-emerald-700 px-4 text-sm font-black text-white shadow-sm transition hover:bg-emerald-800">
                        <i data-lucide="filter" class="h-4 w-4"></i>
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'status', 'sort', 'direction', 'per_page']))
                        <a href="{{ route('admin.payments.index') }}" class="inline-flex h-11 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-black text-slate-600 transition hover:bg-slate-50">
                            <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid gap-3 border-b border-slate-100 p-5 sm:grid-cols-2 xl:grid-cols-6">
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Families</div>
                <div class="mt-1 text-xl font-black text-slate-950">{{ number_format($paymentSummary['families']) }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Children</div>
                <div class="mt-1 text-xl font-black text-slate-950">{{ number_format($paymentSummary['children']) }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Amount</div>
                <div class="mt-1 text-xl font-black text-slate-950">{{ number_format((float) $paymentSummary['amount'], 2) }}</div>
            </div>
            <div class="rounded-xl border border-amber-100 bg-amber-50 p-3">
                <div class="text-[10px] font-black uppercase tracking-wider text-amber-500">Pending</div>
                <div class="mt-1 text-xl font-black text-amber-700">{{ number_format($paymentSummary['pending']) }}</div>
            </div>
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-3">
                <div class="text-[10px] font-black uppercase tracking-wider text-emerald-500">Verified</div>
                <div class="mt-1 text-xl font-black text-emerald-700">{{ number_format($paymentSummary['verified']) }}</div>
            </div>
            <div class="rounded-xl border border-rose-100 bg-rose-50 p-3">
                <div class="text-[10px] font-black uppercase tracking-wider text-rose-500">Rejected</div>
                <div class="mt-1 text-xl font-black text-rose-700">{{ number_format($paymentSummary['rejected']) }}</div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-sm">
                <thead class="bg-white text-[11px] font-black uppercase tracking-widest text-slate-400">
                    <tr class="border-b border-slate-100">
                        @foreach ([
                            'family' => 'Family / Applicant',
                            'children' => 'Children',
                            'amount' => 'Amount',
                            'method' => 'Method',
                            'status' => 'Status',
                            'updated' => 'Updated',
                        ] as $key => $label)
                            <th class="px-4 py-3">
                                <a href="{{ $sortLink($key) }}" class="inline-flex items-center gap-1.5 transition hover:text-emerald-700">
                                    {{ $label }}
                                    <i data-lucide="{{ $sortIcon($key) }}" class="h-3.5 w-3.5"></i>
                                </a>
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($paymentFamilies as $family)
                        @php
                            $payment = $family['payment'];
                            $children = $family['children'];
                            $familyNo = $family['family_no'];
                            $familyLabel = $family['family_label'];
                            $familyStatus = $family['status'];
                            $statusColor = $familyStatus === 'verified' ? 'green' : ($familyStatus === 'rejected' ? 'red' : 'yellow');
                        @endphp
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-4 align-top">
                                <div class="font-black text-slate-950">{{ $familyLabel }}</div>
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                    <span>Family #{{ str_pad((string) $familyNo, 4, '0', STR_PAD_LEFT) }}</span>
                                    @if ($children->count() > 1)
                                        <span>&middot; {{ $children->count() }} children</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top">
                                <div class="flex max-w-xl flex-wrap gap-1.5">
                                    @forelse ($children as $child)
                                        @php
                                            $childStatus = strtolower((string) ($child->payment?->status ?? 'missing'));
                                            $childChip = match ($childStatus) {
                                                'verified' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
                                                'rejected' => 'bg-rose-50 text-rose-700 ring-rose-100',
                                                'pending' => 'bg-amber-50 text-amber-700 ring-amber-100',
                                                default => 'bg-slate-100 text-slate-600 ring-slate-200',
                                            };
                                        @endphp
                                        <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wide ring-1 {{ $childChip }}">
                                            {{ $child->full_name ?: 'Applicant' }}
                                        </span>
                                    @empty
                                        <span class="text-xs font-semibold text-slate-400">No child record</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-4 align-top font-semibold tabular-nums text-slate-700">{{ number_format((float) $family['amount'], 2) }}</td>
                            <td class="px-4 py-4 align-top font-semibold text-slate-700">{{ $family['methods']->isNotEmpty() ? $family['methods']->join(', ') : '-' }}</td>
                            <td class="px-4 py-4 align-top"><x-badge color="{{ $statusColor }}">{{ Str::upper($familyStatus) }}</x-badge></td>
                            <td class="px-4 py-4 align-top font-semibold text-slate-500">{{ optional($family['updated_at'])->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-right align-top">
                                @if ($payment->applicant)
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3.5 py-2 text-xs font-black uppercase tracking-wider text-slate-700 transition hover:bg-emerald-50 hover:text-emerald-700">
                                        Review
                                        <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center">
                                <div class="mx-auto flex max-w-sm flex-col items-center">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i data-lucide="search-x" class="h-6 w-6"></i>
                                    </span>
                                    <div class="mt-3 text-sm font-black text-slate-700">No payment families found</div>
                                    <div class="mt-1 text-xs font-semibold text-slate-400">Adjust the search or filters to see more records.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 px-5 py-4">{{ $paymentFamilies->links() }}</div>
    </x-card>
</x-admin-layout>
