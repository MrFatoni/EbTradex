<div class="row">
    <div class="col-md-12">
        <div class="nav-tabs-custom full-in-small" style="cursor: move;">
            <ul class="nav nav-tabs ui-sortable-handle">
                <li class="active">
                    <a href="#market_trade" data-toggle="tab" aria-expanded="false">{{ __('MARKET TRADES') }}</a>
                </li>
                @auth
                    <li class="">
                        <a href="#my_trade" data-toggle="tab" aria-expanded="true">{{ __('MY TRADES') }}</a>
                    </li>
                @endauth
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="market_trade">
                    <table id="trade_history_table" class="table table-hover table-responsive small exchange-table"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th class="hide_in_mobile">{{ __('DATE') }}</th>
                            <th>{{ __('TYPE') }}</th>
                            <th class="text-center">{{ __('PRICE') }}</th>
                            <th class="text-center">{{ __('AMOUNT') }}</th>
                            <th class="text-center hide_in_mobile_small">{{ __('TOTAL') }}</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                @auth
                    <div class="tab-pane" id="my_trade">
                        <table id="my_trade_table" class="table table-hover table-responsive small exchange-table"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th class="hide_in_mobile">{{ __('DATE') }}</th>
                                <th>{{ __('TYPE') }}</th>
                                <th class="text-center">{{ __('PRICE') }}</th>
                                <th class="text-center">{{ __('AMOUNT') }}</th>
                                <th class="text-center hide_in_mobile_small">{{ __('TOTAL') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div>