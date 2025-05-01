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
        <img src="https://friospops.wpenginepowered.com/wp-content/uploads/2022/04/Frios-Logo-2022-light-yellow-orange-pop-01.png"
            class="logo" />
    </a>
</div>
<style>
    .nav-header {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    .brand-logo {
        margin-left: 10px;
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
                        <div class="dropdown-menu dropdown-menu-end">
                            @role('corporate_admin')
                            <a href="{{ route('profile.index') }}" class="dropdown-item ai-icon d-flex">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ms-2">Profile</span>
                            </a>
                            @endrole
                            @role('franchise_admin')
                            <a href="{{ route('profile.index') }}" class="dropdown-item ai-icon d-flex">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ms-2">Profile</span>
                            </a>
                            @endrole
                            @role('franchise_admin')
                            <a href="{{ route('franchise.staff.index') }}" class="dropdown-item ai-icon d-flex">
                                <i class="bi bi-people-fill text-primary"></i>
                                <span class="ms-2">Manage Users</span>
                            </a>
                            @endrole
                            @role('franchise_manager')
                            <a href="{{ route('franchise.staff.index') }}" class="dropdown-item ai-icon d-flex">
                                <i class="bi bi-people-fill text-primary"></i>
                                <span class="ms-2">Manage Users</span>
                            </a>
                            @endrole
                            <a href="{{ route('logout') }}" class="dropdown-item ai-icon d-flex"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ms-2">Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>

                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>

<!--**********************************
            Header end ti-comment-alt
        ***********************************-->