@extends('layouts.app')

@section('content')
@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

        .swal2-confirm {
            background-color: #00ABC7 !important;
        }

        .swal2-cancel {
            background-color: #FF3131 !important;
        }
    </style>
@endpush

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
                @can('orders.create')
                    <a href="{{ route('franchise.orderpops.create', ['franchisee' => request()->route('franchisee')]) }}"
                        class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Order</a>
                @endcan
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
                    <table id="ordersTable" class="table customer-table display mb-4 fs-14 card-table">
                        <thead>
                            <tr>
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
                    </table>
                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->
</div> <!-- content-body -->
</div> <!-- row -->
</div> <!-- container-fluid -->

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('franchise.orderpops.view', request()->route('franchisee')) }}"
                },
                columns: [
                    { data: 'order_number', name: 'order_number' },
                    { data: 'date', name: 'date_transaction' },
                    { data: 'shipping', name: 'ship_to_name' },
                    { data: 'tracking', name: 'tracking_number' },
                    { data: 'total', name: 'total' },
                    { data: 'flavors', name: 'flavors' },
                    { data: 'paid_status', name: 'is_paid' },
                    { data: 'delivery_status', name: 'is_delivered' }
                ],
                order: [[1, 'desc']],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    }
                },
                pageLength: 25,
                drawCallback: function (settings) {

                    // Add custom classes to pagination elements
                    $('.dataTables_paginate').addClass('paging_simple_numbers');
                    $('.paginate_button').each(function() {
                        if ($(this).hasClass('current')) {
                            $(this).attr('aria-current', 'page');
                        }
                    });
                    $('.paginate_button.previous, .paginate_button.next').attr({
                        'role': 'link',
                        'aria-disabled': function() {
                            return $(this).hasClass('disabled') ? 'true' : 'false';
                        }
                    });
                }
            });
        });
    </script>
@endpush

