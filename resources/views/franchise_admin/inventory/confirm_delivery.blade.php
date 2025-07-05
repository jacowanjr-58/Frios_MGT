{{-- /**
 * Confirm Delivery View for Franchise Admin
 *
 * This view allows franchise admins to confirm the delivery of an order,
 * including entering received quantities and any discrepancies.
 *
 * @package FranchiseAdmin
 */ --}}

{{--
    Confirm Delivery View for Franchise Admin
    (resources/views/franchise_admin/inventory/confirm_delivery.blade.php)
--}}

@extends('layouts.app')

@section('content')
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="row">
            {{-- @dd($order); --}}

            @if ($errors->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first('error') }}
            </div>
            @endif

            <div class="container mx-auto px-4">
                <h1 class="text-2xl font-bold mb-4">Confirm Delivery for Order #FGP-{{ $orders->id }}</h1>

                <form
                    action="{{ route('franchise.inventory.confirm_delivery.store', ['orders' => $orders->id, 'franchise' => $franchise]) }}"
                    method="POST"
                    x-data="{}"
                    class="bg-white p-6 rounded shadow"
                >
                    @csrf

                    {{-- Order header info --}}
                    <div class="mb-6 p-4 bg-gray-100 rounded">
                       <p><strong>Order Date:</strong>
                            {{ $orders->created_at ? \Carbon\Carbon::parse($orders->created_at)->format('M d, Y') : 'N/A' }}                    </p>
                        <p><strong>Franchise:</strong> {{ $orders->franchise->frios_territory_name ?? 'N/A' }}</p>
                        {{-- any other summary fields --}}
                    </div>

                    {{-- Table of line items --}}
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto border-collapse">
                            <thead>
                                <tr class="bg-gray-200">
                                    <th class="px-4 py-2 border text-left">Item</th>
                                    <th class="px-4 py-2 border text-center">Cases Ordered</th>
                                    <th class="px-4 py-2 border text-center">Cases Received</th>
                                    <th class="px-4 py-2 border text-center">Damaged Units</th> {{-- New column --}}
                                    <th class="px-4 py-2 border text-left">Discrepancy Notes (Did not arrive, melted pop, broken stick, etc.)</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($orders->orderItems as $detail)

                                    <tr class="@if($loop->even) bg-white @else bg-gray-50 @endif">
                                        {{-- 1) Display the item name --}}
                                        <td class="px-4 py-2 border">
                                            {{ $detail->item->name ?? "Item #{$detail->fgp_item_id}" }}
                                        </td>

                                        {{-- 2) Show how many were ordered --}}
                                        <td class="px-4 py-2 border text-center">
                                            {{ $detail->quantity }}
                                            <input
                                                type="hidden"
                                                name="ordered_qty[{{ $detail->id }}]"
                                                value="{{ $detail->quantity }}"
                                                id="ordered_qty_{{ $detail->id }}">
                                             <input
                                                type="hidden"
                                                name="splitfactor_id[{{ $detail->id }}]"
                                                value="{{ $detail->item->split_factor }}"
                                                id="splitfactor_{{ $detail->id }}">
                                                @php
                                                    $totalUnits = $detail->quantity * $detail->item->split_factor;
                                                @endphp

                                              <br> (Total Pops = {{ $totalUnits }} )
                                        </td>

                                        {{-- 3) Input: how many actually arrived --}}
                                        <td class="px-4 py-2 border text-center">
                                            <input
                                                type="number"
                                                name="received_qty[{{ $detail->id }}]"
                                                id="received_qty_{{ $detail->id }}"
                                                value="{{ old("received_qty.{$detail->id}", $detail->quantity) }}"
                                                min="0"
                                                class="w-20 px-2 py-1 border rounded text-center"
                                                required
                                            >
                                            @error("received_qty.{$detail->id}")
                                                <p class="js-error-message text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </td>

                                        {{-- 4) Input: damaged units --}}
                                        <td class="px-4 py-2 border text-center">
                                            <input type="number" name="damaged_units[{{ $detail->id }}]" id="damaged_units_{{ $detail->id }}" value="{{ old("
                                                damaged_units.{$detail->id}", 0) }}"
                                                min="0" class="w-20 px-2 py-1 border rounded text-center"
                                                required>
                                            {{-- Note: This field is required, but can be 0 --}}

                                            @error("damaged_units.{$detail->id}")
                                            <p class="js-error-message text-red-600 text-sm mt-1">{{ $message }}</p>
                                            @enderror
                                        </td>

                                        {{-- 5) Textarea: any discrepancy note --}}
                                        <td class="px-4 py-2 border">
                                            <textarea
                                                name="notes[{{ $detail->id }}]"
                                                id="notes_{{ $detail->id }}"
                                                rows="2"
                                                class="w-full px-2 py-1 border rounded"
                                                placeholder=""
                                            >{{ old("notes.{$detail->id}") }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- [Optional] Overall comments (not currently consumed by controller, but left here for future) --}}
                    {{--
                    <div class="mt-6">
                        <label for="overall_notes" class="block font-medium">Overall Comments:</label>
                        <textarea
                            name="overall_notes"
                            id="overall_notes"
                            rows="3"
                            class="w-full px-2 py-1 border rounded"
                            placeholder="E.g. Driver left one box at front door…"
                        >{{ old('overall_notes') }}</textarea>
                    </div>
                    --}}

                    {{-- Submit button --}}
                    <div class="mt-6 flex items-center justify-end">
                        <a href="{{ route('franchise.orders', ['franchise' => $franchise]) }}"
                           class="px-4 py-2 mr-4 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                            Cancel
                        </a>
                        <button
                            type="submit"
                            style="background: hotpink; color: black; padding: 1rem;"
                        >
                            Confirm &amp; Add to Inventory
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
document.querySelector('form').addEventListener('submit', function(e) {
    let valid = true;
    let errorMessages = [];

    // First, remove previous highlights
    document.querySelectorAll('.input-error').forEach(el => {
        el.classList.remove('input-error');
    });

    @foreach($orders->orderItems as $detail)
        let receivedInput = document.getElementById('received_qty_{{ $detail->id }}');
        let damagedInput = document.getElementById('damaged_units_{{ $detail->id }}');
        let ordered = parseInt(document.getElementById('ordered_qty_{{ $detail->id }}').value) || 0;
        let splitfactor = parseInt(document.getElementById('splitfactor_{{ $detail->id }}').value) || 1;
        let received = parseInt(receivedInput.value) || 0;
        let damaged = parseInt(damagedInput.value) || 0;

        let orderedTotal = ordered * splitfactor;
        let receivedTotal = (received * splitfactor) - damaged;

        if (orderedTotal < receivedTotal) {
            valid = false;
            errorMessages.push("For item '{{ $detail->item->name ?? "Item #{$detail->fgp_item_id}" }}', received quantity minus damaged units exceeds ordered quantity.");
            receivedInput.classList.add('input-error');
            damagedInput.classList.add('input-error');
        }
    @endforeach

    if (!valid) {
        e.preventDefault();
    }
});
</script>
@endpush
@push('styles')
<style>
    /* Match card/table style from corporate_admin/orders/index.blade.php */
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .card-header {
        background: #00ABC7;
        color: white;
        font-weight: 600;
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }

    .card-title {
        margin-bottom: 0;
        font-weight: 600;
        font-size: 1.1rem;
        color: #fff;
    }

    .table {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .table th,
    .table td {
        padding: 0.75rem 1rem;
        vertical-align: middle;
        border: 1px solid #e9ecef;
    }

    .table thead th {
        background: #f8f9fa;
        color: #495057;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table tbody tr:nth-child(even) {
        background: #f8f9fa;
    }

    .table tbody tr:nth-child(odd) {
        background: #fff;
    }

    .form-control,
    input[type="number"],
    textarea {
        border: 2px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background: white;
        transition: border-color 0.2s;
    }

    .form-control:focus,
    input[type="number"]:focus,
    textarea:focus {
        border-color: #00ABC7;
        box-shadow: 0 0 0 0.2rem rgba(0, 171, 199, 0.15);
        outline: 0;
    }

    .input-error {
        border: 2px solid #e3342f !important;
        background: #fdecea !important;
    }

    .btn-primary,
    button[type="submit"] {
        background: #00ABC7;
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.2s;
    }

    .btn-primary:hover,
    button[type="submit"]:hover {
        background: #007bff;
        color: white;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.2s;
    }

    .btn-secondary:hover {
        background: #5a6268;
        color: white;
    }

    .bg-gray-100 {
        background: #f8f9fa !important;
    }

    .bg-gray-200 {
        background: #e9ecef !important;
    }

    .rounded {
        border-radius: 0.5rem !important;
    }

    .text-primary {
        color: #00ABC7 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .text-success {
        color: #198754 !important;
    }

    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }
</style>
@endpush
@endsection
