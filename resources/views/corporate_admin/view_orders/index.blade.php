@extends('layouts.app')
@section('content')
    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Pops Order List</p>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
                <div class="col-xl-12 col-lg-12">
                    <div class="card m-0">
                        <div class="card-body py-3 py-md-2">
                            <div class="d-sm-flex d-block align-items-center">
                                <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                    <div class="media-body">
                                        <p class="mb-1 fs-12">Total Orders Pops</p>
                                        <h3 class="mb-0 font-w600 fs-22">{{ $totalOrders }} Flavor Pops</h3>
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
                                <tr>
                                    <th>User Name</th>
                                    <th>Items Ordered</th>
                                    <th>Total Price</th>
                                    <th>Order Date/Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                @php
                                    $totalAmount = \DB::table('fgp_order_details')
                                        ->where('fgp_order_id', $order->fgp_ordersID)
                                        ->selectRaw('SUM(unit_number * unit_cost) as total')
                                        ->value('total');
                                        $franchisee = App\Models\Franchisee::where('franchisee_id' , $order->user_ID)->first();
                                        $customer = App\Models\Customer::where('customer_id' , $order->customer_id)->first();
                                @endphp
                                    <tr style="text-wrap: nowrap;">
                                        <td>{{ $customer?->name ?? $franchisee?->business_name }}</td>
                                        <td>
                                            <span class="cursor-pointer text-primary order-detail-trigger" data-id="{{ $order->fgp_ordersID }}">
                                                {{ \DB::table('fgp_order_details')->where('fgp_order_id', $order->fgp_ordersID)->count() }} items
                                            </span>
                                        </td>
                                        <td>${{ number_format($totalAmount, 2) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($order->date_transaction)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <select class="status-select" data-date="{{ $order->date_transaction }}"
                                                data-fgp-orders-id="{{ $order->fgp_ordersID }}">
                                                <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>
                                                    Pending</option>
                                                <option value="Paid" {{ $order->status == 'Paid' ? 'selected' : '' }}>Paid
                                                </option>
                                                <option value="Shipped"
                                                    {{ $order->status == 'Shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="Delivered"
                                                    {{ $order->status == 'Delivered' ? 'selected' : '' }}>Delivered
                                                </option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded text-secondary custom-hover" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
        $(document).on('change', '.status-select', function () {
            let select = $(this);
            let dateTransaction = select.data('date');
            let newStatus = select.val();
            let fgpOrdersId = select.data('fgp-orders-id');

            const data = {
                date_transaction: dateTransaction,
                status: newStatus,
                fgp_ordersID: fgpOrdersId
            };
            $.ajax({
                url: "/corporate_admin/vieworders/update-status",
                method: "POST",
                data: JSON.stringify(data),
                contentType: "application/json",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    console.log('Success:', response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('Error:', errorThrown);
                }
            });
        });
    });

        </script>
    <style>
        .pagination .page-item {
            margin: 0 5px;
        }

        .pagination .page-link {
            border: none;
            background-color: transparent;
            color: #00abc9;
            font-weight: bold;
            padding: 8px 12px;
            font-size: 1rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #00abc9;
            color: white !important;
            border-radius: 8px;
            min-width: 40px;
            text-align: center;
        }

        .pagination .page-link:hover {
            background-color: #e0f7fa;
            border-radius: 8px;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            pointer-events: none;
        }

        .pagination .custom-arrow {
            font-size: 1.2rem;
        }
    </style>
@endsection
@push('scripts')
<script>
$(document).on('click', '.order-detail-trigger', function () {
    const orderId = $(this).data('id');

    $.ajax({
        url: '{{ route('corporate_admin.vieworders.detail') }}',
        method: 'GET',
        data: { id: orderId },
        success: function (response) {
            let orderDetails = response.orderDetails;
            let detailsHtml = `
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col">Unit Cost</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            orderDetails.forEach(function(detail) {
                detailsHtml += `
                    <tr>
                        <td>${detail.name}</td>
                        <td>$${detail.unit_cost}</td>
                        <td>${detail.unit_number}</td>
                        <td>${detail.formatted_date}</td>
                    </tr>
                `;
            });

            detailsHtml += `</tbody></table>`;
            $('#orderModal .modal-body').html(detailsHtml);
            $('#orderModal').modal('show');
        },
        error: function () {
            alert('Error loading order details.');
        }
    });
});

</script>
@endpush
