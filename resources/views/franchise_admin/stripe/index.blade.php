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
                    <p>Stripe Keys</p>
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
                                        <h4 class="card-title">Stripe Keys</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->

                                            <form action="{{ route('franchise.stripe.post') }}" method="POST">
                                                @csrf

                                                <div class="row">

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Public key <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('public_key') is-invalid @enderror" name="public_key"
                                                            value="{{ old('public_key', $stripe?->public_key) }}" required>
                                                        @error('public_key') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Secret key <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('secret_key') is-invalid @enderror" name="secret_key"
                                                            value="{{ old('secret_key', $stripe?->secret_key) }}" required>
                                                        @error('secret_key') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>


                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Save</button>
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
@endsection
