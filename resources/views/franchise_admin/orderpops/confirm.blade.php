@extends('layouts.app')

@section('content')
    <style>
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
        }

        .card-header {
            background: linear-gradient(135deg, #00ABC7 0%, #00ABC7 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 20px;
            border: none;
        }

        .card-header h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background-color: #f1f3f4;
            border: none;
            font-weight: 600;
            color: #333;
            padding: 15px;
        }

        .table tbody td {
            padding: 12px 15px;
            border: none;
            border-bottom: 1px solid #e9ecef;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #fff;
            border-radius: 6px;
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .shipping-section {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .section-title {
            color: #333;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.1rem;
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
        }

        .address-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .total-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .total-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .payment-section {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            border: 2px dashed #e9ecef;
        }

        .card-title {
            color: #fff !important;
        }

        /* Professional Totals Styling */
        .optional-charge-row {
            background-color: #f8f9ff !important;
            border-left: 4px solid #17a2b8;
        }

        .optional-charge-row:hover {
            background-color: #e3f2fd !important;
            transition: background-color 0.3s ease;
        }

        .totals-separator td {
            padding: 0 !important;
        }

        .totals-separator hr {
            border: none;
            height: 2px;
            background: linear-gradient(to right, #00ABC7, #17a2b8);
            margin: 15px 0;
        }

        .subtotal-row,
        .charges-total-row {
            background-color: #f8f9fa !important;
            border-top: 1px solid #e9ecef;
        }

        .grand-total-row {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border-top: 3px solid #00ABC7;
            border-bottom: 3px solid #00ABC7;
        }

        .total-display {
            text-align: right;
            padding: 8px 12px;
        }

        .total-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
        }

        .grand-total-display {
            text-align: right;
            padding: 12px;
            background: rgba(0, 171, 199, 0.1);
            border-radius: 8px;
            border: 2px solid #00ABC7;
        }

        .grand-total-display h4 {
            font-size: 1.5rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        /* Badge styling for charges */
        .badge.bg-info {
            background: linear-gradient(135deg, #17a2b8, #00ABC7) !important;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Required charges styling */
        .required-charge {
            background-color: #fff3cd !important;
            border-color: #ffeaa7 !important;
            color: #856404 !important;
            font-weight: 600;
        }

        /* Layout alignment classes */
        .form-layout-table {
            table-layout: fixed;
            width: 100%;
        }

        .order-content-cell {
            vertical-align: top;
            padding-right: 15px;
        }

        .shipping-content-cell {
            vertical-align: top;
            padding-left: 15px;
        }
    </style>
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">
            <div class="order-confirm-container">

                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0 text-dark">Dashboard \</h2>
                        <p class="text-muted text-white">Confirm Order</p>
                    </div>
                    <a href="{{ route('franchise.orderpops.create' , ['franchise' => $franchise]) }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-2"></i> Back to Items
                    </a>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Confirm Order</h4>
                            </div>
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form id="stripe-payment-form" method="POST"
                                    action="{{ route('franchise.orderpops.store', ['franchise' => $franchise]) }}">
                                    @csrf
                                    <input type="hidden" name="franchise_id" value="{{ $franchise }}" id="franchise_id">
                                    <table class="form-head mb-4 form-layout-table">
                                        <tr>
                                            <td width="60%" class="order-content-cell">
                                                <!--   Order Contents        -->
                                                <div class="table-responsive">
                                                    <table class="table mb-4 fs-14 card-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Item Name</th>
                                                                <th>Qty</th>
                                                                <th>Cost</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {{-- Items will be populated via JavaScript --}}

                                                            @foreach ($requiredCharges as $charge)
                                                                <tr>
                                                                    <td colspan="3"><strong>{{ $charge->charge_name }}</strong>
                                                                        <small>(Required)</small>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control required-charge"
                                                                            readonly
                                                                            value="{{ $charge->charge_type == 'percentage' ? $charge->charge_price . '%' : $charge->charge_price }}"
                                                                            data-charge="{{ $charge->charge_price }}"
                                                                            data-charge-type="{{ $charge->charge_type }}">
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                            

                                                                                                        @foreach ($optionalCharges as $charge)
                                                <tr class="optional-charge-row">
                                                    <td colspan="3">
                                                        <div class="form-check d-flex align-items-center">
                                                            <input class="form-check-input optional-charge me-3" type="checkbox" 
                                                                id="charge_{{ $charge->id }}"
                                                                name="optional_charges[]" 
                                                                value="{{ $charge->charge_price }}"
                                                                data-charge="{{ $charge->charge_price }}" 
                                                                data-charge-type="{{ $charge->charge_type }}" 
                                                                {{ in_array($charge->charge_price, old('optional_charges', [])) ? 'checked' : '' }}
                                                                onchange="updateChargeCalculations()">
                                                            <label class="form-check-label d-flex align-items-center" for="charge_{{ $charge->id }}">
                                                                <i class="fa fa-plus-circle text-info me-2"></i>
                                                                <div>
                                                                    <strong>{{ $charge->charge_name }}</strong>
                                                                    <small class="text-muted d-block">(Optional)</small>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info text-white px-3 py-2">
                                                            {{ $charge->charge_type == 'percentage' ? $charge->charge_price . '%' : '$' . number_format($charge->charge_price, 2) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach

                                                            {{-- Professional Totals Section --}}
                                                            <!-- <tr class="totals-separator">
                                                                    <td colspan="4"><hr class="my-2"></td>
                                                                </tr> -->
                                                            <tr class="subtotal-row">
                                                                <td colspan="3" class="text-end fw-bold text-muted">Items
                                                                    Subtotal:</td>
                                                                <td>
                                                                    <div class="total-display">
                                                                        <span class="total-amount"
                                                                            id="subtotalDisplay">$0.00</span>
                                                                        <input type="hidden" id="subtotal" name="subtotal"
                                                                            value="0">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="charges-total-row">
                                                                <td colspan="3" class="text-end fw-bold text-muted">
                                                                    Additional Charges:</td>
                                                                <td>
                                                                    <div class="total-display">
                                                                        <span class="total-amount"
                                                                            id="chargesDisplay">$0.00</span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="grand-total-row">
                                                                <td colspan="3" class="text-end">
                                                                    <h5 class="mb-0 text-primary fw-bold">Grand Total:</h5>
                                                                </td>
                                                                <td>
                                                                    <div class="grand-total-display">
                                                                        <h4 class="mb-0 text-primary fw-bold"
                                                                            id="grandTotalDisplay">$0.00</h4>
                                                                        <input type="hidden" id="grandTotal"
                                                                            name="grandTotal" value="0">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>

                                            <!-- Shipping -->
                                            <td class="shipping-section shipping-content-cell">
                                                <h5 class="section-title">
                                                    <i class="fa fa-shipping-fast me-2"></i>Shipping Information
                                                </h5>

                                                <div class="address-buttons">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                                        onclick="autofillFranchisee()">
                                                        <i class="fa fa-building me-1"></i>Use Franchise Address
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                                        onclick="clearShippingFields()">
                                                        <i class="fa fa-times me-1"></i>Clear Fields
                                                    </button>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="customer_id" class="form-label fw-bold">Choose Customer
                                                        (optional)</label>
                                                    <select name="customer_id" id="customer_id"
                                                        class="form-select form-control select2"
                                                        onchange="autofillFromCustomer(this.value)">
                                                        <option value="">-- Select Customer --</option>
                                                        @foreach($customers as $customer)
                                                            <option value="{{ $customer->id }}">{{ $customer->name }} -
                                                                {{ $customer->address1 }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label for="ship_to_name" class="form-label">Recipient Name</label>
                                                        <input type="text" name="ship_to_name" id="ship_to_name"
                                                            class="form-control" value="{{ old('ship_to_name') }}"
                                                            placeholder="Enter recipient name">
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="ship_to_address1" class="form-label">Address Line
                                                            1</label>
                                                        <input type="text" name="ship_to_address1" id="ship_to_address1"
                                                            class="form-control" value="{{ old('ship_to_address1') }}"
                                                            placeholder="Street address">
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="ship_to_address2" class="form-label">Address Line 2
                                                            (Optional)</label>
                                                        <input type="text" name="ship_to_address2" id="ship_to_address2"
                                                            class="form-control" value="{{ old('ship_to_address2') }}"
                                                            placeholder="Apt, suite, unit, etc.">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="ship_to_city" class="form-label">City</label>
                                                        <input type="text" name="ship_to_city" id="ship_to_city"
                                                            class="form-control" value="{{ old('ship_to_city') }}"
                                                            placeholder="City">
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label for="ship_to_state" class="form-label">State</label>
                                                        <input type="text" name="ship_to_state" id="ship_to_state"
                                                            class="form-control" value="{{ old('ship_to_state') }}"
                                                            placeholder="State">
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label for="ship_to_zip" class="form-label">ZIP Code</label>
                                                        <input type="text" name="ship_to_zip" id="ship_to_zip"
                                                            class="form-control" value="{{ old('ship_to_zip') }}"
                                                            placeholder="ZIP">
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="ship_to_phone" class="form-label">Phone Number</label>
                                                        <input type="tel" name="ship_to_phone" id="ship_to_phone"
                                                            class="form-control" value="{{ old('ship_to_phone') }}"
                                                            placeholder="Phone number">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <div>
                                        <!-- Right Column: Payment Info -->
                                        <div class="payment-section">
                                            <h5 class="section-title">
                                                <i class="fa fa-credit-card me-2"></i>Payment Information (Optional)
                                            </h5>
                                            <p class="text-muted mb-3">Complete payment now or leave blank to generate an
                                                invoice</p>

                                            <div class="mb-3">
                                                <label for="cardholder-name" class="form-label">Cardholder Name</label>
                                                <input type="text" id="cardholder-name" name="cardholder_name"
                                                    class="form-control" placeholder="Name on card">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Card Number</label>
                                                <div id="card-number-element" class="form-control"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Expiration</label>
                                                    <div id="card-expiry-element" class="form-control"></div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">CVC</label>
                                                    <div id="card-cvc-element" class="form-control"></div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="payment_reference" class="form-label">Payment Reference</label>
                                                <input type="text" name="payment_reference" class="form-control"
                                                    placeholder="Optional reference number">
                                            </div>
                                            <div id="card-errors" class="text-danger mb-3"></div>
                                            <input type="hidden" name="stripeToken" id="stripeToken">
                                        </div>
                                    </div>

                                    <input type="hidden" name="is_paid" id="is_paid" value="0">
                                    <div class="mt-4 text-center">
                                        <button type="submit" id="submit-button"
                                            class="btn btn-primary btn-lg fw-bold shadow-lg px-5">
                                            <i class="fa fa-check-circle me-2"></i>Confirm Order
                                        </button>
                                        <p class="text-muted mt-2 small text-white">
                                            By confirming, you agree to place this order with the specified details.
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const stripe = Stripe("{{ env('STRIPE_PUBLIC_KEY') }}");
            const elements = stripe.elements();

            const cardNumber = elements.create('cardNumber');
            const cardExpiry = elements.create('cardExpiry');
            const cardCvc = elements.create('cardCvc');

            cardNumber.mount('#card-number-element');
            cardExpiry.mount('#card-expiry-element');
            cardCvc.mount('#card-cvc-element');

            const cardholderName = document.getElementById('cardholder-name');
            const form = document.getElementById('stripe-payment-form');
            const submitButton = document.getElementById('submit-button');
            const errorElement = document.getElementById('card-errors');
            const tokenInput = document.getElementById('stripeToken');
            const isPaidInput = document.getElementById('is_paid');

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // If no card number entered, proceed without Stripe
                if (cardholderName.value.trim() === "") {
                    form.submit();  // Allow fallback to invoice logic
                    return;
                }

                submitButton.disabled = true;

                // Try to create a Stripe token
                const { token, error } = await stripe.createToken(cardNumber, {
                    name: cardholderName.value
                });

                if (error) {
                    errorElement.textContent = error.message;
                    submitButton.disabled = false;
                } else {
                    tokenInput.value = token.id;
                    isPaidInput.value = "1";
                    form.submit();
                }
            });
        });
    </script>

    <script>
        function calculateTotals() {
            let subtotal = 0;

            // Calculate items subtotal
            document.querySelectorAll('.qty').forEach((el, index) => {
                let qty = parseFloat(el.value) || 0;
                let cost = parseFloat(document.querySelector(`.cost[data-index='${index}']`).value) || 0;
                let total = qty * cost;
                document.querySelector(`.total[data-index='${index}']`).value = total.toFixed(2);
                subtotal += total;
            });

            // Calculate required charges
            let requiredChargeTotal = 0;
            document.querySelectorAll('.required-charge').forEach(el => {
                let charge = parseFloat(el.dataset.charge) || 0;
                let type = el.dataset.chargeType;
                if (type === 'percentage') {
                    requiredChargeTotal += (subtotal * charge) / 100;
                } else {
                    requiredChargeTotal += charge;
                }
            });

            // Calculate optional charges (only checked ones)
            let optionalChargeTotal = 0;
            document.querySelectorAll('.optional-charge:checked').forEach(el => {
                let charge = parseFloat(el.dataset.charge) || 0;
                let type = el.dataset.chargeType;
                if (type === 'percentage') {
                    optionalChargeTotal += (subtotal * charge) / 100;
                } else {
                    optionalChargeTotal += charge;
                }
            });

            // Calculate totals
            let totalCharges = requiredChargeTotal + optionalChargeTotal;
            let grandTotal = subtotal + totalCharges;

            // Update displays with professional formatting
            if (document.getElementById('subtotalDisplay')) {
                document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            }
            if (document.getElementById('chargesDisplay')) {
                document.getElementById('chargesDisplay').textContent = '$' + totalCharges.toFixed(2);
            }
            if (document.getElementById('grandTotalDisplay')) {
                document.getElementById('grandTotalDisplay').textContent = '$' + grandTotal.toFixed(2);
            }

            // Update hidden fields for form submission
            if (document.getElementById('subtotal')) {
                document.getElementById('subtotal').value = subtotal.toFixed(2);
            }
            if (document.getElementById('grandTotal')) {
                document.getElementById('grandTotal').value = grandTotal.toFixed(2);
            }
        }

        // Function to update calculations when charges change
        function updateChargeCalculations() {
            calculateTotals();
        }

        // Enhanced event binding
        function attachCalculationEvents() {
            document.querySelectorAll('.qty, .cost').forEach(el => {
                el.addEventListener('input', calculateTotals);
                el.addEventListener('change', calculateTotals);
            });
            calculateTotals();
        }
    </script>

    <script>
        const customers = @json($customers);
        const franchisee = @json($franchisee);

        // Simple test function to check sessionStorage
        function testSessionStorage() {
            console.log('=== SessionStorage Test ===');
            console.log('All sessionStorage keys:', Object.keys(sessionStorage));

            const orderItems = sessionStorage.getItem('orderItems');
            console.log('Raw orderItems data:', orderItems);

            if (orderItems) {
                try {
                    const parsed = JSON.parse(orderItems);
                    console.log('Parsed orderItems:', parsed);
                    console.log('Number of items:', parsed.length);
                    return parsed;
                } catch (e) {
                    console.error('Parse error:', e);
                }
            }

            return null;
        }

        // Debug function to monitor customer dropdown changes
        function monitorCustomerDropdown() {
            const customerDropdown = $('#customer_id');

            if (customerDropdown.length > 0) {
                // Monitor all change events
                customerDropdown.on('change', function (e) {
                    console.log('🔍 Customer dropdown changed:', {
                        value: $(this).val(),
                        trigger: e.originalEvent ? 'user' : 'programmatic',
                        stackTrace: new Error().stack
                    });
                });

                // Monitor Select2 events
                customerDropdown.on('select2:select', function (e) {
                    console.log('✅ Select2 selection:', e.params.data);
                });

                customerDropdown.on('select2:clear', function (e) {
                    console.log('❌ Select2 cleared:', e);
                });

                console.log('👀 Customer dropdown monitoring enabled');
            }
        }

        // Debug function to test Select2 functionality
        function debugSelect2() {
            const customerDropdown = $('#customer_id');
            console.log('=== Select2 Debug ===');
            console.log('Dropdown exists:', customerDropdown.length > 0);
            console.log('Has select2 class:', customerDropdown.hasClass('select2'));
            console.log('Current value:', customerDropdown.val());
            console.log('Is Select2 initialized:', customerDropdown.hasClass('select2-hidden-accessible'));

            // Try to set a test value
            if (customerDropdown.length > 0) {
                const testValue = customerDropdown.find('option:first').val();
                console.log('Setting test value:', testValue);
                customerDropdown.val(testValue).trigger('change');
                console.log('Value after setting:', customerDropdown.val());
            }
        }

        // Load items from sessionStorage on page load
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Page loaded, testing sessionStorage...');

            // Start monitoring customer dropdown changes
            setTimeout(function () {
                monitorCustomerDropdown();
            }, 500);

            // Add debug buttons for testing

            // Test sessionStorage first
            const testItems = testSessionStorage();

            if (testItems && testItems.length > 0) {
                console.log('SessionStorage test passed, loading items...');
                loadItemsFromSessionStorage();
            } else {
                console.error('SessionStorage test failed!');
                // Add a button to manually test
                const button = document.createElement('button');
                button.textContent = 'Test SessionStorage';
                button.onclick = testSessionStorage;
                button.style.position = 'fixed';
                button.style.top = '10px';
                button.style.right = '10px';
                button.style.zIndex = '9999';
                button.style.padding = '10px';
                button.style.backgroundColor = '#007bff';
                button.style.color = 'white';
                button.style.border = 'none';
                button.style.cursor = 'pointer';
                document.body.appendChild(button);

                alert('No items found. A test button has been added to check sessionStorage.');
            }

            // Add form submission debugging
            const form = document.getElementById('stripe-payment-form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    console.log('Form is being submitted. Checking form data...');

                    // Check for items inputs
                    const itemInputs = document.querySelectorAll('input[name*="[fgp_item_id]"]');
                    console.log('Found ' + itemInputs.length + ' fgp_item_id inputs');

                    let hasValidItems = false;
                    itemInputs.forEach(function (input, index) {
                        console.log('Input ' + index + ' name: ' + input.name + ', value: "' + input.value + '"');
                        if (input.value && input.value.trim() !== '') {
                            hasValidItems = true;
                        }
                    });

                    if (!hasValidItems) {
                        console.error('No valid item IDs found in form!');
                        e.preventDefault();
                        alert('Error: No items found in form. Please go back and select items again.');
                        return false;
                    }

                    console.log('Form validation passed, submitting...');
                });
            }
        });

        function loadItemsFromSessionStorage() {
            console.log('loadItemsFromSessionStorage called');

            const orderItems = sessionStorage.getItem('orderItems');
            console.log('SessionStorage orderItems:', orderItems);

            if (!orderItems) {
                console.error('No items found in sessionStorage!');
                alert('No items found in session. Redirecting back to item selection.');
                window.location.href = "{{ route('franchise.orderpops.index', ['franchise' => $franchise]) }}";
                return;
            }

            let items;
            try {
                items = JSON.parse(orderItems);
                console.log('Parsed items from sessionStorage:', items);
                console.log('Items length:', items.length);
            } catch (e) {
                console.error('Error parsing sessionStorage data:', e);
                alert('Error loading items. Please go back and try again.');
                return;
            }

            if (!items || items.length === 0) {
                console.error('Items array is empty!');
                alert('No items to display. Please go back and select items.');
                return;
            }

            // Debug each item to check if ID is present
            items.forEach(function (item, index) {
                console.log('Item ' + index + ':', {
                    id: item.id,
                    name: item.name,
                    hasId: !!item.id,
                    idType: typeof item.id
                });
            });

            populateOrderTable(items);

            // Ensure totals are calculated after a delay to allow DOM updates
            setTimeout(function () {
                calculateTotals();
                console.log('Initial totals calculated after page load');
            }, 200);
        }

        function populateOrderTable(items) {
            const tbody = document.querySelector('.table tbody');
            if (!tbody) {
                console.error('Table tbody not found!');
                return;
            }

            console.log('Populating order table with items:', items);

            // Clear existing item rows except charge rows and totals rows
            const existingRows = tbody.querySelectorAll('tr');
            existingRows.forEach(function (row) {
                if (!row.querySelector('.required-charge') &&
                    !row.querySelector('.optional-charge') &&
                    !row.classList.contains('totals-separator') &&
                    !row.classList.contains('subtotal-row') &&
                    !row.classList.contains('charges-total-row') &&
                    !row.classList.contains('grand-total-row')) {
                    row.remove();
                }
            });

            // Find insertion point (before required charges)
            const firstChargeRow = tbody.querySelector('.required-charge, .optional-charge');
            const insertionPoint = firstChargeRow ? firstChargeRow.closest('tr') : null;

            // Process each item
            for (let index = 0; index < items.length; index++) {
                const item = items[index];
                console.log('Processing item ' + index + ':', item);
                console.log('Item ID: ' + item.id + ', Name: ' + item.name);

                // Validate item has required data
                if (!item.id) {
                    console.error('Item ' + index + ' missing ID:', item);
                    alert('Error: Item ' + (item.name || 'Unknown') + ' is missing ID. Please go back and reselect items.');
                    return;
                }

                const row = document.createElement('tr');

                // Create each cell separately for better control
                const nameCell = document.createElement('td');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'items[' + index + '][fgp_item_id]';
                hiddenInput.value = item.id;
                nameCell.appendChild(hiddenInput);

                const nameStrong = document.createElement('strong');
                nameStrong.textContent = item.name || 'Unknown Item';
                nameCell.appendChild(nameStrong);

                const qtyCell = document.createElement('td');
                const qtyInput = document.createElement('input');
                qtyInput.type = 'number';
                qtyInput.name = 'items[' + index + '][unit_number]';
                qtyInput.className = 'form-control qty';
                qtyInput.min = '1';
                qtyInput.value = item.quantity || 1;
                qtyInput.setAttribute('data-index', index);
                qtyCell.appendChild(qtyInput);

                const costCell = document.createElement('td');
                const costInput = document.createElement('input');
                costInput.type = 'number';
                costInput.name = 'items[' + index + '][unit_cost]';
                costInput.className = 'form-control cost';
                costInput.step = '0.01';
                costInput.value = (item.priceValue || 0).toFixed(2);
                costInput.setAttribute('data-index', index);
                costCell.appendChild(costInput);

                const totalCell = document.createElement('td');
                const totalInput = document.createElement('input');
                totalInput.type = 'text';
                totalInput.className = 'form-control total';
                totalInput.readOnly = true;
                totalInput.value = '0.00';
                totalInput.setAttribute('data-index', index);
                totalCell.appendChild(totalInput);

                row.appendChild(nameCell);
                row.appendChild(qtyCell);
                row.appendChild(costCell);
                row.appendChild(totalCell);

                if (insertionPoint) {
                    tbody.insertBefore(row, insertionPoint);
                } else {
                    tbody.appendChild(row);
                }

                console.log('Created form input for item ' + index + ' with ID: ' + item.id);
            }

            // Verify form inputs were created
            setTimeout(function () {
                const itemIdInputs = document.querySelectorAll('input[name*="[fgp_item_id]"]');
                console.log('Created ' + itemIdInputs.length + ' fgp_item_id inputs:');
                itemIdInputs.forEach(function (input) {
                    console.log(input.name + ': "' + input.value + '"');
                });

                if (itemIdInputs.length === 0) {
                    console.error('No fgp_item_id inputs were created!');
                    alert('Error: Failed to create form inputs. Please refresh and try again.');
                }
            }, 100);

            // Re-attach event listeners and calculate totals
            attachCalculationEvents();
        }

        // Enhanced event attachment
        function attachCalculationEvents() {
            // Remove existing listeners to prevent duplicates
            document.querySelectorAll('.qty, .cost').forEach(el => {
                el.removeEventListener('input', calculateTotals);
                el.removeEventListener('change', calculateTotals);
            });

            // Add fresh listeners
            document.querySelectorAll('.qty, .cost').forEach(el => {
                el.addEventListener('input', calculateTotals);
                el.addEventListener('change', calculateTotals);
            });

            // Calculate totals after a brief delay
            setTimeout(() => {
                calculateTotals();
                console.log('Totals calculation triggered');
            }, 100);
        }

        function autofillFromCustomer(customerId) {
            // Fix: Use 'id' instead of 'customer_id' as per database schema
            const customer = customers.find(c => c.id == parseInt(customerId));
            if (!customer) {
                console.log('Customer not found for ID:', customerId);
                console.log('Available customers:', customers);
                // If no customer found, use route franchise ID
                document.getElementById('franchise_id').value = {{ $franchise }};
                return;
            }

            console.log('Found customer:', customer);

            // Update franchise_id to customer's franchise_id
            if (customer.franchise_id) {
                document.getElementById('franchise_id').value = customer.franchise_id;
                console.log('✅ Updated franchise_id to customer franchise:', customer.franchise_id);
            } else {
                // Fallback to route franchise ID if customer has no franchise_id
                document.getElementById('franchise_id').value = {{ $franchise }};
                console.log('⚠️ Customer has no franchise_id, using route franchise:', {{ $franchise }});
            }

            fillShippingFields({
                name: customer.name || '',
                address1: customer.address1 || '',
                address2: customer.address2 || '',
                city: '', // City field doesn't exist in customers table
                state: customer.state || '',
                zip: customer.zip_code || '',
                phone: customer.phone || '',
            });
        }

        function autofillFranchisee() {
            if (!franchisee) return;

            console.log('🏢 Using franchise address and clearing customer selection');

            // Clear customer dropdown first
            const customerDropdown = $('#customer_id');
            customerDropdown.val('').trigger('change');
            
            // If Select2 is initialized, clear it properly
            if (customerDropdown.hasClass('select2-hidden-accessible')) {
                customerDropdown.select2('val', '');
            }
            
            console.log('✅ Customer dropdown cleared');

            // Reset franchise_id to route franchise ID (since no customer is selected)
            const routeFranchiseId = {{ $franchise }};
            document.getElementById('franchise_id').value = routeFranchiseId;
            console.log('✅ Franchise ID reset to route franchise:', routeFranchiseId);

            // Fill franchise address fields
            fillShippingFields({
                name: franchisee.business_name ?? '',
                address1: franchisee.address1 ?? '',
                address2: franchisee.address2 ?? '',
                city: franchisee.city ?? '',
                state: franchisee.state ?? '',
                zip: franchisee.zip_code ?? '',
                phone: franchisee.phone ?? '',
            });
            
            console.log('✅ Franchise address filled');
        }

        function fillShippingFields({ name, address1, address2, city, state, zip, phone }) {
            document.getElementById('ship_to_name').value = name ?? '';
            document.getElementById('ship_to_address1').value = address1 ?? '';
            document.getElementById('ship_to_address2').value = address2 ?? '';
            document.getElementById('ship_to_city').value = city ?? '';
            document.getElementById('ship_to_state').value = state ?? '';
            document.getElementById('ship_to_zip').value = zip ?? '';
            document.getElementById('ship_to_phone').value = phone ?? '';
        }

        function clearShippingFields() {
            console.log('🧹 Clearing all shipping fields and customer selection');

            // Clear customer dropdown
            const customerDropdown = $('#customer_id');
            customerDropdown.val('').trigger('change');
            
            // If Select2 is initialized, clear it properly
            if (customerDropdown.hasClass('select2-hidden-accessible')) {
                customerDropdown.select2('val', '');
            }
            
            console.log('✅ Customer dropdown cleared');

            // Reset franchise_id to route franchise ID (since no customer is selected)
            const routeFranchiseId = {{ $franchise }};
            document.getElementById('franchise_id').value = routeFranchiseId;
            console.log('✅ Franchise ID reset to route franchise:', routeFranchiseId);

            // Clear shipping fields
            document.getElementById('ship_to_name').value = '';
            document.getElementById('ship_to_address1').value = '';
            document.getElementById('ship_to_address2').value = '';
            document.getElementById('ship_to_city').value = '';
            document.getElementById('ship_to_state').value = '';
            document.getElementById('ship_to_zip').value = '';
            document.getElementById('ship_to_phone').value = '';
            
            console.log('✅ Shipping fields cleared');
        }
    </script>
@endpush

<style scoped>
    .btn {
        color: #fff;
        background-color: #f84b6a !important;
        border-color: #f84b6a !important;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background-color: #e63956 !important;
        border-color: #e63956 !important;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        border: none !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
    }

    #submit-button {
        border-radius: 25px !important;
        padding: 12px 30px !important;
        font-size: 16px !important;
        letter-spacing: 0.5px !important;
    }
</style>