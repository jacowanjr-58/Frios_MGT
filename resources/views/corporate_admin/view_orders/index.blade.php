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
                    <table id="orders-table" class="table customer-table display mb-4 fs-14 card-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date/Time</th>
                                <th>$</th>
                                <th>By</th>
                                <th>Ship To</th>
                                <th>Items</th>
                                <th>Issues</th>
                                <th>Status</th>
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
            var table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('corporate_admin.vieworders.index') }}",
                columns: [
                    { data: 'order_number', name: 'order_number' },
                    { data: 'date_time', name: 'date_transaction' },
                    { data: 'total_amount', name: 'total_amount' },
                    { data: 'ordered_by', name: 'ordered_by' },
                    { data: 'shipping_address', name: 'shipping_address' },
                    { data: 'items_count', name: 'items_count' },
                    { data: 'issues', name: 'issues' },
                    { data: 'status', name: 'status' },
                    { data: 'created_at', name: 'created_at', visible: false }
                ],
                order: [[8, 'desc']],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    }
                },
                drawCallback: function(settings) {
                    // Initialize Bootstrap Select on new elements
                    $('.status-select').selectpicker();
                    
                    // Add custom classes to pagination elements
                    $('.dataTables_paginate').addClass('paging_simple_numbers');
                    $('.paginate_button').each(function() {
                        if ($(this).hasClass('current')) {
                            $(this).attr('aria-current', 'page');
                        }
                    });
                }
            });

            // Order details modal handler
            $(document).on('click', '.order-detail-trigger', function() {
                const orderId = $(this).data('id');
                $.ajax({
                    url: '{{ route('corporate_admin.vieworders.detail') }}',
                    method: 'GET',
                    data: { id: orderId },
                    success: function(response) {
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
                    error: function() {
                        alert('Error loading order details.');
                    }
                });
            });

            // Status update handler
            $(document).on('change', '.status-select', function() {
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
                    url: "{{ route('corporate_admin.vieworders.updateStatus') }}",
                    method: "POST",
                    data: JSON.stringify(data),
                    contentType: "application/json",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('Success:', response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error:', errorThrown);
                    }
                });
            });
        });
    </script>
@endpush

@endsection
