@extends('layouts.app')

@section('content')
<!--**********************************
            Content body start
        ***********************************-->
        <div class=" content-body default-height">
            <!-- row -->
            <div class="container-fluid">
                <div class="form-head mb-4 d-flex flex-wrap align-items-center">
                    <div class="me-auto">
                        <h2 class="font-w600 mb-0">Dashboard \</h2>
                        <p>User Details</p>
                    </div>

                    <a href="{{ route('corporate_admin.users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left me-2"></i> Back to Users
                    </a>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="card-title">User Details: {{ $user->name }}</h4>
                                            <div>
                                                <a href="{{ route('corporate_admin.users.edit', $user->user_id) }}" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit me-1"></i> Edit User
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Name:</label>
                                                        <p class="form-control-plaintext">{{ $user->name }}</p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Email:</label>
                                                        <p class="form-control-plaintext">{{ $user->email }}</p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Phone Number:</label>
                                                        <p class="form-control-plaintext">
                                                            {{ $user->phone_number ?: 'Not provided' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Role:</label>
                                                        <p class="form-control-plaintext">
                                                            @if($user->roles->count() > 0)
                                                                @foreach($user->roles as $role)
                                                                    <span class="badge bg-primary me-1">
                                                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                                    </span>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted">No role assigned</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Created Date:</label>
                                                        <p class="form-control-plaintext">
                                                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Last Updated:</label>
                                                        <p class="form-control-plaintext">
                                                            {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i:s') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>

                                                @if($user->created_date)
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Registration Date:</label>
                                                        <p class="form-control-plaintext">
                                                            {{ \Carbon\Carbon::parse($user->created_date)->format('d/m/Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">User ID:</label>
                                                        <p class="form-control-plaintext">#{{ str_pad($user->user_id, 7, '0', STR_PAD_LEFT) }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <a href="{{ route('corporate_admin.users.edit', $user->user_id) }}" class="btn btn-primary">
                                                    <i class="fa fa-edit me-1"></i> Edit User
                                                </a>
                                                <a href="{{ route('corporate_admin.users.index') }}" class="btn btn-secondary ms-2">
                                                    <i class="fa fa-list me-1"></i> Back to Users
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