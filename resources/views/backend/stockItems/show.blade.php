@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{!!  __('Details of :stockItem', ['stockItem' => '<strong>' . $stockItem->item . '</strong>']) !!}</h3>
                    @if(has_permission('admin.stock-items.index'))
                        <a href="{{ route('admin.stock-items.index') }}" class="btn btn-primary btn-sm back-button">{{ __('Back to list') }}</a>
                    @endif
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-horizontal show-form-data">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Stock Item') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->item }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Stock Item Name') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->item_name }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Stock Item Emoji') }}</label>
                                    <div class="col-sm-8">
                                        @if(get_item_emoji($stockItem->item_emoji))
                                            <img src="{{ get_item_emoji($stockItem->item_emoji) }}" alt="{{ __('Emoji') }}" class="img-responsive img-sm">
                                        @else
                                            <i class="fa fa-money text-green fa-2x"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Stock Item Type') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ stock_item_types($stockItem->item_type) }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Active Status') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ active_status($stockItem->is_active) }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Is ICO') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ active_status($stockItem->is_ico) }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Exchange Status') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ active_status($stockItem->exchange_status) }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Daily Withdrawal Limit') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->daily_withdrawal_limit }} {{ $stockItem->item }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Minimum Withdrawal Amount') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->minimum_withdrawal_amount }} {{ $stockItem->item }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Created At') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->created_at->toFormattedDateString() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-horizontal show-form-data">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Deposit Status') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ active_status($stockItem->deposit_status) }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Deposit Fee') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->deposit_fee }}%</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Withdrawal Status') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ active_status($stockItem->withdrawal_status) }}</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('Withdrawal Fee') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">{{ $stockItem->withdrawal_fee }}%</p>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">{{ __('API Service') }}</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            {{ array_key_exists($stockItem->api_service, api_services()) ? api_services($stockItem->api_service) : '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    <div class="row">
                        <div class="col-md-6">
                            @if(has_permission('admin.stock-items.edit'))
                                <a href="{{ route('admin.stock-items.edit', $stockItem->id) }}"
                                   class="btn btn-sm btn-info btn-flat btn-sm-block">{{ __('Edit Stock Item') }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(in_array($stockItem->item_type, config('commonconfig.currency_transferable')))
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{!!  __('Transaction report of :stockItem', ['stockItem' => '<strong>' . $stockItem->item . '</strong>']) !!}</h3>
                    </div>
                    <div class="box-body">
                        <div class="form-horizontal show-form-data">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Total Deposit') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $stockItem->total_deposit }} {{ $stockItem->item }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Total Deposit Fee') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $stockItem->total_deposit_fee }} {{ $stockItem->item }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Total Withdrawal') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $stockItem->total_withdrawal }} {{ $stockItem->item }}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">{{ __('Total Withdrawal Fee') }}</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">{{ $stockItem->total_withdrawal_fee }} {{ $stockItem->item }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer clearfix">
                        <a href="{{ route('admin.stock-items.index') }}"
                           class="btn btn-sm btn-primary btn-flat btn-sm-block">{{ __('View All Stock Items') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection