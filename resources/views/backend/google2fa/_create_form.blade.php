<h4 class="text-warning">{{ __('Google Authentication is Disabled.') }}</h4>
<div class="row">
    <div class="col-md-6">
        <figure class="text-center">
            <img src="{{ $inlineUrl }}" alt="QR CODE" class="img-responsive cm-center">
            <figcaption>{{__('16-Digit Key')}}: <strong>{{ $secretKey }}</strong></figcaption>
        </figure>
        <p class="help-block small">
            {{ __('NOTE: This code changes each time you enable 2FA. If you disable 2FA this code will no longer be valid.') }}
        </p>
    </div>
    <div class="col-md-6">
        {!! Form::open(['route'=>['profile.google-2fa.store', $secretKey], 'class'=>'validator']) !!}
            <input type="hidden" name="base_key" value="{{ base_key() }}">
            @method('put')
            {{--email--}}
            <div class="form-group">
                <label class="control-label">{{ __('Email') }}</label>
                <p class="form-control-static">{{ $user->email }}</p>
            </div>

            {{--password--}}
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                <label for="{{ fake_field('password') }}" class="control-label">{{ __('Current Password') }}</label>
                {{ Form::password(fake_field('password'), ['class'=>'form-control', 'placeholder' => __('Enter current password'), 'id' => fake_field('password'),'data-cval-name' => 'The password','data-cval-rules' => 'required|escapeInput']) }}
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('password') }}">{{ $errors->first('password') }}</span>
            </div>

            {{--google app code--}}
            <div class="form-group {{ $errors->has('google_app_code') ? 'has-error' : '' }}">
                <label for="google_app_code" class="control-label">{{ __('Enter G2FA App Code') }}</label>
                {{ Form::text('google_app_code', null, ['class'=>'form-control', 'placeholder' => __('Enter G2FA App Code'), 'id' => 'google_app_code','data-cval-name' => 'The G2FA app code field','data-cval-rules' => 'required|escapeInput|integer']) }}
                <span class="validation-message cval-error" data-cval-error="google_app_code">{{ $errors->first('google_app_code') }}</span>
            </div>

            <p>
                {{ __('Before turning on 2FA, write down or print a copy of your 16-digit key and put it in a safe place. If your phone gets lost, stolen, or erased, you will need this key to get back into your account!') }}
            </p>

            {{--back_up--}}
            <div class="checkbox">
                <label>
                    {{ Form::checkbox('back_up', 1, false,['data-cval-rules' => 'required|in:1', 'data-cval-name' => 'Checking']) }} {{ __('I have backed up my 16-digit key.') }}
                </label>
                <span class="validation-message cval-error" data-cval-error="back_up">{{ $errors->first('back_up') }}</span>
            </div>

            {{--submit button--}}
            {{ Form::submit(__('Set Google Authentication'), ['class'=>'btn btn-success form-submission-button']) }}
        {!! Form::close() !!}
    </div>
</div>