<!DOCTYPE html>
<html lang="en">
<head>
    <title>Frios Management System - Password Reset</title>
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
                                    <h4 class="text-center mb-4">Reset Your Password</h4>
                                    <form method="POST" action="{{ route('password.store') }}">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                        
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                        
                                        <div class="mb-3 position-relative">
                                            <label class="mb-1 form-label">New Password</label>
                                            <input type="password" id="password" class="form-control" name="password" required>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>
                                        
                                        <div class="mb-3 position-relative">
                                            <label class="mb-1 form-label">Confirm Password</label>
                                            <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" required>
                                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                        </div>
                                        
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3 text-center">
                                        <a class="text-primary" href="{{ route('login') }}">Back to Login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   {{-- Include necessary scripts --}}
   <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
   <script src="{{ asset('assets/vendor/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
   <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
   <script src="{{ asset('assets/js/jquery.localizationTool.js') }}"></script>
   <script src="{{ asset('assets/js/translator.js') }}"></script>
   <script src="{{ asset('assets/js/custom.min.js') }}"></script>
   {{-- <script src="{{ asset('assets/js/demo.js') }}"></script> --}}
   {{-- <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script> --}}
   {{-- @notifyJs --}}
</body>
</html>
