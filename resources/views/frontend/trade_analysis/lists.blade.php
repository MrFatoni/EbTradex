@extends('backend.layouts.top_navigation_layout')
@section('title', $title)
@section('after-style')
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
@endsection
@section('content')
    <div class="fullwidth" style="background: #fff">


        <div class="container">
            <div style="padding-top:40px">
                <h2 class="title-center">{{$title}}</h2>
            </div>
            {!! $posts['filters'] !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="row dc-clear">
                        @foreach($posts['query'] as $post)
                        <div class="col-sm-4">
                            <div class="trade-analysis">
                                <img src="{{get_post_image($post->featured_image)}}" alt="">
                                <div style="overflow: hidden"><a href="{{route('trading-views.show',$post->id)}}" class="analysis-title">{{$post->title}}</a></div>
                                <div><i class="fa fa-pencil-square-o"></i> {{$post->first_name}} {{$post->last_name}}<span style="margin:0 5px;"></span><i class="fa fa-clock-o"></i> {{$post->created_at}}<span style="margin:0 5px;"></span><i class="fa fa-comment-o"></i> {{$post->comments}}</div>
                                <p>{!!  str_limit($post->content, 150) !!}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="padding-top:20px"></div>
            {!! $posts['pagination'] !!}
            <div style="padding-top:40px"></div>
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