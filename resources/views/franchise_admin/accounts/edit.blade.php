@extends('layouts.app')
@section('content')

<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Dashboard \</h2>
                <p>Edit Customer</p>
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
                        <h4 class="card-title">Edit Customer</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="{{ route('franchise.account.update', $account->id) }}" method="POST" novalidate>
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cardholder_name" class="form-label">Cardholder Name</label>
                                            <input type="text" class="form-control" id="cardholder_name"
                                                   name="cardholder_name" required
                                                   value="{{ old('cardholder_name', $account->cardholder_name) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="card_number" class="form-label">Card Number</label>
                                            <input type="text" class="form-control" id="card_number"
                                                   name="card_number" required
                                                   value="{{ old('card_number', $account->card_number) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="card_expiry" class="form-label">Expiry Date (MM/YY)</label>
                                            <input type="text" class="form-control" id="card_expiry"
                                                   name="card_expiry" required
                                                   pattern="^(0[1-9]|1[0-2])\/\d{2}$"
                                                   value="{{ old('card_expiry', $account->card_expiry) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="card_cvc" class="form-label">CVC</label>
                                            <input type="text" class="form-control" id="card_cvc"
                                                   name="card_cvc" required pattern="\d{3,4}" maxlength="4"
                                                   value="{{ old('card_cvc', $account->card_cvc) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="is_active">Status</label>
                                            <select name="is_active" id="is_active" class="form-control w-100">
                                                <option value="0" {{ old('is_active', $account->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                                                <option value="1" {{ old('is_active', $account->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary bg-primary">
                                    Update Card
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cleave.js formatting -->
    <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
    <script>
        new Cleave('#card_number', {
            creditCard: true,
            onCreditCardTypeChanged: function(type) {
                console.log('Card type: ' + type);
            }
        });

        new Cleave('#card_expiry', {
            date: true,
            datePattern: ['m', 'y']
        });

        new Cleave('#card_cvc', {
            numeral: true,
            numeralPositiveOnly: true,
            stripLeadingZeroes: true
        });

        // Strip spaces before submission
        document.querySelector('form').addEventListener('submit', function () {
            const cardNumber = document.getElementById('card_number');
            cardNumber.value = cardNumber.value.replace(/\s+/g, '');
        });
    </script>
</div>

@endsection
