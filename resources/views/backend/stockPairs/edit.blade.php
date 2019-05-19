@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Edit Stock Pair') }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('admin.stock-pairs.index') }}" class="btn btn-primary back-button">{{ __('Back to list') }}</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-8">
                    {!! Form::open(['route'=>['admin.stock-pairs.update', $stockPair->id], 'class'=>'form-horizontal validator', 'enctype'=>'multipart/form-data']) !!}
                    @method('PUT')
                    @include('backend.stockPairs._edit_form')
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });
    </script>
@endsection