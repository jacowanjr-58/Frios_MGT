@extends('layouts.app')
@section('content')
    <style>
        .dropdown-menu.show {
            max-height: 184.306px !important;
            overflow-y: auto;
            min-height: 96px;
        }
        :disabled{
            color: #00ABC7 !important;
        }
    </style>
    <div class="content-body default-height p-5 mt-5">
        <div class="container-fluid rounded border p-5 bg-white">
            <div class="d-flex justify-content-between">
                <h1 style="font-size: 28px; font-weight: bold">
                    Create Event
                </h1>

                <a href="{{ route('franchise.events.calender') }}" class="btn btn-primary">
                    Back
                </a>
            </div>




            <div class="mt-3 alert alert-info message alert-dismissible fade show" style="display: none;" role="alert">
                <strong></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>


            <form action="{{ route('franchise.events.store') }}" method="post" id="stripe-payment-form">
                @csrf
                <div class="form-group">
                    <label for="" class="form-label">Event Name</label>
                    <input type="text" name="event_name" class="form-control" value="{{ old('event_name') }}">
                    @error('event_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </div>
                @php
use Carbon\Carbon;

$rawDate = request('date');
$start = null;
$end = null;

if ($rawDate) {
    // Clean up any spaces and replace them with a plus sign (in case there's a space between time and timezone)
    $cleanDate = str_replace(' ', '+', $rawDate);

    // Check if the cleaned date is in a specific format (e.g., `2025-05-01T05:00:00+05:00`)
    if ($cleanDate === '2025-05-01T05:00:00+05:00') {
        // Parse the date and set both start and end to the same date, with time set to 12 AM (00:00:00)
        $parsedDate = Carbon::parse($cleanDate)->format('Y-m-d\T00:00:00');
        $start = $parsedDate;
        $end = $parsedDate;

    } elseif (str_contains($cleanDate, 'T')) {
        // If the date contains time (T), parse the date and set both start and end to the same date, with time set to 12 AM (00:00:00)
        $parsedDate = Carbon::parse($cleanDate);
        $start = $parsedDate->format('Y-m-d\T00:00:00');  // Set to 12 AM (midnight)
        $end = $parsedDate->format('Y-m-d\T00:00:00');  // Set to 12 AM (midnight)

    } else {
        // If no time part is provided, assume it's just a date and add default time of '00:00:00' (12 AM)
        $start = Carbon::parse($cleanDate)->format('Y-m-d\T00:00:00');
        $end = null;  // End is null if no time is provided
    }
}


                @endphp




                <div class="row mt-2">
                    <div class="col-md-6 form-group">
                        <label for="" class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control" name="start_date" value="{{ old('start_date', $start) }}" readonly>
                        @error('start_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="" class="form-label">End Date</label>
                        <input type="datetime-local" class="form-control" name="end_date" value="{{ old('end_date', $start) }}">
                        @error('end_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="row">
                    <div class="form-group col-md-4 mt-2">
                        <label for="" class="form-label">Event Status</label>
                        <select name="event_status" class="form-control">
                            <option value="">Please Select</option>
                            <option value="scheduled" {{ old('event_status') == 'scheduled' ? 'selected' : '' }}>Scheduled
                            </option>
                            <option value="tentative" {{ old('event_status') == 'tentative' ? 'selected' : '' }}>Tentative
                            </option>
                            <option value="staffed" {{ old('event_status') == 'staffed' ? 'selected' : '' }}>Staffed
                            </option>
                        </select>
                        @error('event_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                    </div>
                    <div class="col-md-4 mt-2">
                        <label for="event_type" class="form-check-label">Event Type</label>
                        <select class="form-control" name="event_type" id="event_type">
                            <option value="">Select an Event Type</option>
                            <option value="Sports" {{ old('event_type') == 'Sports' ? 'selected' : '' }}>Sports</option>
                            <option value="School" {{ old('event_type') == 'School' ? 'selected' : '' }}>School</option>
                            <option value="Festival/Fair" {{ old('event_type') == 'Festival/Fair' ? 'selected' : '' }}>
                                Festival/Fair</option>
                            <option value="Corporate" {{ old('event_type') == 'Corporate' ? 'selected' : '' }}>Corporate
                            </option>
                            <option value="Wedding" {{ old('event_type') == 'Wedding' ? 'selected' : '' }}>Wedding</option>
                            <option value="Birthday Party" {{ old('event_type') == 'Birthday Party' ? 'selected' : '' }}>
                                Birthday Party</option>
                            <option value="Private Catering"
                                {{ old('event_type') == 'Private Catering' ? 'selected' : '' }}>Private Catering</option>
                            <option value="Holiday" {{ old('event_type') == 'Holiday' ? 'selected' : '' }}>Holiday</option>
                            <option value="Concert/Music" {{ old('event_type') == 'Concert/Music' ? 'selected' : '' }}>
                                Concert/Music</option>
                            <option value="Pop-up Sales" {{ old('event_type') == 'Pop-up Sales' ? 'selected' : '' }}>Pop-up
                                Sales</option>
                            <option value="Church/Religious"
                                {{ old('event_type') == 'Church/Religious' ? 'selected' : '' }}>Church/Religious</option>
                            <option value="Other" {{ old('event_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('event_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="col-md-4 mt-2">
                        <label for="">Planned Paymnet</label>
                        <select name="planned_payment" id="planned_payment" class="form-control">
                            <option value="">Please Select</option>
                            <option value="cash" {{ old('planned_payment') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ old('planned_payment') == 'check' ? 'selected' : '' }}>check</option>
                            <option value="inovice" {{ old('planned_payment') == 'inovice' ? 'selected' : '' }}>Inovice
                            </option>
                            <option value="credit-card" {{ old('planned_payment') == 'credit-card' ? 'selected' : '' }}>
                                Credit Card</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 mt-2 form-group">
                        <label for="" class="form-label">Staff assigned</label>
                        <select name="staff_assigned[]" multiple class="form-control">
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->user_id }}"
                                    {{ collect(old('staff_assigned'))->contains($staff->user_id) ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_assigned')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="col-md-6 mt-2 form-group">
                        <label for="" class="form-label">Assign Customers</label>
                        <select name="customer_id" id="assign_customer" class="form-control">
                            <option value="">Please Select</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->customer_id }}"
                                    {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                    </div>


                    <div class="col-md-4 form-group mt-3">
                        <label for="" class="form-label">Costs</label>
                        <input type="text" name="costs" class="form-control" value="{{ old('costs') }}">
                        @error('costs')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group mt-3">
                        <label for="" class="form-label">Expected Sales</label>
                        <input type="text" name="expected_sales" class="form-control"
                            value="{{ old('expected_sales') }}">
                        @error('expected_sales')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group mt-3">
                        <label for="" class="form-label">Actual Sales</label>
                        <input type="text" name="actual_sales" class="form-control"
                            value="{{ old('actual_sales') }}">
                        @error('actual_sales')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="mt-3 form-group">
                    <label for="" class="form-label">Events notes</label>
                    <textarea name="event_notes" class="form-control" id="" cols="10" rows="5"
                        placeholder="(Contact Infromation , Special Instructions, Agreements, Special Circumstances)">{{ old('event_notes') }}</textarea>
                    @error('event_notes')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                </div>


                <div class="mt-4">
                    <h3><b>Resources Selection:</b></h3>
                    <div class="row mt-2">
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[van]"
                                    {{ old('resources_selection.van') ? 'checked' : '' }} type="checkbox" value="1">
                                Van
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[trailer]"
                                    {{ old('resources_selection.trailer') ? 'checked' : '' }} type="checkbox"
                                    value="1"> Trailer
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[cart]"
                                    {{ old('resources_selection.cart') ? 'checked' : '' }} type="checkbox"
                                    value="1"> Cart
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[cooler]"
                                    {{ old('resources_selection.cooler') ? 'checked' : '' }} type="checkbox"
                                    value="1"> Cooler
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[dry_ice]"
                                    {{ old('resources_selection.dry_ice') ? 'checked' : '' }} type="checkbox"
                                    value="1"> Dry Ice
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[freezer]"
                                    {{ old('resources_selection.freezer') ? 'checked' : '' }} type="checkbox"
                                    value="1"> Freezer
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[direct_delivery]"
                                    {{ old('resources_selection.direct_delivery') ? 'checked' : '' }} type="checkbox"
                                    value="1"> Direct Delivery
                            </label>
                        </div>
                        @error('resources_selection.van')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('resources_selection.trailer')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('resources_selection.cart')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('resources_selection.cooler')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('resources_selection.dry_ice')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('resources_selection.freezer')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('resources_selection.direct_delivery')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="displayFlavor">

                </div>



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
                                                    <div id="card-errors" class="text-danger mb-3"></div>

                                                </div>


                <button disabled class="btn btn-outline-primary" id="submit-button">
                    Submit
                </button>

            </form>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

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
$(document).ready(function () {
    function fetchFlavorData() {
        var selectedEndDate = $('input[name="end_date"]').val();
        var selectedStartDate = $('input[name="start_date"]').val();

        if (!selectedEndDate || !selectedStartDate) {
            return; // Don't fire AJAX if dates are not selected
        }

        $.ajax({
    url: '{{ route('franchise.events.date') }}',
    method: 'POST',
    data: {
        start_date: selectedStartDate,
        end_date: selectedEndDate,
        _token: '{{ csrf_token() }}'
    },
    beforeSend: function () {
        // Clear previous message and table content
        $('.displayFlavor').html('');
        $('.message').hide();
        $('tbody').html('');
    },
    success: function (response) {
        if (response.success) {
            // Update the table content with the new data
            $('.displayFlavor').html(response.html);  // Assuming response.html contains the new table content

            // Show any message if returned
            if (response.message) {
                $('.message').html(response.message).show();
            } else {
                $('.message').hide();
            }

            // Re-initialize selectpicker for the newly loaded content
            initializeSelectpicker();  // Make sure selectpicker is refreshed after new rows are added
        }
    },
    error: function (xhr) {
        console.error(xhr.responseText);
    }
});

    }

    // Call on page load
    fetchFlavorData();

    // Call on end_date change
    $('input[name="end_date"]').on('change', fetchFlavorData);
});

</script>
@endsection
