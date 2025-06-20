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
                    <p>Edit Flaover Item</p>
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
                                        <h4 class="card-title">Edit Flaover Item</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->


                                            <form action="{{ route('franchise.fgpitem.update', ['franchisee' => request()->route('franchisee'), 'fgpitem' => $fgpitem->fgp_item_id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6">
                                                        <!-- Item Name -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                                   name="name" value="{{ old('name', $fgpitem->name) }}" placeholder="Enter Item Name">
                                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                        </div>

                                                        <!-- Case Cost -->
                                                        <div class="mb-3 ">
                                                            <label class="form-label">Case Cost <span class="text-danger">*</span></label>
                                                            <input type="number" step="0.01" class="form-control @error('case_cost') is-invalid @enderror"
                                                                   name="case_cost" value="{{ old('case_cost', $fgpitem->case_cost) }}" placeholder="Enter Case Cost">
                                                            @error('case_cost') <div class="text-danger">{{ $message }}</div> @enderror
                                                        </div>

                                                       <!-- Category Selection -->
                                                       <div class="mb-3">
                                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                                        <div class="form-control" style="height: auto; padding: 10px;">
                                                            @foreach ($categorizedCategories as $categoryGroup => $categories)
                                                                <h6 class="fw-bold p-2">{{ $categoryGroup }}</h6>
                                                                @foreach ($categories as $category)
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" name="category_ID[]"
                                                                            value="{{ $category->category_ID }}" id="category_{{ $category->category_ID }}"
                                                                            {{ in_array($category->category_ID, $selectedCategories) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="category_{{ $category->category_ID }}">
                                                                            {{ $category->name }}
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                                <hr>
                                                            @endforeach
                                                        </div>
                                                        @error('category_ID')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>


                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="col-md-6">
                                                        <!-- Description -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <input type="text" class="form-control @error('description') is-invalid @enderror"
                                                                   name="description" value="{{ old('description', $fgpitem->description) }}" placeholder="Enter Description">
                                                            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                                                        </div>

                                                        <!-- Internal Inventory -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Internal Inventory <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control @error('internal_inventory') is-invalid @enderror"
                                                                   name="internal_inventory" value="{{ old('internal_inventory', $fgpitem->internal_inventory) }}"
                                                                   placeholder="Enter Inventory Count">
                                                            @error('internal_inventory') <div class="text-danger">{{ $message }}</div> @enderror
                                                        </div>

                                                          <!-- Image Uploads -->
                                                    <div class="mb-3">
                                                        <label class="form-label">Image 1</label>
                                                        <input type="file" class="form-control @error('image1') is-invalid @enderror" name="image1">
                                                        @if ($fgpitem->image1)
                                                            <img src="{{ asset('storage/'.$fgpitem->image1) }}" alt="Item Image 1" class="img-thumbnail mt-2" width="100">
                                                        @endif
                                                        @error('image1') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Image 2</label>
                                                        <input type="file" class="form-control @error('image2') is-invalid @enderror" name="image2">
                                                        @if ($fgpitem->image2)
                                                            <img src="{{ asset('storage/'.$fgpitem->image2) }}" alt="Item Image 2" class="img-thumbnail mt-2" width="100">
                                                        @endif
                                                        @error('image2') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Image 3</label>
                                                        <input type="file" class="form-control @error('image3') is-invalid @enderror" name="image3">
                                                        @if ($fgpitem->image3)
                                                            <img src="{{ asset('storage/'.$fgpitem->image3) }}" alt="Item Image 3" class="img-thumbnail mt-2" width="100">
                                                        @endif
                                                        @error('image3') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                                    </div>
                                                </div>



                                                <button type="submit" class="btn btn-primary bg-primary">Update Item</button>
                                            </form>

                                            <!-- Flatpickr CSS -->
                                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

                                            <!-- Flatpickr JS -->
                                            <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    flatpickr("#dates_available", {
                                                        mode: "multiple",
                                                        dateFormat: "Y-m-d",
                                                        allowInput: true
                                                    });
                                                });
                                            </script>



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
