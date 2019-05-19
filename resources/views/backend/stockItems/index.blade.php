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
                            <th class="text-center">{{ __('Emoji') }}</th>
                            <th class="all text-center">{{ __('Stock Item') }}</th>
                            <th class="text-center">{{ __('Stock Item Name') }}</th>
                            <th class="text-center">{{ __('Stock Item Type') }}</th>
                            <th class="text-center">{{ __('Active Status') }}</th>
                            <th class="text-center">{{ __('Created Date') }}</th>
                            <th class="text-center all no-sort">{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $stockItem)
                            <tr>
                                <td class="text-center">
                                    @if(!is_null(get_item_emoji($stockItem->item_emoji)))
                                        <img src="{{ get_item_emoji($stockItem->item_emoji) }}" alt="Item Emoji" class="img-sm cm-center">
                                    @else
                                        <i class="fa fa-money fa-lg text-green"></i>
                                    @endif
                                </td>
                                <td class="text-center">{{ $stockItem->item }}</td>
                                <td class="text-center">{{ $stockItem->item_name }}</td>
                                <td class="text-center">{{ stock_item_types($stockItem->item_type) }}</td>
                                <td class="text-center">{!!   $stockItem->is_active ? '<i class="fa fa-check text-green"></i>' :  '<i class="fa fa-close text-red"></i>' !!}</td>
                                <td class="text-center">{{ $stockItem->created_at->toFormattedDateString() }}</td>

                                <td class="cm-action">
                                    <div class="btn-group pull-right">
                                        <button class="btn green btn-xs btn-outline dropdown-toggle"
                                                data-toggle="dropdown">
                                            <i class="fa fa-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-right">
                                            @if(has_permission('admin.stock-items.show'))
                                                <li>
                                                    <a href="{{ route('admin.stock-items.show', $stockItem->id) }}"><i
                                                                class="fa fa-eye"></i> {{ __('Show') }}</a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-items.edit'))
                                                <li>
                                                    <a href="{{ route('admin.stock-items.edit', $stockItem->id) }}"><i
                                                                class="fa fa-pencil"></i> {{ __('Edit') }}</a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-items.toggle-status'))
                                                <li>
                                                    <a data-form-id="update-{{ $stockItem->id }}" data-form-method="PUT"
                                                       href="{{ route('admin.stock-items.toggle-status', $stockItem->id) }}" class="confirmation"
                                                       data-alert="{{__('Do you want to change this stock item\'s status?')}}"><i
                                                                class="fa fa-edit"></i> {{ __('Change Status') }}</a>
                                                </li>
                                            @endif

                                            @if(has_permission('admin.stock-items.destroy'))
                                                <li>
                                                    <a data-form-id="delete-{{ $stockItem->id }}" data-form-method="DELETE"
                                                       href="{{ route('admin.stock-items.destroy', $stockItem->id) }}" class="confirmation"
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