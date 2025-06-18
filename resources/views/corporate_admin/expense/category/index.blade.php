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
						<p>Expense Categories</p>
					</div>

				</div>
                <div class="row mb-4 align-items-center">
                    <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                        <a href="{{ route('expense-category.create') }}" class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Category</a>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                        <div class="card m-0">
                            <div class="card-body py-3 py-md-2">
                                <div class="d-sm-flex d-block align-items-center">
                                    <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                        <div class="media-body">
                                            <p class="mb-1 fs-12">Total Categories</p>
                                            <h3 class="mb-0 font-w600 fs-22">{{ $expenseSubCategoryCount }} Categories</h3>
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
                                        <th>Main Category</th>
                                        <th>Sub Category</th>
                                        <th>Description</th>
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
            $('#category-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('expense-category') }}",
                columns: [
                    { data: 'category', name: 'category' },
                    { data: 'sub_category', name: 'sub_category' },
                    { data: 'sub_category_description', name: 'sub_category_description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at', visible: false }
                ],
                order: [[4, 'desc']], // Order by created_at column
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    }
                },
                drawCallback: function(settings) {
                    // Initialize SweetAlert confirmation for delete buttons
                    window.initSwalConfirm({
                        triggerSelector: '.delete-category',
                        title: 'Delete Category',
                        text: 'Are you sure you want to delete this category? This action cannot be undone.',
                        confirmButtonText: 'Yes, delete category'
                    });

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
