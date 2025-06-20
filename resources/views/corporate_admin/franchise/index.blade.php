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

    {{-- <div class="container">
        <h1>Franchise List</h1>
        <a href="{{ route('franchise.create') }}" class="btn btn-primary">Add Franchise</a>

        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>Business Name</th>
                    <th>Address</th>
                    <th>State</th>
                    <th>Zip Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($franchises as $franchise)
                <tr>
                    <td>{{ $franchise->business_name }}</td>
                    <td>{{ $franchise->address1 }} {{ $franchise->address2 }}</td>
                    <td>{{ $franchise->state }}</td>
                    <td>{{ $franchise->zip_code }}</td>
                    <td>
                        <a href="{{ route('franchise.edit', $franchise->franchisee_id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('franchise.destroy', $franchise->franchisee_id) }}" method="POST"
                            style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> --}}
    <!--**********************************
                        Content body start
                    ***********************************-->
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


                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive rounded">
                            <table id="franchise-table" class="table customer-table display mb-4 fs-14 card-table">
                                <thead>
                                    <tr>
                                        {{-- <th>Sr No.</th> --}}
                                        <th>Business Name</th>
                                        <th>Contact Number</th>
                                        <th>
                                            <a href="javascript:void(0)" class="text-decoration-none">Total Customers</a>
                                        </th>
                                        <th>Frios Territory Name</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Zip Code</th>
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
        <!--**********************************
                        Content body end
                    ***********************************-->

        @push('scripts')
            <script>
                $(document).ready(function () {
                    $('#franchise-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('franchise.index') }}",
                        columns: [
                            { data: 'business_name', name: 'business_name' },
                            {
                                data: 'contact_number',
                                name: 'contact_number',
                                render: function (data, type, row) {
                                    return data && data.trim() !== '' ? data : 'N/A';
                                }
                            },
                            { data: 'customer_count', name: 'customer_count', orderable: false, searchable: false },
                            {
                                data: 'frios_territory_name',
                                name: 'frios_territory_name',
                                render: function (data, type, row) {
                                    return data && data.trim() !== '' ? data : 'N/A';
                                }
                            },

                            { data: 'city', name: 'city' },
                            { data: 'state', name: 'state' },
                            { data: 'zip_code', name: 'zip_code' },
                            { data: 'location_zip', name: 'location_zip' },
                            { data: 'action', name: 'action', orderable: false, searchable: false },
                            { data: 'created_at', name: 'created_at', visible: false }
                        ],
                        order: [[6, 'desc']], // Order by created_at column
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right"></i>',
                                previous: '<i class="fa fa-angle-double-left"></i>'
                            }
                        },
                        drawCallback: function (settings) {
                            // Initialize SweetAlert confirmation for delete buttons
                            window.initSwalConfirm({
                                triggerSelector: '.delete-franchisee',
                                title: 'Delete Franchise',
                                text: 'Are you sure you want to delete this franchise? This action cannot be undone.',
                                confirmButtonText: 'Yes, delete franchise'
                            });

                            // Add custom classes to pagination elements
                            $('.dataTables_paginate').addClass('paging_simple_numbers');
                            $('.paginate_button').each(function () {
                                if ($(this).hasClass('current')) {
                                    $(this).attr('aria-current', 'page');
                                }
                            });
                            $('.paginate_button.previous, .paginate_button.next').attr({
                                'role': 'link',
                                'aria-disabled': function () {
                                    return $(this).hasClass('disabled') ? 'true' : 'false';
                                }
                            });
                        }
                    });
                });
            </script>
        @endpush

@endsection