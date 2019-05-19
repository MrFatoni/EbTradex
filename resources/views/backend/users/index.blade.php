@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    {!!  $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Email') }}</th>
                            <th  class="min-phone-l">{{ __('First Name') }}</th>
                            <th  class="min-phone-l">{{ __('Last Name') }}</th>
                            <th class="min-phone-l">{{ __('User Group') }}</th>
                            <th class="min-phone-l">{{ __('Username') }}</th>
                            <th class="none">{{ __('Registered Date') }}</th>
                            <th class="text-center min-phone-l">{{ __('Status') }}</th>
                            <th class="text-center all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $key=>$user)
                            <tr>
                                <td>
                                    @if(has_permission('users.show'))
                                        <a href="{{ route('users.show', $user->id) }}">{{ $user->email }}</a>
                                    @else
                                        {{ $user->email }}
                                    @endif
                                </td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->role_name}}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">{!! $user->is_active ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' !!}</td>
                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            @if(has_permission('users.edit'))
                                                <li><a href="{{ route('users.show',$user->id)}}"><i class="fa fa-eye"></i> {{ __('Show') }}</a></li>
                                                <li><a href="{{ route('users.edit',$user->id)}}"><i
                                                            class="fa fa-pencil-square-o fa-lg text-info"></i> {{ __('Edit Info') }}</a></li>
                                                <li><a href="{{ route('users.edit.status',$user->id)}}"><i
                                                            class="fa fa-pencil-square fa-lg text-info"></i> {{ __('Edit Status') }}</a></li>
                                            @endif

                                            @if(has_permission('admin.users.wallets'))
                                                <li><a href="{{ route('admin.users.wallets',$user->id)}}"><i class="fa fa-list fa-lg text-info"></i> {{ __('View Wallets') }}</a></li>
                                            @endif

                                            @if(has_permission('reports.admin.open-orders'))
                                                <li><a href="{{ route('reports.admin.open-orders', $user->id)}}"><i class="fa fa-list fa-lg text-info"></i> {{ __('View Open Orders') }}</a></li>
                                            @endif

                                            @if(has_permission('reports.admin.trades'))
                                                <li><a href="{{ route('reports.admin.trades', $user->id)}}"><i class="fa fa-list fa-lg text-info"></i> {{ __('View trade history') }}</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {!! $list['pagination'] !!}
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{ asset('common/vendors/datepicker/datepicker.js') }}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/table-datatables-responsive.js')}}"></script>
    <script type="text/javascript">
        //Init jquery Date Picker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom',
            todayHighlight: true,
        });
    </script>
@endsection