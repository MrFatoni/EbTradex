@extends('backend.layouts.top_navigation_layout')
@section('title', $title)
@section('after-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/mCustomScrollbar/jquery.mCustomScrollbar.min.css') }}">
    <style>
        #stock_market_table tbody tr:hover, #buy_order_table tbody tr:hover, #sell_order_table tbody tr:hover {
            cursor: pointer;
        }

        .no-clicke-header {
            pointer-events: none;
        }

        .exchange-loader {
            background: url({{asset('common/images/progress-bar.gif')}}) no-repeat center;
            background-size: 150px;
        }

        .filter {
            display: inline-block;
        }

        .exchange-table {
            font-size: 12px;
        }

        table.exchange-table {
            width: 100% !important;
        }

        table.exchange-table tr th, table.exchange-table tr td {
            /*text-align: right !important;*/
            padding-left: 5px;
            padding-right: 5px;
        }

        table.exchange-table tr th::after {
            font-size: 10px;
            right: 5px !important;
            bottom: 10px !important;
        }

        table.exchange-table tbody tr td {
            padding: 3px 5px;
        }

        table.exchange-table tbody tr.selected td {
            background: #cfffcf;
        }

        table.exchange-table tbody tr:first-child td {
            border-top: none !important;
        }

        table.exchange-table tr th {
            /*text-align: center !important;*/
            padding-right: 18px !important;
        }


        .dataTables_wrapper .dataTables_scroll div.dataTables_scrollBody {
            border: none;
            overflow: hidden !important;
        }

        .dataTables_scrollHeadInner {
            width: 100% !important;
        }

        .mCSB_scrollTools {
            right: -5px;
        }

        .mCS-dark-thick.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar {
            background: #b7deed;
            background: -moz-linear-gradient(left, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
            background: -webkit-linear-gradient(left, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
            background: linear-gradient(to right, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);

        }

        .mCS-dark-thick.mCSB_scrollTools .mCSB_dragger:hover .mCSB_dragger_bar {
            background: #b7deed;
            background: -moz-linear-gradient(left, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
            background: -webkit-linear-gradient(left, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
            background: linear-gradient(to right, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
        }

        .mCS-dark-thick.mCSB_scrollTools .mCSB_draggerRail {
            background-color: #efefef;
        }

        .mCS-dark-thick.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar, .mCS-dark-thin.mCSB_scrollTools .mCSB_dragger:active .mCSB_dragger_bar {
            background: #b7deed;
            background: -moz-linear-gradient(left, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
            background: -webkit-linear-gradient(left, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
            background: linear-gradient(to right, #b7deed 0%, #71ceef 50%, #21b4e2 51%, #b7deed 100%);
        }


        .mCSB_inside > .mCSB_container {
            margin-right: 10px;
        }

        .filter {
            width: 110px;
            position: relative;
        }

        .filter::after {
            content: '\f103';
            width: 25px;
            height: 100%;
            position: absolute;
            top: 0;
            right: 0;
            font-family: "FontAwesome";
            text-align: center;
            padding-top: 7px;
            color: #999;
        }

        #datatable-filter:focus {
            border: 1px solid #d2d6de;
            outline: none !important;
        }


        div.dataTables_wrapper div.dataTables_filter input {
            height: 34px;
        }

        #datatable-filter {
            -moz-appearance: none;
            -webkit-appearance: none;
            padding-right: 20px;

            position: relative;

            z-index: 99999999;

            background: none;

            color: rgba(0, 0, 0, 0);
            text-shadow: 0 0 0 #999;
        }

        #datatable-filter::-ms-expand {
            display: none;
        }

        @media all and (max-width: 420px) {
            .hide_in_mobile_small {
                display: none !important;
            }
        }


        @media all and (max-width: 512px) {

            .hide_in_mobile {
                display: none !important;
            }


            table.exchange-table tr td {
                font-size: 10px;
                padding: 3px;
            }

            .full-in-small {
                width: auto;
                margin-right: -15px;
                margin-left: -15px;
            }

            .full-in-small > div {
                padding-left: 8px !important;
                padding-right: 10px !important;
            }

            table.exchange-table.dataTable > tbody > tr.child > td {
                padding: 3px !important;
            }
        }

        @keyframes fadeGreen {
            from {
                background: rgba(0, 210, 0, 1);
            }

            to {
                background: rgba(0, 210, 0, 0);
            }
        }

        @keyframes fadeRed {
            from {
                background: rgba(210, 0, 0, 1);
            }

            to {
                background: rgba(210, 0, 0, 0);
            }
        }

        @keyframes fadeYellow {
            from {
                background: rgba(210, 210, 0, 1);
            }

            to {
                background: rgba(210, 210, 0, 0);
            }
        }

        .deleted {
            animation-name: fadeRed;
            animation-duration: 1s;
            animation-fill-mode: both;
        }

        .updated {
            animation-name: fadeYellow;
            animation-duration: 1s;
            animation-fill-mode: both;
        }

        .inserted {
            animation-name: fadeGreen;
            animation-duration: 1s;
            animation-fill-mode: both;
        }

        #candlestick_zoom li.disabled a, #candlestick li.disabled a {
            background: #fdb;
        }

        #fixed-stock-market {
            transition: all 0.3s linear;
        }

        #fixed-stock-market i {
            display: none;
        }

        #fixed-stock-market.opened i {
            display: inline-block;
        }

        #fixed-stock-market.opened span {
            display: none;
        }

        #fixed-stock-market.opened {
            left: 0;
        }

        #fixed-stock-market-toggler.opened {
            left: 293px;
            transform: translateX(-100%);
        }

        #fixed-stock-market > div {
            background: #fff;
            height: 100%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        #fixed-stock-market-toggler:hover {
            background: #f3b60e !important;
        }

        #fixed-stock-market-toggler {
            background: #f39c12 !important;
            border-color: #f39c12 !important;
            position: absolute;
            top: 10px;
            left: 300px;
            transform: translateX(0);
            transition: all 0.3s linear;
            z-index: 3;
        }

        #fixed-stock-market .box-body {
            padding-left: 7px;
            padding-right: 7px;
        }

        #fixed-stock-market {
            position: absolute;
            top: 0;
            left: 0;
            left: -300px;
            width: 300px;
            height: 100%;
            margin: 0;
            z-index: 3;
            padding-top: 50px;
        }
        .main-header {
            z-index: 4;
        }

        /*@media all and (max-width: 767px) {
            #fixed-stock-market {
                padding-top: 100px;
            }
        }*/

        @media all and (max-width: 640px) {
            #fixed-stock-market .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter {
                float: right;
            }
        }

        @media all and (min-width: 1200px) {
            .summary-padding-fixer {
                padding-left: 50px;
            }
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-borderless full-in-small">
                <div class="box-header" style="padding-bottom: 5px;border-bottom:1px solid #efefef">
                    @include('frontend.exchange.stock_pair_summary')
                </div>

                <div class="box-body" style="padding-top: 10px">
                    @include('frontend.exchange.chart')
                </div>
            </div>
        </div>
    </div>

    @include('frontend.exchange.stock_market')

    <div class="row">
        <div class="col-md-4">
            @include('frontend.exchange.buy_form')
        </div>

        <div class="col-md-4">
            @include('frontend.exchange.stop_limit_form')
        </div>

        <div class="col-md-4">
            @include('frontend.exchange.sell_form')
        </div>
    </div>

    @include('frontend.exchange.order_book')
    @auth
        @include('frontend.exchange.my_order')
    @endauth

    @include('frontend.exchange.trade_history')

@endsection

@section('script')
    <script src="{{asset('common/vendors/bcmath/libbcmath-min.js')}}"></script>
    <script src="{{asset('common/vendors/bcmath/bcmath.js')}}"></script>
    <script src="{{asset('common/vendors/mCustomScrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script src="{{asset('common/vendors/echart/echarts.min.js')}}"></script>
    <script src="{{asset('js/chart.js')}}"></script>
    <script src="{{asset('common/vendors/cvalidator/cvalidator.js')}}"></script>

    @include('frontend.exchange.initial_js')

    @include('frontend.exchange.broadcast_js')

    @include('frontend.exchange.custom_function_js')

@endsection