@include('backend.layouts.includes._header')
<body class="skin-blue layout-top-nav">
    <div class="wrapper" id="app">
@include('backend.layouts.includes._top_navigation_header')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
        <!-- Extended content -->
        @yield('extended-content')
    </div>
    <!-- /.content-wrapper -->
@include('backend.layouts.includes._footer')
