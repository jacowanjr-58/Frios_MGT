@extends('layouts.app')
@section('content')
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
            @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

            <form action="{{ route('franchise.events.store') }}" method="post">
                @csrf
                <div class="form-group">
                    <label for="" class="form-label">Event Name</label>
                    <input type="text" name="event_name" class="form-control" value="{{ old('event_name') }}" >
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
                    $cleanDate = str_replace(' ', '+', $rawDate);

                    // Case 3: Special case — same start and end
                    if ($cleanDate === '2025-05-01T05:00:00+05:00') {
                        $parsedDate = Carbon::parse($cleanDate)->format('Y-m-d');
                        $start = $parsedDate;
                        $end = $parsedDate;

                    // Case 1: If datetime exists (has T), add 7 days
                    } elseif (str_contains($cleanDate, 'T')) {
                        $parsedDate = Carbon::parse($cleanDate);
                        $start = $parsedDate->format('Y-m-d');
                        $end = $parsedDate->copy()->addDays(7)->format('Y-m-d');

                    // Case 2: Pure date — only start_date
                    } else {
                        $start = Carbon::parse($cleanDate)->format('Y-m-d');
                        $end = null;
                    }
                }
            @endphp




<div class="row mt-2">
    <div class="col-md-6 form-group">
        <label for="" class="form-label">Start Date</label>
        <input type="date" class="form-control" name="start_date" value="{{ $start }}" readonly>
        @error('start_date') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 form-group">
        <label for="" class="form-label">End Date</label>
        <input type="date" class="form-control" name="end_date" value="{{ old('end_date', $end) }}">
        @error('end_date') <div class="text-danger">{{ $message }}</div> @enderror
    </div>
</div>


                <div class="row">
                    <div class="form-group col-md-4 mt-2">
                        <label for="" class="form-label">Event Status</label>
                        <select name="event_status" class="form-control">
                            <option value="">Please Select</option>
                            <option value="scheduled" {{ old('event_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="tentative" {{ old('event_status') == 'tentative' ? 'selected' : '' }}>Tentative</option>
                            <option value="staffed" {{ old('event_status') == 'staffed' ? 'selected' : '' }}>Staffed</option>
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
                            <option value="Festival/Fair" {{ old('event_type') == 'Festival/Fair' ? 'selected' : '' }}>Festival/Fair</option>
                            <option value="Corporate" {{ old('event_type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                            <option value="Wedding" {{ old('event_type') == 'Wedding' ? 'selected' : '' }}>Wedding</option>
                            <option value="Birthday Party" {{ old('event_type') == 'Birthday Party' ? 'selected' : '' }}>Birthday Party</option>
                            <option value="Private Catering" {{ old('event_type') == 'Private Catering' ? 'selected' : '' }}>Private Catering</option>
                            <option value="Holiday" {{ old('event_type') == 'Holiday' ? 'selected' : '' }}>Holiday</option>
                            <option value="Concert/Music" {{ old('event_type') == 'Concert/Music' ? 'selected' : '' }}>Concert/Music</option>
                            <option value="Pop-up Sales" {{ old('event_type') == 'Pop-up Sales' ? 'selected' : '' }}>Pop-up Sales</option>
                            <option value="Church/Religious" {{ old('event_type') == 'Church/Religious' ? 'selected' : '' }}>Church/Religious</option>
                            <option value="Other" {{ old('event_type') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('event_type') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-4 mt-2">
                        <label for="">Planned Paymnet</label>
                        <select name="planned_payment" id="planned_payment" class="form-control">
                            <option value="">Please Select</option>
                            <option value="cash" {{ old('planned_payment') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ old('planned_payment') == 'check' ? 'selected' : '' }}>check</option>
                            <option value="inovice" {{ old('planned_payment') == 'inovice' ? 'selected' : '' }}>Inovice</option>
                            <option value="credit-card" {{ old('planned_payment') == 'credit-card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 mt-2 form-group">
                        <label for="" class="form-label">Staff assigned</label>
                        <select name="staff_assigned[]" multiple class="form-control">
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->user_id }}" {{ collect(old('staff_assigned'))->contains($staff->user_id) ? 'selected' : '' }}>
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
                                <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                    </div>
                    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
                    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('select[name="staff_assigned"]').select2({
                                placeholder: "Please Select",
                            });
                        });

    const popsOptions = `{!! collect($pops)->map(fn($item) => "<option value='{$item->fgp_item_id}'>{$item->name}</option>")->implode('') !!}`;
    const orderOptions = `{!! collect($orderDetails)->map(fn($item) => "<option value='{$item->fgp_item_id}'>{$item->item_name} - (x{$item->total_units})</option>")->implode('') !!}`;

    function addRow(button) {
    let tableBody = document.getElementById("dynamicTable").getElementsByTagName("tbody")[0];

    // Create new row
    let newRow = document.createElement("tr");

    newRow.innerHTML = `
        <td>
            <select name="in_stock[]" class="form-control status-select" style="width: 100%">
                ${popsOptions}  <!-- Using dynamically created options for this select -->
            </select>
        </td>
        <td>
            <select name="orderable[]" class="form-control status-select" style="width: 100%">
                ${orderOptions}  <!-- Using dynamically created options for this select -->
            </select>
        </td>
        <td>
    <input type="number" class="form-control" name="quantity[]" min="0" placeholder="0"  />

        </td>
        <td>
            <span class="btn btn-success action-btn" onclick="addRow(this)">+</span>
            <span class="btn btn-danger action-btn" onclick="removeRow(this)">−</span>
        </td>
    `;

    // Append the new row to the table body
    tableBody.appendChild(newRow);

    // Refresh selectpicker on dynamically added selects
    $('.status-select').selectpicker('refresh');
}

// Make sure to bind the 'draw' event after the table is initialized or after any table redrawing.
table.on('draw', function () {
    $('.status-select').selectpicker('refresh');
});

// Function to prevent negative values while typing
function preventNegativeInput(event) {
    let input = event.target;

    // If the input value is negative, reset it to 0
    if (input.value < 0) {
        input.value = 0; // Set to 0 if negative value
    }

    // Optional: If the user types minus "-" symbol, remove it (to block negative numbers)
    if (input.value.startsWith('-')) {
        input.value = input.value.substring(1); // Remove the "-" sign
    }
}

// Apply this to all quantity fields to prevent negative input
$(document).on('input', 'input[name="quantity[]"]', preventNegativeInput);



        function removeRow(button) {
            let row = button.closest("tr");
            let table = document
                .getElementById("dynamicTable")
                .getElementsByTagName("tbody")[0];
            if (table.rows.length > 1) {
                row.remove();
            } else {
                alert("At least one row is !");
            }
        }
                    </script>

<div class="col-md-4 form-group mt-3">
    <label for="" class="form-label">Costs</label>
    <input type="text" name="costs" class="form-control" value="{{ old('costs') }}">
    @error('costs') <div class="text-danger">{{ $message }}</div> @enderror
</div>
                    <div class="col-md-4 form-group mt-3">
                        <label for="" class="form-label">Expected Sales</label>
                        <input type="text" name="expected_sales" class="form-control" value="{{ old('expected_sales') }}">
                        @error('expected_sales') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 form-group mt-3">
                        <label for="" class="form-label">Actual Sales</label>
                        <input type="text" name="actual_sales" class="form-control" value="{{ old('actual_sales') }}">
                        @error('actual_sales') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                </div>
                <div class="mt-3 form-group">
                    <label for="" class="form-label">Events notes</label>
                    <textarea name="event_notes" class="form-control" id="" cols="10" rows="5" placeholder="(Contact Infromation , Special Instructions, Agreements, Special Circumstances)">{{ old('event_notes') }}</textarea>
                    @error('event_notes') <div class="text-danger">{{ $message }}</div> @enderror

                </div>


                <div class="mt-4">
                    <h3><b>Resources Selection:</b></h3>
                    <div class="row mt-2">
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[van]" {{ old('resources_selection.van') ? 'checked' : '' }} type="checkbox" value="1"> Van
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[trailer]" {{ old('resources_selection.trailer') ? 'checked' : '' }} type="checkbox" value="1"> Trailer
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[cart]" {{ old('resources_selection.cart') ? 'checked' : '' }} type="checkbox" value="1"> Cart
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[cooler]" {{ old('resources_selection.cooler') ? 'checked' : '' }} type="checkbox" value="1"> Cooler
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[dry_ice]" {{ old('resources_selection.dry_ice') ? 'checked' : '' }} type="checkbox" value="1"> Dry Ice
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[freezer]" {{ old('resources_selection.freezer') ? 'checked' : '' }} type="checkbox" value="1"> Freezer
                            </label>
                        </div>
                        <div class="col-md-3 mt-2">
                            <label class="form-check-label">
                                <input class="form-check-input" name="resources_selection[direct_delivery]" {{ old('resources_selection.direct_delivery') ? 'checked' : '' }} type="checkbox" value="1"> Direct Delivery
                            </label>
                        </div>
                        @error('resources_selection.van') <div class="text-danger">{{ $message }}</div> @enderror
                        @error('resources_selection.trailer') <div class="text-danger">{{ $message }}</div> @enderror
                        @error('resources_selection.cart') <div class="text-danger">{{ $message }}</div> @enderror
                        @error('resources_selection.cooler') <div class="text-danger">{{ $message }}</div> @enderror
                        @error('resources_selection.dry_ice') <div class="text-danger">{{ $message }}</div> @enderror
                        @error('resources_selection.freezer') <div class="text-danger">{{ $message }}</div> @enderror
                        @error('resources_selection.direct_delivery') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>


                <table class="table table-bordered rounded mt-5" id="dynamicTable">
                    <thead>
                        <tr>
                            <th>In-Stock</th>
                            <th>Orderable</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (old('in_stock', [null]) as $i => $stock)
                        <tr>
                            <td>
                                <select name="in_stock[]" class="form-control">
                                    @foreach ($pops as $item)
                                        <option value="{{ $item->fgp_item_id }}" {{ old("in_stock.$i") == $item->fgp_item_id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("in_stock.$i") <div class="text-danger">{{ $message }}</div> @enderror
                            </td>
                            <td>
                                <select name="orderable[]" class="form-control">
                                    @foreach ($orderDetails as $item)
                                        <option value="{{ $item->fgp_item_id }}" {{ old("orderable.$i") == $item->fgp_item_id ? 'selected' : '' }}>
                                            {{ $item->item_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("orderable.$i") <div class="text-danger">{{ $message }}</div> @enderror
                            </td>
                            <td>
                                <input type="number" name="quantity[]" placeholder="0" class="form-control" value="{{ old("quantity.$i") }}" >
                                @error("quantity.$i") <div class="text-danger">{{ $message }}</div> @enderror
                            </td>
                            <td>
                                <span class="btn btn-success action-btn" onclick="addRow(this)">+</span>
                                <span class="btn btn-danger action-btn" onclick="removeRow(this)">−</span>
                            </td>
                        </tr>
                        @endforeach


                    </tbody>
                </table>

                <button class="btn btn-outline-primary">
                    Submit
                </button>

            </form>
        </div>
    </div>

@endsection
