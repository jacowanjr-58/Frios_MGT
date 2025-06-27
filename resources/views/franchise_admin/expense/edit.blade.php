@extends('layouts.app')
@section('content')
<style>
    div#sub_category_list {
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
    }
    #sub_category_placeholder {
        padding-top: 15px;
        font-size: 15px;
        font-weight: 500;
        cursor: pointer;
    }
    .dropdown-item {
        padding: 10px 15px;
        cursor: pointer;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    .dropdown-item.selected {
        background-color: #e9ecef;
    }
</style>


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
                    <p>Edit Expense</p>
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
                                        <h4 class="card-title">Edit Expense</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <form action="{{ route('franchise.expenses_by_franchise-update' , ['franchise' => $franchiseId, 'id' => $expense->id]) }}" method="POST">
                                                @method('PUT')
                                                @csrf

                                                <div class="row">
 
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Main Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ $expense->name }}" placeholder="Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                            name="amount" value="{{ $expense->amount }}" placeholder="Amount">
                                                        @error('amount')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                                        name="date" id="date" value="{{ $expense->date }}">


                                                        @error('date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" id="category_id" class="form-control select2">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}"
                                                                        {{ $ExpenseCategory->id == $expense->expense_category_id ? 'selected' : '' }}>
                                                                    {{ $ExpenseCategory->category }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Sub Category <span class="text-danger">*</span></label>

                                                        <!-- This div will serve as the dropdown -->
                                                        <div id="sub_category_div" class="dropdown">
                                                            <div id="sub_category_placeholder" class="form-control">
                                                                @if($expense->expense_sub_category_id)
                                                                    {{ $expense->sub_category->category }}
                                                                @else
                                                                    Please Select
                                                                @endif
                                                            </div>
                                                            <div id="sub_category_list" class="dropdown-menu" style="display: none;"></div>
                                                        </div>

                                                        <!-- Hidden input field to store selected value -->
                                                        <input type="hidden" name="sub_category_id" id="sub_category_id" value="{{ $expense->expense_sub_category_id }}" class="form-control">

                                                        @error('sub_category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>


                                                </div>
                                                <button type="submit" class="btn btn-primary bg-primary">Edit Expense</button>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2();

    // Function to load subcategories
    function loadSubCategories(categoryId, selectedSubCategoryId = null) {
        if (categoryId) {
            $.ajax({
                url: '/franchise/{{ $franchiseId }}/get-subcategories/' + categoryId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#sub_category_list').empty();
                    $.each(response.data, function(index, subCategory) {
                        var isSelected = selectedSubCategoryId && selectedSubCategoryId == subCategory.id;
                        $('#sub_category_list').append(
                            '<div class="dropdown-item sub_category_option ' + (isSelected ? 'selected' : '') + 
                            '" data-id="' + subCategory.id + '">' + subCategory.category + '</div>'
                        );

                        // If this is the selected subcategory, update the placeholder
                        if (isSelected) {
                            $('#sub_category_placeholder').text(subCategory.category);
                            $('#sub_category_id').val(subCategory.id);
                        }
                    });
                },
                error: function() {
                    console.error('Failed to fetch sub-categories');
                }
            });
        } else {
            $('#sub_category_list').empty();
            $('#sub_category_placeholder').text('Please Select');
            $('#sub_category_id').val('');
        }
    }

    // Toggle dropdown visibility
    $('#sub_category_div').on('click', function() {
        $('#sub_category_list').toggle();
    });

    // Handle subcategory selection
    $(document).on('click', '.sub_category_option', function() {
        var selectedText = $(this).text();
        var selectedValue = $(this).data('id');
        $('#sub_category_placeholder').text(selectedText);
        $('#sub_category_id').val(selectedValue);
        $('#sub_category_list').hide();
        $('.sub_category_option').removeClass('selected');
        $(this).addClass('selected');
    });

    // Handle category change
    $('#category_id').on('change', function() {
        var categoryId = $(this).val();
        loadSubCategories(categoryId);
    });

    // Load initial subcategories if category is selected
    var initialCategoryId = $('#category_id').val();
    var initialSubCategoryId = '{{ $expense->expense_sub_category_id }}';
    if (initialCategoryId) {
        loadSubCategories(initialCategoryId, initialSubCategoryId);
    }

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#sub_category_div').length) {
            $('#sub_category_list').hide();
        }
    });
});
</script>
@endpush

@endsection
