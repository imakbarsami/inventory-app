<x-app-layout>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
            background: #fff;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            .invoice-box { box-shadow: none; border: none; }
        }

        @media print {
        .no-print, nav, header { 
            display: none !important; 
        }
        .py-12 { 
            padding: 0 !important; 
        }
        .invoice-box { 
            box-shadow: none; 
            border: none; 
            max-width: 100%;
        }
}
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto mb-4 flex justify-end gap-2 no-print">
            <a href="{{ route('sales.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded shadow">Back</a>
            <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded shadow">Print Invoice</button>
        </div>

        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0">
                <tr class="top">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td style="font-size: 45px; line-height: 45px; color: #333; font-weight: bold;">
                                    INVOICE
                                </td>
                                <td>
                                    Invoice #: {{ $sale->invoice_no }}<br />
                                    Created: {{ \Carbon\Carbon::parse($sale->sale_date)->format('F d, Y') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    <strong>IFish Zone</strong><br />
                                    123 Quaish<br />
                                    Hathazari, Chittagong, 4337
                                </td>
                                <td>
                                    <strong>Customer Info:</strong><br />
                                    {{ $sale->customer->name }}<br />
                                    {{ $sale->customer->phone }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td>Item Description</td>
                    <td>Price</td>
                </tr>

                @foreach($sale->saleItems as $item)
                <tr class="item {{ $loop->last ? 'last' : '' }}">
                    <td>{{ $item->product->name }} (x{{ $item->quantity }})</td>
                    <td>BDT {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach

                <tr class="total">
                    <td></td>
                    <td>
                        <div style="margin-top: 20px;">
                            Sub-total: BDT {{ number_format($sale->gross_amount, 2) }} <br>
                            Discount: BDT {{ number_format($sale->discount, 2) }} <br>
                            VAT: BDT {{ number_format($sale->vat, 2) }} <br>
                            <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
                            <span style="font-size: 1.2em; color: #4F46E5;">Net Total: BDT {{ number_format($sale->net_amount, 2) }}</span> <br>
                            Paid: BDT {{ number_format($sale->paid_amount, 2) }} <br>
                            <span style="color: #DC2626;">Due: BDT {{ number_format($sale->due_amount, 2) }}</span>
                        </div>
                    </td>
                </tr>
            </table>
            
            <div style="text-align: center; margin-top: 50px; color: #999; font-style: italic;">
                Thank you for your business!
            </div>
        </div>
    </div>
</x-app-layout>