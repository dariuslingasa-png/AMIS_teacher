<x-admin-layout title="Teachers Roster">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        search: '',
        addModal: false
    }">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-sky-900/10" style="background: linear-gradient(135deg, #0f172a 0%, #075985 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-sky-50">Academic Workspace</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Teachers Directory</h1>
                    <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-sky-50/90">
                        Manage faculty details, department classifications, and school email contacts.
                    </p>
                </div>
                <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-sky-800 shadow-lg shadow-sky-900/20 transition hover:bg-sky-50">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    Register Teacher
                </button>
            </div>
        </section>

        @php
            $activeCount = collect($teachers)->where('status', 'Active')->count();
            $inactiveCount = count($teachers) - $activeCount;
            $islamicStaff = collect($teachers)->where('dept', 'Arabic & Islamic Studies')->count();
            $acadStaff = count($teachers) - $islamicStaff;
        @endphp

        <!-- Telemetry metric cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Active Faculty</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-650 animate-bounce">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $activeCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Currently teaching</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Islamic & Arabic</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-655">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $islamicStaff }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Arabic & IS specialists</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">General Academics</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $acadStaff }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Elementary academics</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Faculty Inactive</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-655">
                        <i data-lucide="user-x" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $inactiveCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">On leave / inactive</p>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" x-model="search" placeholder="Search teacher by name..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
            </div>
        </div>

        <!-- Teachers Table -->
        <div class="admin-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="admin-card-header bg-slate-50/50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">Staff Roster</span>
                <span class="badge badge-purple font-bold px-3 py-1 bg-purple-50 text-purple-755 text-xs">{{ count($teachers) }} Registered</span>
            </div>
            <div class="admin-table-container relative overflow-x-auto">
                <table class="admin-table w-full text-sm text-left text-gray-700">
                    <thead class="text-xxs text-gray-400 uppercase tracking-wider bg-slate-50/20 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Teacher Name</th>
                            <th class="px-5 py-3">School Email</th>
                            <th class="px-5 py-3">Department</th>
                            <th class="px-5 py-3">Assigned Class Section</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($teachers as $t)
                            @php
                                $initials = collect(explode(' ', str_replace(['Ust. ', 'Tchr. '], '', $t['name'])))
                                    ->map(fn($part) => substr($part, 0, 1))
                                    ->implode('');
                            @endphp
                            <tr class="hover:bg-slate-50/30 transition-colors" x-show="search === '' || '{{ strtolower($t['name']) }}'.includes(search.toLowerCase())">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-655 font-black text-xxs flex items-center justify-center shrink-0">
                                            {{ $initials }}
                                        </div>
                                        <span class="font-extrabold text-slate-900 text-sm">{{ $t['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs font-semibold font-mono text-slate-655">{{ $t['email'] }}</td>
                                <td class="px-5 py-4 text-xs font-semibold text-slate-500">{{ $t['dept'] }}</td>
                                <td class="px-5 py-4 text-xs font-bold text-slate-700 bg-slate-50/80 px-2 py-0.5 rounded inline-block mt-3">{{ $t['sections'] }}</td>
                                <td class="px-5 py-4">
                                    <span class="badge {{ $t['status'] === 'Active' ? 'badge-green' : 'badge-gray' }} font-bold text-xxs">{{ $t['status'] }}</span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="px-3 py-1.5 text-xxs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">Edit</button>
                                        <button class="px-3 py-1.5 text-xxs font-bold text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 rounded-lg border border-rose-100 transition">Remove</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Register Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
             x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200 animate-scaleUp" @click.away="addModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Register Teacher</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Add a new faculty teacher to the AMIS database</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="addModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Teacher Full Name *</label>
                        <input type="text" placeholder="e.g. Ust. Bilal Al-Madani" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">School Email Address *</label>
                        <input type="email" placeholder="e.g. tr.bilal@amis.edu.ph" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Department *</label>
                            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                                <option>Arabic & Islamic Studies</option>
                                <option>Elementary Academics</option>
                                <option>Secondary Academics</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Assigned Section</label>
                            <input type="text" placeholder="e.g. Grade 1 - HUDHAYFAH" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        </div>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="addModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-purple-850 hover:bg-purple-750 rounded-xl transition" @click="addModal = false">Register Teacher</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
