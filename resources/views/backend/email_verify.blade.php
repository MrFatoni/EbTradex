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
                <p>{{ __('Get verification email.') }}</p>
            </div>

            {{ Form::open(['route'=>'verification.send', 'medthod' => 'post','class' => 'verification-form validator']) }}
            <input type="hidden" value="{{base_key()}}" name="base_key">
            @if(!Auth::user())
                <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                    <div>
                        {{ Form::email(fake_field('email'), null, ['class'=>'form-control', 'placeholder' => __('Enter Email'),'data-cval-name' => 'The email field','data-cval-rules' => 'required|email']) }}
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>
                    <span class="validation-message cval-error" data-cval-error="{{ fake_field('email') }}">{{ $errors->first('email') }}</span>
                </div>
            @endif

            @if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
                <div class="form-group has-feedback {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                    <div>
                        {!! NoCaptcha::display() !!}
                    </div>
                    <span class="validation-message cval-error">{{ $errors->first('g-recaptcha-response') }}</span>
                </div>
            @endif

            {{ Form::submit(__('Get Email verify Link'), ['class'=>'btn btn-primary btn-flat btn-block form-submission-button']) }}
            {{ Form::close() }}
            <div class="clearfix link-after-form">
                @if(!Auth::user())
                    <a href="{{route('login')}}" class="pull-left link-underline">{{ __('login') }}</a>
                    <a href="{{ route('forget-password.index') }}" class="pull-right link-underline">{{ __('Forgot password?') }}</a>
                @else
                    <a href="{{route('profile.index')}}" class="pull-left link-underline">{{ __('Profile') }}</a>
                    <a href="{{ route('profile.change-password') }}" class="pull-right link-underline">{{ __('Change password') }}</a>
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
            $('.verification-form').cValidate({});
        });
    </script>
@endsection