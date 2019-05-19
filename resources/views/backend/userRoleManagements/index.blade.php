@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all text-center">{{ __('Role Name') }}</th>
                            <th class="min-phone-l text-center">{{ __('Created Date') }}</th>
                            <th class="min-phone-l text-center">{{ __('Status') }}</th>
                            <th class="text-center all no-sort">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $userRoleManagement)
                            <tr>
                                <td class="text-center">{{ $userRoleManagement->role_name }}</td>
                                <td class="text-center">{{ $userRoleManagement->created_at->toFormattedDateString() }}</td>
                                <td class="text-center">{!!   $userRoleManagement->is_active ? '<i class="fa fa-check text-green"></i>' :  '<i class="fa fa-close text-red"></i>' !!}</td>
                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle"
                                                data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="{{ route('user-role-managements.edit',$userRoleManagement->id) }}"><i
                                                            class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                            </li>
                                            @if(!in_array($userRoleManagement->id, $defaultRoles))
                                                <li>
                                                    <a class="confirmation" data-alert="{{__('Do you want to delete this role?')}}" data-form-id="ur-{{ $userRoleManagement->id }}" data-form-method='delete' href="{{ route('user-role-managements.destroy',$userRoleManagement->id) }}"><i class="fa fa-trash-o"></i> {{ __('Delete') }}</a>
                                                </li>
                                                @if($userRoleManagement->is_active == ACTIVE_STATUS_ACTIVE)
                                                        <li>
                                                            <a data-form-id="ur-{{ $userRoleManagement->id }}" data-form-method="PUT" href={{ route('user-role-managements.status',$userRoleManagement->id) }} class="confirmation" data-alert="{{__('Do you want to disable this role?')}}"><i class="fa  fa-times-circle-o"></i> {{ __('Disable') }}</a>
                                                        </li>
                                                @endif
                                            @endif
                                            @if($userRoleManagement->is_active == ACTIVE_STATUS_INACTIVE)
                                                <li>
                                                    <a data-form-id="ur-{{ $userRoleManagement->id }}" data-form-method="PUT" href={{ route('user-role-managements.status',$userRoleManagement->id) }} class="confirmation" data-alert="{{__('Do you want to active this role?')}}"><i class="fa fa-check-square-o"></i> {{ __('Active') }}</a>
                                                </li>
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