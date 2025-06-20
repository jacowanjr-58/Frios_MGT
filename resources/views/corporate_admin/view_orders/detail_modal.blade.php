<div class="order-details-content">
    @if($order)
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="text-primary">Order Information</h5>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Order Number:</strong></td>
                        <td>{{ $order->getOrderNum() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Order Date:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($order->date_transaction)->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-secondary">{{ $order->status ?? 'Unknown' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="text-primary">Shipping Information</h5>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Ship To:</strong></td>
                        <td>{{ $order->ship_to_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>
                            {{ $order->ship_to_address1 ?? '' }}<br>
                            {{ $order->ship_to_address2 ? $order->ship_to_address2 . '<br>' : '' }}
                            {{ $order->ship_to_city ?? '' }}, {{ $order->ship_to_state ?? '' }} {{ $order->ship_to_zip ?? '' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <h5 class="text-primary">Order Items</h5>
            @if($orderDetails && $orderDetails->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th>Unit Cost</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Date Added</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($orderDetails as $detail)
                                @php 
                                    $itemTotal = $detail->unit_cost * $detail->unit_number;
                                    $grandTotal += $itemTotal;
                                @endphp
                                <tr>
                                    <td>{{ $detail->name ?? 'Unknown Item' }}</td>
                                    <td>${{ number_format($detail->unit_cost, 2) }}</td>
                                    <td>{{ $detail->unit_number }}</td>
                                    <td>${{ number_format($itemTotal, 2) }}</td>
                                    <td>{{ $detail->formatted_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <th colspan="3">Grand Total:</th>
                                <th>${{ number_format($grandTotal, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fa fa-info-circle me-2"></i>
                    No items found for this order.
                </div>
            @endif
        </div>
    </div>
</div> 