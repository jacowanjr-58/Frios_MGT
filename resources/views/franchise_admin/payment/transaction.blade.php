@extends('layouts.app')

@section('content')
    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">

            <div class="row">
                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                    <div class="card chart-bx">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex align-items-center">
                                <h2 class="chart-num font-w600 mb-0">${{ number_format($totalAmount['daily']) }}</h2>
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                        fill="#0E8A74" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Daily</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0 chart-body-wrapper">
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                    <div class="card chart-bx">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex align-items-center">
                                <h2 class="chart-num font-w600 mb-0">${{ number_format($totalAmount['weekly']) }}</h2>
                                <svg class="ms-2" width="19" height="12" viewBox="0 0 19 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 -4.76837e-06C0.222201 -4.76837e-06 -0.670134 2.15428 0.589795 3.41421L7.78218 10.6066C8.56323 11.3876 9.82956 11.3876 10.6106 10.6066L17.803 3.41421C19.0629 2.15428 18.1706 -4.76837e-06 16.3888 -4.76837e-06H2.00401Z"
                                        fill="#FF3131" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Weekly</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0 chart-body-wrapper">
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                    <div class="card chart-bx">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex align-items-center">
                                <h2 class="chart-num font-w600 mb-0">${{ number_format($totalAmount['monthly']) }}</h2>
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                        fill="#0E8A74" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Monthly</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-xxl-3 col-sm-6 ">
                    <div class="card chart-bx">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex align-items-center">
                                <h2 class="chart-num font-w600 mb-0">${{ number_format($totalAmount['yearly']) }}</h2>
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2.00401 11.1924C0.222201 11.1924 -0.670134 9.0381 0.589795 7.77817L7.78218 0.585786C8.56323 -0.195262 9.82956 -0.195262 10.6106 0.585786L17.803 7.77817C19.0629 9.0381 18.1706 11.1924 16.3888 11.1924H2.00401Z"
                                        fill="#0E8A74" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-black font-w500 mb-0">Yearly</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                        </div>
                    </div>
                </div>

            </div>





            <div class="row card pt-4">
                <h4 class="card-title">Invoice Transaction</h4>
                <div class="col-lg-12 pt-4">
                    <div class="table-responsive rounded">
                        <table id="invoice-table" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row card pt-4">
                <h4 class="card-title">Order Transaction</h4>
                <div class="col-lg-12 pt-4">
                    <div class="table-responsive rounded">
                        <table id="order-table" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Cardholder Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>

        </div>



    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#invoice-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("franchise.transaction", request()->route("franchisee")) }}',
                    data: function (d) {
                        d.table = 'invoice';
                    }
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                order: [[0, 'asc']],
                pageLength: 10,
                drawCallback: function (settings) {
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

            $('#order-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("franchise.transaction", request()->route("franchisee")) }}',
                    data: function (d) {
                        d.table = 'order';
                    }
                },
                columns: [
                    { data: 'cardholder_name', name: 'cardholder_name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>'
                    },
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                },
                dom: '<"top"f>rt<"bottom"lip><"clear">',
                order: [[0, 'asc']],
                pageLength: 10,
                drawCallback: function (settings) {
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