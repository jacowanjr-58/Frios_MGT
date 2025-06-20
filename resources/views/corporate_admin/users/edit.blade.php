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
                        <p>Edit User</p>
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
                                            <h4 class="card-title">Edit User: {{ $user->name }}</h4>
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

                                                <form action="{{ route('users.update', $user->user_id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="row">
                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                                name="name" value="{{ old('name', $user->name) }}" placeholder="Full Name">
                                                            @error('name')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                                name="email" value="{{ old('email', $user->email) }}" placeholder="Email Address">
                                                            @error('email')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                      

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Role <span class="text-danger">*</span></label>
                                                            <select class="form-control @error('role') is-invalid @enderror" name="role" id="role_select">
                                                                <option value="">Select Role</option>
                                                                @foreach($roles as $role)
                                                                    <option value="{{ $role->name }}" 
                                                                        {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('role')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3 col-md-6" id="franchise_field">
                                                            <label class="form-label">Assign Franchise <span
                                                                    class="text-danger" id="franchise_required">*</span></label>
                                                            <select
                                                                class="form-control select2 flex-grow-1 @error('franchisee_id') is-invalid @enderror"
                                                                name="franchisee_id" id="franchisee_select">
                                                                <option value="">Select Franchise</option>
                                                                @foreach ($franchises as $franchise)
                                                                    @php
                                                                        $selectedFranchise = old('franchisee_id', $user->franchisees ? $user->franchisees->first()?->franchisee_id : null);
                                                                    @endphp
                                                                    <option value="{{ $franchise->franchisee_id }}"
                                                                        {{ $selectedFranchise == $franchise->franchisee_id ? 'selected' : '' }}>
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
                                                                name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                                                placeholder="Phone Number">
                                                            @error('phone_number')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">New Password</label>
                                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                                name="password" placeholder="Leave blank to keep current password">
                                                            @error('password')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                            <small class="text-muted">Leave blank to keep current password</small>
                                                        </div>

                                                        <div class="mb-3 col-md-6">
                                                            <label class="form-label">Confirm New Password</label>
                                                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                                                name="password_confirmation" placeholder="Confirm new password">
                                                            @error('password_confirmation')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                    </div>

                                                    <button type="submit" class="btn btn-primary bg-primary">Update User</button>
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
                const roleSelect = document.getElementById("role_select");
                const franchiseField = document.getElementById("franchise_field");
                const franchiseRequired = document.getElementById("franchise_required");
                const franchiseSelect = document.getElementById("franchisee_select");

                // Phone number formatting
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

                // Role selection logic
                function handleRoleChange() {
                    const selectedRole = roleSelect.value;
                    
                    if (selectedRole === 'corporate_admin') {
                        // Hide franchise field for corporate admin
                        franchiseField.style.display = 'none';
                        franchiseRequired.style.display = 'none';
                        franchiseSelect.removeAttribute('required');
                        franchiseSelect.value = ''; // Clear selection
                    } else {
                        // Show franchise field for other roles
                        franchiseField.style.display = 'block';
                        franchiseRequired.style.display = 'inline';
                        franchiseSelect.setAttribute('required', 'required');
                    }
                }

                // Handle role change
                roleSelect.addEventListener('change', handleRoleChange);

                // Handle initial state on page load
                handleRoleChange();
            });
        </script>

@endsection 