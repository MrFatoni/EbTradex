@extends('backend.layouts.top_navigation_layout')
@section('title', $title)

@section('after-style')
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
@endsection

@section('content')
<div class="fullwidth" style="background: #fff">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="box box-primary box-borderless">
                    <div class="box-header">
                        <h3 class="box-title">{{ __('Current ICO') }}</h3>
                    </div>
                    <div class="box-body">
                        <table class="table datatable dt-responsive display nowrap dc-table" style="width:100% !important;">
                            <thead>
                            <tr>
                                <th class="all">{{ __('Stock Name') }}</th>
                                <th  class="min-phone-l">{{ __('Market') }}</th>
                                <th  class="min-phone-l">{{ __('Price') }}</th>
                                <th  class="min-phone-l">{{ __('Volume') }}</th>
                                <th class="text-center all no-sort">{{ __('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($list['query'] as $stockItem)
                                @if($stockItem->stock_pair_id)
                                <tr>
                                    <td>{{ $stockItem->item_name }} ({{ $stockItem->item }})</td>
                                    <td>{{ $stockItem->item }}/{{ $stockItem->base_item }}</td>
                                    <td>{{ $stockItem->last_price }} {{ $stockItem->base_item }}</td>
                                    <td>{{ $stockItem->ico_total_sold }} <span class="strong">{{ $stockItem->item }}</span> / {{ $stockItem->ico_total_earned }} <span class="strong">{{ $stockItem->base_item }}</span></td>
                                    <td class="cm-action text-center">
                                        @auth
                                            @if(has_permission('exchange.ico.buy'))
                                                <a href="{{ route('exchange.ico.buy', ['id' => $stockItem->stock_pair_id])}}"><i class="fa fa-google-wallet"></i> {{ __('Buy now') }}</a>
                                            @endif
                                        @endauth

                                        @guest
                                            <a href="{{ route('login') }}">{{__('Login')}}</a> {{ __('or') }}
                                            <a href="{{ route('register.index') }}">{{ __('Register') }}</a> {{ __('to buy') }}
                                        @endguest
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {!! $list['pagination'] !!}
    </div>
    <footer class="footer">
        <div class="top-footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="pad-tb-20 text-center">
                            <img src="{{asset('frontend/images/logo-inverse.png')}}" alt="" class="img-fluid pad-b-10">
                            <ul class="floated-li-inside clearfix centered">
                                <li><a href="#"><i class="fa fa-facebook-square font-20"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter font-20"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin font-20"></i></a></li>
                                <li><a href="#"><i class="fa fa-google-plus font-20"></i></a></li>
                                <li><a href="#"><i class="fa fa-pinterest font-20"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{ asset('common/vendors/datepicker/datepicker.js') }}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/datatables.min.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/datatables/plugins/bootstrap/datatables.bootstrap.js')}}"></script>
    <script src="{{asset('common/vendors/datatable_responsive/table-datatables-responsive.js')}}"></script>
    <script type="text/javascript">
        //Init jquery Date Picker
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom',
            todayHighlight: true,
        });
    </script>
@endsection