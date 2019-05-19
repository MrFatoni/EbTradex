@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-header with-border">
                    <div class="user-block">
                        <img class="img-circle" src="{{ get_avatar($question->user->avatar) }}" alt="User Image">
                        <span class="username"><a href="">{{ $question->user->userInfo->full_name }}</a></span>
                        <span class="description">{{ __('Asked at :time',['time'=>$question->created_at->toDayDateTimeString()]) }}</span>
                    </div>
                </div>
                <div class="box-body">
                    <h3 class="cm-mt-5 cm-mb-5 h4 strong">{{ $question->title }}</h3>
                    <p>{!!  $question->content !!}</p>

                    <div class="box-comments cm-mt-40 cm-p-15">
                        <h4>{{ __('Answers') }}({{ $question->comments->count() }}):</h4>
                        <hr class="no-padding">
                        @foreach($question->comments as $comment)
                            <div class="box-comment">
                                <!-- User image -->
                                <img class="img-circle img-sm" src="{{ get_avatar($comment->user->avatar) }}"
                                     alt="Analyst Image">

                                <div class="comment-text">
                                    <span class="username">{{ $comment->user->userInfo->full_name }}
                                        <span class="text-muted pull-right">{{ $comment->created_at->toDayDateTimeString() }}</span>
                                    </span>
                                    <p>{!!  $comment->content !!}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="box-footer">
                    {{ Form::open(['route'=>['trade-analyst.questions.answer', $question->id], 'method' => 'post', 'class'=>'form-horizontal validator','files'=> true]) }}
                    @include('backend.questions._form_answer',['buttonText' => __('Answer')])
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="{{ asset('common/vendors/tinymce/tinymce.min.js') }}"></script>
    <script>

        tinymce.init({
            selector: "#content_textarea",
            menubar: false,
            theme: "modern",
            relative_urls: false,
            force_div_newlines: true,
            force_h1_newlines: true,
            force_h2_newlines: true,
            force_h3_newlines: true,
            force_h4_newlines: true,
            force_h5_newlines: true,
            force_h6_newlines: true,
            force_ul_newlines: true,
            force_ol_newlines: true,
            force_li_newlines: true,
            force_hr_newlines: true,
            forced_br_newlines: true,
            forced_p_newlines: false,
            forced_root_block: false,
            remove_linebreaks: true,
            convert_urls: false,
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor colorpicker textpattern"
            ],
            toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | print preview | forecolor backcolor | code link image",
            image_advtab: false,
        });

    </script>
@endsection

