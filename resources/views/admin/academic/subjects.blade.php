<x-admin-layout title="Subjects Workspace">
    <div class="analytics-page flex flex-col gap-6" x-data="{
        search: '',
        activeDivision: 'elementary',
        activeMode: 'f2f',
        activeGrade: 'All',
        createModal: false,
        name: '', code: '', grade: 'Grade 1', sy: @js(config('services.school.year', '2026-2027')),
        showSkeleton: false,
        isSaving: false,
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
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-white">Subjects Directory</h1>
                    <p class="mt-2 text-sm md:text-base text-indigo-100 max-w-2xl font-light">
                        Configure course catalogs, manage subject codes, and allocate curriculum tracks.
                    </p>
                </div>
                <button type="button" @click="createModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-indigo-50 active:bg-indigo-100 text-indigo-950 font-black text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-md shadow-indigo-950/20 hover:scale-[1.02] cursor-pointer">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-700"></i>
                    Create Subject
                </button>
            </div>
        </div>

        @php
            $grouped = $subjects->groupBy('grade_level');
            $elementaryGrades = ['Kinder 1','Kinder 2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6'];
            $highSchoolGrades = ['Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'];
            $totalCount = $subjects->count();
            $islamicCount = $subjects->filter(fn($s) => in_array(strtolower($s->name), ['qur’an', 'qur\'an', 'arabic', 'shaf (seerah, hadith, aqeedah, and fiqh)']))->count();
            $coreCount = $totalCount - $islamicCount;
        @endphp

        <!-- Telemetry Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Total Subjects</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-650 group-hover:scale-110 transition-transform">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $totalCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Offered courses</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">General Academics</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-650 group-hover:scale-110 transition-transform">
                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $coreCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">DepEd core subjects</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Islamic & Arabic</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-650 group-hover:scale-110 transition-transform">
                        <i data-lucide="book-marked" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-900 group-hover:text-purple-600 transition-colors">{{ $islamicCount }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Core value subjects</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Academic Year</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-650 group-hover:scale-110 transition-transform">
                        <i data-lucide="calendar" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-900 group-hover:text-amber-600 transition-colors">{{ config('services.school.year', '2026-2027') }}</span>
                    <p class="text-[11px] text-gray-400 mt-1 font-medium">Active target year</p>
                </div>
            </div>
        </div>

        <!-- Grade & Modality Filter Panel -->
        <div class="rounded-2xl border border-gray-150 bg-white p-4 shadow-sm space-y-4">
            <div class="grid gap-4 xl:grid-cols-[280px_1fr] xl:items-center">
                <div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1.5 border border-slate-200/40">
                    <button type="button" @click="activeDivision = 'elementary'; activeGrade = 'All'" :class="activeDivision === 'elementary' ? 'bg-white text-sky-700 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-900'" class="rounded-lg py-2 text-xs font-semibold uppercase tracking-wider transition cursor-pointer">Elementary</button>
                    <button type="button" @click="activeDivision = 'highschool'; activeGrade = 'All'" :class="activeDivision === 'highschool' ? 'bg-white text-sky-700 shadow-xs font-bold' : 'text-slate-500 hover:text-slate-900'" class="rounded-lg py-2 text-xs font-semibold uppercase tracking-wider transition cursor-pointer">High School</button>
                </div>

                <div class="grid gap-2 md:grid-cols-3">
                    <button type="button" @click="activeMode = 'f2f'" :class="activeMode === 'f2f' ? 'border-emerald-500 bg-emerald-50 text-emerald-800 font-bold' : 'border-gray-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-xl border px-4 py-2.5 text-left text-xs uppercase tracking-wide transition cursor-pointer">Face to Face</button>
                    <button type="button" @click="activeMode = 'flex1'" :class="activeMode === 'flex1' ? 'border-sky-500 bg-sky-50 text-sky-800 font-bold' : 'border-gray-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-xl border px-4 py-2.5 text-left text-xs uppercase tracking-wide transition cursor-pointer">Flexible - 1st Shift</button>
                    <button type="button" @click="activeMode = 'flex2'" :class="activeMode === 'flex2' ? 'border-amber-500 bg-amber-50 text-amber-800 font-bold' : 'border-gray-200 bg-white text-slate-600 hover:bg-slate-50'" class="rounded-xl border px-4 py-2.5 text-left text-xs uppercase tracking-wide transition cursor-pointer">Flexible - 2nd Shift</button>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
                <template x-if="activeDivision === 'elementary'">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($elementaryGrades as $grade)
                            <button type="button" @click="activeGrade = '{{ $grade }}'" :class="activeGrade === '{{ $grade }}' ? 'bg-slate-900 text-white border-slate-900 font-bold shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1.5 text-xs uppercase tracking-wide transition cursor-pointer">{{ $grade }}</button>
                        @endforeach
                    </div>
                </template>
                <template x-if="activeDivision === 'highschool'">
                    <div class="flex flex-wrap gap-2">
                        @foreach ($highSchoolGrades as $grade)
                            <button type="button" @click="activeGrade = '{{ $grade }}'" :class="activeGrade === '{{ $grade }}' ? 'bg-slate-900 text-white border-slate-900 font-bold shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1.5 text-xs uppercase tracking-wide transition cursor-pointer">{{ $grade }}</button>
                        @endforeach
                    </div>
                </template>
                <button type="button" @click="activeGrade = 'All'" :class="activeGrade === 'All' ? 'bg-slate-900 text-white border-slate-900 font-bold shadow-xs' : 'bg-white text-slate-650 border-slate-200 hover:bg-slate-50'" class="rounded-full border px-3 py-1.5 text-xs uppercase tracking-wide transition cursor-pointer">All</button>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-xs flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" x-model="search" @input="triggerSearch($event.target.value)" placeholder="Search subject code or name..." class="w-full bg-gray-50 border border-gray-200 text-slate-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition">
            </div>
        </div>

        <!-- Grade-wise catalog cards -->
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-2">
            <!-- Skeletons -->
            <template x-if="showSkeleton">
                <div class="rounded-2xl border border-gray-150 bg-white p-5 shadow-xs flex flex-col gap-4">
                    <div class="h-16 w-full rounded-xl skeleton-box"></div>
                </div>
            </template>

            @forelse($grouped as $grade => $subs)
                <div class="rounded-2xl border border-gray-150 bg-white p-5 shadow-xs transition hover:border-sky-300 hover:shadow-sm" 
                     x-show="!showSkeleton && ((activeDivision === 'elementary' && @js(in_array($grade, $elementaryGrades))) || (activeDivision === 'highschool' && @js(in_array($grade, $highSchoolGrades)))) &&
                             (activeGrade === 'All' || activeGrade === '{{ $grade }}') && 
                             ([
                                 @foreach($subs as $subject)
                                     '{{ strtolower($subject->name) }} {{ strtolower($subject->code) }}',
                                  @endforeach
                             ].some(s => s.includes(search.toLowerCase())))">
                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 mb-4">
                        <div>
                            <span class="inline-flex rounded-full bg-sky-50 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-sky-700 border border-sky-100 shadow-3xs">{{ $grade }}</span>
                            <h3 class="mt-2.5 text-base font-extrabold text-slate-900">{{ $grade }} Course Cards</h3>
                        </div>
                        <span class="rounded-full bg-slate-50 border border-slate-150 px-3 py-1 text-xs font-black text-slate-600 shadow-3xs">{{ $subs->count() }} SUBJECTS</span>
                    </div>

                    <div class="mt-4 grid gap-3">
                        @foreach($subs as $subject)
                            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition hover:border-sky-200 hover:bg-sky-50/30 shadow-3xs" x-show="'{{ strtolower($subject->name) }} {{ strtolower($subject->code) }}'.includes(search.toLowerCase())">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $subject->name }}</div>
                                        <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">SY {{ $subject->school_year }}</div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-lg bg-white border border-slate-150 px-3 py-1.5 font-mono text-[11px] font-bold text-slate-700 shadow-2xs">{{ $subject->code }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-gray-150 bg-white p-8 text-center text-slate-400 shadow-xs flex flex-col items-center justify-center gap-2">
                    <i data-lucide="info" class="w-8 h-8 text-slate-300"></i>
                    <p class="font-semibold text-sm">No subjects cataloged.</p>
                </div>
            @endforelse
        </div>

        <!-- Create Modal -->
        <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
             x-show="createModal" x-cloak x-transition>
            <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-200" @click.away="createModal = false">
                <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <span class="admin-modal-title text-base font-extrabold text-slate-950">Add New Subject</span>
                    </div>
                    <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" @click="createModal = false">&times;</button>
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
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-50 border border-slate-200 rounded-xl transition cursor-pointer" @click="createModal = false">Cancel</button>
                    <button type="button" class="relative inline-flex items-center justify-center px-5 py-2 text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-600 rounded-xl transition cursor-pointer min-w-[125px] shadow-sm shadow-emerald-950/20" 
                            :class="isSaving ? 'btn-loading' : ''" 
                            @click="isSaving = true; setTimeout(() => { isSaving = false; createModal = false; }, 850)">
                        <span class="btn-spinner" x-show="isSaving"></span>
                        <span class="btn-text-content">Save Subject</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
