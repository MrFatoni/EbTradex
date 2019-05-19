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
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                <tr>
                                    <th>{{ __('Language') }}</th>
                                    <td>{{ $user->userSetting->language }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Timezone') }}</th>
                                    <td>{{ $user->userSetting->timezone }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer clearfix">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('profile.setting.edit') }}"
                                   class="btn btn-sm btn-info btn-flat btn-sm-block">{{ __('Edit Setting') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection