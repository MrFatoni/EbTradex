<input type="hidden" name="base_key" value="{{ base_key() }}">
{{--title--}}
<div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
    <label for="{{ fake_field('title') }}" class="col-md-4 control-label required">{{ __('Title') }}</label>
    <div class="col-md-8">
        {{ Form::text(fake_field('title'),  old('title', isset($systemNotice) ? $systemNotice->title : null), ['class'=>'form-control', 'id' => fake_field('title'),'data-cval-name' => 'The title field','data-cval-rules' => 'required']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('title') }}">{{ $errors->first('title') }}</span>
    </div>
</div>
{{--description--}}
<div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
    <label for="{{ fake_field('description') }}" class="col-md-4 control-label required">{{ __('Description') }}</label>
    <div class="col-md-8">
        {{ Form::textarea(fake_field('description'),  old('description', isset($systemNotice) ? $systemNotice->description : null), ['class'=>'form-control', 'id' => fake_field('description'),'data-cval-name' => 'The description field','data-cval-rules' => 'required']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('description') }}">{{ $errors->first('description') }}</span>
    </div>
</div>
{{--type--}}
<div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
    <label for="{{ fake_field('type') }}" class="col-md-4 control-label required">{{ __('Type') }}</label>
    <div class="col-md-8">
        {{ Form::select(fake_field('type'), $types, old('type', isset($systemNotice) ? $systemNotice->type : null), ['class'=>'form-control', 'placeholder'=> __('Select type'), 'id' => fake_field('type'),'data-cval-name' => 'The type field','data-cval-rules' => 'required']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('type') }}">{{ $errors->first('type') }}</span>
    </div>
</div>
{{--Start Time--}}
<div class="form-group {{ $errors->has('start_at') ? 'has-error' : '' }}">
    <label for="start_time" class="col-md-4 control-label required">{{ __('Start Time') }}</label>
    <div class="col-md-8">
        {{ Form::text(fake_field('start_at'),  old('start_at', isset($systemNotice) ? $systemNotice->start_at : null), ['class'=>'form-control', 'id' => 'start_time','data-cval-name' => 'The start time field','data-cval-rules' => 'date']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('start_at') }}">{{ $errors->first('start_at') }}</span>
    </div>
</div>
{{--End Time--}}
<div class="form-group {{ $errors->has('end_at') ? 'has-error' : '' }}">
    <label for="end_time" class="col-md-4 control-label required">{{ __('End Time') }}</label>
    <div class="col-md-8">
        {{ Form::text(fake_field('end_at'),  old('end_at', isset($systemNotice) ? $systemNotice->end_at : null), ['class'=>'form-control', 'id' => 'end_time','data-cval-name' => 'The end time field','data-cval-rules' => 'data']) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('end_at') }}">{{ $errors->first('end_at') }}</span>
    </div>
</div>

{{--Stauts--}}
<div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
    <label for="{{ fake_field('status') }}" class="col-md-4 control-label required">{{ __('Status') }}</label>
    <div class="col-md-8">
        {{ Form::select(fake_field('status'), active_status(), old('status', isset($systemNotice) ? $systemNotice->status : null), ['class'=>'form-control', 'id' => fake_field('status'),'data-cval-name' => 'The status field','data-cval-rules' => 'required|in:'.array_to_string(active_status())]) }}
        <span class="validation-message cval-error" data-cval-error="{{ fake_field('status') }}">{{ $errors->first('status') }}</span>
    </div>
</div>

{{--submit buttn--}}
<div class="form-group">
    <div class="col-md-offset-4 col-md-8">
        {{ Form::submit($buttonText,['class'=>'btn btn-success form-submission-button']) }}
        {{ Form::reset(__('Reset'),['class'=>'btn btn-danger']) }}
    </div>
</div>