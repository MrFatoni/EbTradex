<div class="box box-primary">
    <div class="box-body box-profile">
        <img src="{{ get_avatar($user->avatar) }}" alt="{{ __('Profile Image') }}"
             class="img-responsive cm-center">
        <h4 class="cm-mt-10 text-center">{{ $user->userInfo->full_name }}</h4>
    </div>

    <div class="box-footer no-padding">
        <ul class="nav nav-stacked">
            @if(has_permission($profileRouteInfo['walletRouteName']))
                <li><a href="{{ $profileRouteInfo['walletRoute'] }}">{{ __('Wallets') }} <span
                                class="pull-right badge bg-green">{{ $profileRouteInfo['totalWallets'] }}</span></a></li>
            @endif

            @if(has_permission($profileRouteInfo['openOrderRouteName']))
                <li><a href="{{ $profileRouteInfo['openOrderRoute'] }}">{{ __('Open Orders') }} <span
                                class="pull-right badge bg-green">{{ $profileRouteInfo['totalOpenOrders'] }}</span></a></li>
            @endif

            @if(has_permission($profileRouteInfo['tradeHistoryRouteName']))
                <li><a href="{{ $profileRouteInfo['tradeHistoryRoute'] }}">{{ __('Trade History') }} <span
                                class="pull-right badge bg-green">{{ $profileRouteInfo['totalTrades'] }}</span></a></li>
            @endif
        </ul>
    </div>
</div>