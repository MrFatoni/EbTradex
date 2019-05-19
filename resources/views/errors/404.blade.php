@extends('backend.layouts.no_header_layout')
@section('title', !empty($exception) && $exception->getMessage() ? $exception->getMessage() : __('404 NOT FOUND!'))
@section('centralize-content')
    <div class="login-box">
        <div class="login-box-body text-center">
            <h2>{{ !empty($exception) && $exception->getMessage() ? $exception->getMessage() : __('404 NOT FOUND!') }}</h2>
            <p>{{ __('The page you are looking for is not found.') }}</p>
            <a href="{{route('home')}}" class="btn btn-primary btn-flat btn-block">{{ __('Home') }}</a>
        </div>
    </div>
@endsection