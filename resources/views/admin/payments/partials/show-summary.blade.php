        <section class="overflow-hidden rounded-3xl p-6 text-white shadow-xl shadow-amber-900/10" style="background: linear-gradient(135deg, #111827 0%, #92400e 48%, #065f46 100%);">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-black uppercase tracking-[0.22em] text-amber-50">Enrollment Payment Approval</span>
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
                        Back to Payment Approval
                    </a>
                    <a href="{{ route('admin.soa.index') }}" class="inline-flex items-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-black text-amber-700 shadow-lg shadow-amber-900/20 transition hover:bg-amber-50">
                        <i data-lucide="scroll-text" class="h-4 w-4"></i>
                        SOA
                    </a>
                </div>
            </div>
        </section>

        @php
            $hasVerifiedPayment = $allPayments->filter(fn($p) => strtolower($p->status) === 'verified')->isNotEmpty();
        @endphp

        @unless ($hasVerifiedPayment)
            <div class="mt-6 overflow-hidden rounded-3xl bg-amber-50 border border-amber-200 p-6 shadow-md shadow-amber-900/5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="text-left">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center rounded-full bg-amber-500 p-1.5 text-white shadow-md shadow-amber-500/20">
                                <i data-lucide="clock" class="h-5 w-5"></i>
                            </span>
                            <h3 class="text-lg font-black text-amber-900 uppercase tracking-wider">Payment Proof Pending Verification</h3>
                        </div>
                        <p class="text-sm font-semibold text-amber-700 mt-2">
                            The enrollment downpayment proof must be verified below before proceeding to student onboarding.
                        </p>
                        @if ($potentialExcess > 0)
                            <div class="mt-3 inline-flex items-center gap-2 rounded-2xl bg-amber-100 border border-amber-250 px-4 py-2 text-amber-950 shadow-sm">
                                <i data-lucide="piggy-bank" class="h-5 w-5 text-amber-600 animate-pulse"></i>
                                <span class="text-xs font-black uppercase tracking-wider">
                                    Expected Advance Payment Credit: PHP {{ number_format($potentialExcess, 2) }} (will be generated upon verification)
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach ($invoiceChildren as $child)
                            <a href="{{ route('admin.applicants.show', $child) }}" class="inline-flex items-center gap-2 rounded-2xl bg-white border border-amber-300 px-4 py-2.5 text-xs font-black uppercase tracking-wider text-amber-800 shadow-sm transition hover:bg-amber-50">
                                <i data-lucide="user" class="h-4 w-4"></i>
                                View {{ $child->first_name ?: $child->full_name }} Details
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endunless
