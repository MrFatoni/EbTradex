@extends('backend.layouts.top_navigation_layout')
@section('title', $title)

@section('after-style')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <h3 class="title text-center margin-b-20">{{ __('ICO: Buy') }} {{ $stockPair->stockItem->item }} / {{ $stockPair->baseItem->item }}</h3>

            <div class="box box-primary box-borderless">
                <div class="box-header with-border">
                    {{ $stockPair->stockItem->item }} / {{ $stockPair->baseItem->item }}
                </div>
                <div class="box-body calculator">
                    <div class="form-horizontal show-form-data">
                        @auth
                            <div class="form-group margin-bottom-none">
                                <label class="col-xs-6 font-light margin-bottom-none">{{ __('You have') }}:</label>
                                <div class="col-xs-6">
                                    <div class="text-right">
                                        <span class="base_item_balance clickable"
                                              data-baseitembalance="{{ $wallets[$stockPair->baseItem->id] }}">{{ $wallets[$stockPair->baseItem->id] }}</span>
                                        <span class="stock_item">{{ $stockPair->baseItem->item }}</span>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>
                    <hr class="margin-t-10 margin-b-10">
                    {!! Form::open(['route'=>['exchange.ico.store'], 'class'=>'form-horizontal show-form-data validator']) !!}
                        <input type="hidden" value="{{ base_key() }}" name="base_key">
                        <input type="hidden" value="{{ $stockPair->id }}" name="{{ fake_field('stock_pair_id') }}">
                        <div class="form-group">
                            <label for="price" class="col-xs-4 control-label">{{ __('Price') }}</label>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm" style="width: 100%;">
                                    <p class="form-control-static strong text-right last-price"
                                       data-lastprice="{{ $stockPair->last_price }}">{{ $stockPair->last_price }} {{ $stockPair->baseItem->item }}</p>
                                </div>
                            </div>
                        </div>


                        <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('amount') }}"  class="col-xs-4 control-label">{{ __('Amount') }}</label>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    {{ Form::text(fake_field('amount'),  old('amount', null), ['class'=>'form-control text-right amount', 'id' => fake_field('amount'),'data-cval-name' => 'The amount field','data-cval-rules' => 'required|numeric|between:0.00000001,99999999999.99999999', 'placeholder' => __('ex: 66')]) }}
                                    <span class="input-group-addon text-uppercase">
                                        <span class="stock_item">{{ $stockPair->stockItem->item }}</span>
                                    </span>
                                </div>
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('amount') }}">{{ $errors->first('amount') }}</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="amount" class="col-xs-4 control-label">{{ __('Fee') }}</label>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm" style="width: 100%;">
                                    <p class="form-control-static text-right">
                                        <span class="fee">0</span>
                                        <span class="base_stock_item">{{ $stockPair->baseItem->item }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <hr class="margin-top-none margin-b-10">

                        <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
                            <label for="{{ fake_field('total') }}"  class="col-xs-4 control-label">{{ __('Total') }}</label>
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    {{ Form::text(fake_field('total'),  old('total', null), ['class'=>'form-control text-right total', 'id' => fake_field('total'),'data-cval-name' => 'The total field','data-cval-rules' => 'numeric|between:0.00000001,99999999999.99999999']) }}
                                    <span class="input-group-addon text-uppercase">
                                        <span class="stock_item">{{ $stockPair->baseItem->item }}</span>
                                    </span>
                                </div>
                                <span class="validation-message cval-error" data-cval-error="{{ fake_field('total') }}">{{ $errors->first('total') }}</span>
                            </div>
                        </div>

                        <hr class="margin-t-10 margin-b-10">

                        @auth
                            <button type="submit" class="btn btn-success btn-sm btn-block form-submission-button">
                                {{ __('Buy') }}
                            </button>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}">{{__('Login')}}</a> {{ __('or') }}
                            <a href="{{ route('register.index') }}">{{ __('Register') }}</a> {{ __('to buy') }}
                        @endguest
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{asset('common/vendors/cvalidator/cvalidator.js')}}"></script>

    <script>
        function setValues(total, element) {
            var element = element.closest('.calculator');
            var icoFee = parseFloat({{ admin_settings('ico_fee') > 0 ? admin_settings('ico_fee') : 0  }});
            var total = parseFloat(total);
            var price = parseFloat(element.find('.last-price').data('lastprice'));
            total = !total ? 0 : total;
            var fee = parseFloat(total * icoFee / 100);
            var actualTotal = parseFloat(total - fee);
            var amount = parseFloat(actualTotal / price);
            amount = !amount ? 0 : amount;

            element.find('.amount').val(amount.toFixed(8));
            element.find('.fee').text(fee.toFixed(8));
        }

        $(function () {
            $('.validator').cValidate({});
            $(document).on('keyup', '.amount', function () {
                var element = $(this).closest('.calculator');
                var icoFee = parseFloat({{ admin_settings('ico_fee') > 0 ? admin_settings('ico_fee') : 0  }});
                var amount = parseFloat($(this).val());
                var price = parseFloat(element.find('.last-price').data('lastprice'));
                var total = parseFloat(price * amount);
                total = !total ? 0 : total;
                var fee = total * icoFee / 100;
                var totalCost = total + fee;

                element.find('.fee').text(fee.toFixed(8));
                element.find('.total').val(totalCost.toFixed(8));
            });

            $(document).on('keyup', '.total', function () {
                setValues($(this).val(), $(this));
            });

            $(document).on('click', '.base_item_balance', function () {
                var balance = $(this).data('baseitembalance');
                $(this).closest('.calculator').find('.total').val(parseFloat(balance).toFixed(8));

                setValues(balance, $(this));
            });
        });
    </script>
@endsection