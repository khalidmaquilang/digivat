<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BIR Official Receipt</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-white font-mono text-sm">
    <div class="max-w-4xl mx-auto p-8">
        <!-- Company Header -->
        <div class="text-center border-b-2 border-black pb-4 mb-6">
            <h1 class="text-xl font-bold mb-2">DIGIVAT CORPORATION</h1>
            <p class="text-base">Complete Business Address</p>
            <p class="text-base">City, Province, ZIP Code</p>
            <p class="text-base">TIN: {{ $taxRecord->business->tin_number }}</p>
            <p class="text-base font-semibold mt-2">BIR OFFICIAL RECEIPT</p>
        </div>

        <!-- Receipt Header -->
        <div class="grid grid-cols-2 gap-8 mb-6">
            <div>
                <p><strong>Receipt No:</strong> {{ $taxRecord->transaction_reference }}</p>
                <p><strong>Date:</strong>
                    {{ $taxRecord->sales_date?->format('M d, Y') ?? $taxRecord->created_at->format('M d, Y') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($taxRecord->status->value) }}</p>
            </div>
            <div>
                <p><strong>Valid Until:</strong> {{ $taxRecord->valid_until->format('M d, Y') }}</p>
                <p><strong>Category:</strong> {{ ucfirst(str_replace('_', ' ', $taxRecord->category_type->value)) }}</p>
            </div>
        </div>

        <!-- Items Table -->
        @if ($taxRecord->taxRecordItems->count() > 0)
            <div class="mb-6">
                <h3 class="text-lg font-bold mb-3 border-b border-gray-300 pb-1">ITEMS PURCHASED</h3>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b-2 border-black">
                            <th class="text-left py-2 px-1">DESCRIPTION</th>
                            <th class="text-right py-2 px-1">QTY</th>
                            <th class="text-right py-2 px-1">UNIT PRICE</th>
                            <th class="text-right py-2 px-1">DISCOUNT</th>
                            <th class="text-right py-2 px-1">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taxRecord->taxRecordItems as $item)
                            <tr class="border-b border-gray-200">
                                <td class="py-2 px-1">{{ $item->item_name }}</td>
                                <td class="text-right py-2 px-1">{{ number_format($item->quantity) }}</td>
                                <td class="text-right py-2 px-1">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-right py-2 px-1">₱{{ number_format($item->discount_amount, 2) }}</td>
                                <td class="text-right py-2 px-1">₱{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Tax Computation -->
        <div class="grid grid-cols-2 gap-8 mb-6">
            <div></div>
            <div class="border-t-2 border-black pt-4">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Gross Amount:</span>
                        <span class="font-semibold">₱{{ number_format($taxRecord->gross_amount, 2) }}</span>
                    </div>
                    @if ($taxRecord->order_discount > 0)
                        <div class="flex justify-between">
                            <span>Less: Discount:</span>
                            <span class="font-semibold">₱{{ number_format($taxRecord->order_discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between border-t border-gray-300 pt-2">
                        <span>Taxable Amount:</span>
                        <span class="font-semibold">₱{{ number_format($taxRecord->taxable_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>VAT (12%):</span>
                        <span class="font-semibold">₱{{ number_format($taxRecord->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t-2 border-black pt-2 text-lg font-bold">
                        <span>TOTAL AMOUNT:</span>
                        <span>₱{{ number_format($taxRecord->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- VAT Information -->
        <div class="border-t border-gray-300 pt-4 mb-6">
            <p class="text-center text-xs">
                <strong>VAT Reg. TIN:</strong> {{ $taxRecord->business->tin_number }}<br>
                This receipt shall be valid for five (5) years from the date of the ATP (Authority to Print)
            </p>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs border-t border-gray-300 pt-4">
            <p>This document is machine generated and does not require signature.</p>
            <p class="mt-2">Thank you for your business!</p>
            <p class="mt-4 font-semibold">{{ $taxRecord->id }}</p>
        </div>

        <!-- Print Button -->
        <div class="mt-8 text-center no-print">
            <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Print Receipt
            </button>
        </div>
    </div>

    <style>
        @media print {
            body {
                margin: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</body>

</html>
