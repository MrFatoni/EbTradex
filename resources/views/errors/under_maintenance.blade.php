@extends('backend.layouts.no_header_layout')
@section('title', __('Under Maintenance'))
@section('centralize-content')
    <div class="login-box">
        <div class="login-box-body text-center">
            <h2>{{ __('Under Maintenance') }}</h2>
            <p>{{ __("The website is still under maintenance mode. send us an email anytime: :email",['email'=> admin_settings('admin_receive_email')]) }}</p>
        </div>
    </div>
@endsection
