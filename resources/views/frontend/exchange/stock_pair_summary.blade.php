<div class="summary-padding-fixer">
<div class="row dc-clear">
    <div class="col-md-4 col-sm-6 col-lg-2 text-center">
        <h3 class="box-title text-uppercase"><span class="stock_item"></span> {{ __('Exchange') }}
        </h3>
        <div>
            <small class="text-bold cm-ml-10 text-green text-left">
                <span class="stock_item"></span>/
                <span class="base_item"></span>
            </small>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-2 exchange-table">
        <div class="text-center border-right">
            <span class="description-text">{{ __('Last Price') }}: </span><br>
            <strong><span id="last_price" class="description-header"></span></strong>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-lg-2 exchange-table">
        <div class="text-center border-right">
            <span class="description-text">{{ __('24hr Change') }}: </span><br>
            <strong><span id="_24_hour_percentage"
                          class="description-header description-percentage"><i
                            class="fa fa-caret-up"></i>0%</span></strong>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-2 exchange-table">
        <div class="text-center border-right">
            <span class="description-text">{{ __('24hr High') }}: </span><br>
            <strong><span id="_24_hour_high" class="description-header"></span></strong>
        </div>
    </div>
    <div class="col-md-4 col-sm-6 col-lg-2 exchange-table">
        <div class="text-center border-right">
            <span class="description-text">{{ __('24hr Low') }}: </span><br>
            <strong><span id="_24_hour_low" class="description-header"></span></strong>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-lg-2 text-center exchange-table">
                            <span class="description-text" style="margin: 0">
                                {{ __('24hr Volume') }}:
                            </span><br>

        <span id="_24_hour_item_volume" class="strong"></span>
        <span class="stock_item"></span>/
        <span id="_24_hour_base_volume" class="strong"></span>
        <span class="base_item"></span>
    </div>

</div>
</div>