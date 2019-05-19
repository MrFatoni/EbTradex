<div class="row">
    <div class="col-md-6">
        <div class="box box-borderless full-in-small">
            <div class="box-header">
                <h3 class="box-title text-uppercase">{{ __('Sell Orders') }}</h3>
                <div class="pull-right text-bold">{{ __('Total') }}:
                    <span id="total_sell_order_in_item"></span>
                    <span class="stock_item"></span>
                </div>
            </div>
            <div class="box-body pt-0" style="height: 465px">
                <table id="sell_order_table"
                       class="table table-hover table-striped table-responsive small exchange-table">
                    <thead class="no-clicke-header">
                    <tr>
                        <th>{{ __('PRICE') }}</th>
                        <th><span class="stock_item"></span></th>
                        <th><span class="base_item"></span></th>
                        <th class="hide_in_mobile_small">{{ __('SUM') }}(<span class="base_item"></span>)</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box box-borderless full-in-small">
            <div class="box-header">
                <h3 class="box-title text-uppercase">{{ __('Buy Orders') }}</h3>
                <div class="pull-right text-bold">{{ __('Total') }}:
                    <span id="total_buy_order_in_base"></span>
                    <span class="base_item"></span>
                </div>
            </div>

            <div class="box-body pt-0" style="height: 465px">
                <table id="buy_order_table"
                       class="table table-hover table-striped table-responsive small exchange-table">
                    <thead class="no-clicke-header">
                    <tr>
                        <th>{{ __('PRICE') }}</th>
                        <th><span class="stock_item"></span></th>
                        <th><span class="base_item"></span></th>
                        <th class="hide_in_mobile_small">{{ __('SUM') }}(<span class="base_item"></span>)</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>