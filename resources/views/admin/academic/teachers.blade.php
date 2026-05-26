<x-admin-layout title="Teachers Workspace">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        activeWorkspace: 'faculty',
        search: '',
        addModal: false,
        assignModal: false,
        showSkeleton: false,
        isRegistering: false,
        isAssigning: false,
        triggerSearch(val) {
            this.showSkeleton = true;
            setTimeout(() => { this.showSkeleton = false; }, 300);
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
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Teachers & Faculty</h1>
                    <p class="mt-2 text-sm md:text-base text-indigo-100 max-w-2xl font-light">
                        Monitor teaching workload distributions, map course assignments, and view the faculty directory.
                    </p>
                </div>
                <div>
                    <template x-if="activeWorkspace === 'assignments'">
                        <button type="button" @click="assignModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 active:bg-indigo-100 text-indigo-950 font-black text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-md shadow-indigo-950/20 hover:scale-[1.02] cursor-pointer">
                            <i data-lucide="book-check" class="w-4 h-4 text-indigo-700"></i>
                            Assign Subject
                        </button>
                    </template>
                    <template x-if="activeWorkspace !== 'assignments'">
                        <button type="button" @click="addModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 active:bg-indigo-100 text-indigo-950 font-black text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-md shadow-indigo-950/20 hover:scale-[1.02] cursor-pointer">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-700"></i>
                            Register Teacher
                        </button>
                    </template>
                </div>
            </div>
        </div>

        @php
            $activeCount = collect($teachers)->where('status', 'Active')->count();
            $inactiveCount = count($teachers) - $activeCount;
            $islamicStaff = collect($teachers)->where('dept', 'Arabic & Islamic Studies')->count();
            $acadStaff = count($teachers) - $islamicStaff;
        @endphp

        <!-- Telemetry Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Active Faculty</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-650 group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-900 group-hover:text-purple-655 transition-colors">{{ $activeCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Currently teaching</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Islamic & Arabic</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-655 group-hover:scale-110 transition-transform">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-emerald-700 group-hover:text-emerald-650 transition-colors">{{ $islamicStaff }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Arabic & IS specialists</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">General Academics</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655 group-hover:scale-110 transition-transform">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-blue-700 group-hover:text-blue-650 transition-colors">{{ $acadStaff }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Elementary academics</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Average Load</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-655 group-hover:scale-110 transition-transform">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-900 group-hover:text-amber-650 transition-colors">18 hrs/wk</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Optimal staff utilization</p>
                </div>
            </div>
        </div>

        <!-- Segmented Tab Switcher Bar -->
        <div class="flex gap-1.5 p-1 bg-slate-100 border border-slate-200/50 rounded-2xl max-w-xl shadow-3xs">
            <button type="button" @click="activeWorkspace = 'faculty'" 
                :class="activeWorkspace === 'faculty' ? 'bg-white text-indigo-800 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900 font-bold'" 
                class="flex-1 py-2 text-xs rounded-xl transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="contact-2" class="w-3.5 h-3.5"></i>
                Faculty Roster
            </button>
            <button type="button" @click="activeWorkspace = 'workload'" 
                :class="activeWorkspace === 'workload' ? 'bg-white text-indigo-800 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900 font-bold'" 
                class="flex-1 py-2 text-xs rounded-xl transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                Teaching Workload
            </button>
            <button type="button" @click="activeWorkspace = 'assignments'" 
                :class="activeWorkspace === 'assignments' ? 'bg-white text-indigo-800 shadow-sm font-black' : 'text-slate-500 hover:text-slate-900 font-bold'" 
                class="flex-1 py-2 text-xs rounded-xl transition duration-200 cursor-pointer flex items-center justify-center gap-1.5 uppercase tracking-wider">
                <i data-lucide="book-check" class="w-3.5 h-3.5"></i>
                Subject Assignments
            </button>
        </div>

        <!-- ==================== TAB: FACULTY ROSTER ==================== -->
        <div x-show="activeWorkspace === 'faculty'" x-transition class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-xs">
                <div class="relative w-full sm:max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="search" x-model="search" @input="triggerSearch($event.target.value)" placeholder="Search teacher by name..." class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all duration-150">
                </div>
            </div>

            <div class="bg-white border border-gray-150 rounded-2xl shadow-xs overflow-hidden">
                <div class="bg-slate-50/50 border-b border-gray-150 px-5 py-4 flex items-center justify-between">
                    <span class="text-slate-900 font-extrabold text-sm tracking-wide uppercase">Staff Roster</span>
                    <x-badge color="purple">{{ count($teachers) }} Registered</x-badge>
                </div>
                <div class="premium-table-wrap">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>School Email</th>
                                <th>Department</th>
                                <th>Assigned Class Section</th>
                                <th>Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Skeletons -->
                            <template x-if="showSkeleton">
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full skeleton-box shrink-0"></div>
                                            <div class="h-4 w-32 skeleton-box"></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4"><div class="h-3.5 w-44 skeleton-box"></div></td>
                                    <td class="px-6 py-4"><div class="h-3.5 w-28 skeleton-box"></div></td>
                                    <td class="px-6 py-4"><div class="h-5 w-24 skeleton-box"></div></td>
                                    <td class="px-6 py-4"><div class="h-5 w-16 skeleton-box"></div></td>
                                    <td class="px-6 py-4 text-right"><div class="inline-block h-8 w-24 skeleton-box"></div></td>
                                </tr>
                            </template>
                            <!-- Real Data -->
                            @foreach ($teachers as $t)
                                @php
                                    $initials = collect(explode(' ', str_replace(['Ust. ', 'Tchr. '], '', $t['name'])))
                                        ->map(fn($part) => substr($part, 0, 1))
                                        ->implode('');
                                @endphp
                                <tr x-show="!showSkeleton && (search === '' || '{{ strtolower($t['name']) }}'.includes(search.toLowerCase()))">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-655 font-black text-xxs flex items-center justify-center shrink-0 shadow-3xs">
                                                {{ $initials }}
                                            </div>
                                            <span class="font-extrabold text-slate-900 text-sm tracking-wide">{{ $t['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-xs font-semibold font-mono text-slate-500">{{ $t['email'] }}</td>
                                    <td class="text-xs font-semibold text-slate-500">{{ $t['dept'] }}</td>
                                    <td>
                                        <span class="inline-flex rounded bg-slate-50 border border-slate-150 px-2 py-0.5 text-xs font-bold text-slate-700 shadow-3xs">{{ $t['sections'] }}</span>
                                    </td>
                                    <td>
                                        <x-badge color="{{ $t['status'] === 'Active' ? 'green' : 'gray' }}">{{ Str::upper($t['status']) }}</x-badge>
                                    </td>
                                    <td style="text-align: right;">
                                        <button class="px-3 py-1.5 text-xxs font-bold text-slate-700 hover:bg-slate-100 rounded-lg border border-slate-200 transition cursor-pointer shadow-3xs">Edit</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================== TAB: TEACHING WORKLOAD ==================== -->
        <div x-show="activeWorkspace === 'workload'" x-transition class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($teachers as $index => $t)
                @php
                    $loadHours = [18, 22, 16, 20, 0][$index % 5];
                    $maxHours = 24;
                    $percent = ($loadHours / $maxHours) * 100;
                    $loadStatus = $loadHours >= 20 ? 'Optimal Load' : ($loadHours == 0 ? 'No Load' : 'Underloaded');
                    $loadColor = $loadHours >= 20 ? 'indigo' : ($loadHours == 0 ? 'amber' : 'emerald');
                @endphp
                <div class="bg-white border border-gray-150 rounded-2xl shadow-xs p-5 hover:shadow-md transition duration-250 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-extrabold text-slate-900 text-sm block tracking-wide">{{ $t['name'] }}</span>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider mt-0.5">{{ $t['dept'] }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-{{ $loadColor }}-50 text-{{ $loadColor }}-700 border border-{{ $loadColor }}-100/50 uppercase font-black">{{ $loadStatus }}</span>
                        </div>
                        <div class="mt-6 space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 font-bold">Assigned Period Capacity</span>
                                <span class="font-extrabold text-slate-900">{{ $loadHours }} / {{ $maxHours }} hrs/wk</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                <div class="bg-{{ $loadColor == 'indigo' ? 'indigo-600' : ($loadColor == 'amber' ? 'amber-500' : 'emerald-500') }} h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Active Classes</span>
                            <span class="font-extrabold text-slate-800">{{ $t['sections'] }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ==================== TAB: SUBJECT ASSIGNMENTS ==================== -->
        <div x-show="activeWorkspace === 'assignments'" x-transition class="space-y-6">
            <div class="bg-white border border-gray-150 rounded-2xl shadow-xs overflow-hidden">
                <div class="bg-slate-50/50 border-b border-gray-150 px-5 py-4 flex items-center justify-between">
                    <span class="text-slate-900 font-extrabold text-sm tracking-wide uppercase">Subject - Instructor Mappings</span>
                </div>
                <div class="premium-table-wrap">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Grade Level</th>
                                <th>Assigned Instructor</th>
                                <th style="text-align: right;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-mono font-bold text-slate-500 text-xs">QURAN1</td>
                                <td class="font-extrabold text-slate-900 text-sm">Qur’an Studies</td>
                                <td class="font-semibold text-slate-500 text-xs">Grade 1</td>
                                <td class="font-bold text-indigo-700 text-xs">Ust. Raffy Lingasa</td>
                                <td style="text-align: right;"><x-badge color="indigo">ASSIGNED</x-badge></td>
                            </tr>
                            <tr>
                                <td class="font-mono font-bold text-slate-500 text-xs">MATH1</td>
                                <td class="font-extrabold text-slate-900 text-sm">Mathematics</td>
                                <td class="font-semibold text-slate-500 text-xs">Grade 1</td>
                                <td class="font-bold text-indigo-700 text-xs">Tchr. Wendy Monlingasa</td>
                                <td style="text-align: right;"><x-badge color="indigo">ASSIGNED</x-badge></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Register Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
             x-show="addModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200" @click.away="addModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Register Teacher</span>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="addModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Teacher Full Name *</label>
                        <input type="text" placeholder="e.g. Ust. Bilal Al-Madani" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-50 border border-slate-200 rounded-xl transition cursor-pointer" @click="addModal = false">Cancel</button>
                    <button type="button" class="relative inline-flex items-center justify-center px-5 py-2 text-xs font-bold text-white bg-indigo-700 hover:bg-indigo-600 rounded-xl transition cursor-pointer min-w-[125px] shadow-sm shadow-indigo-950/20" 
                            :class="isRegistering ? 'btn-loading' : ''" 
                            @click="isRegistering = true; setTimeout(() => { isRegistering = false; addModal = false; }, 850)">
                        <span class="btn-spinner" x-show="isRegistering"></span>
                        <span class="btn-text-content">Register Teacher</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Assign Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
             x-show="assignModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200" @click.away="assignModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-955">Assign Subject</span>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="assignModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Select Subject *</label>
                        <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                            <option>QURAN1 — Qur’an Studies (Grade 1)</option>
                        </select>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-50 border border-slate-200 rounded-xl transition cursor-pointer" @click="assignModal = false">Cancel</button>
                    <button type="button" class="relative inline-flex items-center justify-center px-5 py-2 text-xs font-bold text-white bg-indigo-700 hover:bg-indigo-600 rounded-xl transition cursor-pointer min-w-[125px] shadow-sm shadow-indigo-950/20" 
                            :class="isAssigning ? 'btn-loading' : ''" 
                            @click="isAssigning = true; setTimeout(() => { isAssigning = false; assignModal = false; }, 850)">
                        <span class="btn-spinner" x-show="isAssigning"></span>
                        <span class="btn-text-content">Assign Instructor</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
