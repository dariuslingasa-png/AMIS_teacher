@php
    $paymentUrl = \App\Support\EnrollmentStorage::url($payment->receipt_url);
    $paymentIsPdf = $payment->receipt_url && strtolower(pathinfo($payment->receipt_url, PATHINFO_EXTENSION)) === 'pdf';
    $familyNo = $applicant?->family_application_id ?: $applicant?->id;
    $invoiceNo = 'INV-ENR-'.str_pad((string) $payment->id, 5, '0', STR_PAD_LEFT);
    $invoiceDate = $payment->paid_at ?? $payment->created_at;
    $invoiceChildAmount = 4000.00;
    $invoiceChildren = $familyChildren->isNotEmpty() ? $familyChildren : collect([$applicant])->filter();
    $invoiceTotal = $invoiceChildren->count() * $invoiceChildAmount;
    $canReviewPayments = auth()->user()?->canReviewEnrollmentPayments() ?? false;
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
        <div x-data="{
             preview: false,
             src: '',
             label: '',
             pdf: false,
             zoom: 1,
             panning: false,
             panEl: null,
             panX: 0,
             panY: 0,
             panLeft: 0,
             panTop: 0,
             openPreview(url, title, isPdf) {
                 this.preview = true;
                 this.src = url;
                 this.label = title;
                 this.pdf = isPdf;
                 this.zoom = 1;
             },
             closePreview() {
                 this.preview = false;
                 this.zoom = 1;
                 this.stopPan();
             },
             zoomIn() {
                 this.zoom = Math.min(3, Number((this.zoom + 0.1).toFixed(2)));
             },
             zoomOut() {
                 this.zoom = Math.max(0.1, Number((this.zoom - 0.1).toFixed(2)));
             },
             resetZoom() {
                 this.zoom = 1;
             },
             startPan(event) {
                 if (this.pdf) return;
                 const point = event.touches ? event.touches[0] : event;
                 this.panning = true;
                 this.panEl = event.currentTarget;
                 this.panX = point.pageX;
                 this.panY = point.pageY;
                 this.panLeft = this.panEl.scrollLeft;
                 this.panTop = this.panEl.scrollTop;
                 this.panEl.classList.add('cursor-grabbing');
             },
             movePan(event) {
                 if (!this.panning || !this.panEl) return;
                 event.preventDefault();
                 const point = event.touches ? event.touches[0] : event;
                 this.panEl.scrollLeft = this.panLeft - (point.pageX - this.panX);
                 this.panEl.scrollTop = this.panTop - (point.pageY - this.panY);
             },
             stopPan() {
                 if (this.panEl) this.panEl.classList.remove('cursor-grabbing');
                 this.panning = false;
                 this.panEl = null;
             },
             async downloadPdf() {
                 if (!this.src) return;
                 const url = this.src;
                 const filename = (this.label || 'document').replace(/[^a-zA-Z0-9]/g, '_') + '.pdf';
                 if (this.pdf) {
                     const link = document.createElement('a');
                     link.href = url;
                     link.download = filename;
                     document.body.appendChild(link);
                     link.click();
                     document.body.removeChild(link);
                     return;
                 }
                 try {
                     const btn = document.getElementById('download-pdf-btn');
                     const originalText = btn.innerHTML;
                     btn.innerHTML = '<i data-lucide=\'loader-2\' class=\'h-3.5 w-3.5 animate-spin\'></i> Converting...';
                     if (window.lucide) window.lucide.createIcons();
                     const { jsPDF } = window.jspdf;
                     const img = new Image();
                     img.crossOrigin = 'Anonymous';
                     img.src = url;
                     img.onload = () => {
                         const pdf = new jsPDF({
                             orientation: img.width > img.height ? 'landscape' : 'portrait',
                             unit: 'px',
                             format: [img.width, img.height]
                         });
                         pdf.addImage(img, 'JPEG', 0, 0, img.width, img.height);
                         pdf.save(filename);
                         btn.innerHTML = originalText;
                         if (window.lucide) window.lucide.createIcons();
                     };
                     img.onerror = () => {
                         const link = document.createElement('a');
                         link.href = url;
                         link.download = this.label || 'image';
                         document.body.appendChild(link);
                         link.click();
                         document.body.removeChild(link);
                         btn.innerHTML = originalText;
                         if (window.lucide) window.lucide.createIcons();
                     };
                 } catch (e) {
                     console.error(e);
                     window.open(url, '_blank');
                 }
             }
         }"
         x-effect="document.body.classList.toggle('overflow-hidden', preview)"
         @keydown.escape.window="closePreview()"
         @mouseup.window="stopPan()"
         @touchend.window="stopPan()"
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
                                        <td class="py-4 pr-4 font-black uppercase text-slate-950">
                                            <div>{{ $index + 1 }}. {{ $child->full_name ?: 'Applicant' }}</div>
                                            <div class="text-[10px] font-semibold text-slate-400 mt-0.5 tracking-wider">APPLICANT #{{ str_pad((string) $child->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </td>
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
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-500">Payment Proofs</h3>
                        <div class="mt-3 grid gap-4">
                            @forelse ($invoiceChildren as $child)
                                @php
                                    $childPayment = $child->payment;
                                    $childPaymentUrl = \App\Support\EnrollmentStorage::url($childPayment?->receipt_url);
                                    $childPaymentIsPdf = $childPayment?->receipt_url && strtolower(pathinfo($childPayment->receipt_url, PATHINFO_EXTENSION)) === 'pdf';
                                    $childStatus = strtolower((string) ($childPayment?->status ?? 'missing'));
                                    $childStatusColor = $childStatus === 'verified' ? 'green' : ($childStatus === 'rejected' ? 'red' : 'yellow');
                                @endphp
                                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                    <div class="flex flex-col gap-3 border-b border-slate-200 bg-white p-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div>
                                            <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Applicant #{{ str_pad((string) $child->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            <div class="mt-1 text-base font-black uppercase text-slate-950">{{ $child->full_name ?: 'Applicant' }}</div>
                                            <div class="mt-1 text-xs font-black uppercase tracking-wide text-slate-500">
                                                {{ $child->grade_level ?? 'GRADE PENDING' }} | {{ $learningModeLabel($child->learning_mode ?? null) }}
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <x-badge color="{{ $childStatusColor }}">{{ Str::upper($childStatus === 'missing' ? 'pending' : $childStatus) }}</x-badge>
                                        </div>
                                    </div>
                                    <div class="grid gap-4 p-4 lg:grid-cols-[260px_1fr] lg:items-center">
                                        @if ($childPaymentUrl)
                                            <button type="button" class="upload-preview h-44 rounded-xl border border-slate-200 bg-white text-left" @click="openPreview('{{ $childPaymentUrl }}', '{{ addslashes($child->full_name ?: 'Payment Proof') }}', {{ $childPaymentIsPdf ? 'true' : 'false' }})">
                                                @if ($childPaymentIsPdf)
                                                    <span class="upload-pdf"><i data-lucide="file-text" class="h-9 w-9"></i>PDF Receipt</span>
                                                @else
                                                    <img src="{{ $childPaymentUrl }}" alt="Payment Proof">
                                                @endif
                                            </button>
                                        @else
                                            <div class="empty-state h-44 rounded-xl border border-slate-200 bg-white">
                                                <i data-lucide="receipt-text" class="h-8 w-8"></i>
                                                <p>No payment proof uploaded.</p>
                                            </div>
                                        @endif
                                        <dl class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Reference No.</dt>
                                                <dd class="mt-1 text-sm font-black uppercase text-slate-950">{{ $childPayment?->reference_no ?: 'Not provided' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Payment Method</dt>
                                                <dd class="mt-1 text-sm font-black uppercase text-slate-950">{{ $childPayment?->method_label ?? $childPayment?->method ?? '-' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Upload Date</dt>
                                                <dd class="mt-1 text-sm font-black text-slate-950">{{ $childPayment?->paid_at?->format('M d, Y h:i A') ?? 'Not provided' }}</dd>
                                            </div>
                                            <div>
                                                <dt class="text-[10px] font-black uppercase tracking-widest text-slate-400">Amount</dt>
                                                <dd class="mt-1 text-sm font-black text-slate-950">PHP {{ number_format((float) ($childPayment?->amount ?? 0), 2) }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                    @if ($childPayment && $canReviewPayments && $childPayment->status === 'pending')
                                        <div class="border-t border-slate-200 bg-white p-4 space-y-4">
                                            <form method="POST" action="{{ route('admin.payments.verify', $childPayment) }}" class="space-y-3">
                                                @csrf
                                                @method('PATCH')
                                                <button class="w-full rounded-2xl bg-emerald-600 px-5 py-3 text-xs font-black uppercase tracking-[0.18em] text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700">
                                                    APPROVE PAYMENT
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.payments.reject', $childPayment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="remarks" value="Payment proof rejected by Sir Cabel.">
                                                <button class="w-full rounded-2xl border border-rose-200 bg-rose-50 px-5 py-3 text-xs font-black uppercase tracking-[0.18em] text-rose-700 shadow-lg shadow-rose-100 transition hover:border-rose-600 hover:bg-rose-600 hover:text-white">
                                                    REJECT PAYMENT
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </article>
                            @empty
                                <div class="empty-state">
                                    <i data-lucide="receipt-text" class="h-8 w-8"></i>
                                    <p>No payment proof uploaded.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <div x-show="preview" x-cloak x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
            <div class="relative max-h-[92vh] w-full max-w-5xl overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <h2 class="font-black text-slate-950" x-text="label"></h2>
                    <div class="ml-auto flex items-center gap-2">
                        <div class="flex items-center gap-2" x-show="!pdf">
                            <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-black text-slate-700 shadow-sm transition hover:bg-slate-100" @click="zoomOut()">-</button>
                            <span class="min-w-14 rounded-full bg-slate-100 px-3 py-1 text-center text-xs font-black text-slate-700" x-text="Math.round(zoom * 100) + '%'"></span>
                            <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-sm font-black text-slate-700 shadow-sm transition hover:bg-slate-100" @click="zoomIn()">+</button>
                            <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-slate-500 shadow-sm transition hover:bg-slate-100" @click="resetZoom()">Reset</button>
                        </div>
                        <button id="download-pdf-btn" type="button" class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-black uppercase tracking-[0.14em] text-emerald-700 shadow-sm transition hover:bg-emerald-100 flex items-center gap-1 cursor-pointer" @click="downloadPdf()">
                            <i data-lucide="download" class="h-3.5 w-3.5"></i> Download PDF
                        </button>
                        <button type="button" class="rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-slate-200" @click="closePreview()">
                            <i data-lucide="x" class="h-5 w-5"></i>
                        </button>
                    </div>
                </div>
                <div class="max-h-[78vh] cursor-grab select-none overflow-auto bg-slate-50 p-4"
                     @mousedown="startPan($event)"
                     @mousemove="movePan($event)"
                     @mouseleave="stopPan()"
                     @touchstart.passive="startPan($event)"
                     @touchmove="movePan($event)">
                    <template x-if="pdf">
                        <iframe :src="src" class="h-[75vh] w-full rounded-2xl bg-white"></iframe>
                    </template>
                    <template x-if="!pdf">
                        <img :src="src" :alt="label" class="mx-auto rounded-2xl object-contain transition-all duration-150" :style="'max-width: none; width: ' + (zoom * 100) + '%; height: auto;'">
                    </template>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</x-admin-layout>
