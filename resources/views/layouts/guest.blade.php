<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>


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
    </body>
</html>
