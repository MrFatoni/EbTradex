@extends('backend.layouts.main_layout')
@section('title', $title)
@section('after-style')
    <link rel="stylesheet" href="{{asset('backend/assets/css/menu.css')}}">
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Select Nav</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="form-group">
                        <ul class="list-unstyled" style="overflow-y: scroll; max-height: 150px;">
                            @foreach($navigationPlaces as $navigationPlace)
                            <li><a href="{{route('menu-manager.index',$navigationPlace)}}">{{ucfirst(str_replace('-',' ',$navigationPlace))}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Routes</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div id="all-routes" class="box-body" style="overflow-y: scroll; max-height: 150px;" data-name="Unnamed">
                    @foreach($allRoutes as $routeName => $routeData)
                        @php
                            $middleware = $routeData->middleware();
                            $parameters = $routeData->signatureParameters();
                            $isMenuable = true;
                        @endphp
                        @foreach($parameters as $parameter)
                            @if(!$parameter->isOptional())
                                @php($isMenuable = false)
                                @break
                            @endif
                        @endforeach
                        @if($isMenuable && !empty($middleware) && !in_array('api', $middleware) && !in_array('Barryvdh\Debugbar\Middleware\DebugbarEnabled',$middleware))
                            <?php
                            $route = explode('/{', $routeName)[0];
                            if ($route == '/' || $route == '' || strlen($route) == 2) {
                                $route = 'Home';
                            } else {
                                if (strpos($route, '/') == 2) {
                                    $route = substr($route, 3);
                                }
                                $route = ucfirst(str_replace('/', ' - ', str_replace('-', ' ', $route)));
                            }
                            ?>
                            <div class="checkbox" style="margin:3px 0;">
                                <label>
                                    <input type="checkbox" class="route-check-box" value="{{$routeData->getName()}}"> <span>{{$route}}</span>
                                </label>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" id="add-route">Add Route</button>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add LINK</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <div class="box-body">
                    <div class="form-group">
                        <input type="text" id="link-data" placeholder="Enter url" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" data-name="Unnamed" id="link-name" placeholder="Enter Menu Item Name" class="form-control">
                    </div>
                </div>
                <div class="box-footer">
                    <button class="btn btn-primary" id="add-link">Add A custom Link</button>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="box box-primary" style="min-height: 636px;">
                <div class="box-header with-border">
                    <h3 class="box-title">Menu ITEMS</h3>
                </div>
                {{ Form::open(['route'=>['menu-manager.save', $slug], 'method'=>'post','id'=>'menu-form']) }}
                <div class="box-header with-border">
                    {{--<button class="btn btn-primary menu-submit" type="submit">Save Menu</button>--}}
                </div>
                <div class="box-body">
                    <div style="overflow:hidden; width:100%;">
                        {!! $menu !!}
                    </div>
                </div>
                    <button id="form-submit-button" type="submit" style="display:none">Save Menu</button>
                {{ Form::close() }}
                <div class="box-footer">
                    <button class="btn btn-primary menu-submit">Save Menu</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="{{asset('backend/vendors/menu_manager/jquery.mjs.nestedSortable.js')}}"></script>
    <script src="{{asset('backend/vendors/menu_manager/adminmenuhandler.js')}}"></script>
@endsection