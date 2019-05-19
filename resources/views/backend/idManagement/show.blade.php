@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-sm-12 col-md-4">
            <div class="box box-widget widget-user-2">
                <div class="widget-user-header bg-green">
                    <div class="widget-user-image">
                        <img src="{{ get_avatar($user->user->avatar) }}" alt="{{ __('Avatar') }}" class="img-responsive cm-center">
                    </div>
                    <h3 class="widget-user-username">{{ $user->full_name }}</h3>
                    <h5 class="widget-user-desc"><span class="label label-info">{{ $user->user->userRoleManagement->role_name }}</span></h5>
                </div>
                <div class="box-footer no-padding">
                    <div class="user-info">
                        <div class="form-horizontal show-form-data">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Email') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $user->user->email }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Address') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $user->address }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Country') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $user->country_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(has_permission('users.show'))
                    <hr class="maginless">
                    <ul class="nav nav-stacked">
                        <li><a href="{{ route('users.show', $user->user_id) }}" target="_blank" class="text-center">{{ __('View Detail') }}</a></li>
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-8">
            <div class="row">
                <div class="col-sm-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">{!!  __('ID Verification Request') !!}</h3>
                            @if(has_permission('admin.stock-items.index'))
                                <a href="{{ route('admin.id-management.index') }}" class="btn btn-primary btn-sm back-button">{{ __('Back to list') }}</a>
                            @endif
                        </div>
                        <div class="box-body">
                            <div class="form-horizontal show-form-data">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('ID Type') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $user->id_type ? id_type($user->id_type) : '-' }}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('ID Status') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <span class="label label-{{ config('commonconfig.id_status.' . $user->is_id_verified . '.color_class') }}">{{ id_status($user->is_id_verified) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div>
                                @include('backend.idManagement._show')
                            </div>
                        </div>
                        @if($user->is_id_verified == ID_STATUS_PENDING)
                        <div class="box-footer clearfix">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    @if(has_permission('admin.id-management.approve'))
                                        <a data-form-id="approve-{{ $user->user_id }}" data-form-method="PUT"
                                           href="{{ route('admin.id-management.approve', $user->user_id) }}" class="confirmation btn btn-sm btn-success btn-flat btn-sm-block" data-alert="{{__('Do you want to approve this ID?')}}">
                                            {{ __('Approve') }}
                                        </a>
                                    @endif
                                    @if(has_permission('admin.id-management.decline'))
                                        <a data-form-id="decline-{{ $user->user_id }}" data-form-method="PUT" href="{{ route('admin.id-management.decline', $user->user_id) }}"
                                           class="confirmation btn btn-sm btn-danger btn-flat btn-sm-block" data-alert="{{__('Do you want to decline this ID?')}}">
                                            {{ __('Decline') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after-style')
    <style>
        .user-info {
            padding: 15px;
        }
        .maginless{
            margin: 0;
        }
    </style>
@endsection

@section('script')
    <script>
        new Vue({
            el: "#app"
        });
    </script>
@endsection