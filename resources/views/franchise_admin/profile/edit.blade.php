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
                    <p>Edit Profile</p>
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
                                        <h4 class="card-title">Edit Profile</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->
                                            @if(session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif
                                            <form action="{{ route('profile.update', $user->user_id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                            
                                                <div class="row">
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" 
                                                            value="{{ old('name', $user->name) }}" required>
                                                        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                            
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" 
                                                            value="{{ old('email', $user->email) }}" required>
                                                        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Phone number</label>
                                                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                                            id="phone_number" name="phone_number" 
                                                            value="{{ old('phone_number', $user->phone_number) }}" placeholder="Phone number">
                                                        @error('phone_number') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                                    <div class="mt-3 mb-3">
                                                        <h4 class="card-title">Change Password</h4>
                                                        <hr>
                                                    </div>
                                                    <!-- Old Password Field -->
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Old Password <span class="text-danger">*</span></label>
                                                        <input type="password" class="form-control @error('old_password') is-invalid @enderror" 
                                                            name="old_password">
                                                        @error('old_password') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                            
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">New Password (Leave empty to keep current)</label>
                                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                                                        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                            
                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Confirm Password (Leave empty to keep current)</label>
                                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                            name="password_confirmation">
                                                        @error('password_confirmation') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                            
                                                </div>
                                            
                                                <button type="submit" class="btn btn-primary bg-primary">Update Profile</button>
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
                    const phoneError = document.getElementById("phoneError");
            
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
            
                        // Validate phone format
                        const phoneValid = /^\(\d{3}\) \d{3}-\d{4}$/.test(phoneInput.value);
                        if (!phoneValid) {
                            phoneError.textContent = "Invalid phone number format.";
                        } else {
                            phoneError.textContent = "";
                        }
                    });
            
                    phoneInput.addEventListener("keypress", function (e) {
                        if (!/[0-9]/.test(e.key)) {
                            e.preventDefault(); // Allow only numbers
                        }
                    });
                });
            </script>
@endsection