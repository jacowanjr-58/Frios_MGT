@extends('layouts.app')
@section('content')

<div class="container py-4">
    <h3>Create Bank Transaction</h3>
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Name/Description</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="type" class="form-control" id="type-select" required>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
            </select>
        </div>
        <div class="mb-3" id="income-category-group">
            <label>Income Category</label>
            <select name="income_category_id" class="form-control">
                <option value="">--</option>
                @foreach($incomeCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="expense-category-group">
            <label>Expense Category</label>
            <select name="expense_category_id" class="form-control">
                <option value="">--</option>
                @foreach($expenseCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="expense-subcategory-group">
            <label>Expense Subcategory</label>
            <select name="expense_sub_category_id" class="form-control">
                <option value="">--</option>
                @foreach($expenseSubCategories as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->category }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary">Create</button>
        <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
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
