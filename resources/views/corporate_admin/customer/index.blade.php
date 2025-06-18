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
        /* Custom SweetAlert2 button styles */
        .swal2-confirm {
            background-color: #00ABC7 !important;
        }
        .swal2-cancel {
            background-color: #FF3131 !important;
        }
    </style>
@endpush

<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body default-height">
            <!-- row -->
			<div class="container-fluid">

				<div class="form-head mb-4 d-flex flex-wrap align-items-center">
					<div class="me-auto">
						<h2 class="font-w600 mb-0">Dashboard \</h2>
						<p>Customers</p>
					</div>

				</div>

                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-lg-6">
                        <div class="form-group">
                            <label for="franchise-filter" class="form-label">Filter by Franchise:</label>
                            <select id="franchise-filter" class="form-select select2 flex-grow-1">
                                <option value="">All Franchises</option>
                                @foreach(\App\Models\Franchisee::all() as $franchise)
                                    <option value="{{ $franchise->franchisee_id }}" 
                                        {{ request('franchise_filter') == $franchise->franchisee_id ? 'selected' : '' }}>
                                        {{ $franchise->business_name ?? 'N/A' }} - {{ $franchise->frios_territory_name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                </div>

                <div class="row mb-4 align-items-center">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card m-0">
                            <div class="card-body py-3 py-md-2">
                                <div class="d-sm-flex d-block align-items-center">
                                    <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                        <div class="media-body">
                                            <p class="mb-1 fs-12">Total Customers</p>
                                            <h3 class="mb-0 font-w600 fs-22">
                                                <span id="customer-count">{{ $customerCount }}</span> 
                                                <span id="franchise-label">Customers</span>
                                            </h3>
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
                            <table id="customer-table" class="table customer-table display mb-4 fs-14 card-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Franchise</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>

            </div>

        </div>

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#customer-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('franchise_customer') }}",
                    data: function (d) {
                        d.franchise_filter = $('#franchise-filter').val();
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'franchise', name: 'franchise' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', visible: false }
                ],
                order: [[5, 'desc']], // Order by created_at column
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    }
                },
                drawCallback: function(settings) {
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

            // Handle franchise filter change
            $('#franchise-filter').on('change', function() {
                var selectedFranchise = $(this).val();
                var selectedText = $(this).find('option:selected').text();
                
                // Update the customer count display
                updateCustomerCount(selectedFranchise, selectedText);
                
                // Refresh table
                table.draw();
            });

            // Function to update customer count dynamically
            function updateCustomerCount(franchiseId, franchiseText) {
                if (franchiseId) {
                    // Show loading state
                    $('#customer-count').text('Loading...');
                    $('#franchise-label').text('Customers');
                    
                    // Make AJAX call to get franchise-specific customer count
                    $.ajax({
                        url: '{{ route("franchise_customer") }}',
                        type: 'GET',
                        data: {
                            franchise_filter: franchiseId,
                            count_only: true
                        },
                        success: function(response) {
                            if (response.count !== undefined) {
                                $('#customer-count').text(response.count);
                            }
                        },
                        error: function() {
                            $('#customer-count').text('Error');
                        }
                    });
                } else {
                    // Show total count for all franchises
                    $('#customer-count').text('{{ $customerCount }}');
                    $('#franchise-label').text('Customers');
                }
            }

            // Initialize select2 for franchise filter
            $('#franchise-filter').select2({
                placeholder: 'Select a franchise...',
                allowClear: true
            });

            // Auto-select franchise if URL parameter exists and update count
            var initialFilter = '{{ request("franchise_filter") }}';
            if (initialFilter) {
                $('#franchise-filter').val(initialFilter);
                var selectedText = $('#franchise-filter').find('option:selected').text();
                updateCustomerCount(initialFilter, selectedText);
                $('#franchise-filter').trigger('change.select2');
            }
        });
    </script>
@endpush

@endsection
