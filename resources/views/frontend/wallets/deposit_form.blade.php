@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary box-borderless">
                @if($wallet->stockItem->deposit_status == ACTIVE_STATUS_ACTIVE)
                    <div class="box-header text-center with-border">
                        <h3 class="box-title font-weight-bold">
                            {{ __('Deposit :stockItem', ['stockItem' => $wallet->stockItem->item]) }}
                        </h3>
                    </div>
                    <div class="box-body">
                        {!! Form::open(['route'=>['trader.wallets.deposit.store', $wallet->id], 'method' => 'post', 'class'=>'form-horizontal validator']) !!}
                            {{ Form::hidden('base_key', base_key()) }}
                            {{--stock_item_id--}}
                            <div class="form-group {{ $errors->has('stock_item_id') ? 'has-error' : '' }}">
                                <label class="col-md-2 control-label required">{{ __('Currency') }}</label>
                                <div class="col-md-10">
                                    <p class="form-control-static strong">{{ $wallet->stockItem->stock_item_name }}</p>
                                </div>
                            </div>

                            {{--amount--}}
                            <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                                <label for="{{ fake_field('amount') }}" class="col-md-2 control-label required">{{ __('Amount') }}</label>
                                <div class="col-md-10">
                                    {{ Form::text(fake_field('amount'),  old('amount', null), ['class'=>'form-control', 'id' => fake_field('amount'),'data-cval-name' => 'The amount field','data-cval-rules' => 'required|numeric|escapeInput|between:0.01, 99999999999.99', 'placeholder' => __('ex: 20.99')]) }}
                                    <span class="validation-message cval-error" data-cval-error="{{ fake_field('amount') }}">{{ $errors->first('amount') }}</span>
                                </div>
                            </div>

                            {{--accept_policy--}}
                            <div class="form-group {{ $errors->has('accept_policy') ? 'has-error' : '' }}">
                                <label class="col-md-2 control-label"></label>
                                <div class="col-md-10">
                                    <div class="checkbox">
                                        <label>
                                            {{ Form::checkbox('accept_policy', 1, false, ['data-cval-name' => 'The deposit policy checking','data-cval-rules' => 'required|in:1']) }}
                                            {{ __("Check to accept deposit's policy.") }}
                                        </label>
                                    </div>
                                    <span class="validation-message cval-error" data-cval-error="{{ fake_field('accept_policy') }}">{{ $errors->first('accept_policy') }}</span>
                                </div>
                            </div>

                            {{--submit button--}}
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-10">
                                    {{ Form::submit(__('Deposit Balance'),['class'=>'btn btn-success form-submission-button']) }}
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                @else
                    <div class="box-body text-center">
                        <h4 class="bg-gray-light py-5 font-weight-bold strong" data-toggle="tooltip" data-placement="top" title="{{ __('Click to copy') }}">
                            {{ __('Deposit is currently disabled.') }}
                        </h4>
                    </div>
                @endif
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