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
                            <th class="all">{{ __('Notice') }}</th>
                            <th class="min-phone-l">{{ __('Date') }}</th>
                            <th class="min-phone-l">{{ __('Status') }}</th>
                            <th class="all no-sort">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $key=>$notice)
                            <tr>
                                <td {{ $notice->read_at ? '' : 'class=text-bold' }}>{{$notice->data}}</td>
                                <td {{ $notice->read_at ? '' : 'class=text-bold' }}>{{$notice->created_at}}</td>
                                <td {{ $notice->read_at ? '' : 'class=text-bold' }}>{{ $notice->read_at ? __('Read') : __('Unread') }}</td>
                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            <li>
                                                @if($notice->read_at)
                                                    <a href="{{ route('notices.mark-as-unread',$notice->id) }}"><i class="fa fa-dot-circle-o text-red"></i> {{ __('Mark as unread') }}</a>
                                                    @else
                                                    <a href="{{ route('notices.mark-as-read',$notice->id) }}"><i class="fa fa-dot-circle-o text-green"></i> {{ __('Mark as read') }}</a>
                                                    @endif

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