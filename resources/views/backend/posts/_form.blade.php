<input type="hidden" name="base_key" value="{{ base_key() }}">

{{--title--}}
<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
    <label for="{{ fake_field('title') }}" class="col-md-12 required">{{ __('Title') }}</label>
    <div class="col-md-12">
        {{ Form::text(fake_field('title'), old('title', isset($post) ? $post->title : null), ['class' => 'form-control', 'id' => fake_field('title'), 'data-cval-name' => 'The title field','data-cval-rules' => 'required|escapeInput']) }}

        <span class="validation-message cval-error"
              data-cval-error="{{ fake_field('title') }}">{{ $errors->first('title') }}</span>
    </div>
</div>

{{--content--}}
<div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
    <label for="content_textarea" class="col-md-12 required">{{ __('Content') }}</label>
    <div class="col-md-12">
        {{ Form::textarea(fake_field('content'), old('content', isset($post) ? $post->content : null), ['class' => 'form-control', 'id' => 'content_textarea']) }}

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('content') }}">{{ $errors->first('content') }}</span>
    </div>
</div>

{{--featured_image--}}
<div class="form-group {{ $errors->has('featured_image') ? 'has-error' : '' }}">
    <label for="{{ fake_field('featured_image') }}" class="col-md-12 required">{{ __('Featured Image') }}</label>
    <div class="col-md-12">
        <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-new thumbnail">
                @if(isset($post))
                    <img src="{{ get_post_image($post->featured_image) }}" alt="{{ __('Featured Image') }}">
                @else
                    <i class="fa fa-image"></i>
                @endif
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail"></div>
            <div>
                <span class="btn btn-default btn-file">
                    <span class="fileinput-new">{{ __('Select') }}</span>
                    <span class="fileinput-exists">{{ __('Change') }}</span>
                    {{ Form::file(fake_field('featured_image'), ['data-cval-name' => 'The featured image field','data-cval-rules' => 'files:jpg,png,jpeg|max:512']) }}
                </span>
                <a href="javascript:;" class="btn btn-default fileinput-exists" data-dismiss="fileinput">{{ __('Remove') }}</a>
            </div>
        </div>
        <p class="help-block">{{ __('Upload item emoji 400X400 and less than or equal 512KB.') }}</p>

        <span class="validation-message cval-error" data-cval-error="{{ fake_field('featured_image') }}">{{ $errors->first('featured_image') }}</span>
    </div>
</div>

{{--is_published--}}
<div class="form-group {{ $errors->has('is_published') ? 'has-error' : '' }}">
    <label for="{{ fake_field('is_published') }}" class="col-md-12 required">{{ __('Status') }}</label>
    <div class="col-md-12">
        <div class="cm-switch">
            {{ Form::radio(fake_field('is_published'), ACTIVE_STATUS_ACTIVE, isset($post) ? $post->is_published : true, ['id' => fake_field('is_published') . '-active', 'class' => 'cm-switch-input', 'data-cval-name' => 'The active status field', 'data-cval-rules' => 'required|integer|in:' . array_to_string(active_status())]) }}
            <label for="{{ fake_field('is_published') }}-active" class="cm-switch-label">{{ __('Publish') }}</label>

            {{ Form::radio(fake_field('is_published'), ACTIVE_STATUS_INACTIVE, isset($post) ? ($post->is_published ? false : true) : false, ['id' => fake_field('is_published') . '-inactive', 'class' => 'cm-switch-input']) }}
            <label for="{{ fake_field('is_published') }}-inactive" class="cm-switch-label">{{ __('Draft') }}</label>
        </div>
        <span class="validation-message cval-error"
              data-cval-error="{{ fake_field('is_published') }}">{{ $errors->first('is_publish') }}</span>
    </div>
</div>

{{--submit button--}}
<div class="form-group">
    <div class="col-md-12">
        {{ Form::submit($buttonText,['class'=>'btn btn-success form-submission-button']) }}
    </div>
</div>