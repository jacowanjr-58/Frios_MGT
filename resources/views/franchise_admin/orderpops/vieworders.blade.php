@extends('layouts.app')

@section('content')


        <div class="content-body default-height">
            <!-- row -->
			<div class="container-fluid">

				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Dashboard \</h2>
						<p>Orders</p>
					</div>

				</div>
                <div class="row mb-4 align-items-center">
                    <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                        <a href="{{ route('franchise.orderpops.create') }}" class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Order</a>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        <div class="card m-0">
                            <div class="card-body py-3 py-md-2">
                                <div class="d-sm-flex d-block align-items-center">
                                    <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                        <i class="bi bi-cart-check-fill me-3 fs-3 text-primary"></i>
                                        <div class="media-body">
                                            <p class="mb-1 fs-12">Total Orders</p>
                                            <h3 class="mb-0 font-w600 fs-22">{{ $totalOrders }} Orders</h3>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive rounded">
                            <table id="example5" class="table customer-table display mb-4 fs-14 card-table">
                                <thead>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Shipping</th>
                                    <th>Tracking</th>
                                    <th>Total</th>
                                    <th>Flavors</th>
                                    <th>Paid</th>
                                    <th>Delivered</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                <tr>
                                    <td><strong>FGP-{{ $order->fgp_ordersID }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($order->date_transaction)->format('M d, Y') }}</td>
                                    <td class="text-wrap">
                                       {{ $order->ship_to_name }}<br>
                                       {{ $order->fullShippingAddress() }}<br>
                                       {{ $order->ship_to_phone }}
                                    </td>
                                    <td>
                                        @if ($order->tracking_number)
                                            <a href="https://www.ups.com/track?tracknum={{ $order->tracking_number }}" target="_blank">
                                                {{ $order->tracking_number }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td class="text-wrap">
                                        {{ $order->flavorSummary() }}
                                    </td>
                                    <td>
                                        @if ($order->is_paid)
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$order->is_delivered)
                                            <form method="POST" action="{{ route('franchise.orderpops.markDelivered', $order->fgp_ordersID) }}"
                                                onsubmit="return confirm('Confirming Delivery will close out the order. Are you sure?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">Confirm</button>
                                            </form>
                                        @else
                                            <span class="badge bg-secondary">Delivered</span>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- table-responsive -->
                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col-12 -->
    </div> <!-- row -->
</div> <!-- container-fluid -->
@endsection


{{-- @extends('layouts.app')
@section('content')
 --}}
{{-- <div class="content-body default-height">
            <!-- row -->
	<div class="container-fluid">
        <div class="container mt-4">
            <h2>Orders Overview</h2>
            <p>Total Orders: {{ $totalOrders }}</p>

            @foreach($orders as $order)
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Order #: </strong> FGP-{{ $order->id }} <br>
                            <strong>Date: </strong> {{ $order->date_transaction->format('M d, Y') }} <br>
                            <strong>Paid:</strong>
                            @if ($order->is_paid)
                            <span class="badge bg-success">Yes</span>
                            @else
                            <span class="badge bg-danger">No</span>
                            @endif
                            <strong>Status: </strong> {{ ucfirst($order->status) }}
                        </div>
                        <div>
                        @if (!$order->is_delivered)
                            <form method="POST" action="{{ route('franchise.orderpops.markDelivered', ['order' => $order->fgp_ordersID]) }}"
                                onsubmit="return confirm('Confirming Delivery will close out the order. Are you sure?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Confirm Delivery</button>
                            </form>
                            @else
                            <span class="badge bg-secondary">Delivered</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Shipping Info</h6>
                                <p>
                                    {{ $order->ship_to_name }}<br>
                                    {{ $order->ship_to_address1 }} {{ $order->ship_to_address2 }}<br>
                                    {{ $order->ship_to_city }}, {{ $order->ship_to_state }} {{ $order->ship_to_zip }}<br>
                                    Phone: {{ $order->ship_to_phone }}
                                </p>
                                @if($order->tracking_number)
                                    <p>
                                        <strong>Tracking:</strong>
                                        <a href="https://www.ups.com/track?tracknum={{ $order->tracking_number }}" target="_blank">
                                            {{ $order->tracking_number }}
                                        </a>
                                    </p>
                                @endif
                                @if($order->shipstation_order_id)
                                    <p><strong>ShipStation Order:</strong> {{ $order->shipstation_order_id }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>Order Details</h6>
                                <p><strong>Total:</strong> ${{ number_format($order->items->sum(fn($i) => $i->unit_cost * $i->unit_number), 2) }}</p>
                                <p>
                                    @foreach($order->items as $item)
                                        ({{ $item->unit_number }}) - {{ $item->flavor->name ?? 'Unknown Flavor' }};<br>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection --}}
