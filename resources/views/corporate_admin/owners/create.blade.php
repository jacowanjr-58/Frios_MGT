@extends('layouts.app')
@section('content')

    {{-- <div class="container">
        <h1>Add Franchise</h1>
        <form action="{{ route('corporate_admin.franchise.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Business Name</label>
                <input type="text" name="business_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div> --}}


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
                    <p>Add Owner</p>
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
                                        <h4 class="card-title">Add Owner</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->


                                            <form action="{{ route('corporate_admin.owner.store') }}" method="POST">
                                                @csrf

                                                <div class="row">
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Owner Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name') }}" placeholder="Owner Name">
                                                        @error('name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                            name="email" value="{{ old('email') }}" placeholder="Email">
                                                        @error('email')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                            name="password" placeholder="Password">
                                                        @error('password')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Assign Franchise <span class="text-danger">*</span></label>
                                                        <select class="form-control multiple-select  @error('franchisee_id') is-invalid @enderror" name="franchisee_id[]" multiple>
                                                            <option value="">Select Franchise</option>
                                                            @foreach ($franchises as $franchise)
                                                                <option value="{{ $franchise->franchisee_id }}">{{ $franchise->business_name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('franchisee_id')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    {{-- <div class="mb-3 col-md-6">
                                                        <label class="form-label">Clearance</label>
                                                        <input type="text" class="form-control @error('clearance') is-invalid @enderror"
                                                            name="clearance" value="{{ old('clearance') }}" placeholder="Clearance">
                                                        @error('clearance')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Security</label>
                                                        <input type="text" class="form-control @error('security') is-invalid @enderror"
                                                            name="security" value="{{ old('security') }}" placeholder="Security">
                                                        @error('security')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div> --}}
                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Add Owner</button>
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
