@extends('layouts.app')
@section('content')
<style>
    div#sub_category_list {
    width: 100%;
}
#sub_category_placeholder{
    padding-top: 15px;
    font-size: 15px;
    font-weight: 500;
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



                                            <form action="{{ route('franchise.expense.update' , ['franchisee' => request()->route('franchisee'), 'id' => $expense->id]) }}" method="POST">
                                                @method('PUT')
                                                @csrf

                                                <div class="row">
 
                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Main Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ $expense->name }}" placeholder="Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                            name="amount" value="{{ $expense->amount }}" placeholder="Amount">
                                                        @error('amount')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                                        name="date" id="date" value="{{ $expense->date }}">


                                                        @error('date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}"
                                                                        {{ $ExpenseCategory->id == $expense->category_id ? 'selected' : '' }}>
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
                                                                @if($expense->sub_category_id)
                                                                    {{ $expense->sub_category->sub_category }}  <!-- Assuming you have a relationship set up -->
                                                                @else
                                                                    Please Select
                                                                @endif
                                                            </div>
                                                            <div id="sub_category_list" class="dropdown-menu" style="display: none;"></div>
                                                        </div>

                                                        <!-- Hidden input field to store selected value -->
                                                        <input type="hidden" name="sub_category_id" id="sub_category_id" value="{{ $expense->sub_category_id }}" class="form-control">

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

<script>
$(document).ready(function() {
    // Toggle dropdown visibility on click
    $('#sub_category_div').on('click', function() {
        $('#sub_category_list').toggle();  // Toggle the visibility of the dropdown options
    });

    // When an option is selected
    $(document).on('click', '.sub_category_option', function() {
        var selectedText = $(this).text();
        var selectedValue = $(this).data('id');

        // Update the placeholder text with the selected option
        $('#sub_category_placeholder').text(selectedText);

        // Set the selected value in the hidden input
        $('#sub_category_id').val(selectedValue);

        // Hide the dropdown after selection
        $('#sub_category_list').hide();
    });

    // Fetch subcategories based on category selection
    $('#category_id').on('change', function() {
        var categoryID = $(this).val();

        // Reset sub-category selection if category changes
        $('#sub_category_id').val('');  // Clear the hidden input value
        $('#sub_category_placeholder').text('Please Select');  // Reset placeholder text
        $('#sub_category_list').empty();  // Clear the sub-category list

        if (categoryID) {
            $.ajax({
                url: '{{ url('franchise/get-subcategories') }}/' + categoryID,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#sub_category_list').empty();  // Clear the list before appending new options
                    $.each(response.data, function(index, subCategory) {
                        var isSelected = subCategory.id == $('#sub_category_id').val() ? 'selected' : '';
                        $('#sub_category_list').append(
                            '<div class="dropdown-item sub_category_option ' + isSelected + '" data-id="' + subCategory.id + '">' + subCategory.sub_category + '</div>'
                        );
                    });
                },
                error: function() {
                    alert('Failed to fetch sub-categories. Please try again.');
                }
            });
        } else {
            $('#sub_category_list').empty();  // If no category is selected, clear the sub-category list
        }
    });

    // If category is already selected on page load, fetch subcategories
    var selectedCategory = $('#category_id').val();
    if (selectedCategory) {
        $.ajax({
            url: '{{ url('franchise/get-subcategories') }}/' + selectedCategory,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#sub_category_list').empty();  // Clear the list before appending new options
                $.each(response.data, function(index, subCategory) {
                    var isSelected = subCategory.id == $('#sub_category_id').val() ? 'selected' : '';
                    $('#sub_category_list').append(
                        '<div class="dropdown-item sub_category_option ' + isSelected + '" data-id="' + subCategory.id + '">' + subCategory.sub_category + '</div>'
                    );
                });
            },
            error: function() {
                alert('Failed to fetch sub-categories. Please try again.');
            }
        });
    }
});

</script>

@endsection
