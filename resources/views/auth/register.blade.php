<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Frios Management System</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
                                    <h4 class="text-center mb-4">Sign up your account</h4>
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Username</label>
                                            <input type="text" class="form-control" name="name" placeholder="Enter your username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Email</label>
                                            <input type="email" class="form-control" name="email" placeholder="Enter your email" required>
                                        </div>
                                        <div class="mb-3 position-relative">
                                            <label class="mb-1 form-label">Password</label>
                                            <input type="password" id="password" class="form-control" name="password" required>
                                            <span class="show-pass eye">
                                                <i class="fa fa-eye-slash"></i>
                                                <i class="fa fa-eye"></i>
                                            </span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Confirm Password</label>
                                            <input type="password" class="form-control" name="password_confirmation" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="mb-1 form-label">Select Role</label>
                                            <select class="form-control" name="role" required>
                                                <option value="">Select Role</option>
                                                <option value="admin">Admin</option>
                                                <option value="manager">Manager</option>
                                                <option value="user">User</option>
                                            </select>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary btn-block">Sign me up</button>
                                        </div>
                                    </form>
                                    <div class="new-account mt-3">
                                        <p>Already have an account? <a class="text-primary" href="{{ route('login') }}">Sign in</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.localizationTool.js') }}"></script>
    <script src="{{ asset('assets/js/translator.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>
    <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script>
</body>
</html>
