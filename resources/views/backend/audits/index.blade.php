@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ __('List of Audit') }}</h3>
                </div>
                <div class="box-body">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Event') }}</th>
                            <th class="all">{{ __('Model') }}</th>
                            <th class="none">{{ __('User') }}</th>
                            <th class="none">{{ __('Old Data') }}</th>
                            <th class="none">{{ __('New Data') }}</th>
                            <th class="min-desktop">{{ __('Created Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $audit)
                            <tr>
                                <td>{{ title_case($audit->event) }}</td>
                                <td>{{ $audit->auditable_type }}</td>
                                <td>{{ $audit->full_name }}</td>
                                <td>{{ json_encode($audit->old_values) }}</td>
                                <td>{{ json_encode($audit->new_values) }}</td>
                                <td>{{ $audit->created_at }}</td>
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