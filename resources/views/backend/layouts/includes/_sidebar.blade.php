<aside class="main-sidebar">
    <div class="sidebar-inside">
        <!-- <div class="menu-text show-in-nav-expanded">Back Panel</div> -->
        <div class="logo-panel">
            <div class="image">
                @if(admin_settings('company_logo'))
                    <img src="{{ get_image(admin_settings('company_logo')) }}" class="img-responsive logo-big show-in-nav-expanded">
                @else
                    <a style="color: #fefefe;text-transform: uppercase" href="{{ route('dashboard') }}" class="navbar-brand "><b>{{ env('APP_NAME') }}</b></a>
                @endif

            <!-- <img src="assets/images/logo-small.png" alt="User Image" class="img-responsive logo-small show-in-nav-collapsed"> -->
            </div>
        </div>
        <a href="javascript:" class="sidebar-toggle" data-toggle="push-menu" role="button"></a>
    </div>
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- sidebar menu: : style can be found in sidebar.less -->
        {!! get_nav('back-end') !!}
        {{--<ul class="sidebar-menu" data-widget="tree">
            <li class="active treeview">
                <a href="javascript:;">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                    <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                </a>
                <ul class="treeview-menu">
                    <li class="active"><a href="index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
                    <li><a href="index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a></li>
                </ul>
            </li>

        </ul>--}}
    </section>
    <!-- /.sidebar -->
</aside>