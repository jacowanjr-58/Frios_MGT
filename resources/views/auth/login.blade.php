<!DOCTYPE html>
<html lang="en">

<head>
    <title>Frios Management System</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- @notifyCss --}}
    <link href="{{ asset('assets/vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
    <link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
        rel="stylesheet">
    <link href="{{ asset('assets/css/jquery.localizationTool.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
</head>

<body>
    {{-- <x:notify-messages />
    @if(session('notify'))
    <x:notify-messages />
    @endif --}}

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
                                    <h4 class="text-center mb-4">Sign in to your account</h4>
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email') }}" required autofocus>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>
                                        <div class="mb-3 position-relative">
                                            <label class="mb-1 form-label">Password</label>
                                            <input type="password" id="password" class="form-control" name="password"
                                                required>
                                            <span class="show-pass eye">
                                                <i class="fa fa-eye-slash"></i>
                                                <i class="fa fa-eye"></i>
                                            </span>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>
                                        @if (session('error'))
                                            <div class="alert alert-danger text-center">
                                                {{ session('error') }}
                                            </div>
                                        @endif
                                        <div class="form-row d-flex flex-wrap justify-content-between mt-4 mb-2">
                                            <div class="form-group">
                                                <div class="form-check custom-checkbox ms-1">
                                                    <input type="checkbox" class="form-check-input" id="remember_me"
                                                        name="remember">
                                                    <label class="form-check-label" for="remember_me">Remember
                                                        me</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                @if (Route::has('password.request'))
                                                    <a class="text-hover" href="{{ route('password.request') }}">Forgot
                                                        Password?</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary btn-block">Sign Me In</button>
                                        </div>

                                        <div class="text-center mt-3">
                                            <a href="{{ route('auth.google') }}"
                                                class="btn  border px-4 py-2 d-flex align-items-center justify-content-center"
                                                style="width: 250px; margin: 0 auto; font-weight: 500;">
                                                <img src="https://cdn4.iconfinder.com/data/icons/logos-brands-7/512/google_logo-google_icongoogle-512.png"
                                                    alt="Google" width="20" height="20" class="me-2">
                                                Continue with Google
                                            </a>
                                        </div>


                                    </form>
                                    {{-- <div class="new-account mt-3">
                                        <p>Don't have an account? <a class="text-primary"
                                                href="{{ route('register') }}">Sign up</a></p>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let passwordField = document.getElementById("password");
            let eyeIcons = document.querySelector(".show-pass");

            eyeIcons.addEventListener("click", function () {
                let eyeSlash = eyeIcons.querySelector(".fa-eye-slash");
                let eye = eyeIcons.querySelector(".fa-eye");

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    eyeSlash.style.display = "none";
                    eye.style.display = "inline";
                } else {
                    passwordField.type = "password";
                    eye.style.display = "none";
                    eyeSlash.style.display = "inline";
                }
            });
        });

    </script>
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