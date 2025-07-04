@extends('layouts.app')

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

        /* .filter-input {
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
            } */

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

@section('content')
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">

            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Franchise List</p>
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                @can('franchises.create')
                    <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                        <a href="{{ route('franchise.create') }}"
                            class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Franchise</a>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                    @else
                        <div class="col-xl-12">
                        @endcan
                        <div class="card m-0">
                            <div class="card-body py-3 py-md-2">
                                <div class="d-sm-flex d-block align-items-center">
                                    <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                        <div class="p-2 fs-3"><i class="bi bi-buildings-fill"></i></div>
                                        <div class="media-body">
                                            <p class="mb-1 fs-12">Total Franchises</p>
                                            <h3 class="mb-0 font-w600 fs-22">{{ $totalFranchises }} Franchises</h3>
                                        </div>
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
                                    <div class="col-lg-4 col-md-4">
                                        <div class="filter-group">
                                            <label class="filter-label" for="stateFilter">
                                                <i class="fa fa-building me-1"></i>State
                                            </label>
                                            <select class="form-control select2" id="stateFilter"
                                                data-placeholder="Select State">
                                                <option value="">All States</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4">
                                        <div class="filter-group">
                                            <label class="filter-label" for="locationZipFilter">
                                                <i class="fa fa-map me-1"></i>Location Zip
                                            </label>
                                            <select class="form-control select2" id="locationZipFilter"
                                                data-placeholder="Select Location Zip">
                                                <option value="">All Location Zips</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 filter-actions">
                                        <button type="button" class="btn btn-filter-secondary" id="clearFilters">
                                            <i class="fa fa-refresh me-2"></i>Clear All
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive rounded">
                            <table id="franchise-table" class="table customer-table display mb-4 fs-14 card-table">
                                <thead>
                                    <tr>
                                        <th>Frios Territory Name</th>
                                        <th>Contact Number</th>
                                        <th>Business Name</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Territory Zip codes</th>
                                        @canany(['franchises.edit', 'franchises.delete'])
                                            <th>Actions</th>
                                        @endcanany
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
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var columns = [{
                    data: 'frios_territory_name',
                    name: 'frios_territory_name',
                    render: function(data, type, row) {
                        return data && data.trim() !== '' ? data : 'N/A';
                    }
                },
                {
                    data: 'contact_number',
                    name: 'contact_number',
                    render: function(data, type, row) {
                        return data && data.trim() !== '' ? data : 'N/A';
                    }
                },
                {
                    data: 'business_name',
                    name: 'business_name'
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'state',
                    name: 'state'
                },
                {
                    data: 'location_zipp',
                    name: 'location_zip'
                }
            ];

            // Add action column if user has permissions
            @canany(['franchises.edit', 'franchises.delete'])
                columns.push({
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                });
            @endcanany

            // Initialize DataTable
            var franchiseTable = $('#franchise-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: {
                    url: "{{ route('franchise.index') }}",
                    data: function(d) {
                        d.state = $('#stateFilter').val();
                        d.location_zip = $('#locationZipFilter').val();
                    }
                },
                columns: columns,
                order: [
                    [0, 'asc']
                ], // Order by business name
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    },
                    search: "Search Franchises:",
                    searchPlaceholder: "Type to search..."
                },
                drawCallback: function(settings) {
                    // Initialize SweetAlert confirmation for delete buttons
                    window.initSwalConfirm({
                        triggerSelector: '.delete-franchisee',
                        title: 'Delete Franchise',
                        text: 'Are you sure you want to delete this franchise? This action cannot be undone.',
                        confirmButtonText: 'Yes, delete franchise'
                    });

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

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                allowClear: true,
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });

            // Load business names and territory names for filters
            function loadFilterOptions() {
                $.ajax({
                    url: "{{ route('franchise.filter-options') }}",
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            // Populate business names
                            var businessSelect = $('#stateFilter');
                            businessSelect.empty().append(
                                '<option value="">All States</option>');
                            response.states.forEach(function(name) {
                                businessSelect.append('<option value="' + name + '">' + name +
                                    '</option>');
                            });

                            // Populate territory names
                            var territorySelect = $('#locationZipFilter');
                            territorySelect.empty().append(
                                '<option value="">All Location Zips</option>');
                            response.locationZips.forEach(function(name) {
                                territorySelect.append('<option value="' + name + '">' + name +
                                    '</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading filter options:', error);
                    }
                });
            }

            // Load initial filter options
            loadFilterOptions();

            // Filter controls
            $('#toggleFilters').click(function() {
                $('#filtersContent').slideToggle(300);
                const icon = $(this).find('i');
                if (icon.hasClass('fa-chevron-down')) {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });

            // Clear filters
            $('#clearFilters').click(function() {
                $('#stateFilter').val('').trigger('change');
                $('#locationZipFilter').val('').trigger('change');
                franchiseTable.ajax.reload();
            });

            // Auto-apply filters on change
            $('#stateFilter, #locationZipFilter').change(function() {
                franchiseTable.ajax.reload();
            });
        });
    </script>
@endpush
