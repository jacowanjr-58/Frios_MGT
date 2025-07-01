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
    .card-title{
        color: #fff !important;
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
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
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

                            <form id="stripe-payment-form"  method="POST">
                                @csrf

                                <table class="form-head mb-4 d-flex flex-wrap align-items-center">
                                    <tr>
                                        <td width="60%">
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
                                                                    <small>(Required)</small></td>
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
                                                            <tr>
                                                                <td colspan="3">
                                                                    <div class="form-check">
                                                                        <label class="form-check-label"
                                                                            for="charge_{{ $charge->id }}">
                                                                            <strong>{{ $charge->charge_name }}</strong>
                                                                            <small>(Optional)</small>
                                                                        </label>
                                                                        <input class="form-check-input optional-charge"
                                                                            type="checkbox" id="charge_{{ $charge->id }}"
                                                                            name="optional_charges[]"
                                                                            value="{{ $charge->charge_price }}"
                                                                            data-charge="{{ $charge->charge_price }}"
                                                                            data-charge-type="{{ $charge->charge_type }}" {{
                                                            in_array($charge->charge_price, old('optional_charges', [])) ? 'checked' : '' }}>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control" readonly
                                                                        value="{{ $charge->charge_type == 'percentage' ? $charge->charge_price . '%' : $charge->charge_price }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach

                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                                                            <td><input type="text" id="subtotal" class="form-control"
                                                                    readonly></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                            <td><input type="text" id="grandTotal" name="grandTotal"
                                                                    class="form-control" readonly></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>

                                                                        <!-- Shipping -->
                            <td class="shipping-section">
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
                                    <label for="customer_id" class="form-label fw-bold">Choose Customer (optional)</label>
                                    <select name="customer_id" id="customer_id" class="form-select form-control"
                                        onchange="autofillFromCustomer(this.value)">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} -
                                                {{ $customer->address1 }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="ship_to_name" class="form-label">Recipient Name</label>
                                        <input type="text" name="ship_to_name" id="ship_to_name"
                                            class="form-control" value="{{ old('ship_to_name') }}" placeholder="Enter recipient name">
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="ship_to_address1" class="form-label">Address Line 1</label>
                                        <input type="text" name="ship_to_address1" id="ship_to_address1"
                                            class="form-control" value="{{ old('ship_to_address1') }}" placeholder="Street address">
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="ship_to_address2" class="form-label">Address Line 2 (Optional)</label>
                                        <input type="text" name="ship_to_address2" id="ship_to_address2"
                                            class="form-control" value="{{ old('ship_to_address2') }}" placeholder="Apt, suite, unit, etc.">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="ship_to_city" class="form-label">City</label>
                                        <input type="text" name="ship_to_city" id="ship_to_city"
                                            class="form-control" value="{{ old('ship_to_city') }}" placeholder="City">
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="ship_to_state" class="form-label">State</label>
                                        <input type="text" name="ship_to_state" id="ship_to_state"
                                            class="form-control" value="{{ old('ship_to_state') }}" placeholder="State">
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="ship_to_zip" class="form-label">ZIP Code</label>
                                        <input type="text" name="ship_to_zip" id="ship_to_zip" class="form-control"
                                            value="{{ old('ship_to_zip') }}" placeholder="ZIP">
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label for="ship_to_phone" class="form-label">Phone Number</label>
                                        <input type="tel" name="ship_to_phone" id="ship_to_phone"
                                            class="form-control" value="{{ old('ship_to_phone') }}" placeholder="Phone number">
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
                            <p class="text-muted mb-3">Complete payment now or leave blank to generate an invoice</p>

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

            document.querySelectorAll('.qty').forEach((el, index) => {
                let qty = parseFloat(el.value) || 0;
                let cost = parseFloat(document.querySelector(`.cost[data-index='${index}']`).value) || 0;
                let total = qty * cost;
                document.querySelector(`.total[data-index='${index}']`).value = total.toFixed(2);
                subtotal += total;
            });

            document.getElementById('subtotal').value = subtotal.toFixed(2);

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

            document.getElementById('grandTotal').value = (subtotal + requiredChargeTotal + optionalChargeTotal).toFixed(2);
        }

        document.querySelectorAll('.qty, .cost, .optional-charge').forEach(el => {
            el.addEventListener('input', calculateTotals);
            el.addEventListener('change', calculateTotals);
        });

        window.onload = calculateTotals;
    </script>

    <script>
        const customers = @json($customers);
        const franchisee = @json($franchisee);

        // Load items from sessionStorage on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadItemsFromSessionStorage();
        });

        function loadItemsFromSessionStorage() {
            const orderItems = sessionStorage.getItem('orderItems');
            if (!orderItems) {
                // Redirect back if no items found
                window.location.href = "{{ route('franchise.orderpops.index', ['franchise' => $franchise]) }}";
                return;
            }

            const items = JSON.parse(orderItems);
            populateOrderTable(items);
            calculateTotals();
        }

        function populateOrderTable(items) {
            const tbody = document.querySelector('.table tbody');
            if (!tbody) return;

            // Clear existing rows except charge rows
            const existingRows = tbody.querySelectorAll('tr');
            existingRows.forEach(row => {
                if (!row.querySelector('.required-charge') && !row.querySelector('.optional-charge')) {
                    row.remove();
                }
            });

            // Add item rows before charge rows
            const chargeRows = tbody.querySelectorAll('tr');
            const insertPosition = chargeRows.length > 0 ? chargeRows[0] : null;

            items.forEach((item, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <input type="hidden" name="items[${index}][fgp_item_id]" value="${item.id || ''}">
                        ${item.name || 'Unknown Item'}
                    </td>
                    <td>
                        <input type="number" name="items[${index}][unit_number]" 
                            class="form-control qty" min="1" value="${item.quantity || 1}" 
                            data-index="${index}">
                    </td>
                    <td>
                        <input type="number" name="items[${index}][unit_cost]" 
                            class="form-control cost" step="0.01" 
                            value="${(item.priceValue || 0).toFixed(2)}" 
                            data-index="${index}">
                    </td>
                    <td>
                        <input type="text" class="form-control total" readonly 
                            value="0.00" data-index="${index}">
                    </td>
                `;
                
                if (insertPosition) {
                    tbody.insertBefore(row, insertPosition);
                } else {
                    tbody.appendChild(row);
                }
            });

            // Re-attach event listeners
            document.querySelectorAll('.qty, .cost').forEach(el => {
                el.addEventListener('input', calculateTotals);
                el.addEventListener('change', calculateTotals);
            });
        }

        function autofillFromCustomer(customerId) {
            // Fix: Use 'id' instead of 'customer_id' as per database schema
            const customer = customers.find(c => c.id == parseInt(customerId));
            if (!customer) {
                console.log('Customer not found for ID:', customerId);
                console.log('Available customers:', customers);
                return;
            }

            console.log('Found customer:', customer);

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

            fillShippingFields({
                name: franchisee.business_name ?? '',
                address1: franchisee.address1 ?? '',
                address2: franchisee.address2 ?? '',
                city: franchisee.city ?? '',
                state: franchisee.state ?? '',
                zip: franchisee.zip_code ?? '',
                phone: franchisee.phone ?? '',
            });
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
            document.getElementById('ship_to_name').value = '';
            document.getElementById('ship_to_address1').value = '';
            document.getElementById('ship_to_address2').value = '';
            document.getElementById('ship_to_city').value = '';
            document.getElementById('ship_to_state').value = '';
            document.getElementById('ship_to_zip').value = '';
            document.getElementById('ship_to_phone').value = '';
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
