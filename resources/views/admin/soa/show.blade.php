@php
    $studentName = $account->student?->applicant?->full_name ?: 'RASHIEKA ABDULLA IBRAHIM';
    $address = $account->student?->applicant?->address ?: 'Jeddah, KSA';
    $email = $account->student?->applicant?->email ?: 'rainaabdulla5420@gmail.com';
    $lrn = $account->student?->applicant?->lrn ?: '418614250033';
    $studentId = $account->student?->student_number ?? '260001';
    $category = $account->student?->applicant?->student_type ?: 'Elementary';
    $grade = $account->grade_level ?? $account->student?->grade_level ?? 'G1';
    $discountPrivilege = $account->discount_percentage > 0 ? (int)$account->discount_percentage . '%' : '10%';
    $discountStatus = $account->discount_type ? strtoupper($account->discount_type) : 'Early Enrollment (January 2026)';

    // Math calculations
    $tuition = (float) ($account->tuition_fee ?: 35800.00);
    $discountAmount = (float) ($account->discount_amount ?: 3580.00);
    $tuitionNet = $tuition - $discountAmount;
    $misc = (float) ($account->miscellaneous_fee ?: 1900.00);
    $booksCharge = (float) ($account->books_fee ?: 5900.00);

    $totalFees = $tuition + $misc;
    $finalFees = $tuitionNet + $misc;

    // Ledger balance computation
    $runningBalance = $finalFees;
    $ledgerItems = [];

    // 1. Paid Enrollment Fee
    $enrollmentPayment = $account->payments->first(fn($p) => $p->remarks === 'Paid Enrollment Fee' && $p->status === 'verified');
    $enrollPaid = $enrollmentPayment ? (float)$enrollmentPayment->amount : 3000.00;
    $runningBalance -= $enrollPaid;
    $ledgerItems[] = [
        'description' => 'Paid Enrollment Fee',
        'month' => '',
        'amount' => '',
        'date' => $enrollmentPayment ? optional($enrollmentPayment->verified_at)->format('d-M-y') : '28-Jan-26',
        'paid' => $enrollPaid,
        'or' => $enrollmentPayment ? ($enrollmentPayment->or_number ?: $enrollmentPayment->reference_no) : '70105712',
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
    $booksPayment = $account->payments->first(fn($p) => $p->remarks === 'Paid Books' && $p->status === 'verified');
    $booksPaid = $booksPayment ? (float)$booksPayment->amount : 1000.00;
    $runningBalance -= $booksPaid;
    $ledgerItems[] = [
        'description' => 'Paid Books',
        'month' => '',
        'amount' => '',
        'date' => $booksPayment ? optional($booksPayment->verified_at)->format('d-M-y') : '28-Jan-26',
        'paid' => $booksPaid,
        'or' => $booksPayment ? ($booksPayment->or_number ?: $booksPayment->reference_no) : '70105712',
        'balance' => $runningBalance,
        'highlight_paid' => true,
    ];

    // 4. Other payments (excluding enrollment and books)
    $otherPayments = $account->payments->filter(fn($p) => !in_array($p->remarks, ['Paid Enrollment Fee', 'Paid Books']) && $p->status === 'verified');
    foreach ($otherPayments as $payment) {
        $runningBalance -= (float)$payment->amount;
        $ledgerItems[] = [
            'description' => $payment->remarks ?: 'Tuition Payment',
            'month' => '',
            'amount' => '',
            'date' => optional($payment->verified_at)->format('d-M-y') ?: now()->format('d-M-y'),
            'paid' => (float)$payment->amount,
            'or' => $payment->or_number ?: ($payment->reference_no ?: '-'),
            'balance' => $runningBalance,
            'highlight_paid' => false,
        ];
    }

    $monthsList = ['July', 'August', 'September', 'October', 'November', 'December', 'January', 'February', 'March'];
    $monthlyInstallment = round($runningBalance / 9, 2);
    $remainingBalance = $runningBalance;
@endphp

<x-admin-layout title="Student SOA Document">
    <div class="space-y-6 print:space-y-0">
        <!-- Top Toolbar with Print Command -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm print:hidden">
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-black text-emerald-800 uppercase tracking-wider">High Fidelity Preview</span>
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
            
            <!-- ==================================================
                 OFFICIAL STATEMENT OF ACCOUNT SHEET DOCUMENT
                 ================================================== -->
            <div class="bg-white p-8 border border-slate-300 shadow-md rounded-2xl print:border-0 print:shadow-none print:p-0">
                <div class="mx-auto max-w-[800px] border border-slate-350 p-6 bg-white font-sans text-xs text-slate-800 leading-normal">
                    
                    <!-- 1. School Header -->
                    <div class="flex items-center justify-between border-b-2 border-slate-400 pb-3">
                        <span class="text-base font-black text-slate-900 tracking-wider">AL MUNAWWARA ISLAMIC SCHOOL</span>
                        <img src="{{ asset('images/AMIS_Logo.png') }}" alt="AMIS Logo" width="55" height="55" class="h-14 w-14 rounded-full border border-slate-200 object-contain mx-auto">
                        <span class="text-lg font-bold text-emerald-700 tracking-wider" style="font-family: 'Courier New', Courier, monospace;">المدرسة المنورة الإسلامية</span>
                    </div>

                    <!-- 2. Statement Title Bar -->
                    <div class="bg-slate-100 border-x border-b border-slate-350 py-1.5 text-center font-black uppercase tracking-widest text-slate-900 mt-2 border-t-2 border-slate-400">
                        STATEMENT OF ACCOUNT SY 2026-2027
                    </div>

                    <!-- 3. Secondary Layout (Metadata + Sahih Quote) -->
                    <div class="grid grid-cols-[240px_1fr] gap-4 mt-3 border-b border-slate-350 pb-4">
                        <!-- Left Block: Address + Quote -->
                        <div class="space-y-4 text-[10px]">
                            <div>
                                <h4 class="font-black text-slate-400 uppercase">Address:</h4>
                                <p class="font-bold text-slate-700">Bugac Ma-a Road, Davao City</p>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-400 uppercase">Email Add:</h4>
                                <p class="font-bold text-slate-700">almunawwaraislamicschool@gmail.com</p>
                            </div>
                            <!-- Sahih Quote -->
                            <div class="pt-2 border-t border-slate-200">
                                <span class="text-[9px] font-black uppercase tracking-wider text-amber-700">Sahih International</span>
                                <p class="italic text-[9px] font-semibold text-slate-700 mt-0.5 leading-relaxed">
                                    "Whoever does righteousness, whether male or female, while he is a believer - We will surely cause him to live a good life, and We will surely give them their reward [in the Hereafter] according to the best of what they do."
                                </p>
                                <p class="text-[9px] font-black text-blue-650 mt-1 uppercase">Qur'an 16:97</p>
                            </div>
                        </div>

                        <!-- Right Block: Student Details Grid Table -->
                        <div class="border border-slate-350 rounded overflow-hidden">
                            <table class="w-full text-left border-collapse text-[10px]">
                                <tbody class="divide-y divide-slate-200">
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase w-32 border-r border-slate-200">Name of Student</td>
                                        <td class="px-2.5 py-1.5 font-black text-slate-900 uppercase">{{ $studentName }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">Address</td>
                                        <td class="px-2.5 py-1.5 font-bold text-slate-700">{{ $address }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">Email</td>
                                        <td class="px-2.5 py-1.5 font-semibold text-slate-700">{{ $email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">LRN</td>
                                        <td class="px-2.5 py-1.5 font-black text-slate-900">{{ $lrn }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">AMIS Student ID</td>
                                        <td class="px-2.5 py-1.5 font-black text-amber-800">{{ $studentId }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">Category / Grade</td>
                                        <td class="px-2.5 py-1.5 font-bold text-slate-700">{{ $category }} / {{ $grade }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">Discount Privilege</td>
                                        <td class="px-2.5 py-1.5 font-black text-emerald-800">{{ $discountPrivilege }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-slate-50 px-2.5 py-1.5 font-black text-slate-500 uppercase border-r border-slate-200">Discount Status</td>
                                        <td class="px-2.5 py-1.5 font-bold text-slate-700">{{ $discountStatus }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. Fees Grid Table (Description, Amount, Discount, Net) -->
                    <div class="mt-4">
                        <table class="w-full text-left text-[10px] border border-slate-350 border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-350 text-slate-600 font-black uppercase">
                                    <th class="px-3 py-2 border-r border-slate-350">Description</th>
                                    <th class="px-3 py-2 text-right border-r border-slate-350">Amount</th>
                                    <th colspan="2" class="px-3 py-2 text-center border-r border-slate-350">Discount (% / Amount)</th>
                                    <th class="px-3 py-2 text-right">Net</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-350 font-black">
                                <tr>
                                    <td class="px-3 py-2 border-r border-slate-350 text-slate-800">Tuition Fees</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">PHP {{ number_format($tuition, 2) }}</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-200 text-rose-700">{{ $account->discount_percentage > 0 ? (int)$account->discount_percentage.'%' : '10%' }}</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350 text-rose-700">PHP {{ number_format($discountAmount, 2) }}</td>
                                    <td class="px-3 py-2 text-right">PHP {{ number_format($tuitionNet, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-3 py-2 border-r border-slate-350 text-slate-800">Miscellaneous</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">PHP {{ number_format($misc, 2) }}</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-200">-</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-right">PHP {{ number_format($misc, 2) }}</td>
                                </tr>
                                <tr class="bg-slate-50 border-t border-slate-350 font-black">
                                    <td class="px-3 py-2 border-r border-slate-350">Total Fees</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">PHP {{ number_format($totalFees, 2) }}</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-200">-</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-right">PHP {{ number_format($finalFees, 2) }}</td>
                                </tr>
                                <tr class="bg-slate-100 font-black">
                                    <td class="px-3 py-2 border-r border-slate-350">Final Fees</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-200">-</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-right">PHP {{ number_format($finalFees, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 5. Main Chronological Ledger Table -->
                    <div class="mt-4">
                        <table class="w-full text-left text-[10px] border border-slate-350 border-collapse">
                            <thead>
                                <tr class="bg-slate-200 text-slate-700 font-black border-b border-slate-350">
                                    <th class="px-3 py-2.5 border-r border-slate-350">Description</th>
                                    <th class="px-3 py-2.5 text-center border-r border-slate-350">Month</th>
                                    <th class="px-3 py-2.5 text-right border-r border-slate-350">Amount</th>
                                    <th class="px-3 py-2.5 text-center border-r border-slate-350">Date</th>
                                    <th class="px-3 py-2.5 text-right border-r border-slate-350">Amount Paid</th>
                                    <th class="px-3 py-2.5 text-center border-r border-slate-350">OR</th>
                                    <th class="px-3 py-2.5 text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-300 font-bold text-slate-800">
                                <!-- Baseline Start -->
                                <tr class="text-slate-400">
                                    <td class="px-3 py-2 border-r border-slate-350">Baseline Fees</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-right border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-center border-r border-slate-350">-</td>
                                    <td class="px-3 py-2 text-right font-black text-slate-600 bg-slate-50/50">PHP {{ number_format($finalFees, 2) }}</td>
                                </tr>

                                <!-- Dynamic Ledger Rows -->
                                @foreach ($ledgerItems as $item)
                                    <tr>
                                        <td class="px-3 py-2 border-r border-slate-350 text-slate-900 font-black">{{ $item['description'] }}</td>
                                        <td class="px-3 py-2 text-center border-r border-slate-350">{{ $item['month'] ?: '-' }}</td>
                                        <td class="px-3 py-2 text-right border-r border-slate-350 font-black">
                                            {{ $item['amount'] ? 'PHP ' . number_format($item['amount'], 2) : '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-center border-r border-slate-350">{{ $item['date'] ?: '-' }}</td>
                                        <!-- Amount Paid Column with Yellow Highlight if applicable -->
                                        <td class="px-3 py-2 text-right border-r border-slate-350 font-black {{ $item['highlight_paid'] ? 'bg-yellow-100 text-slate-900' : 'text-slate-700' }}">
                                            {{ $item['paid'] ? 'PHP ' . number_format($item['paid'], 2) : '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-center border-r border-slate-350 font-black text-indigo-950">{{ $item['or'] ?: '-' }}</td>
                                        <td class="px-3 py-2 text-right font-black text-slate-900 bg-slate-50/50">PHP {{ number_format($item['balance'], 2) }}</td>
                                    </tr>
                                @endforeach

                                <!-- Required Payment Monthly Header -->
                                <tr class="bg-slate-50 font-black text-slate-500 border-t border-slate-350">
                                    <td colspan="7" class="px-3 py-1.5 uppercase text-[9px] tracking-wider border-b border-slate-300">Required Payment Monthly</td>
                                </tr>

                                <!-- Installment Breakdown rows July-March -->
                                @foreach ($account->monthlyBillings as $billing)
                                    @php
                                        $isBillingPaid = $billing->status === 'paid';
                                    @endphp
                                    <tr class="text-slate-650">
                                        <td class="px-3 py-2 pl-6 border-r border-slate-350 font-semibold">{{ $billing->month_name }}</td>
                                        <td class="px-3 py-2 text-center border-r border-slate-350 font-semibold">{{ $billing->month_name }}</td>
                                        <td class="px-3 py-2 text-right border-r border-slate-350 font-black">PHP {{ number_format((float)$billing->amount_due, 2) }}</td>
                                        <td class="px-3 py-2 text-center border-r border-slate-350 font-semibold">{{ $isBillingPaid && $billing->paid_at ? $billing->paid_at->format('d-M-y') : '-' }}</td>
                                        <td class="px-3 py-2 text-right border-r border-slate-350 font-black {{ $isBillingPaid ? 'bg-yellow-50 text-emerald-800' : '' }}">
                                            {{ $isBillingPaid ? 'PHP ' . number_format((float)$billing->amount_due, 2) : '-' }}
                                        </td>
                                        <td class="px-3 py-2 text-center border-r border-slate-350 font-bold">-</td>
                                        <td class="px-3 py-2 text-right font-semibold text-slate-400 bg-slate-50/20">-</td>
                                    </tr>
                                @endforeach

                                <!-- Table Footer Highlight summary -->
                                <tr class="bg-slate-100 font-black text-slate-800 border-t border-slate-350 text-[10px]">
                                    <td colspan="4" class="px-3 py-2.5 text-right uppercase border-r border-slate-350">TO BE PAID</td>
                                    <td colspan="2" class="px-3 py-2.5 text-center uppercase border-r border-slate-350 bg-yellow-100">PAID</td>
                                    <td class="px-3 py-2.5 text-right bg-sky-200 font-black text-slate-950">PHP {{ number_format($remainingBalance, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bottom Summary Rows -->
                    <div class="mt-3 grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-700">
                                <span>Total Amount to pay</span>
                                <span class="w-36 bg-sky-100 border border-sky-300 text-slate-950 text-right px-2.5 py-1 rounded font-black">PHP {{ number_format($remainingBalance, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] font-bold text-slate-700">
                                <span>Due Monthly Payment (9 Months)</span>
                                <span class="w-36 bg-yellow-100 border border-yellow-300 text-slate-950 text-right px-2.5 py-1 rounded font-black">PHP {{ number_format($monthlyInstallment, 2) }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col justify-end text-[10px] text-right font-bold text-slate-500">
                            <p>Note: Any discrepancies please inform the office.</p>
                            <p class="text-rose-700 uppercase font-black tracking-wider mt-1 underline">ANY DISCREPANCY PLEASE INFORM, WE WILL CORRECT</p>
                        </div>
                    </div>

                    <!-- Yellow highlight separator and Shukran footer -->
                    <div class="mt-6 border-t-2 border-slate-300 pt-4 text-center">
                        <div class="w-3/4 mx-auto h-2 bg-yellow-200 rounded-full mb-3"></div>
                        <p class="text-sm font-black tracking-wider text-slate-950 uppercase" style="font-family: 'Courier New', Courier, monospace;">Shukran. JazakAllahu khayran</p>
                    </div>

                </div>
            </div>

            <!-- ==================================================
                 RIGHT PANEL: SYSTEM TRANSACTION RECORDER
                 ================================================== -->
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
