<h4 class="text-green">{{ __('Google Authentication is Enabled.') }}</h4>

{!! Form::open(['route'=>['profile.google-2fa.destroy'], 'class'=>'form-horizontal validator']) !!}
<input type="hidden" name="base_key" value="{{ base_key() }}">
@method('put')

<p>
    {{ __('If you want to turn off Google 2FA, input your account password and the six-digit code provided by the Google Authenticator app below, then submit.') }}
</p>

{{--password--}}
<div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
    <label for="{{ fake_field('password') }}"
           class="col-md-3 control-label required">{{ __('Current Password') }}</label>
    <div class="col-md-9">
        {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter current password'), 'id' => fake_field('password'),'data-cval-name' => 'The password','data-cval-rules' => 'required|escapeInput']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
    </div>
</div>


{{--google app code--}}
<div class="form-group {{ $errors->has('google_app_code') ? 'has-error' : '' }}">
    <label for="google_app_code" class="col-md-3 control-label required">{{ __('Enter G2FA App Code') }}</label>
    <div class="col-md-9">
        {{ Form::text('google_app_code', null, ['class'=>'form-control', 'placeholder' => __('Enter G2FA App Code'), 'id' => 'google_app_code','data-cval-name' => 'The G2FA app code field','data-cval-rules' => 'required|escapeInput|integer']) }}
        <span class="validation-message cval-error" data-cval-error="google_app_code">{{ $errors->first('google_app_code') }}</span>
        <p class="help-block">
            {{ __('IMPORTANT: When you disable 2FA, The 16 digit code will no longer be valid.') }}
        </p>
    </div>
</div>

{{--back_up--}}
<div class="form-group">
    <div class="col-md-offset-3 col-md-9">
        <div class="checkbox">
            <label>
                {{ Form::checkbox('back_up', 1, false,['data-cval-rules' => 'required|in:1', 'data-cval-name' => 'Checking']) }} {{ __('I understand.') }}
            </label>
            <span class="validation-message cval-error" data-cval-error="back_up">{{ $errors->first('back_up') }}</span>
        </div>
    </div>
</div>

{{--submit button--}}
<div class="form-group">
    <div class="col-md-offset-3 col-md-9">
        {{ Form::submit(__('Disable Google Authentication'), ['class'=>'btn btn-success form-submission-button']) }}
    </div>
</div>
{!! Form::close() !!}