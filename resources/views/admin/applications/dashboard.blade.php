<x-admin-layout
    title="Applications Dashboard"
    :breadcrumbs="[
        ['label' => 'Applications', 'href' => route('admin.applications.dashboard')],
        ['label' => 'Dashboard', 'href' => null],
    ]"
>
    <script type="application/json" id="application-dashboard-chart-data">
        @json($applicationCharts ?? [])
    </script>

    <section class="overflow-hidden rounded-2xl border border-emerald-700/20 bg-gradient-to-r from-emerald-800 to-teal-950 p-6 text-white shadow-sm">
        <div class="flex items-start justify-between gap-6">
            <div>
                <span class="inline-flex rounded-full border border-emerald-400/30 bg-emerald-400/15 px-3 py-1 text-xs font-extrabold uppercase tracking-wider text-emerald-100">
                    SY {{ $schoolYear }}
                </span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight">Applications Dashboard</h1>
                <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-emerald-50/90">
                    Monitor enrollment demand, review queue health, and grade-level capacity before opening the registry.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.applications.enrollment') }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-white px-4 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50">
                    <i data-lucide="table-2" class="h-4 w-4"></i>
                    Open Registry
                </a>
                <a href="{{ route('admin.applications.review') }}" class="inline-flex h-11 items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 text-sm font-bold text-white transition hover:bg-white/15">
                    <i data-lucide="file-search" class="h-4 w-4"></i>
                    Review Queue
                </a>
            </div>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'Applications', 'value' => $totalApplications, 'icon' => 'files', 'meta' => 'Submitted records'],
            ['label' => 'Families', 'value' => $familiesCount, 'icon' => 'users-round', 'meta' => 'Grouped applications'],
            ['label' => 'Review Queue', 'value' => $reviewQueue, 'icon' => 'search-check', 'meta' => 'Needs admin action'],
            ['label' => 'Seats Available', 'value' => $capacityStats['available'], 'icon' => 'armchair', 'meta' => $capacityStats['utilization'].'% capacity used'],
        ] as $stat)
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-emerald-100 hover:bg-emerald-50/30">
                <div class="flex items-center justify-between">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <i data-lucide="{{ $stat['icon'] }}" class="h-5 w-5"></i>
                    </span>
                    <span class="rounded-full bg-slate-50 px-2.5 py-1 text-xs font-bold text-slate-500">Live</span>
                </div>
                <p class="mt-5 text-sm font-semibold text-slate-500">{{ $stat['label'] }}</p>
                <p class="mt-1 text-3xl font-extrabold text-slate-950">{{ number_format($stat['value']) }}</p>
                <p class="mt-1 text-xs font-medium text-slate-500">{{ $stat['meta'] }}</p>
            </div>
        @endforeach
    </section>

    <section class="mt-6 grid grid-cols-12 gap-6">
        <x-dashboard.chart-card class="lg:col-span-4" title="Capacity Usage" subtitle="Seat utilization for current school year" chart="capacityRadialChart" />
        <x-dashboard.chart-card class="lg:col-span-8" title="Grade Capacity" subtitle="Enrolled seats against available capacity" chart="gradeCapacityChart" />
        <x-dashboard.chart-card class="lg:col-span-7" title="Application Flow" subtitle="Monthly application submissions" chart="applicationFlowChart" />
        <x-dashboard.chart-card class="lg:col-span-5" title="Student Type Mix" subtitle="New, old, returning, and transferee applicants" chart="applicationTypeChart" />
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-base font-bold text-slate-950">Grade Capacity Watchlist</h2>
                <p class="mt-1 text-sm text-slate-500">Slots by grade level, including applicant demand.</p>
            </div>
            <a href="{{ route('admin.enrollment.analytics') }}" class="inline-flex h-10 items-center gap-2 rounded-lg border border-emerald-100 bg-emerald-50 px-4 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                <i data-lucide="chart-no-axes-combined" class="h-4 w-4"></i>
                Analytics
            </a>
        </div>
        <div class="overflow-hidden rounded-lg border border-slate-200">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-bold">Grade</th>
                        <th class="px-5 py-4 font-bold">Applicants</th>
                        <th class="px-5 py-4 font-bold">Enrolled</th>
                        <th class="px-5 py-4 font-bold">Capacity</th>
                        <th class="px-5 py-4 font-bold">Available</th>
                        <th class="px-5 py-4 font-bold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($gradeSlots as $slot)
                        @php
                            $statusColor = $slot['status'] === 'Full' ? 'red' : ($slot['status'] === 'Limited' ? 'yellow' : 'green');
                        @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 font-extrabold text-slate-950">{{ $slot['grade'] }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ number_format($slot['applicant_count'] ?? 0) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ number_format($slot['enrolled']) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ number_format($slot['capacity']) }}</td>
                            <td class="px-5 py-4 font-semibold text-slate-700">{{ number_format($slot['available']) }}</td>
                            <td class="px-5 py-4"><x-badge :color="$statusColor">{{ $slot['status'] }}</x-badge></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm font-medium text-slate-500">
                                No grade capacity configuration found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-admin-layout>
