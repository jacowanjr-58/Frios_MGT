@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
        <div class="container-fluid">
        <div class="page-titles">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Roles
                </a></li>
                <li class="breadcrumb-item active">Create Role</li>
            </ol>
        </div>

            <div class="row">
            <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                        <h4 class="card-title">Create New Role</h4>
                        <div class="card-action">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to Roles
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                        <form action="{{ route('roles.store') }}" method="POST" id="roleForm">
                                @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                    <div class="mb-3">
                                <label class="form-label">Permissions</label>
                                <div class="row">
                                    @foreach($permissions->groupBy(function($permission) { return explode('.', $permission->name)[0]; }) as $module => $modulePermissions)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card">
                                            <div class="card-header py-2">
                                                <h6 class="mb-0">{{ ucfirst(str_replace('_', ' ', $module)) }}</h6>
                                        <div class="form-check">
                                                    <input class="form-check-input module-checkbox" type="checkbox" 
                                                           id="module_{{ $module }}" data-module="{{ $module }}">
                                                    <label class="form-check-label" for="module_{{ $module }}">
                                                        Select All
                                            </label>
                                        </div>
                                    </div>
                                            <div class="card-body py-2">
                                                        @foreach($modulePermissions as $permission)
                                                <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                           value="{{ $permission->name }}" id="permission_{{ $permission->id }}" 
                                                           name="permissions[]" data-module="{{ $module }}"
                                                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ ucfirst(str_replace(['_', '.'], [' ', ' '], $permission->name)) }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @error('permissions')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                                </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Create Role</button>
                                <a href="{{ route('roles.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle module checkbox changes
        document.querySelectorAll('.module-checkbox').forEach(function(moduleCheckbox) {
            moduleCheckbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const isChecked = this.checked;
                
                document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(function(permissionCheckbox) {
                    permissionCheckbox.checked = isChecked;
                });
            });
        });

        // Handle individual permission checkbox changes
        document.querySelectorAll('.permission-checkbox').forEach(function(permissionCheckbox) {
            permissionCheckbox.addEventListener('change', function() {
                const module = this.dataset.module;
                const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${module}"]`);
                const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                const checkedPermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]:checked`);
                
                // Update module checkbox state
                if (checkedPermissions.length === modulePermissions.length) {
                    moduleCheckbox.checked = true;
                    moduleCheckbox.indeterminate = false;
                } else if (checkedPermissions.length > 0) {
                    moduleCheckbox.checked = false;
                    moduleCheckbox.indeterminate = true;
                } else {
                    moduleCheckbox.checked = false;
                    moduleCheckbox.indeterminate = false;
                }
            });
            });
        });
    </script>
@endsection

@push('styles')
    <style scoped>
        .form-check-input:checked {
            background-color: #5a67d8;
            border-color: #5a67d8;
        }


        .permission-checkbox:disabled {
            opacity: 0.6;
        }

        #permissions-container {
            transition: opacity 0.3s ease;
        }
        .form-check-label {
            font-size: 0.9rem;
            cursor: pointer;
        }
        .card-title {
            color: white !important;
        }
        .card-header {
            background-color: #00ABC7 !important;
        }
        [type=button], [type=reset], [type=submit], button {
            background-color: #00ABC7 !important;
        }
        .card {
            box-shadow: 0 0 0 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }
        .fw-semibold {
            font-weight: 600 !important;
            color: white;
            position: relative;
            top: 2px;

        }

        .me-2 {
            margin-right: 0.5rem !important;
            position: relative;
            top: -3px;
        }
    </style>
@endpush