                    
                    <!-- 1. School Header -->
                    <div class="border-t border-black pt-1">
                        <div class="py-2" style="display: flex !important; align-items: center !important; justify-content: space-between !important; min-height: 70px !important;">
                            <div class="font-black uppercase text-black tracking-wider leading-none" style="font-family: Arial, sans-serif; font-weight: 900; padding-left: 14px; font-size: 18px !important;">
                                AL MUNAWWARA ISLAMIC SCHOOL
                            </div>
                            <div class="flex-shrink-0">
                                <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS Logo" style="height: 60px !important; width: auto !important;">
                            </div>
                            <div class="font-black text-right tracking-wider leading-none" style="font-family: 'Times New Roman', serif; font-weight: 900; direction: rtl; padding-right: 14px; font-size: 18px !important; color: #68ac5b !important;">
                                المدرسة المنورة الإسلامية
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-black mt-1"></div>

                    <!-- 2. Statement Title Bar -->
                    <div class="bg-sage-light border-b-sage-dark py-3 text-center font-bold uppercase tracking-widest text-black" style="font-size: 24px !important; line-height: 1.4 !important;">
                        STATEMENT OF ACCOUNT SY {{ $account->school_year ?? $schoolYear }}
                    </div>

                    <!-- 3. Secondary Layout (Metadata Left + Vertical Divider Bar + Right Student details and Tuition stacked) -->
                    <div class="billing-grid-container mt-4 border-b border-slate-400 pb-4">
                        
                        <!-- Left Block: Address + Quote -->
                        <div class="space-y-3 self-start" style="line-height: 1.4 !important; padding-left: 12px !important; padding-right: 16px !important;">
                            <div>
                                <h4 class="font-bold text-slate-500" style="font-size: 14px !important;">Address:</h4>
                                <p class="font-bold text-slate-900 mt-0.5" style="font-size: 14px !important;">{{ $schoolAddress }}</p>
                            </div>
                            <div class="mt-2">
                                <h4 class="font-bold text-slate-500" style="font-size: 14px !important;">Email Add:</h4>
                                <p class="font-bold text-slate-900 mt-0.5 leading-tight" style="font-size: 12px !important; white-space: nowrap !important;">{{ $schoolEmail }}</p>
                            </div>
                            <!-- Sahih Quote -->
                            <div class="pt-2">
                                <span class="font-black uppercase tracking-wider text-[#2962FF] italic" style="font-size: 13px !important;">Sahih International</span>
                                <p class="italic font-semibold text-slate-800 mt-1 leading-normal" style="font-size: 12px !important;">
                                    "Whoever does righteousness, whether male or female, while he is a believer - We will surely cause him to live a good life, and We will surely give them their reward [in the Hereafter] according to the best of what they do."
                                </p>
                                <p class="font-black text-[#2962FF] mt-1 text-right" style="font-size: 13px !important;">Qur'an 16:97</p>
                            </div>
                        </div>

                        <!-- Middle Block: Sage Green Divider Bar (Exact color matching template divider) -->
                        <div class="billing-divider-bar"></div>

                        <!-- Right Block: Student Details & Tuition tables stacked vertically -->
                        <div class="space-y-3" style="padding-left: 16px !important; padding-right: 12px !important;">
                            <!-- Student details plain list -->
                            <div class="billing-right-grid text-slate-800" style="line-height: 1.4 !important;">
                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">Name</div>
                                <div class="font-black text-slate-950 uppercase" style="font-size: 16px !important; white-space: nowrap !important;">{{ $studentName }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">AMIS ID</div>
                                <div class="font-black text-slate-950 uppercase" style="font-size: 16px !important; white-space: nowrap !important;">{{ $studentId }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">Address</div>
                                <div class="font-bold text-slate-900">
                                    <div style="font-size: 14px !important;">{{ $address }}</div>
                                    <div class="mt-0.5 font-semibold text-slate-700" style="font-size: 13px !important;">Email: {{ $email }}</div>
                                    <div class="mt-0.5 font-black text-slate-950" style="font-size: 14px !important;">LRN: {{ $lrn }}</div>
                                </div>

                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">Category</div>
                                <div class="font-bold text-slate-900" style="font-size: 14px !important; white-space: nowrap !important;">{{ $category }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">Grade Level</div>
                                <div class="font-bold text-slate-900" style="font-size: 14px !important; white-space: nowrap !important;">{{ $grade }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">Discount</div>
                                <div class="font-bold text-slate-900" style="font-size: 14px !important; white-space: nowrap !important;">{{ $discountPrivilege }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 14px !important; white-space: nowrap !important;">Discount Status</div>
                                <div class="font-bold text-slate-900" style="font-size: 14px !important; white-space: nowrap !important;">{{ $discountStatus }}</div>

                                @if ($siblingAccounts->count() > 1)
                                    <div class="font-semibold text-slate-500" style="font-size: 13px !important;">Family Siblings</div>
                                    <div class="space-y-1 mt-0.5" style="font-size: 13px !important;">
                                        @foreach ($siblingAccounts as $sib)
                                            <div class="font-semibold {{ $sib->id === $account->id ? 'text-emerald-700 font-bold bg-emerald-50 border border-emerald-250 px-2.5 py-0.5 rounded-lg inline-block' : 'text-slate-700' }}">
                                                &bull; {{ $sib->student?->applicant?->first_name ?: $sib->student?->applicant?->full_name }} 
                                                ({{ $sib->grade_level }})
                                                @if ($sib->discount_percentage > 0)
                                                    - <span class="text-emerald-600 font-extrabold">{{ (int)$sib->discount_percentage }}%</span>
                                                @else
                                                    - <span class="text-slate-500 font-semibold">0%</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($familyAdvanceBalance > 0)
                                    <div class="font-semibold text-emerald-600" style="font-size: 13px !important;">Advance Credit</div>
                                    <div class="font-black text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-200 inline-block mt-0.5" style="font-size: 14px !important;">₱{{ number_format($familyAdvanceBalance, 2) }}</div>
                                @endif
                            </div>

                            <!-- Tuition Fee summary table -->
                            <div>
                                <table class="tuition-summary-table text-left" style="font-size: 14px !important;">
                                    <thead>
                                        <tr class="bg-white text-black font-bold uppercase text-center tracking-wider" style="font-size: 14px !important;">
                                            <th rowspan="2" class="px-2 py-1.5 text-left">DESCRIPTION</th>
                                            <th rowspan="2" class="px-2 py-1.5">AMOUNT</th>
                                            <th colspan="2" class="px-2 py-1.5">DISCOUNT</th>
                                            <th rowspan="2" class="px-2 py-1.5">NET</th>
                                        </tr>
                                        <tr class="bg-white text-black font-bold uppercase text-center tracking-wider" style="font-size: 14px !important;">
                                            <th class="px-2 py-1 font-bold">%</th>
                                            <th class="px-2 py-1">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-semibold text-black" style="font-size: 14px !important;">
                                        <tr>
                                            <td class="px-2 py-1 font-bold">Tuition Fees</td>
                                            <td class="px-2 py-1 text-right">{{ number_format($tuition, 2) }}</td>
                                            <td class="px-2 py-1 text-center font-semibold text-black">{{ $account->discount_percentage > 0 ? (int)$account->discount_percentage . '%' : '-' }}</td>
                                            <td class="px-2 py-1 text-right text-black font-semibold">{{ number_format($discountAmount, 2) }}</td>
                                            <td class="px-2 py-1 text-right font-bold text-black">{{ number_format($tuitionNet, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-2 py-1 font-bold">Miscellaneous</td>
                                            <td class="px-2 py-1 text-right">{{ number_format($misc, 2) }}</td>
                                            <td class="px-2 py-1 text-center"></td>
                                            <td class="px-2 py-1 text-right">-</td>
                                            <td class="px-2 py-1 text-right font-bold text-black">{{ number_format($misc, 2) }}</td>
                                        </tr>
                                        <tr class="bg-white font-bold text-black">
                                            <td class="px-2 py-1 font-bold">Total Fees</td>
                                            <td class="px-2 py-1 text-right font-bold">{{ number_format($totalFees, 2) }}</td>
                                            <td class="px-2 py-1 text-center font-semibold text-black"></td>
                                            <td class="px-2 py-1 text-right text-black font-semibold"></td>
                                            <td class="px-2.5 py-1.5 text-right font-bold">{{ number_format($finalFees, 2) }}</td>
                                        </tr>
                                        <tr class="bg-white font-bold text-black">
                                            <td class="px-2.5 py-1.5 font-bold">Final Fees</td>
                                            <td class="px-2.5 py-1.5 text-right"></td>
                                            <td class="px-2.5 py-1.5 text-center"></td>
                                            <td class="px-2.5 py-1.5 text-right">-</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold">{{ number_format($finalFees, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
