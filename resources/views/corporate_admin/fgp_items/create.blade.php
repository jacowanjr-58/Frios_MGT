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
                    <p>Add Flaover Item</p>
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
                                        <h4 class="card-title">Add Flavor Item</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->


                                            <form action="{{ route('franchise.fgpitem.store', ['franchisee' => request()->route('franchisee')]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf

                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6">
                                                        <!-- Item Name -->
                                                        <div class="mb-3">
                                                            <label for="item_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('item_name') is-invalid @enderror" 
                                                                   id="item_name" name="item_name" value="{{ old('item_name') }}" required>
                                                            @error('item_name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Case Cost -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Case Cost <span class="text-danger">*</span></label>
                                                            <input type="number" step="0.01" class="form-control @error('case_cost') is-invalid @enderror"
                                                                   name="case_cost" value="{{ old('case_cost') }}" placeholder="Enter Case Cost">
                                                            @error('case_cost')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                      <!-- Category Selection -->
                                                      <div class="mb-3">
                                                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                                        <select class="form-control @error('category_id') is-invalid @enderror" 
                                                                id="category_id" name="category_id" required>
                                                            <option value="">Select Category</option>
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->category_ID }}" 
                                                                    {{ old('category_ID') == $category->category_ID ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('category_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>


                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="col-md-6">
                                                        <!-- Description -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                                                   name="description" value="{{ old('description') }}" placeholder="Enter Description">
                                                            @error('description')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Internal Inventory -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Internal Inventory <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control @error('internal_inventory') is-invalid @enderror"
                                                                   name="internal_inventory" value="{{ old('internal_inventory') }}"
                                                                   placeholder="Enter Inventory Count">
                                                            @error('internal_inventory')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <!-- Image Uploads -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Image 1</label>
                                                            <input type="file" class="form-control @error('image1') is-invalid @enderror" name="image1">
                                                            @error('image1')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Image 2</label>
                                                            <input type="file" class="form-control @error('image2') is-invalid @enderror" name="image2">
                                                            @error('image2')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Image 3</label>
                                                            <input type="file" class="form-control @error('image3') is-invalid @enderror" name="image3">
                                                            @error('image3')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submit Button -->
                                                    <button type="submit" class="btn btn-primary bg-primary">Add Item</button>
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
   <!-- Orderable Dropdown -->
{{-- <div class="mb-3 col-md-6">
    <label class="form-label">Orderable <span
            class="text-danger">*</span></label>
    <select
        class="form-control @error('orderable') is-invalid @enderror"
        name="orderable">
        <option value="">Select Option</option>
        <option value="1" {{ old('orderable') == 1 ? 'selected' : '' }}>
            Yes</option>
        <option value="0" {{ old('orderable') == 0 ? 'selected' : '' }}>No
        </option>
    </select>
    @error('orderable')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div> --}}
<!-- Available Dates with Checkboxes for Each Month -->
{{-- <div class="mb-3 col-md-6">
    <label class="form-label">Select Flavor Availability</label>
    <div class="form-control" style="height: auto; padding: 10px;">
        @foreach ([
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'] as $month)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="dates_available[]"
                       value="{{ $month }}" id="month_{{ $month }}">
                <label class="form-check-label" for="month_{{ $month }}">
                    {{ $month }}
                </label>
            </div>
        @endforeach
    </div>
    @error('dates_available')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div> --}}
@endsection
