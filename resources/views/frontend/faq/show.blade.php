@extends('backend.layouts.top_navigation_layout')
@section('title', $title)
@section('after-style')
    <link rel="stylesheet" href="{{asset('frontend/style.css')}}">
@endsection
@section('content')
    <div class="fullwidth" style="background: #fff">
        <div class="container">
            <div style="padding-top:40px">
            </div>

            <div class="row">
                <div class=" col-md-offset-2 col-sm-8">
                    <div class="post clearfix">
                        <div class="user-block">
                            <img class="img-circle" src="{{get_avatar($question->avatar)}}" alt="User Image">
                            <span class="username">
                                            <span style="font-weight: normal;font-size:13px">
                                                {{$question->user->userInfo->full_name}}
                                            </span>
                                        </span>
                            <span class="description">
                                            {{ __('Asked at :time', ['time'=>$question->created_at->toDayDateTimeString()]) }}
                                            <span CLASS="pull-right">
                                                <i class="fa fa-comment-o"></i> {{$question->comments->count()}}
                                            </span>
                                        </span>
                        </div>

                        <!-- /.user-block -->
                        <div class="box box-primary box-borderless bg-gray-light">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{ $question->title }}</h4>
                            </div>
                            <div class="box-body text-black">
                                {!!  $question->content !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>{{ __('Answers') }} ({{$question->comments->count()}})</h4>
                            <hr>
                            <div class="chat">
                                @forelse($question->comments->sortByDesc('id') as $comment)
                                    <div class="item">
                                        <img src="{{get_avatar($comment->user->avatar)}}" alt="user image" class="offline"
                                             style="border:none; border-radius:0">

                                        <p class="message">
                                            <a class="name">
                                                {{$comment->user->userInfo->full_name}}
                                            </a>
                                            <small class="text-muted"><i class="fa fa-clock-o"></i> {{$comment->created_at}}</small>
                                        </p>
                                        <div class="box bg-gray-light box-borderless">
                                            <div class="box-body">
                                                {!! $comment->content !!}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <h3 class="text-center">{{__('NO ANSWER FOUND')}}</h3>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding-top: 40px"></div>
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