<input type="hidden" value="{{base_key()}}" name="base_key">
{{--user email status--}}
<div class="form-group {{ $errors->has('is_email_verified') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_email_verified') }}" class="col-md-3 control-label required">{{ __('Email Status') }}</label>
    <div class="col-md-9">
        {{ Form::select(fake_field('is_email_verified'), email_status(), $user->is_email_verified, ['class' => 'form-control','id' => fake_field('is_email_verified'),'placeholder' => __('Select Status'),'data-cval-name' => 'The email status field','data-cval-rules' => 'required|in:'.array_to_string(email_status())]) }}
        <span class="validation-message cval-error"
              data-cval-error="{{ fake_field('is_email_verified') }}">{{ $errors->first('is_email_verified') }}</span>
    </div>
</div>
{{--user active status--}}
<div class="form-group {{ $errors->has('is_active') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_active') }}" class="col-md-3 control-label required">{{ __('Account Status') }}</label>
    <div class="col-md-9">
        {{ Form::select(fake_field('is_active'), account_status(), $user->is_active, ['class' => 'form-control','id' => 'role','placeholder' => __('Select Status'),'data-cval-name' => 'The account status field','data-cval-rules' => 'required|in:'.array_to_string(account_status())]) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('is_active') }}">{{ $errors->first('is_active') }}</span>
    </div>
</div>
{{--user financial status--}}
<div class="form-group {{ $errors->has('is_financial_active') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_financial_active') }}" class="col-md-3 control-label required">{{ __('Financial Status') }}</label>
    <div class="col-md-9">
        {{ Form::select(fake_field('is_financial_active'), financial_status(), $user->is_financial_active, ['class' => 'form-control','id' => fake_field('is_financial_active'),'placeholder' => __('Select Status'),'data-cval-name' => 'The financial status field','data-cval-rules' => 'required|in:'.array_to_string(financial_status())]) }}
        <span class="validation-message cval-error"
              data-cval-error="{{ fake_field('is_financial_active') }}">{{ $errors->first('is_financial_active') }}</span>
    </div>
</div>
{{--user maintenance accessible status--}}
<div class="form-group {{ $errors->has('is_accessible_under_maintenance') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_accessible_under_maintenance') }}" class="col-md-3 control-label required">{{ __('Maintenace Access Status') }}</label>
    <div class="col-md-9">
        {{ Form::select(fake_field('is_accessible_under_maintenance'), maintenance_accessible_status(), $user->is_accessible_under_maintenance, ['class' => 'form-control','id' => fake_field('is_accessible_under_maintenance'),'placeholder' => __('Select Status'),'data-cval-name' => 'The maintenance access status field','data-cval-rules' => 'required|in:'.array_to_string(maintenance_accessible_status())]) }}
        <span class="validation-message cval-error"
              data-cval-error="{{ fake_field('is_accessible_under_maintenance') }}">{{ $errors->first('is_accessible_under_maintenance') }}</span>
    </div>
</div>
{{--submit buttn--}}
<div class="form-group">
    <div class="col-md-offset-3 col-md-9">
        {{ Form::submit(__('Update Status'),['class'=>'btn btn-info btn-sm btn-left btn-sm-block form-submission-button']) }}
        {{ Form::reset(__('Reset Status'),['class'=>'btn btn-warning btn-sm btn-left btn-sm-block']) }}
    </div>
</div>