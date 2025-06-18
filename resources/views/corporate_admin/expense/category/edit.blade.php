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
                    <p>Edit Expense Sub Category</p>
                </div>

                <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                    <i class="fa fa-arrow-left me-2"></i> Back
                </a>
            </div>

            {{-- <div class="row">
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
                                                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
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
 --}}


            <div class="row">
                <div class="col-xl-12">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Edit Expense Sub Category</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">



                                            <form action="{{ route('expense-category.update' , $expenseSubCategory->id) }}" method="POST">
                                                @method('PUT')
                                                @csrf

                                                <div class="row">

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Main Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" id="category_id" class="form-control  @error('category_id') is-invalid @enderror">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}" {{ $ExpenseCategory->id == $expenseSubCategory->category_id ? 'selected' : '' }}>{{ $ExpenseCategory->category }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Sub Category <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('sub_category') is-invalid @enderror"
                                                            name="sub_category" value="{{ $expenseSubCategory->sub_category }}" placeholder="Sub Category">
                                                        @error('sub_category')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Sub Category Description <span class="text-danger">*</span></label>
                                                        <textarea name="sub_category_description" id="sub_category_description" cols="10" rows="5" class="@error('sub_category_description') is-invalid @enderror form-control">{{ $expenseSubCategory->sub_category_description }}</textarea>
                                                        @error('sub_category_description')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary bg-primary">Update Expense Sub Category</button>
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
