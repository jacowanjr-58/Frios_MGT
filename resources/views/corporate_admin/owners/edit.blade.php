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
                    <p>Edit Owner</p>
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
                                        <h4 class="card-title">Edit Owner</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">

                                            <!-- Display Success Message -->


                                            <form action="{{ route('owner.update', ['franchise' => $franchise, 'owner' => $owner->id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="row">
                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Owner Name <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $owner->name) }}" required>
                                                        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $owner->email) }}" required>
                                                        @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Assign Franchise <span class="text-danger">*</span></label>
                                                        <select
                                                            class="form-control select2"
                                                            name="franchise_id[]" multiple="multiple">
                                                            <option value="">Select Franchise</option>
                                                            @foreach ($franchises as $franchise)
                                                                <option value="{{ $franchise->id }}"
                                                                    {{ in_array($franchise->id, $owner->franchises->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                                    {{ $franchise->business_name ?? 'N/A' }} - {{ $franchise->frios_territory_name ?? 'N/A' }}
                                                                </option>
                                                            @endforeach
                                                        </select>

                                                        @error('franchise_id') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Date Joined</label>
                                                        <input type="date" class="form-control @error('date_joined') is-invalid @enderror" 
                                                               name="date_joined" value="{{ old('date_joined', $owner->date_joined ?? 'N/A') }}">
                                                        @error('date_joined') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-12">
                                                        <label class="form-label">Franchise Contract Document 
                                                            <small class="text-muted">(PDF, DOC, DOCX - Max 10MB)</small>
                                                        </label>
                                                        <input type="file" class="form-control @error('contract_document') is-invalid @enderror" 
                                                               name="contract_document" accept=".pdf,.doc,.docx">
                                                        @error('contract_document') <div class="text-danger">{{ $message }}</div> @enderror
                                                        @if($owner->contract_document_path)
                                                            <small class="text-muted">
                                                                Current document: 
                                                                <a href="{{ url($owner->contract_document_path) }}" target="_blank">
                                                                    {{ basename($owner->contract_document_path) }}
                                                                </a>
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Password (Leave empty to keep current)</label>
                                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                                                        @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>

                                                    <div class="mb-3 col-md-6">
                                                        <label class="form-label">Confirm Password</label>
                                                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation">
                                                        @error('password_confirmation') <div class="text-danger">{{ $message }}</div> @enderror
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Update Owner</button>
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


@endsection
