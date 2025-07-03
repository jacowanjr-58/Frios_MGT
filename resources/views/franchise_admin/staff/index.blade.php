@extends('layouts.app')
@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

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
    </style>
@endpush
@section('content')
    <!--**********************************
                Content body start
            ***********************************-->
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">

            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Staff List</p>
                </div>

            </div>
            <div class="row mb-4 align-items-center">
                <div class="col-xl-3 col-lg-4 mb-4 mb-lg-0">
                    @can('staff.create')
                        <a href="{{ route('franchise.staff.create', ['franchise' => $franchiseeId]) }}"
                            class="btn btn-secondary btn-lg btn-block rounded text-white">+ New Staff</a>
                    @endcan
                </div>
                <div class="col-xl-9 col-lg-8">
                    <div class="card m-0">
                        <div class="card-body py-3 py-md-2">
                            <div class="d-sm-flex d-block align-items-center">
                                <div class="d-flex mb-sm-0 mb-3 me-auto align-items-center">
                                    <div class="p-2 fs-3"><i class="bi bi-people-fill"></i></div>
                                    <div class="media-body">
                                        <p class="mb-1 fs-12">Total Staff</p>
                                        <h3 class="mb-0 font-w600 fs-22">{{ $totalUsers }} Staff Members</h3>
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
                        <table id="staff-table" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Role</th>
                                    <th>Franchise</th>
                                    <th>Date Joined</th>
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
    <!--**********************************
                Content body end
            ***********************************-->

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
    $(document).ready(function() {
        var staffTable = $('#staff-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('franchise.staff.index', ['franchise' => $franchiseeId]) }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { 
                    data: 'phone_number', 
                    name: 'phone_number',
                    render: function(data) {
                        return data || 'N/A';
                    }
                },
                { data: 'role', name: 'role' },
                { data: 'franchise_name', name: 'franchise_name' },
                { data: 'date_joined', name: 'date_joined' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[0, 'asc']], // Order by name
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right"></i>',
                    previous: '<i class="fa fa-angle-double-left"></i>'
                }
            },
            drawCallback: function(settings) {
                // Initialize SweetAlert confirmation for delete buttons
                window.initSwalConfirm({
                    triggerSelector: '.delete-staff',
                    title: 'Delete Staff',
                    text: 'Are you sure you want to delete this staff member? This action cannot be undone.',
                    confirmButtonText: 'Yes, delete staff'
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
    });
</script>
@endpush