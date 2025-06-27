@extends('layouts.app')
@section('content')

@can('flavor_category.edit')
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


                                            <form action="{{ route('franchise.fgpcategory.update', ['fgpcategory' => $fgpcategory->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Category Type <span class="text-danger">*</span></label>
                                                        <select class="form-control select2 @error('type') is-invalid @enderror" name="type"> <!-- Removed multiple attribute -->
                                                            <option value="">Select Category Type</option>  
                                                            @foreach ($types as $type)
                                                                <option value="{{ $type }}" {{ (is_array($fgpcategory->type) ? in_array($type, $fgpcategory->type) : $fgpcategory->type == $type) ? 'selected' : '' }}>
                                                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('type')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Include Select2 for better UI -->
                                        

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
@else
    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="alert alert-warning text-center" role="alert">
                        <i class="ti ti-alert-circle fs-20 me-2"></i>
                        <strong>Access Denied!</strong> You don't have permission to update Flavor Categories.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcan

@endsection
