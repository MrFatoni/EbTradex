@extends('backend.layouts.no_header_layout')
@section('title', company_name('Suspended Account'))
@section('centralize-content')
    <div class="login-box">
        <div class="login-box-body text-center">
            <h2>{{ __('Account Suspended') }}</h2>
            <p>{{ __('Please contact admin to get back your account.') }}</p>
            @if(!Auth::user())
                <a href="{{route('home')}}" class="btn btn-primary btn-flat btn-block">{{ __('Home') }}</a>
            @else
                <a href="{{route('profile.index')}}" class="btn btn-primary btn-flat btn-block">{{ __('Profile') }}</a>
            @endif
        </div>
    </div>
@endsection
