@extends('backend.layouts.top_navigation_layout')
@section('title', 'Home')
@section('after-style')
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
@endsection
@section('content')
    <div class="fullwidth">
        <div class="parallax-window" data-parallax="scroll"
             style="background-image:url({{asset('frontend/images/banner.jpg')}}); background-attachment: fixed; background-size: cover;">
            <div class="overlay-dark">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-sm-6 order-sm-last">
                            <img src="{{asset('frontend/images/bitcoin_circle.png')}}" alt="" class="banner-bitcoin">
                        </div>
                        <div class="col-sm-6 order-sm-first">
                            <div class="banner-text">
                                <p>{{ __('BIGGER PLATFORM, BIGGER OPPORTUNITY') }}</p>
                                <h1>{{ __('IT IS TOTAL CRYPTO CURRENCY SOLUTION') }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="background-image:url({{asset('frontend/images/banner.jpg')}}); background-attachment: fixed; background-size: cover;border-top: 2px solid #f94;border-bottom: 2px solid #26a69a;">
            <div style="padding: 0; background: rgba(0,0,0,0.8);">
                <div class="marquee-container">
                    <div class="marquee-inner-container">
                        <div class="marquee" data-time="{{ 11400+1800*$stockPairs->count() }}">
                            @foreach($stockPairs as $stockPair)
                                <div id="stock_pair{{ $stockPair->id }}" class="marquee-scroll-box">
                                    <h4>{{ $stockPair->stock_item_abbr }}/{{ $stockPair->base_item_abbr }}</h4>
                                    <span class="middark-color font-12">{{ __('Last Price') }}
                                        <span class="text-white last_price">{{ number_format($stockPair->last_price) }}</span>
                                    </span><br>
                                    <span class="font-12">
                                        @if($stockPair->change_24 > 0)
                                            <i class="fa fa-sort-up text-green"></i>
                                        @elseif($stockPair->change_24 < 0)
                                            <i class="fa fa-sort-down text-red"></i>
                                        @else
                                            <i class="fa fa-sort text-gray"></i>
                                        @endif
                                        &nbsp;<span class="text-white change_24">{{ number_format($stockPair->change_24,2) }}%</span>
                                    </span><br>
                                    <span class="middark-color font-12">{{ __('Volume') }}
                                        <span class="text-white volume">{{ number_format($stockPair->exchanged_base_item_volume_24, 3) }}</span>
                                    </span><br>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row align-items-center section-space">
                <div class="col-md-6 col-md-push-6">
                    <img src="{{asset('frontend/images/chart.png')}}" alt="" class="img-responsive">
                </div>
                <div class="col-md-6 col-md-pull-6">
                    <h2 class="tilte-left">{{ __('TRADE CONFIDENTLY') }}</h2>
                    <p>{{ __('We provide individuals and businesses a world class experience to buy and sell cutting-edge cryptocurrencies and digital tokens. Based and fully regulated in the USA, Bittrex is the go-to spot for traders who demand lightning fast trade execution, stable wallets, and industry-best security practices. Whether you are new to trading and cryptocurrencies, or a veteran to both, Bittrex.com was created for you!') }}</p>
                    <a href="#" class="crypto-button">{{ __('GET STARTED NOW') }}</a>
                </div>
            </div>
        </div>

        <div class="left-pattern" style="background-color:#fff">
            <div class="container-fluid">
                <div class="row section-space">
                    <div class="col-md-12">
                        <h2 class="title-center">{{ __('WHY CHOOSE CRYPTOMANIA') }}</h2>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.') }}</p>
                            </div>
                        </div>
                        <div class="row dc-clear">
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-box">
                                    <img src="{{asset('frontend/images/icons/crypto_lock.png')}}" alt=""
                                         class="icon-image">
                                    <h3 class="secondary-title">{{ __('Highly Secured') }}</h3>
                                    <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-box">
                                    <img src="{{asset('frontend/images/icons/crypto_profit.png')}}" alt=""
                                         class="icon-image">
                                    <h3 class="secondary-title">{{ __('Profitable Investment') }}</h3>
                                    <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-box">
                                    <img src="{{asset('frontend/images/icons/crypto_coin.png')}}" alt=""
                                         class="icon-image">
                                    <h3 class="secondary-title">{{ __('Top Crypto Currencies') }}</h3>
                                    <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-box">
                                    <img src="{{asset('frontend/images/icons/crypto_wallet.png')}}" alt=""
                                         class="icon-image">
                                    <h3 class="secondary-title">{{ __('Personal Wallet') }}</h3>
                                    <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-box">
                                    <img src="{{asset('frontend/images/icons/crypto_faith.png')}}" alt=""
                                         class="icon-image">
                                    <h3 class="secondary-title">{{ __('Reliable Platform') }}</h3>
                                    <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="icon-box">
                                    <img src="{{asset('frontend/images/icons/crypto_search.png')}}" alt=""
                                         class="icon-image">
                                    <h3 class="secondary-title">{{ __('Trade Analysis Support') }}</h3>
                                    <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="parallax-window section-space" data-parallax="scroll" data-image-src="images/option.jpg"
             style="background-image:url({{asset('frontend/images/option.jpg')}}); background-attachment: fixed; background-size: cover; ">
            <div class="container">
                <div class="row">
                    <h2 class="title-center text-white">{{ __('START TRADING OR GET SUGGESTED') }}</h2>
                    <div class="col-md-12">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div class="option-box">
                                    <div class="option-icon">
                                        <img src="{{asset('frontend/images/icons/trade.png')}}" alt="">
                                    </div>
                                    <div class="option-content">
                                        <h3 class="primary-color">{{ __('Interested in Trade?') }}</h3>
                                        <p class="text-white">{!! __('Sign up and Create Wallet.<br>Deposit your wallet.<br>Start Trading on Exchange.') !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="option-box">
                                    <div class="option-icon">
                                        <img src="{{asset('frontend/images/icons/analysis.png')}}" alt="">
                                    </div>
                                    <div class="option-content">
                                        <h3 class="primary-color">{{ __('Want To Know More?') }}</h3>
                                        <p class="text-white">{!!  __('Choose an Expert.') !!}<br>{!! __('Send Him/Her a message.') !!}<br>{!! __('You will be connected soon.') !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="left-pattern" style="background-color:#fff;">
            <div class="container-fluid">
                <div class="row section-space">
                    <div class="col-md-12">
                        <h2 class="title-center">{{ __('OUR MAJOR CRYPTO CURRENCIES') }}</h2>
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.') }}</p>
                            </div>
                        </div>
                        <div class="row dc-clear">
                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <img src="{{asset('frontend/images/coin-btc.jpg')}}" alt="" class="coin-image">
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <img src="{{asset('frontend/images/coin-ltc.jpg')}}" alt="" class="coin-image">
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <img src="{{asset('frontend/images/coin-blk.jpg')}}" alt="" class="coin-image">
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <img src="{{asset('frontend/images/coin-dash.jpg')}}" alt="" class="coin-image">
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <img src="{{asset('frontend/images/coin-eth.jpg')}}" alt="" class="coin-image">
                            </div>
                            <div class="col-md-2 col-sm-4 col-xs-6">
                                <img src="{{asset('frontend/images/coin-grd.jpg')}}" alt="" class="coin-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row section-space">
                <div class="col-md-12">
                    <h2 class="title-center">{{ __('EASY TRADE PROCESS') }}</h2>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2 text-center">
                            <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.') }}</p>
                        </div>
                    </div>
                    <div class="row dc-clear">
                        <div class="col-sm-4">
                            <div class="icon-box pad-tb-50">
                                <img src="{{asset('frontend/images/icons/crypto_wallet.png')}}" alt=""
                                     class="icon-image">
                                <h3 class="secondary-title"><span
                                            class="primary-color">{{ __('STEP 01') }} :</span> {{ __('CREATE WALLET') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="icon-box pad-tb-50">
                                <img src="{{asset('frontend/images/icons/crypto_coin.png')}}" alt="" class="icon-image">
                                <h3 class="secondary-title"><span
                                            class="primary-color">{{ __('STEP 02') }} :</span> {{ __('MAKE DEPOSIT') }}
                                </h3>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="icon-box pad-tb-50">
                                <img src="{{asset('frontend/images/icons/analysis.png')}}" alt="" class="icon-image">
                                <h3 class="secondary-title"><span
                                            class="primary-color">{{ __('STEP 03') }} :</span> {{ __('MAKE ORDER') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="parallax-window" data-parallax="scroll" data-image-src="images/glance.jpg"
             style="background-image:url({{asset('frontend/images/glance.jpg')}}); background-attachment: fixed; background-size: cover; ">
            <div class="overlay-dark section-space">
                <div class="container-fluid">
                    <div class="row dc-clear">
                        <div class="col-md-3 col-sm-6 text-center text-white">
                            <div class="pad-t-20">
                                <img src="{{asset('frontend/images/icons/icon-01.png')}}" alt="">
                                <div class="pad-t-20 font-20">{{ __('Total User') }}</div>
                                <div class="primary-color font-35">58497</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 text-center text-white">
                            <div class="pad-t-20">
                                <img src="{{asset('frontend/images/icons/icon-02.png')}}" alt="">
                                <div class="pad-t-20 font-20">{{ __('Total Coins') }}</div>
                                <div class="primary-color font-35">212</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 text-center text-white">
                            <div class="pad-t-20">
                                <img src="{{asset('frontend/images/icons/icon-03.png')}}" alt="">
                                <div class="pad-t-20 font-20">{{ __('Total Orders') }}</div>
                                <div class="primary-color font-35">275433</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 text-center text-white">
                            <div class="pad-t-20">
                                <img src="{{asset('frontend/images/icons/icon-04.png')}}" alt="">
                                <div class="pad-t-20 font-20">{{ __('Total Transactions') }}</div>
                                <div class="primary-color font-35">975721</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row section-space align-items-center">
                <div class="col-md-6 order-md-last">
                    <img src="{{asset('frontend/images/cryptocurrency.png')}}" alt="" class="img-responsive img-center">
                </div>
                <div class="col-md-6 order-md-first">
                    <h2 class="tilte-left">{{ __('ABOUT CRYPTOCURRENCY') }}</h2>
                    <p>{{ __('Cryptocurrency is basically digital currency that is considered as an alternative option of money. It is easily transferable, suitable for online payment and highly secured.') }}</p>
                    <ul class="custom-list">
                        <li>{{ __('Lorem ipsum dolor sit amet, consectetur adipisicing elit.') }}</li>
                        <li>{{ __('Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.') }}</li>
                        <li>{{ __('Ut enim ad minim veniam, quis nostrud exercitation ullamco.') }}</li>
                        <li>{{ __('Laboris nisi ut aliquip ex ea commodo consequat.') }}</li>
                        <li>{{ __('Duis aute irure dolor in reprehenderit in voluptate velit.') }}</li>
                    </ul>

                </div>
            </div>
        </div>
        <div style="background:#fff;">
            <div class="container">
                <div class="row section-space">
                    <div class="col-md-12">
                        <h2 class="title-center">{{ __('OUR ANALYSTS') }}</h2>
                        <div class="row dc-clear">
                            <div class="col-sm-6 col-md-3">
                                <div class="profile-box-front">
                                    <img src="{{asset('frontend/images/01.jpg')}}" alt="">
                                    <div class="primary-bg"><a href="javascript:" class="text-white profile-title">{{ __('JHON DOE') }}</a></div>
                                    <div class="profile-role text-white">{{ __('Bitcoin Analyst') }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="profile-box-front">
                                    <img src="{{asset('frontend/images/02.jpg')}}" alt="">
                                    <div class="primary-bg"><a href="javascript:" class="text-white profile-title">{{ __('JACK DOE') }}</a></div>
                                    <div class="profile-role text-white">{{ __('Ethereum Analyst') }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="profile-box-front">
                                    <img src="{{asset('frontend/images/03.jpg')}}" alt="">
                                    <div class="primary-bg"><a href="javascript:" class="text-white profile-title">{{ __('JOE DOE') }}</a></div>
                                    <div class="profile-role text-white">{{ __('Litecoin Analyst') }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="profile-box-front">
                                    <img src="{{asset('frontend/images/04.jpg')}}" alt="">
                                    <div class="primary-bg"><a href="javascript:" class="text-white profile-title">{{ __('JUKE DOE') }}</a></div>
                                    <div class="profile-role text-white">{{ __('Ripple Analyst') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row section-space">
                <div class="col-md-12">
                    <h2 class="title-center">{{ __('Trading View') }}</h2>
                    <div class="row dc-clear">
                        @foreach($posts as $post)
                            <div class="col-sm-4">
                                <div class="trade-analysis">
                                    <img src="{{ get_post_image($post->featured_image) }}" alt="">
                                    <a href="{{ route('trading-views.show',$post->id) }}"
                                       class="analysis-title">{{ $post->title }}</a>
                                    <div><i class="fa fa-comment-o"></i> {{ $post->comments->count() }} |
                                        Published: {{ $post->created_at }}</div>
                                    <p>{!!  str_limit($post->content, 150) !!}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="top-footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pad-tb-20 text-center">
                                <img src="{{asset('frontend/images/logo-inverse.png')}}" alt=""
                                     class="img-fluid pad-b-10">
                                <ul class="floated-li-inside clearfix centered">
                                    {!! social_media_link('facebook') !!}
                                    {!! social_media_link('twitter') !!}
                                    {!! social_media_link('linkedin') !!}
                                    {!! social_media_link('google_plus') !!}
                                    {!! social_media_link('pinterest') !!}
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
    <script>
        (function ($) {
            $(document).on('click', '.dropdown-item', function () {
                $(this).parent().siblings('.dropdown-toggle').find('.text-dropdown').text($(this).data('val'));
            });

            $('.marquee').each(function () {
                var $this = $(this);
                var parentWidth = $this.parent().outerWidth();
                var scrollTime = +$this.data('time');
                var childWidth = $this.children().eq(0).outerWidth();
                var sublength = $this.children().length;
                var thisWidth = childWidth * (sublength - 1);
                if (parentWidth < thisWidth) {
                    $this.css({
                        'margin-left': (parentWidth - thisWidth) + 'px'
                    });
                } else {
                    thisWidth = parentWidth;
                }
                $this.width(thisWidth);
                var unitDelay = Math.round(scrollTime * childWidth / (thisWidth + childWidth));
                for (var i = 0; i < sublength; i++) {
                    var thisItemDelay = unitDelay * i;
                    // if(thisItemDelay != 0){
                    thisItemDelay = thisItemDelay + 'ms';
                    // }
                    $this.children().eq(i).css({
                        'animation': 'scroll-now ' + scrollTime + 'ms linear ' + thisItemDelay + ' infinite',
                        '-moz-animation': 'scroll-now ' + scrollTime + 'ms linear ' + thisItemDelay + ' infinite',
                        '-webkit-animation': 'scroll-now ' + scrollTime + 'ms linear ' + thisItemDelay + ' infinite',
                        '-o-animation': 'scroll-now ' + scrollTime + 'ms linear ' + thisItemDelay + ' infinite',
                        '-ms-animation': 'scroll-now ' + scrollTime + 'ms linear ' + thisItemDelay + ' infinite'
                    });
                }
            });

            Echo.channel(channelPrefix + 'exchange').listen('Exchange.BroadcastStockSummary', (data) => {
                updateMarque(data.stockSummary);
            });

        })(jQuery);

        function updateMarque(data) {
            let marque = $('#stock_pair' + data.stock_pair_id);
            marque.find('.last_price').text(number_format(data.last_price));
            marque.find('.change_24').text(number_format(data.change_24));
            marque.find('.volume').text(number_format(data.exchanged_base_item_volume_24));
        }
    </script>
@endsection