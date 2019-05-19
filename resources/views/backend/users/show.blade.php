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
                    <h3 class="box-title">{!!  __('Basic Details of :user', ['user' => '<strong>' . $user->userInfo->full_name . '</strong>']) !!}</h3>
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm back-button">{{ __('Back') }}</a>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <td>{{ $user->userInfo->full_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('User Role') }}</th>
                                <td>{{ $user->userRoleManagement->role_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Email') }}</th>
                                <td>{{ $user->email }} <span
                                            class="label label-{{ config('commonconfig.email_status.' . $user->is_email_verified . '.color_class') }}">{{ email_status($user->is_email_verified) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Username') }}</th>
                                <td>{{ $user->username }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Address') }}</th>
                                <td>{{ $user->userInfo->address }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Account Status') }}</th>
                                <td>
                                    <span class="label label-{{ config('commonconfig.account_status.' . $user->is_active . '.color_class') }}">{{ account_status($user->is_active) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Financial Status') }}</th>
                                <td>
                                    <span class="label label-{{ config('commonconfig.financial_status.' . $user->is_financial_active . '.color_class') }}">{{ financial_status($user->is_financial_active) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ __('Maintenance Access Status') }}</th>
                                <td>
                                    <span class="label label-{{ config('commonconfig.maintenance_accessible_status.' . $user->is_accessible_under_maintenance . '.color_class') }}">{{ maintenance_accessible_status($user->is_accessible_under_maintenance) }}</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer clearfix">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('users.edit', $user->id) }}"
                               class="btn btn-sm btn-info btn-flat btn-sm-block">{{ __('Edit Information') }}</a>
                            <a href="{{ route('users.edit.status', $user->id) }}"
                               class="btn btn-sm btn-warning btn-flat btn-sm-block">{{ __('Edit Status') }}</a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('users.index') }}"
                               class="btn btn-sm btn-primary btn-flat btn-sm-block">{{ __('View All Users') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection