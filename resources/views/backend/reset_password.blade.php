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

            <form action="{{ $passwordResetLink }}" method="post" class="validator">
                {{ csrf_field() }}
                <div class="form-group has-feedback {{ $errors->has('new_password') ? 'has-error' : '' }}">
                    <div>
                        {{ Form::password('new_password', ['class'=>'form-control', 'placeholder' => __('Enter new password'),'data-cval-name' => 'The new password field','data-cval-rules' => 'required|escapeInput|followedBy:new_password_confirmation|between:6,32']) }}
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <span class="validation-message cval-error" data-cval-error="new_password">{{ $errors->first('new_password') }}</span>
                </div>
                <div class="form-group has-feedback {{ $errors->has('new_password_confirmation') ? 'has-error' : '' }}">
                    <div>
                        {{ Form::password('new_password_confirmation', ['class'=>'form-control', 'placeholder' => __('Repeat new password'),'data-cval-name' => 'The confirm password field','data-cval-rules' => 'required|escapeInput|follow:new_password']) }}
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <span class="validation-message cval-error" data-cval-error="new_password_confirmation">{{ $errors->first('new_password_confirmation') }}</span>
                </div>

                @if( env('APP_ENV') != 'local' && admin_settings('display_google_captcha') == ACTIVE_STATUS_ACTIVE )
                    <div class="form-group has-feedback {{ $errors->has('g-recaptcha-response') ? 'has-error' : '' }}">
                        <div>
                            {!! NoCaptcha::display() !!}
                        </div>
                        <span class="validation-message cval-error">{{ $errors->first('g-recaptcha-response') }}</span>
                    </div>
                @endif

                {{ Form::submit(__('Reset Password'), ['class'=>'btn btn-primary btn-flat btn-block form-submission-button']) }}
            </form>
            <div class="clearfix link-after-form">
                <a href="{{ route('login') }}" class="pull-left link-underline">{{ __('login') }}</a>
                <a href="{{route('register.index')}}" class="text-center pull-right link-underline">{{ __('Create an account') }}</a>
            </div>
        </div>
        <!-- /.login-box-body -->
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });
    </script>
@endsection