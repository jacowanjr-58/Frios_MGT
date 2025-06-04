<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Frios Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon icon -->
    <link rel="shortcut icon" type="image/x-icon"
        href="https://friospops.wpenginepowered.com/wp-content/uploads/2022/04/Frios-Logo-2022-light-yellow-orange-pop-01.png">

    <!-- Styles -->
    <link href="{{ asset('assets/vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
    <link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/fullcalendar/css/main.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/vendor/fullcalendar/js/main.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/fullcalendar-init.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- jQuery (Always load first) -->

    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
    @notifyCss
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        .custom-hover:hover {
            color: white !important;
        }

        .btn-outline-yellow {
            width: 100%;
            padding: 0.5rem 1rem;
            display: inline-block;
            background-color: #fde68a22;
            /* light transparent fill on hover */
            color: #000;
            /* or keep #FDE68A if you want */
        }

        .notify {
            z-index: 9999 !important;
            position: fixed !important;
        }


        /* .btn-outline-yellow:hover {
    color: #FDE68A;
    border: 1px solid #FDE68A;
    background-color: transparent;
} */
        @stack('styles')
    </style>
</head>


<body class="font-sans antialiased">
    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--dot"></div>
            <div class="loader--text"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
        <div class="min-h-screen bg-gray-100">

            <!-- Header -->
            @include('layouts.header')

            <div class="flex">
                <!-- Sidebar -->
                @include('layouts.sidebar')

                <!-- Main Content -->
                <div class="flex-1 p-6" style="max-width: 100%" >
                    <x-notify::notify />
                    @yield('content')

                </div>
            </div>

            <!-- Footer -->
            @include('layouts.footer')
        </div>
    </div>

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Laravel Notify JS -->
    @stack('scripts')
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <!-- Required vendors -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chartjs/chart.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datetimepicker/js/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/js/bootstrap-select.min.js') }}"></script>

    <!-- Chart piety plugin files -->
    <script src="{{ asset('assets/vendor/peity/jquery.peity.min.js') }}"></script>

    <!-- Apex Chart -->
    <script src="{{ asset('assets/vendor/apexchart/apexchart.js') }}"></script>

    <!-- Dashboard 1 -->
    <script src="{{ asset('assets/js/dashboard/dashboard-1.js') }}"></script>

    <!-- Datatable -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Localization Tool -->
    <script src="{{ asset('assets/js/jquery.localizationTool.js') }}"></script>
    <script src="{{ asset('assets/js/translator.js') }}"></script>

    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>

    {{-- <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script> --}}
    <script>
        $(function() {
            $("#datepicker").datepicker({
                autoclose: true,
                todayHighlight: true
            }).datepicker('update', new Date());

        });
    </script>

    <!-- Alpine.js (for Notify to animate) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (session('info'))
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Information',
                text: '{{ session('info') }}',
                showConfirmButton: true,
            });
        </script>
    @endif

    <script>
        jQuery(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end', // Toast appears at the top-right corner
                showConfirmButton: false,
                timer: 3000, // Automatically close after 3 seconds
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Check for success message
            @if (session('success'))
                let successMessage = "{{ session('success') }}".toLowerCase();
                successMessage = successMessage.charAt(0).toUpperCase() + successMessage.slice(
                1); // Capitalize the first letter

                Toast.fire({
                    icon: 'success',
                    title: successMessage
                });
            @endif

            // Check for error message
            @if ($errors->any())
                let errorMessage = "There were some errors with your submission!".toLowerCase();
                errorMessage = errorMessage.charAt(0).toUpperCase() + errorMessage.slice(1);

                Toast.fire({
                    icon: 'error',
                    title: errorMessage
                });
            @endif

            // Check for info message
            @if (session('info'))
                let infoMessage = "{{ session('info') }}".toLowerCase();
                infoMessage = infoMessage.charAt(0).toUpperCase() + infoMessage.slice(1);

                Toast.fire({
                    icon: 'info',
                    title: infoMessage
                });
            @endif

            // Check for warning message
            @if (session('warning'))
                let warningMessage = "{{ session('warning') }}".toLowerCase();
                warningMessage = warningMessage.charAt(0).toUpperCase() + warningMessage.slice(1);

                Toast.fire({
                    icon: 'warning',
                    title: warningMessage
                });
            @endif
        });
    </script>

    <!-- Laravel Notify -->
    @notifyJs

</body>

</html>
