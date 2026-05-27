<x-admin-layout
    title="Enrollment Applications"
    :breadcrumbs="[
        ['label' => 'Applications', 'href' => route('admin.applications.enrollment')],
        ['label' => 'Enrollment', 'href' => null],
    ]"
>
    @php
        $currentSort = request('sort', 'number');
        $currentDir = request('dir', 'desc');
        $inputClass = 'h-11 rounded-lg border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100';
        $childStatusColor = ['approved' => 'green', 'rejected' => 'red', 'under_review' => 'blue', 'ready_for_submission' => 'yellow', 'pending' => 'yellow', 'submitted' => 'yellow'];
        $childPaymentLabel = fn ($child) => match ($child->payment->status ?? null) {
            'verified' => 'Paid',
            'pending' => 'Pending',
            default => 'No Payment',
        };
        $childPaymentColor = fn ($label) => ['Paid' => 'green', 'Pending' => 'yellow', 'No Payment' => 'gray'][$label] ?? 'gray';
        $typeLabel = fn ($type) => match (strtolower((string) $type)) {
            'old' => 'OLD',
            'returning', 'returnee', 'existing' => 'RETURNING',
            'transferee', 'transfer' => 'TRANSFEREE',
            'new' => 'NEW',
            default => 'NOT SET',
        };
        $typeClass = fn ($label) => match ($label) {
            'OLD', 'RETURNING' => 'bg-green-100 text-green-800',
            'TRANSFEREE' => 'bg-amber-100 text-amber-800',
            'NEW' => 'bg-blue-100 text-blue-800',
            default => 'bg-slate-100 text-slate-600',
        };
        $familyAccents = [
            ['wrap' => 'border-l-green-600 border-green-100 bg-green-50', 'icon' => 'bg-green-100 text-green-700', 'text' => 'text-green-800', 'badge' => 'bg-white text-green-700 ring-1 ring-green-200'],
            ['wrap' => 'border-l-blue-600 border-blue-100 bg-blue-50', 'icon' => 'bg-blue-100 text-blue-700', 'text' => 'text-blue-800', 'badge' => 'bg-white text-blue-700 ring-1 ring-blue-200'],
            ['wrap' => 'border-l-amber-500 border-amber-100 bg-amber-50', 'icon' => 'bg-amber-100 text-amber-700', 'text' => 'text-amber-800', 'badge' => 'bg-white text-amber-700 ring-1 ring-amber-200'],
            ['wrap' => 'border-l-violet-600 border-violet-100 bg-violet-50', 'icon' => 'bg-violet-100 text-violet-700', 'text' => 'text-violet-800', 'badge' => 'bg-white text-violet-700 ring-1 ring-violet-200'],
            ['wrap' => 'border-l-rose-600 border-rose-100 bg-rose-50', 'icon' => 'bg-rose-100 text-rose-700', 'text' => 'text-rose-800', 'badge' => 'bg-white text-rose-700 ring-1 ring-rose-200'],
        ];
    @endphp

    <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-slate-950">Applications</h1>
                    <p class="mt-1 text-sm text-slate-500">Family enrollment registry grouped by child applicants</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700" data-total-count="{{ $families->total() }}">
                    {{ number_format($families->total()) }} families
                </span>
            </div>
        </div>

        <div class="px-6 py-5">
            <!-- Applications Metrics Tracking Panel -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Approved Card -->
                <div class="group relative overflow-hidden rounded-xl border border-emerald-100 bg-emerald-50/30 p-5 transition-all duration-300 hover:-translate-y-0.5 hover:bg-emerald-50/50 hover:shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-600">Approved Applications</span>
                            <h3 class="mt-2 text-3xl font-black tracking-tight text-emerald-950">{{ number_format($approvedCount) }}</h3>
                        </div>
                        <div class="rounded-lg bg-emerald-100/80 p-3 text-emerald-700 transition-transform duration-300 group-hover:scale-110">
                            <i data-lucide="check-circle-2" class="h-6 w-6"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-emerald-500/80 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </div>

                <!-- Review Queue Card -->
                <div class="group relative overflow-hidden rounded-xl border border-blue-100 bg-blue-50/30 p-5 transition-all duration-300 hover:-translate-y-0.5 hover:bg-blue-50/50 hover:shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-blue-600">Review Queue</span>
                            <h3 class="mt-2 text-3xl font-black tracking-tight text-blue-950">{{ number_format($reviewQueueCount) }}</h3>
                        </div>
                        <div class="rounded-lg bg-blue-100/80 p-3 text-blue-700 transition-transform duration-300 group-hover:scale-110">
                            <i data-lucide="clock-4" class="h-6 w-6"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-blue-500/80 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </div>

                <!-- Rejected Card -->
                <div class="group relative overflow-hidden rounded-xl border border-rose-100 bg-rose-50/30 p-5 transition-all duration-300 hover:-translate-y-0.5 hover:bg-rose-50/50 hover:shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-extrabold uppercase tracking-wider text-rose-600">Rejected Applications</span>
                            <h3 class="mt-2 text-3xl font-black tracking-tight text-rose-950">{{ number_format($rejectedCount) }}</h3>
                        </div>
                        <div class="rounded-lg bg-rose-100/80 p-3 text-rose-700 transition-transform duration-300 group-hover:scale-110">
                            <i data-lucide="x-circle" class="h-6 w-6"></i>
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 w-full bg-rose-500/80 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                </div>
            </div>

            <form method="GET" class="mb-5 grid grid-cols-12 gap-3">
                <label class="relative col-span-4">
                    <i data-lucide="search" class="pointer-events-none absolute left-3 top-3.5 h-4 w-4 text-slate-400"></i>
                    <input name="search" value="{{ request('search') }}" placeholder="Search family, child, or email" class="{{ $inputClass }} w-full pl-9">
                </label>
                <select name="status" class="{{ $inputClass }} col-span-2 w-full" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    @foreach ($statusLabels ?? [] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="grade" class="{{ $inputClass }} col-span-2 w-full" onchange="this.form.submit()">
                    <option value="">All grades</option>
                    @foreach ($gradeLevels ?? [] as $grade)
                        <option value="{{ $grade }}" @selected(request('grade') === $grade)>{{ $grade }}</option>
                    @endforeach
                </select>
                <label class="relative col-span-2">
                    <select name="sort" class="{{ $inputClass }} w-full" onchange="this.form.submit()">
                        <option value="number" @selected($currentSort === 'number')>Family no.</option>
                        <option value="parent" @selected($currentSort === 'parent')>Family name</option>
                        <option value="children" @selected($currentSort === 'children')>Children count</option>
                        <option value="progress" @selected($currentSort === 'progress')>Approved progress</option>
                        <option value="payment" @selected($currentSort === 'payment')>Payment status</option>
                        <option value="status" @selected($currentSort === 'status')>Overall status</option>
                    </select>
                </label>
                <select name="dir" class="{{ $inputClass }} col-span-1 w-full px-3" onchange="this.form.submit()">
                    <option value="desc" @selected($currentDir === 'desc')>Desc</option>
                    <option value="asc" @selected($currentDir === 'asc')>Asc</option>
                </select>
                <button class="col-span-1 inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-emerald-700 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">
                    <i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
                    Apply
                </button>
            </form>

            <div class="overflow-hidden rounded-md border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="sticky top-0 z-10 bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="w-36 px-5 py-4 font-bold">Child</th>
                            <th class="px-5 py-4 font-bold">Student Name</th>
                            <th class="w-28 px-5 py-4 font-bold">Type</th>
                            <th class="w-36 px-5 py-4 font-bold">Grade</th>
                            <th class="w-44 px-5 py-4 font-bold">Enrollment Status</th>
                            <th class="w-40 px-5 py-4 font-bold">Payment Status</th>
                            <th class="w-36 px-5 py-4 text-right font-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($families as $family)
                            @php
                                $representative = $family['representative'];
                                [$familyLastName, $familyFirstName] = array_pad(explode(', ', \Illuminate\Support\Str::upper($family['family_label']), 2), 2, 'GUARDIAN');
                                $familyHeader = 'FAMILY OF '.$familyLastName.', '.$familyFirstName;
                                $initials = collect([$familyLastName, $familyFirstName])->filter()->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->join('');
                                $accent = $familyAccents[$family['family_no'] % count($familyAccents)];
                                $maxDiscount = $family['children']->max(fn ($child) => (float) ($child->discount_percentage ?? 0));
                                $discountLabel = $maxDiscount > 0 ? 'SIBLINGS DISCOUNT '.rtrim(rtrim(number_format($maxDiscount, 2), '0'), '.').'%' : 'SIBLINGS DISCOUNT';
                            @endphp
                            <tr>
                                <td colspan="7" class="px-0 py-0">
                                    <div class="border-l-4 px-5 py-3 {{ $accent['wrap'] }}">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-md text-xs font-extrabold {{ $accent['icon'] }}">{{ $initials ?: 'FA' }}</span>
                                                <div>
                                                    <h3 class="text-sm font-extrabold tracking-wide {{ $accent['text'] }}">{{ $familyHeader }}</h3>
                                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-slate-500">FAMILY APPLICATION #{{ str_pad($family['family_no'], 4, '0', STR_PAD_LEFT) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @if ($family['children_count'] > 1)
                                                    <span class="rounded-md px-2.5 py-1 text-xs font-extrabold {{ $accent['badge'] }}">{{ $family['approved_count'] }}/{{ $family['children_count'] }} CHILDREN APPROVED</span>
                                                    <span class="rounded-md px-2.5 py-1 text-xs font-extrabold {{ $accent['badge'] }}">{{ $discountLabel }}</span>
                                                @else
                                                    <span class="rounded-md px-2.5 py-1 text-xs font-extrabold {{ $accent['badge'] }}">{{ $family['approved_count'] }}/1 CHILD APPROVED</span>
                                                @endif
                                                <span class="rounded-md px-2.5 py-1 text-xs font-extrabold {{ $accent['badge'] }}">{{ $family['children_count'] }} {{ \Illuminate\Support\Str::plural('CHILD', $family['children_count']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($family['children'] as $index => $child)
                                @php
                                    $childName = \Illuminate\Support\Str::upper(trim(($child->first_name ?? '').' '.($child->middle_name ?? '').' '.($child->last_name ?? '')) ?: 'Student');
                                    $childInitials = collect(explode(' ', $childName))->filter()->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->join('');
                                    $photoUrl = \App\Support\EnrollmentStorage::url($child->photo_2x2_url);
                                    $statusLabel = $statusLabels[$child->status] ?? \Illuminate\Support\Str::headline($child->status ?? 'under_review');
                                    $paymentLabel = $childPaymentLabel($child);
                                    $studentType = $typeLabel($child->student_type);
                                @endphp
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <span class="font-extrabold uppercase tracking-wide {{ $accent['text'] }}">Child {{ $index + 1 }}</span>
                                    </td>
                                    <td class="px-5 py-4 align-middle">
                                        <div class="flex items-center gap-3">
                                            <x-smart-image
                                                :src="$photoUrl"
                                                :alt="$childName"
                                                :fallback-initials="$childInitials ?: 'ST'"
                                                size="40"
                                                rounded="rounded-lg"
                                                :eager="false"
                                            />
                                            <div>
                                                <div class="font-extrabold text-slate-950">{{ $childName }}</div>
                                                <div class="mt-0.5 text-xs font-medium text-slate-500">Applicant #{{ str_pad($child->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="rounded-md px-2.5 py-1 text-xs font-extrabold {{ $typeClass($studentType) }}">{{ $studentType }}</span>
                                    </td>
                                    <td class="px-5 py-4 font-bold text-slate-700">{{ $child->grade_level ?? 'Not provided' }}</td>
                                    <td class="px-5 py-4"><x-badge :color="$childStatusColor[$child->status] ?? 'blue'">{{ $statusLabel }}</x-badge></td>
                                    <td class="px-5 py-4"><x-badge :color="$childPaymentColor($paymentLabel)">{{ $paymentLabel }}</x-badge></td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('admin.applicants.show', $child) }}" title="View child application" class="inline-flex h-9 items-center gap-2 rounded-md border border-emerald-100 bg-white px-3 text-xs font-bold text-emerald-700 transition hover:border-emerald-200 hover:bg-emerald-50">
                                            <i data-lucide="eye" class="h-4 w-4"></i>
                                            View Child
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr><td colspan="7" class="px-5 py-12 text-center text-sm text-slate-500">No family applications found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex items-center justify-between gap-4">
                <p class="text-sm font-medium text-slate-500">
                    Showing {{ $families->firstItem() ?? 0 }}-{{ $families->lastItem() ?? 0 }} of {{ $families->total() }} family applications
                </p>
                <div>{{ $families->links() }}</div>
            </div>
        </div>
    </section>

    <!-- New applicant polling notification -->
    <div id="new-applicant-banner" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden">
        <div class="flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-lg">
            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
            <span class="text-sm font-semibold" id="new-applicant-text">New applications received</span>
            <button onclick="location.reload()" class="ml-2 bg-white/20 hover:bg-white/30 px-3 py-1 rounded-lg text-xs font-bold transition">Refresh</button>
            <button onclick="document.getElementById('new-applicant-banner').classList.add('hidden')" class="ml-1 text-white/70 hover:text-white text-lg leading-none">&times;</button>
        </div>
    </div>

    <script>
        (function() {
            let lastCount = {{ $families->total() ?? 0 }};
            setInterval(async () => {
                try {
                    const res = await fetch(window.location.href, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const text = await res.text();
                    const match = text.match(/data-total-count="(\d+)"/);
                    if (match) {
                        const newCount = parseInt(match[1]);
                        if (newCount > lastCount) {
                            const diff = newCount - lastCount;
                            document.getElementById('new-applicant-text').textContent = diff + ' new application' + (diff > 1 ? 's' : '') + ' received';
                            document.getElementById('new-applicant-banner').classList.remove('hidden');
                            lastCount = newCount;
                        }
                    }
                } catch(e) {}
            }, 30000);
        })();
    </script>
</x-admin-layout>
