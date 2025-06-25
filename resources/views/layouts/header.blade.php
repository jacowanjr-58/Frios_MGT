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
                        $selectedFranchiseId = request()->route('franchise') ?? $franchiseId ?? null;

                        $showDropdown = false;
                        $dropdownFranchises = collect();
                        
                        if ($user->hasRole('super_admin')) {
                            $showDropdown = false;
                        } elseif ($user->hasRole('corporate_admin')) {
                            $showDropdown = true;
                            $dropdownFranchises = App\Models\Franchise::select('id', 'business_name', 'frios_territory_name')->get();
                        } else {
                            $userFranchises = $user->franchises ?? collect();
                            if ($userFranchises->count() > 0) {
                                $showDropdown = true;
                                $dropdownFranchises = $userFranchises;
                            }
                        }
                    @endphp
                    @if($showDropdown && $currentRouteName != 'franchise.index')
                        <div class="w-100 ml-32">
                            <div class="d-flex align-items-center gap-3">
                                <label for="franchise-select" class="form-label mb-0 text-nowrap fw-semibold">Select
                                    Franchise:</label>
                                <select id="franchise-select" class="form-select select2 flex-grow-1"
                                    onchange="updateFranchiseInCurrentRoute(this.value)">
                                    @foreach($dropdownFranchises as $franchise)
                                        <option value="{{ $franchise->id }}" {{ $selectedFranchiseId == $franchise->id ? 'selected' : '' }}>
                                            {{ $franchise->business_name }} - {{ $franchise->frios_territory_name }}
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
    function updateFranchiseInCurrentRoute(franchiseId) {
        // Get current URL
        let currentUrl = window.location.href;

        // Check if URL already contains a franchise parameter
        let franchisePattern = /\/franchise\/\d+\//;
        let newUrl;

        if (franchisePattern.test(currentUrl)) {
            // Replace existing franchise ID
            newUrl = currentUrl.replace(/\/franchise\/\d+\//, `/franchise/${franchiseId}/`);
        } else {
            // Add franchise ID to URL
            newUrl = currentUrl.replace('/franchise/', `/franchise/${franchiseId}/`);
        }

        // Update URL without reloading page
        window.history.pushState({}, '', newUrl);

        // Make AJAX call to update session
        fetch('/franchise/set-session-franchise', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                franchise_id: franchiseId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Reload the page to reflect the new franchise
                window.location.href = newUrl;
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>