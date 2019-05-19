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
                            <th class="all">{{ __('Title') }}</th>
                            <th class="min-phone-l">{{ __('Start Time') }}</th>
                            <th class="min-phone-l">{{ __('End Time') }}</th>
                            <th class="min-phone-l">{{ __('Type') }}</th>
                            <th class="min-phone-l">{{ __('Status') }}</th>
                            <th class="none">{{ __('Description') }}</th>
                            <th class="all no-sort">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $notice)
                            <tr>
                                <td>{{$notice->title}}</td>
                                <td>{{$notice->start_at}}</td>
                                <td>{{$notice->end_at}}</td>
                                <td><span class="label label-{{ $notice->type }}">{{ ucfirst($notice->type) }}</span></td>
                                <td>{{ active_status($notice->status) }}</td>
                                <td>{{ $notice->description }}</td>
                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle"
                                                data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                <a href="{{ route('system-notices.edit',$notice->id) }}"><i
                                                            class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                            </li>
                                            <li>
                                                <a class="confirmation" data-alert="{{__('Are you sure?')}}" data-form-id="urm-{{$notice->id}}" data-form-method='delete'  href="{{ route('system-notices.destroy',$notice->id) }}"><i class="fa fa-trash-o"></i> {{ __('Delete') }}</a>
                                            </li>
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