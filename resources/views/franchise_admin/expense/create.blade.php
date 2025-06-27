@extends('layouts.app')
@section('content')
    <style>
        div#sub_category_list {
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
        }

        #sub_category_placeholder {
            padding-top: 15px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
        }

        .dropdown-item {
            padding: 10px 15px;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
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
                                            <form id="stripe-payment-form"
                                                action="{{ route('franchise.expenses_by_franchise-store', ['franchise' => $franchiseId]) }}"
                                                method="POST">
                                                @csrf

                                                <div class="row">
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Main Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name') }}" placeholder="Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                            name="amount" value="{{ old('amount') }}" placeholder="Amount">
                                                        @error('amount')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                                            name="date" id="date" value="{{ old('date') }}">
                                                        @error('date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" id="category_id" class="form-control select2">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}">
                                                                    {{ $ExpenseCategory->category }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                                                        <div id="sub_category_div" class="dropdown">
                                                            <div id="sub_category_placeholder" class="form-control">Please Select</div>
                                                            <div id="sub_category_list" class="dropdown-menu" style="display: none;"></div>
                                                        </div>
                                                        <input type="hidden" name="sub_category_id" id="sub_category_id" class="form-control">
                                                        @error('sub_category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary bg-primary">Add Expense</button>
                                                    </div>
                                                </div>
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


    {{--
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

            cardNumber.on('change', function (event) {
                cardComplete.number = event.complete;
                if (event.error) {
                    errorElement.textContent = event.error.message;
                } else {
                    errorElement.textContent = '';
                }
                updateButtonState();
            });

            cardExpiry.on('change', function (event) {
                cardComplete.expiry = event.complete;
                updateButtonState();
            });

            cardCvc.on('change', function (event) {
                cardComplete.cvc = event.complete;
                updateButtonState();
            });

            cardholderName.addEventListener('input', updateButtonState);

            form.addEventListener('submit', async function (e) {
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
        $(document).ready(function () {
            // Initialize select2
            $('.select2').select2();

            // Handle category change
            $('#category_id').on('change', function() {
                var categoryId = $(this).val();
                if (categoryId) {
                    $.ajax({
                        url: "{{ route('franchise.getSubCategories', ['franchise' => $franchiseId, 'category_id' => ':categoryId']) }}".replace(':categoryId', categoryId),
                        type: 'GET',
                        success: function(response) {
                            var subCategories = response.data;
                            var subCategoryList = $('#sub_category_list');
                            subCategoryList.empty();

                            // Add subcategories to dropdown
                            subCategories.forEach(function(subCategory) {
                                var item = $('<div class="dropdown-item" data-id="' + subCategory.id + '">' + 
                                           subCategory.category + '</div>');
                                subCategoryList.append(item);
                            });

                            // Reset placeholder
                            $('#sub_category_placeholder').text('Please Select');
                            $('#sub_category_id').val('');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching subcategories:', error);
                        }
                    });
                } else {
                    // Reset subcategory dropdown when no category is selected
                    $('#sub_category_list').empty();
                    $('#sub_category_placeholder').text('Please Select');
                    $('#sub_category_id').val('');
                }
            });

            // Handle subcategory dropdown toggle
            $('#sub_category_div').on('click', function() {
                $('#sub_category_list').toggle();
            });

            // Handle subcategory selection
            $(document).on('click', '#sub_category_list .dropdown-item', function() {
                var id = $(this).data('id');
                var text = $(this).text();
                $('#sub_category_id').val(id);
                $('#sub_category_placeholder').text(text);
                $('#sub_category_list').hide();
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#sub_category_div').length) {
                    $('#sub_category_list').hide();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            var today = new Date().toISOString().split('T')[0];
            document.getElementById('date').value = today;
        });
    </script>
@endsection