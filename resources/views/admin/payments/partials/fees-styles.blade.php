    <!-- Print and Layout Custom styling -->
    <style>
        :root {
            --table-border: #b8cece; /* Unified border color */
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

        table {
            border-collapse: collapse;
            border: 2px solid var(--table-border) !important;
        }

        table th,
        table td {
            border: none !important; /* No inner dividers as requested */
        }

        /* Yellow Section Headers exactly matching official image */
        .bg-yellow-break {
            background-color: #FFFF00 !important;
            color: #000000 !important;
            font-weight: 900 !important;
            text-align: center !important;
            letter-spacing: 0.1em !important;
        }

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

        /* Premium flyer-inspired discount style classes */
        .discount-section-title {
            color: #000000 !important;
            font-family: Arial, sans-serif;
            font-size: 24px !important;
            font-weight: 900 !important;
            text-align: center !important;
            letter-spacing: 0.05em !important;
            text-transform: uppercase !important;
            margin-top: 10px !important;
        }

        .discount-grid {
            display: grid !important;
            grid-template-columns: repeat(1, minmax(0, 1fr)) !important;
            gap: 24px !important;
            margin-top: 24px !important;
        }

        @media (min-width: 768px) {
            .discount-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        .discount-card {
            border: 3px solid #b8cece !important;
            border-radius: 20px !important;
            background-color: #f3f7f2 !important; /* Soft sage fill */
            padding: 24px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03) !important;
        }

        .discount-card-header {
            background-color: #065f46 !important;
            color: #ffffff !important;
            text-align: center !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            padding: 10px 16px !important;
            border-radius: 12px !important;
            font-size: 16px !important;
            letter-spacing: 0.05em !important;
            margin-bottom: 20px !important;
            box-shadow: 0 2px 4px rgba(6, 95, 70, 0.2) !important;
        }

        .discount-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding-bottom: 12px !important;
            margin-bottom: 12px !important;
            font-weight: 700 !important;
            font-size: 16.5px !important;
            color: #000000 !important;
        }

        .discount-row:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }

        .discount-check {
            color: #d97706 !important; /* Rich amber check icon */
            font-weight: 900 !important;
            margin-right: 8px !important;
            font-size: 18px !important;
        }

        .discount-value {
            color: #065f46 !important; /* forest green */
            font-weight: 900 !important;
            font-size: 19px !important;
        }

        .full-payment-badge {
            background-color: #FFFF00 !important;
            color: #000000 !important;
            border: 2px solid #b8cece !important;
            border-radius: 9999px !important;
            padding: 12px 24px !important;
            font-size: 32px !important;
            font-weight: 900 !important;
            display: inline-block !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
        }
    </style>
