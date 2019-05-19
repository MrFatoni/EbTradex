@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-primary box-borderless">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2">
                            {{ Form::model($post, ['route'=>['trade-analyst.posts.update', $post->id], 'method' => 'post', 'class'=>'form-horizontal validator','files'=> true]) }}
                            @method('PUT')
                            @include('backend.posts._form',['buttonText' => __('Update')])
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('before-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/bootstrap-fileinput/css/jasny-bootstrap.css') }}">
@endsection

@section('after-style')
    <style>
        .thumbnail {
            width: 100px; height: 100px; line-height:100px;
        }

        .thumbnail i{
            font-size: 50px;
        }
    </style>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('common/vendors/bootstrap-fileinput/js/jasny-bootstrap.js') }}"></script>
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
            toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent",
            toolbar2: "print preview | forecolor backcolor | code link image",
            image_advtab: false,
        });


        $(document).ready(function () {
            $('.validator').cValidate();

        });
    </script>
@endsection

