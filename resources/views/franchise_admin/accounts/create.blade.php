@extends('layouts.app')
@section('content')

    <!--**********************************
                        Content body start
                    ***********************************-->
    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Add Customer</p>
                </div>

                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Validation Error:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add Customer</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('franchise.account.store') }}" method="POST" id="payment-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cardholder_name" class="form-label">Cardholder Name</label>
                                            <input type="text" class="form-control" id="cardholder_name"
                                                   name="cardholder_name" required value="{{ old('cardholder_name') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="card" class="form-label">Card Details</label>
                                            <div id="card-element"></div>
                                            <div id="card-errors" role="alert"></div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-check-label" for="is_active">Active</label>
                                            <select name="is_active" id="is_active" class="form-control w-100">
                                                <option value="0">Inactive</option>
                                                <option value="1">Active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary bg-primary" id="submit">
                                    Save Card
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script src="https://js.stripe.com/v3/"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        var stripe = Stripe('pk_test_51O3YibHGvvdXGbsFM5WpPgXdwb25cIjVHvd3emFXWzT3teiNfFJ6nIHhSnAsJcXlKgYbHp752BoemyHWjQ0Y3Naa004WZMdlsQ');
        var elements = stripe.elements();
        var card = elements.create('card', { hidePostalCode: true });
        card.mount('#card-element');

        // Handle form submission with AJAX
        $('#payment-form').on('submit', function(event) {
            event.preventDefault();  // Prevent the form from submitting normally

            var submitButton = $('#submit');
            submitButton.prop('disabled', true);  // Disable the submit button to prevent multiple submissions

            // Remove any previously appended token inputs to prevent multiple tokens
            $('input[name="stripe_token"]').remove();

            // Create the Stripe token
            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // If there's an error, display it and re-enable the submit button
                    $('#card-errors').text(result.error.message);
                    submitButton.prop('disabled', false);  // Re-enable the submit button if there's an error
                } else {
                    // Create a hidden input to store the Stripe token
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'stripe_token',
                        value: result.token.id
                    }).appendTo('#payment-form');

                    // Now use AJAX to submit the form
                    $.ajax({
                        url: $('#payment-form').attr('action'),  // Form action URL
                        type: 'POST',
                        data: $('#payment-form').serialize(),  // Serialize form data including the Stripe token
                        success: function(response) {
                            // Handle success response
                            console.log('Form submitted successfully:', response);
                            alert('Payment was successful!');
                            // You can redirect or show a success message here
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error('Error:', error);
                            alert('There was an error submitting the form. Please try again.');
                            submitButton.prop('disabled', false);  // Re-enable the submit button
                        }
                    });
                }
            }).catch(function(error) {
                // Handle any errors that might occur during the stripe.createToken() process
                console.error('Error creating token:', error);
                submitButton.prop('disabled', false);  // Re-enable the submit button if something fails
            });
        });
    });
</script>

@endsection
