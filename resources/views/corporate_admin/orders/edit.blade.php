{{-- filepath: resources/views/franchise_admin/orderpops/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Edit Order</h2>
                    <p>Edit order #{{ $order->getOrderNum() }} for {{ $franchise->business_name }}</p>
                </div>
                <a href="{{ route('franchise.orders', ['franchise' => $franchiseId]) }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i> Back to Orders
                </a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Order Details</h4>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="edit-order-form"
                                action="{{ route('franchise.orders.update', ['franchise' => $franchiseId, 'orders' => $order->id]) }}" method="POST">
                                @csrf
                                @method('PUT')

                                {{-- Franchise Selection (Only for Corporate Admin) --}}
                                @if(Auth::check() && Auth::user()->role == 'corporate_admin' && $allFranchises->count() > 0)
                                <div class="mb-4">
                                    <h5>Select Franchise <span class="text-danger">*</span></h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="franchise_id" class="form-label">Franchise</label>
                                            <select name="franchise_id" id="franchise_id" class="form-control form-control-sm select2" required>
                                                <option value="">Select a franchise...</option>
                                                @foreach($allFranchises as $franchiseOption)
                                                    <option value="{{ $franchiseOption->id }}" 
                                                        {{ $franchiseOption->id == $order->franchise_id ? 'selected' : '' }}
                                                        data-territory="{{ $franchiseOption->frios_territory_name ?? '' }}"
                                                        data-owner="{{ $franchiseOption->owner_email ?? '' }}">
                                                        {{ $franchiseOption->business_name }} 
                                                        @if($franchiseOption->frios_territory_name)
                                                            - {{ $franchiseOption->frios_territory_name }}
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Select which franchise this order is for</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-info mt-4">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fa fa-info-circle text-info me-2"></i>
                                                        <div>
                                                            <small class="text-muted">Selected Franchise:</small>
                                                            <div class="fw-bold" id="selected-franchise-info">{{ $order->franchise->business_name ?? 'Unknown' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                {{-- Order Items --}}
                                <div class="mb-4">
                                    <h5>Order Items <span class="text-danger">*</span></h5>
                                    <div class="table-responsive">
                                        <table class="table mb-4 fs-14 card-table" id="order-items-table">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Cost per Case</th>
                                                    <th>Quantity (Cases)</th>
                                                    <th>Total</th>
                                                    <th>Remove</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderItems as $index => $detail)
                                                    <tr data-index="{{ $index }}">
                                                        <td>
                                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $detail->id }}">
                                                            <select name="items[{{ $index }}][fgp_item_id]" class="form-control form-control-sm item-select select2" required data-index="{{ $index }}">
                                                                <option value="">Select an item</option>
                                                                @foreach($allItems as $item)
                                                                    <option value="{{ $item->id }}" 
                                                                        data-cost="{{ $item->case_cost }}"
                                                                        {{ $item->id == $detail->fgp_item_id ? 'selected' : '' }}>
                                                                        {{ $item->name }} - ${{ number_format($item->case_cost, 2) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control form-control-sm cost" step="0.01" min="0" readonly 
                                                                value="{{ old("items.$index.unit_cost", number_format($detail->unit_price, 2)) }}" data-index="{{ $index }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" name="items[{{ $index }}][unit_number]" class="form-control form-control-sm qty" min="1" required
                                                                value="{{ old("items.$index.unit_number", $detail->quantity) }}" data-index="{{ $index }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm total" readonly
                                                                value="${{ number_format($detail->unit_price * $detail->quantity, 2) }}" data-index="{{ $index }}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-success btn-sm" id="add-item-btn">
                                            <i class="fa fa-plus me-2"></i>Add Item
                                        </button>
                                    </div>
                                </div>

                                {{-- Shipping Information --}}
                                <div class="mb-4">
                                    <h5>Shipping Information</h5>
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_name" class="form-label">Recipient Name</label>
                                            <input type="text" name="ship_to_name" id="ship_to_name"
                                                class="form-control form-control-sm" value="{{ old('ship_to_name', $order->ship_to_name) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_phone" class="form-label">Phone Number</label>
                                            <input type="text" name="ship_to_phone" id="ship_to_phone"
                                                class="form-control form-control-sm" value="{{ old('ship_to_phone', $order->ship_to_phone) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_address1" class="form-label">Address 1</label>
                                            <input type="text" name="ship_to_address1" id="ship_to_address1"
                                                class="form-control form-control-sm" value="{{ old('ship_to_address1', $order->ship_to_address1) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_address2" class="form-label">Address 2</label>
                                            <input type="text" name="ship_to_address2" id="ship_to_address2"
                                                class="form-control form-control-sm" value="{{ old('ship_to_address2', $order->ship_to_address2) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_city" class="form-label">City</label>
                                            <input type="text" name="ship_to_city" id="ship_to_city"
                                                class="form-control form-control-sm" value="{{ old('ship_to_city', $order->ship_to_city) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_state" class="form-label">State</label>
                                            <input type="text" name="ship_to_state" id="ship_to_state"
                                                class="form-control form-control-sm" value="{{ old('ship_to_state', $order->ship_to_state) }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="ship_to_zip" class="form-label">ZIP Code</label>
                                            <input type="text" name="ship_to_zip" id="ship_to_zip"
                                                class="form-control form-control-sm" value="{{ old('ship_to_zip', $order->ship_to_zip) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold shadow">
                                        <i class="fa fa-save me-2"></i>Update Order
                                    </button>
                                    <a href="{{ route('franchise.orders', ['franchise' => $franchiseId]) }}"
                                        class="btn btn-secondary btn-lg ms-2">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />

<style scoped>
        .btn-danger,
        .btn-success {
            color: #fff !important;
            border: none !important;
        }

        .btn-danger {
            background-color: #dc3545 !important;
        }

        .btn-success {
            background-color: #198754 !important;
        }

        .btn-danger:hover,
        .btn-success:hover {
            opacity: 0.85;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        [type=button],
        [type=reset],
        [type=submit],
        button {
            background-color: #00ABC7 !important;
        }

        .table-responsive {
            overflow-x: clip !important;
        }

        /* Ensure consistent styling for all form controls */
        .item-select {
            height: calc(1.5em + 0.75rem + 2px);
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            appearance: none;
        }

        .item-select:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .total-row td {
            border-top: 2px solid #dee2e6;
            font-size: 1.1rem;
        }
        
        /* Select2 styling to match form controls */
        .select2-container .select2-selection--single {
            height: calc(1.5em + 0.75rem + 2px) !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
            line-height: calc(1.5em + 0.75rem) !important;
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.75rem) !important;
            right: 0.75rem !important;
        }
        
        .select2-dropdown {
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
        }

        .card-table th:first-child,
        .card-table td:first-child {
            padding-left: 0px !important;
        }
    </style>
@endpush

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
        window.fgpItemOptions = `{!! collect($allItems)->map(function ($item) {
        return '<option value="' . $item->id . '" data-cost="' . $item->case_cost . '">' . e($item->name) . ' - $' . number_format($item->case_cost, 2) . '</option>';
    })->implode('') !!}`;

        // Existing item indices for initialization
        window.existingItemIndices = [
            @foreach ($order->orderItems as $index => $detail)
            {{ $index }}{{ !$loop->last ? ',' : '' }}
            @endforeach
        ];

        document.addEventListener("DOMContentLoaded", function () {
            let itemIndex = {{ $order->orderItems->count() }};

            // Initialize existing Select2 dropdowns
            window.existingItemIndices.forEach(function(index) {
                initializeSelect2(index);
            });

            // Add new item row
            document.getElementById('add-item-btn').addEventListener('click', function () {
                let row = `
            <tr data-index="${itemIndex}">
                <td>
                     <select name="items[${itemIndex}][fgp_item_id]" class="form-control form-control-sm item-select select2" required data-index="${itemIndex}">
                         <option value="">Select an item</option>
                         ${window.fgpItemOptions}
                     </select>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_cost]" class="form-control form-control-sm cost" step="0.01" min="0" readonly data-index="${itemIndex}">
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][unit_number]" class="form-control form-control-sm qty" min="1" value="1" required data-index="${itemIndex}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm total" readonly value="$0.00" data-index="${itemIndex}">
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            `;
                document.querySelector('#order-items-table tbody').insertAdjacentHTML('beforeend', row);
                
                // Initialize Select2 for the new dropdown
                initializeSelect2(itemIndex);
                
                itemIndex++;
            });

            function initializeSelect2(index) {
                // Find the select element using a more specific selector
                const selectElement = document.querySelector(`select[name="items[${index}][fgp_item_id]"]`);
                if (selectElement && typeof $ !== 'undefined' && $.fn.select2) {
                    const $select = $(selectElement);
                    
                    // Initialize Select2
                    $select.select2({
                        theme: 'bootstrap4',
                        placeholder: 'Select an item',
                        allowClear: true,
                        width: '100%'
                    });

                    // Bind Select2 specific event
                    $select.on('select2:select', function (e) {
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        const cost = selectedOption.getAttribute('data-cost') || 0;
                        const index = e.target.getAttribute('data-index');

                        document.querySelector(`input[name="items[${index}][unit_cost]"]`).value = parseFloat(cost).toFixed(2);
                        calculateRowTotal(index);
                    });
                }
            }

            function bindEvents() {
                // Use event delegation for dynamically added elements
                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-item-btn') || e.target.closest('.remove-item-btn')) {
                        const btn = e.target.classList.contains('remove-item-btn') ? e.target : e.target.closest('.remove-item-btn');
                        const row = btn.closest('tr');
                        const index = row.getAttribute('data-index');
                        
                        // Destroy Select2 if it exists
                        const selectElement = row.querySelector('select');
                        if (selectElement && typeof $ !== 'undefined' && $.fn.select2) {
                            $(selectElement).select2('destroy');
                        }
                        
                        row.remove();
                        calculateGrandTotal();
                    }
                });

                document.addEventListener('change', function (e) {
                    if (e.target.classList.contains('item-select')) {
                        // Only handle this if Select2 is not initialized on this element
                        const $element = $(e.target);
                        if (!$element.hasClass('select2-hidden-accessible')) {
                            const selectedOption = e.target.options[e.target.selectedIndex];
                            const cost = selectedOption.getAttribute('data-cost') || 0;
                            const index = e.target.getAttribute('data-index');

                            document.querySelector(`input[name="items[${index}][unit_cost]"]`).value = parseFloat(cost).toFixed(2);
                            calculateRowTotal(index);
                        }
                    }
                });

                document.addEventListener('input', function (e) {
                    if (e.target.classList.contains('qty')) {
                        const index = e.target.getAttribute('data-index');
                        calculateRowTotal(index);
                    }
                });
            }

            function calculateRowTotal(index) {
                const costInput = document.querySelector(`input[name="items[${index}][unit_cost]"]`);
                const qtyInput = document.querySelector(`input[name="items[${index}][unit_number]"]`);
                const totalInput = document.querySelector(`input.total[data-index="${index}"]`);

                const cost = parseFloat(costInput.value) || 0;
                const qty = parseInt(qtyInput.value) || 0;
                const total = cost * qty;

                totalInput.value = '$' + total.toFixed(2);
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.total').forEach(input => {
                    const value = input.value.replace('$', '').replace(',', '');
                    grandTotal += parseFloat(value) || 0;
                });

                // Remove existing total row
                const existingTotalRow = document.querySelector('.total-row');
                if (existingTotalRow) {
                    existingTotalRow.remove();
                }

                // Count non-total rows
                const itemRows = document.querySelectorAll('#order-items-table tbody tr:not(.total-row)').length;

                // Add new total row if there are items
                if (itemRows > 0) {
                    const totalRow = `
                <tr class="total-row">
                    <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                    <td><strong>$${grandTotal.toFixed(2)}</strong></td>
                    <td></td>
                </tr>
                `;
                    document.querySelector('#order-items-table tbody').insertAdjacentHTML('beforeend', totalRow);
                }
            }

            function initializeFranchiseDropdown() {
                const franchiseSelect = document.getElementById('franchise_id');
                if (franchiseSelect && typeof $ !== 'undefined' && $.fn.select2) {
                    // Initialize Select2 for franchise dropdown
                    $('#franchise_id').select2({
                        theme: 'bootstrap4',
                        placeholder: 'Select a franchise...',
                        allowClear: true,
                        width: '100%'
                    });

                    // Handle franchise selection change
                    $('#franchise_id').on('select2:select', function (e) {
                        const selectedOption = e.target.options[e.target.selectedIndex];
                        const franchiseId = selectedOption.value;
                        const franchiseName = selectedOption.text.split(' - ')[0]; // Get business name without territory
                        
                        // Update the selected franchise info display
                        const infoElement = document.getElementById('selected-franchise-info');
                        if (infoElement) {
                            infoElement.textContent = franchiseName;
                        }
                        
                        // Update form action to use selected franchise
                        const form = document.getElementById('edit-order-form');
                        if (form && franchiseId) {
                            const baseUrl = '{{ route("franchise.orders.update", ["franchise" => ":franchiseId", "orders" => $order->id]) }}';
                            form.action = baseUrl.replace(':franchiseId', franchiseId);
                        }
                    });

                    // Handle clear selection
                    $('#franchise_id').on('select2:clear', function (e) {
                        const infoElement = document.getElementById('selected-franchise-info');
                        if (infoElement) {
                            infoElement.textContent = 'No franchise selected';
                        }
                    });
                }
            }

            // Initialize event listeners
            bindEvents();
            
            // Initialize franchise dropdown if it exists
            initializeFranchiseDropdown();

            // Calculate initial grand total for existing items
            calculateGrandTotal();
        });
    </script>
@endpush