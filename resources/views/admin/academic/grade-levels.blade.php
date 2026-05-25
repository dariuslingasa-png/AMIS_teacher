<x-admin-layout title="Grade Levels Workspace">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        search: '',
        configModal: false
    }">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-sky-900/10" style="background: linear-gradient(135deg, #0f172a 0%, #075985 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-sky-50">Academic Workspace</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Grade Level Workspace</h1>
                    <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-sky-50/90">
                        Monitor section allocations, configured capacity, and class structure by level.
                    </p>
                </div>
                <button type="button" @click="configModal = true" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-sky-800 shadow-lg shadow-sky-900/20 transition hover:bg-sky-50">
                    <i data-lucide="settings" class="h-4 w-4"></i>
                    Configure Grades
                </button>
            </div>
        </section>

        @php
            $grades = ['Kinder 1','Kinder 2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'];
            $totalSections = $sections->count();
            $totalStudents = $sections->sum('students_count');
        @endphp

        <!-- Telemetry metric cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-violet-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Grade Divisions</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-violet-50 text-violet-650">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">14 Levels</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Kinder to Grade 12</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Total Sections</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655">
                        <i data-lucide="users-round" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $totalSections }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Active group classes</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Enrolled Students</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-655">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ number_format($totalStudents) }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Enrolled capacity learners</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Average Density</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-655">
                        <i data-lucide="gauge" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">
                        {{ $totalSections > 0 ? round($totalStudents / $totalSections, 1) : 0 }}
                    </span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Average learners per class</p>
                </div>
            </div>
        </div>

        <!-- Search bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" x-model="search" placeholder="Search grade levels..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-violet-500 focus:border-violet-500 outline-none">
            </div>
        </div>

        <!-- Grade Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($grades as $g)
                @php
                    $gradeSections = $sections->where('grade_level', $g);
                    $secCount = $gradeSections->count();
                    $studCount = $gradeSections->sum('students_count');
                    $maxCapacity = $secCount * 40; // 40 slots average limit
                    $usedPct = $maxCapacity > 0 ? min(100, round(($studCount / $maxCapacity) * 100)) : 0;
                    $statusColor = $usedPct >= 90 ? 'bg-rose-500' : ($usedPct >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                    $statusLabel = $usedPct >= 90 ? 'Full' : ($usedPct >= 70 ? 'Limited' : 'Available');
                @endphp
                <div class="admin-card bg-white border border-slate-200 rounded-2xl shadow-xs p-5 hover:shadow-md hover:border-violet-300 hover:-translate-y-1 transition duration-200"
                     x-show="search === '' || '{{ strtolower($g) }}'.includes(search.toLowerCase())">
                    <div class="flex justify-between items-start">
                        <span class="font-extrabold text-slate-900 text-sm block tracking-wide">{{ $g }}</span>
                        <span class="badge badge-purple text-[10px] font-bold bg-purple-50 text-purple-755 border border-purple-100">LVL</span>
                    </div>

                    <!-- Enrollment Progress slot bar -->
                    <div class="mt-4 pt-3 border-t border-slate-100/70">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-750">
                            <span>Occupancy</span>
                            <span class="text-slate-900 font-extrabold">{{ $studCount }} / {{ $maxCapacity > 0 ? $maxCapacity : '—' }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300 {{ $statusColor }}" style="width: {{ $usedPct }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-1.5 text-[9px] uppercase tracking-wider text-slate-400 font-bold">
                            <span>{{ $secCount }} active section(s)</span>
                            <span class="{{ $usedPct >= 90 ? 'text-rose-600' : ($usedPct >= 70 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Config modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
             x-show="configModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200" @click.away="configModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-950">Configure Levels</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Toggle active and upcoming grade levels</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="configModal = false">&times;</button>
                </div>
                <div class="space-y-3 max-h-60 overflow-y-auto p-2 bg-slate-50 border border-slate-200 rounded-xl">
                    @foreach($grades as $g)
                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-100/50 cursor-pointer text-xs font-bold text-slate-800">
                            <input type="checkbox" checked class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                            <span>{{ $g }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="configModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-violet-850 hover:bg-violet-750 rounded-xl transition" @click="configModal = false">Save Configuration</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
