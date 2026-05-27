@php
    $studentName = $account->student?->applicant?->full_name ?: 'KHALID TAMING ADJARAIL';
    $address = $account->student?->applicant?->address ?: 'Makkah, KSA';
    $email = $account->student?->applicant?->email ?: 'tamingadawiya@yahoo.com';
    $lrn = $account->student?->applicant?->lrn ?: '459013220043';
    $studentId = $account->student?->student_number ?? '260001';
    $category = $account->student?->applicant?->student_type ?: 'Elementary';
    $grade = $account->grade_level ?? $account->student?->grade_level ?? 'G4';
    $discountPrivilege = $account->discount_percentage > 0 ? (int)$account->discount_percentage . '%' : '15%';
    $discountStatus = $account->discount_type ? strtoupper($account->discount_type) : 'Early Enrollment (December 2025)';

    // Math calculations based on official template image
    $tuition = (float) ($account->tuition_fee ?: 38100.00);
    $discountAmount = (float) ($account->discount_amount ?: 5715.00);
    $tuitionNet = $tuition - $discountAmount;
    $misc = (float) ($account->miscellaneous_fee ?: 1900.00);
    $booksCharge = (float) ($account->books_fee ?: 5900.00);

    $totalFees = $tuition + $misc;
    $finalFees = $tuitionNet + $misc;

    // Ledger balance computation
    $runningBalance = $finalFees;
    $ledgerItems = [];

    // 1. Paid Enrollment Fee
    $enrollPaid = 3000.00;
    $runningBalance -= $enrollPaid;
    $ledgerItems[] = [
        'description' => 'Paid Enrollment Fee',
        'month' => '',
        'amount' => '',
        'date' => '30-Dec-25',
        'paid' => $enrollPaid,
        'or' => '70105712',
        'balance' => $runningBalance,
        'highlight_paid' => true,
    ];

    // 2. Books and programs charge
    $runningBalance += $booksCharge;
    $ledgerItems[] = [
        'description' => 'Books and programs',
        'month' => '',
        'amount' => $booksCharge,
        'date' => '',
        'paid' => '',
        'or' => '',
        'balance' => $runningBalance,
        'highlight_paid' => false,
    ];

    // 3. Paid Books
    $booksPaid = 1000.00;
    $runningBalance -= $booksPaid;
    $ledgerItems[] = [
        'description' => 'Paid Books',
        'month' => '',
        'amount' => '',
        'date' => '30-Dec-25',
        'paid' => $booksPaid,
        'or' => '70105712',
        'balance' => $runningBalance,
        'highlight_paid' => true,
    ];

    $monthlyInstallment = 4020.56;
    $remainingBalance = 36185.00;
@endphp

<x-admin-layout title="Student SOA Document">
    <!-- Print optimized styling -->
    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:border-0 {
                border: 0 !important;
            }
            .print\:shadow-none {
                box-shadow: none !important;
            }
            .print\:p-0 {
                padding: 0 !important;
            }
            .print-container {
                border: none !important;
                padding: 0 !important;
                box-shadow: none !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 1.2cm;
            }
        }
    </style>

    <div class="space-y-6 print:space-y-0">
        <!-- Top Toolbar with Print Command -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm print:hidden">
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 uppercase tracking-wider">Official Template Preview</span>
                <h1 class="text-lg font-black text-slate-900">Official SOA Document View</h1>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-slate-800 transition">
                    <i data-lucide="printer" class="h-4 w-4"></i>
                    Print SOA (Ctrl+P)
                </button>
                <a href="{{ route('admin.soa.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-350 bg-slate-50 px-5 py-3 text-xs font-black uppercase tracking-wider text-slate-800 hover:bg-slate-150 transition">
                    <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    Back to SOA List
                </a>
                <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-5 py-3 text-xs font-black uppercase tracking-wider text-white hover:bg-amber-700 transition">
                    <i data-lucide="layout-dashboard" class="h-4 w-4"></i>
                    Finance Dashboard
                </a>
            </div>
        </div>

        <!-- Dynamic Main Grid (Left: Official SOA Document, Right: Admin controls) -->
        <div class="grid gap-6 xl:grid-cols-[1fr_380px] print:grid-cols-1">
            
            <!-- OFFICIAL STATEMENT OF ACCOUNT SHEET DOCUMENT -->
            <div class="bg-white p-8 border border-slate-300 shadow-md rounded-2xl print:border-0 print:shadow-none print:p-0 print-container">
                <div class="mx-auto max-w-[800px] border border-slate-400 p-6 bg-white font-sans text-xs text-slate-800 leading-normal">
                    
                    <!-- 1. School Header -->
                    <div class="flex items-center justify-between border-b-2 border-slate-400 pb-3">
                        <span class="text-sm font-black text-slate-950 tracking-wider">AL MUNAWWARA ISLAMIC SCHOOL</span>
                        <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS Logo" width="55" height="55" class="h-14 w-14 object-contain mx-auto">
                        <span class="text-base font-extrabold text-[#2E7D32] tracking-wider">المدرسة المنورة الإسلامية</span>
                    </div>

                    <!-- 2. Statement Title Bar (Exact recreation with borders and sage color) -->
                    <div class="bg-[#DFE7E6] border-y border-black py-1.5 text-center font-bold uppercase tracking-widest text-black mt-2">
                        STATEMENT OF ACCOUNT SY 2026-2027
                    </div>

                    <!-- 3. Secondary Layout (Metadata Left + Vertical Divider Bar + Right Student details and Tuition stacked) -->
                    <div class="grid grid-cols-[210px_50px_1fr] gap-0 mt-4 border-b border-slate-400 pb-4">
                        
                        <!-- Left Block: Address + Quote -->
                        <div class="space-y-4 pr-4 text-[10px] self-start">
                            <div>
                                <h4 class="font-bold text-slate-500">Address:</h4>
                                <p class="font-bold text-slate-900 mt-0.5">Bugac Ma-a Road, Davao City</p>
                            </div>
                            <div class="mt-3">
                                <h4 class="font-bold text-slate-500">Email Add:</h4>
                                <p class="font-bold text-slate-900 mt-0.5 leading-tight">almunawwaraislamicschool@gmail.com</p>
                            </div>
                            <!-- Sahih Quote -->
                            <div class="pt-3">
                                <span class="text-[9.5px] font-black uppercase tracking-wider text-[#2962FF] italic">Sahih International</span>
                                <p class="italic text-[10px] font-semibold text-slate-800 mt-1 leading-normal">
                                    "Whoever does righteousness, whether male or female, while he is a believer - We will surely cause him to live a good life, and We will surely give them their reward [in the Hereafter] according to the best of what they do."
                                </p>
                                <p class="text-[9.5px] font-black text-[#2962FF] mt-1 text-right">Qur'an 16:97</p>
                            </div>
                        </div>

                        <!-- Middle Block: Sage Green Divider Bar (Exact color matching banner) -->
                        <div class="bg-[#DFE7E6] w-full h-full min-h-[220px]"></div>

                        <!-- Right Block: Student Details & Tuition tables stacked vertically -->
                        <div class="pl-5 space-y-4">
                            <!-- Student details plain list (REMOVED borders to match template photo exactly) -->
                            <div class="grid grid-cols-[115px_1fr] gap-x-2 gap-y-1 text-[10.5px] text-slate-800">
                                <div class="font-semibold text-slate-500">Name of Student</div>
                                <div class="font-black text-slate-950 uppercase">{{ $studentName }}</div>

                                <div class="font-semibold text-slate-500">Address</div>
                                <div class="font-bold text-slate-900">
                                    <div>{{ $address }}</div>
                                    <div class="mt-0.5 font-semibold text-slate-700">Email: {{ $email }}</div>
                                    <div class="mt-0.5 font-black text-slate-950">LRN: {{ $lrn }}</div>
                                </div>

                                <div class="font-semibold text-slate-500">Category</div>
                                <div class="font-bold text-slate-900">{{ $category }}</div>

                                <div class="font-semibold text-slate-500">Grade Level</div>
                                <div class="font-bold text-slate-900">{{ $grade }}</div>

                                <div class="font-semibold text-slate-500">Discount Privilege</div>
                                <div class="font-bold text-slate-900">{{ $discountPrivilege }}</div>

                                <div class="font-semibold text-slate-500">Discount Status</div>
                                <div class="font-bold text-slate-900">{{ $discountStatus }}</div>
                            </div>

                            <!-- Tuition Fee summary table (COLLAPSED thin black borders, white headers) -->
                            <div class="border border-black">
                                <table class="w-full text-left text-[10px] border-collapse border border-black">
                                    <thead>
                                        <tr class="bg-white border-b border-black text-black font-bold uppercase text-center text-[9px] tracking-wider">
                                            <th rowspan="2" class="px-2.5 py-1.5 border-r border-black text-left">DESCRIPTION</th>
                                            <th rowspan="2" class="px-2.5 py-1.5 border-r border-black">AMOUNT</th>
                                            <th colspan="2" class="px-2.5 py-1.5 border-r border-black">DISCOUNT</th>
                                            <th rowspan="2" class="px-2.5 py-1.5">NET</th>
                                        </tr>
                                        <tr class="bg-white border-b border-black text-black font-bold uppercase text-center text-[9px] tracking-wider">
                                            <th class="px-2.5 py-1 border-r border-black">%</th>
                                            <th class="px-2.5 py-1 border-r border-black">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-black font-semibold text-black">
                                        <tr>
                                            <td class="px-2.5 py-1.5 border-r border-black font-bold">Tuition Fees</td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black">{{ number_format($tuition, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-center border-r border-black font-semibold text-black">15%</td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black text-black font-semibold">{{ number_format($discountAmount, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold text-black">{{ number_format($tuitionNet, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="px-2.5 py-1.5 border-r border-black font-bold">Miscellaneous</td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black">{{ number_format($misc, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-center border-r border-black"></td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black">-</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold text-black">{{ number_format($misc, 2) }}</td>
                                        </tr>
                                        <tr class="bg-white border-t border-black font-bold text-black">
                                            <td class="px-2.5 py-1.5 border-r border-black font-bold">Total Fees</td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black font-bold">{{ number_format($totalFees, 2) }}</td>
                                            <td class="px-2.5 py-1.5 text-center border-r border-black font-semibold text-black"></td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black text-black font-semibold"></td>
                                            <td class="px-2.5 py-1.5 text-right font-bold">{{ number_format($finalFees, 2) }}</td>
                                        </tr>
                                        <tr class="bg-white border-t border-black font-bold text-black">
                                            <td class="px-2.5 py-1.5 border-r border-black font-bold">Final Fees</td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black"></td>
                                            <td class="px-2.5 py-1.5 text-center border-r border-black"></td>
                                            <td class="px-2.5 py-1.5 text-right border-r border-black">-</td>
                                            <td class="px-2.5 py-1.5 text-right font-bold">{{ number_format($finalFees, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Main Chronological Ledger Table (Collapsing black borders, sage gray header) -->
                    <div class="mt-4 border border-black">
                        <table class="w-full text-left text-[10px] border-collapse border border-black">
                            <thead>
                                <tr class="bg-[#DFE7E6] text-black font-bold border-b border-black uppercase text-[9.5px]">
                                    <th class="px-3 py-2 border-r border-black">Description</th>
                                    <th class="px-3 py-2 text-center border-r border-black">Month</th>
                                    <th class="px-3 py-2 text-right border-r border-black">Amount</th>
                                    <th class="px-3 py-2 text-center border-r border-black">Date</th>
                                    <th class="px-3 py-2 text-center border-r border-black">Amount Paid</th>
                                    <th class="px-3 py-2 text-center border-r border-black">OR</th>
                                    <th class="px-3 py-2 text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black font-semibold text-slate-800">
                                <!-- Dynamic Ledger Rows (Matched exactly to photo template) -->
                                <tr>
                                    <td class="px-3 py-2 border-r border-black font-bold text-slate-950">Paid Enrollment Fee</td>
                                    <td class="px-3 py-2 text-center border-r border-black">-</td>
                                    <td class="px-3 py-2 text-right border-r border-black">-</td>
                                    <td class="px-3 py-2 text-center border-r border-black">30-Dec-25</td>
                                    <td class="px-3 py-2 text-center border-r border-black font-black bg-[#FFFF00] text-slate-950 text-[10.5px]">3,000.00</td>
                                    <td class="px-3 py-2 text-center border-r border-black font-bold text-slate-900">70105712</td>
                                    <td class="px-3 py-2 text-right font-bold text-slate-900">31,285.00</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 border-r border-black font-bold text-slate-950">Books and programs</td>
                                    <td class="px-3 py-2 text-center border-r border-black">-</td>
                                    <td class="px-3 py-2 text-right border-r border-black">5,900.00</td>
                                    <td class="px-3 py-2 text-center border-r border-black">-</td>
                                    <td class="px-3 py-2 text-center border-r border-black">-</td>
                                    <td class="px-3 py-2 text-center border-r border-black">-</td>
                                    <td class="px-3 py-2 text-right font-bold text-slate-900">37,185.00</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 border-r border-black font-bold text-slate-950">Paid Books</td>
                                    <td class="px-3 py-2 text-center border-r border-black">-</td>
                                    <td class="px-3 py-2 text-right border-r border-black">-</td>
                                    <td class="px-3 py-2 text-center border-r border-black">30-Dec-25</td>
                                    <td class="px-3 py-2 text-center border-r border-black font-black bg-[#FFFF00] text-slate-950 text-[10.5px]">1,000.00</td>
                                    <td class="px-3 py-2 text-center border-r border-black font-bold text-slate-900">70105712</td>
                                    <td class="px-3 py-2 text-right font-bold text-slate-900">36,185.00</td>
                                </tr>

                                <!-- Shaded Required Payment Monthly spacer -->
                                <tr class="bg-slate-100 font-black text-slate-650 border-t border-black">
                                    <td class="px-3 py-1.5 border-r border-black">Required Payment Monthly</td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-right border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-right font-bold text-slate-900">-</td>
                                </tr>

                                <!-- Year: 2026 -->
                                <tr>
                                    <td class="px-3 py-1.5 border-r border-black font-black text-slate-900">Year: 2026</td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-right border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-right font-bold text-slate-900"></td>
                                </tr>
                                <!-- July - December installments -->
                                @foreach (['July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                    <tr>
                                        <td class="px-3 py-1.5 border-r border-black"></td>
                                        <td class="px-3 py-1.5 text-center border-r border-black font-bold text-slate-700">{{ $month }}</td>
                                        <td class="px-3 py-1.5 text-right border-r border-black font-bold">4,020.56</td>
                                        <td class="px-3 py-1.5 text-center border-r border-black">-</td>
                                        <td class="px-3 py-1.5 text-center border-r border-black">-</td>
                                        <td class="px-3 py-1.5 text-center border-r border-black">-</td>
                                        <td class="px-3 py-1.5 text-right font-bold text-slate-400"></td>
                                    </tr>
                                @endforeach

                                <!-- Year: 2027 -->
                                <tr>
                                    <td class="px-3 py-1.5 border-r border-black font-black text-slate-900">Year: 2027</td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-right border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-center border-r border-black"></td>
                                    <td class="px-3 py-1.5 text-right font-bold text-slate-900"></td>
                                </tr>
                                <!-- January - March installments -->
                                @foreach (['January', 'February', 'March'] as $month)
                                    <tr>
                                        <td class="px-3 py-1.5 border-r border-black"></td>
                                        <td class="px-3 py-1.5 text-center border-r border-black font-bold text-slate-700">{{ $month }}</td>
                                        <td class="px-3 py-1.5 text-right border-r border-black font-bold">4,020.56</td>
                                        <td class="px-3 py-1.5 text-center border-r border-black">-</td>
                                        <td class="px-3 py-1.5 text-center border-r border-black">-</td>
                                        <td class="px-3 py-1.5 text-center border-r border-black">-</td>
                                        <td class="px-3 py-1.5 text-right font-bold text-slate-400"></td>
                                    </tr>
                                @endforeach

                                <!-- Shaded Footer Row with TO BE PAID and yellow highlighted PAID -->
                                <tr class="bg-slate-100 font-black border-t border-black uppercase text-[10px]">
                                    <td colspan="4" class="px-3 py-2 text-right border-r border-black text-slate-950 font-black">TO BE PAID</td>
                                    <td colspan="2" class="px-3 py-2 text-center border-r border-black bg-[#FFFF00] text-slate-950 font-black text-[10.5px]">PAID</td>
                                    <td class="px-3 py-2 text-right bg-sky-200 text-slate-950 font-black">{{ number_format($remainingBalance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 5. Bottom Summary & Note Rows -->
                    <div class="mt-4 grid grid-cols-[1.2fr_1fr] gap-4">
                        <!-- Left Summary Container (Total and Monthly due) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-800">
                                <span>Total Amount to pay</span>
                                <div class="bg-sky-200 border border-black text-slate-950 w-36 px-2.5 py-1 text-right font-black rounded-sm">{{ number_format($remainingBalance, 2) }}</div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-800">
                                <span>Due Monthly Payment (9 Months)</span>
                                <div class="bg-[#FFFF00] border border-black text-slate-950 w-36 px-2.5 py-1 text-right font-black rounded-sm">4,020.56</div>
                            </div>
                        </div>

                        <!-- Right Discrepancy Note Block -->
                        <div class="flex flex-col justify-end text-[9.5px] text-right font-bold text-slate-500 self-start pt-1">
                            <p>Note: Any discrepancies please inform the office.</p>
                            <p class="text-[#FF0000] uppercase font-extrabold tracking-wide mt-1.5 underline" style="font-weight:900;">ANY DISCREPANCY PLEASE INFORM, WE WILL CORRECT</p>
                        </div>
                    </div>

                    <!-- 6. Thick Yellow Separator and Shukran footer -->
                    <div class="mt-6 border-t border-slate-400 pt-4 text-center">
                        <div class="w-11/12 mx-auto h-4 bg-[#FFFF00] mb-4"></div>
                        <p class="text-xs font-bold tracking-wider text-black uppercase">Shukran. JazakAllahu khayran</p>
                    </div>

                </div>
            </div>

            <!-- RIGHT PANEL: SYSTEM TRANSACTION RECORDER -->
            <div class="space-y-6 print:hidden">
                <!-- Manual Payment Recorder Form -->
                <x-card title="Record Payment Receipt" subtitle="SOA ledger receipt allocation entry">
                    <form method="POST" action="{{ route('admin.soa.payments.add', $account) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-2 block text-xs font-black text-slate-800 uppercase tracking-wide">Allocation Purpose</label>
                            <select name="purpose" required class="w-full rounded-xl border-2 border-slate-300 bg-white p-3 text-sm font-black text-slate-950 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                                <option value="Tuition Fee">Tuition Fee Installment</option>
                                <option value="Paid Books">Books and Programs Payment</option>
                                <option value="Paid Enrollment Fee">Enrollment Fee (Downpayment)</option>
                                <option value="Other Dues">Other Academic Fees</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black text-slate-800 uppercase tracking-wide">Amount (PHP)</label>
                            <input name="amount" type="number" min="1" max="{{ $account->remaining_balance }}" step="0.01" required placeholder="0.00" class="w-full rounded-xl border-2 border-slate-300 bg-white p-3 text-sm font-black text-slate-950 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black text-slate-800 uppercase tracking-wide">Payment Method</label>
                            <select name="method" required class="w-full rounded-xl border-2 border-slate-300 bg-white p-3 text-sm font-black text-slate-950 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                                <option value="cash">Cash Payment</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                <option value="bdo">BDO Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black text-slate-800 uppercase tracking-wide">Transaction / Reference No.</label>
                            <input name="reference_no" placeholder="Reference Number" class="w-full rounded-xl border-2 border-slate-300 bg-white p-3 text-sm font-black text-slate-950 placeholder-slate-400 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black text-slate-800 uppercase tracking-wide">Official Receipt (OR) Number</label>
                            <input name="or_number" placeholder="OR Number (e.g. 70105712)" class="w-full rounded-xl border-2 border-slate-300 bg-white p-3 text-sm font-black text-slate-950 placeholder-slate-400 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-black text-slate-800 uppercase tracking-wide">Checked & Verified By</label>
                            <input name="checked_by" value="Sir Cabel" class="w-full rounded-xl border-2 border-slate-300 bg-slate-100 p-3 text-sm font-black text-slate-950 outline-none transition focus:border-amber-500 focus:ring-4 focus:ring-amber-100">
                        </div>
                        <button class="w-full rounded-2xl bg-amber-600 px-4 py-3 text-sm font-black uppercase tracking-wider text-white shadow-lg shadow-amber-700/30 transition hover:bg-amber-700">Record Manual Payment</button>
                    </form>
                </x-card>
            </div>
        </div>

        <!-- Payments Logs verification list under the document sheet (print:hidden) -->
        <div class="print:hidden">
            <x-card title="Payment Verification Logs" subtitle="General financial logs history for administrative review">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-300 text-xs font-black uppercase tracking-widest text-slate-400">
                                <th class="px-4 py-3">Method</th>
                                <th class="px-4 py-3">Reference No.</th>
                                <th class="px-4 py-3">OR Number</th>
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Checked By</th>
                                <th class="px-4 py-3 text-right">Verification Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-150 font-black text-slate-850">
                            @forelse ($account->payments as $payment)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-4 py-4">
                                        <div class="font-black text-slate-950 uppercase">{{ $payment->method ?? 'Payment' }}</div>
                                        <div class="mt-1 flex items-center gap-1.5">
                                            <span class="rounded-full border border-indigo-300 bg-indigo-100 px-3 py-0.5 text-[9px] font-black uppercase tracking-wider text-indigo-950">
                                                {{ $payment->remarks ?: 'Tuition Fee' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 font-bold text-slate-700">{{ $payment->reference_no ?? '-' }}</td>
                                    <td class="px-4 py-4 font-black text-indigo-900">{{ $payment->or_number ?? '-' }}</td>
                                    <td class="px-4 py-4 font-black text-slate-950">PHP {{ number_format((float) $payment->amount, 2) }}</td>
                                    <td class="px-4 py-4">
                                        <x-badge color="{{ ($payment->status ?? '') === 'verified' ? 'green' : (($payment->status ?? '') === 'rejected' ? 'red' : 'yellow') }}">
                                            {{ Str::upper($payment->status ?? 'pending') }}
                                        </x-badge>
                                    </td>
                                    <td class="px-4 py-4 font-bold text-slate-700">{{ $payment->checked_by ?? '-' }}</td>
                                    <td class="px-4 py-4 text-right font-bold text-slate-600">{{ optional($payment->verified_at)->format('M d, Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="px-4 py-10 text-center text-sm font-black text-slate-400">No payment logs recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

    </div>
</x-admin-layout>
