<div class="row">
    <div class="col-md-12">
        <div class="exchange-loader" id="echart" style="width:100%; height: 550px;"></div>

    </div>
</div>

<div class="row">
    <div class="col-md-6 text-center">
        <ul id="candlestick_zoom" class="pagination pagination-sm">
            <li class="active"><span>{{ __('Zoom') }}</span></li>
            <li  class="{{ $chartZoom == 360 ? 'disabled' : '' }}"><a data-zoom="360" href="javascript:">{{ __('6h') }}</a></li>
            <li  class="{{ $chartZoom == 1440 ? 'disabled' : '' }}"><a data-zoom="1440" href="javascript:">{{ __('1D') }}</a></li>
            <li  class="{{ $chartZoom == 2880 ? 'disabled' : '' }}"><a data-zoom="2880" href="javascript:">{{ __('2D') }}</a></li>
            <li  class="{{ $chartZoom == 5760 ? 'disabled' : '' }}"><a data-zoom="5760" href="javascript:">{{ __('4D') }}</a></li>
            <li  class="{{ $chartZoom == 10080 ? 'disabled' : '' }}"><a data-zoom="10080" href="javascript:">{{ __('1W') }}</a></li>
            <li class="{{ $chartZoom == 20160 ? 'disabled' : '' }}"><a data-zoom="20160" href="javascript:">{{ __('2W') }}</a></li>
            <li  class="{{ $chartZoom == 43200 ? 'disabled' : '' }}"><a data-zoom="43200" href="javascript:">{{ __('1M') }}</a></li>
            <li  class="{{ $chartZoom == 0 ? 'disabled' : '' }}"><a data-zoom="0" href="javascript:">{{ __('All') }}</a></li>
        </ul>
    </div>
    <div class="col-md-6 text-center">
        <ul id="candlestick" class="pagination pagination-sm">
            <li class="active"><span>{{ __('Candlesticks') }}</span></li>
            <li class="{{ $chartInterval == 5 ? 'disabled' : '' }}"><a data-interval="5" href="javascript:">{{ __('5M') }}</a></li>
            <li class="{{ $chartInterval == 15 ? 'disabled' : '' }}"><a data-interval="15" href="javascript:">{{ __('15M') }}</a></li>
            <li class="{{ $chartInterval == 30 ? 'disabled' : '' }}"><a data-interval="30" href="javascript:">{{ __('30M') }}</a></li>
            <li class="{{ $chartInterval == 120 ? 'disabled' : '' }}"><a data-interval="120" href="javascript:">{{ __('2H') }}</a></li>
            <li class="{{ $chartInterval == 240 ? 'disabled' : '' }}"><a data-interval="240" href="javascript:">{{ __('4H') }}</a></li>
            <li class="{{ $chartInterval == 1440 ? 'disabled' : '' }}"><a data-interval="1440" href="javascript:">{{ __('1D') }}</a></li>
        </ul>
    </div>
</div>