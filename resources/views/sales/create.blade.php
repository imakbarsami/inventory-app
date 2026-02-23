<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('POS (Point of Sale) - New Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form method="POST" action="{{ route('sales.store') }}" id="pos-form">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 pb-6 border-b border-gray-200">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700">Select Customer <span class="text-red-500">*</span></label>
                                <select name="customer_id" id="customer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Choose Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="sale_date" class="block text-sm font-medium text-gray-700">Sale Date <span class="text-red-500">*</span></label>
                                <input type="date" name="sale_date" id="sale_date" value="{{ date('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="invoice_no" class="block text-sm font-medium text-gray-700">Invoice No.</label>
                                <input type="text" name="invoice_no" id="invoice_no" value="{{ $invoice_no ?? 'INV-AUTO' }}" readonly class="mt-1 block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm sm:text-sm text-gray-500 font-bold">
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Cart Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200" id="items-table">
                                    <thead class="bg-gray-800 text-white">
                                        <tr>
                                            <th class="py-2 px-4 text-left text-sm font-semibold">Product</th>
                                            <th class="py-2 px-4 text-left text-sm font-semibold w-32">Unit Price</th>
                                            <th class="py-2 px-4 text-left text-sm font-semibold w-32">Qty (Stock)</th>
                                            <th class="py-2 px-4 text-left text-sm font-semibold w-32">Sub Total</th>
                                            <th class="py-2 px-4 text-center text-sm font-semibold w-16">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-tbody">
                                        </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <button type="button" onclick="addRow()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    + Add Another Product
                                </button>
                            </div>
                        </div>

                       <div class="flex justify-end border-t border-gray-200 pt-6 mt-6">
                            <div class="w-full md:w-1/2 lg:w-1/3">

                                <!-- Gross -->
                                <div class="flex items-center justify-between py-3">
                                    <label class="text-sm font-medium text-gray-700 w-1/2">
                                        Gross Amount
                                    </label>
                                    <input type="number" id="gross_amount" readonly
                                        class="w-1/2 h-10 bg-gray-100 border border-gray-300 rounded-md px-3 text-right font-semibold">
                                </div>

                                <!-- Discount -->
                                <div class="flex items-center justify-between py-3">
                                    <label class="text-sm font-medium text-gray-700 w-1/2">
                                        Discount (-)
                                    </label>
                                    <input type="number" name="discount" id="discount"
                                        value="0" min="0" step="0.01"
                                        class="w-1/2 h-10 border border-gray-300 rounded-md px-3 text-right focus:ring-2 focus:ring-indigo-500 calculate-total">
                                </div>

                                <!-- VAT -->
                                <div class="flex items-center justify-between py-3">
                                    <label class="text-sm font-medium text-gray-700 w-1/2">
                                        VAT (+)
                                    </label>
                                    <input type="number" name="vat" id="vat"
                                        value="0" min="0" step="0.01"
                                        class="w-1/2 h-10 border border-gray-300 rounded-md px-3 text-right focus:ring-2 focus:ring-indigo-500 calculate-total">
                                </div>

                                <!-- Divider -->
                                <div class="border-t my-2"></div>

                                <!-- Net -->
                                <div class="flex items-center justify-between py-3">
                                    <label class="text-base font-bold text-gray-900 w-1/2">
                                        Net Amount
                                    </label>
                                    <input type="number" id="net_amount" readonly
                                        class="w-1/2 h-10 bg-indigo-50 border border-indigo-300 rounded-md px-3 text-right font-bold text-indigo-700">
                                </div>

                                <!-- Paid -->
                                <div class="flex items-center justify-between py-3">
                                    <label class="text-sm font-medium text-gray-700 w-1/2">
                                        Paid Amount <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="paid_amount" id="paid_amount"
                                        required min="0" step="0.01"
                                        class="w-1/2 h-10 border border-gray-300 rounded-md px-3 text-right font-semibold focus:ring-2 focus:ring-green-500 calculate-total">
                                </div>

                                <!-- Due -->
                                <div class="flex items-center justify-between py-3">
                                    <label class="text-sm font-bold text-red-600 w-1/2">
                                        Due Amount
                                    </label>
                                    <input type="number" id="due_amount" readonly
                                        class="w-1/2 h-10 bg-red-50 border border-red-300 rounded-md px-3 text-right font-bold text-red-600">
                                </div>

                                <!-- Button -->
                                <div class="pt-4">
                                    <button type="submit"
                                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-md shadow">
                                        Confirm Sale
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const products = @json($products);

        document.addEventListener('DOMContentLoaded', function() {
            addRow();
            document.querySelectorAll('.calculate-total').forEach(input => {
                input.addEventListener('input', calculateFinalTotals);
            });
        });

        function addRow() {
            const tbody = document.getElementById('items-tbody');
            const tr = document.createElement('tr');
            tr.className = 'border-b item-row hover:bg-gray-50';

            let productOptions = '<option value="">Select Product...</option>';
            products.forEach(p => {
                productOptions += `<option value="${p.id}" data-price="${p.sell_price}" data-stock="${p.stock}">${p.name} (Stock: ${p.stock})</option>`;
            });

            tr.innerHTML = `
                <td class="py-2 px-4">
                    <select name="product_id[]" required class="product-select w-full border-gray-300 rounded text-sm focus:ring-indigo-500">
                        ${productOptions}
                    </select>
                </td>
                <td class="py-2 px-4">
                    <input type="number" name="unit_price[]" required step="0.01" min="0" class="unit-price w-full border-gray-300 rounded text-sm text-right bg-gray-100" readonly>
                </td>
                <td class="py-2 px-4">
                    <input type="number" name="quantity[]" required min="1" value="1" class="qty w-full border-gray-300 rounded text-sm text-center focus:ring-indigo-500">
                    <small class="text-xs text-red-500 stock-warning hidden">Max stock reached!</small>
                </td>
                <td class="py-2 px-4">
                    <input type="number" class="row-subtotal w-full border-gray-300 rounded text-sm text-right bg-gray-100 font-bold" readonly>
                </td>
                <td class="py-2 px-4 text-center">
                    <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 font-bold">X</button>
                </td>
            `;

            tbody.appendChild(tr);

            const select = tr.querySelector('.product-select');
            const qtyInput = tr.querySelector('.qty');

            select.addEventListener('change', function() {
                updateRowData(tr);
            });

            qtyInput.addEventListener('input', function() {
                updateRowData(tr);
            });
        }

        function updateRowData(tr) {
            const select = tr.querySelector('.product-select');
            const priceInput = tr.querySelector('.unit-price');
            const qtyInput = tr.querySelector('.qty');
            const subtotalInput = tr.querySelector('.row-subtotal');
            const warning = tr.querySelector('.stock-warning');

            const selectedOption = select.options[select.selectedIndex];
            
            if(selectedOption.value) {
                const price = parseFloat(selectedOption.getAttribute('data-price'));
                const maxStock = parseInt(selectedOption.getAttribute('data-stock'));
                let qty = parseInt(qtyInput.value) || 0;

                if(qty > maxStock) {
                    qtyInput.value = maxStock;
                    qty = maxStock;
                    warning.classList.remove('hidden');
                } else {
                    warning.classList.add('hidden');
                }

                priceInput.value = price.toFixed(2);
                subtotalInput.value = (price * qty).toFixed(2);
            } else {
                priceInput.value = '';
                subtotalInput.value = '';
            }

            calculateFinalTotals();
        }

        function removeRow(btn) {
            const tbody = document.getElementById('items-tbody');
            if(tbody.children.length > 1) {
                btn.closest('tr').remove();
                calculateFinalTotals();
            } else {
                alert('You must have at least one product row.');
            }
        }

        function calculateFinalTotals() {
            let grossAmount = 0;
            
            document.querySelectorAll('.row-subtotal').forEach(input => {
                grossAmount += parseFloat(input.value) || 0;
            });

            document.getElementById('gross_amount').value = grossAmount.toFixed(2);

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const vat = parseFloat(document.getElementById('vat').value) || 0;

            const netAmount = (grossAmount - discount) + vat;
            document.getElementById('net_amount').value = netAmount.toFixed(2);

            const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
            const dueAmount = netAmount - paidAmount;
            
            document.getElementById('due_amount').value = dueAmount.toFixed(2);
        }
    </script>
</x-app-layout>