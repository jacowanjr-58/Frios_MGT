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
                    <p>Add Expense</p>
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
                                        <h4 class="card-title">Add Expense</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">



                                            <form action="{{ route('franchise.expense.store') }}" method="POST">
                                                @csrf

                                                <div class="row">

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Main Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name') }}" placeholder="Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                                            name="amount" value="{{ old('amount') }}" placeholder="Amount">
                                                        @error('amount')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-4">
                                                        <label class="form-label">Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                                        name="date" id="date" value="{{ old('date') }}">


                                                        @error('date')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                                        <select name="category_id" id="category_id" class="form-control  @error('category_id') is-invalid @enderror">
                                                            <option value="">Please Select</option>
                                                            @foreach ($ExpenseCategories as $ExpenseCategory)
                                                                <option value="{{ $ExpenseCategory->id }}">{{ $ExpenseCategory->category }}</option>
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
                                                            <div id="sub_category_placeholder" class="form-control">Please Select</div>
                                                            <div id="sub_category_list" class="dropdown-menu" style="display: none;"></div>
                                                        </div>

                                                        <!-- Hidden input field to store selected value -->
                                                        <input type="hidden" name="sub_category_id" id="sub_category_id" class="form-control">

                                                        @error('sub_category_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>


                                                </div>
                                                <button type="submit" class="btn btn-primary bg-primary">Add Expense</button>
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
    // Hide the original dropdown and replace with div-based dropdown
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

        if (categoryID) {
            $.ajax({
                url: '{{ url('franchise/get-subcategories') }}/' + categoryID,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#sub_category_list').empty();
                    $.each(response.data, function(index, subCategory) {
                        $('#sub_category_list').append(
                            '<div class="dropdown-item sub_category_option" data-id="' + subCategory.id + '">' + subCategory.sub_category + '</div>'
                        );
                    });
                },
                error: function() {
                    alert('Failed to fetch sub-categories. Please try again.');
                }
            });
        } else {
            $('#sub_category_list').empty();
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
                                                         // Get today's date in YYYY-MM-DD format
                                                         var today = new Date().toISOString().split('T')[0];
                                                         // Set the value of the date input to today's date
                                                         document.getElementById('date').value = today;
                                                     });
            </script>

@endsection
