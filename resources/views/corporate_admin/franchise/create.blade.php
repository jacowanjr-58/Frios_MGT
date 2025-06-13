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
                    <p>Add Franchise</p>
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
                                        <h4 class="card-title">Add Franchise</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <!-- Display Success Message -->
                                            <form action="{{ route('corporate_admin.franchise.store') }}" method="POST">
                                                @csrf
                                                <div class="row">
                                                    <!-- Left Side Fields -->
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label">Business Name <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('business_name') is-invalid @enderror"
                                                                name="business_name" value="{{ old('business_name') }}"
                                                                placeholder="Business Name">
                                                            @error('business_name')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Contact Number <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('contact_number') is-invalid @enderror"
                                                                name="contact_number" value="{{ old('contact_number') }}"
                                                                placeholder="Contact Number">
                                                            @error('contact_number')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Frios Territory Name</label>
                                                            <input type="text"
                                                                class="form-control @error('frios_territory_name') is-invalid @enderror"
                                                                name="frios_territory_name" value="{{ old('frios_territory_name') }}"
                                                                placeholder="Frios Territory Name">
                                                            @error('frios_territory_name')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Address 1 <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('address1') is-invalid @enderror"
                                                                name="address1" value="{{ old('address1') }}"
                                                                placeholder="Address Line 1">
                                                            <small class="form-text text-muted">Street address, P.O box,
                                                                company name, c/o</small>
                                                            @error('address1')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Address 2</label>
                                                            <input type="text"
                                                                class="form-control @error('address2') is-invalid @enderror"
                                                                name="address2" value="{{ old('address2') }}"
                                                                placeholder="Address Line 2">
                                                            <small class="form-text text-muted">Apartment, suite, unit,
                                                                building, floor, etc.</small>
                                                            @error('address2')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">City <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('city') is-invalid @enderror"
                                                                name="city" value="{{ old('city') }}" placeholder="City">
                                                            @error('city')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">State/Province/Region <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('state') is-invalid @enderror"
                                                                name="state" value="{{ old('state') }}" placeholder="State">
                                                            @error('state')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Zip / Postal Code <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="text"
                                                                class="form-control @error('zip_code') is-invalid @enderror"
                                                                name="zip_code" value="{{ old('zip_code') }}"
                                                                placeholder="Zip Code" pattern="\d{5}">
                                                            @error('zip_code')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Right Side ZIP Code Selection -->
                                                    <div class="col-md-6">
                                                        <!-- <div class="mb-3">
                                                            <label class="form-label">Assign to Parent Franchise (Optional)</label>
                                                            <select class="form-control select2 @error('parent_franchise_id') is-invalid @enderror" 
                                                                    name="parent_franchise_id" id="parent_franchise_id">
                                                                <option value="">Select Parent Franchise</option>
                                                                @if(isset($franchises))
                                                                    @foreach ($franchises as $franchise)
                                                                        <option value="{{ $franchise->franchisee_id }}" 
                                                                                {{ old('parent_franchise_id') == $franchise->franchisee_id ? 'selected' : '' }}>
                                                                            {{ $franchise->frios_territory_name ? $franchise->frios_territory_name . ' - ' : '' }}{{ $franchise->business_name }}
                                                                        </option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                            <small class="form-text text-muted">Link this franchise to an existing parent franchise</small>
                                                            @error('parent_franchise_id')
                                                                <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div> -->

                                                        {{-- <div class="mb-3">
                                                            <label class="form-label">Territory ZIP Codes</label>
                                                            <select class="form-control" name="location_zip[]"
                                                                id="location_zip" multiple></select>
                                                            <small class="form-text text-muted">Select or add ZIP
                                                                codes.</small>
                                                            @error('location_zip')
                                                            <div class="text-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div> --}}

                                                        <div class="mb-3">
                                                            <label for="paste-zipcodes" class="form-label">Paste ZIP Codes
                                                                (comma or newline separated)</label>
                                                            <small id="success-message" class="text-success"
                                                                style="display:none;">
                                                                ZIP codes updated successfully.
                                                            </small>
                                                            <small id="error-message" class="text-danger"
                                                                style="display:none;">
                                                                Invalid ZIP code entered. Please enter a valid 5-digit ZIP
                                                                code.
                                                            </small>
                                                            <small id="duplicate-message" class="text-warning"
                                                                style="display:none;">
                                                                This ZIP code already exists.
                                                            </small>
                                                            <textarea id="paste-zipcodes" class="form-control" rows="3"
                                                                placeholder="Enter ZIP codes..."></textarea>
                                                            <button id="parse-zipcodes"
                                                                class="btn btn-secondary bg-secondary mt-2" type="button">
                                                                Process ZIP Codes
                                                            </button>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="zipcodes" class="form-label">ZIP Codes</label>
                                                            <div id="zipcodes-list"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary bg-primary">Add
                                                    Franchise</button>
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
    <!--********************************** Content body end ***********************************-->
    <script>
        $(document).ready(function () {
            // Initialize Select2 for parent franchise dropdown
            $('#parent_franchise_id').select2({
                placeholder: "Search and select parent franchise...",
                allowClear: true,
                width: '100%'
            });
            let zipSet = new Set();
            let zipDropdown = $('#location_zip');

            // Predefined ZIP codes
            let predefinedZips = [
                "30188", "30144", "30101", "30157", "30132", "30102", "30701", "30165", "30120", "30189",
                "30161", "30141", "30121", "30125", "30153", "30747", "30103", "30145", "30184", "30147",
                "30173", "30104", "30124", "30178", "30731", "30733", "30137", "30149", "30105", "30730",
                "35984", "30525", "30568", "30546", "30582", "30512", "30523", "30552", "30571", "30528",
                "30572", "30533", "30531", "30535", "30577", "30573", "30538", "30557", "30554", "30527",
                "30564", "30563", "30506", "30543", "30507", "30542", "30562", "28707", "28717", "28723",
                "28725", "28736", "28779", "28783", "28788", "28789", "28734", "28741", "28744", "28763",
                "28775", "30121"
            ];
            predefinedZips.forEach(zip => zipSet.add(zip));

            // Function to show messages
            function showMessage(type, message) {
                let msgBox = $(`#${type}-message}`);
                msgBox.text(message).fadeIn();
                setTimeout(() => msgBox.fadeOut(), 3000);
            }

            function initializeZipDropdown() {
                zipDropdown.empty();
                zipSet.forEach(zip => {
                    let newOption = new Option(zip, zip, false, false);
                    zipDropdown.append(newOption);
                });

                if ($.fn.select2) {
                    zipDropdown.select2({
                        tags: true,
                        tokenSeparators: [',', ' '],
                        placeholder: "Type or select ZIP codes...",
                        allowClear: true
                    });

                    // Capture manual ZIP entry
                    zipDropdown.on('select2:select', function (e) {
                        let selectedZip = e.params.data.id;
                        if (!zipSet.has(selectedZip)) {
                            if (/^\d{5}$/.test(selectedZip)) {
                                zipSet.add(selectedZip);
                                addToZipList(selectedZip);
                                showMessage("success", "ZIP code added successfully.");
                            } else {
                                showMessage("error", "Invalid ZIP code entered.");
                                zipDropdown.find(`option[value="${selectedZip}"]`).remove();
                                zipDropdown.trigger('change.select2');
                            }
                        } else {
                            showMessage("duplicate", "This ZIP code already exists.");
                        }
                    });
                }
            }

            initializeZipDropdown();

            $('#parse-zipcodes').click(function () {
                let input = $('#paste-zipcodes').val().trim();
                $('#paste-zipcodes').val("");

                let newZipcodes = input.split(/[ ,\n]+/)
                    .map(zip => zip.trim())
                    .filter(zip => zip !== "");

                newZipcodes.forEach(zip => {
                    if (/^\d{5}$/.test(zip)) {
                        if (!zipSet.has(zip)) {
                            zipSet.add(zip);
                            let newOption = new Option(zip, zip, false, false);
                            zipDropdown.append(newOption);
                            zipDropdown.trigger('change.select2');
                            addToZipList(zip);
                            showMessage("success", "ZIP code added successfully.");
                        } else {
                            showMessage("duplicate", "This ZIP code already exists.");
                        }
                    } else {
                        showMessage("error", "Invalid ZIP code entered.");
                    }
                });
            });

            function addToZipList(zip) {
                let zipList = $('#zipcodes-list');
                let div = $('<div class="d-flex mb-2"></div>');
                div.append(`<input type="text" class="form-control zip-input me-2 border-0" name="location_zip[]" readonly value="${zip}">`);
                div.append('<button class="btn btn-danger bg-danger remove-zip" type="button">Remove</button>');
                zipList.append(div);
            }

            $(document).on('click', '.remove-zip', function () {
                let zipValue = $(this).siblings('.zip-input').val();
                if (zipSet.has(zipValue)) {
                    zipSet.delete(zipValue);
                }
                $(this).parent().remove();
                $('#location_zip option[value="' + zipValue + '"]').remove();
                zipDropdown.trigger('change.select2');
            });
        });
    </script>



@endsection