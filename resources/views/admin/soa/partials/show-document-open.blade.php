        <div class="grid gap-6 grid-cols-1 max-w-[900px] mx-auto print:max-w-none print:p-0">
            
            <!-- OFFICIAL STATEMENT OF ACCOUNT SHEET DOCUMENT -->
            <div class="bg-white p-8 border border-slate-300 shadow-md rounded-2xl print:border-0 print:shadow-none print:p-0 print-container">
                
                @if (($account->applicant?->status ?? '') !== 'approved')
                    <div class="mb-5 bg-amber-50 border border-amber-250 text-amber-900 px-6 py-4 rounded-2xl flex items-start gap-3 shadow-sm print:hidden">
                        <i data-lucide="alert-triangle" class="h-6 w-6 text-amber-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider text-amber-800">Pending Review</span>
                            <h4 class="font-extrabold text-sm mt-1">Pending Enrollment Approval</h4>
                            <p class="text-xs text-amber-800 font-semibold mt-0.5">
                                This student's enrollment application has not been approved yet. The figures below are estimated fees and the initial enrollment payment remains unverified.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="border border-slate-400 p-6 py-8 bg-white font-sans text-xs text-slate-800 leading-normal">
