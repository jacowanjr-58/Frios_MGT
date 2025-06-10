@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <!-- row -->
    <div class="container-fluid">
    <form method="POST" action="{{ route('franchise.inventory.adjust.update') }}">
       @csrf
        <div class="container">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Adjust Inventory</h2>
                        @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
					</div>

				</div>


            <table class="table table-bordered">
                <thead>
                    <tr>

                        <th>Item Name</th>
                        <th>Case Quantity</th>
                        <th>Unit Quantity</th>
                        <th>Total Quantity</th>
                        <th>Notes</th>

                    </tr>
                </thead>
                <tbody>
                @foreach($inventoryMasters as $master)
             <tr>
                    <td>{{ $master->item_name }}</td>
                    <td class="d-flex align-items-center">
                        <input type="number" name="total_cases_{{ $master->inventory_id }}" id="total_cases_{{ $master->inventory_id }}"
                            value="{{ $master->cases }}" min="0" class="form-control case-input"
                            data-id="{{ $master->inventory_id }}"
                           style="width: 12ch">
                        <div class="small"> ({{ $master->split_factor }} units/case)</div>
                    </td>
                    <td>
                        <input type="number" name="total_units_{{ $master->inventory_id }}" id="total_units_{{ $master->inventory_id }}"
                            value="{{ $master->units }}" min="0" class="form-control unit-input"
                            data-id="{{ $master->inventory_id }}"
                            style="width: 12ch">
                    </td>
                    <td>
                        <input type="number" name="total_quantity_{{ $master->inventory_id }}"
                            id="total_quantity_{{ $master->inventory_id }}" value="{{ $master->total_quantity }}" class="form-control"
                            style="width: 12ch"
                            readonly>
                        <input type="hidden" name="split_factor_{{ $master->inventory_id }}"
                            id="split_factor_{{ $master->inventory_id }}" value="{{ $master->split_factor }}">
                    </td>
                    <td>
                        <textarea name="notes_{{ $master->inventory_id }}" class="form-control">{{ $master->notes }}</textarea>
                </tr>
                @endforeach
                <tr>
                    <td colspan="5" class="text-center bg-white">
                        <button type="submit" class="btn btn-primary">Update Inventory</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>


    </div>
</div>

@push('scripts')<script>
document.addEventListener("DOMContentLoaded", function() {
    function recalcTotalQuantity(id) {
        const cases = parseInt(document.getElementById('total_cases_' + id).value) || 0;
        const units = parseInt(document.getElementById('total_units_' + id).value) || 0;
        const split = parseInt(document.getElementById('split_factor_' + id).value) || 1;
        document.getElementById('total_quantity_' + id).value = (cases * split) + units;
    }

    document.querySelectorAll('.case-input, .unit-input').forEach(function(input) {
        input.addEventListener('input', function() {
            recalcTotalQuantity(this.dataset.id);
        });
    });
});
</script>

@endpush

@endsection


