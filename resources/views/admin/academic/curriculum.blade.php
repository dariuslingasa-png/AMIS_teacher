<x-admin-layout title="Curriculum Framework">
    <div class="analytics-page flex flex-col gap-6" x-data="{ activeTab: 'matatag', addModal: false }">
        <!-- Glassmorphic Hero Header Banner -->
        <div class="relative overflow-hidden p-6 md:p-8 bg-gradient-to-r from-emerald-800 to-teal-950 rounded-2xl border border-emerald-700/30 shadow-sm text-white">
            <div class="absolute right-0 top-0 -mt-4 -mr-4 w-56 h-56 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute left-1/3 bottom-0 -mb-8 w-64 h-64 rounded-full bg-teal-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30 backdrop-blur-xs mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Curriculum Framework
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Curriculum Settings</h1>
                    <p class="mt-2 text-sm md:text-base text-emerald-100 max-w-2xl font-light">
                        Configure foundational frameworks, integration guidelines, and primary subject structures.
                    </p>
                </div>
                <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-emerald-50 active:bg-emerald-100 text-emerald-800 font-bold text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-sm hover:scale-[1.02]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Add Framework
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Left: Curriculum details card -->
            <div class="lg:col-span-8 admin-card bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <span class="text-slate-900 font-extrabold text-base block">Active Curriculum Programs</span>
                    <span class="text-[11px] text-slate-400 font-light mt-0.5">Toggle and review active guidelines from DepEd</span>
                </div>

                <!-- Custom segmented tab bar -->
                <div class="flex gap-2 p-1.5 bg-slate-100/70 rounded-xl border border-slate-200/40">
                    <button type="button" @click="activeTab = 'matatag'" :class="activeTab === 'matatag' ? 'bg-white text-slate-900 shadow-xs border border-slate-200/30' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 cursor-pointer">MATATAG Curriculum</button>
                    <button type="button" @click="activeTab = 'k12'" :class="activeTab === 'k12' ? 'bg-white text-slate-900 shadow-xs border border-slate-200/30' : 'text-slate-500 hover:text-slate-900'" class="flex-1 py-2 text-xs font-bold rounded-lg transition duration-200 cursor-pointer">K-12 Basic Education</button>
                </div>

                <!-- MATATAG view -->
                <div x-show="activeTab === 'matatag'" class="space-y-5 pt-1" x-transition>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-800 gap-2">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider block">Status: Active Implementation</span>
                            <span class="text-[10px] text-emerald-600 font-medium">Applied for Kindergarten, Grade 1, 4, and 7</span>
                        </div>
                        <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full border border-emerald-200/40">SY 2025-2026 onwards</span>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Description</label>
                        <p class="text-slate-655 text-xs font-medium leading-relaxed">
                            The MATATAG Curriculum focuses on foundational literacy, numeracy, values integration (GMRC), Makabansa integration, and deep Islamic values integration across grade levels to provide a simplified, high-impact basic education.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Grade Coverage</label>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Kinder 1 & 2</span>
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 1</span>
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 2</span>
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 3</span>
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 4 & 7</span>
                        </div>
                    </div>
                </div>

                <!-- K12 view -->
                <div x-show="activeTab === 'k12'" class="space-y-5 pt-1" x-transition x-cloak>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-xl bg-amber-50 border border-amber-100 text-amber-800 gap-2">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider block">Status: Transition / Legacy</span>
                            <span class="text-[10px] text-amber-600 font-medium">Phasing out as MATATAG rolls out gradually</span>
                        </div>
                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-3 py-1 rounded-full border border-amber-200/40">Legacy Support</span>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Description</label>
                        <p class="text-slate-655 text-xs font-medium leading-relaxed">
                            The standard DepEd K-12 Curriculum continues to support secondary levels (Grade 10) and Senior High School strands (Grade 11 & 12 Academic / TVL) during transition periods.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Grade Coverage</label>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 10</span>
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 11 (SHS)</span>
                            <span class="px-2.5 py-1 text-xxs font-bold bg-slate-50 border border-slate-200 rounded-lg text-slate-600">Grade 12 (SHS)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Subject Categories Overview -->
            <div class="lg:col-span-4 admin-card bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-4">
                    <span class="text-slate-900 font-extrabold text-base block">Core Divisions</span>
                    <span class="text-[11px] text-slate-400 font-light mt-0.5">Overview of category groupings</span>
                </div>

                <div class="space-y-4">
                    <div class="flex gap-3 items-start p-3 bg-violet-50/30 border border-violet-100/50 rounded-xl hover:bg-violet-50/50 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-violet-50 text-violet-650 shrink-0">
                            <i data-lucide="book-marked" class="w-4.5 h-4.5"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-800 text-xs block">Islamic & Arabic Studies</span>
                            <span class="text-[10px] text-slate-400 font-medium">Qur'an, Arabic Language, SHAF values</span>
                            <span class="badge badge-purple font-bold text-[9px] mt-2 block w-max bg-purple-50 text-purple-755 border border-purple-100">4 core branches</span>
                        </div>
                    </div>

                    <div class="flex gap-3 items-start p-3 bg-emerald-50/30 border border-emerald-100/50 rounded-xl hover:bg-emerald-50/50 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-655 shrink-0">
                            <i data-lucide="award" class="w-4.5 h-4.5"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-800 text-xs block">General Academics</span>
                            <span class="text-[10px] text-slate-400 font-medium">Mathematics, Science, English, Filipino</span>
                            <span class="badge badge-green font-bold text-[9px] mt-2 block w-max bg-emerald-50 text-emerald-755 border border-emerald-100">DepEd Standard</span>
                        </div>
                    </div>

                    <div class="flex gap-3 items-start p-3 bg-blue-50/30 border border-blue-100/50 rounded-xl hover:bg-blue-50/50 transition">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655 shrink-0">
                            <i data-lucide="layers" class="w-4.5 h-4.5"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-800 text-xs block">Foundational Integrated</span>
                            <span class="text-[10px] text-slate-400 font-medium">Makabansa, Language, Reading, GMRC</span>
                            <span class="badge badge-blue font-bold text-[9px] mt-2 block w-max bg-blue-50 text-blue-755 border border-blue-100">MATATAG Integration</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Framework modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
             x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-150" @click.away="addModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-950">Add Framework</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Register a new curriculum structure</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="addModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Framework Title *</label>
                        <input type="text" placeholder="e.g. MATATAG Phase 2" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Target Level *</label>
                        <input type="text" placeholder="e.g. Grades 5 and 8" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Implementation Target SY *</label>
                        <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                            <option>SY 2026-2027</option>
                            <option>SY 2027-2028</option>
                        </select>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="addModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition" @click="addModal = false">Save Framework</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
