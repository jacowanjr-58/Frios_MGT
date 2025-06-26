@extends('layouts.app')

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style scoped>
        .dataTables_paginate.paging_simple_numbers {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0;
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

        /* DataTables search box styling */
        .dataTables_filter {
            margin-bottom: 15px;
        }

        .dataTables_filter input {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-left: 8px;
        }

        .dataTables_filter input:focus {
            outline: none;
            border-color: #00ABC7;
            box-shadow: 0 0 0 0.2rem rgba(0, 171, 199, 0.25);
        }

        .statistics-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .statistics-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-light {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }

        .bg-success-light {
            background-color: rgba(var(--bs-success-rgb), 0.1);
        }

        .bg-info-light {
            background-color: rgba(var(--bs-info-rgb), 0.1);
        }

        .bg-warning-light {
            background-color: rgba(var(--bs-warning-rgb), 0.1);
        }

        .bg-danger-light {
            background-color: rgba(var(--bs-danger-rgb), 0.1);
        }

        .dropdown-item.active {
            background-color: var(--bs-primary);
            color: white;
        }

        .filter-loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .filter-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            z-index: 10;
        }

        .filter-loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--bs-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .card {
            border-radius: 12px;
        }

        .btn {
            border-radius: 8px;
        }

        /* Enhanced Dashboard Styles */
        .rank-badge .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
        }

        .button#dateFilterDropdown {
            background-color: #00ABC7 !important;
            color: #fff !important;
        }

        .badge-warning {
            background-color: #ffc107 !important;
            color: #fff !important;
        }

        .badge-secondary {
            background-color: #6c757d !important;
        }

        .badge-info {
            background-color: #0dcaf0 !important;
            color: #fff !important;
        }

        .flavor-image img {
            border: 2px solid #e9ecef;
        }

        .allocation-item {
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }

        .allocation-item:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--bs-primary) 0%, #0056b3 100%);
        }

        .text-white-50 {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        .height300 {
            height: 300px;
        }

        /* Calendar Event Styles */
        .fc-event-scheduled {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .fc-event-tentative {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #212529 !important;
        }

        .fc-event-staffed {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
        }

        .fc-event-title {
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    @can('flavors.view')
        <!--**********************************
                                                    Content body start
                                                ***********************************-->
        <div class="content-body default-height">
            <!-- row -->
            <div class="container-fluid">


                <div class="row">
                    <!-- Delivered Orders Card -->
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card statistics-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="stat-number mb-0" id="totalCustomersStat">
                                            {{ Number::abbreviate($deliveredOrders ?? 0) }}
                                        </h2>
                                        <h6 class="stat-label text-muted mb-0">Delivered</h6>
                                    </div>
                                    <div class="stat-icon bg-success-light">
                                        <i class="fa fa-check-circle text-success"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Shipped Orders Card -->
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card statistics-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="stat-number mb-0" id="totalFlavorsStat">
                                            {{ Number::abbreviate($shippedOrders ?? 0) }}
                                        </h2>
                                        <h6 class="stat-label text-muted mb-0">Shipped</h6>
                                    </div>
                                    <div class="stat-icon bg-info-light">
                                        <i class="fa fa-truck text-info"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Paid Orders Card -->
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card statistics-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="stat-number mb-0" id="totalOrdersStat">
                                            {{ Number::abbreviate($paidOrders ?? 0) }}
                                        </h2>
                                        <h6 class="stat-label text-muted mb-0">Paid</h6>
                                    </div>
                                    <div class="stat-icon bg-primary-light">
                                        <i class="fa fa-credit-card text-primary"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- Pending Orders Card -->
                    <div class="col-xl-3 col-lg-6 col-sm-6">
                        <div class="card statistics-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h2 class="stat-number mb-0" id="totalPopOrdersStat">
                                            {{ Number::abbreviate($pendingOrders ?? 0) }}
                                        </h2>
                                        <h6 class="stat-label text-muted mb-0">Pending</h6>
                                    </div>
                                    <div class="stat-icon bg-warning-light">
                                        <i class="fa fa-clock text-warning"></i>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
               
                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0">Dashboard \ Inventory</h2>
                        <p>Delivered orders</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive rounded">
                            <table id="orders-table" class="table customer-table display mb-4 fs-14 card-table">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Items Ordered</th>
                                        <th>Status</th>
                                        <th>Total Price</th>
                                        <th>Order Date/Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="content-body default-height">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="alert alert-warning text-center" role="alert">
                                    <i class="ti ti-alert-circle fs-20 me-2"></i>
                                    <strong>Access Denied!</strong> You don't have permission to view Flavor Categories.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <div class="modal fade" id="modalId" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
        aria-labelledby="modalTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Body</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!--**********************************
                                    Content body end
                                ***********************************-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.orderable-dropdown').change(function () {
                let itemId = $(this).data('id');
                let orderableValue = $(this).val();

                $.ajax({
                    url: "{{ route('franchise.fgpitem.updateOrderable', ['franchise' => $franchise_id]) }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        item_id: itemId,
                        pop_orderable: orderableValue
                    },
                    success: function (response) {
                        console.log('Success:', response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>

    <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Table for displaying order details -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded text-secondary custom-hover"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
                    // Initialize DataTable
        var table = $('#orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('franchise.flavors', ['franchise' => $franchise_id]) }}",
                type: "GET",
                error: function (xhr, error, code) {
                    console.log('DataTables Ajax Error:', xhr, error, code);
                    alert('Error loading data. Please check console for details.');
                }
            },
            columns: [
                { 
                    data: 'user_name', 
                    name: 'user_name',
                    searchable: true,
                    orderable: true
                },
                { 
                    data: 'items_ordered', 
                    name: 'items_ordered', 
                    searchable: false,
                    orderable: false
                },
                { 
                    data: 'status', 
                    name: 'status',
                    searchable: true,
                    orderable: true
                },
                { 
                    data: 'total_price', 
                    name: 'total_price', 
                    searchable: false,
                    orderable: true
                },
                { 
                    data: 'order_date', 
                    name: 'order_date',
                    searchable: true,
                    orderable: true
                }
            ],
            order: [[4, 'desc']], // Sort by order_date descending by default
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right"></i>',
                    previous: '<i class="fa fa-angle-double-left"></i>'
                },
                search: "Search orders:",
                searchPlaceholder: "Enter business name, status, or date...",
                processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>'
            },
            drawCallback: function (settings) {
                $('.dataTables_paginate').addClass('paging_simple_numbers');
            },
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    if (column.search() !== this.search()) {
                        column.search(this.search()).draw();
                    }
                });
            }
        });

            // Handle order detail modal (delegated event handler for dynamically loaded content)
            $(document).on('click', '.order-detail-trigger', function () {
                const orderId = $(this).data('id');

                $.ajax({
                    url: "{{ route('franchise.flavors.detail', ['franchise' => $franchise_id]) }}",
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

                        orderDetails.forEach(function (detail) {
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
        });
    </script>
@endpush