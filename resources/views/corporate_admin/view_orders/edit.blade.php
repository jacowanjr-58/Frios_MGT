{{-- filepath: resources/views/franchise_admin/orderpops/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Edit Order</h2>
                <p>Edit and update order details</p>
            </div>
            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-2"></i> Back
            </a>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Order #{{ $order->getOrderNum() }}</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="edit-order-form" action="{{ route('franchise.vieworders.update', ['franchise' => $franchiseeId, 'vieworders' => $order->id]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="table-responsive">
                                <table class="table mb-4 fs-14 card-table" id="order-items-table">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Cost</th>
                                            <th>Total</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderDetails as $index => $detail)
                                            <tr>
                                            <td>
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $detail->id }}">
                                                <input type="hidden" name="items[{{ $index }}][fgp_item_id]" value="{{ $detail->fgp_item_id }}">
                                                <input type="text" class="form-control" value="{{ $detail->flavor->name ?? $detail->item->name ?? 'Item' }}"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][unit_number]" class="form-control qty" min="1" value="{{ old("items.$index.unit_number", $detail->unit_number) }}" data-index="{{ $index }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][unit_cost]" class="form-control cost" step="0.01" value="{{ old("items.$index.unit_cost", $detail->unit_cost) }}" data-index="{{ $index }}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control total" readonly
                                                    value="{{ number_format($detail->unit_cost * $detail->unit_number, 2) }}" data-index="{{ $index }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-item-btn">Remove</button>
                                            </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success btn-sm" id="add-item-btn">Add Item</button>
                            </div>

                            {{-- Shipping Information --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ship_to_name" class="form-label">Recipient Name</label>
                                <input type="text" name="ship_to_name" id="ship_to_name" class="form-control" value="{{ old('ship_to_name', $order->ship_to_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ship_to_address1" class="form-label">Address 1</label>
                                <input type="text" name="ship_to_address1" id="ship_to_address1" class="form-control" value="{{ old('ship_to_address1', $order->ship_to_address1) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="ship_to_address2" class="form-label">Address 2</label>
                                <input type="text" name="ship_to_address2" id="ship_to_address2" class="form-control" value="{{ old('ship_to_address2', $order->ship_to_address2) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ship_to_city" class="form-label">City</label>
                                <input type="text" name="ship_to_city" id="ship_to_city" class="form-control" value="{{ old('ship_to_city', $order->ship_to_city) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ship_to_state" class="form-label">State</label>
                                <input type="text" name="ship_to_state" id="ship_to_state" class="form-control" value="{{ old('ship_to_state', $order->ship_to_state) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="ship_to_zip" class="form-label">ZIP</label>
                                <input type="text" name="ship_to_zip" id="ship_to_zip" class="form-control" value="{{ old('ship_to_zip', $order->ship_to_zip) }}">
                            </div>
                        </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold shadow">Update Order</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-danger, .btn-success {
        color: #fff !important;
        border: none !important;
    }
    .btn-danger {
        background-color: #dc3545 !important;
    }
    .btn-success {
        background-color: #198754 !important;
    }
    .btn-danger:hover, .btn-success:hover {
        opacity: 0.85;
    }
</style>
@endpush

@push('scripts')
<script>
window.fgpItemOptions = `{!! collect($allItems)->map(function($item) {
    return '<option value="' . $item->fgp_item_id . '">' . e($item->name) . '</option>';
})->implode('') !!}`;

document.addEventListener("DOMContentLoaded", function() {
    // Remove item row
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('tr').remove();
        });
    });

    // Add new item row
    document.getElementById('add-item-btn').addEventListener('click', function() {
        let idx = document.querySelectorAll('#order-items-table tbody tr').length;
        let row = `
        <tr>
            <td>
                <select name="items[${idx}][fgp_item_id]" class="form-select">
                    ${window.fgpItemOptions}
                </select>
            </td>
            <td>
                <input type="number" name="items[${idx}][unit_number]" class="form-control qty" min="1" value="1" data-index="${idx}">
            </td>
            <td>
                <input type="number" name="items[${idx}][unit_cost]" class="form-control cost" step="0.01" value="0.00" data-index="${idx}">
            </td>
            <td>
                <input type="text" class="form-control total" readonly value="0.00" data-index="${idx}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-item-btn">Remove</button>
            </td>
        </tr>
        `;
        document.querySelector('#order-items-table tbody').insertAdjacentHTML('beforeend', row);
        // Re-bind remove event
        document.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.onclick = function() { this.closest('tr').remove(); };
        });
    });

    // Calculate totals (reuse your existing JS from confirm.blade.php)
    // ...
});
</script>
@endpush
