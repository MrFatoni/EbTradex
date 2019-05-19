<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('common/images/favicon-32x32.png') }}">
    @yield('before-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/admin_lte.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/css/template_color.css') }}">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    @if( env('APP_ENV') == 'production' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
        {!! NoCaptcha::renderJs() !!}
    @endif
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @yield('after-style')
</head>

<body class="hold-transition login-page">
<div class="centralize-wrapper">
    <div class="centralize-inner">
        <div class="centralize-content">
            @yield('centralize-content')
        </div>
    </div>
</div>
@include('errors.flash_message')
<!-- jQuery 3 -->
<script src="{{ asset('common/vendors/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('common/vendors/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('common/vendors/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/adminlte.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/custom.js') }}"></script>
@yield('script')
</body>
</html>
