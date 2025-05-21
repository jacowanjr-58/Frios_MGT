@extends('layouts.app')
@section('content')

    <!--**********************************
                Content body start
            ***********************************-->
    <div class=" content-body default-height">
        <!-- row -->
        <div class="container-fluid">
            <!-- <div class="page-titles">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0)">Analytics</a></li>
                        </ol>
                    </div> -->
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Confirm Order</p>
                </div>

                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Confirm Order</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="table-responsive rounded">
                                                    @if (!empty($items))
                                                    <form id="stripe-payment-form" action="{{ route('franchise.orderpops.store') }}" method="POST">
                                                        @csrf
                                                        <table class="table customer-table display mb-4 fs-14 card-table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Item Name</th>
                                                                    <th>User</th>
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
                                                                        <input type="hidden" name="items[{{ $index }}][user_ID]" value="{{ auth()->user()->user_id }}">
                                                                        {{ auth()->user()->name }}
                                                                    </td>
                                                                    <td>
                                                                        <input
                                                                            type="number"
                                                                            name="items[{{ $index }}][unit_number]"
                                                                            class="form-control qty"
                                                                            min="1"
                                                                            value="{{ old("items.$index.unit_number", $item['quantity']) }}"
                                                                            data-index="{{ $index }}"
                                                                        >
                                                                    </td>
                                                                    <td>
                                                                        <input
                                                                            type="number"
                                                                            name="items[{{ $index }}][unit_cost]"
                                                                            class="form-control cost"
                                                                            step="0.01"
                                                                            value="{{ old("items.$index.unit_cost", $item['price']) }}"
                                                                            data-index="{{ $index }}"
                                                                        >
                                                                    </td>
                                                                    <td>
                                                                        <input
                                                                            type="text"
                                                                            class="form-control total"
                                                                            readonly
                                                                            value="{{ number_format(old("items.$index.unit_cost", $item['price']) * old("items.$index.unit_number", $item['quantity']), 2) }}"
                                                                            data-index="{{ $index }}"
                                                                        >
                                                                    </td>
                                                                </tr>
                                                                @endforeach

                                                              <!-- Required Charges -->
                                                                @foreach ($requiredCharges as $charge)
                                                                <tr>
                                                                    <td colspan="4"><strong>{{ $charge->charge_name }}</strong> <small>(Required Charges)</small></td>
                                                                    <td>
                                                                        @if($charge->charge_type == 'percentage')
                                                                            <!-- Display percentage charges with % symbol -->
                                                                            <input type="text" class="form-control required-charge" readonly value="{{ $charge->charge_price }}%" data-charge="{{ $charge->charge_price }}" data-charge-type="{{ $charge->charge_type }}">
                                                                        @else
                                                                            <!-- Display fixed charges with $ symbol -->
                                                                            <input type="text" class="form-control required-charge" readonly value="{{ $charge->charge_price }}" data-charge="{{ $charge->charge_price }}" data-charge-type="{{ $charge->charge_type }}">
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach

                                                                <!-- Optional Charges -->
                                                                @foreach ($optionalCharges as $charge)
                                                                <tr>
                                                                    <td colspan="4">
                                                                        <div class="form-check">
                                                                            <label class="form-check-label" for="charge_{{ $charge->id }}"><strong>{{ $charge->charge_name }}</strong> <small>(Optional Charges)</small></label>
                                                                            <input
                                                                                class="form-check-input optional-charge"
                                                                                type="checkbox"
                                                                                id="charge_{{ $charge->id }}"
                                                                                name="optional_charges[]"
                                                                                value="{{ $charge->charge_price }}"
                                                                                data-charge="{{ $charge->charge_price }}"
                                                                                data-charge-type="{{ $charge->charge_type }}"
                                                                                {{ in_array($charge->charge_price, old('optional_charges', [])) ? 'checked' : '' }}
                                                                            >
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @if($charge->charge_type == 'percentage')
                                                                            <!-- Display percentage charges with % symbol -->
                                                                            <input type="text" class="form-control" readonly value="{{ $charge->charge_price }}%" data-charge="{{ $charge->charge_price }}" data-charge-type="{{ $charge->charge_type }}">
                                                                        @else
                                                                            <!-- Display fixed charges with $ symbol -->
                                                                            <input type="text" class="form-control" readonly value="{{ $charge->charge_price }}" data-charge="{{ $charge->charge_price }}" data-charge-type="{{ $charge->charge_type }}">
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach


                                                                <tr>
                                                                    <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                                                                    <td><input type="text" id="subtotal" class="form-control" readonly></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="4" class="text-end"><strong>Total</strong></td>
                                                                    <td><input type="text" id="grandTotal" name="grandTotal" class="form-control" readonly></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>

                                                        <div class="row">
                                                                                                                <div class="col-md-6 mb-3">
                                                        <input type="text" id="cardholder-name" name="cardholder_name"
                                                            placeholder="Cardholder Name" class="form-control">
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div id="card-number-element" class="form-control"></div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div id="card-expiry-element" class="form-control"></div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div id="card-cvc-element" class="form-control"></div>
                                                        <input type="hidden" name="stripeToken" id="stripeToken">
                                                    </div>
                                                        </div>

                                                    <div id="card-errors" class="text-danger mb-3"></div>

                                                        <button disabled type="submit" id="submit-button" class="btn btn-primary bg-primary">Confirm Order</button>
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
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--**********************************
                Content body end
            ***********************************-->
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

            let cardComplete = {
                number: false,
                expiry: false,
                cvc: false
            };

            function updateButtonState() {
                const allComplete = cardComplete.number && cardComplete.expiry && cardComplete.cvc && cardholderName
                    .value.trim() !== "";
                submitButton.disabled = !allComplete;
            }

            cardNumber.on('change', function(event) {
                cardComplete.number = event.complete;
                if (event.error) {
                    errorElement.textContent = event.error.message;
                } else {
                    errorElement.textContent = '';
                }
                updateButtonState();
            });

            cardExpiry.on('change', function(event) {
                cardComplete.expiry = event.complete;
                updateButtonState();
            });

            cardCvc.on('change', function(event) {
                cardComplete.cvc = event.complete;
                updateButtonState();
            });

            cardholderName.addEventListener('input', updateButtonState);

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                submitButton.disabled = true;

                const {
                    token,
                    error
                } = await stripe.createToken(cardNumber, {
                    name: cardholderName.value
                });

                if (error) {
                    errorElement.textContent = error.message;
                    submitButton.disabled = false;
                } else {
                    tokenInput.value = token.id;
                    form.submit();
                }
            });
        });
    </script>
            <script>
function calculateTotals() {
    let subtotal = 0;

    // Calculate item totals based on quantity and cost
    document.querySelectorAll('.qty').forEach((el, index) => {
        let qty = parseFloat(el.value) || 0;
        let cost = parseFloat(document.querySelector(`.cost[data-index='${index}']`).value) || 0;
        let total = qty * cost;
        document.querySelector(`.total[data-index='${index}']`).value = total.toFixed(2);
        subtotal += total;
    });

    // Update the subtotal
    document.getElementById('subtotal').value = subtotal.toFixed(2);

    let requiredChargeTotal = 0;

    // Calculate required charges (both fixed and percentage)
    document.querySelectorAll('.required-charge').forEach(el => {
        let charge = parseFloat(el.getAttribute('data-charge')) || 0;
        let chargeType = el.getAttribute('data-charge-type');

        if (chargeType === 'percentage') {
            // If charge is percentage, check if it's positive or negative
            if (charge > 0) {
                requiredChargeTotal += (subtotal * charge) / 100; // Apply percentage to subtotal
            } else {
                requiredChargeTotal -= (subtotal * Math.abs(charge)) / 100; // Subtract discount from subtotal
            }
        } else {
            // If charge is fixed, simply add the charge
            requiredChargeTotal += charge;
        }
    });

    let optionalChargeTotal = 0;

    // Calculate optional charges (both fixed and percentage)
    document.querySelectorAll('.optional-charge:checked').forEach(el => {
        let charge = parseFloat(el.getAttribute('data-charge')) || 0;
        let chargeType = el.getAttribute('data-charge-type');

        if (chargeType === 'percentage') {
            // If charge is percentage, check if it's positive or negative
            if (charge > 0) {
                optionalChargeTotal += (subtotal * charge) / 100; // Apply percentage to subtotal
            } else {
                optionalChargeTotal -= (subtotal * Math.abs(charge)) / 100; // Subtract discount from subtotal
            }
        } else {
            // If charge is fixed, simply add the charge
            optionalChargeTotal += charge;
        }
    });

    // Update the grand total (subtotal + required charges + optional charges)
    document.getElementById('grandTotal').value = (subtotal + requiredChargeTotal + optionalChargeTotal).toFixed(2);
}

document.querySelectorAll('.qty, .cost, .optional-charge').forEach(el => {
    el.addEventListener('input', calculateTotals);
    el.addEventListener('change', calculateTotals);
});

window.onload = calculateTotals;

            </script>


@endsection
