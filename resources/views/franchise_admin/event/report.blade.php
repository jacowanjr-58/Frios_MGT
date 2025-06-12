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

    <div class="content-body default-height">
        <!-- row -->
        <div class="container-fluid">
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p> Inventory Report</p>
                </div>
            </div>
            <div class="row mb-4 align-items-center">
               
            <div class="d-flex justify-content-between">
                <h1 style="font-size: 28px; font-weight: bold">
                    @php
                        $currentDate = \Carbon\Carbon::now()->startOfMonth()->format('F 1, Y');
                    @endphp
                    Event Inventory Report (as of {{ $currentDate }})
                </h1>
            </div>

            <form id="report-form" class="mb-4">
                <div class="mt-5 mb-3 flex">
                    <input type="month" name="month_year" id="month_year" class="w-25 form-control"
                        value="{{ request('month_year', \Carbon\Carbon::now()->format('Y-m')) }}">
                    <div style="margin-left: 10px;">
                        <button type="submit" class="btn btn-primary custom-hover text-primary">Generate Report</button>
                    </div>
                </div>
            </form>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive rounded">
                        <table id="report-table" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Orderable flover</th>
                                    <th>Quantity</th>
                                    <th>On hand flover</th>
                                    <th>Quantity</th>
                                    <th>Shortage / Overage</th>
                                    <th>Month Avaliable to Order</th>
                                </tr>
                            </thead>
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
        <script>
            $(document).ready(function () {
                let table = $('#report-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('franchise.events.report', ['franchisee' => request()->route('franchisee')]) }}",
                        data: function (d) {
                            d.month_year = $('#month_year').val();
                        }
                    },
                    columns: [
                        { data: 'orderable_flover', name: 'orderable_flover' },
                        { data: 'quantity', name: 'quantity' },
                        { data: 'on_hand_flover', name: 'on_hand_flover' },
                        { data: 'on_hand_quantity', name: 'on_hand_quantity' },
                        { data: 'shortage_overage', name: 'shortage_overage' },
                        { data: 'month_available', name: 'month_available' }
                    ],
                    language: {
                        paginate: {
                            next: '<i class="fa fa-angle-double-right"></i>',
                            previous: '<i class="fa fa-angle-double-left"></i>'
                        }
                    },
                    drawCallback: function (settings) {
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

                $('#report-form').on('submit', function (e) {
                    e.preventDefault();
                    table.draw();
                });
            });
        </script>
    @endpush
@endsection