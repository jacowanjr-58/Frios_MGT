@extends('layouts.app')
@section('content')

@push('styles')
    <style>
        .dataTables_paginate.paging_simple_numbers {
            margin: 15px 0;
        }
        .paginate_button {
            padding: 8px 12px;
            margin: 0 4px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: #555;
        }
        .paginate_button.current {
            background-color: #007bff;
            color: white;
        }
        .paginate_button.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        .paginate_button:not(.disabled):hover {
            background-color: #f0f0f0;
        }
    </style>
@endpush

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
                                    <p class="mb-1 fs-12">Total Orders</p>
                                    <h3 class="mb-0 font-w600 fs-22">{{ $totalOrders }}</h3>
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
                    <table id="orders-table" class="table customer-table display mb-4 card-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Franchise</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
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

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('vieworders.index') }}",
                columns: [
                    {data: 'order_id', name: 'order_id'},
                    {data: 'customer_name', name: 'customer_name'},
                    {data: 'franchise_name', name: 'franchise_name'},
                    {data: 'total_amount', name: 'total_amount'},
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            var badgeClass = 'bg-secondary';
                            switch(data) {
                                case 'pending': badgeClass = 'bg-warning'; break;
                                case 'confirmed': badgeClass = 'bg-info'; break;
                                case 'processing': badgeClass = 'bg-primary'; break;
                                case 'shipped': badgeClass = 'bg-success'; break;
                                case 'delivered': badgeClass = 'bg-success'; break;
                                case 'cancelled': badgeClass = 'bg-danger'; break;
                            }
                            return '<span class="badge ' + badgeClass + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                        }
                    },
                    {data: 'created_at', name: 'created_at'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var actions = '<div class="dropdown">';
                            actions += '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">';
                            actions += '<i class="fa fa-cog"></i>';
                            actions += '</button>';
                            actions += '<ul class="dropdown-menu">';
                            actions += '<li><a class="dropdown-item" href="javascript:void(0)" onclick="viewOrderDetails(' + row.order_id + ')"><i class="fa fa-eye me-2"></i>View Details</a></li>';
                            actions += '<li><a class="dropdown-item" href="/vieworders/' + row.order_id + '/edit"><i class="fa fa-edit me-2"></i>Edit</a></li>';
                            actions += '<li><hr class="dropdown-divider"></li>';
                            actions += '<li><a class="dropdown-item text-danger" href="javascript:void(0)" onclick="changeOrderStatus(' + row.order_id + ', \'cancelled\')"><i class="fa fa-times me-2"></i>Cancel Order</a></li>';
                            actions += '</ul>';
                            actions += '</div>';
                            return actions;
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 25
            });
        });

        function viewOrderDetails(orderId) {
                $.ajax({
                url: '{{ route('vieworders.detail') }}',
                    method: 'GET',
                data: { order_id: orderId },
                    success: function(response) {
                    // Handle order details display
                        $('#orderModal').modal('show');
                    $('#orderModal .modal-body').html(response);
                    },
                    error: function() {
                    toastr.error('Failed to load order details');
                }
            });
        }

        function changeOrderStatus(orderId, status) {
            if (confirm('Are you sure you want to change the order status?')) {
                $.ajax({
                    url: "{{ route('vieworders.updateStatus') }}",
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        order_id: orderId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Order status updated successfully');
                            $('#orders-table').DataTable().ajax.reload();
                        } else {
                            toastr.error('Failed to update order status');
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred while updating order status');
                    }
                });
            }
        }
    </script>
@endpush

@endsection
