@extends('layouts.app')
@section('content')

<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Profit &amp; Loss Statement</h2>
                <p class="mb-0 text-muted">Summary by Income and Expense Categories</p>
            </div>
        </div>

        <!-- Date Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filters-card">
                    <div class="filters-header">
                        <h5><i class="fa fa-calendar me-2"></i>Filter by Date</h5>
                    </div>
                    <div class="filters-content">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="form-control"
                                    value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" id="end_date" name="end_date" class="form-control"
                                    value="{{ request('end_date', \Carbon\Carbon::now()->endOfMonth()->toDateString()) }}">
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-search me-2"></i>Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- P&L Table -->
        <div class="row">
            <div class="col-12">
                <div class="card main-content-card">
                    <div class="card-body">
                        <h4 class="mb-3 text-success">Income</h4>
                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalIncome = 0; @endphp
                                @foreach($income as $cat => $txns)
                                    @php $catTotal = $txns->sum('amount'); $totalIncome += $catTotal; @endphp
                                    <tr>
                                        <td>{{ $cat }}</td>
                                        <td class="text-end">${{ number_format($catTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td>Total Income</td>
                                    <td class="text-end">${{ number_format($totalIncome, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="mb-3 text-danger">Expenses</h4>
                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalExpense = 0; @endphp
                                @foreach($expenses as $cat => $txns)
                                    @php $catTotal = $txns->sum('amount'); $totalExpense += $catTotal; @endphp
                                    <tr>
                                        <td>{{ $cat }}</td>
                                        <td class="text-end">${{ number_format($catTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold">
                                    <td>Total Expenses</td>
                                    <td class="text-end">${{ number_format($totalExpense, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <h4 class="mb-3 text-primary">Net Profit / Loss</h4>
                        <div class="fs-4 fw-bold">
                            ${{ number_format($totalIncome - $totalExpense, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
