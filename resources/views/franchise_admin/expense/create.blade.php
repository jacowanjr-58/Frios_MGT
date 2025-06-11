@extends('layouts.app')
@section('content')
    <style>
        div#sub_category_list {
            width: 100%;
        }

        #sub_category_placeholder {
            padding-top: 15px;
            font-size: 15px;
            font-weight: 500;
        }
    </style>


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
                    <p>Add Expense</p>
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
                                        <h4 class="card-title">Add Expense</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">



                                            <form id="stripe-payment-form" action="{{ route('franchise.expense.store', ['franchisee' => request()->route('franchisee')]) }}"
                                                method="POST">
                                                @csrf

                                                <div class="row">

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Main Name <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text"
                                                            class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name') }}" placeholder="Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Amount <span
                                                                class="text-danger">*</span></label>
                                                        <input type="number"
                                                            class="form-control @error('amount') is-invalid @enderror"
                                                            name="amount" value="{{ old('amount') }}" placeholder="Amount">
                                                        @error('amount')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Date <span
                                                                class="text-danger">*</span></label>
                                                        <input type="date"
                                                            class="form-control @error('date') is-invalid @enderror"
                                                            name="date" id="date" value="{{ old('date') }}">


                                                        @error('date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category <span
                                                                class="text-danger">*</span></label>
                                                        <select name="category_id" id="category_id"
                                                            class="form-control  @error('category_id') is-invalid @enderror">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}">
                                                                    {{ $ExpenseCategory->category }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Sub Category <span
                                                                class="text-danger">*</span></label>

                                                        <!-- This div will serve as the dropdown -->
                                                        <div id="sub_category_div" class="dropdown">
                                                            <div id="sub_category_placeholder" class="form-control">Please
                                                                Select</div>
                                                            <div id="sub_category_list" class="dropdown-menu"
                                                                style="display: none;"></div>
                                                        </div>

                                                        <!-- Hidden input field to store selected value -->
                                                        <input type="hidden" name="sub_category_id" id="sub_category_id"
                                                            class="form-control">

                                                        @error('sub_category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- <div class="col-md-6 mb-3">
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

                                                    <div id="card-errors" class="text-danger mb-3"></div> --}}

                                                </div>
                                                {{-- <button type="submit" class="btn btn-primary bg-primary"
                                                    id="submit-button" disabled>
                                                    Add Expense
                                                </button> --}}
                                                <button type="submit" class="btn btn-primary bg-primary">
                                                    Add Expense
                                                </button>

                                            </form>

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


    {{-- <script src="https://js.stripe.com/v3/"></script>
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
    </script> --}}


    <script>
        $(document).ready(function() {
            $('#sub_category_div').on('click', function() {
                $('#sub_category_list').toggle();
            });

            $(document).on('click', '.sub_category_option', function() {
                var selectedText = $(this).text();
                var selectedValue = $(this).data('id');
                $('#sub_category_placeholder').text(selectedText);
                $('#sub_category_id').val(selectedValue);
                $('#sub_category_list').hide();
            });

            $('#category_id').on('change', function() {
                var categoryID = $(this).val();
                if (categoryID) {
                    $.ajax({
                        url: '{{ url('franchise/get-subcategories') }}/' + categoryID,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            $('#sub_category_list').empty();
                            $.each(response.data, function(index, subCategory) {
                                $('#sub_category_list').append(
                                    '<div class="dropdown-item sub_category_option" data-id="' +
                                    subCategory.id + '">' + subCategory
                                    .sub_category + '</div>'
                                );
                            });
                        },
                        error: function() {
                            alert('Failed to fetch sub-categories. Please try again.');
                        }
                    });
                } else {
                    $('#sub_category_list').empty();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('date').value = today;
        });
    </script>
@endsection
