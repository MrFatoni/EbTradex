@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ __('Edit Stock Item') }}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('admin.stock-items.index') }}" class="btn btn-primary back-button">{{ __('Back to list') }}</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-8">
                    {!! Form::open(['route'=>['admin.stock-items.update', $stockItem->id], 'class'=>'form-horizontal validator', 'enctype'=>'multipart/form-data']) !!}
                    @method('PUT')
                    @include('backend.stockItems._edit_form')
                    {!! Form::close() !!}
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
    <script src="{{ asset('common/vendors/cvalidator/cvalidator.js') }}"></script>
    <script src="{{ asset('common/vendors/bootstrap-fileinput/js/jasny-bootstrap.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.validator').cValidate({});
        });
        new Vue({
            el: '#app',
            data: {
                showOptionalFields : {{ in_array(old('item_type', $stockItem->item_type), config('commonconfig.currency_transferable')) ? 'true' : 'false' }},
                hideIcoOptionFields: {{ $stockItem->is_ico == ACTIVE_STATUS_ACTIVE ? 1 : 0 }},
                itemTypes: @json(config('commonconfig.currency_transferable')),
                cryptoApis: @json(crypto_currency_api_services()),
                realApis: @json(real_currency_api_services()),
                apis: @json(api_services())
            },
            methods: {
                changeItemType: function (event) {
                    let itemTypeValue = parseInt(event.target.value);

                    if(itemTypeValue == {{ CURRENCY_CRYPTO }})
                    {
                        this.apis = this.cryptoApis;
                    }
                    else if(itemTypeValue == {{ CURRENCY_REAL }})
                    {
                        this.apis = this.realApis;
                    }
                    else
                    {
                        this.apis = @json(api_services());
                    }

                    this.showOptionalFields = this.itemTypes.indexOf(itemTypeValue) > -1 ? true : false;
                }
            },
            created: function () {
                if({{ old('item_type', $stockItem->item_type) }} == {{ CURRENCY_CRYPTO }})
                {
                    this.apis = this.cryptoApis;
                }
                else if({{ old('item_type', $stockItem->item_type) }} == {{ CURRENCY_REAL }})
                {
                    this.apis = this.realApis;
                }
                else {
                    this.apis = @json(api_services());
                }
            }
        });
    </script>
@endsection