@extends('backend.layouts.no_header_layout')
@section('title', company_name())
@section('centralize-content')
    <div class="login-box">
        <div class="login-box-body text-center">
            <div class="clearfix">
                <img src="{{ asset('common/images/g2fa.png') }}" alt="GOOGLE AUTHENTICATION" class="img-responsive img-md cm-center">
            </div>
            <h4>{{ __('Google 2 Factor Authentication') }}</h4>
            <form action="{{ route('profile.google-2fa.verify') }}" method="post" class="login-form">
                {{ csrf_field() }}
                <div class="form-group has-feedback {{ $errors->has('google_app_code') ? 'has-error' : '' }}">
                    <div>
                        {{ Form::text('google_app_code', null, ['class'=>'form-control text-center', 'placeholder' => __('Enter G2FA app code'), 'data-cval-name' => 'One time password','data-cval-rules' => 'required|integer']) }}
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <span class="validation-message cval-error" data-cval-error="google_app_code">{{ $errors->first('google_app_code') }}</span>
                </div>
                <div class="row">
                    <!-- /.col -->
                    <div class="col-xs-12">
                        {{ Form::submit(__('Verify Google 2FA Code'), ['class'=>'btn btn-primary btn-flat btn-block form-submission-button']) }}
                    </div>
                    <!-- /.col -->
                </div>
            </form>
        </div>
    </div>
@endsection

@section('after-style')
    <style>
        .cm-center {
            float: none;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.login-form').cValidate({});
        });
    </script>
@endsection