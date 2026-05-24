@php
    $paymentUrl = $payment->receipt_url ? asset('storage/'.$payment->receipt_url) : null;
    $paymentIsPdf = $payment->receipt_url && strtolower(pathinfo($payment->receipt_url, PATHINFO_EXTENSION)) === 'pdf';
    $familyNo = $applicant?->family_application_id ?: $applicant?->id;
    $invoiceNo = 'INV-ENR-'.str_pad((string) $payment->id, 5, '0', STR_PAD_LEFT);
    $invoiceDate = $payment->paid_at ?? $payment->created_at;
    $invoiceChildAmount = 4000.00;
    $invoiceChildren = $familyChildren->isNotEmpty() ? $familyChildren : collect([$applicant])->filter();
    $invoiceTotal = $invoiceChildren->count() * $invoiceChildAmount;
    $learningModeLabel = function ($mode) {
        $normalized = strtolower(trim((string) $mode));

        return match ($normalized) {
            'face_to_face', 'face-to-face', 'f2f' => 'F2F',
            'flexible_1st_shift', 'flexible learning - 1st shift', 'flexible 1st shift', '1st shift' => 'FLEXIBLE LEARNING - 1ST SHIFT',
            'flexible_2nd_shift', 'flexible learning - 2nd shift', 'flexible 2nd shift', '2nd shift' => 'FLEXIBLE LEARNING - 2ND SHIFT',
            default => $mode ? strtoupper((string) $mode) : 'LEARNING MODE PENDING',
        };
    };
@endphp

<x-admin-layout title="Payment Review">
    <div x-data="{ preview: false, src: '', label: '', pdf: false, openPreview(url, title, isPdf) { this.preview = true; this.src = url; this.label = title; this.pdf = isPdf; } }"
         @keydown.escape.window="preview = false"
         class="space-y-6">
        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-amber-900/10" style="background: linear-gradient(135deg, #111827 0%, #92400e 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">Enrollment Payment Review</span>
                    <h1 class="mt-4 text-3xl font-black tracking-tight">{{ $familyLabel }}</h1>
                    <p class="mt-2 text-sm font-semibold text-amber-50/90">
                        {{ $familyNo ? 'FAMILY #'.str_pad($familyNo, 4, '0', STR_PAD_LEFT) : 'Single application' }}
                        @if ($familyChildren->count() > 1)
                            &middot; {{ $familyChildren->count() }} children in this family batch
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center gap-2 rounded-2xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-black text-white transition hover:bg-white/15">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                        Back to Payment Review
                    </a>
                    <a href="{{ route('admin.soa.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                        <i data-lucide="scroll-text" class="h-4 w-4"></i>
                        SOA
                    </a>
                </div>
            </div>
        </section>

        <x-card title="Invoice" subtitle="Invoice, payment details, proof, and finance review">
            <div class="mx-auto max-w-4xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-950 px-6 py-7 text-center text-white">
                    <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS Logo" class="mx-auto h-16 w-16 rounded-full bg-white p-1">
                    <div class="mt-3 text-xl font-black uppercase tracking-wide">AMIS Admin Portal</div>
                    <div class="text-xs font-black uppercase tracking-[0.25em] text-emerald-200">School Finance</div>
                </div>

                <div class="grid gap-4 border-b border-slate-200 bg-slate-50 px-6 py-5 md:grid-cols-3">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Invoice No.</div>
                        <div class="mt-1 text-sm font-black text-slate-950">{{ $invoiceNo }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Date</div>
                        <div class="mt-1 text-sm font-black text-slate-950">{{ optional($invoiceDate)->format('M d, Y h:i A') ?? 'Not provided' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Bill To</div>
                        <div class="mt-1 text-sm font-black text-slate-950">{{ $familyLabel }}</div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[720px] text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">
                                    <th class="py-3 pr-4">Child</th>
                                    <th class="px-4 py-3">Grade</th>
                                    <th class="px-4 py-3">Learning Mode</th>
                                    <th class="py-3 pl-4 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($invoiceChildren as $index => $child)
                                    <tr>
                                        <td class="py-4 pr-4 font-black uppercase text-slate-950">{{ $index + 1 }}. {{ $child->full_name ?: 'Applicant' }}</td>
                                        <td class="px-4 py-4 font-black uppercase text-slate-700">{{ $child->grade_level ?? 'GRADE PENDING' }}</td>
                                        <td class="px-4 py-4 font-black uppercase text-slate-700">{{ $learningModeLabel($child->learning_mode ?? null) }}</td>
                                        <td class="py-4 pl-4 text-right text-base font-black text-slate-950">PHP {{ number_format($invoiceChildAmount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-sm font-bold text-slate-400">No child record found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex items-center justify-between border-y border-slate-200 bg-slate-50 px-4 py-4">
                        <span class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Total Amount</span>
                        <span class="text-2xl font-black text-emerald-700">PHP {{ number_format($invoiceTotal, 2) }}</span>
                    </div>

                    <div class="mt-7">
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Payment Details</h3>
                        <dl class="mt-3 grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Reference No.</dt>
                                <dd class="mt-1 text-sm font-black uppercase text-slate-950">{{ $payment->reference_no ?: 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Payment Method</dt>
                                <dd class="mt-1 text-sm font-black uppercase text-slate-950">{{ $payment->method_label ?? $payment->method ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Upload Date</dt>
                                <dd class="mt-1 text-sm font-black text-slate-950">{{ $payment->paid_at?->format('M d, Y h:i A') ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Amount</dt>
                                <dd class="mt-1 text-sm font-black text-slate-950">PHP {{ number_format((float) ($payment->amount ?? 0), 2) }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-7">
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Payment Proof</h3>
                        @if ($paymentUrl)
                            <button type="button" class="mt-3 block w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 text-left transition hover:border-emerald-200 hover:bg-emerald-50/30" @click="openPreview('{{ $paymentUrl }}', 'Payment Proof', {{ $paymentIsPdf ? 'true' : 'false' }})">
                                <div class="grid gap-4 p-4 md:grid-cols-[260px_1fr] md:items-center">
                                    <div class="upload-preview h-44 rounded-xl border border-slate-200 bg-white">
                                        @if ($paymentIsPdf)
                                            <span class="upload-pdf"><i data-lucide="file-text" class="h-9 w-9"></i>PDF Receipt</span>
                                        @else
                                            <img src="{{ $paymentUrl }}" alt="Payment Proof">
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-base font-black text-slate-950">Screenshot preview attached</div>
                                        <div class="mt-1 text-sm font-semibold text-slate-500">Click to view full image.</div>
                                    </div>
                                </div>
                            </button>
                        @else
                            <div class="mt-3 empty-state">
                                <i data-lucide="receipt-text" class="h-8 w-8"></i>
                                <p>No payment proof uploaded.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>

        <div x-show="preview" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
            <div class="relative max-h-[92vh] w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h2 class="font-black text-slate-950" x-text="label"></h2>
                    <button type="button" class="rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-slate-200" @click="preview = false">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>
                <div class="max-h-[78vh] overflow-auto bg-slate-50 p-4">
                    <template x-if="pdf">
                        <iframe :src="src" class="h-[75vh] w-full rounded-2xl bg-white"></iframe>
                    </template>
                    <template x-if="!pdf">
                        <img :src="src" :alt="label" class="mx-auto max-h-[75vh] rounded-2xl object-contain">
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
