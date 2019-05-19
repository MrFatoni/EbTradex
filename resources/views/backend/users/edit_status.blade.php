@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            @include('backend.profile.avatar', ['profileRouteInfo' => profileRoutes('admin', $user->id)])
        </div>
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!  __('Status Details of :user', ['user' => '<strong>' . $user->userInfo->full_name . '</strong>']) !!}</h3>
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm back-button">{{ __('Back') }}</a>
                </div>
                <div class="box-body">
                @if($user->id == Auth::user()->id)
                    {{__('You cannot change your own status.')}}
                @elseif(in_array($user->id, config('commonconfig.fixed_users')))
                    {{__("You cannot change primary user's status.")}}
                @else
                    {{ Form::model($user,['route'=>['users.update.status',$user->id],'class'=>'form-horizontal user-form','method'=>'put']) }}
                    @include('backend.users._edit_status_form')
                    {{ Form::close() }}
                @endif
                </div>
                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('users.show', $user->id) }}"
                               class="btn btn-sm btn-info btn-flat btn-sm-block">{{ __('View Information') }}</a>
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="btn btn-sm btn-warning btn-flat btn-sm-block">{{ __('Edit Information') }}</a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary btn-flat btn-sm-block">{{ __('View All Users') }}</a>
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
            $('.user-form').cValidate({});
        });
    </script>
@endsection