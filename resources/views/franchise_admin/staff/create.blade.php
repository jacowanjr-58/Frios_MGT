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
                    <p>Add Staff</p>
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
                                        <h4 class="card-title">Add Staff</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->

                                            @role('franchise_admin')
                                            <form action="{{ route('franchise.staff.store', ['franchise' => request()->route('franchise')]) }}" method="POST">
                                            @endrole
                                            @role('franchise_manager')
                                            <form action="{{ route('franchise.staff.store', ['franchise' => request()->route('franchise')]) }}" method="POST">
                                            @endrole
                                                @csrf

                                                <div class="row">
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Staff Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                            name="name" value="{{ old('name') }}">
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
                                                        <label class="form-label">Assign Role <span class="text-danger">*</span></label>
                                                        <select class="select2 form-control @error('role') is-invalid @enderror" name="role">
                                                            <option value="">Select Role</option>
                                                            <option value="franchise_manager">Manager</option>
                                                            <option value="franchise_staff">Staff</option>
                                                        </select>
                                                        @error('role')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Phone number</label>
                                                        <input type="text" class="form-control" id="phone_number"
                                                            name="phone_number" value="{{ old('phone_number') }}"
                                                            placeholder="Phone number"  class="form-control @error('phone_number') is-invalid @enderror">
                                                        @error('phone_number')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>



                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Add Staff</button>
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
