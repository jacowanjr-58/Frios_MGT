@extends('layouts.app')

@section('content')
<style>
    .btn-primary {
        background-color: #00abc7;
        border-color: #00abc7;
    }
    .btn-success {
        background-color: #7fd649;
        border-color: #7fd649;
    }
</style>
<div class="content-body default-height p-5 mt-5">
    <div class="container-fluid rounded border p-5 bg-white">
        <div class="row">
            <div class="col-md-12">
                <div style="float: right;">
                    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="container">
                <h1 style="font-size: 28px; font-weight: bold">Edit Invoice</h1>

                <form action="{{ route('franchise.invoice.update', $invoice->id) }}" method="POST" id="invoice-form">
                    @csrf
                    @method('PUT')

                    <div class="row mt-5">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name">Customer Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ old('name', $invoice->name) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="customer">Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->customer_id }}"
                                            {{ $customer->customer_id == $invoice->customer_id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Invoice Items Table --}}
                    <h4>Invoice Items</h4>
                    <table class="table table-bordered" id="items-table">
                        <thead>
                            <tr>
                                <th>Taxable</th>
                                <th>Flavor</th>
                                <th>Allocation Location</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $index => $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="items[{{ $index }}][taxable]" class="taxable-checkbox"
                                            {{ $item->taxable ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $item->flavor->name ?? 'N/A' }}</td>
                                    <td>{{ $item->inventory_allocation_id }}</td>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][flavor_id]" value="{{ $item->flavor_id }}">
                                        <input type="hidden" name="items[{{ $index }}][location]" value="{{ $item->inventory_allocation_id }}">
                                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" class="form-control form-control-sm" readonly>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][price]" value="{{ $item->unit_price }}">
                                        {{ $item->unit_price }}
                                    </td>
                                    <td class="item-total">{{ $item->quantity * $item->unit_price }}</td>
                                    <td class="remove-item">
                                        <button type="button" class="remove-item">
                                            <svg class="remove-item" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path class="remove-item" d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path class="remove-item"
                                                    d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z"
                                                    stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end">Subtotal</td>
                                <td><span id="subtotal">0.00</span></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr id="tax-row" style="display: none;">
                                <td colspan="4" class="text-end">Sales Tax</td>
                                <td><span id="tax-amount">0.00</span></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total</strong></td>
                                <td><span id="total">0.00</span></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    {{-- Tax Rate + Notes --}}
                    <div class="row mb-4">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4 flex">
                            Tax Rate (%):
                            <input type="number" class="form-control w-75" id="taxRate"
                                value="{{ $invoice->tax_price ?? $franchisee }}" min="0" max="100"
                                step="0.01" name="tax_price">
                        </div>
                        <div class="col-md-12">
                            <label for="Note">Note to customer</label>
                            <textarea name="note" id="note" class="form-control">{{ old('note', $invoice->note) }}</textarea>
                        </div>
                    </div>

                    {{-- Add Item Section --}}
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <select id="item-flavor" class="form-control">
                                <option value="">-- Select Flavor & Location --</option>
                                @foreach ($allocations as $alloc)
                                    <option value="{{ $alloc->fgp_item_id }}"
                                        data-price="{{ $alloc->case_cost }}"
                                        data-location="{{ $alloc->location }}">
                                        {{ $alloc->name }} ({{ $alloc->location }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="item-quantity" class="form-control" placeholder="Qty"
                                min="1" value="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary action-btn" id="add-item">Add Item</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Update Invoice</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsTableBody = document.querySelector('#items-table tbody');
            const subtotalEl = document.getElementById('subtotal');
            const taxRow = document.getElementById('tax-row');
            const taxAmountEl = document.getElementById('tax-amount');
            const totalEl = document.getElementById('total');
            const taxRateInput = document.getElementById('taxRate');

            // Function to update the totals
            function updateTotals() {
                let subtotal = 0;
                let tax = 0;
                const taxRate = parseFloat(taxRateInput.value) || 0;

                document.querySelectorAll('#items-table tbody tr').forEach(row => {
                    const itemTotal = parseFloat(row.querySelector('.item-total').textContent);
                    const checkbox = row.querySelector('.taxable-checkbox');
                    subtotal += itemTotal;
                    if (checkbox && checkbox.checked) {
                        tax += itemTotal * (taxRate / 100);
                    }
                });

                subtotalEl.textContent = subtotal.toFixed(2);

                if (tax > 0) {
                    taxRow.style.display = '';
                    taxAmountEl.textContent = tax.toFixed(2);
                    totalEl.textContent = (subtotal + tax).toFixed(2);
                } else {
                    taxRow.style.display = 'none';
                    taxAmountEl.textContent = '0.00';
                    totalEl.textContent = subtotal.toFixed(2);
                }
            }

            // Add item button click
            document.getElementById('add-item').addEventListener('click', () => {
                const flavorSelect = document.getElementById('item-flavor');
                const qtyInput = document.getElementById('item-quantity');
                const selectedOpt = flavorSelect.options[flavorSelect.selectedIndex];
                if (!selectedOpt.value) return;

                const flavorId = selectedOpt.value;
                const fullText = selectedOpt.text;
                const location = selectedOpt.dataset.location;
                const price = parseFloat(selectedOpt.dataset.price);
                const qty = parseInt(qtyInput.value, 10);
                const total = (price * qty).toFixed(2);

                const match = fullText.match(/^(.+?)\s*\((.*?)\)$/);
                const flavorName = match ? match[1] : fullText;
                const locationName = match ? match[2] : location;

                const index = document.querySelectorAll('#items-table tbody tr').length;

                const row = document.createElement('tr');
                row.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" name="items[${index}][taxable]" class="taxable-checkbox" checked>
                </td>
                <td>${flavorName}</td>
                <td>${locationName}</td>
                <td>
                    <input type="hidden" name="items[${index}][flavor_id]" value="${flavorId}">
                    <input type="hidden" name="items[${index}][location]" value="${locationName}">
                    <input type="number" name="items[${index}][quantity]" value="${qty}" class="form-control form-control-sm" readonly>
                </td>
                <td>
                    <input type="hidden" name="items[${index}][price]" value="${price}">
                    ${price.toFixed(2)}
                </td>
                <td class="item-total">${total}</td>
                <td class="remove-item">
                    <button type="button" class="remove-item">
                        <svg class="remove-item" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path class="remove-item" d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path class="remove-item"
                                  d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z"
                                  stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </td>
            `;

                itemsTableBody.appendChild(row);
                updateTotals();
            });

            // Handle remove button
            itemsTableBody.addEventListener('click', e => {
                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            });

            // When checkbox (taxable) changes
            itemsTableBody.addEventListener('change', e => {
                if (e.target.classList.contains('taxable-checkbox')) {
                    updateTotals();
                }
            });

            // When tax rate input changes
            taxRateInput.addEventListener('input', updateTotals);

            // Initial call
            updateTotals();
        });
</script>


@endsection
