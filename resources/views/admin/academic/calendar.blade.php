<x-admin-layout title="Academic Calendar">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        addModal: false
    }">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-sky-900/10" style="background: linear-gradient(135deg, #0f172a 0%, #075985 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-sky-50">Academic Workspace</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Academic Calendar</h1>
                    <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-sky-50/90">
                        Schedule school operations, examination dates, holidays, and academic events.
                    </p>
                </div>
                <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-sky-800 shadow-lg shadow-sky-900/20 transition hover:bg-sky-50">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    Add Event
                </button>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Left: Calendar Events List -->
            <div class="lg:col-span-2 admin-card bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="admin-card-header bg-slate-50/50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                    <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">Upcoming Academic Events</span>
                </div>
                <div class="p-5 space-y-6">
                    @php
                        $groupedEvents = collect($events)->groupBy(function($event) {
                            return \Carbon\Carbon::parse($event['date'])->format('F Y');
                        });
                    @endphp

                    @forelse($groupedEvents as $month => $monthEvents)
                        <div class="space-y-3">
                            <span class="text-xs font-black text-slate-400 uppercase tracking-wider block border-b border-slate-100 pb-1.5">{{ $month }}</span>
                            <div class="space-y-2">
                                @foreach($monthEvents as $ev)
                                    @php
                                        $typeBadge = $ev['type'] === 'Enrollment' ? 'badge-blue' : ($ev['type'] === 'Holiday' ? 'badge-red' : ($ev['type'] === 'Exam' ? 'badge-yellow' : 'badge-green'));
                                        $day = \Carbon\Carbon::parse($ev['date'])->format('d');
                                        $dayName = \Carbon\Carbon::parse($ev['date'])->format('D');
                                    @endphp
                                    <div class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-11 h-11 bg-white border border-slate-200 rounded-xl flex flex-col items-center justify-center shrink-0 shadow-2xs">
                                                <span class="text-xs font-bold text-slate-400 leading-none uppercase">{{ $dayName }}</span>
                                                <span class="text-sm font-black text-slate-900 mt-0.5 leading-none">{{ $day }}</span>
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-905 text-xs sm:text-sm block">{{ $ev['title'] }}</span>
                                                <span class="text-[10px] text-slate-400 font-medium">{{ \Carbon\Carbon::parse($ev['date'])->format('F d, Y') }}</span>
                                            </div>
                                        </div>
                                        <span class="badge {{ $typeBadge }} font-bold text-[9px] uppercase tracking-wider px-2.5 py-0.5">{{ $ev['type'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty py-8 text-center text-slate-400">
                            <p class="font-semibold text-sm">No scheduled events found.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Event Type Legend -->
            <div class="admin-card bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">Category Legend</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-blue-50/30 border border-blue-100/50 rounded-xl">
                        <span class="text-xs font-bold text-slate-800">Enrollment Phase</span>
                        <span class="badge badge-blue font-bold text-[9px] uppercase">Enrollment</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-emerald-50/30 border border-emerald-100/50 rounded-xl">
                        <span class="text-xs font-bold text-slate-800">Academic / Class Schedule</span>
                        <span class="badge badge-green font-bold text-[9px] uppercase">Academic</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50/30 border border-red-100/50 rounded-xl">
                        <span class="text-xs font-bold text-slate-800">School Holiday</span>
                        <span class="badge badge-red font-bold text-[9px] uppercase">Holiday</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-amber-50/30 border border-amber-100/50 rounded-xl">
                        <span class="text-xs font-bold text-slate-800">Term Examination</span>
                        <span class="badge badge-yellow font-bold text-[9px] uppercase">Exam</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
             x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200 animate-scaleUp" @click.away="addModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Add Calendar Event</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Publish a school operation calendar event</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="addModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Event Title *</label>
                        <input type="text" placeholder="e.g. Midterm Exams" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Target Date *</label>
                            <input type="date" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Event Type *</label>
                            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                                <option>Academic</option>
                                <option>Enrollment</option>
                                <option>Holiday</option>
                                <option>Exam</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="addModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-rose-850 hover:bg-rose-700 rounded-xl transition" @click="addModal = false">Save Event</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
