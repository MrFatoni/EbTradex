<header class="main-header">
    <!-- Logo -->
    <!-- <a href="index2.html" class="logo"> -->
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <!-- <span class="logo-mini"><b>A</b>LT</span> -->
    <!-- logo for regular state and mobile devices -->
    <!-- <span class="logo-lg"><b>Admin</b>LTE</span> -->
    <!-- </a> -->
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <!-- <a href="javascript:;" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a> -->

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
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
                                        <a><i class="fa fa-bell text-orange"></i><span style="color: #000000">{{ str_limit($notification->data, 50) }}</span></a>
                                    </li>
                                        @endforeach
                                </ul>
                            </li>
                            <li class="footer"><a class="bg-green-active" style="color: #FFFFFF !important" href="{{ route('notices.index') }}">View all</a></li>
                        </ul>
                    @endif
                </li>
                <li class="user user-menu">
                    <a href="{{ route('profile.index') }}">
                        <img src="{{ get_avatar(Auth::user()->avatar) }}" class="user-image img-circle" alt="User Image">
                        <span class="hidden-xs cm-ml-5">{{ Auth::user()->userInfo->full_name }}</span>
                    </a>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li>
                    <a href="{{ route('logout') }}"><i class="fa fa-sign-out"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>