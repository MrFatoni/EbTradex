<input type="hidden" name="base_key" value="{{ base_key() }}">

{{--item--}}
<div class="form-group {{ $errors->has('item') ? 'has-error' : '' }}">
    <label for="{{ fake_field('item') }}" class="col-md-4 control-label required">{{ __('Item') }}</label>
    <div class="col-md-8">
        {{ Form::text(fake_field('item'),  old('item', null), ['class'=>'form-control', 'id' => fake_field('item'),'data-cval-name' => 'The item field','data-cval-rules' => 'required|escapeInput|max:255', 'placeholder' => __('ex: USD')]) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('item') }}">{{ $errors->first('item') }}</span>
    </div>
</div>

{{--item_name--}}
<div class="form-group {{ $errors->has('item_name') ? 'has-error' : '' }}">
    <label for="{{ fake_field('item_name') }}" class="col-md-4 control-label required">{{ __('Item Name') }}</label>
    <div class="col-md-8">
        {{ Form::text(fake_field('item_name'),  old('item_name', null), ['class'=>'form-control', 'id' => fake_field('item_name'),'data-cval-name' => 'The item name field','data-cval-rules' => 'required|escapeInput|max:255', 'placeholder' => __('ex: United States Dollar')]) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('item_name') }}">{{ $errors->first('item_name') }}</span>
    </div>
</div>

{{--item_type--}}
<div class="form-group {{ $errors->has('item_type') ? 'has-error' : '' }}">
    <label for="{{ fake_field('item_type') }}" class="col-md-4 control-label required">{{ __('Item Type') }}</label>
    <div class="col-md-8">
        {{ Form::select(fake_field('item_type'), stock_item_types(), old('item_type', null),['class' => 'form-control','id' => fake_field('item_type'), 'placeholder' => __('Select Stock Item Type'),'data-cval-name' => 'The item type field','data-cval-rules' => 'required|in:'.array_to_string(stock_item_types()), 'v-on:change' => 'changeItemType']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('item_type') }}">{{ $errors->first('item_type') }}</span>
    </div>
</div>

{{--item_emoji--}}
<div class="form-group {{ $errors->has('item_emoji') ? 'has-error' : '' }}">
    <label for="{{ fake_field('item_emoji') }}" class="col-md-4 control-label required">{{ __('Item Emoji') }}</label>
    <div class="col-md-8">
        <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-new thumbnail">
                <i class="fa fa-money text-green"></i>
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
            <div>
                <span class="btn btn-default btn-file">
                    <span class="fileinput-new">{{ __('Select Emoji') }}</span>
                    <span class="fileinput-exists">{{ __('Change') }}</span>
                    {{ Form::file('item_emoji', ['class' => '','id' => fake_field('item_emoji'),'data-cval-name' => 'The item emoji field','data-cval-rules' => 'files:jpg,png,jpeg|max:1024']) }}
                </span>
                <a href="javascript:;" class="btn btn-default fileinput-exists" data-dismiss="fileinput">{{ __('Remove') }}</a>
            </div>
        </div>
        <p class="help-block">{{ __('Upload item emoji 100x100 and less than or equal 1MB.') }}</p>

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('item_emoji') }}">{{ $errors->first('item_emoji') }}</span>
    </div>
</div>

{{--is_active--}}
<div class="form-group {{ $errors->has('is_active') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_active') }}" class="col-md-4 control-label required">{{ __('Active Status') }}</label>
    <div class="col-md-8">
        <div class="cm-switch">
            {{ Form::radio(fake_field('is_active'), ACTIVE_STATUS_ACTIVE, true, ['id' => fake_field('is_active') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The active status field', 'data-cval-rules' => 'required|integer|in:' . array_to_string(active_status())]) }}
            <label for="{{ fake_field('is_active') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

            {{ Form::radio(fake_field('is_active'), ACTIVE_STATUS_INACTIVE, false, ['id' => fake_field('is_active') . '-inactive', 'class' => 'cm-switch-input']) }}
            <label for="{{ fake_field('is_active') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
        </div>
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_active') }}">{{ $errors->first('is_active') }}</span>
    </div>
</div>

{{--is_ico--}}
<div class="form-group {{ $errors->has('is_ico') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_ico') }}" class="col-md-4 control-label required">{{ __('Is ICO') }}</label>
    <div class="col-md-8">
        <div class="cm-switch">
            {{ Form::radio(fake_field('is_ico'), ACTIVE_STATUS_ACTIVE, false, ['id' => fake_field('is_ico') . '-yes', 'class' => 'cm-switch-input', 'data-cval-name' => 'The ICO field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status()), 'v-model' => 'hideIcoOptionFields']) }}
            <label for="{{ fake_field('is_ico') }}-yes" class="cm-switch-label">{{ __('Yes') }}</label>

            {{ Form::radio(fake_field('is_ico'), ACTIVE_STATUS_INACTIVE, true, ['id' => fake_field('is_ico') . '-no', 'class' => 'cm-switch-input', 'v-model' => 'hideIcoOptionFields']) }}
            <label for="{{ fake_field('is_ico') }}-no" class="cm-switch-label">{{ __('No') }}</label>
        </div>
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_ico') }}">{{ $errors->first('is_ico') }}</span>
    </div>
</div>
<div v-if="hideIcoOptionFields == 0">
    {{--exchange_status--}}
    <div class="form-group {{ $errors->has('exchange_status') ? 'has-error' : '' }}">
        <label for="{{ fake_field('exchange_status') }}" class="col-md-4 control-label required">{{ __('Exchange Status') }}</label>
        <div class="col-md-8">
            <div class="cm-switch">
                {{ Form::radio(fake_field('exchange_status'), ACTIVE_STATUS_ACTIVE, old('exchange_status', true), ['id' => fake_field('exchange_status') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The exchange status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                <label for="{{ fake_field('exchange_status') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                {{ Form::radio(fake_field('exchange_status'), ACTIVE_STATUS_INACTIVE, old('exchange_status', false), ['id' => fake_field('exchange_status') . '-inactive', 'class' => 'cm-switch-input']) }}
                <label for="{{ fake_field('exchange_status') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
            </div>
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('exchange_status') }}">{{ $errors->first('exchange_status') }}</span>
        </div>
    </div>

    {{--is_fee_applicable--}}
    {{--    <div class="form-group {{ $errors->has('is_fee_applicable') ? 'has-error' : '' }}">--}}
    {{--        <label for="{{ fake_field('is_fee_applicable') }}" class="col-md-4 control-label required">{{ __('Is Exchange Fee Applicable?') }}</label>--}}
    {{--        <div class="col-md-8">--}}
    {{--            <div class="cm-switch">--}}
    {{--                {{ Form::radio(fake_field('is_fee_applicable'), ACTIVE_STATUS_ACTIVE, true, ['id' => fake_field('is_fee_applicable') . '-yes', 'class' => 'cm-switch-input', 'data-cval-name' => 'The fee applicable field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}--}}
    {{--                <label for="{{ fake_field('is_fee_applicable') }}-yes" class="cm-switch-label">{{ __('Yes') }}</label>--}}

    {{--                {{ Form::radio(fake_field('is_fee_applicable'), ACTIVE_STATUS_INACTIVE, false, ['id' => fake_field('is_fee_applicable') . '-no', 'class' => 'cm-switch-input']) }}--}}
    {{--                <label for="{{ fake_field('is_fee_applicable') }}-no" class="cm-switch-label">{{ __('No') }}</label>--}}
    {{--            </div>--}}
    {{--            <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_fee_applicable') }}">{{ $errors->first('is_fee_applicable') }}</span>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    <div v-if="showOptionalFields">
        {{--deposit_status--}}
        <div class="form-group {{ $errors->has('deposit_status') ? 'has-error' : '' }}">
            <label for="{{ fake_field('deposit_status') }}" class="col-md-4 control-label required">{{ __('Deposit Status') }}</label>
            <div class="col-md-8">
                <div class="cm-switch">
                    {{ Form::radio(fake_field('deposit_status'), ACTIVE_STATUS_ACTIVE, false, ['id' => fake_field('deposit_status') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The deposit status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                    <label for="{{ fake_field('deposit_status') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                    {{ Form::radio(fake_field('deposit_status'), ACTIVE_STATUS_INACTIVE, true, ['id' => fake_field('deposit_status') . '-inactive', 'class' => 'cm-switch-input']) }}
                    <label for="{{ fake_field('deposit_status') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
                </div>

                <span class="validation-message cval-error" data-cval-error="{{ fake_field('deposit_status') }}">{{ $errors->first('deposit_status') }}</span>
            </div>
        </div>

        <div class="form-group {{ $errors->has('deposit_fee') ? 'has-error' : '' }}">
            <label for="{{ fake_field('deposit_fee') }}" class="col-md-4 control-label required">{{ __('Deposit Fee') }}</label>
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::text(fake_field('deposit_fee'),  old('deposit_fee', 0), ['class'=>'form-control', 'id' => fake_field('deposit_fee'),'data-cval-name' => 'The deposit fee field','data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99', 'placeholder' => __('ex: 0.01')]) }}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('deposit_fee') }}">{{ $errors->first('deposit_fee') }}</span>
            </div>
        </div>

        {{--withdrawal_status--}}
        <div class="form-group {{ $errors->has('withdrawal_status') ? 'has-error' : '' }}">
            <label for="{{ fake_field('withdrawal_status') }}" class="col-md-4 control-label required">{{ __('Withdrawal Status') }}</label>
            <div class="col-md-8">
                <div class="cm-switch">
                    {{ Form::radio(fake_field('withdrawal_status'), ACTIVE_STATUS_ACTIVE, false, ['id' => fake_field('withdrawal_status') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The withdrawal status field', 'data-cval-rules' => 'integer|in:' . array_to_string(active_status())]) }}
                    <label for="{{ fake_field('withdrawal_status') }}-active" class="cm-switch-label">{{ __('Active') }}</label>

                    {{ Form::radio(fake_field('withdrawal_status'), ACTIVE_STATUS_INACTIVE, true, ['id' => fake_field('withdrawal_status') . '-inactive', 'class' => 'cm-switch-input']) }}
                    <label for="{{ fake_field('withdrawal_status') }}-inactive" class="cm-switch-label">{{ __('Inactive') }}</label>
                </div>

                <span class="validation-message cval-error" data-cval-error="{{ fake_field('withdrawal_status') }}">{{ $errors->first('withdrawal_status') }}</span>
            </div>
        </div>

        {{--minimum_withdrawal_amount--}}
        <div class="form-group {{ $errors->has('minimum_withdrawal_amount') ? 'has-error' : '' }}">
            <label for="{{ fake_field('minimum_withdrawal_amount') }}" class="col-md-4 control-label required">{{ __('Minimum Withdrawal Amount') }}</label>
            <div class="col-md-8">
                {{ Form::text(fake_field('minimum_withdrawal_amount'),  old('minimum_withdrawal_amount', null), ['class'=>'form-control', 'id' => fake_field('minimum_withdrawal_amount'),'data-cval-name' => 'The minimum withdrawal amount field','data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99999999', 'placeholder' => __('ex: 0.0005')]) }}

                <span class="validation-message cval-error" data-cval-error="{{ fake_field('minimum_withdrawal_amount') }}">{{ $errors->first('minimum_withdrawal_amount') }}</span>
            </div>
        </div>

        {{--daily_withdrawal_limit--}}
        <div class="form-group {{ $errors->has('daily_withdrawal_limit') ? 'has-error' : '' }}">
            <label for="{{ fake_field('daily_withdrawal_limit') }}" class="col-md-4 control-label required">{{ __('Daily Withdrawal Limit') }}</label>
            <div class="col-md-8">
                {{ Form::text(fake_field('daily_withdrawal_limit'),  old('daily_withdrawal_limit', 25000), ['class'=>'form-control', 'id' => fake_field('daily_withdrawal_limit'),'data-cval-name' => 'The daily withdrawal limit field','data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99999999', 'placeholder' => __('ex: 25')]) }}

                <span class="validation-message cval-error" data-cval-error="{{ fake_field('daily_withdrawal_limit') }}">{{ $errors->first('daily_withdrawal_limit') }}</span>
            </div>
        </div>

        {{--withdrawal_fee--}}
        <div class="form-group {{ $errors->has('withdrawal_fee') ? 'has-error' : '' }}">
            <label for="{{ fake_field('withdrawal_fee') }}" class="col-md-4 control-label required">{{ __('Withdrawal Fee') }}</label>
            <div class="col-md-8">
                <div class="input-group">
                    {{ Form::text(fake_field('withdrawal_fee'),  old('withdrawal_fee', 0), ['class'=>'form-control', 'id' => fake_field('withdrawal_fee'),'data-cval-name' => 'The withdrawal fee field','data-cval-rules' => 'numeric|escapeInput|between:0, 99999999999.99', 'placeholder' => __('ex: 0.01')]) }}
                    <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                </div>
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('withdrawal_fee') }}">{{ $errors->first('withdrawal_fee') }}</span>
            </div>
        </div>

        {{--api_service--}}
        <div class="form-group {{ $errors->has('api_service') ? 'has-error' : '' }}">
            <label for="api-services" class="col-md-4 control-label required">{{ __('API Service') }}</label>
            <div class="col-md-8">
                <select class="form-control" id="api-services" data-cval-name="{{ __('The API service field') }}" data-cval-rules="require" name="{{ fake_field('api_service') }}">
                    <option value="">{{ __('Select API Service') }}</option>
                    <option v-for="(api, index) in apis" v-bind:value="index" v-text="api"></option>
                </select>

                <span class="validation-message cval-error" data-cval-error="{{ fake_field('api_service') }}">{{ $errors->first('api_service') }}</span>
            </div>
        </div>
    </div>
</div>

{{--submit button--}}
<div class="form-group">
    <div class="col-md-offset-4 col-md-8">
        {{ Form::submit(__('Create'),['class'=>'btn btn-success form-submission-button']) }}
        {{ Form::reset(__('Reset'),['class'=>'btn btn-danger']) }}
    </div>
</div>