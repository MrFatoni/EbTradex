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
                <p>{{ __('Sign in to start your session') }}</p>
                <p><a href="{{ route('register.index') }}" class="link-underline">{{ __('I need a new account') }}</a></p>
            </div>

            {{ Form::open(['route'=>'login', 'medthod' => 'post','class' => 'login-form']) }}
            <input type="hidden" value="{{base_key()}}" name="base_key">
            <div class="form-group has-feedback {{ $errors->has('username') ? 'has-error' : '' }}">
                <div>
                    {{ Form::text(fake_field('username'), null, ['class'=>'form-control', 'placeholder' => __('Username'),'data-cval-name' => 'Username','data-cval-rules' => 'required']) }}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('username') }}">{{ $errors->first('username') }}</span>
            </div>
            <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                <div>
                    {{ Form::input('password',fake_field('password'), null,['class'=>'form-control', 'placeholder' => __('Password'),'data-cval-name' => 'Password','data-cval-rules' => 'required']) }}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
            </div>

            @if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
                <div class="form-group has-feedback {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                    <div>
                        {!! NoCaptcha::display() !!}
                    </div>
                    <span class="validation-message cval-error">{{ $errors->first('g-recaptcha-response') }}</span>
                </div>
            @endif

            <div class="row">
                <div class="col-xs-7">
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox(fake_field('remember_me'), 1, false) }}
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-xs-5">
                    {{ Form::submit(__('Sign In'), ['class'=>'btn btn-primary btn-flat btn-block form-submission-button']) }}
                </div>
                <!-- /.col -->
            </div>
            {{ Form::close() }}
            <div class="clearfix link-after-form">
                <a href="{{ route('forget-password.index') }}" class="pull-left link-underline">{{ __('Forget Password') }}</a>
                @if(admin_settings('require_email_verification') == ACTIVE_STATUS_ACTIVE)
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
            $('.login-form').cValidate({});
        });
    </script>
@endsection