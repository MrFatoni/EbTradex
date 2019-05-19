<div id="fixed-stock-market">
    <div>
        <div class="box box-borderless">
            <button id="fixed-stock-market-toggler" class="btn btn-primary"><i class="fa fa-bars"></i> <span class="stock_item"></span><span>/</span><span class="base_item"></span></button>
            <div class="box-header">
                <h3 class="box-title text-uppercase">{{ __('Markets') }}</h3>
            </div>
            <div class="box-body" id="stock-market-section">
                <table id="stock_market_table" class="table table-hover table-responsive small exchange-table">
                    <thead>
                    <tr>
                        <th>{{ __('STOCK') }}</th>
                        <th>{{ __('PRICE') }}</th>
                        <th>{{ __('VOLUME') }}</th>
                        <th>{{ __('CHANGE') }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>