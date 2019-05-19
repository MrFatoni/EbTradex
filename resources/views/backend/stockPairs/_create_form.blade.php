<input type="hidden" name="base_key" value="{{ base_key() }}">

{{--stock_item_id--}}
<div class="form-group {{ $errors->has('stock_item_id') ? 'has-error' : '' }}">
    <label for="{{ fake_field('stock_item_id') }}" class="col-md-4 control-label required">{{ __('Exchangeable Item') }}</label>
    <div class="col-md-8">
        {{ Form::select(fake_field('stock_item_id'), $stockItems, old('stock_item_id', null), ['class' => 'form-control', 'id' => fake_field('stock_item_id'), 'placeholder' => __('Select Exchangeable Item'), 'data-cval-name' => 'The exchangable item field','data-cval-rules' => 'required|in:' . array_to_string($stockItems)]) }}

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('stock_item_id') }}">{{ $errors->first('stock_item_id') }}</span>
    </div>
</div>

{{--base_item_id--}}
<div class="form-group {{ $errors->has('base_item_id') ? 'has-error' : '' }}">
    <label for="{{ fake_field('base_item_id') }}" class="col-md-4 control-label required">{{ __('Base Item') }}</label>
    <div class="col-md-8">
        {{ Form::select(fake_field('base_item_id'), $stockItems, old('base_item_id', null),['class' => 'form-control','id' => fake_field('base_item_id'), 'placeholder' => __('Select Base Item'), 'data-cval-name' => 'The base item field','data-cval-rules' => 'required|in:'.array_to_string($stockItems)]) }}

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('base_item_id') }}">{{ $errors->first('base_item_id') }}</span>
    </div>
</div>

{{--last_price--}}
<div class="form-group {{ $errors->has('last_price') ? 'has-error' : '' }}">
    <label for="{{ fake_field('last_price') }}" class="col-md-4 control-label required">{{ __('Last Price') }}</label>
    <div class="col-md-8">
        {{ Form::text(fake_field('last_price'),  old('last_price', null), ['class'=>'form-control', 'id' => fake_field('last_price'),'data-cval-name' => 'The last price field','data-cval-rules' => 'required|numeric|escapeInput|between:0.00000001, 99999999999.99999999', 'placeholder' => __('ex: 0.00150000')]) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('last_price') }}">{{ $errors->first('last_price') }}</span>
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

{{--is_default--}}
<div class="form-group {{ $errors->has('is_default') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_default') }}" class="col-md-4 control-label required">{{ __('Is Default') }}</label>
    <div class="col-md-8">
        <div class="cm-switch">
            {{ Form::radio(fake_field('is_default'), ACTIVE_STATUS_ACTIVE, null, ['id' => fake_field('is_default') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'Is default field', 'data-cval-rules' => 'required|integer|in:' . array_to_string(active_status())]) }}
            <label for="{{ fake_field('is_default') }}-active" class="cm-switch-label">{{ __('Yes') }}</label>

            {{ Form::radio(fake_field('is_default'), ACTIVE_STATUS_INACTIVE, true, ['id' => fake_field('is_default') . '-inactive', 'class' => 'cm-switch-input']) }}
            <label for="{{ fake_field('is_default') }}-inactive" class="cm-switch-label">{{ __('No') }}</label>
        </div>
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_default') }}">{{ $errors->first('is_default') }}</span>
    </div>
</div>

{{--submit button--}}
<div class="form-group">
    <div class="col-md-offset-4 col-md-8">
        {{ Form::submit(__('Create'),['class'=>'btn btn-success form-submission-button']) }}
    </div>
</div>