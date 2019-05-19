@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width: 100% !important;">
                        <thead>
                        <tr>
                            <th class="all text-center">{{ __('Stock Pair') }}</th>
                            <th class="text-center">{{ __('Exchangeable Item') }}</th>
                            <th class="text-center">{{ __('Base Item') }}</th>
                            <th class="text-center">{{ __('Last Price') }}</th>
                            <th class="text-center">{{ __('Active Status') }}</th>
                            <th class="text-center">{{ __('Default Status') }}</th>
                            <th class="text-center">{{ __('Created Date') }}</th>
                            <th class="text-center all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $stockPair)
                            <tr>
                                <td class="text-center">{{ $stockPair->stock_item }}/{{ $stockPair->base_stock_item }}</td>
                                <td class="text-center">{{ $stockPair->stock_item }} ({{ $stockPair->stock_name }})</td>
                                <td class="text-center">{{ $stockPair->base_stock_item }} ({{ $stockPair->base_stock_name }})</td>
                                <td class="text-center">{{ $stockPair->last_price }}</td>
                                <td class="text-center">
                                    {!! $stockPair->is_active == ACTIVE_STATUS_ACTIVE ? '<i class="fa fa-check text-green"></i>' : '<i class="fa fa-close text-red"></i>' !!}
                                </td>
                                <td class="text-center">
                                    {!! $stockPair->is_default == ACTIVE_STATUS_ACTIVE ? '<i class="fa fa-check text-green"></i>' : '<i class="fa fa-close text-red"></i>' !!}
                                </td>
                                <td class="text-center">{{ $stockPair->created_at->toFormattedDateString() }}</td>
                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            @if(has_permission('admin.stock-pairs.show'))
                                                <li>
                                                    <a href="{{ route('admin.stock-pairs.show', $stockPair->id) }}"><i
                                                                class="fa fa-eye"></i> {{ __('Show') }}</a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-pairs.edit'))
                                                <li>
                                                    <a href="{{ route('admin.stock-pairs.edit', $stockPair->id) }}"><i
                                                                class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                                </li>
                                            @endif

                                            @if(
                                                has_permission('admin.stock-pairs.toggle-status') &&
                                                $stockPair->is_default != ACTIVE_STATUS_ACTIVE
                                            )
                                                <li>
                                                    <a data-form-id="update-{{ $stockPair->id }}" data-form-method="PUT"
                                                       href={{ route('admin.stock-pairs.toggle-status', $stockPair->id) }} class="confirmation"
                                                       data-alert="{{__("Do you want to change this stock pair's status?")}}"><i
                                                                class="fa fa-edit"></i> {{ __('Change Status') }}</a>
                                                </li>
                                            @endif

                                            @if(
                                                has_permission('admin.stock-pairs.make-status-default') &&
                                                $stockPair->is_default != ACTIVE_STATUS_ACTIVE &&
                                                $stockPair->is_active == ACTIVE_STATUS_ACTIVE
                                            )
                                                <li>
                                                    <a data-form-id="update-default-{{ $stockPair->id }}" data-form-method="PUT"
                                                       href={{ route('admin.stock-pairs.make-status-default', $stockPair->id) }} class="confirmation"
                                                       data-alert="{{__("Do you want to make this stock pair  default?")}}">
                                                        <i class="fa fa-edit"></i> {{ __('Make Default Pair') }}
                                                    </a>
                                                </li>
                                            @endif

                                            @if(
                                                has_permission('admin.stock-pairs.destroy') &&
                                                $stockPair->is_default != ACTIVE_STATUS_ACTIVE
                                            )
                                                <li>
                                                    <a data-form-id="delete-{{ $stockPair->id }}" data-form-method="DELETE"
                                                       href={{ route('admin.stock-pairs.destroy', $stockPair->id) }} class="confirmation"
                                                       data-alert="{{__('Do you want to delete this stock item?')}}"><i
                                                                class="fa fa-trash-o"></i> {{ __('Delete') }}</a>
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