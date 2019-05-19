<input type="hidden" value="{{base_key()}}" name="base_key">
{{--user group field--}}
<div class="form-group {{ $errors->has('user_role_management_id') ? 'has-error' : '' }}">
    <label for="{{ fake_field('user_role_management_id') }}" class="col-md-3 control-label required">{{ __('User Role') }}</label>
    <div class="col-md-9">
        @if(!in_array($user->id, config('commonconfig.fixed_users')) && $user->id != Auth::user()->id)
            {{ Form::select(fake_field('user_role_management_id'), $userRoleManagements, old('user_role_management_id', $user->user_role_management_id),['class' => 'form-control','id' => fake_field('user_role_management_id'),'placeholder' => __('Select Role'),'data-cval-name' => 'The user role field','data-cval-rules' => 'required|in:'.array_to_string($userRoleManagements->toArray())]) }}
            <span class="validation-message cval-error" data-cval-error="{{ fake_field('user_role_management_id') }}">{{ $errors->first('user_role_management_id') }}</span>
        @else
            <p class="form-control">{{ $userRoleManagements[$user->user_role_management_id] }}</p>
        @endif
    </div>
</div>
{{--first name--}}
<div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
    <label for="{{ fake_field('first_name') }}" class="col-md-3 control-label required">{{ __('First Name') }}</label>
    <div class="col-md-9">
        {{ Form::text(fake_field('first_name'), old('first_name', $user->userInfo->first_name), ['class'=>'form-control', 'id' => fake_field('first_name'),'data-cval-name' => 'The first name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('first_name') }}">{{ $errors->first('first_name') }}</span>
    </div>
</div>
{{--last name--}}
<div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
    <label for="{{ fake_field('last_name') }}" class="col-md-3 control-label required testing">{{ __('Last Name') }}</label>
    <div class="col-md-9">
        {{ Form::text(fake_field('last_name'), old('last_name', $user->userInfo->last_name), ['class'=>'form-control', 'id' => fake_field('last_name'),'data-cval-name' => 'The last name field','data-cval-rules' => 'required|escapeInput|alphaSpace']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('last_name') }}">{{ $errors->first('last_name') }}</span>
    </div>
</div>
{{--email--}}
<div class="form-group">
    <label class="col-md-3 control-label required">{{ __('Email') }}</label>
    <div class="col-md-9">
        <p class="form-control">{{ $user->email }}</p>
    </div>
</div>
{{--username--}}
<div class="form-group">
    <label class="col-md-3 control-label required">{{ __('Username') }}</label>
    <div class="col-md-9">
        <p class="form-control">{{ $user->username }}</p>
    </div>
</div>
{{--address--}}
<div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
    <label for="{{ fake_field('address') }}" class="col-md-3 control-label">{{ __('Address') }}</label>
    <div class="col-md-9">
        {{ Form::textarea(fake_field('address'),  old('address', $user->userInfo->address), ['class'=>'form-control', 'id' => fake_field('address'), 'rows'=>2,'data-cval-name' => 'The address field','data-cval-rules' => 'escapeText']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('address') }}">{{ $errors->first('address') }}</span>
    </div>
</div>
{{--submit button--}}
<div class="form-group">
    <div class="col-md-offset-3 col-md-9">
        {{ Form::submit(__('Update Information'),['class'=>'btn btn-info btn-sm btn-left btn-sm-block form-submission-button']) }}
        {{ Form::reset(__('Reset Information'),['class'=>'btn btn-warning btn-sm btn-left btn-sm-block reset-button']) }}
    </div>
</div>