@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <h3 class="page-header">{{ __('My Trades') }}</h3>
    {!! $list['filters'] !!}
    <div class="row">
        <div class="col-lg-12">
            @include('frontend.reports._category_nav', ['routeName' => 'reports.trader.trades'])
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                        <thead>
                        <tr>
                            <th class="all">{{ __('Market') }}</th>
                            <th class="all">{{ __('Type') }}</th>
                            @if(!$categoryType )
                            <th class="min-desktop">{{ __('Category') }}</th>
                            @endif
                            <th class="all">{{ __('Price') }}</th>
                            <th class="min-desktop">{{ __('Amount') }}</th>
                            <th class="min-desktop">{{ __('Fee') }}</th>
                            <th class="min-desktop">{{ __('Total') }}</th>
                            <th class="min-desktop">{{ __('Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($list['query'] as $transaction)
                            <tr>
                                <td>{{ $transaction->stock_item_abbr }}/{{ $transaction->base_item_abbr }}</td>
                                <td>{{ exchange_type($transaction->exchange_type) }}</td>
                                @if(!$categoryType )
                                <td>{{ category_type($transaction->category) }}</td>
                                @endif
                                <td>{{ $transaction->price }} <span class="strong">{{ $transaction->base_item_abbr }}</span></td>
                                <td>{{ $transaction->amount }} <span class="strong">{{ $transaction->stock_item_abbr }}</span></td>
                                <td>
                                    {{ bcadd($transaction->fee,$transaction->referral_earning) }}
                                    <span class="strong">{{ $transaction->exchange_type == EXCHANGE_BUY ? $transaction->stock_item_abbr : $transaction->base_item_abbr }}</span>
                                    ({{ $transaction->is_maker == 1 ?
                                            number_format($transaction->maker_fee, 2) . '%' :
                                            number_format($transaction->taker_fee, 2) . '%' }})
                                </td>
                                <td>{{ $transaction->total }} <span class="strong">{{ $transaction->base_item_abbr }}</span></td>
                                <td>{{ $transaction->created_at->toFormattedDateString() }}</td>
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