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
                                <th class="all text-center">{{ __('Wallet') }}</th>
                                <th class="text-center">{{ __('Wallet Name') }}</th>
                                <th class="text-center">{{ __('Total Balance') }}</th>
                                <th class="text-center">{{ __('On Order') }}</th>
                                <th class="text-center all no-sort">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($list['query'] as $wallet)
                                <tr>
                                    <td class="text-center">{{ $wallet->item }}</td>
                                    <td class="text-center">{{ $wallet->item_name }}</td>
                                    <td class="text-center">{{ $wallet->primary_balance }}</td>
                                    <td class="text-center">{{ $wallet->on_order_balance }}</td>
                                    <td class="cm-action">
                                        @if( in_array($wallet->item_type, config('commonconfig.currency_transferable')) )
                                            <div class="btn-group pull-right">
                                                <button class="btn green btn-xs btn-outline dropdown-toggle"
                                                        data-toggle="dropdown">
                                                    <i class="fa fa-gear"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right">
                                                    @if( has_permission('trader.wallets.deposit'))
                                                        <li>
                                                            <a href="{{ route('trader.wallets.deposit', $wallet->id) }}"><i class="fa fa-magic"></i> {{ __('Deposit') }}</a>
                                                        </li>
                                                    @endif

                                                    @if( has_permission('reports.trader.deposits'))
                                                        <li>
                                                            <a href="{{ route('reports.trader.deposits', $wallet->id) }}"><i class="fa fa-magic"></i> {{ __('Deposit History') }}</a>
                                                        </li>
                                                    @endif

                                                    @if( has_permission('trader.wallets.withdrawal') )
                                                        <li>
                                                            <a href="{{ route('trader.wallets.withdrawal', $wallet->id) }}"><i class="fa fa-external-link"></i> {{ __('Withdrawal') }}</a>
                                                        </li>
                                                    @endif

                                                    @if( has_permission('reports.trader.withdrawals'))
                                                        <li>
                                                            <a href="{{ route('reports.trader.withdrawals', $wallet->id) }}"><i class="fa fa-magic"></i> {{ __('Withdrawal History') }}</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
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
        $(document).ready(function () {
            //Init jquery Date Picker
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                orientation: 'bottom',
                todayHighlight: true
            });
        });
    </script>
@endsection