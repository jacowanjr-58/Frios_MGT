@extends('layouts.app')
@section('content')

    @push('styles')
        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
            rel="stylesheet" />

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

            /* Professional Filter Styles */
            .filters-card {
                border: none;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border-radius: 0.5rem;
                margin-bottom: 1.5rem;
            }

            .filters-header {
                background: #00ABC7;
                color: white;
                padding: 1rem 1.5rem;
                border: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .filters-header h5 {
                margin: 0;
                font-weight: 600;
                font-size: 1.1rem;
                color: #fff;
            }

            .toggle-filters-btn {
                background: rgba(255, 255, 255, 0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                transition: all 0.3s ease;
            }

            .toggle-filters-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                border-color: rgba(255, 255, 255, 0.5);
                color: white;
                transform: translateY(-1px);
            }

            .filters-content {
                background: #f8f9fa;
                padding: 2rem;
                border-top: 1px solid #e9ecef;
            }

            .filter-group {
                margin-bottom: 1.5rem;
            }

            .filter-group:last-child {
                margin-bottom: 0;
            }

            .filter-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 0.5rem;
                font-size: 0.875rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .filter-input {
                border: 2px solid #e9ecef;
                border-radius: 0.5rem;
                padding: 0.75rem 1rem;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                background: white;
            }

            .filter-input:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
                outline: 0;
            }

            .filter-input::placeholder {
                color: #adb5bd;
                font-style: italic;
            }

            /* Select2 Custom Styling */
            .select2-container--bootstrap-5 .select2-selection {
                border: 2px solid #e9ecef !important;
                border-radius: 0.5rem !important;
                padding: 0.75rem 1rem !important;
                min-height: auto !important;
                font-size: 0.9rem !important;
                background: white !important;
            }

            .select2-container--bootstrap-5 .select2-selection:focus-within {
                border-color: #667eea !important;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15) !important;
            }

            .select2-container--bootstrap-5 .select2-selection__rendered {
                color: #495057 !important;
                padding: 0 !important;
            }

            .select2-container--bootstrap-5 .select2-selection__placeholder {
                color: #adb5bd !important;
                font-style: italic !important;
            }

            .filter-actions {
                display: flex;
                gap: 1rem;
                align-items: center;
                justify-content: flex-start;
                flex-wrap: wrap;
                margin-top: 0.8rem;
                padding-top: 1.5rem;
                border-top: 1px solid #e9ecef;
            }

            .btn-filter-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border: none;
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                font-weight: 600;
                font-size: 0.9rem;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
            }

            .btn-filter-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(102, 126, 234, 0.4);
                color: white;
            }

            .btn-filter-secondary {
                background: #6c757d;
                border: none;
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: 0.5rem;
                font-weight: 600;
                font-size: 0.9rem;
                transition: all 0.3s ease;
            }

            .btn-filter-secondary:hover {
                background: #5a6268;
                transform: translateY(-1px);
                color: white;
            }

            .filter-tip {
                color: #6c757d;
                font-size: 0.8rem;
                font-style: italic;
                margin-left: auto;
                display: flex;
                align-items: center;
            }

            /* Custom SweetAlert2 button styles */
            .swal2-confirm {
                background-color: #00ABC7 !important;
            }

            .swal2-cancel {
                background-color: #FF3131 !important;
            }

            .filter-tip i {
                margin-right: 0.5rem;
                color: #007bff;
            }

            .main-content-card {
                border: none;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border-radius: 0.5rem;
                overflow: hidden;
            }



            .stats-card .card-body {
                padding: 1.5rem;
            }

            .stats-number {
                font-size: 2rem;
                font-weight: 700;
                line-height: 1;
            }

            .stats-label {
                font-size: 0.9rem;
                opacity: 0.9;
                margin-bottom: 0.5rem;
            }

            @media (max-width: 768px) {
                .filters-content {
                    padding: 1rem;
                }

                .filter-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .filter-tip {
                    margin-left: 0;
                    margin-top: 1rem;
                    text-align: center;
                }
            }
        </style>
    @endpush
    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p class="mb-0 text-muted">Pops Order Management</p>
                </div>
            </div>
            <!-- Stats Card -->
            <div class="row mb-4 align-items-center">
              
                <div class="col-xl-12 col-lg-8">
                    <div class="card m-0">
                        <div class="card-body py-3 py-md-2">
                            <div class="d-flex align-items-center">
                                <div class="me-auto">
                                    <p class="stats-label mb-1">Total Orders</p>
                                    <h3 class="stats-number mb-0">{{ $totalOrders }}</h3>
                                </div>
                                <div class="stats-icon">
                                    <i class="fa fa-shopping-cart fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Filters Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card filters-card">
                        <div class="filters-header">
                            <h5><i class="fa fa-filter me-2"></i>Advanced Filters</h5>
                            <button type="button" class="btn toggle-filters-btn" id="toggleFilters">
                                <i class="fa fa-chevron-down me-1"></i> Toggle Filters
                            </button>
                        </div>
                        <div class="filters-content" id="filtersContent" style="display: none;">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="filter-group">
                                        <label class="filter-label" for="statusFilter">
                                            <i class="fa fa-info-circle me-1"></i>Order Status
                                        </label>
                                        <select class="form-control select2" id="statusFilter"
                                            data-placeholder="Select Status">
                                            <option value="">All Statuses</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Paid</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="filter-group">
                                        <label class="filter-label" for="shippingAddressFilter">
                                            <i class="fa fa-map-marker me-1"></i>Shipping Address
                                        </label>
                                        <select class="form-control filter-input select2" id="shippingAddressFilter"
                                            data-placeholder="Select Address">
                                            <option value="">All Addresses</option>
                                            <!-- Options populated dynamically -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="filter-group">
                                        <label class="filter-label" for="flavorFilter">
                                            <i class="fa fa-ice-cream me-1"></i>Flavor
                                        </label>
                                        <select class="form-control filter-input select2" id="flavorFilter"
                                            data-placeholder="Select Flavor">
                                            <option value="">All Flavors</option>
                                            <!-- Options populated dynamically -->
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-4 col-md-6">
                                    <div class="filter-group">
                                        <label class="filter-label" for="dateFromFilter">
                                            <i class="fa fa-calendar me-1"></i>Date From
                                        </label>
                                        <input type="date" class="form-control filter-input" id="dateFromFilter">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="filter-group">
                                        <label class="filter-label" for="dateToFilter">
                                            <i class="fa fa-calendar me-1"></i>Date To
                                        </label>
                                        <input type="date" class="form-control filter-input" id="dateToFilter">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="filter-actions">
                                        <button type="button" class="btn btn-filter-primary" id="applyFilters">
                                            <i class="fa fa-search me-2"></i>Apply Filters
                                        </button>
                                        <button type="button" class="btn btn-filter-secondary" id="clearFilters">
                                            <i class="fa fa-refresh me-2"></i>Clear All
                                        </button>
                                        <div class="filter-tip">
                                            <i class="fa fa-lightbulb"></i>
                                            Select2 dropdowns with search functionality
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card main-content-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="orders-table" class="table customer-table display mb-4 card-table">
                                    <thead>
                                        <tr>
                                            <th>Order Number</th>
                                            <th>Ordered By</th>
                                            <th>Franchise</th>
                                            <th>Flavors</th>
                                            <th>Shipping Address</th>
                                            <th>Total Amount</th>
                                            <th>Items</th>
                                            <th>Issues</th>
                                            <th>Status</th>
                                            <th>UPS Label</th>
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
                    <button type="button" class="btn btn-secondary rounded text-secondary custom-hover"
                        data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script>
            $(document).ready(function () {
                // Initialize Select2 dropdowns
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    allowClear: true,
                    placeholder: function () {
                        return $(this).data('placeholder');
                    }
                });

                // Initialize DataTable
                var ordersTable = $('#orders-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('franchise.orders', ['franchise' => $franchiseId]) }}", // Changed from franchiseeId to franchiseId  
                        data: function (d) {
                            d.status = $('#statusFilter').val();
                            d.shipping_address = $('#shippingAddressFilter').val();
                            d.flavor = $('#flavorFilter').val();
                            d.date_from = $('#dateFromFilter').val();
                            d.date_to = $('#dateToFilter').val();
                        }
                    },
                    columns: [
                        { data: 'order_number', name: 'fgp_orders.id' },
                        { data: 'ordered_by', name: 'ordered_by' },
                        { data: 'franchise', name: 'franchise' },
                        { data: 'flavors', name: 'flavors' },
                        { data: 'shipping_address', name: 'shipping_address' },
                        { data: 'total_amount', name: 'total_amount' },
                        { data: 'items_count', name: 'items_count' },
                        { data: 'issues', name: 'issues' },
                        { data: 'status', name: 'is_paid' },
                        { data: 'ups_label', name: 'ups_label' },
                        { data: 'date_time', name: 'fgp_orders.date_transaction' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ],
                    order: [[0, 'desc']],
                    pageLength: 25,
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right"></i>',
                            previous: '<i class="fa fa-angle-double-left"></i>'
                        }
                    }
                });

                // Load dropdowns data
                loadFlavors();
                loadShippingAddresses();

                // Filter controls
                $('#toggleFilters').click(function () {
                    $('#filtersContent').slideToggle(300);
                    const icon = $(this).find('i');
                    if (icon.hasClass('fa-chevron-down')) {
                        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    } else {
                        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    }
                });

                $('#applyFilters').click(function () {
                    ordersTable.ajax.reload();
                });

                $('#clearFilters').click(function () {
                    $('#statusFilter').val('').trigger('change');
                    $('#shippingAddressFilter').val('').trigger('change');
                    $('#flavorFilter').val('').trigger('change');
                    $('#dateFromFilter').val('');
                    $('#dateToFilter').val('');
                    ordersTable.ajax.reload();
                });

                // Auto-apply filters on change for select elements
                $('#statusFilter, #shippingAddressFilter, #flavorFilter').change(function () {
                    ordersTable.ajax.reload();
                });

                // Date filter changes
                $('#dateFromFilter, #dateToFilter').change(function () {
                    ordersTable.ajax.reload();
                });
            });

            function loadFlavors() {
                $.ajax({
                    url: '/franchise/{{ $franchiseId }}/orders/flavors',
                    method: 'GET',
                    success: function (response) {
                        var flavorSelect = $('#flavorFilter');
                        flavorSelect.empty();
                        flavorSelect.append('<option value="">All Flavors</option>');

                        if (response.success && response.flavors && response.flavors.length > 0) {
                            $.each(response.flavors, function (index, flavor) {
                                flavorSelect.append('<option value="' + flavor.fgp_item_id + '">' + flavor.name + '</option>');
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading flavors:', error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            function loadShippingAddresses() {
                $.ajax({
                    url: '/franchise/{{ $franchiseId }}/orders/shipping-addresses',
                    method: 'GET',
                    success: function (response) {
                        var addressSelect = $('#shippingAddressFilter');
                        addressSelect.empty();
                        addressSelect.append('<option value="">All Addresses</option>');

                        if (response.success && response.addresses) {
                            $.each(response.addresses, function (index, addr) {
                                addressSelect.append('<option value="' + addr.address + '">' + addr.address + '</option>');
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading shipping addresses:', error);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            function viewOrderDetails(orderId) {
                console.log('Loading order details for ID:', orderId);

                $.ajax({
                    url: '{{ route('franchise.orders.detail', ['franchise' => $franchiseId]) }}',
                    method: 'GET',
                    data: { id: orderId },
                    beforeSend: function () {
                        $('#orderModal .modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-2">Loading order details...</p></div>');
                        $('#orderModal .modal-title').text('Order Details - Loading...');
                        $('#orderModal').modal('show');
                    },
                    success: function (response) {
                        console.log('Order details loaded successfully');
                        $('#orderModal .modal-body').html(response);
                        $('#orderModal .modal-title').text('Order Details');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading order details:', error);
                        console.error('Response:', xhr.responseText);
                        $('#orderModal .modal-body').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle me-2"></i>Error loading order details. Please try again.<br><small>Error: ' + error + '</small></div>');
                        $('#orderModal .modal-title').text('Error Loading Order Details');
                    }
                });
            }

            function changeOrderStatus(orderId, status) {
                if (status === 'cancelled') {
                    // Show SweetAlert confirmation dialog
                    Swal.fire({
                        title: 'Cancel Order?',
                        text: 'Are you sure you want to cancel this order? This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, cancel order',
                        cancelButtonText: 'No, keep order',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User confirmed, proceed with cancellation
                            updateOrderStatus(orderId, status);
                        }
                    });
                } else {
                    // For other status changes, proceed directly
                    updateOrderStatus(orderId, status);
                }
            }

            function updateOrderStatus(orderId, status) {
                $.ajax({
                    url: "{{ route('franchise.orders.updateStatus', ['franchise' => $franchiseId]) }}",
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        // Show loading state
                        console.log('Updating order status...');
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message with SweetAlert
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                            
                            // Reload the table to reflect changes
                            $('#orders-table').DataTable().ajax.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Something went wrong!'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating order status:', error);
                        console.error('Response:', xhr.responseText);
                        
                        let errorMessage = 'Error updating order status. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                });
            }


        </script>
    @endpush

@endsection

@push('styles')
    <style>
        .btn.btn-primary {
            background: #00ABC7;
            color: white;
        }
    </style>
@endpush