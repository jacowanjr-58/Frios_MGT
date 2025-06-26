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
                    <p>View Customer</p>
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
                                        <h4 class="card-title">View Customer</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <div class="row">
                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Franchise Name</label>
                                                    <input type="text" disabled
                                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                                        value="{{ $franchise->business_name }}" placeholder="Name">
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Customer Name</label>
                                                    <input type="text" disabled
                                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                                        value="{{ $customer->name }}" placeholder="Name">
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Phone number</label>
                                                    <input type="text" disabled
                                                        class="form-control @error('phone') is-invalid @enderror"
                                                        name="phone" value="{{ $customer->phone }}" placeholder="Phone">
                                                    @error('phone')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" disabled
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" id="email" value="{{ $customer->email }}">


                                                    @error('email')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label">Address 1</label>
                                                    <input disabled name="address1" id="address1" cols="10" rows="5"
                                                        class="form-control" value="{{ $customer->address1 }}">
                                                    @error('address1')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label">Address 2</label>
                                                    <input disabled name="address2" id="address2" cols="10" rows="5"
                                                        class="form-control" value="{{ $customer->address2 }}">
                                                    @error('address2')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>


                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label">State</label>
                                                    <input type="text"
                                                        class="form-control @error('state') is-invalid @enderror" disabled
                                                        name="state" value="{{ $customer->state }}" placeholder="State">
                                                    @error('state')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3 col-md-6">
                                                    <label class="form-label">Zip code</label>
                                                    <input type="text"
                                                        class="form-control @error('zip_code') is-invalid @enderror"
                                                        disabled name="zip_code" id="zip_code"
                                                        value="{{ $customer->zip_code }}">


                                                    @error('zip_code')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea disabled name="notes" id="notes" cols="10" rows="5"
                                                        class="form-control">{{ $customer->notes }}</textarea>
                                                    @error('zip_code')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
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
        </div>
    </div>
    <!--**********************************
                    Content body end
                ***********************************-->


@endsection