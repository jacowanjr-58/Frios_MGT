{{-- filepath: resources/views/franchise_admin/profile/view.blade.php --}}
@extends('layouts.app')
@section('content')

<!--**********************************
        Content body start
    ***********************************-->
<div class="content-body default-height">
    <!-- row -->
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Dashboard \</h2>
                <p>Profile Details</p>
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
                                    <h4 class="card-title">Profile Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Name</label>
                                                <div class="form-control-plaintext">{{ $user->name }}</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Email</label>
                                                <div class="form-control-plaintext">{{ $user->email }}</div>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Phone number</label>
                                                <div class="form-control-plaintext">{{ $user->phone_number }}</div>
                                            </div>
                                        </div>
                                        @if($franchisee)
                                            <a href="{{ route('franchise.profile.edit', [$franchisee, $user->user_id]) }}" class="btn btn-primary mt-3">
                                        @else
                                            <a href="{{ route('profile.edit', $user->user_id) }}" class="btn btn-primary mt-3">
                                        @endif
                                            Edit Profile
                                        </a>
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