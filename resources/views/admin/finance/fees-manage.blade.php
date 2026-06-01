<x-admin-layout title="School Fees Management">
    <div class="space-y-6">
        <!-- Hero Banner -->
        <section class="overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-slate-950 via-amber-800 to-orange-700 p-6 text-white shadow-xl shadow-amber-900/10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">Finance Management</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">School Fees Configuration</h1>
                    <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-amber-50/90">Manage tuition, miscellaneous, and books fees per grade level for SY {{ $schoolYear }}.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.finance.fees') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white/10 border border-white/20 px-5 py-3 text-sm font-black text-white transition hover:bg-white/20">
                        <i data-lucide="printer" class="h-4 w-4"></i>
                        Print Fee Schedule
                    </a>
                    <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                        <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                        Finance Dashboard
                    </a>
                </div>
            </div>
        </section>

        <!-- Fee Table -->
        <x-card title="Fee Schedule" subtitle="Edit tuition, miscellaneous, and books fees per grade level.">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-white text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3">Grade Level</th>
                            <th class="px-4 py-3 text-right">Tuition Fee</th>
                            <th class="px-4 py-3 text-right">Miscellaneous</th>
                            <th class="px-4 py-3 text-right">Books & E-Fees</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($fees as $fee)
                            <tr class="hover:bg-slate-50/80 transition" x-data="{ editing: false }">
                                <td class="px-4 py-4 font-black text-slate-950">{{ $fee->grade_level }}</td>
                                <td class="px-4 py-4 text-right font-semibold tabular-nums">{{ number_format((float) $fee->tuition_fee, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold tabular-nums">{{ number_format((float) $fee->misc_fee, 2) }}</td>
                                <td class="px-4 py-4 text-right font-semibold tabular-nums">{{ number_format((float) $fee->books_fee, 2) }}</td>
                                <td class="px-4 py-4 text-right font-black text-emerald-700 tabular-nums">{{ number_format((float) $fee->tuition_fee + (float) $fee->misc_fee + (float) $fee->books_fee, 2) }}</td>
                                <td class="px-4 py-4 text-center">
                                    <button @click="editing = !editing" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-100 px-3 py-1.5 text-xs font-black text-slate-700 transition hover:bg-amber-50 hover:text-amber-700">
                                        <i data-lucide="pencil" class="h-3 w-3"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <tr x-show="editing" x-cloak class="bg-amber-50/30">
                                <td colspan="6" class="px-4 py-4">
                                    <form method="POST" action="{{ route('admin.finance.fees-manage.store') }}" class="flex flex-wrap items-end gap-3">
                                        @csrf
                                        <input type="hidden" name="grade_level" value="{{ $fee->grade_level }}">
                                        <input type="hidden" name="school_year" value="{{ $schoolYear }}">
                                        <div>
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tuition</label>
                                            <input name="tuition_fee" type="number" step="0.01" value="{{ $fee->tuition_fee }}" class="mt-1 w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Miscellaneous</label>
                                            <input name="misc_fee" type="number" step="0.01" value="{{ $fee->misc_fee }}" class="mt-1 w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Books</label>
                                            <input name="books_fee" type="number" step="0.01" value="{{ $fee->books_fee }}" class="mt-1 w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                                        </div>
                                        <button type="submit" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-black text-white hover:bg-emerald-800">Save</button>
                                        <button type="button" @click="editing = false" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-black text-slate-600 hover:bg-slate-50">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-sm font-bold text-slate-400">No fee records found for SY {{ $schoolYear }}.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Add New Fee -->
        <x-card title="Add New Fee" subtitle="Create a fee record for a grade level not yet configured.">
            <form method="POST" action="{{ route('admin.finance.fees-manage.store') }}" class="flex flex-wrap items-end gap-3 p-5">
                @csrf
                <div>
                    <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Grade Level</label>
                    <select name="grade_level" class="mt-1 w-40 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                        @foreach (['Kinder 1','Kinder 2','Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6','Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12'] as $grade)
                            <option value="{{ $grade }}">{{ $grade }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="school_year" value="{{ $schoolYear }}">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tuition</label>
                    <input name="tuition_fee" type="number" step="0.01" required placeholder="0.00" class="mt-1 w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Miscellaneous</label>
                    <input name="misc_fee" type="number" step="0.01" required placeholder="0.00" class="mt-1 w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-wider text-slate-400">Books</label>
                    <input name="books_fee" type="number" step="0.01" required placeholder="0.00" class="mt-1 w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold">
                </div>
                <button type="submit" class="rounded-xl bg-amber-600 px-5 py-2 text-xs font-black text-white hover:bg-amber-700">Add Fee</button>
            </form>
        </x-card>
    </div>
</x-admin-layout>
