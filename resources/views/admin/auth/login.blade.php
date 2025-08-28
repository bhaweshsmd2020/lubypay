<?php
    $logo = getCompanyLogoWithoutSession();
?>

<!DOCTYPE html>
<html>
<head>
    <style>
    .login-box, .register-box {
    width: 420px;
    margin: 7% auto;
    margin-bottom: 120px!important;
}
        .form-control {
    border-radius: 4px!important;
    box-shadow: none;
    border-color: #d2d6de;
}.login-logo, .register-logo {
    font-size: 35px;
    text-align: center;
    margin-bottom: 15px!important;
    font-weight: 300;
}
.btn.btn-flat {
    border-radius: 4px!important;}
html, body {
    height: auto!important;
}
    </style>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="paymoney">
    <title>Admin</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/bootstrap/dist/css/bootstrap.min.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/font-awesome/css/font-awesome.min.css')}}">

    <!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/dist/css/AdminLTE.min.css') }}">

    <!-- iCheck -->
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/iCheck/square/blue.css') }}">

    <!---favicon-->
    @if (!empty(getfavicon()))
        <link rel="shortcut icon" href="{{ url('public/frontend/images/favicon.png') }}" />
    @endif

</head>

<body class="hold-transition login-page" style="    background-image: '{{url('/public/frontend/images/architecture-blur-building-business-449559.jpg')}}';
    background-size: cover;
    background-position: center;">
<div class="login-box">
  

    <div class="login-box-body" style="padding:16px 18px 40px 18px  ;   border-radius: 8px;box-shadow:0 0 5px #121212;">
          <div class="login-logo">

    @if(!empty($logo))
        <a href="{{ url('admin/') }}" style="font-size:20px;">
            <img src='{{ url('public/images/logos/'. $logo) }}' class="img-responsive" width="282" height="63" style="width: 40%; margin: auto;">Admin Login
        </a>
    @else
        <img src='{{ url('public/frontend/images/logomain.png') }}' class="img-responsive" width="282" height="63" style="width: 40%; margin: auto;">
    @endif

    </div><!-- /.login-logo -->
        {{-- <p class="login-box-msg">Admin Login</p> --}}

        @if(Session::has('message'))
            <div class="alert {{ Session::get('alert-class') }} text-center">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>{{ Session::get('message') }}</strong>
            </div>
        @endif

        <form action="{{ url('admin/adminlog') }}" method="POST" id="admin_login_form">
            {{ csrf_field() }}

            <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                <label class="control-label sr-only" for="inputSuccess2">Email</label>
                <input type="email" class="form-control" placeholder="Email" name="email">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                @if ($errors->has('email'))
                    <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                @endif
            </div>

            <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                <label class="control-label sr-only" for="inputSuccess2">Password</label>
                <input type="password" class="form-control" placeholder="Password" name="password" id="password">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>

                @if ($errors->has('password'))
                    <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                @endif
            </div>

            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>
        </form>
        {{-- <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="javascript:void(0)" class="btn btn-block btn-social btn-facebook btn-flat"><i
                        class="fa fa-facebook"></i> Sign in
                using
                Facebook</a>
            <a href="javascript:void(0)" class="btn btn-block btn-social btn-google btn-flat"><i
                        class="fa fa-google-plus"></i> Sign in
                using
                Google+</a>
        </div> --}}
        <!-- /.social-auth-links -->
        <a href="{{ url('admin/forget-password') }}" style="font-size: 13px;">Forgot Password</a><br>
        {{-- <a href="javascript:void(0)" class="text-center">Register a new membership</a> --}}
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->


<footer style="
    text-align: center;
    padding: 10px;
    color: black;
">
                
<strong style="font-size: 12px;">Copyright © 2024 <a href="https://lubypay.com/" target="_blank"> Luby Pay </a> |  Powered By <a href="https://www.quickewallet.com" target="_blank">Quickewallet</a></strong>
      </footer>

<!-- jQuery 3 -->
<script src="{{ asset('public/backend/jquery/dist/jquery.min.js') }}" type="text/javascript"></script>

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<!-- Bootstrap 3.3.5 -->
<script src="{{ asset('public/backend/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>

<!-- iCheck -->
<script src="{{ asset('public/backend/iCheck/icheck.min.js') }}" type="text/javascript"></script>

<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });

    $.validator.setDefaults({
        highlight: function(element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).parent('div').removeClass('has-error');
        },
    });

    $('#admin_login_form').validate({
        errorClass: "has-error",
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true
            }
        }
    });
</script>
</body>
