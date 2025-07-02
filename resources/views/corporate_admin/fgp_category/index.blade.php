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
<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Dashboard \</h2>
                <p>Category List</p>
            </div>
        </div>

        <div class="row mb-4 align-items-center">
            @can('flavor_category.create')
            <div class="col-xl-3 col-lg-4 mb-3 mb-lg-0">
                <a href="{{ route('fgpcategory.create') }}"
                    class="btn btn-secondary btn-lg btn-block rounded text-white">
                    <i class="fa fa-plus me-2"></i> Add Category
                </a>
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
                                        <h3 class="mb-0 font-w600 fs-22" id="category-count">{{ $totalCategories }}</h3>
                                    </div>
                                </div>
                                <h5 class="text-muted fw-light" id="franchise-label">Categories</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered" id="category-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Parent</th>
                                <th>Items</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endcan

    @endsection

    @push('scripts')
    <script>
        $(document).ready(function () {
    @can('flavor_category.view')
    var columns = [
        { data: 'name', name: 'name' },
        { data: 'parent_name', name: 'parent_name', render: function(data) {
                return data ? '<span class="text-secondary">' + data + '</span>' : '<span class="text-muted">—</span>';
            }
        },
        { data: 'flavor_items', name: 'flavor_items' }
    ];

    @canany(['flavor_category.edit', 'flavor_category.delete'])
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    @endcanany

    columns.push({ data: 'created_at', visible: false });

    var sortColumnIndex = columns.length - 1;

    var table = $('#category-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "{{ route('fgpcategory.index') }}",
            data: function (d) {
                var selectedFranchise = $('#franchise-select').val();
                if (selectedFranchise) {
                    d.franchise_filter = selectedFranchise;
                }
                if ($('#franchise-filter').length && $('#franchise-filter').val()) {
                    d.franchise_filter = $('#franchise-filter').val();
                }
            }
        },
        columns: columns,
        order: [[sortColumnIndex, 'desc']],
        language: {
            paginate: {
                next: '<i class="fa fa-angle-double-right"></i>',
                previous: '<i class="fa fa-angle-double-left"></i>'
            }
        },
        drawCallback: function (settings) {
            window.initSwalConfirm({
                triggerSelector: '.delete-category',
                title: 'Delete Category',
                text: 'Are you sure you want to delete this category? This action cannot be undone.',
                confirmButtonText: 'Yes, delete category'
            });
            $('.dataTables_paginate').addClass('paging_simple_numbers');
            $('.paginate_button').each(function () {
                if ($(this).hasClass('current')) {
                    $(this).attr('aria-current', 'page');
                }
            });
        }
    });

    $(document).on('change', '#franchise-select', function () {
        var selectedFranchise = $(this).val();
        var selectedText = $(this).find('option:selected').text();
        updateCategoryCount(selectedFranchise, selectedText);
        table.draw();
    });

    function updateCategoryCount(franchiseId, franchiseText) {
        if (franchiseId) {
            $('#category-count').text('Loading...');
            $('#franchise-label').text('Categories');
            $.ajax({
                url: '{{ route("fgpcategory.index") }}',
                type: 'GET',
                data: {
                    franchise_filter: franchiseId,
                    count_only: true
                },
                success: function (response) {
                    if (response.count !== undefined) {
                        $('#category-count').text(response.count);
                    }
                },
                error: function () {
                    $('#category-count').text('Error');
                }
            });
        } else {
            $('#category-count').text('{{ $totalCategories }}');
            $('#franchise-label').text('Categories');
        }
    }
    @endcan
});
    </script>
    @endpush
