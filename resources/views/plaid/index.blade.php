@extends('layouts.app')
@section('content')
{{-- // Main content section for the Plaid categorizedtransactions page --}}
@push('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@php
    $franchiseId = $franchiseId ?? (request()->route('franchise') ?? session('franchise_id') ?? Auth::user()->franchise_id);
@endphp

<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Accounting</h2>
                <p class="mb-0 text-muted">Bank Transactions & Categorization</p>
            </div>
            <div>
                <a href="{{ url('/plaid/bank-connect') }}" class="btn btn-primary me-2">Sync Transactions</a>
                <a href="{{ route('transactions.categorize', ['franchise' => $franchiseId]) }}" class="btn btn-secondary me-2">Categorize Transactions</a>
                <a href="{{ route('transactions.pnl', ['franchise' => $franchiseId]) }}" class="btn btn-info">View P&amp;L</a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card filters-card">
                    <div class="filters-header">
                        <h5><i class="fa fa-filter me-2"></i>Filters</h5>
                        <button type="button" class="btn toggle-filters-btn" id="toggleFilters">
                            <i class="fa fa-chevron-down me-1"></i> Toggle Filters
                        </button>
                    </div>
                    <div class="filters-content" id="filtersContent" style="display: none;">
                        <form id="filtersForm" class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label" for="categoryFilter">
                                        <i class="fa fa-list me-1"></i>Category
                                    </label>
                                    <select class="form-control select2" id="categoryFilter" name="category">
                                        <option value="">All</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label" for="typeFilter">
                                        <i class="fa fa-exchange me-1"></i>Type
                                    </label>
                                    <select class="form-control select2" id="typeFilter" name="type">
                                        <option value="">All</option>
                                        <option value="income">Income</option>
                                        <option value="expense">Expense</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label" for="dateFromFilter">
                                        <i class="fa fa-calendar me-1"></i>Date From
                                    </label>
                                    <input type="date" class="form-control filter-input" id="dateFromFilter" name="date_from" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label" for="dateToFilter">
                                        <i class="fa fa-calendar me-1"></i>Date To
                                    </label>
                                    <input type="date" class="form-control filter-input" id="dateToFilter" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="col-12 filter-actions">
                                <button type="submit" class="btn btn-filter-primary"><i class="fa fa-search me-2"></i>Apply Filters</button>
                                <a href="{{ route('transactions.index', ['franchise' => $franchiseId]) }}" class="btn btn-filter-secondary"><i class="fa fa-refresh me-2"></i>Clear All</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card main-content-card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="transactions-table" class="table display mb-4 card-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Income Category</th>
                                        <th>Expense Category</th>
                                        <th>Expense Subcategory</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $txn)
                                    <tr>
                                        <td>{{ $txn->date }}</td>
                                        <td>{{ $txn->name }}</td>
                                        <td>{{ number_format($txn->amount, 2) }}</td>
                                        <td>
                                            @if($txn->income_category_id)
                                                <span class="badge bg-success">Income</span>
                                            @elseif($txn->expense_category_id)
                                                <span class="badge bg-danger">Expense</span>
                                            @else
                                                <span class="badge bg-secondary">Uncategorized</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($txn->incomeCategory)->category }}</td>
                                        <td>{{ optional($txn->expenseCategory)->category }}</td>
                                        <td>{{ optional($txn->expenseSubCategory)->category }}</td>
                                        <td>
                                            <a href="{{ route('transactions.categorize', ['franchise' => $franchiseId])  }}" class="btn btn-sm btn-outline-primary">Categorize</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{-- Add pagination if needed --}}
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2({ theme: 'bootstrap-5', allowClear: true });
            $('#toggleFilters').click(function () {
                $('#filtersContent').slideToggle(300);
                const icon = $(this).find('i');
                icon.toggleClass('fa-chevron-down fa-chevron-up');
            });
        });
    </script>
@endpush

@endsection
