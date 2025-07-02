@extends('layouts.app')
@section('content')

<style>
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        padding-left: 0px !important;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background-color: #00abc7;
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    .disabled-control {
        opacity: 0.5;
        pointer-events: none;
    }

    .select-all-btns {
        display: flex;
        gap: 4px;
    }
</style>

@can('frios_availability.view')
<div class="content-body default-height">
    <div class="container-fluid">

        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Dashboard \</h2>
                <p>Flavor Availability List</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive rounded">
                    <table id="example5" class="table customer-table display mb-4 fs-14 card-table">
                        <thead>
                            <tr>

                                <th>Orderable?</th>
                                <th>Flavor</th>
                                <th>Bulk Actions</th>
                                <th>Jan</th>
                                <th>Feb</th>
                                <th>Mar</th>
                                <th>Apr</th>
                                <th>May</th>
                                <th>Jun</th>
                                <th>Jul</th>
                                <th>Aug</th>
                                <th>Sep</th>
                                <th>Oct</th>
                                <th>Nov</th>
                                <th>Dec</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            @foreach ($flavors as $flavor)
                            <tr>
                                <td>
                                    @can('frios_availability.edit')
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="toggle-input" data-id="{{ $flavor->id }}" {{
                                            $flavor->orderable ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    @else
                                    <label class="toggle-switch disabled-control"
                                        title="You don't have permission to update availability">
                                        <input type="checkbox" disabled {{ $flavor->orderable ? 'checked' : '' }}>
                                        <span class="slider"></span>
                                    </label>
                                    @endcan
                                </td>
                                <td>
                                  <div class="flex items-center space-x-2">
                                        @if($flavor->image1)
                                        <img src="{{ asset('storage/'.$flavor->image1) }}" alt="{{ $flavor->name }}" class="rounded" style="width:40px; height:40px; object-fit:cover;">
                                        @endif
                                        <span>{{ $flavor->name }}</span>
                                        @if($flavor->categories->contains(function($cat) { return strtolower($cat->name) === 'signature'; }))
                                        <span class="badge bg-primary ms-1">S</span>
                                        @endif
                                    </div>
                                </td>
                                <td >
                                    @can('frios_availability.edit')
                                    <div class="select-all-btns">
                                        <button type="button" class="btn btn-sm btn-outline-primary select-all-months"
                                            data-flavor-id="{{ $flavor->id }}">All</button>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary unselect-all-months"
                                            data-flavor-id="{{ $flavor->id }}">None</button>
                                    </div>
                                    @endcan
                                </td>
                               @php $datesAvailable = $flavor->dates_available ?? []; @endphp
                                @foreach(range(1, 12) as $month)
                                <td>
                                    @can('frios_availability.edit')
                                    <input type="checkbox" class="month-checkbox" data-flavor-id="{{ $flavor->id }}"
                                        data-month="{{ $month }}" {{ in_array($month, $datesAvailable) ? 'checked' : ''
                                        }}>
                                    @else
                                    <input type="checkbox" disabled {{ in_array($month, $datesAvailable) ? 'checked'
                                        : '' }} title="You don't have permission to update availability">
                                    @endcan
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-warning text-center" role="alert">
                    <i class="ti ti-alert-circle fs-20 me-2"></i>
                    <strong>Access Denied!</strong> You don't have permission to view Frios Availability.
                </div>
            </div>
        </div>
    </div>
</div>
@endcan

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@push('scripts')
<script>
    $(document).ready(function () {
var hasUpdatePermission = {{ auth()->user()->can('frios_availability.edit') ? 'true' : 'false' }};
if (hasUpdatePermission) {
    // Toggle orderable
    $('#example5').on('change', '.toggle-input', function () {
        let flavorId = $(this).data('id');
        let orderable = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: `/fgpitem/update-status/${flavorId}`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                orderable: orderable
            },
            success: function (response) {
                if (!response.success) {
                    alert(response.message || "Error updating orderable status.");
                    $(this).prop('checked', !orderable);
                }
            },
            error: function (xhr) {
                alert("Error updating orderable status: " + (xhr.responseJSON?.message || "Access denied"));
                $(this).prop('checked', !orderable);
            }
        });
    });

    // Toggle month
    $('#example5').on('change', '.month-checkbox', function () {
        let flavorId = $(this).data('flavor-id');
        let month = $(this).data('month');
        let available = $(this).is(':checked') ? 1 : 0;
        $.ajax({
            url: `/fgpitem/update-month/${flavorId}`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                month: month,
                available: available
            },
            success: function (response) {
                if (!response.success) {
                    alert(response.message || "Error updating month availability.");
                    $(this).prop('checked', !available);
                }
            },
            error: function (xhr) {
                alert("Error updating month availability: " + (xhr.responseJSON?.message || "Access denied"));
                $(this).prop('checked', !available);
            }
        });
    });

    // Select all months
    $('#example5').on('click', '.select-all-months', function () {
        let flavorId = $(this).data('flavor-id');
        $(`.month-checkbox[data-flavor-id="${flavorId}"]`).each(function () {
            if (!$(this).is(':checked')) {
                $(this).prop('checked', true).trigger('change');
            }
        });
    });

    // Unselect all months
    $('#example5').on('click', '.unselect-all-months', function () {
        let flavorId = $(this).data('flavor-id');
        $(`.month-checkbox[data-flavor-id="${flavorId}"]`).each(function () {
            if ($(this).is(':checked')) {
                $(this).prop('checked', false).trigger('change');
            }
        });
    });
}
});
</script>
@endpush
@endsection
