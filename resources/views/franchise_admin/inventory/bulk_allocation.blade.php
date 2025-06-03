@extends('layouts.app')

@section('content')

<div class="flex justify-center">
    <div class="w-full max-w-6xl">
        <table class="table table-bordered w-full">
            <div class="container">
                <h2 class="mb-4">Bulk Inventory Allocation</h2>

                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('franchise.inventory.bulk-allocation.store') }}">
                    @csrf
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Item</th>
                                <th>Ordered</th>
                                <th>Allocated</th>
                                <th>Remaining</th>
                                <th>Location</th>
                                <th>Allocate Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            @foreach ($order->orderDetails ?? [] as $detail)
                            @php
                            $remaining = $detail->quantity - $detail->allocated_quantity;
                            @endphp
                            @if ($remaining > 0)
                            <tr>
                                <td>{{ $order->fgp_ordersID }}</td>
                                <td>{{ $detail->item->name ?? 'N/A' }}</td>
                                <td>{{ $detail->quantity }}</td>
                                <td>{{ $detail->allocated_quantity }}</td>
                                <td>{{ $remaining }}</td>
                                <td>
                                    <select name="allocations[{{ $detail->fgp_order_detail_id }}][location_id]"
                                        class="form-control">
                                        @foreach($locations as $loc)
                                        <option value="{{ $loc->location_id }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number"
                                        name="allocations[{{ $detail->fgp_order_detail_id }}][quantity]"
                                        class="form-control" max="{{ $remaining }}" min="1">
                                    <input type="hidden"
                                        name="allocations[{{ $detail->fgp_order_detail_id }}][fgp_order_detail_id]"
                                        value="{{ $detail->fgp_order_detail_id }}">
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary">Submit Allocations</button>
                </form>
            </div>

        </table>
    </div>
</div>
@endsection
