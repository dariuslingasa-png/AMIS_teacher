<x-admin-layout title="Class Schedules Workspace">
    @php
        $firstSec = $sections->first()?->id ?? 0;
    @endphp
    <div class="analytics-page flex flex-col gap-6" x-data="{
        activeSectionId: {{ $firstSec }},
        addModal: false
    }">
        <!-- Glassmorphic Hero Header Banner -->
        <div class="relative overflow-hidden p-6 md:p-8 bg-gradient-to-r from-amber-700 to-yellow-950 rounded-2xl border border-amber-600/30 shadow-sm text-white">
            <div class="absolute right-0 top-0 -mt-4 -mr-4 w-56 h-56 rounded-full bg-amber-500/10 blur-3xl"></div>
            <div class="absolute left-1/3 bottom-0 -mb-8 w-64 h-64 rounded-full bg-yellow-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-amber-500/20 text-amber-300 rounded-full border border-amber-500/30 backdrop-blur-xs mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-40 animate-pulse"></span>
                        Timetable planner
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Class Schedule Maker</h1>
                    <p class="mt-2 text-sm md:text-base text-amber-100 max-w-2xl font-light">
                        Configure daily class timetables, assign subject time-blocks, and check teacher availability schedules.
                    </p>
                </div>
                <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-amber-50 active:bg-amber-100 text-amber-850 font-bold text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-sm hover:scale-[1.02]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Schedule Class
                </button>
            </div>
        </div>

        <!-- Telemetry metric cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Active Timetables</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-655">
                        <i data-lucide="calendar-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $sections->count() }} Schedules</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Configured section groups</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Academic Hours</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-655">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">8:00 - 5:00</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Standard school day hours</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">F2F Density</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">6 Periods</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Maximum daily courses</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Islamic Studies</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-655">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">2 Hours</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Daily IS integration blocks</p>
                </div>
            </div>
        </div>

        <!-- Section Switcher Bar -->
        <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-xs">
            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-2">Select Class Group Schedule</label>
            <div class="flex flex-wrap gap-1.5">
                @forelse($sections as $sec)
                    <button type="button" @click="activeSectionId = {{ $sec->id }}" :class="activeSectionId === {{ $sec->id }} ? 'bg-amber-700 text-white border-amber-700 shadow-sm' : 'bg-gray-50 text-slate-650 hover:bg-gray-100 border-gray-250'" class="px-3 py-1.5 text-xs font-bold rounded-lg border transition">
                        {{ $sec->grade_level }} @if($sec->name) — {{ $sec->name }} @endif
                    </button>
                @empty
                    <div class="text-xs text-slate-400">No active class sections found.</div>
                @endforelse
            </div>
        </div>

        <!-- Timetable Grid -->
        @foreach($sections as $sec)
            <div class="admin-card bg-white border border-gray-200 rounded-2xl shadow-sm p-6 space-y-5" x-show="activeSectionId === {{ $sec->id }}" x-transition>
                <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <span class="text-slate-900 font-extrabold text-base block">{{ $sec->grade_level }} @if($sec->name) — {{ $sec->name }} @endif Timetable</span>
                        <span class="text-[11px] text-slate-400 font-light mt-0.5">Assigned daily courses and active timeslots</span>
                    </div>
                    <span class="badge {{ $sec->learning_mode === 'Face-to-Face' ? 'badge-blue' : 'badge-purple' }} font-bold text-xxs px-3 py-1">{{ $sec->learning_mode }}</span>
                </div>

                <!-- Custom visual timetable time-slots -->
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 text-xs font-semibold text-slate-700">
                    <div class="p-3.5 bg-violet-50/50 border border-violet-100 rounded-xl">
                        <span class="block text-[9px] uppercase tracking-wider text-violet-400 font-bold mb-1">08:00 - 09:30 AM</span>
                        <span class="block font-extrabold text-slate-900 text-sm">Qur'an Studies</span>
                        <span class="text-[10px] text-slate-400 mt-1 block">Ust. Raffy Lingasa</span>
                    </div>
                    <div class="p-3.5 bg-violet-50/50 border border-violet-100 rounded-xl">
                        <span class="block text-[9px] uppercase tracking-wider text-violet-400 font-bold mb-1">09:30 - 11:00 AM</span>
                        <span class="block font-extrabold text-slate-900 text-sm">Arabic Language</span>
                        <span class="text-[10px] text-slate-400 mt-1 block">Ust. Ahmad Al-Jamil</span>
                    </div>
                    <div class="p-3.5 bg-slate-50 border border-slate-150 rounded-xl flex items-center justify-center text-center">
                        <div>
                            <span class="block text-[9px] uppercase tracking-wider text-slate-400 font-bold mb-0.5">11:00 - 11:30 AM</span>
                            <span class="block font-extrabold text-slate-505 text-xxs uppercase tracking-wider">Break Period</span>
                        </div>
                    </div>
                    <div class="p-3.5 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                        <span class="block text-[9px] uppercase tracking-wider text-emerald-600 font-bold mb-1">11:30 - 01:00 PM</span>
                        <span class="block font-extrabold text-slate-900 text-sm">Mathematics</span>
                        <span class="text-[10px] text-slate-400 mt-1 block">Tchr. Wendy Monlingasa</span>
                    </div>
                    <div class="p-3.5 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                        <span class="block text-[9px] uppercase tracking-wider text-emerald-600 font-bold mb-1">01:00 - 02:30 PM</span>
                        <span class="block font-extrabold text-slate-900 text-sm">Makabansa / Science</span>
                        <span class="text-[10px] text-slate-400 mt-1 block">Tchr. Sarah Balabagan</span>
                    </div>
                    <div class="p-3.5 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                        <span class="block text-[9px] uppercase tracking-wider text-emerald-600 font-bold mb-1">02:30 - 04:00 PM</span>
                        <span class="block font-extrabold text-slate-900 text-sm">Reading & Literacy</span>
                        <span class="text-[10px] text-slate-400 mt-1 block">Tchr. Sarah Balabagan</span>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Add Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
             x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-150 animate-scaleUp" @click.away="addModal = false">
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
                                <option value="{{ $sec->id }}">{{ $sec->grade_level }} @if($sec->name) — {{ $sec->name }} @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Subject Name *</label>
                        <input type="text" placeholder="e.g. Mathematics" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Timeslot *</label>
                            <input type="text" placeholder="e.g. 11:30 - 01:00 PM" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Class Teacher</label>
                            <input type="text" placeholder="e.g. Ust. Raffy" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="addModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-amber-805 hover:bg-amber-700 rounded-xl transition" @click="addModal = false">Save Schedule</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
