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
                <p>{{ __('Create your account.') }}</p>
            </div>

            {{ Form::open(['route'=>'register.store', 'method'=>'post','class'=>'validator']) }}
            <input type="hidden" value="{{base_key()}}" name="base_key">

            @if(request()->has('ref') && admin_settings('referral'))
                <input type="hidden" name="referral_code" value="{{ request()->get('ref') }}">
            @endif

            <div class="form-group has-feedback {{ $errors->has('first_name') ? 'has-error' : '' }}">
                <div>
                    {{ Form::text(fake_field('first_name'), old('first_name', null), ['class'=>'form-control', 'placeholder' => __('Enter first name'),'data-cval-name' => 'The first name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ fake_field('first_name') }}">{{ $errors->first('first_name') }}</span>
            </div>

            <div class="form-group has-feedback {{ $errors->has('last_name') ? 'has-error' : '' }}">
                <div>
                    {{ Form::text(fake_field('last_name'), old('last_name', null), ['class'=>'form-control', 'placeholder' => __('Enter last name'),'data-cval-name' => 'The last name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ fake_field('last_name') }}">{{ $errors->first('last_name') }}</span>
            </div>

            <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                <div>
                    {{ Form::email(fake_field('email'), old('email', null), ['class'=>'form-control', 'placeholder' => __('Enter Email'),'data-cval-name' => 'The email field','data-cval-rules' => 'required|escapeInput|email']) }}
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ fake_field('email') }}">{{ $errors->first('email') }}</span>
            </div>

            <div class="form-group has-feedback {{ $errors->has('username') ? 'has-error' : '' }}">
                <div>
                    {{ Form::text(fake_field('username'), old('username', null), ['class'=>'form-control', 'placeholder' => __('Enter username'),'data-cval-name' => 'The username field','data-cval-rules' => 'required|escapeInput']) }}
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ fake_field('username') }}">{{ $errors->first('username') }}</span>
            </div>

            <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                <div>
                    {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter password'),'data-cval-name' => 'The password field','data-cval-rules' => 'required|escapeInput|between:6,32|followedBy:'.fake_field('password_confirmation')]) }}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
            </div>

            <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                <div>
                    {{ Form::password(fake_field('password_confirmation'), ['class'=>'form-control', 'placeholder' => __('Repeat password'),'data-cval-name' => 'The confirm password field','data-cval-rules' => 'required|escapeInput|between:6,32|follow:'.fake_field('password')]) }}
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ fake_field('password_confirmation') }}">{{ $errors->first('password_confirmation') }}</span>
            </div>

            @if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
                <div class="form-group has-feedback {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                    <div>
                        {!! NoCaptcha::display() !!}
                    </div>
                    <span class="validation-message cval-error">{{ $errors->first('g-recaptcha-response') }}</span>
                </div>
            @endif

            <div class="form-group has-feedback {{ $errors->has('check_agreement') ? 'has-error' : '' }}">
                <div>
                    <label>
                        <input type="checkbox" name="check_agreement" value="1" data-cval-name='The agreement field'
                               data-cval-rules='required'{{old('check_agreement') ? ' checked' : ''}}> {{  __('Accept our terms and conditions.') }}
                    </label>
                </div>
                <span class="validation-message cval-error"
                      data-cval-error="{{ 'check_agreement' }}">{{ $errors->first('check_agreement') }}</span>
            </div>

            {{ Form::submit(__('Register'), ['class'=>'btn btn-primary btn-flat btn-block form-submission-button']) }}
            {{ Form::close() }}

            <div class="clearfix link-after-form">
                <a href="{{ route('forget-password.index') }}"
                   class="pull-left link-underline">{{ __('Forget Password') }}</a>
                <a href="{{ route('login') }}"
                   class="text-center pull-right link-underline">{{ __('Login to your account') }}</a>
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
