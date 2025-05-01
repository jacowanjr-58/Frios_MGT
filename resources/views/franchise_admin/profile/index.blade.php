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
                    <p>Profile</p>
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
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Profile</h4>
                                        <a href="{{ route('profile.edit', $user->user_id) }}" class="btn btn-primary">Edit Profile</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                
                                            <!-- Display Success Message -->
                                            @if(session('success'))
                                                <div class="alert alert-success">
                                                    {{ session('success') }}
                                                </div>
                                            @endif
                                
                                            <form>
                                                @csrf
                                
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                                </div>
                                
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                                </div>
                                
                                                <div class="mb-3">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="text" class="form-control" value="{{ $user->phone_number }}" readonly>
                                                </div>
                                
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