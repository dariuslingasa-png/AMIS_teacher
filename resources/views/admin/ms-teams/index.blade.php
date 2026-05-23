<x-admin-layout title="Academic Workspace — Sections">
    <div x-data="{
        createModal: false, editModal: false, editId: null, editName: '', editError: '', editSaving: false,
        mode: 'Flexible Online Learning', grade: 'Kinder 1', shifts: ['1st Shift'], genders: ['male', 'female'],
        genderSingle: 'male', schoolYear: '2026-2027', previewList: [], progressMode: false,
        progressPercent: 0, progressLabel: '', progressRows: [], search: '',
        init() {
            this.$watch('mode', () => this.updatePreview()); this.$watch('grade', () => this.updatePreview());
            this.$watch('shifts', () => this.updatePreview()); this.$watch('genders', () => this.updatePreview());
            this.updatePreview();
        },
        updatePreview() {
            if (this.mode !== 'Flexible Online Learning') { this.previewList = []; return; }
            let list = [];
            this.shifts.forEach(s => this.genders.forEach(g => {
                const name = getSectionName(this.grade, s, g);
                const prefix = getGradePrefix(this.grade);
                list.push(`${prefix} - ${name || '?'} [${g === 'male' ? 'Boys' : 'Girls'} & ${s}]`);
            }));
            this.previewList = list;
        },
        async startCreating() {
            let combos = [];
            if (this.mode === 'Flexible Online Learning') {
                this.shifts.forEach(s => this.genders.forEach(g => { combos.push({ grade_level: this.grade, learning_mode: this.mode, shift: s, gender: g, name: getSectionName(this.grade, s, g), school_year: this.schoolYear }); }));
            } else { combos.push({ grade_level: this.grade, learning_mode: this.mode, shift: null, gender: this.genderSingle, name: getSectionName(this.grade, null, this.genderSingle), school_year: this.schoolYear }); }
            if (!combos.length) return;
            this.progressMode = true;
            this.progressRows = combos.map(c => ({ title: c.learning_mode === 'Flexible Online Learning' ? `${c.grade_level} ${c.shift} ${c.gender === 'male' ? 'Boys' : 'Girls'}` : `${c.grade_level} F2F ${c.gender === 'male' ? 'Boys' : 'Girls'}`, status: 'pending', error: '' }));
            for (let i = 0; i < combos.length; i++) {
                this.progressLabel = `Creating ${i + 1} of ${combos.length}…`; this.progressRows[i].status = 'spinning';
                try {
                    const res = await fetch('{{ route("admin.ms-teams.store-single") }}', {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify(combos[i]),
                    });
                    const data = await res.json();
                    this.progressRows[i].status = data.success ? 'done' : 'error';
                    if (!data.success) this.progressRows[i].error = data.message || 'Failed';
                } catch (e) { this.progressRows[i].status = 'error'; }
                this.progressPercent = Math.round(((i + 1) / combos.length) * 100);
            }
            this.progressLabel = 'Done!';
        },
        openEdit(id, name) { this.editId = id; this.editName = name; this.editError = ''; this.editSaving = false; this.editModal = true; },
        async saveEdit() {
            this.editSaving = true; this.editError = '';
            try {
                const res = await fetch(`/ms-teams/${this.editId}/update`, {
                    method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ name: this.editName.trim() }),
                });
                const data = await res.json();
                if (data.success) { this.editModal = false; location.reload(); }
                else { this.editError = data.message || 'Failed to update'; this.editSaving = false; }
            } catch (e) { this.editError = 'Network error. Try again.'; this.editSaving = false; }
        }
    }">

    <div class="analytics-page flex flex-col gap-6">
        <!-- Glassmorphic Command Hero Banner -->
        <div class="relative overflow-hidden p-6 md:p-8 bg-gradient-to-r from-emerald-800 to-teal-950 rounded-2xl border border-emerald-700/30 shadow-sm text-white">
            <div class="absolute right-0 top-0 -mt-4 -mr-4 w-56 h-56 rounded-full bg-emerald-500/10 blur-3xl"></div>
            <div class="absolute left-1/3 bottom-0 -mb-8 w-64 h-64 rounded-full bg-teal-500/10 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold bg-emerald-500/20 text-emerald-300 rounded-full border border-emerald-500/30 backdrop-blur-xs mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Academic sections
                    </span>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white">Class Sections Directory</h1>
                    <p class="mt-2 text-sm md:text-base text-emerald-100 max-w-2xl font-light">
                        Configure sections, set up schedules, assign academic courses, and enroll students into active classes.
                    </p>
                </div>
                <button type="button" @click="createModal = true" class="inline-flex items-center gap-2 bg-white hover:bg-emerald-50 active:bg-emerald-100 text-emerald-800 font-bold text-sm px-5 py-2.5 rounded-xl transition-all duration-150 shadow-sm hover:scale-[1.02]">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Add Section
                </button>
            </div>
        </div>

        @php
            $f2fCount = $sections->where('learning_mode', 'Face-to-Face')->count();
            $flexCount = $sections->filter(fn($s) => str_contains($s->learning_mode ?? '', 'Flexible'))->count();
            $totalSubjects = $sections->sum('subjects_count');
        @endphp

        <!-- Telemetry metric cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-emerald-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Total Sections</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-50 text-emerald-650">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $stats['total_sections'] }}</span>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-blue-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Face-to-Face</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-50 text-blue-655">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $f2fCount }}</span>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-purple-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Flexible Online</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-purple-50 text-purple-650">
                        <i data-lucide="globe" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $flexCount }}</span>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-amber-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Subjects Set Up</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-50 text-amber-650">
                        <i data-lucide="book-marked" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ $totalSubjects }}</span>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-150 p-5 shadow-xs hover:shadow-md transition-all duration-200 hover:-translate-y-1 relative overflow-hidden group border-t-4 border-t-indigo-500">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-gray-400 text-xs tracking-wider uppercase">Enrolled Students</span>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-indigo-50 text-indigo-650">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl md:text-3xl font-extrabold text-gray-955">{{ number_format($stats['total_enrolled']) }}</span>
                </div>
            </div>
        </div>

        <!-- Filter bar -->
        <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-xs">
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </span>
                <input type="search" x-model="search" placeholder="Search sections..." class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
            </div>
        </div>

        <!-- Sections Table -->
        <div class="admin-card bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="admin-card-header bg-slate-50/50 border-b border-gray-200 px-5 py-4 flex items-center justify-between">
                <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">All Active Sections</span>
                <span class="badge badge-blue font-bold px-3 py-1 bg-blue-50 text-blue-700 text-xs">{{ $sections->count() }} Sections</span>
            </div>
            <div class="admin-table-container relative overflow-x-auto">
                <table class="admin-table w-full text-sm text-left text-gray-700">
                    <thead class="text-xxs text-gray-400 uppercase tracking-wider bg-slate-50/20 border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Section Name</th>
                            <th class="px-5 py-3">Grade</th>
                            <th class="px-5 py-3">Mode</th>
                            <th class="px-5 py-3">Gender</th>
                            <th class="px-5 py-3">Subjects</th>
                            <th class="px-5 py-3">Enrolled</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($sections as $section)
                            @php
                                $isFlex = str_contains($section->learning_mode ?? '', 'Flexible');
                                $modeColor = $isFlex ? 'badge-purple' : 'badge-blue'; 
                                $modeLabel = $isFlex ? 'Flexible ' . ($section->shift ?? '') : 'F2F';
                            @endphp
                            <tr class="hover:bg-slate-50/30 transition-colors" x-show="search === '' || '{{ strtolower($section->grade_level) }} {{ strtolower($section->name) }}'.includes(search.toLowerCase())">
                                <td class="px-5 py-4 font-extrabold text-slate-900 text-sm">
                                    {{ $section->grade_level }} @if($section->name) — {{ $section->name }} @endif
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-500 text-xs">{{ $section->grade_level }}</td>
                                <td class="px-5 py-4"><span class="badge {{ $modeColor }} font-bold text-xxs">{{ $modeLabel }}</span></td>
                                <td class="px-5 py-4"><span class="badge {{ $section->gender === 'male' ? 'badge-blue' : 'badge-red' }} font-bold text-xxs">{{ $section->gender === 'male' ? 'Boys' : 'Girls' }}</span></td>
                                <td class="px-5 py-4 font-extrabold text-xs">{{ $section->subjects_count }} subjects</td>
                                <td class="px-5 py-4 font-black text-emerald-600 text-xs">{{ $section->enrolled_count }} enrolled</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.ms-teams.show', $section) }}" class="px-3 py-1.5 text-xxs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-lg transition">Manage</a>
                                        <button type="button" @click="openEdit({{ $section->id }}, '{{ addslashes($section->name ?? '') }}')" class="px-3 py-1.5 text-xxs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">Rename</button>
                                        <form method="POST" action="{{ route('admin.ms-teams.destroy', $section) }}" x-on:submit.prevent="if(confirm('Delete section {{ addslashes($section->grade_level) }}?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 text-xxs font-bold text-rose-705 hover:text-white bg-rose-50 hover:bg-rose-600 rounded-lg border border-rose-100 transition">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr x-show="search === ''"><td colspan="7"><div class="admin-empty">No sections configured. Create one to begin.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Section Modal -->
    <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
         x-show="createModal" x-cloak x-transition @click.self="if(!progressMode) createModal = false">
        <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-150 animate-scaleUp">
            <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <span class="admin-modal-title text-base font-extrabold text-slate-950">Create Grade Section</span>
                    <div class="text-[11px] text-slate-400 font-light mt-0.5">Initializes a new grade-level class group</div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" x-show="!progressMode" @click="createModal = false">&times;</button>
            </div>
            
            <div class="space-y-4" x-show="!progressMode">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Grade Level *</label>
                    <select x-model="grade" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                        <option value="Kinder 1">Kinder 1</option><option value="Kinder 2">Kinder 2</option><option value="Grade 1">Grade 1</option><option value="Grade 2">Grade 2</option><option value="Grade 3">Grade 3</option><option value="Grade 4">Grade 4</option><option value="Grade 5">Grade 5</option><option value="Grade 6">Grade 6</option><option value="Grade 7">Grade 7</option><option value="Grade 8">Grade 8</option><option value="Grade 9">Grade 9</option><option value="Grade 10">Grade 10</option><option value="Grade 11">Grade 11</option><option value="Grade 12">Grade 12</option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Learning Mode *</label>
                    <select x-model="mode" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none">
                        <option value="Face-to-Face">Face-to-Face</option>
                        <option value="Flexible Online Learning">Flexible Online Learning</option>
                    </select>
                </div>
                <div x-show="mode !== 'Flexible Online Learning'" class="flex flex-col gap-1" x-transition>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Gender *</label>
                    <select x-model="genderSingle" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none"><option value="male">Boys Only</option><option value="female">Girls Only</option></select>
                </div>
                <div x-show="mode === 'Flexible Online Learning'" class="grid grid-cols-2 gap-4" x-transition>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Shift *</label>
                        <div class="space-y-2 text-xs font-bold text-slate-700 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="1st Shift" x-model="shifts" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"> 1st Shift</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="2nd Shift" x-model="shifts" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"> 2nd Shift</label>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Gender *</label>
                        <div class="space-y-2 text-xs font-bold text-slate-700 mt-1">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="male" x-model="genders" class="rounded border-slate-300 text-emerald-650 focus:ring-emerald-500"> Boys</label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="female" x-model="genders" class="rounded border-slate-300 text-emerald-650 focus:ring-emerald-500"> Girls</label>
                        </div>
                    </div>
                </div>
                <div x-show="mode === 'Flexible Online Learning' && previewList.length > 0" class="rounded-xl bg-emerald-50 border border-emerald-100 p-3.5 text-xs text-emerald-800 space-y-1" x-transition>
                    <div>Will create <strong x-text="previewList.length"></strong> section(s):</div>
                    <template x-for="p in previewList"><div x-text="'• ' + p" class="font-extrabold"></div></template>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition">Cancel</button>
                    <button type="button" @click="startCreating()" class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition" :disabled="mode === 'Flexible Online Learning' && !previewList.length">Create Section</button>
                </div>
            </div>

            <!-- Progress Loader -->
            <div class="space-y-4 pt-2" x-show="progressMode" x-transition>
                <div class="space-y-2 max-h-48 overflow-y-auto p-1 bg-slate-50 border border-slate-150 rounded-xl">
                    <template x-for="(row, idx) in progressRows">
                        <div class="flex items-center gap-2.5 text-xs font-bold text-slate-700 py-1 px-2 hover:bg-slate-100/50 rounded-lg">
                            <span class="shrink-0 flex items-center">
                                <template x-if="row.status === 'pending'"><span class="h-4 w-4 rounded-full border-2 border-slate-200"></span></template>
                                <template x-if="row.status === 'spinning'"><svg class="animate-spin h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></template>
                                <template x-if="row.status === 'done'"><svg class="h-4 w-4 text-emerald-655" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg></template>
                                <template x-if="row.status === 'error'"><svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></template>
                            </span>
                            <span x-text="row.title + (row.error ? ' — ' + row.error : '')"></span>
                        </div>
                    </template>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-emerald-655 rounded-full transition-all duration-300" :style="'width: ' + progressPercent + '%'"></div>
                </div>
                <div x-text="progressLabel" class="text-xxs font-bold text-center text-slate-500 uppercase tracking-wider"></div>
                <div x-show="progressLabel === 'Done!'" class="text-center pt-2" x-transition><button type="button" @click="location.reload()" class="w-full px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition">Close & Reload</button></div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-950/40 backdrop-blur-xs" 
         x-show="editModal" x-cloak x-transition @click.self="if(!editSaving) editModal = false">
        <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-150 animate-scaleUp">
            <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <span class="admin-modal-title text-base font-extrabold text-slate-950">Rename Section</span>
                    <div class="text-[11px] text-slate-400 font-light mt-0.5">Renames the class group</div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" x-show="!editSaving" @click="editModal = false">&times;</button>
            </div>
            <div class="space-y-4">
                <div x-show="editError" class="rounded-xl bg-rose-50 border border-rose-100 p-3 text-xs font-bold text-rose-800" x-text="editError"></div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Section Name (optional)</label>
                    <input type="text" x-model="editName" placeholder="e.g. UTHMAN IBN AFFAN" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" :disabled="editSaving">
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" :disabled="editSaving">Cancel</button>
                    <button type="button" @click="saveEdit()" class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition" :disabled="editSaving" x-text="editSaving ? 'Saving...' : 'Save Changes'"></button>
                </div>
            </div>
        </div>
    </div>

    <script>
    const NAMES = {
        'Kinder 1|1st Shift|male':'HUSAYN IBN ALI','Kinder 1|1st Shift|female':'HUSAYN IBN ALI','Kinder 2|1st Shift|male':'ABU BAKR AS-SIDDEEQ','Kinder 2|1st Shift|female':'UTHMAN IBN AFFAN',
        'Grade 1|1st Shift|male':'HUDHAYFAH IBN AL-YAMAN','Grade 1|1st Shift|female':'ALI IBN ABI TALIB','Grade 2|1st Shift|male':'AMR IBN AL-JAMUH','Grade 2|1st Shift|female':'TALHAH IBN UBAYDALLAH',
        'Grade 3|1st Shift|male':'AMMAR IBN YASIR','Grade 3|1st Shift|female':'HABIB IBN ZAYD AL-ANSARI','Grade 4|1st Shift|male':'ABDUR RAHMAN IBN AWF','Grade 4|1st Shift|female':'HAKIM IBN HIZAM',
        'Grade 5|1st Shift|male':'MUHAMMAD IBN MASLAMAH','Grade 5|1st Shift|female':'HAMZA IBN ABDUL-MUTTALIB','Grade 6|1st Shift|male':'ABBAS IBN ABD AL-MUTTALIB','Grade 6|1st Shift|female':'ABDULLAH IBN SALAM',
        'Grade 7|1st Shift|male':'ABU SUFYAN IBN AL-HARITH','Grade 7|1st Shift|female':'USAMA IBN ZAYD','Grade 8|1st Shift|male':"SA'AD IBN MU'ADH",'Grade 8|1st Shift|female':"SA'AD IBN MU'ADH",
        'Grade 9|1st Shift|male':'ABU HURAYRAH','Grade 9|1st Shift|female':'ABU HURAYRAH','Grade 10|1st Shift|male':'UTBAH IBN GHAZWAN','Grade 10|1st Shift|female':'UTBAH IBN GHAZWAN',
        'Grade 11|1st Shift|male':'ABU UBAYDAH IBN AL-JARRAH','Grade 11|1st Shift|female':'ABU UBAYDAH IBN AL-JARRAH','Grade 12|1st Shift|male':"ABU MUSA AL-ASH'ARI",'Grade 12|1st Shift|female':"ABU MUSA AL-ASH'ARI",
        'Kinder 1|2nd Shift|male':'HUSAYN IBN ALI','Kinder 1|2nd Shift|female':'HUSAYN IBN ALI','Kinder 2|2nd Shift|male':'UMAR IBN AL-KHATTAB','Kinder 2|2nd Shift|female':"ABDULLAH IBN MAS'UD",
        'Grade 1|2nd Shift|male':'SUHAYB AR-RUMI','Grade 1|2nd Shift|female':"SA'D IBN ABI WAQQAS",'Grade 2|2nd Shift|male':'SAEED IBN ZAYD','Grade 2|2nd Shift|female':'ASIM IBN THABIT',
        'Grade 3|2nd Shift|male':'ZAYD IBN HARITHA','Grade 3|2nd Shift|female':'THABIT IBN QAYS','Grade 4|2nd Shift|male':'IKRIMAH IBN ABI JAHL','Grade 4|2nd Shift|female':'AZ-ZUBAIR IBN AL AWWAM',
        'Grade 5|2nd Shift|male':"MUS'AB IBN UMAIR",'Grade 5|2nd Shift|female':"MUS'AB IBN UMAIR",'Grade 6|2nd Shift|male':'KHALID IBN WALID','Grade 6|2nd Shift|female':'KHALID IBN WALID',
        'Grade 7|2nd Shift|male':'ANAS IBN MALIK','Grade 7|2nd Shift|female':'ANAS IBN MALIK','Grade 8|2nd Shift|male':"MU'ADH IBN JABAL",'Grade 8|2nd Shift|female':"NU'AYM IBN MAS'UD",
        'Grade 9|2nd Shift|male':'ABU DHARR AL-GHIFARI','Grade 9|2nd Shift|female':'ABU DHARR AL-GHIFARI','Grade 10|2nd Shift|male':'ABU AYYUB AL-ANSARI','Grade 10|2nd Shift|female':'ABU AYYUB AL-ANSARI',
        'Grade 11|2nd Shift|male':'ABU UBAY IBN HATIM','Grade 11|2nd Shift|female':'ABU UBAY IBN HATIM','Grade 12|2nd Shift|male':'SUHAYB AR-RUMI','Grade 12|2nd Shift|female':'SUHAYB AR-RUMI'
    };
    function getSectionName(grade, shift, gender) { return NAMES[`${grade}|${shift}|${gender}`] || null; }
    function getGradePrefix(grade) {
        if (grade === 'Kinder 1') return 'K1'; if (grade === 'Kinder 2') return 'K2';
        return 'G' + grade.replace('Grade ', '');
    }
    </script>
</x-admin-layout>
