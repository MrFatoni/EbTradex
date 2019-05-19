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
                            <th  class="min-phone-l">{{ __('ID Type') }}</th>
                            <th  class="min-phone-l">{{ __('Verification Status') }}</th>
                            <th class="text-center all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $user)
                            <tr>
                                <td>
                                    @if(has_permission('users.show'))
                                        <a href="{{ route('users.show', $user->id) }}">{{ $user->email }}</a>
                                    @else
                                        {{ $user->email }}
                                    @endif
                                </td>
                                <td>{{ $user->id_type ? id_type($user->id_type) : '-' }}</td>
                                <td class="text-center">
                                    <span class="label label-{{ config('commonconfig.id_status.' . $user->is_id_verified . '.color_class') }}">{{ id_status($user->is_id_verified) }}</span>
                                </td>
                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            @if(has_permission('admin.id-management.show'))
                                                <li><a href="{{ route('admin.id-management.show',$user->id)}}"><i class="fa fa-eye"></i> {{ __('Show') }}</a></li>
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