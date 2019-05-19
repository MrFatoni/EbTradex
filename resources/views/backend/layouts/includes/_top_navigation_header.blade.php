<header class="main-header">
    <nav class="navbar navbar-static-top">
        <div class="container-fluid">
            <div class="navbar-header">
                @if(admin_settings('company_logo'))
                    <a href="{{ route('home') }}" class="navbar-brand"><img
                                style="width: 40px;height: 40px; margin-top: -10px"
                                src="{{ get_image(admin_settings('company_logo')) }}"></a>
                @else
                    <a style="text-transform: uppercase" href="{{ route('home') }}" class="navbar-brand"><b>{{ env('APP_NAME') }}</b></a>
                @endif

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#navbar-collapse">
                    <i class="fa fa-bars"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="{{ is_current_route('home') }}"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li class="{{ is_current_route('exchange.index') }}"><a
                                href="{{ route('exchange.index') }}">{{ __('Exchange') }}</a></li>
                    <li class="{{ is_current_route('exchange.ico.index') }}"><a
                                href="{{ route('exchange.ico.index') }}">{{ __('ICO') }}</a></li>
                    <li class="{{ is_current_route('trading-views.index') }}"><a
                                href="{{ route('trading-views.index') }}">{{ __('Trading View') }}</a></li>
                    <li class="{{ is_current_route('faq.index') }}"><a
                                href="{{ route('faq.index') }}">{{ __('FAQ') }}</a></li>
                </ul>
            </div>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                @auth
                    <!-- User Account: style can be found in dropdown.less -->
                        @php
                            $userNotifications = get_user_specific_notice();
                        @endphp
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning">{{ $userNotifications['count_unread'] }}</span>
                            </a>
                            @if(!$userNotifications['list']->isEmpty())
                                <ul class="dropdown-menu">
                                    <li class="header text-bold">{{ __('You have :count notifications',['count' => $userNotifications['count_unread']]) }}</li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            @foreach($userNotifications['list'] as $notification)
                                                <li>
                                                    <a><i class="fa fa-bell text-orange"></i><span
                                                                style="color: #000000">{{ str_limit($notification->data, 50) }}</span></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                    <li class="footer"><a class="bg-green-active" style="color: #FFFFFF !important"
                                                          href="{{ route('notices.index') }}">View all</a></li>
                                </ul>
                            @endif
                        </li>
                        <li class="user user-menu">
                            <a href="{{ route('profile.index') }}">
                                <img src="{{ get_avatar(Auth::user()->avatar) }}" class="user-image img-circle"
                                     alt="User Image">
                                <span class="hidden-xs cm-ml-5">{{ Auth::user()->userInfo->full_name }}</span>
                            </a>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <li>
                            <a href="{{ route('logout') }}"><i class="fa fa-sign-out"></i></a>
                        </li>
                    @endauth
                    @guest
                        <li>
                            <a href="{{ route('login') }}">{{__('Login')}}</a>
                        </li>

                        <li>
                            <a href="{{ route('register.index') }}">{{ __('Register') }}</a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>