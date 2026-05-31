                    
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

                    <!-- 2. Statement Title Bar (Exact recreation with #e8eee7 background and #618889 bottom line, with increased font size) -->
                    <div class="bg-sage-light border-b-sage-dark py-4 text-center font-bold uppercase tracking-widest text-black" style="font-size: 32px !important; line-height: 1.4 !important;">
                        STATEMENT OF ACCOUNT SY 2026-2027
                    </div>

                    <!-- 3. Secondary Layout (Metadata Left + Vertical Divider Bar + Right Student details and Tuition stacked) -->
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

                        <!-- Middle Block: Sage Green Divider Bar (Exact color matching template divider) -->
                        <div class="billing-divider-bar"></div>

                        <!-- Right Block: Student Details & Tuition tables stacked vertically -->
                        <div class="space-y-4" style="padding-left: 24px !important; padding-right: 16px !important;">
                            <!-- Student details plain list (REMOVED borders to match template photo exactly) -->
                            <div class="billing-right-grid text-slate-800" style="line-height: 1.5 !important;">
                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Name of Student</div>
                                <div class="font-black text-slate-950 uppercase" style="font-size: 21.5px !important;">{{ $studentName }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">AMIS ID Student</div>
                                <div class="font-black text-slate-950 uppercase" style="font-size: 21.5px !important;">{{ $studentId }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Address</div>
                                <div class="font-bold text-slate-900">
                                    <div style="font-size: 19.5px !important;">{{ $address }}</div>
                                    <div class="mt-0.5 font-semibold text-slate-700" style="font-size: 19.5px !important;">Email: {{ $email }}</div>
                                    <div class="mt-0.5 font-black text-slate-950" style="font-size: 21.5px !important;">LRN: {{ $lrn }}</div>
                                </div>

                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Category</div>
                                <div class="font-bold text-slate-900" style="font-size: 19.5px !important;">{{ $category }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Grade Level</div>
                                <div class="font-bold text-slate-900" style="font-size: 19.5px !important;">{{ $grade }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Discount Privilege</div>
                                <div class="font-bold text-slate-900" style="font-size: 19.5px !important;">{{ $discountPrivilege }}</div>

                                <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Discount Status</div>
                                <div class="font-bold text-slate-900" style="font-size: 19.5px !important;">{{ $discountStatus }}</div>

                                @if ($siblingAccounts->count() > 1)
                                    <div class="font-semibold text-slate-500" style="font-size: 19.5px !important;">Family Siblings</div>
                                    <div class="space-y-1.5 mt-0.5" style="font-size: 19.5px !important;">
                                        @foreach ($siblingAccounts as $sib)
                                            <div class="font-semibold {{ $sib->id === $account->id ? 'text-emerald-700 font-bold bg-emerald-50 border border-emerald-250 px-2.5 py-0.5 rounded-lg inline-block' : 'text-slate-700' }}">
                                                &bull; {{ $sib->student?->applicant?->first_name ?: $sib->student?->applicant?->full_name }} 
                                                ({{ $sib->grade_level }})
                                                @if ($sib->discount_percentage > 0)
                                                    - <span class="text-emerald-600 font-extrabold">{{ (int)$sib->discount_percentage }}% Sibling Discount</span>
                                                @else
                                                    - <span class="text-slate-500 font-semibold">1st Child (0%)</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($familyAdvanceBalance > 0)
                                    <div class="font-semibold text-emerald-600" style="font-size: 19.5px !important;">Advance Payment Credit</div>
                                    <div class="font-black text-emerald-800 bg-emerald-50 px-3.5 py-1.5 rounded-2xl border border-emerald-200 inline-block mt-0.5" style="font-size: 21.5px !important;">₱{{ number_format($familyAdvanceBalance, 2) }}</div>
                                @endif
                            </div>

                            <!-- Tuition Fee summary table (COLLAPSED black borders, white background) -->
                            <div>
                                <table class="tuition-summary-table text-left" style="font-size: 19.5px !important;">
                                    <thead>
                                        <tr class="bg-white text-black font-bold uppercase text-center tracking-wider" style="font-size: 19.5px !important;">
                                            <th rowspan="2" class="px-2.5 py-2 text-left">DESCRIPTION</th>
                                            <th rowspan="2" class="px-2.5 py-2">AMOUNT</th>
                                            <th colspan="2" class="px-2.5 py-2">DISCOUNT</th>
                                            <th rowspan="2" class="px-2.5 py-2">NET</th>
                                        </tr>
                                        <tr class="bg-white text-black font-bold uppercase text-center tracking-wider" style="font-size: 19.5px !important;">
                                            <th class="px-2.5 py-1.5 font-bold">%</th>
                                            <th class="px-2.5 py-1.5">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="font-semibold text-black" style="font-size: 19.5px !important;">
                                        <tr>
                                            <td class="px-2.5 py-1.5 font-bold">Tuition Fees</td>
                                            <td class="px-2.5 py-1.5 text-right">{{ number_format($tuition, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-center font-semibold text-black">{{ $account->discount_percentage > 0 ? (int)$account->discount_percentage . '%' : '-' }}</td>
                                            <td class="px-2.5 py-1.5 text-right text-black font-semibold">{{ number_format($discountAmount, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold text-black">{{ number_format($tuitionNet, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-2.5 py-1.5 font-bold">Miscellaneous</td>
                                            <td class="px-2.5 py-1.5 text-right">{{ number_format($misc, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-center"></td>
                                            <td class="px-2.5 py-1.5 text-right">-</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold text-black">{{ number_format($misc, 2) }}</td>
                                        </tr>
                                        <tr class="bg-white font-bold text-black">
                                            <td class="px-2.5 py-1.5 font-bold">Total Fees</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold">{{ number_format($totalFees, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-center font-semibold text-black"></td>
                                            <td class="px-2.5 py-1.5 text-right text-black font-semibold"></td>
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
