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
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
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
                                <svg class="ms-2 primary-icon" width="19" height="12" viewBox="0 0 19 12"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
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


            {{-- <div class="row card pt-4">
                <h4 class="card-title">Expense Transaction</h4>
                <div class="col-lg-12 pt-4">
                    <div class="table-responsive rounded">
                        <table id="example5" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Cardholder Name</th>
                                    <th>Amonut</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expenseTransactions as $expenseTransaction)
                                    <tr>
                                        <td>{{ $expenseTransaction->expense->name ?? '-' }}</td>
                                        <td>{{ $expenseTransaction->cardholder_name ?: '-' }}</td>
                                        <td>${{ number_format($expenseTransaction->amount) ?: '-' }}</td>
                                        <td>{{ $expenseTransaction->stripe_status ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a target="_blank" href="{{ route('corporate_admin.pos.expense',$expenseTransaction->id) }}" class="me-4">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                                                </a>
                                                <a href="{{ route('corporate_admin.expenses.pos.download', $expenseTransaction->id) }}" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" aria-label="PDF" role="img" viewBox="0 0 512 512" width="24px" height="24px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#c80a0a"></rect><path fill="#ffffff" d="M413 302c-9-10-29-15-56-15-16 0-33 2-53 5a252 252 0 0 1-52-69c10-30 17-59 17-81 0-17-6-44-30-44-7 0-13 4-17 10-10 18-6 58 13 100a898 898 0 0 1-50 117c-53 22-88 46-91 65-2 9 4 24 25 24 31 0 65-45 91-91a626 626 0 0 1 92-24c38 33 71 38 87 38 32 0 35-23 24-35zM227 111c8-12 26-8 26 16 0 16-5 42-15 72-18-42-18-75-11-88zM100 391c3-16 33-38 80-57-26 44-52 72-68 72-10 0-13-9-12-15zm197-98a574 574 0 0 0-83 22 453 453 0 0 0 36-84 327 327 0 0 0 47 62zm13 4c32-5 59-4 71-2 29 6 19 41-13 33-23-5-42-18-58-31z"></path></g></svg>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>


            <div class="row card pt-4">
                <h4 class="card-title">Event Transaction</h4>
                <div class="col-lg-12 pt-4">
                    <div class="table-responsive rounded">
                        <table id="example8" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Cardholder Name</th>
                                    <th>Amonut</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($eventTransactions as $eventTransaction)
                                    <tr>
                                        <td>{{ $eventTransaction->event->event_name ?: '-' }}</td>
                                        <td>{{ $eventTransaction->cardholder_name ?: '-' }}</td>
                                        <td>${{ number_format($eventTransaction->amount) ?: '-' }}</td>
                                        <td>{{ $eventTransaction->stripe_status ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a target="_blank" href="{{ route('corporate_admin.pos.event' , $eventTransaction->id) }}" class="me-4">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                                                </a>
                                                <a href="{{ route('corporate_admin.event.pos.download', $eventTransaction->id) }}" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" aria-label="PDF" role="img" viewBox="0 0 512 512" width="24px" height="24px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#c80a0a"></rect><path fill="#ffffff" d="M413 302c-9-10-29-15-56-15-16 0-33 2-53 5a252 252 0 0 1-52-69c10-30 17-59 17-81 0-17-6-44-30-44-7 0-13 4-17 10-10 18-6 58 13 100a898 898 0 0 1-50 117c-53 22-88 46-91 65-2 9 4 24 25 24 31 0 65-45 91-91a626 626 0 0 1 92-24c38 33 71 38 87 38 32 0 35-23 24-35zM227 111c8-12 26-8 26 16 0 16-5 42-15 72-18-42-18-75-11-88zM100 391c3-16 33-38 80-57-26 44-52 72-68 72-10 0-13-9-12-15zm197-98a574 574 0 0 0-83 22 453 453 0 0 0 36-84 327 327 0 0 0 47 62zm13 4c32-5 59-4 71-2 29 6 19 41-13 33-23-5-42-18-58-31z"></path></g></svg>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div> --}}


            <div class="row card pt-4">
                <h4 class="card-title">Order Transaction</h4>
                <div class="col-lg-12 pt-4">
                    <div class="table-responsive rounded">
                        <table id="example7" class="table customer-table display mb-4 fs-14 card-table">
                            <thead>
                                <tr>
                                    <th>Cardholder Name</th>
                                    <th>Amonut</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderTransactions as $orderTransaction)
                                    <tr>
                                        <td>{{ $orderTransaction->cardholder_name ?: '-' }}</td>
                                        <td>${{ number_format($orderTransaction->amount) ?: '-' }}</td>
                                        <td>{{ $orderTransaction->stripe_status ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a target="_blank" href="{{ route('corporate_admin.pos.order' , $orderTransaction->id) }}" class="me-4">
                                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                                                </a>
                                                <a href="{{ route('corporate_admin.order.pos.download', $orderTransaction->id) }}" >
                                                    <svg xmlns="http://www.w3.org/2000/svg" aria-label="PDF" role="img" viewBox="0 0 512 512" width="24px" height="24px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#c80a0a"></rect><path fill="#ffffff" d="M413 302c-9-10-29-15-56-15-16 0-33 2-53 5a252 252 0 0 1-52-69c10-30 17-59 17-81 0-17-6-44-30-44-7 0-13 4-17 10-10 18-6 58 13 100a898 898 0 0 1-50 117c-53 22-88 46-91 65-2 9 4 24 25 24 31 0 65-45 91-91a626 626 0 0 1 92-24c38 33 71 38 87 38 32 0 35-23 24-35zM227 111c8-12 26-8 26 16 0 16-5 42-15 72-18-42-18-75-11-88zM100 391c3-16 33-38 80-57-26 44-52 72-68 72-10 0-13-9-12-15zm197-98a574 574 0 0 0-83 22 453 453 0 0 0 36-84 327 327 0 0 0 47 62zm13 4c32-5 59-4 71-2 29 6 19 41-13 33-23-5-42-18-58-31z"></path></g></svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>



    </div>
@endsection
