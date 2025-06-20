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

            .swal2-confirm {
                background-color: #00ABC7 !important;
            }

            .swal2-cancel {
                background-color: #FF3131 !important;
            }
        </style>
    @endpush

    @can('flavor_category.view')
        <!--**********************************
                    Content body start
                ***********************************-->
        <div class="content-body default-height">
            <!-- row -->
            <div class="container-fluid">

                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0">Dashboard \</h2>
                        <p>Category List</p>
                    </div>

                </div>
                <div class="row mb-4 align-items-center">

                    @can('flavor_category.create')
                        <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                            <a href="{{ route('fgpcategory.create') }}"
                                class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Category</a>
                        </div>


                        <div class="col-xl-9 col-lg-8">
                         @else
                            <div class="col-xl-12">
                        @endcan


                            <div class="card m-0">
                                <div class="card-body py-3 py-md-2">
                                    <div class="d-sm-flex d-block align-items-center">
                                        <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                            <div class="p-2 fs-3"><i class="bi bi-tags-fill"></i></div>
                                            <div class="media-body">
                                                <p class="mb-1 fs-12">Total Categories</p>
                                                <h3 class="mb-0 font-w600 fs-22">{{ $totalCategories }} Categories</h3>
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
                                <table id="category-table" class="table customer-table display mb-4 fs-14 card-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Created Date</th>
                                            @canany(['flavor_category.update', 'flavor_category.delete'])
                                                <th>Actions</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--**********************************
                    Content body end
                ***********************************-->
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

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".edit-franchisee").forEach(button => {
                    button.addEventListener("click", function () {
                        let franchiseeId = this.getAttribute("data-id");
                        window.location.href = `/franchisee/${franchiseeId}/edit`;
                    });
                });
            });
        </script>
@endsection

    @push('scripts')
        <script>
            $(document).ready(function () {
                @can('flavor_category.view')
                    // Build columns array based on user permissions
                    var columns = [
                        { data: 'name', name: 'name' },
                        { data: 'type', name: 'type' },
                        { data: 'created_at', name: 'created_at' }
                    ];

                    // Add action column if user has update or delete permissions
                    var hasActionPermissions = {{ auth()->user() && auth()->user()->canAny(['flavor_category.update', 'flavor_category.delete']) ? 'true' : 'false' }};
                    if (hasActionPermissions) {
                        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
                    }

                    // Add hidden created_at column for sorting
                    columns.push({ data: 'created_at', name: 'created_at', visible: false });

                    // Determine the sort column index (created_at is always last)
                    var sortColumnIndex = columns.length - 1;

                    var table = $('#category-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('fgpcategory.index') }}",
                        columns: columns,
                        order: [[sortColumnIndex, 'desc']], // Order by created_at column by default
                        language: {
                            paginate: {
                                next: '<i class="fa fa-angle-double-right"></i>',
                                previous: '<i class="fa fa-angle-double-left"></i>'
                            }
                        },
                        drawCallback: function (settings) {
                            // Initialize SweetAlert confirmation for delete buttons
                            window.initSwalConfirm({
                                triggerSelector: '.delete-category',
                                title: 'Delete Category',
                                text: 'Are you sure you want to delete this category? This action cannot be undone.',
                                confirmButtonText: 'Yes, delete category'
                            });

                            // Add custom classes to pagination elements
                            $('.dataTables_paginate').addClass('paging_simple_numbers');
                            $('.paginate_button').each(function () {
                                if ($(this).hasClass('current')) {
                                    $(this).attr('aria-current', 'page');
                                }
                            });
                        }
                    });
                @endcan
            });
        </script>
    @endpush