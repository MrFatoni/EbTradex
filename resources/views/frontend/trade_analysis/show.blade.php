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
                <div class="col-md-5 col-md-offset-1">
                    <img src="{{get_post_image($post->featured_image)}}" alt="" style="margin-bottom: 30px">
                </div>
                <div class="col-md-5">
                    <h3 style="margin-top: 0" class="analysis-title">{{$post->title}}</h3>
                    <div><i class="fa fa-pencil-square-o"></i> {{$post->user->userInfo->full_name}}<span style="margin:0 5px;"></span><i
                                class="fa fa-clock-o"></i> {{$post->created_at}}<span style="margin:0 5px;"></span><i
                                class="fa fa-comment-o"></i> {{$post->comments->count()}}</div>
                    <p style="margin-top:30px">{!! $post->content !!}</p>
                    <div style="padding-top:40px">
                        <h2 class="title-center cm-mt-5 cm-mb-15">{{__('Comments')}} ({{$post->comments->count()}})</h2>
                        <div class="chat">
                            @forelse($post->comments->sortByDesc('id') as $comment)
                                <div class="item">
                                    <img src="{{get_avatar($comment->user->avatar)}}" alt="user image" class="offline"
                                         style="border:none; border-radius:0">

                                    <p class="message">
                                        <a href="javascript:" class="name">
                                            <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> {{$comment->created_at}}</small>
                                            {{$comment->user->userInfo->full_name}}
                                        </a>
                                        {!! $comment->content !!}
                                    </p>
                                </div>
                            @empty
                                <h3>{{__('NO COMMENT FOUND')}}</h3>
                            @endforelse
                        </div>
                        <hr class="cm-mt-15 cm-mb-15">
                        @auth
                            {!! Form::open(['route'=>['trading-views.comment', $post->id], 'class'=>'validator']) !!}
                            <input type="hidden" name="base_key" value="{{ base_key() }}">
                            <textarea class="form-control" name="{{fake_field('content')}}" rows="3">{{old(fake_field('content'))}}</textarea>
                            {{ Form::submit(__('Submit Comment'), ['class'=>'btn btn-success form-submission-button cm-mt-10']) }}
                            {!! Form::close() !!}
                        @endauth
                        @guest
                            <p>{!! __('Please :login to comment this post',['login'=>'<a href="'.route('login').'">login</a>']) !!}</p>
                        @endguest
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