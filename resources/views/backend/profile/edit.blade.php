@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('user', $user->id)])
        </div>
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                @include('backend.profile.profile_nav')
                <div class="box box-solid">
                    <div class="box-body">
                        {{ Form::open(['route'=>['profile.update'],'class'=>'form-horizontal edit-profile-form','method'=>'put']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">
                        {{--first name--}}
                        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('first_name') }}" class="col-md-3 control-label required">{{ __('First Name') }}</label>
                            <div class="col-md-9">
                                {{ Form::text(fake_field('first_name'), old('first_name', $user->userInfo->first_name), ['class'=>'form-control', 'id' => fake_field('first_name'),'data-cval-name' => 'The first name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('first_name') }}">{{ $errors->first('first_name') }}</span>
                            </div>
                        </div>
                        {{--last name--}}
                        <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('last_name') }}" class="col-md-3 control-label required">{{ __('Last Name') }}</label>
                            <div class="col-md-9">
                                {{ Form::text(fake_field('last_name'), old('last_name', $user->userInfo->last_name), ['class'=>'form-control', 'id' => fake_field('last_name'),'data-cval-name' => 'The last name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('last_name') }}">{{ $errors->first('last_name') }}</span>
                            </div>
                        </div>
                        {{--email--}}
                        <div class="form-group">
                            <label class="col-md-3 control-label required">{{ __('Email') }}</label>
                            <div class="col-md-9">
                                <p class="form-control">{{ $user->email }}</p>
                            </div>
                        </div>
                        {{--username--}}
                        <div class="form-group">
                            <label class="col-md-3 control-label required">{{ __('Username') }}</label>
                            <div class="col-md-9">
                                <p class="form-control">{{ $user->username }}</p>
                            </div>
                        </div>
                        {{--address--}}
                        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('address') }}" class="col-md-3 control-label">{{ __('Address') }}</label>
                            <div class="col-md-9">
                                {{ Form::textarea(fake_field('address'),  old('address', $user->userInfo->address), ['class'=>'form-control', 'id' => fake_field('address'), 'rows'=>2,'data-cval-name' => 'The address name field','data-cval-rules' => 'escapeInput']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('address') }}">{{ $errors->first('address') }}</span>
                            </div>
                        </div>
                        {{--submit button--}}
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-9">
                                {{ Form::submit(__('Update Information'),['class'=>'btn btn-info btn-sm btn-left btn-sm-block form-submission-button']) }}
                                {{ Form::reset(__('Reset'),['class'=>'btn btn-warning btn-sm btn-left btn-sm-block reset-button']) }}
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                    <div class="box-footer clearfix">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('profile.index') }}"
                                   class="btn btn-sm btn-info btn-flat btn-sm-block">{{ __('View Profile') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.edit-profile-form').cValidate();
        });
    </script>
@endsection