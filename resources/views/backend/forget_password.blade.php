@extends('backend.layouts.no_header_layout')
@section('title', company_name())
@section('centralize-content')
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/') }}"><b>{{ company_name() }}</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <div class="login-box-msg">
                <p>{{ __('Reset password ') }}</p>
            </div>

            {{ Form::open(['route'=>'forget-password.send-mail', 'medthod' => 'post','class'=>'validator']) }}
            <input type="hidden" value="{{base_key()}}" name="base_key">
            <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                <div>
                    {{ Form::email(fake_field('email'), null, ['class'=>'form-control', 'placeholder' => __('Enter Email'), 'data-cval-name' => 'Email','data-cval-rules' => 'required|email']) }}
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('email') }}">{{ $errors->first('email') }}</span>
            </div>

            @if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
                <div class="form-group has-feedback {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                    <div>
                        {!! NoCaptcha::display() !!}
                    </div>
                    <span class="validation-message cval-error">{{ $errors->first('g-recaptcha-response') }}</span>
                </div>
            @endif

            {{ Form::submit(__('Get Password Reset Link'), ['class'=>'btn btn-primary btn-flat btn-block form-submission-button']) }}
            {{ Form::close() }}
            <div class="clearfix link-after-form">
                <a href="{{ route('login') }}" class="pull-left link-underline">{{ __('Login') }}</a>
                @if(admin_settings('require_email_verification'))
                    <a href="{{ route('verification.form') }}" class="text-center pull-right link-underline">{{ __('Get verification email') }}</a>
                @endif
            </div>
        </div>
        <!-- /.login-box-body -->
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate();
        });
    </script>
@endsection