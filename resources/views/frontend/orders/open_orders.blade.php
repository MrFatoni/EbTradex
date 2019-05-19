@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <h3 class="page-header">{{ __('Open Orders') }}</h3>
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Market') }}</th>
                            <th class="min-desktop">{{ __('Type') }}</th>
                            <th class="min-desktop">{{ __('Category') }}</th>
                            <th class="all">{{ __('Price') }}</th>
                            <th class="min-desktop">{{ __('Amount') }}</th>
                            <th class="min-desktop">{{ __('Total') }}</th>
                            <th class="min-desktop">{{ __('Stop/Rate') }}</th>
                            <th class="min-desktop">{{ __('Date') }}</th>
                            @if(has_permission('trader.orders.destroy'))
                                <th class="all">{{ __('Action') }}</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $order)
                            <tr id="order-{{ $order->id }}">
                                <td>{{ $order->stock_item_abbr }}/{{ $order->base_item_abbr }}</td>
                                <td>{{ exchange_type($order->exchange_type) }}</td>
                                <td>{{ category_type($order->category) }}</td>
                                <td>{{ $order->price }} <span class="strong">{{ $order->base_item_abbr }}</span></td>
                                <td>{{ $order->amount }} <span class="strong">{{ $order->stock_item_abbr }}</span></td>
                                <td>{{ bcmul($order->amount, $order->price) }} <span
                                            class="strong">{{ $order->base_item_abbr }}</span></td>
                                <td>
                                    @if(!is_null($order->stop_limit))
                                        {{ $order->stop_limit }}
                                        <span class="strong">{{ $order->base_item_abbr }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $order->created_at->toFormattedDateString() }}</td>
                                @if(has_permission('trader.orders.destroy'))
                                    <td>
                                        <a href="{{ route('trader.orders.destroy', $order->id) }}"
                                           class="cancel-order">{{ __('Cancel') }}</a>
                                    </td>
                                @endif
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
        @if(has_permission('trader.orders.destroy'))
        $(document).on('click', '.cancel-order', function (event) {
            event.preventDefault();
            let token = $('meta[name="csrf-token"]').attr('content');
            let $this = $(this);
            let url = $this.attr('href');
            let column = $this.closest('td');
            column.html('<span class="text-red">{{ __('Cancelling') }}</span>');

            $.ajax({
                type: 'POST',
                url: url,
                data: {_token: token, _method: 'DELETE'},
                success: function (data) {
                    let message = data.success || data.dismiss || data.error;
                    if (data.dismiss || data.error) {
                        flashBox('error', message);
                        column.html($this);
                    } else {
                        flashBox('success', message);
                    }

                }
            });
        });

        let userId = '{{ auth()->id() }}';
        let channelPrefix = '{{ channel_prefix() }}';
        let stockPairId = null;
        @foreach($list['query']->unique('stock_pair_id') as $stockOrder)
        stockPairId = '{{ $stockOrder->stock_pair_id }}';
        Echo.private(channelPrefix + 'orders.' + stockPairId + '.' + userId).listen('Exchange.BroadcastPrivateCancelOrder', (data) => {
            $('.datatable').DataTable().row("#order-" + data.order_number).remove().draw();
        });
        @endforeach

        @endif

    </script>
@endsection