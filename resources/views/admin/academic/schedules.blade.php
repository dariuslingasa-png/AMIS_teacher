<x-admin-layout title="Class Management Workspace">
    @php
        $firstSec = $sections->first()?->id ?? 0;
    @endphp
    <div class="analytics-page flex flex-col gap-6" x-data="{
        activeWorkspace: 'sections',
        activeSectionId: {{ $firstSec }},
        addModal: false,
        syncModal: false,
        isSyncing: false,
        triggerSync() {
            this.isSyncing = true;
            setTimeout(() => { this.isSyncing = false; this.syncModal = false; }, 900);
        }
    }">
        <!-- Hero / Header Banner -->
        <div class="academic-hero-banner">
            <div class="absolute right-0 top-0 -mt-4 -mr-4 w-56 h-56 rounded-full bg-indigo-500/15 blur-3xl"></div>
            <div class="absolute left-1/3 bottom-0 -mb-8 w-64 h-64 rounded-full bg-sky-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-white/10 text-indigo-100 rounded-full border border-white/10 backdrop-blur-xs mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                        Academic Workspace
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Class Management</h1>
                    <p class="mt-2 text-sm md:text-base text-indigo-100 max-w-2xl font-light">
                        Configure daily class sections, assign advisory roles, and schedule daily timetables.
                    </p>
                </div>
                <div>
                    <template x-if="activeWorkspace === 'sections'">
                        <button type="button" @click="syncModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 active:bg-indigo-100 text-indigo-950 font-black text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-md shadow-indigo-950/20 hover:scale-[1.02] cursor-pointer">
                            <i data-lucide="refresh-cw" class="w-4 h-4 text-indigo-700"></i>
                            Sync MS Teams
                        </button>
                    </template>
                    <template x-if="activeWorkspace === 'schedule'">
                        <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 active:bg-indigo-100 text-indigo-950 font-black text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-md shadow-indigo-950/20 hover:scale-[1.02] cursor-pointer">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-700"></i>
                            Schedule Class
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Segmented Tab Switcher Bar -->
        <div class="flex gap-1.5 p-1 bg-slate-100 border border-slate-200/50 rounded-2xl max-w-xl shadow-3xs">
            <button type="button" @click="activeWorkspace = 'sections'" 
                :class="activeWorkspace === 'sections' ? 'bg-white text-indigo-800 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900 font-bold'" 
                class="flex-1 py-2 text-xs rounded-xl transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="users-round" class="w-3.5 h-3.5"></i>
                Active Sections
            </button>
            <button type="button" @click="activeWorkspace = 'advisory'" 
                :class="activeWorkspace === 'advisory' ? 'bg-white text-indigo-800 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900 font-bold'" 
                class="flex-1 py-2 text-xs rounded-xl transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="contact-2" class="w-3.5 h-3.5"></i>
                Advisory Faculty
            </button>
            <button type="button" @click="activeWorkspace = 'schedule'" 
                :class="activeWorkspace === 'schedule' ? 'bg-white text-indigo-800 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900 font-bold'" 
                class="flex-1 py-2 text-xs rounded-xl transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                Class Schedules
            </button>
        </div>

        <!-- ==================== WORKSPACE: SECTIONS ==================== -->
        <div x-show="activeWorkspace === 'sections'" x-transition class="space-y-6">
            <div class="bg-white border border-gray-150 rounded-2xl shadow-xs overflow-hidden">
                <div class="bg-slate-50/50 border-b border-gray-150 px-5 py-4 flex items-center justify-between">
                    <span class="text-slate-900 font-extrabold text-sm tracking-wide uppercase">Active Sections Catalog</span>
                </div>
                <div class="premium-table-wrap">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Section Name</th>
                                <th>Grade Level</th>
                                <th>Learning Mode</th>
                                <th>Advisory Advisor</th>
                                <th>Students Enrolled</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sections as $sec)
                                <tr>
                                    <td class="font-bold text-slate-900 text-sm">{{ $sec->grade_level }} — {{ $sec->name ?? 'HUDHAYFAH' }}</td>
                                    <td class="font-semibold text-slate-500 text-xs">{{ $sec->grade_level }}</td>
                                    <td>
                                        <x-badge color="{{ $sec->learning_mode === 'Face-to-Face' ? 'blue' : 'purple' }}">{{ $sec->learning_mode }}</x-badge>
                                    </td>
                                    <td class="font-bold text-indigo-700 text-xs">Tchr. Sarah Balabagan</td>
                                    <td class="font-extrabold text-slate-800 text-xs">{{ $sec->students_count }} students</td>
                                    <td style="text-align: right;">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100/50 uppercase font-black">Active</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================== WORKSPACE: ADVISORY ==================== -->
        <div x-show="activeWorkspace === 'advisory'" x-transition class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($sections as $sec)
                <div class="bg-white border border-gray-150 rounded-2xl shadow-xs p-5 hover:shadow-md transition duration-250 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="font-extrabold text-slate-900 text-sm block tracking-wide">{{ $sec->grade_level }}</span>
                            <x-badge color="blue">ADVISORY</x-badge>
                        </div>
                        <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-indigo-650 font-black text-xxs flex items-center justify-center shrink-0">
                                AM
                            </div>
                            <div>
                                <span class="font-extrabold text-slate-900 text-xs block">Ust. Ahmad Al-Jamil</span>
                                <span class="text-[10px] text-slate-400 font-semibold mt-0.5 block uppercase tracking-wide">Advisor assigned</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ==================== WORKSPACE: SCHEDULES ==================== -->
        <div x-show="activeWorkspace === 'schedule'" x-transition class="space-y-6">
            <!-- Section Switcher Bar -->
            <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-xs">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Select Class Group Schedule</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($sections as $sec)
                        <button type="button" @click="activeSectionId = {{ $sec->id }}" :class="activeSectionId === {{ $sec->id }} ? 'bg-indigo-700 text-white border-indigo-700 shadow-xs font-bold' : 'bg-gray-50 text-slate-600 hover:bg-gray-100 border-slate-200'" class="px-3.5 py-2 text-xs rounded-xl border transition cursor-pointer shadow-3xs">
                            {{ $sec->grade_level }} @if($sec->name) — {{ $sec->name }} @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Timetables -->
            @foreach($sections as $sec)
                <div class="bg-white border border-gray-150 rounded-2xl shadow-xs p-6 space-y-5" x-show="activeSectionId === {{ $sec->id }}" x-transition>
                    <div class="border-b border-slate-100 pb-3.5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div>
                            <span class="text-slate-900 font-extrabold text-base block">{{ $sec->grade_level }} @if($sec->name) — {{ $sec->name }} @endif Timetable</span>
                        </div>
                        <x-badge color="indigo">{{ $sec->learning_mode }}</x-badge>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-xs font-semibold text-slate-700">
                        <div class="p-4 bg-purple-50/50 border border-purple-100 rounded-xl shadow-3xs">
                            <span class="block text-[9px] uppercase tracking-wider text-purple-500 font-bold mb-1">08:00 - 09:30 AM</span>
                            <span class="block font-extrabold text-slate-900 text-sm">Qur'an Studies</span>
                            <span class="text-[10px] text-slate-500 mt-1 block">Ust. Raffy Lingasa</span>
                        </div>
                        <div class="p-4 bg-purple-50/50 border border-purple-100 rounded-xl shadow-3xs">
                            <span class="block text-[9px] uppercase tracking-wider text-purple-500 font-bold mb-1">09:30 - 11:00 AM</span>
                            <span class="block font-extrabold text-slate-900 text-sm">Arabic Language</span>
                            <span class="text-[10px] text-slate-500 mt-1 block">Ust. Ahmad Al-Jamil</span>
                        </div>
                        <div class="p-4 bg-slate-50/70 border border-slate-150 rounded-xl flex items-center justify-center text-center shadow-3xs">
                            <div>
                                <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">11:00 - 11:30 AM</span>
                                <span class="block font-extrabold text-slate-500 text-xxs uppercase">Recess Break</span>
                            </div>
                        </div>
                        <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl shadow-3xs">
                            <span class="block text-[9px] uppercase tracking-wider text-emerald-600 font-bold mb-1">11:30 - 01:00 PM</span>
                            <span class="block font-extrabold text-slate-900 text-sm">Mathematics</span>
                            <span class="text-[10px] text-slate-500 mt-1 block">Tchr. Wendy Monlingasa</span>
                        </div>
                        <div class="p-4 bg-emerald-50/50 border border-emerald-100 rounded-xl shadow-3xs">
                            <span class="block text-[9px] uppercase tracking-wider text-emerald-600 font-bold mb-1">01:00 - 02:30 PM</span>
                            <span class="block font-extrabold text-slate-900 text-sm">Makabansa</span>
                            <span class="text-[10px] text-slate-500 mt-1 block">Tchr. Sarah Balabagan</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ==================== MODALS CONFIG REGISTRY ==================== -->
        <!-- 1. Sync MS Teams Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" x-show="syncModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200" @click.away="syncModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Sync Microsoft Teams</span>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="syncModal = false">&times;</button>
                </div>
                <div class="space-y-3">
                    <p class="text-xs font-semibold text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-150">
                        This action will sync all active class sections with your institutional Microsoft 365 Tenant, assigning teacher owners and students automatically.
                    </p>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-50 border border-slate-200 rounded-xl transition cursor-pointer" @click="syncModal = false">Cancel</button>
                    <button type="button" class="relative inline-flex items-center justify-center px-5 py-2 text-xs font-bold text-white bg-indigo-700 hover:bg-indigo-600 rounded-xl transition cursor-pointer min-w-[125px] shadow-sm shadow-indigo-950/20" 
                            :class="isSyncing ? 'btn-loading' : ''" 
                            @click="triggerSync()">
                        <span class="btn-spinner" x-show="isSyncing"></span>
                        <span class="btn-text-content">Sync Workspace</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Schedule Class Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200" @click.away="addModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Schedule Class</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Map a subject to a time-slot</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="addModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Class Section *</label>
                        <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->grade_level }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Subject Name *</label>
                        <input type="text" placeholder="e.g. Science" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition cursor-pointer" @click="addModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-indigo-700 hover:bg-indigo-600 rounded-xl transition cursor-pointer" @click="addModal = false">Save Schedule</button>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
