@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-thermometer"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('CPU Traffic') }}</span>
                    <span class="info-box-number">{{ $cpuUsages }}<small>%</small></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-snowflake-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('TOTAL STOCK PAIR') }}</span>
                    <span class="info-box-number">{{ $stockPairs->count() }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix visible-sm-block"></div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-empire"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('TOTAL STOCK ITEM') }}</span>
                    <span class="info-box-number">{{ $totalStockItem }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-user-o"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('TOTAL MEMBER') }}</span>
                    <span class="info-box-number">{{ $totalUser }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    <div class="row dc-clear">
        @foreach($stockPairs as $stockPair)

            <div class="col-md-6">
                <div class="box box-borderless">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $stockPair->stock_item_abbr }}/{{ $stockPair->base_item_abbr }}</h3>
                    </div>
                    <div class="box-body" style="padding: 0 15px">
                        <div class="row">
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block border-right">
                                    <span class="small text-bold">{{ $stockPair->last_price }}</span><br>
                                    <span class="small">{{ __('Last Price') }}</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block border-right">
                                    <span class="small text-bold">{{ $stockPair->high_24 }}</span><br>
                                    <span class="small">{{ __('24 High') }}</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block border-right">
                                    <span class="small text-bold">{{ $stockPair->low_24 }}</span><br>
                                    <span class="small">{{ __('24 Low') }}</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-3 col-xs-6">
                                <div class="description-block">
                                    @if($stockPair->change_24 > 0)
                                        <span class="text-green text-bold"><i class="fa fa-caret-up"></i> {{ $stockPair->change_24 }}%</span>
                                    @elseif($stockPair->change_24 < 0)
                                        <span class="text-red text-bold"><i class="fa fa-caret-down"></i> {{ $stockPair->change_24 }}%</span>
                                    @else
                                        <span class="text-bold text-body"><i class="fa fa-sort"></i> {{ $stockPair->change_24 }}%</span>
                                    @endif
                                    <br>
                                    <span class="small">{{ __('24 Change') }}</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                        </div>
                    </div>
                    <div class="box-footer no-border">
                        <table class="table table-striped table-responsive small">
                            <tr>
                                <th>{{ __('On buy order base item volume') }}</th>
                                <td>{{ $stockPair->base_item_buy_order_volume }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('On buy order stock item volume') }}</th>
                                <td>{{ $stockPair->stock_item_buy_order_volume }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('On sell order base item volume') }}</th>
                                <td>{{ $stockPair->base_item_sale_order_volume }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('On sell order stock item volume') }}</th>
                                <td>{{ $stockPair->stock_item_sale_order_volume }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Total Buy Exchanged') }}</th>
                                <td>{{ $stockPair->exchanged_buy_total }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Total Sell Exchanged') }}</th>
                                <td>{{ $stockPair->exchanged_sale_total }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Total Amount Exchanged') }}</th>
                                <td>{{ $stockPair->exchanged_amount }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Total Market Exchanged') }}</th>
                                <td>{{ $stockPair->exchanged_maker_total }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Total Buy Fee') }}</th>
                                <td>{{ $stockPair->exchanged_buy_fee }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Total Sell Fee') }}</th>
                                <td>{{ $stockPair->exchanged_sale_fee }}</td>
                            </tr>
                        </table>

                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
        @endforeach
    </div>
@endsection
@section('after-style')
    <style>
        .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {
            padding: 4px 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
    </style>
@endsection
