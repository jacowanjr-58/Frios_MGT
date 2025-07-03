@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h3>Edit Bank Transaction</h3>
    <form method="POST" action="{{ route('transactions.update', $transaction->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $transaction->date }}" required>
        </div>
        <div class="mb-3">
            <label>Name/Description</label>
            <input type="text" name="name" class="form-control" value="{{ $transaction->name }}" required>
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ $transaction->amount }}" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-control" id="type-select" required>
                <option value="income" {{ $transaction->income_category_id ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ $transaction->expense_category_id ? 'selected' : '' }}>Expense</option>
            </select>
        </div>
        <div class="mb-3" id="income-category-group">
            <label>Income Category</label>
            <select name="income_category_id" class="form-control">
                <option value="">--</option>
                @foreach($incomeCategories as $cat)
                    <option value="{{ $cat->id }}" {{ $transaction->income_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->category }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="expense-category-group">
            <label>Expense Category</label>
            <select name="expense_category_id" class="form-control">
                <option value="">--</option>
                @foreach($expenseCategories as $cat)
                    <option value="{{ $cat->id }}" {{ $transaction->expense_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->category }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="expense-subcategory-group">
            <label>Expense Subcategory</label>
            <select name="expense_sub_category_id" class="form-control">
                <option value="">--</option>
                @foreach($expenseSubCategories as $sub)
                    <option value="{{ $sub->id }}" {{ $transaction->expense_sub_category_id == $sub->id ? 'selected' : '' }}>{{ $sub->category }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="{{ route('transactions.index'), ['franchise' => $franchiseId] }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleCategoryFields() {
        var type = document.getElementById('type-select').value;
        document.getElementById('income-category-group').style.display = (type === 'income') ? '' : 'none';
        document.getElementById('expense-category-group').style.display = (type === 'expense') ? '' : 'none';
        document.getElementById('expense-subcategory-group').style.display = (type === 'expense') ? '' : 'none';
    }
    document.getElementById('type-select').addEventListener('change', toggleCategoryFields);
    toggleCategoryFields(); // Initial call
});
</script>
@endsection
