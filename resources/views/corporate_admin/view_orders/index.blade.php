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
                                <th>Order Number</th>
                                <th>Ordered By</th>
                                <th>Shipping Address</th>
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
                ajax: "{{ route('vieworders.index', ['franchisee' => $franchiseeId]) }}",
                columns: [
                    {data: 'order_number', name: 'fgp_orders.fgp_ordersID'},
                    {data: 'ordered_by', name: 'ordered_by'},
                    {data: 'shipping_address', name: 'shipping_address'},
                    {data: 'total_amount', name: 'total_amount'},
                    {data: 'status', name: 'fgp_orders.status'},
                    {data: 'date_time', name: 'fgp_orders.date_transaction'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                pageLength: 25
            });
        });

        function viewOrderDetails(orderId) {
            console.log('Loading order details for ID:', orderId); // Debug log
            
            $.ajax({
                url: '{{ route('vieworders.detail') }}',
                method: 'GET',
                data: { id: orderId },
                beforeSend: function() {
                    // Show loading indicator
                    $('#orderModal .modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading order details...</p></div>');
                    $('#orderModal .modal-title').text('Order Details - Loading...');
                    $('#orderModal').modal('show');
                },
                success: function(response) {
                    console.log('Order details loaded successfully'); // Debug log
                    // Handle HTML response and display in modal
                    $('#orderModal .modal-body').html(response);
                    $('#orderModal .modal-title').text('Order Details');
                },
                error: function(xhr, status, error) {
                    console.error('Error loading order details:', error);
                    console.error('Response:', xhr.responseText);
                    $('#orderModal .modal-body').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle me-2"></i>Error loading order details. Please try again.<br><small>Error: ' + error + '</small></div>');
                    $('#orderModal .modal-title').text('Error Loading Order Details');
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
                        fgp_ordersID: orderId,
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
<style script>
 .btn.btn-primary {
    background: #00ABC7;
    color: white;
}
</style>