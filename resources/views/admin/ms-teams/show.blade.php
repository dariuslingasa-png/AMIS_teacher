<x-admin-layout title="Section: {{ $section->grade_level }}">
    <div x-data="{
        modalOpen: false, modalMode: 'add', loading: false, errorMsg: '', successMsg: '',
        subjectId: null, subjectName: '', teacherName: '', schedule: '', suggestedList: [],
        init() {
            const grade = '{{ $section->grade_level }}';
            const existing = @json($section->subjects->pluck('subject_name'));
            const GRADE_SUBJECTS = {
                'Grade 1': ['Makabansa', 'GMRC', 'Language', 'Reading & Literacy', 'Mathematics', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 2': ['Makabansa', 'GMRC', 'Filipino', 'English', 'Mathematics', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 3': ['Makabansa', 'GMRC', 'Filipino', 'English', 'Science', 'Mathematics', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 4': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'GMRC', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 5': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'GMRC', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 6': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'GMRC', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 7': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'GMRC', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 8': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'GMRC', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 9': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'GMRC', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)'],
                'Grade 10': ['Filipino', 'English', 'Science', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'TLE', 'ESP', 'Qur’an', 'Arabic', 'SHAF (Seerah, Hadith, Aqeedah, and Fiqh)']
            };
            const subs = GRADE_SUBJECTS[grade] || [];
            this.suggestedList = subs.map(name => ({ name, checked: !existing.includes(name), alreadyExists: existing.includes(name) }));
        },
        openAdd() {
            this.modalMode = 'add'; this.subjectName = ''; this.teacherName = ''; this.schedule = ''; this.errorMsg = ''; this.successMsg = ''; this.modalOpen = true;
        },
        openEdit(id, name, teacher, sched) {
            this.modalMode = 'edit'; this.subjectId = id; this.subjectName = name; this.teacherName = teacher; this.schedule = sched || ''; this.errorMsg = ''; this.successMsg = ''; this.modalOpen = true;
        },
        async submitForm() {
            this.loading = true; this.errorMsg = ''; this.successMsg = '';
            if (this.modalMode === 'add') {
                const selected = this.suggestedList.filter(s => s.checked).map(s => s.name);
                if (this.subjectName.trim()) selected.push(this.subjectName.trim());
                if (!selected.length) { this.errorMsg = 'Select suggested subjects or enter a custom one.'; this.loading = false; return; }
                for (let name of selected) {
                    try {
                        const res = await fetch('{{ route("admin.ms-teams.subjects.store", $section) }}', {
                            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                            body: JSON.stringify({ subject_name: name, teacher_name: this.teacherName.trim(), schedule: this.schedule.trim() })
                        });
                        const data = await res.json();
                        if (!data.success) { this.errorMsg = `Failed for ${name}: ` + (data.message || 'Error'); this.loading = false; return; }
                    } catch (e) { this.errorMsg = 'Network error assigning subjects.'; this.loading = false; return; }
                }
                this.modalOpen = false; location.reload();
            } else if (this.modalMode === 'edit') {
                if (!this.subjectName.trim()) return;
                try {
                    const res = await fetch(`/ms-teams/subjects/${this.subjectId}/update`, {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ subject_name: this.subjectName.trim(), teacher_name: this.teacherName.trim(), schedule: this.schedule.trim() })
                    });
                    const data = await res.json();
                    if (data.success) { this.modalOpen = false; location.reload(); } else { this.errorMsg = data.message || 'Failed to update subject.'; }
                } catch (e) { this.errorMsg = 'Network error. Try again.'; }
                this.loading = false;
            }
        }
    }">

    <!-- Back navigation -->
    <div class="mb-4">
        <a href="{{ route('admin.ms-teams.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-slate-900 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Sections
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-xs font-bold text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start animate-fade-in">
        <!-- Left Column: Section Details & Subjects -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Section Info Card -->
            <div class="admin-card bg-white border border-gray-255 rounded-2xl shadow-xs p-6 relative overflow-hidden group">
                <div class="absolute right-0 top-0 -mt-2 -mr-2 w-28 h-28 rounded-full bg-slate-50/50 blur-xl"></div>
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-wide">{{ $section->grade_level }} @if($section->name) — {{ $section->name }} @endif</h2>
                        @php
                            $isFlex = str_contains($section->learning_mode ?? '', 'Flexible');
                            $modeColor = $isFlex ? 'badge-purple' : 'badge-blue'; 
                            $modeLabel = $isFlex ? 'Flexible Online' : 'Face-to-Face';
                        @endphp
                        <div class="flex flex-wrap gap-2 mt-3">
                            <span class="badge {{ $modeColor }} font-bold text-xxs px-2.5 py-0.5">{{ $modeLabel }}</span>
                            @if($section->shift)<span class="badge badge-gray font-bold text-xxs px-2.5 py-0.5">{{ $section->shift }}</span>@endif
                            <span class="badge {{ $section->gender === 'male' ? 'badge-blue' : 'badge-red' }} font-bold text-xxs px-2.5 py-0.5">{{ $section->gender === 'male' ? 'Boys Only' : 'Girls Only' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Subjects Card -->
            <div class="admin-card bg-white border border-gray-255 rounded-2xl shadow-xs overflow-hidden">
                <div class="admin-card-header bg-slate-50/50 border-b border-slate-200/50 px-5 py-4 flex items-center justify-between">
                    <div>
                        <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">Assigned Courses</span>
                        <div class="text-[10px] text-slate-400 font-light mt-0.5">Manage subjects, class schedules, and teachers</div>
                    </div>
                    <button @click="openAdd()" class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition">+ Add Subject</button>
                </div>

                @if($section->subjects->isNotEmpty())
                    <div class="admin-table-container relative overflow-x-auto">
                        <table class="admin-table w-full text-sm text-left text-gray-700">
                            <thead class="text-xxs text-gray-400 uppercase tracking-wider bg-slate-50/20 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3">Subject Name</th>
                                    <th class="px-5 py-3">Teacher</th>
                                    <th class="px-5 py-3">Schedule</th>
                                    <th class="px-5 py-3">Link</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($section->subjects as $subject)
                                    <tr class="hover:bg-slate-50/30 transition-colors">
                                        <td class="px-5 py-4 font-bold text-slate-900 text-sm">{{ $subject->subject_name }}</td>
                                        <td class="px-5 py-4 text-xs font-semibold text-slate-700">{{ $subject->teacher_name ?? '—' }}</td>
                                        <td class="px-5 py-4 text-xs font-bold font-mono text-slate-655 bg-slate-50 px-2 py-0.5 rounded">{{ $subject->schedule ?? 'TBA' }}</td>
                                        <td class="px-5 py-4">
                                            @if($subject->meeting_link)
                                                <a href="{{ $subject->meeting_link }}" target="_blank" class="px-2.5 py-1 text-xxs font-bold text-emerald-800 bg-emerald-50 rounded-lg hover:bg-emerald-600 hover:text-white transition">Join Class ↗</a>
                                            @else
                                                <span class="text-xxs font-medium text-slate-400">Not set</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-right">
                                            <div class="flex justify-end gap-2">
                                                <button @click="openEdit({{ $subject->id }}, '{{ addslashes($subject->subject_name) }}', '{{ addslashes($subject->teacher_name ?? '') }}', '{{ addslashes($subject->schedule ?? '') }}')" class="px-3 py-1.5 text-xxs font-bold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200 transition">Edit</button>
                                                <form method="POST" action="{{ route('admin.ms-teams.subjects.destroy', $subject) }}" x-on:submit.prevent="if(confirm('Remove subject {{ addslashes($subject->subject_name) }}?')) $el.submit()">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 text-xxs font-bold text-rose-705 hover:text-white bg-rose-50 hover:bg-rose-600 rounded-lg border border-rose-100 transition">Remove</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="admin-empty p-8 text-center text-slate-400">
                        <i data-lucide="info" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                        <p class="font-semibold text-sm">No subjects assigned yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Enrolled Students Roster -->
        <div class="admin-card bg-white border border-gray-255 rounded-2xl shadow-xs p-5 space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <span class="admin-card-title text-slate-900 font-extrabold text-sm tracking-wide">Enrolled Students</span>
                <span class="badge badge-green font-bold text-xxs bg-emerald-50 text-emerald-805 border border-emerald-100 mt-1 block w-max">{{ $enrollments->count() }} active</span>
            </div>
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto space-y-1 p-1">
                @forelse($enrollments as $e)
                    @php
                        $first = $e->student->applicant?->first_name ?? 'S';
                        $last = $e->student->applicant?->last_name ?? '';
                        $initials = strtoupper(substr($first, 0, 1) . ( $last ? substr($last, 0, 1) : '' ));
                    @endphp
                    <div class="flex items-center justify-between py-2.5 hover:bg-slate-50/50 rounded-lg px-2 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200/50 text-slate-655 font-black text-xxs flex items-center justify-center shrink-0">
                                {{ $initials }}
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900 text-xs uppercase">{{ $last }}, {{ $first }}</div>
                                <div class="text-[9px] text-slate-400 font-mono tracking-wide mt-0.5">{{ $e->student->student_number }}</div>
                            </div>
                        </div>
                        <span class="badge badge-green text-[9px] font-bold">Active</span>
                    </div>
                @empty
                    <div class="admin-empty py-6 text-center text-slate-400">
                        <p class="font-semibold text-xs">No students enrolled yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Assign / Edit modal -->
    <div class="admin-modal-overlay flex items-center justify-center fixed inset-0 z-50 bg-slate-955/40 backdrop-blur-xs" 
         x-show="modalOpen" x-cloak x-transition @click.self="if(!loading) modalOpen = false">
        <div class="admin-modal-card bg-white rounded-2xl shadow-xl w-full max-w-md p-6 flex flex-col gap-4 border border-slate-150 animate-scaleUp">
            <div class="admin-modal-header border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <span class="admin-modal-title text-base font-extrabold text-slate-950" x-text="modalMode === 'add' ? 'Assign Courses' : 'Edit Subject Details'"></span>
                    <div class="text-[11px] text-slate-400 font-light mt-0.5" x-text="modalMode === 'add' ? 'Assign academic courses to this section group' : 'Modify subject parameters'"></div>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-655 text-xl font-bold" x-show="!loading" @click="modalOpen = false">&times;</button>
            </div>

            <div class="space-y-4">
                <div x-show="errorMsg" class="rounded-xl bg-rose-50 border border-rose-100 p-3 text-xs font-bold text-rose-800" x-text="errorMsg"></div>

                <!-- Suggested checklist (Only for mode ADD) -->
                <div x-show="modalMode === 'add' && suggestedList.length > 0" class="flex flex-col gap-1">
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Suggested Subjects for {{ $section->grade_level }}</label>
                    <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <template x-for="item in suggestedList">
                            <label class="flex items-center gap-2 text-xs font-bold p-1.5 rounded-lg hover:bg-slate-150 cursor-pointer" :class="item.alreadyExists ? 'text-slate-400 cursor-not-allowed opacity-50' : 'text-slate-750'">
                                <input type="checkbox" x-model="item.checked" :disabled="item.alreadyExists" class="rounded border-slate-350 text-emerald-600 focus:ring-emerald-500">
                                <span x-text="item.name"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400" x-text="modalMode === 'add' ? 'Custom Subject Name (Optional)' : 'Subject Name *'"></label>
                    <input type="text" x-model="subjectName" placeholder="e.g. Arabic, Mathematics" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" :disabled="loading">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Teacher Display Name</label>
                        <input type="text" x-model="teacherName" placeholder="e.g. Ust. Raffy" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" :disabled="loading">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Class Schedule</label>
                        <input type="text" x-model="schedule" placeholder="e.g. Mon/Wed 1:00-2:00 PM" class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl px-3 py-2 outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500" :disabled="loading">
                    </div>
                </div>
                <div class="admin-modal-footer flex justify-end gap-2 pt-3 border-t border-slate-50 mt-2">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-655 hover:bg-slate-50 border border-slate-200 rounded-xl transition" :disabled="loading">Cancel</button>
                    <button type="button" @click="submitForm()" class="px-4 py-2 text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-700 rounded-xl transition" :disabled="loading">
                        <span x-show="!loading" x-text="modalMode === 'add' ? 'Assign Subject(s)' : 'Save Changes'"></span>
                        <span x-show="loading" x-text="modalMode === 'add' ? 'Assigning...' : 'Saving...'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
