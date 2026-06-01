    <!-- JavaScript to toggle Modal with premium fade/scale animations -->
    <script>
        let currentPaymentBreakdownTemplate = null;
        let currentPaymentBreakdownMonth = null;

        function openPaymentModal() {
            const modal = document.getElementById('paymentModal');
            const card = document.getElementById('modalCard');
            
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');
            
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
            
            // Re-render Lucide icons dynamically in modal
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function closePaymentModal() {
            const modal = document.getElementById('paymentModal');
            const card = document.getElementById('modalCard');
            
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
            
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');
        }

        function openPaymentBreakdownModal(templateId, monthName) {
            const template = document.getElementById(templateId);
            const modal = document.getElementById('paymentBreakdownModal');
            const card = document.getElementById('paymentBreakdownModalCard');
            const body = document.getElementById('paymentBreakdownBody');
            const title = document.getElementById('paymentBreakdownTitle');
            const back = document.getElementById('paymentBreakdownBack');

            if (!template || !modal || !card || !body || !title) {
                return;
            }

            currentPaymentBreakdownTemplate = templateId;
            currentPaymentBreakdownMonth = monthName;
            body.innerHTML = template.innerHTML;
            title.replaceChildren();
            title.insertAdjacentHTML('beforeend', '<i data-lucide="receipt-text" class="h-6 w-6 text-emerald-600"></i>');
            title.appendChild(document.createTextNode(`${monthName} Payment Details`));
            back?.classList.add('hidden');
            back?.classList.remove('flex');

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');

            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function openPaymentProofInBreakdown(proofUrl) {
            const modal = document.getElementById('paymentBreakdownModal');
            const card = document.getElementById('paymentBreakdownModalCard');
            const body = document.getElementById('paymentBreakdownBody');
            const title = document.getElementById('paymentBreakdownTitle');
            const back = document.getElementById('paymentBreakdownBack');

            if (!modal || !card || !body || !title) {
                return;
            }

            title.replaceChildren();
            title.insertAdjacentHTML('beforeend', '<i data-lucide="image" class="h-6 w-6 text-emerald-600"></i>');
            title.appendChild(document.createTextNode('Payment Proof'));

            const wrapper = document.createElement('div');
            wrapper.className = 'rounded-2xl border border-slate-200 bg-slate-50 p-4';

            const image = document.createElement('img');
            image.src = proofUrl;
            image.alt = 'Payment proof';
            image.className = 'mx-auto max-h-[68vh] max-w-full rounded-xl border border-slate-200 bg-white object-contain shadow-sm';

            wrapper.appendChild(image);
            body.replaceChildren(wrapper);
            if (currentPaymentBreakdownTemplate) {
                back?.classList.remove('hidden');
                back?.classList.add('flex');
            } else {
                back?.classList.add('hidden');
                back?.classList.remove('flex');
            }

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100');

            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        function restorePaymentBreakdownList() {
            if (currentPaymentBreakdownTemplate && currentPaymentBreakdownMonth) {
                openPaymentBreakdownModal(currentPaymentBreakdownTemplate, currentPaymentBreakdownMonth);
            }
        }

        function closePaymentBreakdownModal() {
            const modal = document.getElementById('paymentBreakdownModal');
            const card = document.getElementById('paymentBreakdownModalCard');
            const body = document.getElementById('paymentBreakdownBody');
            const back = document.getElementById('paymentBreakdownBack');

            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');

            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0', 'pointer-events-none');

            if (body) {
                body.innerHTML = '';
            }
            back?.classList.add('hidden');
            back?.classList.remove('flex');
            currentPaymentBreakdownTemplate = null;
            currentPaymentBreakdownMonth = null;
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                openPaymentModal();
            });
        @endif
    </script>
