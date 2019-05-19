@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Create New Notice') }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('system-notices.index') }}" class="btn btn-primary back-button">{{ __('Back') }}</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-8">
                    {!! Form::open(['route'=>'system-notices.store', 'method' => 'post', 'class'=>'form-horizontal system-notice-form']) !!}
                    @include('backend.systemNotice._form',['buttonText'=> __('Create')])
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after-style')
    <link rel="stylesheet" href="{{ asset('common/vendors/datetimepicker/css/bootstrap-datetimepicker.min.css') }}">
@endsection

@section('script')
    <!-- for datatable and date picker -->
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('backend/assets/js/moment.js') }}"></script>
    <script src="{{ asset('common/vendors/datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            //Init jquery Date Picker
            $('#start_time').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });

            $('#end_time').datetimepicker({
                format: 'YYYY-MM-DD HH:mm:ss'
            });

            $("#start_time").on("dp.change", function (e) {
                $('#end_time').data("DateTimePicker").minDate(e.date);
            });
            $("#end_time").on("dp.change", function (e) {
                $('#start_time').data("DateTimePicker").maxDate(e.date);
            });

            $('.system-notice-form').cValidate({});
        });
    </script>
@endsection