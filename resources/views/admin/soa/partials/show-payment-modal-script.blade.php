    <!-- JavaScript to toggle Modal with premium fade/scale animations -->
    <script>
        function toggleSingleBreakdown(className) {
            const rows = document.querySelectorAll('.' + className);
            rows.forEach(row => {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    row.style.display = 'table-row';
                } else {
                    row.classList.add('hidden');
                    row.style.display = 'none';
                }
            });
        }

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

        // Close on overlay click
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('paymentModal');
            if (e.target === modal) {
                closePaymentModal();
            }
        });
    </script>
