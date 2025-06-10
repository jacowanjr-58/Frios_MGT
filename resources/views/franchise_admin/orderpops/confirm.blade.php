@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Dashboard</h2>
                <p>Confirm Order</p>
            </div>
            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-2"></i> Back
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

                        @if (!empty($items))
     <form id="stripe-payment-form" action="{{ route('franchise.orderpops.store') }}" method="POST">
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
                                        @foreach ($items as $index => $item)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][fgp_item_id]" value="{{ $item['id'] }}">
                                                {{ $item['name'] }}
                                            </td>

                                            <td>
                                                <input type="number" name="items[{{ $index }}][unit_number]" class="form-control qty" min="1"
                                                    value="{{ old(" items.$index.unit_number", $item['quantity']) }}" data-index="{{ $index }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control cost" step="0.01"
                                                    value="{{ old(" items.$index.unit_cost", $item['case_cost']) }}" data-index="{{ $index }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control total" readonly value="{{ number_format(old("
                                                    items.$index.unit_cost", $item['case_cost']) * old("items.$index.unit_number", $item['quantity']),
                                                    2) }}" data-index="{{ $index }}">
                                            </td>
                                        </tr>
                                        @endforeach

                                        @foreach ($requiredCharges as $charge)
                                        <tr>
                                            <td colspan="3"><strong>{{ $charge->charge_name }}</strong> <small>(Required)</small></td>
                                            <td>
                                                <input type="text" class="form-control required-charge" readonly
                                                    value="{{ $charge->charge_type == 'percentage' ? $charge->charge_price . '%' : $charge->charge_price }}"
                                                    data-charge="{{ $charge->charge_price }}" data-charge-type="{{ $charge->charge_type }}">
                                            </td>
                                        </tr>
                                        @endforeach

                                        @foreach ($optionalCharges as $charge)
                                        <tr>
                                            <td colspan="3">
                                                <div class="form-check">
                                                    <label class="form-check-label" for="charge_{{ $charge->id }}">
                                                        <strong>{{ $charge->charge_name }}</strong> <small>(Optional)</small>
                                                    </label>
                                                    <input class="form-check-input optional-charge" type="checkbox" id="charge_{{ $charge->id }}"
                                                        name="optional_charges[]" value="{{ $charge->charge_price }}"
                                                        data-charge="{{ $charge->charge_price }}" data-charge-type="{{ $charge->charge_type }}" {{
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
                                            <td><input type="text" id="subtotal" class="form-control" readonly></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Total</strong></td>
                                            <td><input type="text" id="grandTotal" name="grandTotal" class="form-control" readonly></td>
                                        </tr>
                                    </tbody>
                                </table>
                    </div>
    </td>

            <!-- Shipping -->
     <td>
        <hr class="my-4">
        <h5 class="mb-3">Shipping</h5>

            <!-- Right Column: Shipping Info -->

                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="autofillFranchisee()">Use Franchise Address</button>
                </div>

                <div class="mb-3">
                    <label for="customer_id" class="form-label">Choose Customer (optional)</label>
                    <select name="customer_id" id="customer_id" class="form-select" onchange="autofillFromCustomer(this.value)">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}">{{ $customer->name }} - {{ $customer->address1 }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="ship_to_name" class="form-label">Recipient Name</label>
                    <input type="text" name="ship_to_name" id="ship_to_name" class="form-control" value="{{ old('ship_to_name') }}">
                </div>
                <div class="mb-3">
                    <label for="ship_to_address1" class="form-label">Address Line 1</label>
                    <input type="text" name="ship_to_address1" id="ship_to_address1" class="form-control" value="{{ old('ship_to_address1') }}">
                </div>
                <div class="mb-3">
                    <label for="ship_to_address2" class="form-label">Address Line 2</label>
                    <input type="text" name="ship_to_address2" id="ship_to_address2" class="form-control" value="{{ old('ship_to_address2') }}">
                </div>
                <div class="mb-3">
                    <label for="ship_to_city" class="form-label">City</label>
                    <input type="text" name="ship_to_city" id="ship_to_city" class="form-control" value="{{ old('ship_to_city') }}">
                </div>
                <div class="mb-3">
                    <label for="ship_to_state" class="form-label">State</label>
                    <input type="text" name="ship_to_state" id="ship_to_state" class="form-control" value="{{ old('ship_to_state') }}">
                </div>
                <div class="mb-3">
                    <label for="ship_to_zip" class="form-label">ZIP</label>
                    <input type="text" name="ship_to_zip" id="ship_to_zip" class="form-control" value="{{ old('ship_to_zip') }}">
                </div>
                <div class="mb-3">
                    <label for="ship_to_phone" class="form-label">Phone</label>
                    <input type="text" name="ship_to_phone" id="ship_to_phone" class="form-control" value="{{ old('ship_to_phone') }}">
                </div>
              </td>
</tr>
    </table>
<div >
            <!-- Right Column: Payment Info -->

                <div class="mb-3"> Optional Pay Now
                </div>
                <div class="mb-3">
                    <label for="cardholder-name" class="form-label">Cardholder Name</label>
                    <input type="text" id="cardholder-name" name="cardholder_name" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Card Number</label>
                    <div id="card-number-element" class="form-control"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Expiration</label>
                    <div id="card-expiry-element" class="form-control"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">CVC</label>
                    <div id="card-cvc-element" class="form-control"></div>
                    <input type="hidden" name="stripeToken" id="stripeToken">
                </div>
                <div class="mb-3">
                    <label for="payment_reference" class="form-label">Payment Reference (if paid now)</label>
                    <input type="text" name="payment_reference" class="form-control">
                </div>
                <div id="card-errors" class="text-danger mb-3"></div>

        </div>

                <input type="hidden" name="is_paid" id="is_paid" value="0">
                            <div class="mt-4">
                                <button type="submit" id="submit-button" class="btn btn-danger btn-lg fw-bold shadow">Confirm Order</button>
                            </div>
        </form>
                        @else
                            <p>No items in the order.</p>
                        @endif
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
document.addEventListener("DOMContentLoaded", function() {
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

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // If no card number entered, proceed without Stripe
        if (!cardComplete.number && !cardComplete.expiry && !cardComplete.cvc && cardholderName.value.trim() === "") {
            form.submit();  // Allow fallback to invoice logic
            return;
        }

        submitButton.disabled = true;

        const { token, error } = await stripe.createToken(cardNumber, { name: cardholderName.value });

        if (error) {
            errorElement.textContent = error.message;
            submitButton.disabled = false;
        } else {
            tokenInput.value = token.id;
            form.submit();
        }
    });

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
     const franchisee = @json($franchisee instanceof \Illuminate\Support\Collection ? $franchisee->first() : $franchisee);

    function autofillFromCustomer(customerId) {
        const customer = customers.find(c => c.customer_id == parseInt(customerId));
        if (!customer) return;

        fillShippingFields({
            name: customer.name,
            address1: customer.address1,
            address2: customer.address2,
            city: customer.city,
            state: customer.state,
            zip: customer.zip_code,
            phone: customer.phone,
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
</script>
@endpush
