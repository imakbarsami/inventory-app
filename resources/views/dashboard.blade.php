<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Business Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 uppercase">Today's Sales</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">BDT {{ number_format($todaySales, 2) }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 uppercase">Inventory Value (at Cost)</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">BDT {{ number_format($inventoryValue, 2) }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6">
                        <div class="text-sm font-medium text-gray-500 uppercase">Current Cash Balance</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900">BDT {{ number_format($cashBalance, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">Sales Trend (Last 7 Days)</h3>
                    <canvas id="salesChart" height="200"></canvas>
                </div>

                <div class="bg-white p-6 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-700">Recent Sales</h3>
                        <a href="{{ route('sales.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-bold text-gray-500 uppercase">Invoice</th>
                                    <th class="px-4 py-2 text-xs font-bold text-gray-500 uppercase">Customer</th>
                                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentSales as $sale)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-indigo-600">{{ $sale->invoice_no }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $sale->customer->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-bold">BDT {{ number_format($sale->net_amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400 italic">No sales recorded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($lastSevenDays) !!},
                datasets: [{
                    label: 'Sales (BDT )',
                    data: {!! json_encode($salesData) !!},
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</x-app-layout>