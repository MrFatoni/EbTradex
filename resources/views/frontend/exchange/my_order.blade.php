<div class="row">
    <div class="col-md-12">
        <div class="box box-borderless full-in-small">
            <div class="box-header">
                <h3 class="box-title text-uppercase">{{ __('My Orders') }}</h3>
            </div>
            <div class="box-body pt-0">
                <table id="my_open_order_table" class="table table-hover table-responsive small exchange-table"
                       style="width:100%">
                    <thead>
                    <tr>
                        <th>{{ __('TYPE') }}</th>
                        <th>{{ __('PRICE') }}</th>
                        <th>{{ __('AMOUNT') }}</th>
                        <th class="hide_in_mobile_small">{{ __('TOTAL') }}</th>
                        <th class="hide_in_mobile">{{ __('DATE') }}</th>
                        <th class="hide_in_mobile">{{ __('STOP') }}</th>
                        <th>{{ __('ACTION') }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>