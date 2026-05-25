<x-admin-layout title="Academic Dashboard">
    <script type="application/json" id="academic-dashboard-chart-data">
        @json($academicCharts ?? [])
    </script>

    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-sky-900/10" style="background: linear-gradient(135deg, #0f172a 0%, #075985 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-sky-50">Academic Workspace</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">Academic Dashboard</h1>
                    <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-sky-50/90">
                        Monitor subjects, sections, learning modes, grade coverage, and school-year setup from one workspace.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.academic.subjects') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-sky-800 shadow-lg shadow-sky-900/20 transition hover:bg-sky-50">
                        <i data-lucide="book-open" class="h-4 w-4"></i>
                        Open Subjects
                    </a>
                    <a href="{{ route('admin.ms-teams.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        <i data-lucide="users-round" class="h-4 w-4"></i>
                        Sections
                    </a>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Total Subjects</span>
                <p class="mt-2 text-3xl font-black text-slate-950">{{ $academicStats['subjects'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Sections</span>
                <p class="mt-2 text-3xl font-black text-sky-700">{{ $academicStats['sections'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Enrolled Learners</span>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $academicStats['students'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <span class="text-xs font-black uppercase tracking-wider text-slate-400">School Year</span>
                <p class="mt-2 text-2xl font-black text-slate-950">{{ $academicStats['school_year'] }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-12">
            <x-dashboard.chart-card class="xl:col-span-4" title="Subject Division" subtitle="Elementary against high school subjects" chart="academicSubjectDivisionChart" />
            <x-dashboard.chart-card class="xl:col-span-4" title="Learning Mode Sections" subtitle="Face to face and flexible learning shifts" chart="academicSectionModeChart" />
            <x-dashboard.chart-card class="xl:col-span-4" title="Grade Subject Load" subtitle="Subjects configured per grade level" chart="academicGradeSubjectsChart" />
            <x-dashboard.chart-card class="xl:col-span-12" title="Grade Section Coverage" subtitle="Active sections from Kinder to Grade 12" chart="academicGradeSectionsChart" />
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <x-card title="Elementary" subtitle="Kinder 1 to Grade 6">
                <div class="flex flex-wrap gap-2">
                    @foreach (['Kinder 1', 'Kinder 2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'] as $grade)
                        <span class="rounded-full border border-sky-100 bg-sky-50 px-3 py-1 text-xs font-black text-sky-700">{{ Str::upper($grade) }}</span>
                    @endforeach
                </div>
            </x-card>
            <x-card title="High School" subtitle="Grade 7 to Grade 12">
                <div class="flex flex-wrap gap-2">
                    @foreach (['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $grade)
                        <span class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-700">{{ Str::upper($grade) }}</span>
                    @endforeach
                </div>
            </x-card>
            <x-card title="Learning Modes" subtitle="Academic class delivery setup">
                <div class="space-y-2">
                    @foreach (['Face to Face', 'Flexible Online Learning - 1st Shift', 'Flexible Online Learning - 2nd Shift'] as $mode)
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs font-black uppercase tracking-wide text-slate-700">{{ $mode }}</div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-admin-layout>
