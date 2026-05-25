<x-admin-layout title="School Years Workspace">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        addModal: false
    }">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-sky-900/10" style="background: linear-gradient(135deg, #0f172a 0%, #075985 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-sky-50">Academic Workspace</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">School Years & Semesters</h1>
                    <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-sky-50/90">
                        Configure school years, academic terms, and overall enrollment period tracking.
                    </p>
                </div>
                <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-sky-800 shadow-lg shadow-sky-900/20 transition hover:bg-sky-50">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    Add Term
                </button>
            </div>
        </section>

        @php
            $activeYear = collect($schoolYears)->where('status', 'Active')->first();
            $upcomingYear = collect($schoolYears)->where('status', 'Upcoming')->first();
            $totalHistorical = collect($schoolYears)->sum('enrolled');
        @endphp

        <!-- Telemetry metric cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-teal-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Active Year</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-teal-50 text-teal-655 animate-pulse">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $activeYear['year'] ?? '—' }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">{{ $activeYear['semester'] ?? '—' }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Target Upcoming</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-655">
                        <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $upcomingYear['year'] ?? '—' }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Upcoming target period</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Historical Enrollment</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ number_format($totalHistorical) }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Total registered learners</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Semesters Config</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-655">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">2 Terms</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Standard school year terms</p>
                </div>
            </div>
        </div>

        <!-- School Years Table -->
        <div class="admin-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="admin-card-header bg-slate-50/50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">Historical Terms</span>
            </div>
            <div class="admin-table-container relative overflow-x-auto">
                <table class="admin-table w-full text-sm text-left text-gray-700">
                    <thead class="text-xxs text-gray-400 uppercase tracking-wider bg-slate-50/20 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">School Year</th>
                            <th class="px-5 py-3">Active Semester</th>
                            <th class="px-5 py-3">Total Enrollment</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($schoolYears as $sy)
                            @php
                                $statusBadge = $sy['status'] === 'Active' ? 'badge-green' : ($sy['status'] === 'Upcoming' ? 'badge-blue' : 'badge-gray');
                            @endphp
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-5 py-4 font-extrabold text-slate-900 text-sm">SY {{ $sy['year'] }}</td>
                                <td class="px-5 py-4 text-xs font-semibold text-slate-700">{{ $sy['semester'] }}</td>
                                <td class="px-5 py-4 text-xs font-extrabold text-emerald-600">{{ number_format($sy['enrolled']) }} enrolled</td>
                                <td class="px-5 py-4">
                                    <span class="badge {{ $statusBadge }} font-bold text-xxs px-2.5 py-0.5">{{ $sy['status'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="px-3 py-1.5 text-xxs font-bold text-slate-705 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">Configure</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
             x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200 animate-scaleUp" @click.away="addModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Add Academic Term</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Initialize a new academic calendar year</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="addModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">School Year *</label>
                        <input type="text" placeholder="e.g. 2027-2028" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Semester *</label>
                            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                                <option>1st Semester</option>
                                <option>2nd Semester</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Status *</label>
                            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                                <option>Upcoming</option>
                                <option>Active</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="addModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-teal-850 hover:bg-teal-700 rounded-xl transition" @click="addModal = false">Save Year</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
