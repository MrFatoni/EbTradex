<input type="hidden" name="base_key" value="{{ base_key() }}">

{{--content--}}
<div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
    <div class="col-md-12">
        {{ Form::textarea(fake_field('content'), old('content', isset($post) ? $post->content : null), ['class' => 'form-control', 'id' => 'content_textarea','rows'=>3]) }}

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('content') }}">{{ $errors->first('content') }}</span>
    </div>
</div>

{{--submit button--}}
<div class="form-group">
    <div class="col-md-12">
        {{ Form::submit($buttonText,['class'=>'btn btn-success form-submission-button']) }}
    </div>
</div>