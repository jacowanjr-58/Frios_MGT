@extends('layouts.app')
@section('content')

    @can('flavor_category.create')
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
                        <p>Add Category</p>
                    </div>

                    <a href="javascript:history.back()" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left me-2"></i> Back
                    </a>
                </div>

               <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Category</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('fgpcategory.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Category Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="parent_id" class="form-label">Parent Category</label>
                                        <select class="form-select" name="parent_id" id="parent_id">
                                            <option value="">-- No Parent (Top Level) --</option>
                                            @foreach($parents as $parent)
                                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Add Category</button>
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
                            <strong>Access Denied!</strong> You don't have permission to create Flavor Categories.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

@endsection
