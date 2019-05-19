@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="col-sm-3 col-sm-offset-3">
                        <div class="description-block border-right">
                            <span class="description-text">{{ __('STOCK ITEM BUY FEES VOLUME') }}</span>
                            <h5 class="description-header">
                                {{ $stockPair->exchanged_buy_fee }} {{ $stockPair->stockItem->item }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="description-block border-right">
                            <span class="description-text">{{ __('BASE ITEM SALE FEES VOLUME') }}</span>
                            <h5 class="description-header">
                                {{ $stockPair->exchanged_sale_fee }} {{ $stockPair->baseItem->item }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!  __('Details of :stockPair', ['stockPair' => '<strong>' . $stockPair->stock_pair . '</strong>']) !!}</h3>
                </div>
                <div class="box-body">
                    <div class="form-horizontal show-form-data">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Stock Pair') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ $stockPair->stock_pair }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Exchangeable Item') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ $stockPair->stockItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Base Item') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Active Status') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ active_status($stockPair->is_active) }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Default Status') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ active_status($stockPair->is_default) }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Created Date') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ $stockPair->created_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    <div class="row">
                        <div class="col-md-6">
                            @if(has_permission('admin.stock-pairs.edit'))
                                <a href="{{ route('admin.stock-pairs.edit', $stockPair->id) }}"
                                   class="btn btn-sm btn-info btn-flat btn-sm-block">{{ __('Edit Stock Pair') }}</a>
                            @endif
                        </div>
                        <div class="col-md-6 text-right">
                            @if(has_permission('admin.stock-pairs.index'))
                                <a href="{{ route('admin.stock-pairs.index') }}"
                                   class="btn btn-primary btn-sm back-button btn-sm-block">{{ __('View all Stock Pair') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!  __('24 Hour Exchange of :stockPair', ['stockPair' => '<strong>' . $stockPair->stock_pair . '</strong>']) !!}</h3>
                </div>
                <div class="box-body">
                    <div class="form-horizontal show-form-data">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('Last Price') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ $stockPair->last_price }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('24hr Change') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ number_format($stockPair->change_24, 8)  }}%</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('24hr High') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ number_format($stockPair->high_24, 8) }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('24hr Low') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">{{ number_format($stockPair->low_24, 8) }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{ __('24hr Volume') }}</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    {{ number_format($stockPair->exchanged_stock_item_volume_24, 8) }} {{ $stockPair->stockItem->item }}
                                    / {{ number_format($stockPair->exchanged_base_item_volume_24, 8) }} {{ $stockPair->baseItem->item }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer clearfix" style="margin-top:51px;">
                    <div class="row">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6 text-right">
                            @if(has_permission('reports.admin.stock-pairs.trades'))
                                <a href="{{ route('reports.admin.stock-pairs.trades', ['id' => $stockPair->id]) }}" class="btn btn-sm btn-primary btn-flat btn-sm-block">{{ __('View Trade History') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!  __('Order Summery of :stockPair', ['stockPair' => '<strong>' . $stockPair->stock_pair . '</strong>']) !!}</h3>
                </div>
                <div class="box-body">
                    <div class="form-horizontal show-form-data">
                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Buy Order Volume', ['item' => $stockPair->stockItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->stock_item_buy_order_volume }} {{ $stockPair->stockItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Sell Order Volume', ['item' => $stockPair->stockItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->stock_item_sale_order_volume }} {{ $stockPair->stockItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Buy Order Volume', ['item' => $stockPair->baseItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->stock_item_buy_order_volume }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Sell Order Volume', ['item' => $stockPair->baseItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->stock_item_sale_order_volume }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @if(has_permission('reports.admin.stock-pairs.open-orders'))
                <div class="box-footer clearfix">
                    <a href="{{ route('reports.admin.stock-pairs.open-orders', ['id' => $stockPair->id]) }}" class="btn btn-sm btn-primary btn-flat btn-sm-block">{{ __('View Open Orders') }}</a>
                </div>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!  __('Trade Summery of :stockPair', ['stockPair' => '<strong>' . $stockPair->stock_pair . '</strong>']) !!}</h3>
                </div>
                <div class="box-body">
                    <div class="form-horizontal show-form-data">
                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Exchanged Buy Total', ['item' => $stockPair->baseItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->exchanged_buy_total }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Exchanged Sale Total', ['item' => $stockPair->baseItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->exchanged_sale_total }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Exchanged Maker Total', ['item' => $stockPair->baseItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->exchanged_maker_total }} {{ $stockPair->baseItem->item }}</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-6 control-label">{{ __(':item Exchanged amount', ['item' => $stockPair->stockItem->item]) }}</label>
                            <div class="col-sm-6">
                                <p class="form-control-static">{{ $stockPair->exchanged_amount }} {{ $stockPair->stockItem->item }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @if(has_permission('reports.admin.stock-pairs.trades'))
                <div class="box-footer clearfix">
                        <a href="{{ route('reports.admin.stock-pairs.trades', ['id' => $stockPair->id]) }}" class="btn btn-sm btn-primary btn-flat btn-sm-block">{{ __('View Trade History') }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection