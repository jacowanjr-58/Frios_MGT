@extends('layouts.app')
@section('content')

{{-- This file is part of Plaid API integration for categorizing bank transactions. --}}
<div class="container py-4">
    <h3>Categorize Bank Transactions</h3>
    <form method="POST" action="{{ route('transactions.categorize.save'), ['franchise' => $franchiseId]}}">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th><th>Name</th><th>Amount</th>
                    <th>Type</th>
                    <th>Income Category</th>
                    <th>Expense Category</th>
                    <th>Expense Subcategory</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $txn)
                <tr>
                    <td>{{ $txn->date }}</td>
                    <td>{{ $txn->name }}</td>
                    <td>{{ $txn->amount }}</td>
                    <td>
                        <select name="transactions[{{ $txn->id }}][type]" class="form-select">
                            <option value="">--</option>
                            <option value="income" {{ $txn->income_category_id ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ $txn->expense_category_id ? 'selected' : '' }}>Expense</option>
                        </select>
                    </td>
                    <td>
                        <select name="transactions[{{ $txn->id }}][income_category_id]" class="form-select">
                            <option value="">--</option>
                            @foreach($incomeCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $txn->income_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->category }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="transactions[{{ $txn->id }}][expense_category_id]" class="form-select">
                            <option value="">--</option>
                            @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $txn->expense_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->category }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="transactions[{{ $txn->id }}][expense_sub_category_id]" class="form-select">
                            <option value="">--</option>
                            @foreach($expenseSubCategories as $sub)
                                <option value="{{ $sub->id }}" {{ $txn->expense_sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->category }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <button class="btn btn-primary">Save Categories</button>
    </form>
</div>
@endsection
