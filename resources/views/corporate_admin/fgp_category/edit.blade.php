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
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Category</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('franchise.fgpcategory.update', $fgpcategory->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Category Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="{{ old('name', $fgpcategory->name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="parent_id" class="form-label">Parent Category</label>
                                    <select class="form-select" name="parent_id" id="parent_id">
                                        <option value="">-- No Parent (Top Level) --</option>
                                        @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ $fgpcategory->parent_id ==
                                            $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Category</button>
                            </form>
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
