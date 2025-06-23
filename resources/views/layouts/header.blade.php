<!--**********************************
            Nav header start
        ***********************************-->
<div class="nav-header">
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
    <a href="{{ route('dashboard') }}" class="brand-logo">
        <img src="{{ asset('assets/images/Frios-Logo.png') }}" class="logo" />
    </a>
</div>
<style>
    .nav-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .brand-logo {
        /* margin-left: 10px; */
        /* Adjust spacing */
    }

    .logo {
        height: 5rem;
    }

    /* Hide logo on mobile and tablet, then show after hamburger */
    @media (max-width: 768px) {
        .brand-logo {
            display: none;
        }

        .nav-header {
            justify-content: flex-start;
        }

        .nav-control {
            display: flex;
            align-items: center;
        }

        .nav-control .hamburger {
            margin-right: 70px;
        }

        .nav-header .brand-logo {
            display: block;
            margin-left: 50px !important;
        }
    }

    /* Hide hamburger menu on desktop */
    @media (min-width: 769px) {
        .nav-control {
            display: none !important;
        }
    }
</style>
<!--**********************************
            Nav header end
        ***********************************-->

<!--**********************************
            Header start
        ***********************************-->
<header class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                
                <div class="header-left">
                    @php
                        $currentRouteName = request()->route()->getName();
                        $user = auth()->user();
                        // Get franchisee ID from the current route parameter
                        $selectedFranchiseeId = request()->route('franchisee') ?? $franchiseeId ?? null;
                        $showDropdown = false;
                        $dropdownFranchisees = collect();
                        
                        // Determine if dropdown should be shown and which franchisees to display
                        if ($user->hasRole('super_admin')) {
                            // Super Admin: No dropdown
                            $showDropdown = false;
                        } elseif ($user->hasRole('corporate_admin')) {
                            // Corporate Admin: Show all franchises
                            $showDropdown = true;
                            $dropdownFranchisees = App\Models\Franchisee::select('franchisee_id', 'business_name', 'frios_territory_name')->get();
                        } else {
                            // All other roles: Show only assigned franchises (if more than one)
                            $userFranchisees = $user->franchisees ?? collect();
                            if ($userFranchisees->count() > 0) {
                                $showDropdown = true;
                                $dropdownFranchisees = $userFranchisees;
                            }
                        }
                    @endphp
                    @if($showDropdown && $currentRouteName != 'franchise.index' && $currentRouteName != 'owner.index')
                        <div class="w-100 ml-32">
                            <div class="d-flex align-items-center gap-3">
                                <label for="franchisee_id" class="form-label mb-0 text-nowrap fw-semibold">Select
                                    Franchisee:</label>
                                <select name="franchisee_id" id="franchisee_id" class="form-select select2 flex-grow-1"
                                    onchange="updateFranchiseeInCurrentRoute(this.value)">
                                    @foreach($dropdownFranchisees as $franchisee)
                                        <option value="{{ $franchisee->franchisee_id }}" {{ $selectedFranchiseeId == $franchisee->franchisee_id ? 'selected' : '' }}>
                                            {{ $franchisee->business_name ?? 'N/A' }} -
                                            {{ $franchisee->frios_territory_name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

                <ul class="navbar-nav header-right">
                    {{-- <li class="nav-item">
                        <div id="selectLanguageDropdown" name="selectLanguageDropdown" class="localizationTool"></div>
                    </li> --}}

                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                            <div class="header-info me-3">
                                <span class="fs-16 font-w600">
                                    {{ Auth::user()->name ?? 'Guest' }}
                                </span>
                                <small class="text-end fs-14 font-w400">
                                    {{ Auth::user()->role->name ?? 'User' }}
                                </small>
                            </div>
                            {{-- <img
                                src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('assets/images/profile/default.png') }}"
                                width="20" alt="Profile Picture"> --}}
                            <img src="{{asset('assets/images/default_avatar.jpg') }}" width="20" alt="Profile Picture">
                        </a>
                        

                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>

<!--**********************************
            Header end ti-comment-alt
        ***********************************-->
<style scoped>
    .ml-32 {
        margin-left: -32px;
    }

    /* Fix dropdown overflow issues */
    .header-content {
        overflow: visible !important;
    }

    .navbar-collapse {
        overflow: visible !important;
    }

    .header-left {
        overflow: visible !important;
        position: relative;
        z-index: 1000;
    }

    /* Ensure Select2 dropdown appears properly */
    .select2-container {
        z-index: 9999 !important;
    }

    .select2-dropdown {
        z-index: 9999 !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Improve select appearance */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        padding: 6px 12px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 0 !important;
        /* color: #495057 !important; */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 10px !important;
    }

    .position-relative {
        position: relative !important;
        /* background: red; */
        /* width: 253px; */
    }

    .select2-results__option[role=option] {
    margin: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    padding: 0.5rem 1rem;
    color: black;
}
    .header-left input {
    background: #fff;
    min-width: 124px !important;
    min-height: 40px;
    border-color: transparent;
    color: #6e6e6e !important;
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    box-shadow: none;
}
</style>

<script>
function updateFranchiseeInCurrentRoute(franchiseeId) {
    if (!franchiseeId) return;

    // Send an AJAX request to update the session
    fetch('{{ route("franchise.set_session_franchisee") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ franchisee_id: franchiseeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Now that session is set, redirect
            const currentPath = window.location.pathname;
            const franchiseRouteRegex = /\/franchise\/\d+/;
            if (franchiseRouteRegex.test(currentPath)) {
                const newPath = currentPath.replace(/\/franchise\/\d+/, '/franchise/' + franchiseeId);
                window.location.href = window.location.origin + newPath + window.location.search;
            } else {
                window.location.href = '/franchise/' + franchiseeId + '/dashboard';
            }
        } else {
            // Handle error, maybe show an alert
            alert('Could not update franchisee. Please try again.');
        }
    }).catch(error => {
        console.error('Error updating franchisee session:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>