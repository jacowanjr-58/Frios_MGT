@extends('layouts.app')

@section('content')
    <div class="content-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Create Role</h4>
                            <div class="card-header-right">
                                <a href="{{ route('corporate_admin.roles.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Roles
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

                            <form action="{{ route('corporate_admin.roles.store') }}" method="POST" id="roleForm">
                                @csrf

                                <!-- Role Name -->
                                <div class="mb-4">
                                    <label for="role_name" class="form-label">Role Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="role_name" name="name" value="{{ old('name') }}" placeholder="Enter role name"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Permissions -->
                                <div class="mb-4">
                                    <label class="form-label">Permissions <span class="text-danger">*</span></label>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="give_all_permissions"
                                                name="give_all_permissions" onchange="toggleAllPermissions()">
                                            <label class="form-check-label fw-bold" for="give_all_permissions">
                                                Give All Permissions
                                            </label>
                                        </div>
                                    </div>

                                    <div class="row" id="permissions-container">
                                        @foreach($permissions as $module => $modulePermissions)
                                            <div class="col-md-4 mb-4">
                                                <div class="card border">
                                                    <div class="card-header bg-light py-2">
                                                        <h6 class="mb-0 fw-semibold">{{ $module }}</h6>
                                                    </div>
                                                    <div class="card-body py-3">
                                                        @foreach($modulePermissions as $permission)
                                                            <div class="form-check form-switch mb-2">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    id="permission_{{ $permission['id'] }}" name="permissions[]"
                                                                    value="{{ $permission['id'] }}">
                                                                <label class="form-check-label"
                                                                    for="permission_{{ $permission['id'] }}">
                                                                    {{ $permission['display_name'] }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Create Role
                                    </button>
                                    <a href="{{ route('corporate_admin.roles.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleAllPermissions() {
            const giveAllCheckbox = document.getElementById('give_all_permissions');
            const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
            const permissionsContainer = document.getElementById('permissions-container');

            if (giveAllCheckbox.checked) {
                // Hide individual permissions and check all
                permissionsContainer.style.opacity = '0.5';
                permissionsContainer.style.pointerEvents = 'none';
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    checkbox.disabled = true;
                });
            } else {
                // Show individual permissions and enable selection
                permissionsContainer.style.opacity = '1';
                permissionsContainer.style.pointerEvents = 'auto';
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.disabled = false;
                });
            }
        }

        // Form validation
        document.getElementById('roleForm').addEventListener('submit', function (e) {
            const roleName = document.getElementById('role_name').value.trim();
            const giveAllPermissions = document.getElementById('give_all_permissions').checked;
            const selectedPermissions = document.querySelectorAll('.permission-checkbox:checked').length;

            if (!roleName) {
                e.preventDefault();
                alert('Please enter a role name.');
                return;
            }

            if (!giveAllPermissions && selectedPermissions === 0) {
                e.preventDefault();
                alert('Please select at least one permission or check "Give All Permissions".');
                return;
            }
        });

        // Add module toggle functionality
        document.addEventListener('DOMContentLoaded', function () {
            // Add "Select All" checkboxes to each module
            const modules = document.querySelectorAll('#permissions-container .card');

            modules.forEach(module => {
                const header = module.querySelector('.card-header h6');
                const checkboxes = module.querySelectorAll('.permission-checkbox');

                if (checkboxes.length > 1) {
                    const selectAllCheckbox = document.createElement('input');
                    selectAllCheckbox.type = 'checkbox';
                    selectAllCheckbox.className = 'form-check-input me-2';
                    selectAllCheckbox.style.transform = 'scale(0.8)';

                    selectAllCheckbox.addEventListener('change', function () {
                        checkboxes.forEach(checkbox => {
                            if (!checkbox.disabled) {
                                checkbox.checked = this.checked;
                            }
                        });
                    });

                    header.insertBefore(selectAllCheckbox, header.firstChild);
                }
            });
        });
    </script>
@endpush

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