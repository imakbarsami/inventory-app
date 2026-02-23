<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales & Invoices') }}
            </h2>
            <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                + New Sale (POS)
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <form method="GET" action="{{ route('sales.index') }}" class="flex flex-wrap items-end gap-4">
                            
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search Customer</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Phone..." class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                    Filter
                                </button>
                                
                                @if(request('search') || request('start_date') || request('end_date'))
                                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table class="w-full whitespace-no-wrap bg-white border border-gray-200">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Invoice # ↕</a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Date ↕</a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'customer_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Customer ↕</a>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'net_amount', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}">Net Amount ↕</a>
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($sales as $sale)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-bold text-indigo-600">{{ $sale->invoice_no }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $sale->created_at->format('d M Y, h:i A') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                                            <div class="text-xs text-gray-500">{{ $sale->customer->phone ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-center text-gray-900">
                                            <span class="bg-gray-200 text-gray-800 py-1 px-2 rounded-full text-xs font-bold">
                                                {{ $sale->saleItems->count() }} Types
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-right font-bold text-gray-900">৳{{ number_format($sale->net_amount, 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-center">
                                            <a href="{{ route('sales.show', $sale->id) }}" class="text-white bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-xs font-semibold">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <div class="text-lg font-medium mb-2">No sales found.</div>
                                            <p class="text-sm">Click on "+ New Sale (POS)" to create your first invoice.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>