                    <div>
                        <table class="ledger-table w-full text-left" style="font-size: 19.5px !important; border-collapse: collapse;">
                            <thead>
                                <tr class="bg-sage-medium text-black font-bold uppercase" style="font-size: 19.5px !important; background-color: #b8cece !important;">
                                    <th class="px-3 py-3" style="background-color: #b8cece !important;">DESCRIPTION</th>
                                    <th class="px-3 py-3 text-center" style="background-color: #b8cece !important;">MONTH</th>
                                    <th class="px-3 py-3 text-right" style="background-color: #b8cece !important;">AMOUNT</th>
                                    <th class="px-3 py-3 text-center" style="background-color: #b8cece !important;">DATE</th>
                                    <th class="px-3 py-3 text-center" style="background-color: #b8cece !important;">AMOUNT PAID</th>
                                    <th class="px-3 py-3 text-center" style="background-color: #b8cece !important;">OR</th>
                                    <th class="px-3 py-3 text-right" style="background-color: #b8cece !important;">BALANCE</th>
                                </tr>
                            </thead>
                            <tbody class="font-semibold text-black">
                                <!-- Dynamic Ledger Rows (Matched exactly to photo template) -->
                                @if ($enrollPaid > 0)
                                <tr class="bg-sage-row text-black">
                                    <td class="px-3 py-3 font-bold">Paid Enrollment Fee</td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-right"></td>
                                    <td class="px-3 py-3 text-center">{{ $enrollDate }}</td>
                                    <td class="px-3 py-3 text-center font-bold bg-[#FFFF00] text-black" style="font-size: 19.5px !important;">{{ number_format($enrollPaid, 2) }}</td>
                                    <td class="px-3 py-3 text-center font-bold">{{ $enrollOrNumber }}</td>
                                    <td class="px-3 py-3 text-right font-bold">{{ number_format($runningBalance -= $enrollPaid, 2) }}</td>
                                </tr>
                                @endif
                                
                                @if ($additionalSoaPaid > 0)
                                <tr class="bg-sage-row text-black">
                                    <td class="px-3 py-3 font-bold">Paid Additional SOA Paid (Allocated Excess)</td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-right"></td>
                                    <td class="px-3 py-3 text-center">{{ $enrollDate }}</td>
                                    <td class="px-3 py-3 text-center font-bold bg-[#FFFF00] text-black" style="font-size: 19.5px !important;">{{ number_format($additionalSoaPaid, 2) }}</td>
                                    <td class="px-3 py-3 text-center font-bold">{{ $enrollOrNumber }}-EXCESS</td>
                                    <td class="px-3 py-3 text-right font-bold">{{ number_format($runningBalance -= $additionalSoaPaid, 2) }}</td>
                                </tr>
                                @endif
                                @if ($booksCharge > 0)
                                <tr class="bg-sage-row text-black">
                                    <td class="px-3 py-3 font-bold">Books and programs</td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-right">{{ number_format($booksCharge, 2) }}</td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-right font-bold">{{ number_format($runningBalance += $booksCharge, 2) }}</td>
                                </tr>
                                @endif
                                @if ($booksPaid > 0)
                                <tr class="bg-sage-row text-black">
                                    <td class="px-3 py-3 font-bold">Paid Books</td>
                                    <td class="px-3 py-3 text-center"></td>
                                    <td class="px-3 py-3 text-right"></td>
                                    <td class="px-3 py-3 text-center">{{ $enrollDate }}</td>
                                    <td class="px-3 py-3 text-center bg-[#FFFF00] text-black" style="font-size: 19.5px !important;">{{ number_format($booksPaid, 2) }}</td>
                                    <td class="px-3 py-3 text-center font-bold">{{ $enrollOrNumber }}</td>
                                    <td class="px-3 py-3 text-right font-bold">{{ number_format($runningBalance -= $booksPaid, 2) }}</td>
                                </tr>
                                @endif

                                <!-- Shaded Required Payment Monthly spacer -->
                                <tr class="bg-sage-row font-bold text-black">
                                    <td class="px-3 py-2">Required Payment Monthly</td>
                                    <td class="px-3 py-2 text-center"></td>
                                    <td class="px-3 py-2 text-right"></td>
                                    <td class="px-3 py-2 text-center"></td>
                                    <td class="px-3 py-2 text-center"></td>
                                    <td class="px-3 py-2 text-center"></td>
                                    <td class="px-3 py-2 text-right font-bold text-black">-</td>
                                </tr>

                                @php
                                    $fifoPayments = [];
                                    foreach ($verifiedPayments as $payment) {
                                        $fifoPayments[] = [
                                            'id' => $payment->id,
                                            'amount' => (float) $payment->amount,
                                            'remaining' => (float) $payment->amount,
                                            'or_number' => $payment->or_number,
                                            'paid_at' => $payment->paid_at ?? $payment->created_at,
                                            'receipt_url' => $payment->receipt_url,
                                        ];
                                    }
                                    
                                    $billingsByYear = $account->monthlyBillings->groupBy(function($billing) {
                                        return \Carbon\Carbon::parse($billing->due_date)->format('Y');
                                    });
                                @endphp

                                @foreach ($billingsByYear as $year => $yearBillings)
                                    <!-- Year Header -->
                                    <tr class="bg-sage-row font-bold text-black">
                                        <td class="px-3 py-2">Year: {{ $year }}</td>
                                        <td class="px-3 py-2 text-center"></td>
                                        <td class="px-3 py-2 text-right"></td>
                                        <td class="px-3 py-2 text-center"></td>
                                        <td class="px-3 py-2 text-center"></td>
                                        <td class="px-3 py-2 text-center"></td>
                                        <td class="px-3 py-2 text-right"></td>
                                    </tr>

                                    <!-- Billing Rows for this Year -->
                                    @foreach ($yearBillings as $billing)
                                        @php
                                             $payDate = '';
                                             $payAmount = 0.00;
                                             $payOr = '';
                                             
                                             $needed = (float) $billing->amount_due;
                                             $contributions = [];
                                             
                                             $runningBalanceBeforeThisMonth = $runningBalance;
                                             
                                             // Allocate from FIFO payments to cover this installment
                                             foreach ($fifoPayments as &$p) {
                                                 if ($p['remaining'] > 0 && $needed > 0) {
                                                     $allocated = min($needed, $p['remaining']);
                                                     $p['remaining'] -= $allocated;
                                                     $needed -= $allocated;
                                                     $contributions[] = [
                                                         'amount' => $allocated,
                                                         'or_number' => $p['or_number'],
                                                         'paid_at' => $p['paid_at'],
                                                         'receipt_url' => $p['receipt_url'] ?? null,
                                                     ];
                                                 }
                                             }
                                             unset($p); // break reference
                                             
                                             if (count($contributions) > 0) {
                                                 $payAmount = array_sum(array_column($contributions, 'amount'));
                                                 
                                                 // OR Number: show the latest payment's OR number
                                                 $latestContribution = end($contributions);
                                                 $payOr = $latestContribution['or_number'] ?? '';
                                                 
                                                 // Date: show the latest payment date contributing to this row
                                                 $dates = array_filter(array_column($contributions, 'paid_at'));
                                                 if (count($dates) > 0) {
                                                     $latestDate = max($dates);
                                                     $payDate = $latestDate->format('d-M-y');
                                                 }
                                                 
                                                 $runningBalance -= $payAmount;
                                             }
                                             // Fallback to billing row marked as paid (legacy support)
                                             elseif ($billing->status === 'paid') {
                                                 $payDate = $billing->paid_at?->format('d-M-y') ?? '';
                                                 $payAmount = (float) $billing->amount_due;
                                                 $payOr = '';
                                                 $runningBalance -= $payAmount;
                                             }
                                             
                                             $billingDueDate = \Carbon\Carbon::parse($billing->due_date);
                                             $isBillingDueOrPassed = $billingDueDate->lte($currentDate);
                                         @endphp
                                        
                                         <!-- Payment Breakdown Sub-rows (Rendered ABOVE the main row) -->
                                         @if (count($contributions) > 1)
                                             @php
                                                 $runningBalanceSub = $runningBalanceBeforeThisMonth;
                                             @endphp
                                             @foreach ($contributions as $index => $c)
                                                 @php
                                                     $cPayAmount = (float) $c['amount'];
                                                     $cPayOr = $c['or_number'];
                                                     $cPayDate = $c['paid_at']?->format('d-M-y') ?? '';
                                                     $runningBalanceSub -= $cPayAmount;
                                                 @endphp
                                                 <tr class="breakdown-{{ $billing->id }} breakdown-row text-black font-semibold print:table-row hidden" style="background-color: #ffffff !important; font-size: 19.5px !important; border-left: 4px solid #0d9488 !important;">
                                                     <td class="px-3 py-2 pl-6 font-bold text-teal-800">Payment #{{ $index + 1 }}</td>
                                                     <td class="px-3 py-2 text-center font-bold">
                                                        @php
                                                            $proofUrl = $c['receipt_url'] ?? $account->applicant?->payment?->receipt_url ?? 'receipts/test_payment_proof.png';
                                                        @endphp
                                                        @if ($proofUrl)
                                                            <a href="{{ asset('storage/' . $proofUrl) }}" target="_blank" class="inline-flex items-center gap-1.5 text-teal-700 hover:text-teal-900 bg-teal-50 hover:bg-teal-100 border border-teal-200 hover:border-teal-400 rounded-lg px-2.5 py-1 transition-all select-none print:hidden shadow-sm" style="font-size: 15.5px !important;">
                                                                <i data-lucide="eye" class="h-4 w-4" style="stroke-width: 2.5;"></i>
                                                                <span>View Proof</span>
                                                            </a>
                                                            <span class="hidden print:inline text-xs font-semibold italic text-slate-400">Viewed online</span>
                                                        @else
                                                            <span class="italic text-slate-400 font-semibold">Breakdown</span>
                                                        @endif
                                                     </td>
                                                     <td class="px-3 py-2 text-right font-bold text-slate-400">—</td>
                                                     <td class="px-3 py-2 text-center">{{ $cPayDate }}</td>
                                                     <td class="px-3 py-2 text-center bg-[#FFFF00] text-black font-bold" style="font-size: 19.5px !important;">
                                                         {{ number_format($cPayAmount, 2) }}
                                                     </td>
                                                     <td class="px-3 py-2 text-center font-bold">{{ $cPayOr }}</td>
                                                     <td class="px-3 py-2 text-right font-bold text-slate-400">—</td>
                                                 </tr>
                                             @endforeach
                                         @endif

                                         <!-- Main Month Row (Rendered BELOW the breakdown rows) -->
                                         @if (count($contributions) > 1)
                                             <tr class="bg-sage-row text-black font-semibold cursor-pointer hover:bg-[#e2ebe9] transition-colors select-none" onclick="toggleSingleBreakdown('breakdown-{{ $billing->id }}')">
                                         @else
                                             <tr class="bg-sage-row text-black font-semibold">
                                         @endif
                                             <td class="px-3 py-2"></td>
                                             <td class="px-3 py-2 text-center font-bold text-black">{{ $billing->month_name }}</td>
                                             <td class="px-3 py-2 text-right font-bold">{{ number_format($installmentAmount, 2) }}</td>
                                             <td class="px-3 py-2 text-center">{{ $payDate }}</td>
                                             <td class="px-3 py-2 text-center @if($payAmount > 0) bg-[#FFFF00] text-black font-bold @endif" style="font-size: 19.5px !important;">
                                                 {{ $payAmount > 0 ? number_format($payAmount, 2) : '' }}
                                             </td>
                                             <td class="px-3 py-2 text-center font-bold">{{ $payOr }}</td>
                                             <td class="px-3 py-2 text-right font-bold">
                                                 @if ($isBillingDueOrPassed)
                                                     {{ number_format($runningBalance, 2) }}
                                                 @endif
                                             </td>
                                         </tr>
                                    @endforeach
                                @endforeach

                                 <!-- Shaded Footer Row with TO BE PAID and yellow highlighted PAID -->
                                 <tr class="bg-sage-row font-bold uppercase" style="font-size: 19.5px !important;">
                                     <td class="px-3 py-3"></td>
                                     <td class="px-3 py-3"></td>
                                     <td class="px-3 py-3"></td>
                                     <td class="px-3 py-3"></td>
                                     <td class="px-3 py-3 text-center bg-sage-medium text-black font-bold" style="font-size: 19.5px !important; background-color: #b8cece !important; border-left: 1.5px solid var(--table-border) !important; border-right: 1.5px solid var(--table-border) !important;">TO BE PAID</td>
                                     <td class="px-3 py-3 text-center bg-[#FFFF00] text-black font-bold" style="font-size: 19.5px !important; border-left: 1.5px solid var(--table-border) !important; border-right: 1.5px solid var(--table-border) !important;">PAID</td>
                                     <td class="px-3 py-3 text-right"></td>
                                 </tr>
 
                                 <!-- Total Amount to Pay Row (Enclosed in white table rows) -->
                                 <tr class="font-black text-black" style="font-size: 19.5px !important; background-color: #ffffff !important;">
                                     <td colspan="6" class="px-3 py-3.5 text-left text-black font-bold" style="background-color: #ffffff !important;">Total Amount to pay</td>
                                     <td class="px-3 py-3.5 text-right bg-sage-medium text-black font-bold amount-box" style="font-size: 21.5px !important; background-color: #b8cece !important;">{{ number_format($remainingBalance, 2) }}</td>
                                 </tr>
                                 <!-- Due Monthly Payment Row -->
                                 <tr class="font-black text-black" style="font-size: 19.5px !important; background-color: #ffffff !important;">
                                     <td colspan="4" class="px-3 py-3.5 text-left text-black font-bold" style="background-color: #ffffff !important;">Due Monthly Payment ({{ $billingMonthsCount }} Months)</td>
                                     <td class="px-3 py-3.5 text-center bg-[#FFFF00] text-black font-bold amount-box" style="font-size: 21.5px !important; background-color: #FFFF00 !important;">{{ number_format($installmentAmount, 2) }}</td>
                                     <td colspan="2" class="px-3 py-3.5" style="background-color: #ffffff !important;"></td>
                                 </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 5. Bottom Note Row -->
                    <div class="mt-3 flex justify-end text-right font-bold text-slate-600" style="font-size: 16.5px !important;">
                        <div class="flex flex-col">
                            <p>Note: Any discrepancies please inform the office.</p>
                            <p class="text-[#FF0000] uppercase font-extrabold tracking-wide mt-1 underline" style="font-weight:900; font-size: 16.5px !important;">ANY DISCREPANCY PLEASE INFORM, WE WILL CORRECT</p>
                        </div>
                    </div>

                    <!-- 6. Thick Yellow Separator and Shukran footer -->
                    <div class="mt-6 border-t border-slate-400 pt-4 text-center">
                        <div class="w-11/12 mx-auto h-4 bg-[#FFFF00] mb-4"></div>
                        <p class="text-xs font-bold tracking-wider text-black uppercase">Shukran. JazakAllahu khayran</p>
                    </div>

                </div>
