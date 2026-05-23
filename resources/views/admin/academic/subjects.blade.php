<x-admin-layout title="Subjects Workspace">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        search: '',
        activeGrade: 'All',
        createModal: false,
        name: '', code: '', grade: 'Grade 1', sy: '2026-2027'
    }">
        <!-- Glassmorphic Command Hero Banner -->
        <div class="relative overflow-hidden p-6 md:p-8 bg-gradient-to-r from-emerald-800 to-teal-950 rounded-2xl border border-emerald-700/30 shadow-sm text-white">
            <div class="absolute right-0 top-0 -mt-4 -mr-4 w-56 h-56 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute left-1/3 bottom-0 -mb-8 w-64 h-64 rounded-full bg-teal-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30 backdrop-blur-xs mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Academic core
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Subjects Directory</h1>
                    <p class="mt-2 text-sm md:text-base text-emerald-100 max-w-2xl font-light">
                        Configure school courses, curriculum codes, and grade level assignments for academic semesters.
                    </p>
                </div>
                <button type="button" @click="createModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-emerald-50 active:bg-emerald-100 text-emerald-800 font-bold text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-sm hover:scale-[1.02]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Create Subject
                </button>
            </div>
        </div>

        @php
            $grouped = $subjects->groupBy('grade_level');
            $totalCount = $subjects->count();
            $islamicCount = $subjects->filter(fn($s) => in_array(strtolower($s->name), ['qur’an', 'qur\'an', 'arabic', 'shaf (seerah, hadith, aqeedah, and fiqh)']))->count();
            $coreCount = $totalCount - $islamicCount;
        @endphp

        <!-- Metrics grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Total Subjects</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-650">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $totalCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Offered courses</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">General Academics</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-650">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $coreCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">DepEd core subjects</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Islamic & Arabic</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-650">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $islamicCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Core value subjects</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Academic Year</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-650">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xl md:text-2xl font-extrabold text-gray-955">2026-2027</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Active target year</p>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-xs flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" x-model="search" placeholder="Search subject code or name..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            </div>
            <div class="flex flex-wrap gap-1.5 w-full sm:w-auto justify-end">
                <button type="button" @click="activeGrade = 'All'" :class="activeGrade === 'All' ? 'bg-emerald-800 text-white border-emerald-800' : 'bg-gray-50 text-slate-600 hover:bg-gray-100 border-gray-250'" class="px-3.5 py-1.5 text-xs font-bold rounded-lg border transition">All Grades</button>
                @foreach($grouped as $grade => $subs)
                    <button type="button" @click="activeGrade = '{{ $grade }}'" :class="activeGrade === '{{ $grade }}' ? 'bg-emerald-800 text-white border-emerald-800' : 'bg-gray-50 text-slate-600 hover:bg-gray-100 border-gray-250'" class="px-3 py-1.5 text-xs font-bold rounded-lg border transition">{{ $grade }}</button>
                @endforeach
            </div>
        </div>

        <!-- Grade-wise sections -->
        <div class="space-y-6">
            @forelse($grouped as $grade => $subs)
                <div class="admin-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden" 
                     x-show="(activeGrade === 'All' || activeGrade === '{{ $grade }}') && 
                             ([
                                 @foreach($subs as $subject)
                                     '{{ strtolower($subject->name) }} {{ strtolower($subject->code) }}',
                                 @endforeach
                             ].some(s => s.includes(search.toLowerCase())))">
                    <div class="admin-card-header bg-slate-50/50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="font-extrabold text-slate-900 text-sm tracking-wide">{{ $grade }} Framework</span>
                        </div>
                        <span class="badge badge-blue font-bold px-3 py-1 bg-blue-50 text-blue-700 text-xs">{{ $subs->count() }} Subjects</span>
                    </div>
                    <div class="admin-table-container relative overflow-x-auto">
                        <table class="admin-table w-full text-sm text-left text-gray-700">
                            <thead class="text-xxs text-gray-400 uppercase tracking-wider bg-slate-50/20 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3">Subject Name</th>
                                    <th class="px-5 py-3">Code</th>
                                    <th class="px-5 py-3">Academic Year</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($subs as $subject)
                                    <tr class="hover:bg-slate-50/30 transition-colors" x-show="'{{ strtolower($subject->name) }} {{ strtolower($subject->code) }}'.includes(search.toLowerCase())">
                                        <td class="px-5 py-4 font-bold text-slate-900 text-sm">{{ $subject->name }}</td>
                                        <td class="px-5 py-4 font-extrabold font-mono text-xs text-blue-650 bg-blue-50/30 px-2 py-0.5 rounded-md inline-block mt-3">{{ $subject->code }}</td>
                                        <td class="px-5 py-4 font-semibold text-xs text-slate-500">SY {{ $subject->school_year }}</td>
                                        <td class="px-5 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button class="px-3 py-1.5 text-xxs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">Edit</button>
                                                <button class="px-3 py-1.5 text-xxs font-bold text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 rounded-lg border border-rose-100 transition">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="admin-card bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center text-slate-400">
                    <i data-lucide="info" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                    <p class="font-semibold text-sm">No subjects registered yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Create modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
             x-show="createModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-150 animate-scaleUp" @click.away="createModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-950">Add New Subject</span>
                        <div class="text-[11px] text-slate-400 font-light mt-0.5">Register a course in the school database</div>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-650 text-xl font-bold" @click="createModal = false">&times;</button>
                </div>
                <div class="space-y-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Subject Name *</label>
                        <input type="text" x-model="name" placeholder="e.g. Mathematics" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Subject Code *</label>
                        <input type="text" x-model="code" placeholder="e.g. MATH1" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Grade Level *</label>
                        <select x-model="grade" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                            <option value="Kinder 1">Kinder 1</option><option value="Kinder 2">Kinder 2</option>
                            <option value="Grade 1">Grade 1</option><option value="Grade 2">Grade 2</option>
                            <option value="Grade 3">Grade 3</option><option value="Grade 4">Grade 4</option>
                            <option value="Grade 5">Grade 5</option><option value="Grade 6">Grade 6</option>
                            <option value="Grade 7">Grade 7</option><option value="Grade 8">Grade 8</option>
                            <option value="Grade 9">Grade 9</option><option value="Grade 10">Grade 10</option>
                            <option value="Grade 11">Grade 11</option><option value="Grade 12">Grade 12</option>
                        </select>
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" @click="createModal = false">Cancel</button>
                    <button class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition" @click="createModal = false">Save Subject</button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
