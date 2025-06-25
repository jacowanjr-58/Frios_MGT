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
                    <p>Add Additional Charges</p>
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
                                        <h4 class="card-title">Add Additional Charges</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            @can('additional_charges.create')
                                            <!-- Display Success Message -->

                                            <form action="{{ route('additionalcharges.store') }}" method="POST">
                                                @csrf

                                                <div class="row">
                                                    <!-- Charges Name -->
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Charges Name <span class="text-danger">*</span></label>
                                                        <input type="text"
                                                               class="form-control @error('charge_name') is-invalid @enderror"
                                                               name="charge_name"
                                                               value="{{ old('charge_name') }}"
                                                               placeholder="Enter charge name">
                                                        @error('charge_name')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Charges Amount -->
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Charges Amount <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01"
                                                               class="form-control @error('charge_price') is-invalid @enderror"
                                                               name="charge_price"
                                                               value="{{ old('charge_price') }}"
                                                               placeholder="Enter charge amount">
                                                        @error('charge_price')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Charges Type (Fixed or Percentage) -->
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Charges Type <span class="text-danger">*</span></label> <br>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" class="form-check-input" id="chargeTypeFixed" name="charge_type" value="fixed"
                                                                {{ old('charge_type', 'fixed') == 'fixed' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="chargeTypeFixed">Fixed</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input type="radio" class="form-check-input" id="chargeTypePercentage" name="charge_type" value="percentage"
                                                                {{ old('charge_type') == 'percentage' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="chargeTypePercentage">Percentage</label>
                                                        </div>
                                                    </div>

                                                    <!-- Charges Type (Checkbox: if checked, then "required", otherwise defaults to "optional") -->
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Charges Type <span class="text-danger">*</span></label>
                                                        <!-- Hidden input to default to "optional" -->
                                                        <input type="hidden" name="charge_optional" value="optional">
                                                        <div class="form-check">
                                                            <input type="checkbox"
                                                                   class="form-check-input @error('charge_optional') is-invalid @enderror"
                                                                   id="chargeOptional"
                                                                   name="charge_optional"
                                                                   value="required"
                                                                   {{ old('charge_optional') == 'required' ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="chargeOptional">Required</label>
                                                        </div>
                                                        <small class="text-muted">Leave unchecked to make this optional.</small>
                                                        @error('charge_optional')
                                                            <div class="text-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Add Charges</button>
                                            </form>
                                            @else
                                            <div class="alert alert-warning">
                                                <h5><i class="fa fa-lock me-2"></i>Access Denied</h5>
                                                <p>You don't have permission to create additional charges. Please contact your administrator for access.</p>
                                                <a href="{{ route('additionalcharges.index') }}" class="btn btn-secondary btn-sm">
                                                    <i class="fa fa-arrow-left me-2"></i>Back to Additional Charges List
                                                </a>
                                            </div>
                                            @endcan

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
