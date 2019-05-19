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
                        {{ Form::open(['route'=>['profile.setting'],'class'=>'form-horizontal edit-profile-setting-form','method'=>'put']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">
                        {{--first name--}}
                        <div class="form-group {{ $errors->has('language') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('language') }}"
                                   class="col-md-3 control-label required">{{ __('Language') }}</label>
                            <div class="col-md-9">
                                {{ Form::select(fake_field('language'), language(), old('language', $user->userSetting->language), ['class'=>'form-control', 'id' => fake_field('language'),'data-cval-name' => 'The language field','data-cval-rules' => 'required|escapeInput']) }}
                                <span class="validation-message cval-error"
                                      data-cval-error="{{ fake_field('language') }}">{{ $errors->first('language') }}</span>
                            </div>
                        </div>
                        {{--last name--}}
                        <div class="form-group {{ $errors->has('timezone') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('timezone') }}"
                                   class="col-md-3 control-label required">{{ __('Timezone') }}</label>
                            <div class="col-md-9">
                                {{ Form::select(fake_field('timezone'), get_available_timezones(), old('timezone', $user->userSetting->timezone), ['class'=>'form-control', 'id' => fake_field('timezone'),'data-cval-name' => 'The timezone field','data-cval-rules' => 'required|escapeInput']) }}
                                <span class="validation-message cval-error"
                                      data-cval-error="{{ fake_field('timezone') }}">{{ $errors->first('timezone') }}</span>
                            </div>
                        </div>

                        {{--submit button--}}
                        <div class="form-group">
                            <div class="col-md-offset-3 col-md-9">
                                {{ Form::submit(__('Update Setting'),['class'=>'btn btn-info btn-sm btn-left btn-sm-block']) }}
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
            $('.edit-profile-setting-form').cValidate({});
        });
    </script>
@endsection