@extends('layouts.app')

@section('content')
    <div class="content-body default-height p-5 mt-5">

        <div class="container-fluid rounded border p-5 bg-white">
            <div class="d-flex justify-content-between">
                <div class="container">
                    <h1>Create Invoice</h1>
                    <form action="{{ route('franchise.invoice.store') }}" method="POST" id="invoice-form">
                        @csrf

                        {{-- Customer Selection --}}
                        <div class="form-group mb-3">
                            <label for="customer">Customer</label>
                            <select name="customer_id" id="customer" class="form-control" required>
                                <option value="">-- Select Customer --</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->customer_ID }}"
                                        {{ old('customer_id') == $customer->customer_ID ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sales Tax Toggle --}}
                        <div class="form-check mb-3">
                            <input type="checkbox" id="apply_tax" name="apply_sales_tax" value="1"
                                class="form-check-input" {{ old('apply_sales_tax') ? 'checked' : '' }}>
                            <label for="apply_tax" class="form-check-label">
                                {{-- Apply Sales Tax ({{ $franchisee->sales_tax_rate }}%) --}}
                                Apply Sales Tax ({{ $franchisee }}%)
                            </label>
                        </div>

                        {{-- Invoice Items Table --}}
                        <h4>Invoice Items</h4>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Flavor</th>
                                    <th>Allocation Location</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Dynamic rows will be appended here --}}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end">Subtotal</td>
                                    <td><span id="subtotal">0.00</span></td>
                                    <td></td>
                                </tr>
                                <tr id="tax-row" style="display: none;">
                                    <td colspan="4" class="text-end">Sales Tax</td>
                                    <td><span id="tax-amount">0.00</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                                    <td><span id="total">0.00</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Add Item Controls --}}
                        <div class="row mb-4">
                            <div class="col-md-5">
                                <select id="item-flavor" class="form-control">
                                    <option value="">-- Select Flavor &amp; Location --</option>
                                    @foreach ($allocations as $alloc)
                                                <!-- Check if the flavor exists -->
                                                <option value="{{ $alloc->flavor->fgp_item_id }}"
                                                    data-price="{{ $alloc->flavor->case_cost }}"
                                                    data-location="{{ $alloc->location }}">
                                                    {{ $alloc->flavor->name }} ({{ $alloc->location }})
                                                </option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" id="item-quantity" class="form-control" placeholder="Qty"
                                    min="1" value="1">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="add-item" class="btn btn-primary">Add Item</button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Create Invoice</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTableBody = document.querySelector('#items-table tbody');
            const subtotalEl = document.getElementById('subtotal');
            const taxRow = document.getElementById('tax-row');
            const taxAmountEl = document.getElementById('tax-amount');
            const totalEl = document.getElementById('total');
            const applyTaxCheckbox = document.getElementById('apply_tax');
            const taxRate = parseFloat({{ $franchisee }});

            function updateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.item-total').forEach(el => {
                    subtotal += parseFloat(el.textContent);
                });
                subtotalEl.textContent = subtotal.toFixed(2);

                if (applyTaxCheckbox.checked) {
                    const tax = subtotal * (taxRate / 100);
                    taxAmountEl.textContent = tax.toFixed(2);
                    taxRow.style.display = '';
                    totalEl.textContent = (subtotal + tax).toFixed(2);
                } else {
                    taxRow.style.display = 'none';
                    totalEl.textContent = subtotal.toFixed(2);
                }
            }

            document.getElementById('add-item').addEventListener('click', () => {
                const flavorSelect = document.getElementById('item-flavor');
                const qtyInput = document.getElementById('item-quantity');
                const selectedOpt = flavorSelect.options[flavorSelect.selectedIndex];
                if (!selectedOpt.value) return;

                const flavorId = selectedOpt.value;
                const flavorName = selectedOpt.text;
                const location = selectedOpt.dataset.location;
                const price = parseFloat(selectedOpt.dataset.price);
                const qty = parseInt(qtyInput.value, 10);
                const total = (price * qty).toFixed(2);

                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${flavorName}</td>
            <td>${location}</td>
            <td>
                <input type="hidden" name="items[][flavor_id]" value="${flavorId}">
                <input type="hidden" name="items[][location]" value="${location}">
                <input type="number" name="items[][quantity]" value="${qty}" class="form-control form-control-sm" readonly>
            </td>
            <td>
                <input type="hidden" name="items[][price]" value="${price}">
                ${price.toFixed(2)}
            </td>
            <td class="item-total">${total}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove-item">Remove</button></td>
        `;
                itemsTableBody.appendChild(row);
                updateTotals();
            });

            itemsTableBody.addEventListener('click', e => {
                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            });

            applyTaxCheckbox.addEventListener('change', updateTotals);
        });
    </script>
@endsection
