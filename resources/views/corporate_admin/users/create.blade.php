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
                        <p>Add User</p>
                    </div>

                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left me-2"></i> Back to Users
                    </a>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title">Add New User</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="basic-form">

                                                <!-- Display Success Message -->
                                                @if(session('success'))
                                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                        {{ session('success') }}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                @endif

                                                <!-- Display Error Message -->
                                                @if(session('error'))
                                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                        {{ session('error') }}
                                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                @endif

                                                <form action="{{ route('users.store') }}" method="POST">
                                                    @csrf

                                                    <div class="row">
                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                                name="name" value="{{ old('name') }}" placeholder="Full Name">
                                                            @error('name')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                                name="email" value="{{ old('email') }}" placeholder="Email Address">
                                                            @error('email')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                      

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Role <span class="text-danger">*</span></label>
                                                            <select class="form-control @error('role') is-invalid @enderror" name="role">
                                                                <option value="">Select Role</option>
                                                                @foreach($roles as $role)
                                                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('role')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Assign Franchise <span
                                                                    class="text-danger">*</span></label>
                                                            <select
                                                                class="form-control select2 flex-grow-1 @error('franchisee_id') is-invalid @enderror"
                                                                name="franchisee_id">
                                                                <option value="">Select Franchise</option>
                                                                @foreach ($franchises as $franchise)
                                                                    <option value="{{ $franchise->franchisee_id }}" {{ old('franchisee_id') == $franchise->franchisee_id ? 'selected' : '' }}>
                                                                    {{ $franchise->business_name ?? 'N/A' }} - {{ $franchise->frios_territory_name ?? 'N/A' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('franchisee_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Phone Number</label>
                                                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                                                name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                                                placeholder="Phone Number">
                                                            @error('phone_number')
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
                                                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                                name="password_confirmation" placeholder="Confirm Password">
                                                            @error('password_confirmation')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                    </div>

                                                    <button type="submit" class="btn btn-primary bg-primary">Add User</button>
                                                    <a href="{{ route('users.index') }}" class="btn btn-secondary ms-2">Cancel</a>
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

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const phoneInput = document.getElementById("phone_number");

                phoneInput.addEventListener("input", function (e) {
                    let value = phoneInput.value.replace(/\D/g, ""); // Remove non-numeric characters

                    // Format as (123) 456-7890
                    if (value.length > 0) {
                        value = "(" + value;
                    }
                    if (value.length > 4) {
                        value = value.slice(0, 4) + ") " + value.slice(4);
                    }
                    if (value.length > 9) {
                        value = value.slice(0, 9) + "-" + value.slice(9, 13);
                    }

                    phoneInput.value = value;
                });

                phoneInput.addEventListener("keypress", function (e) {
                    if (!/[0-9]/.test(e.key)) {
                        e.preventDefault(); // Allow only numbers
                    }
                });
            });
        </script>

@endsection 