@extends('layouts.app')
@section('content')

@push('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
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
                <div class="row mb-4 align-items-center">

                    <div class="col-xl-12 col-lg-12">
                        <div class="card m-0">
                            <div class="card-body py-3 py-md-2">
                                <div class="d-sm-flex d-block align-items-center">
                                    <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                        <div class="media-body">
                                            <p class="mb-1 fs-12">Total Customers</p>
                                            <h3 class="mb-0 font-w600 fs-22">{{ $customerCount }} Customers</h3>
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
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/app.js'])
    <script>
        $(document).ready(function() {
            $('#customer-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('corporate_admin.customer') }}",
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
        });
    </script>
@endpush

@endsection
