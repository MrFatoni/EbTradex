@extends('backend.layouts.no_header_layout')
@section('title', company_name('Unverified Account'))
@section('centralize-content')
    <div class="login-box">
        <div class="login-box-body text-center">
            <h2>{{ __('Email Unverified') }}</h2>
            <p>{{ __('Please verify your email address to explore permitted access paths in full.') }}</p>
            @if(!Auth::user())
                <a href="{{route('home')}}" class="btn btn-primary btn-flat btn-block">{{ __('Home') }}</a>
            @else
                <a href="{{route('profile.index')}}" class="btn btn-primary btn-flat btn-block">{{ __('Profile') }}</a>
            @endif
            <a href="{{route('verification.form')}}" class="btn btn-primary btn-flat btn-block">{{ __('Resend Verification Email') }}</a>
        </div>
    </div>
@endsection
