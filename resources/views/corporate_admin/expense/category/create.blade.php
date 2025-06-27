@extends('layouts.app')
@section('content')



    <!--**********************************
                Content body start
            ***********************************-->
    <div class=" content-body default-height">
        <!-- row -->
        <div class="container-fluid">
            <!-- <div class="page-titles">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0)">Analytics</a></li>
                        </ol>
                    </div> -->
            <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                <div class="me-auto">
                    <h2 class="font-w600 mb-0">Dashboard \</h2>
                    <p>Add Expense Sub Category</p>
                </div>

                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Add Expense Category</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <!-- Display Success Message -->
                                            <form action="{{ route('expense-category.store') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Main Category <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('category') is-invalid @enderror"
                                                            name="category" value="{{ old('category') }}" placeholder="Expense Category">
                                                        @error('category')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary bg-primary">Add Expense Category</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Add Expense Sub Category</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <form action="{{ route('expense-sub-category.store') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Main Category <span class="text-danger">*</span></label>
                                                        <select name="expense_category_id" id="expense_category_id" class="form-control select2  @error('expense_category_id') is-invalid @enderror">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}" {{ $ExpenseCategory->id == old('expense_category_id') ? 'selected' : '' }}>{{ $ExpenseCategory->category }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('expense_category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('category') is-invalid @enderror"
                                                            name="category" value="{{ old('category') }}" placeholder="Sub Category">
                                                        @error('category')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Sub Category Description <span class="text-danger">*</span></label>
                                                        <textarea name="description" id="description" cols="10" rows="5" class="@error('description') is-invalid @enderror form-control">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary bg-primary">Add Expense Sub Category</button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--**********************************
                Content body end
            ***********************************-->


@endsection
