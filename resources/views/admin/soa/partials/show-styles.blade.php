    <!-- Print optimized styling -->
    <style>
        :root{
            --table-border: #b8cece; /* template color */
        }
        /* Custom hex-color utility overrides for billing layout */
        .bg-sage-light {
            background-color: #e8eee7 !important;
        }
        .bg-sage-medium {
            background-color: #b8cece !important;
        }
        .bg-sage-row {
            background-color: #EBF0EF !important;
        }
        .border-b-sage-dark {
            border-bottom: 3px solid #618889 !important;
        }

        table{
            border-collapse: collapse;
            border: 2px solid var(--table-border) !important;
        }

        table th,
        table td{
            border: none !important;
        }

        table thead th,
        .table-header,
        .invoice-table th,
        table th {
            background: #b8cece !important;
            background-color: #b8cece !important;
            color: #000000 !important;
            font-weight: bold !important;
        }

        table tbody tr,
        table tbody td {
            background-color: #e8eee7 !important;
        }

        /* Hover effect for clickable rows, overriding td !important */
        tr.cursor-pointer:hover td {
            background-color: #e2ebe9 !important;
        }

        /* Preserve specialized highlights */
        table tbody td.bg-sage-medium {
            background-color: #b8cece !important;
        }
        table tbody td.bg-\[\#FFFF00\] {
            background-color: #FFFF00 !important;
        }

        .total-box,
        .amount-box{
            border: 2px solid var(--table-border) !important;
        }

        /* Custom Tuition Summary Table style - Black inner and outer lines, white/clean background */
        .tuition-summary-table {
            border-collapse: collapse !important;
            border: 2px solid #000000 !important;
            width: 100% !important;
            background-color: #ffffff !important;
        }
        .tuition-summary-table th,
        .tuition-summary-table td {
            border: 1px solid #000000 !important;
            background-color: #ffffff !important;
            background: #ffffff !important;
            color: #000000 !important;
        }
        .tuition-summary-table thead th {
            background-color: #ffffff !important;
            background: #ffffff !important;
            color: #000000 !important;
            font-weight: bold !important;
            border: 1px solid #000000 !important;
        }

        /* Custom Ledger Table - Horizontal Inner Lines */
        .ledger-table {
            border-collapse: collapse !important;
            border: 2px solid var(--table-border) !important;
        }
        .ledger-table th,
        .ledger-table td {
            border-bottom: 1.5px solid var(--table-border) !important;
        }

        /* Admin Payment History Ledger Styles - Matches Enrollment layout exactly */
        .admin-ledger-table {
            border-collapse: collapse !important;
            width: 100% !important;
            font-size: 19.5px !important;
            border-top: 2px solid var(--table-border) !important;
            border-bottom: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        .admin-ledger-table tr,
        .admin-ledger-table th,
        .admin-ledger-table td {
            border: none !important;
            border-left: none !important;
            border-right: none !important;
            border-top: none !important;
            border-bottom: none !important;
        }
        .admin-ledger-table thead th {
            background: #b8cece !important;
            background-color: #b8cece !important;
            color: #000000 !important;
            font-weight: bold !important;
            padding: 12px 16px !important;
            border-top: 2px solid var(--table-border) !important;
        }
        .admin-ledger-table tbody tr td {
            border-bottom: 1.5px solid var(--table-border) !important;
            padding: 16px 16px !important;
            background-color: #ffffff !important;
            color: #000000 !important;
        }
        .admin-ledger-table tbody tr {
            background-color: #ffffff !important;
        }
        .admin-ledger-table tr.hover-row:hover td {
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
        .btn-view {
            background-color: #6366f1 !important;
            color: #ffffff !important;
            padding: 6px 14px !important;
        }
        .btn-view:hover {
            background-color: #4f46e5 !important;
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.2) !important;
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

        @media print {
            /* Hide the sidebar, top navbar, breadcrumbs, and footer completely */
            nav,
            aside,
            footer,
            .admin-sidebar,
            #default-sidebar,
            header,
            .breadcrumbs,
            [aria-label="Breadcrumb"],
            .print\:hidden {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                width: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Strip off any parent margins, paddings, and sidebar offsets */
            body, 
            html {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .admin-shell {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .admin-content {
                margin-left: 0 !important;
                padding-left: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
            }

            .mx-auto.max-w-screen-2xl {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Symmetrical full-width document focus with print-safe scaling */
            .print-container {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                max-width: 130% !important;
                width: 130% !important;
                background: #ffffff !important;
                transform: scale(0.7) !important;
                transform-origin: top left !important;
            }

            /* Reduce all font sizes for print to fit on fewer pages */
            .print-container * {
                font-size: inherit !important;
            }
            .print-container table {
                font-size: 11px !important;
            }
            .print-container table th,
            .print-container table td {
                padding: 4px 6px !important;
                font-size: 11px !important;
            }
            .print-container h2,
            .print-container .text-center.font-bold {
                font-size: 16px !important;
            }

            .billing-grid-container {
                grid-template-columns: 280px 30px 1fr !important;
            }
            .billing-divider-bar {
                width: 30px !important;
                min-height: 160px !important;
            }
            .billing-right-grid {
                grid-template-columns: 150px 1fr !important;
            }

            .border.border-slate-400 {
                border: none !important; /* Remove outer sheet borders for perfect A4 page print */
            }

            @page {
                size: A4 portrait;
                margin: 0.5cm !important;
            }
        }
    </style>
