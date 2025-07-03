{{-- filepath: resources/views/corporate_admin/franchise/view.blade.php --}}
@extends('layouts.app')
@section('content')

<div class="content-body default-height">
    <div class="container-fluid">
        <div class="form-head mb-4 d-flex flex-wrap align-items-center">
            <div class="me-auto">
                <h2 class="font-w600 mb-0">Dashboard \</h2>
                <p>Franchise Details</p>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
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
                                    <h4 class="card-title">Franchise Information</h4>
                                </div>
                                <div class="card-body">
                                    <div class="basic-form">
                                        <div class="row">
                                            <!-- Left Side: Business Information -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Business Name</label>
                                                    <div class="form-control-plaintext">{{ $franchise->business_name  ?? 'N/A'}}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">EIN/SSN</label>
                                                    <div class="form-control-plaintext">{{ $franchise->decrypted_ein_ssn ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Contact Number</label>
                                                    <div class="form-control-plaintext">{{ $franchise->contact_number ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Frios Territory Name</label>
                                                    <div class="form-control-plaintext">{{ $franchise->frios_territory_name ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Address 1</label>
                                                    <div class="form-control-plaintext">{{ $franchise->address1 }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Address 2</label>
                                                    <div class="form-control-plaintext">{{ $franchise->address2 }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">City</label>
                                                    <div class="form-control-plaintext">{{ $franchise->city }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">State/Province/Region</label>
                                                    <div class="form-control-plaintext">{{ $franchise->state }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Zip / Postal Code</label>
                                                    <div class="form-control-plaintext">{{ $franchise->zip_code }}</div>
                                                </div>
                                            </div>

                                            <!-- Right Side: ZIP Code Management -->
                                            <div class="col-md-6">
                                                @php
                                                    $selectedZips = is_array($franchise->location_zip)
                                                        ? $franchise->location_zip
                                                        : (is_string($franchise->location_zip) ? explode(',', $franchise->location_zip) : []);
                                                @endphp
                                                <div class="mb-3">
                                                    <label class="form-label">Territory ZIP Codes</label>
                                                    <ul class="list-group">
                                                        @forelse($selectedZips as $zip)
                                                            <li class="list-group-item">{{ $zip }}</li>
                                                        @empty
                                                            <li class="list-group-item text-muted">No ZIP codes assigned.</li>
                                                        @endforelse
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        @can('franchises.edit')
                                            <a href="{{ route('franchise.edit', $franchise->franchise_id) }}" class="btn btn-primary mt-3">
                                                Edit Franchise
                                            </a>
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
@endsection
