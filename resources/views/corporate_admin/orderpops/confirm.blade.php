@extends('layouts.app')
@section('content')
<style>
    div#customer_list {
    width: 100%;
}
#customer_placeholder{
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

                                            @if(session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
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
                                                    <form action="{{ route('corporate_admin.orderpops.store') }}" method="POST">
                                                        <div class="row">
                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Franchise <span class="text-danger">*</span></label>
                                                                <select name="user_ID" id="user_ID" class="form-control  @error('user_ID') is-invalid @enderror">
                                                                    <option value="">Please Select</option>
                                                                    @foreach ($users as $user)
                                                                        <option value="{{ $user->franchisee_id }}">{{ $user->business_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('user_ID')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>

                                                            <div class="mb-3 col-md-6">
                                                                <label class="form-label">Customer <span class="text-danger">*</span></label>

                                                                <!-- This div will serve as the dropdown -->
                                                                <div id="customer_div" class="dropdown">
                                                                    <div id="customer_placeholder" class="form-control">Please Select</div>
                                                                    <div id="customer_list" class="dropdown-menu" style="display: none;"></div>
                                                                </div>

                                                                <!-- Hidden input field to store selected value -->
                                                                <input type="hidden" name="customer_id" id="customer_id" class="form-control">

                                                                @error('customer_id')
                                                                    <div class="text-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
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
                                                                    <td><input type="text" id="grandTotal" class="form-control" readonly></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <button type="submit" class="btn btn-primary bg-primary">Confirm Order</button>
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



$(document).ready(function() {
    // Hide the original dropdown and replace with div-based dropdown
    $('#customer_div').on('click', function() {
        $('#customer_list').toggle();  // Toggle the visibility of the dropdown options
    });

    // When an option is selected
    $(document).on('click', '.customer_option', function() {
        var selectedText = $(this).text();
        var selectedValue = $(this).data('id');

        // Update the placeholder text with the selected option
        $('#customer_placeholder').text(selectedText);

        // Set the selected value in the hidden input
        $('#customer_id').val(selectedValue);

        // Hide the dropdown after selection
        $('#customer_list').hide();
    });

    // Fetch subcategories based on category selection
    $('#user_ID').on('change', function() {
        var userID = $(this).val();

        if (userID) {
            $.ajax({
                url: '{{ url('corporate_admin/get-customer') }}/' + userID,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#customer_list').empty();
                    $.each(response.data, function(index, customer) {
                        $('#customer_list').append(
                            '<div class="dropdown-item customer_option" data-id="' + customer.customer_id + '">' + customer.name + '</div>'
                        );
                    });
                },
                error: function() {
                    alert('Failed to fetch customer. Please try again.');
                }
            });
        } else {
            $('#customer_list').empty();
        }
    });
});

            </script>



@endsection
