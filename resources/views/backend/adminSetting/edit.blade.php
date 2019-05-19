@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <?php $title_name = ucwords(str_replace_last('_', ' ', $adminSettingType)); ?>
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Admin Setting - {{$title_name}}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('admin-settings.index',['admin_setting_type'=>$adminSettingType]) }}"
                   class="btn btn-primary back-button">{{__('View :settingName Setting',['settingName' =>$title_name])}}</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4 col-md-3">
                    <ul class="nav nav-pills nav-stacked">
                        @foreach($settings['settingSections'] as $settingSection)
                            <li class="{{is_current_route('admin-settings.edit', 'active', ['admin_setting_type'=>$settingSection])}}">
                                <a href="{{route('admin-settings.edit',['admin_setting_type'=>$settingSection])}}">{{ ucwords(str_replace_last('_',' ',$settingSection)) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-8 col-md-9">
                    {{ Form::open(['route'=>['admin-settings.update','admin_setting_type'=>$adminSettingType], 'method'=>'PUT','files'=> true]) }}
                    <table class="table table-bordered">
                        {!! $settings['html'] !!}
                        <tr>
                            <td colspan="2" class="text-right">
                                {{ Form::submit(__('Update :settingName Setting',['settingName' =>$title_name]),['class'=>'btn btn-success']) }}
                            </td>
                        </tr>
                    </table>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection