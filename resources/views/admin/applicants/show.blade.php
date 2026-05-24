@php
    use Illuminate\Support\Str;

    $familyNo = $applicant->family_application_id ?: min($applicant->id, isset($siblings) ? $siblings->min('id') ?? $applicant->id : $applicant->id);
    $accentClasses = ['accent-green', 'accent-blue', 'accent-amber', 'accent-violet', 'accent-rose'];
    $accentClass = $accentClasses[$familyNo % 5];

    $name = trim(($applicant->first_name ?? '').' '.($applicant->middle_name ?? '').' '.($applicant->last_name ?? ''));
    $displayName = $name ? Str::upper($name) : 'APPLICANT';
    $breadcrumbName = $displayName;
    $photoUrl = $applicant->photo_2x2_url ? asset('storage/'.$applicant->photo_2x2_url) : null;
    $paymentUrl = $payment?->receipt_url ? asset('storage/'.$payment->receipt_url) : null;
    $paymentIsPdf = $payment?->receipt_url && strtolower(pathinfo($payment->receipt_url, PATHINFO_EXTENSION)) === 'pdf';
    $canReviewPayments = auth()->user()?->canReviewEnrollmentPayments() ?? false;
    $canReviewApplications = auth()->user()?->canReviewEnrollmentApplications() ?? false;
    $paymentReadinessLabel = match (true) {
        !$payment?->receipt_url => 'Waiting for Verification (Sir Cabel)',
        $payment->status === 'verified' && $applicant->status === 'approved' => 'Approved by Sir Cabel',
        $payment->status === 'verified' => 'Verified by Sir Cabel',
        $payment->status === 'rejected' => 'Rejected by Sir Cabel ❌',
        default => 'Waiting for Verification (Sir Cabel)',
    };
    $approvalReadinessLabel = match (true) {
        $applicant->status === 'approved' && $allDocsOk && $paymentOk => 'Enrollment Approved ✅',
        $payment?->status === 'rejected' => 'Enrollment On Hold / Payment Rejected',
        $canApprove => 'Ready for Approval / Under Review',
        default => 'Not Ready for Enrollment / Pending',
    };
    $currentStatus = $applicant->status ?? 'under_review';
    $currentStatusLabel = $statusLabels[$currentStatus] ?? Str::headline($currentStatus);
    $fatherName = trim(($applicant->father_first_name ?? '').' '.($applicant->father_middle_name ?? '').' '.($applicant->father_last_name ?? ''));
    $motherName = trim(($applicant->mother_first_name ?? '').' '.($applicant->mother_middle_name ?? '').' '.($applicant->mother_last_name ?? ''));
    $emergencyContact = trim(($applicant->emergency_name ?? '').' / '.($applicant->emergency_phone ?? ''), ' /');
    $hasMedicalConcern = (bool) $applicant->medical_has_concern;
    $studentSections = [
        ['title' => 'Academic Details', 'icon' => 'graduation-cap', 'fields' => [
            ['Student Type', $applicant->student_type], ['Grade Level', $applicant->grade_level],
            ['School Year', $applicant->school_year], ['Learning Mode', $applicant->learning_mode],
            ['Timezone', $applicant->timezone], ['LRN', $applicant->lrn],
            ['AMIS Student ID', $applicant->amis_student_id],
        ]],
        ['title' => 'Personal Details', 'icon' => 'id-card', 'fields' => [
            ['Gender', $applicant->gender], ['Date of Birth', optional($applicant->date_of_birth)->format('M d, Y')],
            ['Place of Birth', $applicant->place_of_birth], ['Religion', $applicant->religion],
            ['Ethnicity', $applicant->ethnicity],
        ]],
        ['title' => 'Student Contact', 'icon' => 'mail', 'fields' => [['Email', $applicant->user->email ?? $applicant->email], ['Mobile', $studentMobile]]],
    ];
    $addressSections = [
        ['title' => 'Student Address', 'icon' => 'map', 'fields' => [['Street Address', $applicant->street_address], ['City', $applicant->city], ['State / Province', $applicant->state_province], ['Postal Code', $applicant->postal_code], ['Country', $applicant->country], ['Full Address', $studentAddress]]],
    ];
    $guardianSections = [
        ['title' => "Father's Details", 'icon' => 'user', 'fields' => [["Father's Full Name", $fatherName], ['Occupation', $applicant->father_occupation]]],
        ['title' => "Mother's Details", 'icon' => 'user-round', 'fields' => [["Mother's Full Name", $motherName], ['Occupation', $applicant->mother_occupation]]],
        ['title' => 'Parent Contact', 'icon' => 'phone', 'fields' => [['Parent Email', $applicant->parent_email], ['Parent Mobile', $parentMobile], ['Referral Source', $applicant->referral_source]]],
        ['title' => 'Home Address', 'icon' => 'map-pin', 'fields' => [['Full Home Address', $homeAddress], ['City', $applicant->home_city], ['State / Province', $applicant->home_state_province], ['Postal Code', $applicant->home_postal_code]]],
    ];
    $medicalSections = [
        ['title' => 'Emergency Contact', 'icon' => 'shield-alert', 'fields' => [
            ['Contact Person', $emergencyContact], ['Relationship', $applicant->emergency_relationship],
            ['Family Physician', $applicant->family_physician], ['Physician Phone', $applicant->physician_phone],
        ]],
    ];
    if ($hasMedicalConcern) {
        array_unshift($medicalSections, ['title' => 'Medical Background', 'icon' => 'heart-pulse', 'fields' => [
            ['Medical Concern', 'Yes'], ['Psych Testing', $applicant->psych_testing],
            ['Prescription Medicine', $applicant->prescription_med], ['Allergies', $applicant->allergies],
            ['Current Medications', $applicant->current_medications], ['Health Conditions', $applicant->health_conditions],
            ['Medical History', $applicant->medical_history], ['Emergency Instructions', $applicant->emergency_instructions],
            ['Medication Explanation', $applicant->med_explanation],
        ]]);
    }
    $discountInfo = [
        ['Sibling Order', $applicant->sibling_order], ['Discount Type', $applicant->discount_type],
        ['Discount Percentage', filled($applicant->discount_percentage) ? $applicant->discount_percentage.'%' : null],
        ['Discount Amount', filled($applicant->discount_amount) ? 'PHP '.number_format((float) $applicant->discount_amount, 2) : null],
        ['Last Completed Step', $applicant->last_step], ['Current Status', $statusLabels[$applicant->status] ?? $applicant->status],
    ];
@endphp

<x-admin-layout title="Applicant Detail" :breadcrumbs="[['label' => 'Applications', 'href' => route('admin.applications.enrollment')], ['label' => 'Enrollment', 'href' => route('admin.applications.enrollment')], ['label' => $breadcrumbName, 'href' => null]]">
    <div x-data="{ preview: false, src: '', label: '', pdf: false, statusOpen: false, statusValue: @js($currentStatus), statusLabel: @js($currentStatusLabel), statusDescriptions: { draft: 'Application is still being drafted.', ready_for_submission: 'Application is complete and ready for submission.', submitted: 'Student successfully submitted application.', under_review: 'Admin is already reviewing the enrollment application.', pending: 'Waiting for additional requirements, payment, or clarification.', approved: 'Enrollment application approved.', rejected: 'Enrollment application declined.' }, openPreview(url, title, isPdf) { this.preview = true; this.src = url; this.label = title; this.pdf = isPdf; }, chooseStatus(value, label) { this.statusValue = value; this.statusLabel = label; this.statusOpen = false; } }"
         @keydown.escape.window="preview = false; statusOpen = false">
        <div class="mb-5 flex justify-end">
            <a href="{{ route('admin.applications.enrollment') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-4 py-2 text-sm font-bold text-emerald-700 shadow-sm transition hover:border-emerald-300 hover:bg-emerald-50">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Back to applications
            </a>
        </div>

        <div class="applicant-page">
            <main class="space-y-6">
                <section class="applicant-profile-card relative {{ $accentClass }}">
                    <span class="application-number-pill">Application #{{ str_pad($applicant->id, 4, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" class="applicant-photo" @if ($photoUrl) @click="openPreview('{{ $photoUrl }}', '2x2 Photo', false)" @endif>
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="2x2 Photo">
                        @else
                            NO PHOTO
                        @endif
                    </button>
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight">{{ $displayName }}</h2>
                        <p class="mt-2 text-sm text-emerald-50/90">{{ filled($studentAddress ?? null) ? Str::upper($studentAddress) : 'STUDENT ADDRESS NOT PROVIDED' }}</p>
                        <div class="applicant-pill-row">
                            <span class="applicant-pill applicant-pill-grade">{{ Str::upper($applicant->grade_level ?: 'Grade pending') }}</span>
                            <span class="applicant-pill applicant-pill-type">{{ Str::upper($applicant->student_type ?: 'Student') }}</span>
                            <span class="applicant-pill applicant-pill-mode">{{ Str::upper($applicant->learning_mode ?: 'Learning mode pending') }}</span>
                            <span class="applicant-pill applicant-pill-year">SY {{ $applicant->school_year ?? '-' }}</span>
                        </div>
                    </div>

                    @if(isset($siblings) && $siblings->isNotEmpty())
                        @php
                            $nextSibling = $siblings->where('id', '>', $applicant->id)->first() ?? $siblings->first();
                        @endphp
                        <a href="{{ route('admin.applicants.show', $nextSibling) }}" class="applicant-next-sibling">
                            Next Sibling: {{ Str::upper($nextSibling->full_name ?: 'Applicant') }}
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </a>
                    @endif
                </section>

                <x-card title="Student Information" subtitle="Core enrollment details">
                    <div class="detail-section-stack">
                        @foreach ($studentSections as $section)
                            <x-applicant.detail-section :title="$section['title']" :icon="$section['icon']" :fields="$section['fields']" />
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Address & Contact" subtitle="Student residence from enrollment form">
                    <div class="detail-section-stack">
                        @foreach ($addressSections as $section)
                            <x-applicant.detail-section :title="$section['title']" :icon="$section['icon']" :fields="$section['fields']" />
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Parent / Guardian" subtitle="Organized parent and home details">
                    <div class="detail-section-stack">
                        @foreach ($guardianSections as $section)
                            <x-applicant.detail-section :title="$section['title']" :icon="$section['icon']" :fields="$section['fields']" />
                        @endforeach
                    </div>
                </x-card>

                @if(isset($siblings) && $siblings->isNotEmpty())
                <x-card title="Family & Siblings" subtitle="Other children enrolled under the same parent account">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-slate-200 text-slate-500">
                                <tr>
                                    <th class="py-2 pr-4 font-medium">Name</th>
                                    <th class="py-2 pr-4 font-medium">Grade</th>
                                    <th class="py-2 pr-4 font-medium">Status / Completion</th>
                                    <th class="py-2 font-medium">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($siblings as $sibling)
                                <tr>
                                    <td class="py-3 pr-4 font-bold text-slate-900">{{ Str::upper($sibling->full_name) }}</td>
                                    <td class="py-3 pr-4">{{ $sibling->grade_level ?: '-' }}</td>
                                    <td class="py-3 pr-4">
                                        @if(in_array($sibling->status, ['draft', 'pending', 'ready_for_submission']))
                                            <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Missing Details / Incomplete</span>
                                        @else
                                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">{{ $statusLabels[$sibling->status] ?? ucfirst($sibling->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.applicants.show', $sibling) }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-700 font-medium">
                                            View Sibling <i data-lucide="arrow-right" class="h-3 w-3"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
                @endif


                <x-card title="Medical & Emergency" subtitle="Health details submitted by parent">
                    <div class="detail-section-stack">
                        @foreach ($medicalSections as $section)
                            <x-applicant.detail-section :title="$section['title']" :icon="$section['icon']" :fields="$section['fields']" />
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Enrollment Metadata" subtitle="Discount, progress, and review state">
                    <dl class="detail-grid">
                        @foreach ($discountInfo as [$label, $value])
                            <x-applicant.field :label="$label" :value="$value" />
                        @endforeach
                    </dl>
                </x-card>

                @if (filled($applicant->affidavit_data))
                    <x-card title="Affidavit Details" subtitle="Temporary proof information">
                        <dl class="detail-grid">
                            @foreach ($applicant->affidavit_data as $label => $value)
                                <x-applicant.field :label="Str::headline($label)" :value="is_array($value) ? implode(', ', $value) : $value" />
                            @endforeach
                        </dl>
                    </x-card>
                @endif

                <x-card title="Uploaded Documents" subtitle="Review submitted files and mark each document status">
                    @if ($canReviewApplications)
                        <x-slot:actions>
                            <form method="POST" action="{{ route('admin.applicants.document', $applicant) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="doc_key" value="uploaded_documents">
                                <input type="hidden" name="status" value="approved">
                                <button class="doc-action doc-action-approve">APPROVE</button>
                            </form>
                            <form method="POST" action="{{ route('admin.applicants.document', $applicant) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="doc_key" value="uploaded_documents">
                                <input type="hidden" name="status" value="rejected">
                                <button class="doc-action doc-action-reject">REJECT</button>
                            </form>
                        </x-slot:actions>
                    @endif
                    <div class="upload-grid">
                        @foreach ($docMap as $docKey => $doc)
                            <x-applicant.document-card :applicant="$applicant" :doc-key="$docKey" :doc="$doc" :status="$docStatuses[$docKey] ?? 'pending'" />
                        @endforeach
                    </div>
                </x-card>

                <x-card title="Payment Proof" subtitle="Enrollment fee verification">
                    <x-slot:actions>
                        @if ($payment?->receipt_url && $canReviewPayments)
                            <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                                @csrf
                                @method('PATCH')
                                <button class="doc-action doc-action-approve">APPROVE</button>
                            </form>
                            <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="remarks" value="Payment proof rejected during admin review.">
                                <button class="doc-action doc-action-reject">REJECT</button>
                            </form>
                        @endif
                    </x-slot:actions>
                    @if ($paymentUrl)
                        <div class="grid grid-cols-3 gap-4">
                            <button type="button" class="upload-preview rounded-xl border border-slate-200" @click="openPreview('{{ $paymentUrl }}', 'Payment Proof', {{ $paymentIsPdf ? 'true' : 'false' }})">
                                @if ($paymentIsPdf)
                                    <span class="upload-pdf"><i data-lucide="file-text" class="h-9 w-9"></i>PDF Receipt</span>
                                @else
                                    <img src="{{ $paymentUrl }}" alt="Payment Proof">
                                @endif
                            </button>
                            <dl class="detail-grid col-span-2">
                                <x-applicant.field label="Status" :value="$pmLabels[$payment->status] ?? $payment->status" />
                                <x-applicant.field label="Method" :value="$payment->method_label ?? $payment->method" />
                                <x-applicant.field label="Reference No." :value="$payment->reference_no" />
                                <x-applicant.field label="Amount" :value="$payment->amount ? 'PHP '.number_format((float) $payment->amount, 2) : null" />
                                <x-applicant.field label="Submitted At" :value="$payment->paid_at?->format('M d, Y h:i A')" />
                                <x-applicant.field label="Verified At" :value="$payment->verified_at?->format('M d, Y h:i A')" />
                            </dl>
                        </div>
                    @else
                        <div class="empty-state">
                            <i data-lucide="receipt-text" class="h-8 w-8"></i>
                            <p>No payment proof uploaded.</p>
                        </div>
                    @endif
                </x-card>
            </main>

            <aside class="review-panel space-y-4">
                <x-card title="Applicant">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Application ID #</span>
                            <span class="text-base font-black text-slate-950">{{ str_pad($applicant->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Family</span>
                            <span class="text-sm font-bold text-slate-700">FAMILY #{{ str_pad($familyNo, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Full Name</span>
                            <span class="text-sm font-black text-slate-950">{{ $displayName }}</span>
                        </div>
                    </div>
                </x-card>

                @if ($canReviewApplications)
                <x-card title="Review Actions">
                    <form method="POST" action="{{ route('admin.applicants.status', $applicant) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-900">Application Status</label>
                            <input type="hidden" name="status" :value="statusValue">
                            <div class="review-select-wrap" @click.outside="statusOpen = false">
                                <button type="button" class="review-select-button" :class="{ 'review-select-button-open': statusOpen }" @click="statusOpen = !statusOpen">
                                    <span x-text="statusLabel"></span>
                                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                                </button>
                                <div x-show="statusOpen" x-transition class="review-select-menu" x-cloak>
                                    @foreach ($statusLabels ?? [] as $value => $label)
                                        <button type="button" class="review-select-option" :class="{ 'review-select-option-active': statusValue === @js($value) }" @click="chooseStatus(@js($value), @js($label))">
                                            <div class="text-left pr-4">
                                                <div class="font-bold">{{ $label }}</div>
                                                <div class="text-[10px] text-slate-400 mt-0.5" :class="{ 'text-emerald-700/80': statusValue === @js($value) }" x-text="statusDescriptions[@js($value)]"></div>
                                            </div>
                                            <i data-lucide="check" class="h-4 w-4 shrink-0 text-emerald-600" x-show="statusValue === @js($value)"></i>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-900">Remarks</label>
                            <textarea name="remarks" rows="2" class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm">{{ old('remarks', $applicant->review_remarks) }}</textarea>
                        </div>
                        <button class="review-save-button">
                            <i data-lucide="check-circle-2" class="h-4 w-4"></i>
                            Save Review
                        </button>
                    </form>
                </x-card>
                @endif

                <x-card title="Readiness">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-3"><span>Documents</span><span class="readiness-pill {{ $allDocsOk ? 'readiness-emerald' : 'readiness-amber' }}">{{ $allDocsOk ? 'Approved' : 'Pending' }}</span></div>
                        <div class="flex justify-between gap-3"><span>Payment</span><span class="readiness-pill {{ $paymentOk ? 'readiness-emerald' : ($payment?->status === 'rejected' ? 'readiness-rose' : 'readiness-orange') }}">{{ $paymentReadinessLabel }}</span></div>
                        <div class="flex justify-between gap-3"><span>Approval</span><span class="readiness-pill {{ $applicant->status === 'approved' ? 'readiness-emerald' : ($payment?->status === 'rejected' ? 'readiness-rose' : ($canApprove ? 'readiness-emerald' : 'readiness-amber')) }}">{{ $approvalReadinessLabel }}</span></div>
                        @unless ($canApprove)
                            <div class="rounded-xl border border-rose-100 bg-rose-50 px-3 py-2 text-xs font-bold leading-5 text-rose-700">
                                @if (!$allDocsOk && !$paymentOk)
                                    Pending / Not Complete: documents and verified payment are required.
                                @elseif (!$allDocsOk)
                                    If no documents + may payment = not allow.
                                @elseif ($payment?->status === 'rejected')
                                    Payment rejected by Sir Cabel. Enrollment is on hold.
                                @else
                                    Documents approved, but no verified payment yet.
                                @endif
                            </div>
                        @endunless
                    </div>
                </x-card>
            </aside>
        </div>

        <template x-teleport="body">
            <div x-show="preview" class="preview-modal" x-cloak>
                <button type="button" class="preview-backdrop" @click="preview = false"></button>
                <div class="preview-panel">
                    <div class="preview-head">
                        <strong x-text="label"></strong>
                        <button type="button" class="text-2xl leading-none text-slate-500" @click="preview = false">&times;</button>
                    </div>
                    <div class="preview-body">
                        <template x-if="!pdf"><img :src="src" :alt="label"></template>
                        <template x-if="pdf"><iframe :src="src"></iframe></template>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-admin-layout>
