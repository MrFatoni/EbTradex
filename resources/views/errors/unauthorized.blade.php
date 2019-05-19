@extends('backend.layouts.no_header_layout')
@section('title', __('Unauthorized Access'))
@section('centralize-content')
    <div class="login-box">
        <div class="login-box-body text-center">
            <h2>{{ __('Unauthorized Access') }}</h2>
            <p>{{ __('You are not authorized to access this page.') }}</p>
            @if(!Auth::user())
                <a href="{{route('home')}}" class="btn btn-primary btn-flat btn-block">{{ __('Home') }}</a>
            @else
                <a href="{{route('profile.index')}}" class="btn btn-primary btn-flat btn-block">{{ __('Profile') }}</a>
            @endif
        </div>
    </div>
@endsection
