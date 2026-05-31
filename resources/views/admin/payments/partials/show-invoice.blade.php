        <div class="mt-6"></div>

        <x-card title="Invoice" subtitle="Invoice, payment details, proof, and finance review">
            <div class="mx-auto max-w-3xl overflow-hidden bg-white font-sans text-xs shadow-sm p-8 rounded-3xl border border-slate-200">
                
                <!-- 1. School Header (Exact recreation of official design reference) -->
                <div class="border-t border-black pt-2">
                    <div class="py-3" style="display: flex !important; align-items: center !important; justify-content: space-between !important; min-height: 96px !important;">
                        <!-- School name position (left) with left padding and exactly 24px font size -->
                        <div class="font-black uppercase text-black tracking-wider leading-none" style="font-family: Arial, sans-serif; font-weight: 900; padding-left: 14px; font-size: 24px !important;">
                            AL MUNAWWARA ISLAMIC SCHOOL
                        </div>
                        <!-- Logo placement (center) -->
                        <div class="flex-shrink-0">
                            <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS Logo" style="height: 80px !important; width: auto !important;">
                        </div>
                        <!-- Arabic school name position (right) with right padding, exactly 24px font size and #68ac5b color -->
                        <div class="font-black text-right tracking-wider leading-none" style="font-family: 'Times New Roman', serif; font-weight: 900; direction: rtl; padding-right: 14px; font-size: 24px !important; color: #68ac5b !important;">
                            المدرسة المنورة الإسلامية
                        </div>
                    </div>
                </div>
                <div class="border-b border-black mt-2"></div>

                <!-- 2. Invoice Title Bar (Exact recreation with #e8eee7 background and #618889 bottom line, with increased font size) -->
                <div class="bg-sage-light border-b-sage-dark py-4 text-center font-bold uppercase tracking-widest text-black" style="font-size: 32px !important; line-height: 1.4 !important;">
                    INVOICE FOR ENROLLMENT SY 2026-2027
                </div>

                <!-- 3. Secondary Layout (Metadata Left + Vertical Divider Bar + Right Family Details) -->
                <div class="billing-grid-container mt-4 border-b border-slate-400 pb-4">
                    
                    <!-- Left Block: Address + Quote -->
                    <div class="space-y-4 self-start" style="line-height: 1.5 !important; padding-left: 16px !important; padding-right: 24px !important;">
                        <div>
                            <h4 class="font-bold text-slate-500" style="font-size: 19.5px !important;">Address:</h4>
                            <p class="font-bold text-slate-900 mt-0.5" style="font-size: 19.5px !important;">Bugac Ma-a Road, Davao City</p>
                        </div>
                        <div class="mt-3">
                            <h4 class="font-bold text-slate-500" style="font-size: 19.5px !important;">Email Add:</h4>
                            <p class="font-bold text-slate-900 mt-0.5 leading-tight" style="font-size: 16.5px !important; white-space: nowrap !important;">almunawwaraislamicschool@gmail.com</p>
                        </div>
                        <!-- Sahih Quote -->
                        <div class="pt-3">
                            <span class="font-black uppercase tracking-wider text-[#2962FF] italic" style="font-size: 19.5px !important;">Sahih International</span>
                            <p class="italic font-semibold text-slate-800 mt-1 leading-normal" style="font-size: 17.5px !important;">
                                "Whoever does righteousness, whether male or female, while he is a believer - We will surely cause him to live a good life, and We will surely give them their reward [in the Hereafter] according to the best of what they do."
                            </p>
                            <p class="font-black text-[#2962FF] mt-1 text-right" style="font-size: 19.5px !important;">Qur'an 16:97</p>
                        </div>
                    </div>

                    <!-- Middle Block: Sage Green Divider Bar -->
                    <div class="billing-divider-bar"></div>

                    <!-- Right Block: Family/Student Details -->
                    <div class="space-y-4" style="padding-left: 24px !important; padding-right: 16px !important;">
                        @php
                            $familyLastName = '';
                            if ($invoiceChildren->isNotEmpty()) {
                                $first = $invoiceChildren->first();
                                if (!empty($first->last_name)) {
                                    $familyLastName = $first->last_name;
                                } else {
                                    $parts = explode(' ', trim($first->full_name));
                                    $familyLastName = end($parts);
                                }
                            }
                        @endphp
                        <div class="space-y-3.5 text-slate-800" style="line-height: 1.5 !important;">
                            <div>
                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Family Name:</div>
                                <div class="font-black text-slate-950 uppercase mt-0.5" style="font-size: 21.5px !important; padding-left: 20px !important;">FAMILY OF {{ strtoupper($familyLastName) }}</div>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Name of Applicant:</div>
                                <div class="font-bold text-slate-950 mt-0.5" style="padding-left: 20px !important;">
                                    @foreach ($invoiceChildren as $index => $child)
                                        <div class="font-black uppercase" style="font-size: 21.5px !important;">{{ $index + 1 }}) {{ $child->full_name }}</div>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Address:</div>
                                <div class="font-bold text-slate-900 uppercase mt-0.5" style="font-size: 19.5px !important; padding-left: 20px !important;">
                                    {{ strtoupper($invoiceChildren->first()?->address ?: 'Davao City, Philippines') }}
                                </div>
                            </div>

                            <div>
                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Discount:</div>
                                <div class="font-bold text-slate-900 mt-0.5" style="font-size: 19.5px !important; padding-left: 20px !important;">
                                    <div>
                                        @if ($invoiceChildren->count() > 1)
                                            Siblings Discount (15% for {{ $invoiceChildren->count() }} Siblings)
                                        @else
                                            Early Enrollment (15%)
                                        @endif
                                    </div>
                                    <div class="mt-0.5 font-bold text-slate-900">
                                        @if ($invoiceChildren->count() > 1)
                                            Siblings Discount Applied
                                        @else
                                            Early Enrollment Active
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($availableAdvanceTotal > 0 || $potentialExcess > 0)
                            <div>
                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Advance Payment Credit:</div>
                                <div class="font-bold mt-0.5" style="font-size: 19.5px !important; padding-left: 20px !important;">
                                    @if ($availableAdvanceTotal > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 border border-emerald-250 px-3.5 py-1.5 text-emerald-800 text-[16px] font-black shadow-sm">
                                            <i data-lucide="piggy-bank" class="h-4.5 w-4.5"></i>
                                            PHP {{ number_format($availableAdvanceTotal, 2) }} (Available)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 border border-amber-250 px-3.5 py-1.5 text-amber-800 text-[16px] font-black shadow-sm">
                                            <i data-lucide="piggy-bank" class="h-4.5 w-4.5 animate-pulse"></i>
                                            PHP {{ number_format($potentialExcess, 2) }} (Pending Verification)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 4. Items Table -->
                <div>
                    <table class="w-full text-left invoice-table" style="font-size: 19.5px !important;">
                        <thead>
                            <tr class="bg-sage-medium text-black font-bold uppercase" style="font-size: 19.5px !important; background-color: #b8cece !important;">
                                <th class="px-4 py-3" style="background-color: #b8cece !important;">Name</th>
                                <th class="px-4 py-3" style="background-color: #b8cece !important;">Grade</th>
                                <th class="px-4 py-3" style="background-color: #b8cece !important;">Type</th>
                                <th class="px-4 py-3" style="background-color: #b8cece !important;">Learning Mode</th>
                                <th class="px-4 py-3 text-right" style="background-color: #b8cece !important;">Amount Paid</th>
                                <th class="px-4 py-3 text-center" style="background-color: #b8cece !important;">Official Receipt (OR)</th>
                                <th class="px-4 py-3 text-right" style="background-color: #b8cece !important;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody class="font-semibold text-black">
                            @php
                                $sumDue = 0.00;
                            @endphp
                            @forelse ($invoiceChildren as $index => $child)
                                @php
                                    $sumDue += $invoiceChildAmount;
                                @endphp
                                <tr class="bg-white">
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-black uppercase" style="font-size: 19.5px !important;">{{ $index + 1 }}. {{ $child->full_name ?: 'Applicant' }}</div>
                                        <div class="text-[13.5px] font-semibold text-slate-500 mt-1 tracking-wider">APPLICANT #{{ str_pad((string) $child->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-4 py-4 uppercase text-black font-semibold" style="font-size: 19.5px !important;">{{ $child->grade_level ?? 'GRADE PENDING' }}</td>
                                    <td class="px-4 py-4 uppercase text-black font-semibold" style="font-size: 19.5px !important;">{{ $typeLabel($child->student_type ?? null) }}</td>
                                    <td class="px-4 py-4 uppercase text-black font-semibold" style="font-size: 19.5px !important;">{{ $learningModeLabel($child->learning_mode ?? null) }}</td>
                                    
                                    <!-- Amount Paid (clean empty cell!) -->
                                    <td class="px-4 py-4" style="font-size: 19.5px !important;">
                                        
                                    </td>
                                    
                                    <!-- Invoice Or (clean empty cell!) -->
                                    <td class="px-4 py-4" style="font-size: 19.5px !important;">
                                        
                                    </td>
                                    
                                    <!-- Total Amount -->
                                    <td class="px-4 py-4 text-right font-bold text-black" style="font-size: 19.5px !important;">
                                        {{ number_format($invoiceChildAmount, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-bold" style="font-size: 19.5px !important;">No child record found.</td>
                                </tr>
                            @endforelse
                            
                            <!-- Totals Row matching spreadsheet layout -->
                            <tr class="font-black text-black" style="font-size: 19.5px !important; background-color: #ffffff !important;">
                                <td colspan="4" class="px-4 py-4 text-right uppercase font-extrabold" style="background-color: #ffffff !important; border-top: 8px solid #8bacad !important;">PAID:</td>
                                <td class="px-4 py-4 text-right font-black" style="background-color: #FFFF00 !important; color: #000000 !important; border-top: 8px solid #8bacad !important;">
                                    {{ number_format($actualPaid, 2) }}
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-indigo-900" style="font-size: 17.5px !important; background-color: #ffffff !important; border-top: 8px solid #8bacad !important;">
                                    {{ $familyOrNo }}
                                </td>
                                <td class="px-4 py-4 text-right font-black" style="background-color: #ffffff !important; border-top: 8px solid #8bacad !important;">
                                    {{ number_format($sumDue, 2) }}
                                </td>
                            </tr>
                            
                            <!-- Remaining Balance Row (TO BE PAID with #ace0ee color fill) -->
                            @php
                                $balanceDue = $sumDue - $actualPaid;
                            @endphp

                            <tr class="font-black text-black" style="font-size: 19.5px !important; background-color: #ffffff !important;">
                                <td colspan="4" class="px-4 py-4 text-right uppercase font-extrabold" style="background-color: #ffffff !important;">
                                    TO BE PAID:
                                </td>
                                <td class="px-4 py-4" style="background-color: #ffffff !important;">
                                    
                                </td>
                                <td class="px-4 py-4 text-center" style="background-color: #ffffff !important;"></td>
                                <td class="px-4 py-4 text-right font-black" style="background-color: #ace0ee !important; color: #000000 !important;">
                                    {{ number_format(max(0, $balanceDue), 2) }}
                                </td>
                            </tr>
                            
                            @if ($availableAdvanceTotal > 0)
                            <tr class="font-black text-emerald-800 bg-emerald-50" style="font-size: 19.5px !important;">
                                <td colspan="4" class="px-4 py-4 text-right uppercase font-extrabold" style="border-top: 2px dashed #34d399 !important;">
                                    ADVANCE PAYMENT CREDIT:
                                </td>
                                <td class="px-4 py-4" style="background-color: transparent !important; border-top: 2px dashed #34d399 !important;"></td>
                                <td class="px-4 py-4 text-center" style="background-color: transparent !important; border-top: 2px dashed #34d399 !important;"></td>
                                <td class="px-4 py-4 text-right font-black" style="background-color: #34d399 !important; color: #ffffff !important; border-top: 2px dashed #34d399 !important;">
                                    {{ number_format($availableAdvanceTotal, 2) }}
                                </td>
                            </tr>
                            @elseif ($potentialExcess > 0)
                            <tr class="font-black text-amber-800 bg-amber-50" style="font-size: 19.5px !important;">
                                <td colspan="4" class="px-4 py-4 text-right uppercase font-extrabold" style="border-top: 2px dashed #f59e0b !important;">
                                    EXPECTED ADVANCE CREDIT:
                                </td>
                                <td class="px-4 py-4" style="background-color: transparent !important; border-top: 2px dashed #f59e0b !important;"></td>
                                <td class="px-4 py-4 text-center" style="background-color: transparent !important; border-top: 2px dashed #f59e0b !important;"></td>
                                <td class="px-4 py-4 text-right font-black" style="background-color: #f59e0b !important; color: #ffffff !important; border-top: 2px dashed #f59e0b !important;">
                                    {{ number_format($potentialExcess, 2) }}
                                </td>
                            </tr>
                            @endif
                    </table>
                </div>

            </div>
