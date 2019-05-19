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
                        {{ Form::open(['route'=>['profile.avatar.update'],'class'=>'validator','method'=>'put', 'enctype'=>'multipart/form-data']) }}
                        <input type="hidden" value="{{base_key()}}" name="base_key">
                        {{--password--}}
                        <div class="form-group {{ $errors->has('avatar') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('avatar') }}"
                                   class="control-label required">{{ __('Upload new avatar') }}</label>
                            {{ Form::file(fake_field('avatar'), ['id' => fake_field('avatar'), 'data-cval-name' => 'The avatar','data-cval-rules' => 'required|files:jpg,png,jpeg|max:2048']) }}
                            <p class="help-block">{{ __('Upload avatar 300x300 and less than or equal 2MB.') }}</p>
                            <span class="validation-message cval-error"
                                  data-cval-error="{{ fake_field('avatar') }}">{{ $errors->first('avatar') }}</span>
                        </div>

                        {{--submit button--}}
                        {{ Form::submit(__('Upload Avatar'), ['class'=>'btn btn-info btn-sm btn-left btn-sm-block form-submission-button']) }}
                        {{ Form::close() }}
                    </div>
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
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate();
        });
    </script>
@endsection