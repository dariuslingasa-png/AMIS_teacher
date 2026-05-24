<x-admin-layout title="Edit Access Account">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-slate-950 via-emerald-800 to-teal-700 p-6 text-white shadow-xl shadow-emerald-900/10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-emerald-50">Access Editor</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">{{ $user->name }}</h1>
                    <p class="mt-2 text-sm font-medium leading-6 text-emerald-50/90">{{ $user->email }}</p>
                </div>
                <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-emerald-800 shadow-lg shadow-emerald-900/20 transition hover:bg-emerald-50">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Back to Accounts
                </a>
            </div>
        </section>

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.admins.update', $user) }}" class="grid gap-6 xl:grid-cols-[1fr_420px]">
            @csrf
            @method('PATCH')

            <div class="space-y-6">
                <x-card title="Account Details" subtitle="Admin can edit all profile and login fields">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Display Name</label>
                            <input name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Email</label>
                            <input name="email" value="{{ old('email', $user->email) }}" type="email" required class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Account Status</label>
                            <select name="account_status" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                                @foreach (['verified', 'pending', 'disabled'] as $status)
                                    <option value="{{ $status }}" @selected(old('account_status', $user->account_status ?? 'verified') === $status)>{{ Str::headline($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Current Role</label>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm font-black uppercase text-slate-950">{{ $user->role }}</div>
                        </div>
                    </div>
                </x-card>

                <x-card title="Access Matrix" subtitle="Set role and permissions for this account">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-center text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-black uppercase tracking-widest text-slate-400">
                                    <th class="px-3 py-3 text-left">Permission</th>
                                    <th class="px-3 py-3">Enabled</th>
                                    <th class="px-3 py-3 text-left">Meaning</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ([
                                    'admin' => ['Role: Admin', 'Full admin role label and access baseline.'],
                                    'finance' => ['Role: Finance', 'Finance role label and access baseline.'],
                                    'staff' => ['Role: Staff', 'Staff role label and view-only baseline.'],
                                ] as $roleValue => [$label, $description])
                                    <tr>
                                        <td class="px-3 py-4 text-left font-black text-slate-950">{{ $label }}</td>
                                        <td class="px-3 py-4">
                                            <input
                                                type="checkbox"
                                                name="role"
                                                value="{{ $roleValue }}"
                                                @checked(old('role', $user->role) === $roleValue)
                                                class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-500"
                                                onclick="document.querySelectorAll('input[name=role]').forEach((box) => { if (box !== this) box.checked = false; });"
                                            >
                                        </td>
                                        <td class="px-3 py-4 text-left text-xs font-semibold text-slate-500">{{ $description }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="px-3 py-4 text-left font-black text-slate-950">Able to approve/reject payment</td>
                                    <td class="px-3 py-4">
                                        <input type="checkbox" name="payment_review" value="1" @checked(old('payment_review', $permissions['payment_review'] ?? false)) class="h-4 w-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                    </td>
                                    <td class="px-3 py-4 text-left text-xs font-semibold text-slate-500">Shows and allows payment proof APPROVE/REJECT actions.</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-4 text-left font-black text-slate-950">Able to approve/reject documents</td>
                                    <td class="px-3 py-4">
                                        <input type="checkbox" name="document_review" value="1" @checked(old('document_review', $permissions['document_review'] ?? false)) class="h-4 w-4 rounded border-slate-300 text-emerald-700 focus:ring-emerald-500">
                                    </td>
                                    <td class="px-3 py-4 text-left text-xs font-semibold text-slate-500">Shows and allows document/application approval actions.</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-4 text-left font-black text-slate-950">View only</td>
                                    <td class="px-3 py-4">
                                        <input
                                            type="checkbox"
                                            name="view_only"
                                            value="1"
                                            @checked(old('view_only', $permissions['view_only'] ?? false))
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                            onchange="if (this.checked) { document.querySelectorAll('input[name=payment_review], input[name=document_review]').forEach((box) => box.checked = false); }"
                                        >
                                    </td>
                                    <td class="px-3 py-4 text-left text-xs font-semibold text-slate-500">Hides and blocks all approval actions.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            <div class="space-y-6">
                <x-card title="Password" subtitle="Leave blank to keep current password">
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">New Password</label>
                            <input name="password" type="password" placeholder="Optional" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-black uppercase tracking-wider text-slate-500">Confirm New Password</label>
                            <input name="password_confirmation" type="password" placeholder="Retype optional password" class="w-full rounded-xl border border-slate-200 bg-white p-3 text-sm font-semibold outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                        </div>
                    </div>
                </x-card>

                <x-card title="Save Changes">
                    <button class="w-full rounded-2xl bg-emerald-700 px-4 py-3 text-sm font-black uppercase tracking-wider text-white shadow-lg shadow-emerald-700/20 transition hover:bg-emerald-800">
                        Save Account
                    </button>
                </x-card>
            </div>
        </form>
    </div>
</x-admin-layout>
