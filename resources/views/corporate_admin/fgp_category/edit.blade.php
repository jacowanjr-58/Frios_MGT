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
                    <p>Edit Category</p>
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
                                        <h4 class="card-title">Edit Category</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->


                                            <form action="{{ route('corporate_admin.fgpcategory.update', $fgpcategory->category_ID) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category Type <span class="text-danger">*</span></label>
                                                        <select class="form-control @error('type') is-invalid @enderror" name="type"> <!-- Removed multiple attribute -->
                                                            <option value="Availability" {{ $fgpcategory->type == 'Availability' ? 'selected' : '' }}>Availability</option>
                                                            <option value="Flavor" {{ $fgpcategory->type == 'Flavor' ? 'selected' : '' }}>Flavor</option>
                                                            <option value="Allergen" {{ $fgpcategory->type == 'Allergen' ? 'selected' : '' }}>Allergen</option>
                                                        </select>
                                                        @error('type')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Include Select2 for better UI -->
                                                    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
                                                    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('select[name="type"]').select2({
                                                                placeholder: "Select Category Type",
                                                                allowClear: true
                                                            });
                                                        });
                                                    </script>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name', $fgpcategory->name) }}" required>
                                                        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Update Category</button>
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
