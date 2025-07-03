{{-- Order Detail Modal Content --}}
<div class="order-details-container">
    {{-- Order Header --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-shopping-cart me-2"></i>Order Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-bold text-muted">Order Number:</td>
                            <td class="text-primary fw-bold">{{ $order->getOrderNum() }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Order Date:</td>
                            <td>{{ $order->created_at }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Payment Status:</td>
                            <td>
                                @if($order->is_paid)
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Total Amount:</td>
                            <td class="fw-bold text-success">
                                ${{ number_format(DB::table('fgp_order_items')->where('fgp_order_id', $order->id)->selectRaw('SUM(quantity * unit_price) as total')->value('total'), 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-info">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-building me-2"></i>Franchise Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="fw-bold text-muted">Business Name:</td>
                            <td class="fw-bold">{{ $order->user->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Territory:</td>
                            <td>{{ $order->franchise->business_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Contact Email:</td>
                            <td>{{ $order->user->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Phone:</td>
                            <td>{{ $order->user->phone ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Shipping Information --}}
    @if($order->ship_to_name || $order->ship_to_address1)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-truck me-2"></i>Shipping Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold text-muted">Recipient Name:</td>
                                    <td>{{ $order->ship_to_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Phone:</td>
                                    <td>{{ $order->ship_to_phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Address 1:</td>
                                    <td>{{ $order->ship_to_address1 ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Address 2:</td>
                                    <td>{{ $order->ship_to_address2 ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold text-muted">City:</td>
                                    <td>{{ $order->ship_to_city ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">State:</td>
                                    <td>{{ $order->ship_to_state ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">ZIP Code:</td>
                                    <td>{{ $order->ship_to_zip ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Full Address:</td>
                                    <td>{{ $order->fullShippingAddress() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Order Items --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-list me-2"></i>Order Items ({{ count($orderDetails) }} items)</h6>
                </div>
                <div class="card-body">
                    @if(count($orderDetails) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="35%">Item Name</th>
                                        <th width="15%" class="text-end">Unit Price</th>
                                        <th width="15%" class="text-center">Quantity</th>
                                        <th width="15%" class="text-end">Total Price</th>
                                        <th width="15%" class="text-center">Date Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach($orderDetails as $index => $detail)
                                        @php 
                                            $itemTotal = $detail->quantity * $detail->unit_price;
                                            $grandTotal += $itemTotal;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="item-icon me-3">
                                                        <i class="fa fa-cube text-primary fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark">{{ $detail->name ?? 'Unknown Item' }}</span>
                                                        <br><small class="text-muted">Item ID: {{ $detail->fgp_item_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">${{ number_format($detail->unit_price, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">{{ $detail->quantity }} cases</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-success">${{ number_format($itemTotal, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <small class="text-muted">
                                                    {{ $detail->created_at }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold text-dark fs-6">Grand Total:</td>
                                        <td class="text-end fw-bold text-success fs-5">${{ number_format($grandTotal, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-box-open text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No Items Found</h5>
                            <p class="text-muted">This order doesn't have any items associated with it.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <small><i class="fa fa-info-circle me-1"></i>Order created {{ $order->created_at }}</small>
                </div>
                <div class="btn-group" role="group">
                    @if(Auth::check() && Auth::user()->can('orders.edit'))
                        <a href="{{ route('franchise.orders.edit', ['franchise' => $franchiseId, 'orders' => $order->id]) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-edit me-1"></i>Edit Order
                        </a>
                    @endif
                    @if(Auth::check() && Auth::user()->can('orders.view'))
                        <a href="{{ url('/order/' . $order->id . '/create-ups-label') }}" 
                           class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fa fa-truck me-1"></i>Generate UPS Label
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-details-container {
        font-size: 0.9rem;
    }
    
    .card {
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        font-weight: 600;
    }
    
    .table td {
        padding: 0.5rem 0.75rem;
        vertical-align: middle;
    }
    
    .item-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 123, 255, 0.1);
        border-radius: 0.5rem;
    }
    
    .badge {
        font-size: 0.75rem;
    }
    
    .btn-group .btn {
        border-radius: 0.375rem;
        margin-left: 0.25rem;
    }
    
    .text-success {
        color: #198754 !important;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
</style> 