<x-admin-layout title="Subjects Workspace">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        search: '',
        activeDivision: 'elementary',
        activeMode: 'f2f',
        activeGrade: 'All',
        createModal: false,
        name: '', code: '', grade: 'Grade 1', sy: '2026-2027'
    }">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-sky-900/10" style="background: linear-gradient(135deg, #0f172a 0%, #075985 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-sky-50">Academic Workspace</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Subjects Directory</h1>
                    <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-sky-50/90">
                        Configure school courses, curriculum codes, and grade-level assignments from one academic control panel.
                    </p>
                </div>
                <button type="button" @click="createModal = true" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-sky-800 shadow-lg shadow-sky-900/20 transition hover:bg-sky-50">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    Create Subject
                </button>
            </div>
        </section>

        @php
            $grouped = $subjects->groupBy('grade_level');
            $elementaryGrades = ['Kinder 1','Kinder 2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6'];
            $highSchoolGrades = ['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'];
            $totalCount = $subjects->count();
            $islamicCount = $subjects->filter(fn($s) => in_array(strtolower($s->name), ['qur’an', 'qur\'an', 'arabic', 'shaf (seerah, hadith, aqeedah, and fiqh)']))->count();
            $coreCount = $totalCount - $islamicCount;
        @endphp

        <!-- Metrics grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
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
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
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

        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 xl:grid-cols-[260px_1fr] xl:items-center">
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-100 p-1">
                    <button type="button" @click="activeDivision = 'elementary'; activeGrade = 'All'" :class="activeDivision === 'elementary' ? 'bg-white text-sky-800 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="rounded-xl px-4 py-3 text-xs font-black uppercase tracking-[0.16em] transition">Elementary</button>
                    <button type="button" @click="activeDivision = 'highschool'; activeGrade = 'All'" :class="activeDivision === 'highschool' ? 'bg-white text-sky-800 shadow-sm' : 'text-slate-500 hover:text-slate-900'" class="rounded-xl px-4 py-3 text-xs font-black uppercase tracking-[0.16em] transition">High School</button>
                </div>

                <div class="grid gap-2 md:grid-cols-3">
                    <button type="button" @click="activeMode = 'f2f'" :class="activeMode === 'f2f' ? 'border-emerald-500 bg-emerald-50 text-emerald-800' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-2xl border px-4 py-3 text-left text-xs font-black uppercase tracking-wide transition">
                        Face to Face
                    </button>
                    <button type="button" @click="activeMode = 'flex1'" :class="activeMode === 'flex1' ? 'border-sky-500 bg-sky-50 text-sky-800' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-2xl border px-4 py-3 text-left text-xs font-black uppercase tracking-wide transition">
                        Flexible Online Learning - 1st Shift
                    </button>
                    <button type="button" @click="activeMode = 'flex2'" :class="activeMode === 'flex2' ? 'border-amber-500 bg-amber-50 text-amber-800' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-2xl border px-4 py-3 text-left text-xs font-black uppercase tracking-wide transition">
                        Flexible Online Learning - 2nd Shift
                    </button>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($elementaryGrades as $grade)
                    <button type="button" x-show="activeDivision === 'elementary'" @click="activeGrade = '{{ $grade }}'" :class="activeGrade === '{{ $grade }}' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-wide transition">{{ $grade }}</button>
                @endforeach
                @foreach ($highSchoolGrades as $grade)
                    <button type="button" x-show="activeDivision === 'highschool'" @click="activeGrade = '{{ $grade }}'" :class="activeGrade === '{{ $grade }}' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-wide transition">{{ $grade }}</button>
                @endforeach
                <button type="button" @click="activeGrade = 'All'" :class="activeGrade === 'All' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1.5 text-xs font-black uppercase tracking-wide transition">All</button>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" x-model="search" placeholder="Search subject code or name..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            </div>
            <div class="w-full text-right text-xs font-black uppercase tracking-[0.18em] text-slate-400 sm:w-auto" x-text="(activeDivision === 'elementary' ? 'Elementary' : 'High School') + ' / ' + (activeMode === 'f2f' ? 'Face to Face' : (activeMode === 'flex1' ? 'Flexible 1st Shift' : 'Flexible 2nd Shift'))"></div>
        </div>

        <!-- Grade-wise cards -->
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            @forelse($grouped as $grade => $subs)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-md" 
                     x-show="((activeDivision === 'elementary' && @js(in_array($grade, $elementaryGrades))) || (activeDivision === 'highschool' && @js(in_array($grade, $highSchoolGrades)))) &&
                             (activeGrade === 'All' || activeGrade === '{{ $grade }}') && 
                             ([
                                 @foreach($subs as $subject)
                                     '{{ strtolower($subject->name) }} {{ strtolower($subject->code) }}',
                                 @endforeach
                             ].some(s => s.includes(search.toLowerCase())))">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-sky-700">{{ $grade }}</span>
                            <h3 class="mt-3 text-lg font-black text-slate-950">{{ $grade }} Subject Card</h3>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400" x-text="activeMode === 'f2f' ? 'Face to Face' : (activeMode === 'flex1' ? 'Flexible Online Learning - 1st Shift' : 'Flexible Online Learning - 2nd Shift')"></p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $subs->count() }} SUBJECTS</span>
                    </div>

                    <div class="mt-5 grid gap-3">
                        @foreach($subs as $subject)
                            <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 transition hover:border-sky-100 hover:bg-sky-50/40" x-show="'{{ strtolower($subject->name) }} {{ strtolower($subject->code) }}'.includes(search.toLowerCase())">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <div class="text-sm font-black text-slate-950">{{ $subject->name }}</div>
                                        <div class="mt-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-400">SY {{ $subject->school_year }}</div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-xl bg-white px-3 py-2 font-mono text-[11px] font-black text-slate-700 shadow-sm">{{ $subject->code }}</span>
                                        <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-[10px] font-black uppercase tracking-wide text-slate-700 transition hover:bg-slate-100">Edit</button>
                                        <button class="rounded-xl border border-rose-100 bg-rose-50 px-3 py-2 text-[10px] font-black uppercase tracking-wide text-rose-700 transition hover:bg-rose-600 hover:text-white">Delete</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-200 bg-white p-8 text-center text-slate-400 shadow-sm">
                    <i data-lucide="info" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                    <p class="font-semibold text-sm">No subjects registered yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Create modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
             x-show="createModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200 animate-scaleUp" @click.away="createModal = false">
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
