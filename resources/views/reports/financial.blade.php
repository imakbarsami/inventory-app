<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 no-print">
                <form method="GET" action="{{ route('reports.financial') }}" id="report-form" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md font-bold text-sm hover:bg-indigo-700 transition">
                            Generate Report
                        </button>
                        
                        <a href="{{ route('reports.financial') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-md font-bold text-sm hover:bg-gray-300 transition">
                            Clear
                        </a>

                        <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-6 py-2 rounded-md font-bold text-sm hover:bg-gray-900 transition">
                            Print
                        </button>
                    </div>
                </form>
            </div>

            <div class="space-y-4 mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-blue-500">
            <div class="text-xs font-bold text-gray-400 uppercase">Total Sales</div>
            <div class="text-2xl font-black text-gray-800">৳{{ number_format($totalSales, 2) }}</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-emerald-500">
            <div class="text-xs font-bold text-gray-400 uppercase">Total Collected</div>
            <div class="text-2xl font-black text-emerald-600">৳{{ number_format($totalCollected, 2) }}</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-orange-500">
            <div class="text-xs font-bold text-gray-400 uppercase">Total Due</div>
            <div class="text-2xl font-black text-orange-600">৳{{ number_format($totalDue, 2) }}</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-purple-500">
            <div class="text-xs font-bold text-gray-400 uppercase">Total VAT</div>
            <div class="text-2xl font-black text-purple-600">৳{{ number_format($totalVat, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-red-400">
            <div class="text-xs font-bold text-gray-400 uppercase">Cost of Goods (COGS)</div>
            <div class="text-2xl font-black text-gray-800">৳{{ number_format($totalCogs, 2) }}</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-yellow-400">
            <div class="text-xs font-bold text-gray-400 uppercase">Total Discount</div>
            <div class="text-2xl font-black text-gray-800">৳{{ number_format($totalDiscount, 2) }}</div>
        </div>

        <div class="bg-white p-5 rounded-lg shadow-sm border-t-4 border-indigo-700">
            <div class="text-xs font-bold text-gray-400 uppercase">Net Profit</div>
            <div class="text-2xl font-black {{ $netProfit >= 0 ? 'text-indigo-800' : 'text-red-600' }}">
                ৳{{ number_format($netProfit, 2) }}
            </div>
        </div>
    </div>
</div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="font-bold text-lg text-gray-700 mb-4">Daily Sales Summary</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="py-3 px-4 text-sm font-bold text-gray-600">Date</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 text-right">Gross Amount</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 text-right">Discount</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 text-right">VAT</th>
                                <th class="py-3 px-4 text-sm font-bold text-gray-600 text-right">Net Sales</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($dailyReports as $report)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($report->sale_date)->format('d M, Y') }}</td>
                                <td class="py-3 px-4 text-sm text-right">BDT {{ number_format($report->gross, 2) }}</td>
                                <td class="py-3 px-4 text-sm text-right text-red-500">BDT {{ number_format($report->disc, 2) }}</td>
                                <td class="py-3 px-4 text-sm text-right">BDT {{ number_format($report->vt, 2) }}</td>
                                <td class="py-3 px-4 text-sm text-right font-bold text-indigo-700">BDT {{ number_format($report->net, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400 italic">No data available for the selected range.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .no-print, nav, header { display: none !important; }
            .py-12 { padding: 0 !important; }
            .max-w-7xl { max-width: 100% !important; width: 100% !important; }
            .shadow-sm { box-shadow: none !important; border: 1px solid #eee !important; }
        }
    </style>
</x-app-layout>