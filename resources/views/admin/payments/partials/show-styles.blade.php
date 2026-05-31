    <!-- Custom hex-color utility overrides for billing layout -->
    <style>
        :root{
            --table-border: #b8cece; /* template color */
        }
        .bg-sage-light {
            background-color: #e8eee7 !important;
        }
        .bg-sage-medium {
            background-color: #b8cece !important;
        }
        .border-b-sage-dark {
            border-bottom: 3px solid #618889 !important;
        }

        .invoice-table {
            border-collapse: collapse !important;
            border-top: 2px solid var(--table-border) !important;
            border-bottom: 45px solid var(--table-border) !important;
            border-left: none !important;
            border-right: none !important;
        }

        .invoice-table tr,
        .invoice-table th,
        .invoice-table td {
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
        }

        .invoice-table thead th {
            border-top: 2px solid var(--table-border) !important;
        }

        .invoice-table tbody tr:not(.font-black) td {
            border-bottom: 1.5px solid var(--table-border) !important;
        }

        .invoice-table thead th,
        .table-header,
        .invoice-table th {
            background: #b8cece !important;
            background-color: #b8cece !important;
            color: #000000 !important;
            font-weight: bold !important;
        }

        .invoice-table tbody tr,
        .invoice-table tbody td {
            background-color: #e8eee7 !important;
        }

        /* Preserve specialized highlights */
        .invoice-table tbody td.bg-sage-medium {
            background-color: #b8cece !important;
        }
        .invoice-table tbody td.bg-\[\#FFFF00\] {
            background-color: #FFFF00 !important;
        }

        /* Ledger Table Styles (Same size just like above) */
        .ledger-table {
            border-collapse: collapse !important;
            width: 100% !important;
            font-size: 19.5px !important;
            border-top: 2px solid var(--table-border) !important;
            border-bottom: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .ledger-table tr,
        .ledger-table th,
        .ledger-table td {
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
        }
        .ledger-table thead th {
            background: #b8cece !important;
            background-color: #b8cece !important;
            color: #000000 !important;
            font-weight: bold !important;
            padding: 12px 16px !important;
            border-top: 2px solid var(--table-border) !important;
        }
        .ledger-table tbody tr:not(.font-black) td {
            border-bottom: 1.5px solid var(--table-border) !important;
            padding: 16px 16px !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        .ledger-table tbody tr {
            background-color: #ffffff !important;
        }
        .ledger-table tr.hover-row:hover td {
            background-color: #f8fafc !important;
        }

        /* Premium Button Styles */
        .btn-premium {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 8px 18px !important;
            border-radius: 9999px !important; /* capsule */
            font-weight: 800 !important;
            font-size: 13.5px !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease-in-out !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        }
        .btn-premium:active {
            transform: scale(0.95) !important;
        }

        .btn-approve {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
        .btn-approve:hover {
            background-color: #059669 !important;
            box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2) !important;
        }

        .btn-reject {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }
        .btn-reject:hover {
            background-color: #dc2626 !important;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2) !important;
        }

        .btn-view {
            background-color: #6366f1 !important;
            color: #ffffff !important;
            padding: 6px 14px !important;
        }
        .btn-view:hover {
            background-color: #4f46e5 !important;
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2) !important;
        }

        .btn-cancel {
            background-color: #e2e8f0 !important;
            color: #475569 !important;
            border: none !important;
        }
        .btn-cancel:hover {
            background-color: #cbd5e1 !important;
            color: #1e293b !important;
        }

        .total-box,
        .amount-box{
            border: 2px solid var(--table-border) !important;
        }

        /* Pure CSS Grid Classes to guarantee side-by-side layout without Tailwind compiler dependency */
        .billing-grid-container {
            display: grid !important;
            grid-template-columns: 400px 100px 1fr !important;
            gap: 0 !important;
        }
        .billing-divider-bar {
            background-color: #A0B7BC !important;
            width: 100px !important;
            height: 100% !important;
            min-height: 220px !important;
            margin: 0 auto !important;
            justify-self: center !important;
            align-self: stretch !important;
        }
        .billing-right-grid {
            display: grid !important;
            grid-template-columns: 210px 1fr !important;
            gap: 4px 8px !important;
        }
    </style>
