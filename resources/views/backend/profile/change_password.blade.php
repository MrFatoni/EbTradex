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
                        {{ Form::open(['route'=>['profile.update-password'],'class'=>'form-horizontal validator','method'=>'put']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">
                        {{--password--}}
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('password') }}"
                                   class="col-md-3 control-label required">{{ __('Current Password') }}</label>
                            <div class="col-md-9">
                                {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter current password'), 'id' => fake_field('password'),'data-cval-name' => 'The password','data-cval-rules' => 'required|escapeInput']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
                            </div>
                        </div>
                        {{--new password--}}
                        <div class="form-group {{ $errors->has('new_password') ? 'has-error' : '' }}">
                            <label for="new_password"
                                   class="col-md-3 control-label required">{{ __('New Password') }}</label>
                            <div class="col-md-9">
                                {{ Form::password('new_password', ['class'=>'form-control', 'placeholder' => __('Enter new password'), 'id' => 'new_password','data-cval-name' => 'The new password','data-cval-rules' => 'required|escapeInput|between:6,32|followedBy:new_password_confirmation']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('new_password') }}">{{ $errors->first('new_password') }}</span>
                            </div>
                        </div>
                        {{--email--}}
                        <div class="form-group {{ $errors->has('new_password_confirmation') ? 'has-error' : '' }}">
                            <label for="new_password_confirmation"
                                   class="col-md-3 control-label required">{{ __('Confirm New Password') }}</label>
                            <div class="col-md-9">
                                {{ Form::password('new_password_confirmation', ['class'=>'form-control', 'placeholder' => __('Confirm new password'), 'id' => 'new_password_confirmation','data-cval-name' => 'The confirm new password','data-cval-rules' => 'required|escapeInput|between:6,32|follow:new_password']) }}
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('new_password_confirmation') }}">{{ $errors->first('new_password_confirmation') }}</span>
                            </div>
                        </div>
                        {{--submit button--}}
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-9">
                                {{ Form::submit(__('Update Password'),['class'=>'btn btn-info btn-sm btn-left btn-sm-block form-submission-button']) }}
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
            $('.validator').cValidate();
        });
    </script>
@endsection