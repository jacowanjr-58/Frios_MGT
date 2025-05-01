<!DOCTYPE html>
<html lang="en">
<head>
    <title>Frios Management System - Forgot Password</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Include necessary styles --}}
        {{-- @notifyCss --}}
        <link href="{{ asset('assets/vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
        <link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
        <link class="main-css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>
<body>

    <div class="authincation">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-6 col-md-8">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-3">
                                        <h2 style="color:#00abc7;">Frios Management System</h2>
                                    </div>
                                    <h4 class="text-center mb-4">Forgot Your Password?</h4>

                                    <p class="text-center mb-4 text-gray-600">
                                        No problem! Just enter your email below, and we will send you a password reset link.
                                    </p>

                                    {{-- Session Status (Success Message) --}}
                                    @if (session('status'))
                                        <div class="alert alert-success text-center">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf

                                        <!-- Email Input -->
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                Send Password Reset Link
                                            </button>
                                        </div>
                                    </form>

                                    <div class="text-center mt-3">
                                        <a href="{{ route('login') }}" class="text-primary">Back to Login</a>
                                    </div>

                                </div> <!-- auth-form -->
                            </div> <!-- col-xl-12 -->
                        </div> <!-- row no-gutters -->
                    </div> <!-- authincation-content -->
                </div> <!-- col-lg-6 col-md-8 -->
            </div> <!-- row justify-content-center -->
        </div> <!-- container -->
    </div> <!-- authincation -->

    {{-- Include necessary scripts --}}
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.localizationTool.js') }}"></script>
    <script src="{{ asset('assets/js/translator.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>
    <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script>
    {{-- @notifyJs --}}
</body>
</html>
