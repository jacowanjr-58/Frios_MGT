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

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-xl-12">
                    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
                        <div class="col-auto text-center">
                            <a href="{{ route('franchise.stripe.onboard') }}" class="btn btn-primary btn-lg">
                                Connect to Stripe
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
