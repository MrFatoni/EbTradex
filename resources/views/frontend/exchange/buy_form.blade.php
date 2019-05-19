<div class="box box-borderless full-in-small">
    <div class="box-header with-border">
        <h3 class="box-title text-uppercase">{{ __('Buy') }} <span class="stock_item"></span></h3>
        @auth
            <div class="box-tools pull-right">
                <a class="btn btn-box-tool" href="depositPageLink">{{ __('Deposit') }}
                    <span class="base_item"></span>
                </a>
            </div>

            <div class="form-horizontal show-form-data">
                <div class="form-group margin-bottom-none">
                    <label class="col-xs-6 font-light margin-bottom-none">{{ __('You have') }}:</label>
                    <div class="col-xs-6">
                        <div class="text-right">
                            <span class="clickable base_item_balance"></span>
                            <span class="base_item"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group margin-bottom-none">
                    <label class="col-xs-6 font-light margin-bottom-none">{{ __('Lowest Ask') }}:</label>
                    <div class="col-xs-6">
                        <div class=" text-right">
                            <span class="clickable lowest_ask"></span>
                            <span class="base_item"></span>
                        </div>
                    </div>
                </div>
            </div>
        @endauth

    </div>
    <div class="box-body">
        <form method="post" action="{{ route('trader.orders.store') }}" id="buy_order_form"
              class="form-horizontal show-form-data" data-ajax-submission="y"
              data-reset-on-success="y">
            @csrf
            <input type="hidden" value="{{ base_key() }}" name="base_key">
            <input type="hidden" value="{{ EXCHANGE_BUY }}"
                   name="{{ fake_field('exchange_type') }}">
            <input type="hidden" value="{{ CATEGORY_EXCHANGE }}"
                   name="{{ fake_field('category') }}">
            <input type="hidden" value="{{ $stockPair->id }}"
                   name="{{ fake_field('stock_pair_id') }}" class="stock-pair">
            <div class="form-group">
                <label for="price" class="col-xs-4 control-label">{{ __('Price') }}</label>
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control text-right price"
                               name="{{ fake_field('price') }}">
                        <span class="input-group-addon text-uppercase">
                                                <span class="base_item"></span>
                                            </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="amount" class="col-xs-4 control-label">{{ __('Amount') }}</label>
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control text-right amount"
                               name="{{ fake_field('amount') }}">
                        <span class="input-group-addon text-uppercase">
                                                <span class="stock_item"></span>
                                            </span>
                    </div>
                </div>
            </div>

            <hr class="margin-top-none margin-b-10">

            <div class="form-group">
                <label for="total" class="col-xs-4 control-label">{{ __('Total') }}</label>
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control text-right total">
                        <span class="input-group-addon text-uppercase">
                                                <span class="base_item"></span>
                                            </span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 30px">
                {{ __('Fee: :makerFee/:takerFee%',['makerFee' =>admin_settings('exchange_maker_fee'),'takerFee' => admin_settings('exchange_taker_fee')]) }}
            </div>

            <hr class="margin-t-10 margin-b-10">
            @auth
                <button class="btn btn-success btn-sm btn-block form-submission-button">{{ __('BUY') }}</button>
            @endauth

            @guest
                <a href="{{ route('login') }}">{{__('Login')}}</a> {{ __('or') }} <a
                        href="{{ route('register.index') }}">{{ __('Register') }}</a>{{ __(' to trade') }}
            @endguest
        </form>
    </div>
</div>
