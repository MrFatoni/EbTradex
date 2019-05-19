@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary box-borderless">
                @if($wallet->stockItem->withdrawal_status == ACTIVE_STATUS_ACTIVE)
                    <div class="box-header text-center with-border">
                        <h3 class="box-title font-weight-bold">
                            {{ __('Withdraw :stockItem', ['stockItem' => $wallet->stockItem->item]) }}
                        </h3>
                    </div>
                    <div class="box-body">
                        <h5 class="text-center">{!! __('You have :balance :stockItem available for withdrawal. :onOrder :stockItem is held on orders.', ['balance' => '<span class="strong">' . $wallet->primary_balance . '</span>', 'onOrder' => '<span class="strong">' . $wallet->on_order_balance . '</span>', 'stockItem' => '<span class="strong">' .$wallet->stockItem->item . '</span>' ]) !!}</h5>

                        {!! Form::open(['route'=>['frontend.wallets.withdrawal.store', $wallet->id], 'method' => 'post', 'class'=>'form-horizontal validator']) !!}
                            {{ Form::hidden('base_key', base_key()) }}
                            {{ Form::hidden('stock_item_type', $wallet->stockItem->item_type) }}
                            {{--stock_item_id--}}
                            <div class="form-group {{ $errors->has('stock_item_id') ? 'has-error' : '' }}">
                                <label class="col-md-4 control-label required">{{ __('Currency') }}</label>
                                <div class="col-md-8">
                                    <p class="form-control-static strong">{{ $wallet->stockItem->stock_item_name }}</p>
                                </div>
                            </div>

                            @if($wallet->stockItem->item_type == CURRENCY_CRYPTO)
                                {{--address--}}
                                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                                    <label class="col-md-4 control-label required">{{ __('Address') }}</label>
                                    <div class="col-md-8">
                                        {{ Form::text(fake_field('address'),  old('address', null), ['class'=>'form-control', 'id' => fake_field('address'),'data-cval-name' => 'The address field','data-cval-rules' => 'required', 'placeholder' => __('ex: 1Gx9FCknxSsLfFDzFdn75Xgqx95sDp38ir')]) }}
                                        <span class="validation-message cval-error"
                                              data-cval-error="{{ fake_field('address') }}">{{ $errors->first('address') }}</span>
                                    </div>
                                </div>

                                {{--amount--}}
                                <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                                    <label for="{{ fake_field('amount') }}" class="col-md-4 control-label required">{{ __('Amount') }}</label>
                                    <div class="col-md-8">
                                        {{ Form::text(fake_field('amount'),  old('amount', null), ['class'=>'form-control', 'id' => fake_field('amount'),'data-cval-name' => 'The amount field','data-cval-rules' => 'required|numeric|escapeInput|between:0.00000001, 99999999999.99999999', 'placeholder' => __('ex: 0.100000000')]) }}
                                        <span class="validation-message cval-error"
                                              data-cval-error="{{ fake_field('amount') }}">{{ $errors->first('amount') }}</span>
                                    </div>
                                </div>
                            @elseif($wallet->stockItem->item_type == CURRENCY_REAL)
                                {{--payment method--}}
                                <div class="form-group {{ $errors->has('payment_method') ? 'has-error' : '' }}">
                                    <label for="{{ fake_field('payment_method') }}" class="col-md-4 control-label required">{{ __('Payment Method') }}</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static strong">
                                            {{ api_services($wallet->stockItem->api_service) }}
                                        </p>
                                    </div>
                                </div>

                                {{--address--}}
                                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                                    <label class="col-md-4 control-label required">{{ __('Email Address') }}</label>
                                    <div class="col-md-8">
                                        {{ Form::text(fake_field('address'),  old('address', null), ['class'=>'form-control', 'id' => fake_field('address'),'data-cval-name' => 'The address field','data-cval-rules' => 'required|email', 'placeholder' => __('ex: Email Address')]) }}
                                        <span class="validation-message cval-error"
                                              data-cval-error="{{ fake_field('address') }}">{{ $errors->first('address') }}</span>
                                    </div>
                                </div>

                                {{--amount--}}
                                <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                                    <label for="{{ fake_field('amount') }}" class="col-md-4 control-label required">{{ __('Amount') }}</label>
                                    <div class="col-md-8">
                                        {{ Form::text(fake_field('amount'),  old('amount', null), ['class'=>'form-control', 'id' => fake_field('amount'),'data-cval-name' => 'The amount field','data-cval-rules' => 'required|numeric|escapeInput|between:0.01, 99999999999.99', 'placeholder' => __('ex: 25.99')]) }}
                                        <span class="validation-message cval-error"
                                              data-cval-error="{{ fake_field('amount') }}">{{ $errors->first('amount') }}</span>
                                    </div>
                                </div>
                            @endif

                            {{--accept_policy--}}
                            <div class="form-group {{ $errors->has('accept_policy') ? 'has-error' : '' }}">
                                <label class="col-md-4 control-label"></label>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('accept_policy', 1, false, ['data-cval-name' => 'The deposit policy checking','data-cval-rules' => 'required|in:1']) }}
                                            {{ __("Check to withdrawal's policy.") }}
                                        </label>
                                    </div>
                                    <span class="validation-message cval-error"
                                          data-cval-error="{{ fake_field('accept_policy') }}">{{ $errors->first('accept_policy') }}</span>
                                </div>
                            </div>

                            {{--submit button--}}
                            <div class="form-group">
                                <div class="col-md-offset-4 col-md-8">
                                    {{ Form::submit(__('Withdraw Balance'),['class'=>'btn btn-success form-submission-button']) }}
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                @else
                    <div class="box-body text-center">
                        <h4 class="bg-gray-light py-5 font-weight-bold" data-toggle="tooltip" data-placement="top" title="{{ __('Click to copy') }}">
                            <strong>{{ __('Withdrawal is currently disabled.') }}</strong>
                        </h4>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });
    </script>
@endsection