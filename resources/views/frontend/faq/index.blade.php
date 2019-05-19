@extends('backend.layouts.top_navigation_layout')
@section('title', $title)
@section('after-style')
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
    <style>
        .cm-filter > .cm-search-filter input {
            width: 261px !important;
        }

        .cm-filter > div {
            float: none;
            width: 300px !important;
            margin: 5px auto;
        }

        .box.box-primary {
            border: none !important;
            box-shadow: none !important;
        }
    </style>
@endsection
@section('content')
    <div class="fullwidth" style="background: #fff">


        <div class="container">
            <div style="padding-top:40px">
                <h2 class="title-center">{{$title}}</h2>
            </div>
            {!! $questions['filters'] !!}

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        @foreach($questions['query'] as $question)
                            <div class="col-sm-offset-2 col-sm-8">
                                <hr>
                                <div class="post clearfix">
                                    <div class="user-block">
                                        <img class="img-circle" src="{{ get_avatar($question->avatar) }}" alt="User Image">
                                        <span class="username">
                                            <span style="font-weight: normal;font-size:13px">
                                                {{$question->first_name}} {{$question->last_name}}
                                            </span>
                                        </span>
                                        <span class="description">
                                            {{ __('Asked at :time', ['time'=>$question->created_at->toDayDateTimeString()]) }}
                                            <span CLASS="pull-right">
                                                <i class="fa fa-comment-o"></i> {{$question->comments}}
                                            </span>
                                        </span>
                                    </div>

                                    <h4>
                                        <a href="{{route('faq.show',$question->id)}}" style="font-weight:bold; font-size:16px;">
                                            {{$question->title}}
                                        </a>
                                    </h4>
                                    {{strip_tags($question->content)}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="padding-top:20px"></div>
            {!! $questions['pagination'] !!}
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