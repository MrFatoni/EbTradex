<input type="hidden" name="base_key" value="{{ base_key() }}">

{{--title--}}
<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
    <label for="{{ fake_field('title') }}" class="col-md-12 required">{{ __('Title') }}</label>
    <div class="col-md-12">
        {{ Form::text(fake_field('title'), old('title', null), ['class' => 'form-control', 'id' => fake_field('title'), 'data-cval-name' => 'The title field','data-cval-rules' => 'required|escapeInput']) }}

        <span class="validation-message cval-error"
              data-cval-error="{{ fake_field('title') }}">{{ $errors->first('title') }}</span>
    </div>
</div>

{{--content--}}
<div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
    <label for="content_textarea" class="col-md-12 required">{{ __('Content') }}</label>
    <div class="col-md-12">
        {{ Form::textarea(fake_field('content'), old('content', null), ['class' => 'form-control', 'id' => 'content_textarea']) }}

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('content') }}">{{ $errors->first('content') }}</span>
    </div>
</div>

{{--submit button--}}
<div class="form-group">
    <div class="col-md-12">
        {{ Form::submit($buttonText,['class'=>'btn btn-success form-submission-button']) }}
    </div>
</div>