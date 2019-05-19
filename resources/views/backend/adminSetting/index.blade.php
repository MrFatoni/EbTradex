@extends('backend.layouts.main_layout')
@section('title', $title)
@section('content')
    <?php $title_name = ucwords(str_replace_last('_',' ',$adminSettingType)); ?>
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Admin Setting - {{$title_name}}</h3>
            <div class="box-tools pull-right">
                <a href="{{ route('admin-settings.edit',['admin_setting_type'=>$adminSettingType]) }}"
                   class="btn btn-primary back-button">{{__('Edit :settingName Setting',['settingName' =>$title_name])}}</a>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-4 col-md-3">
                    <ul class="nav nav-pills nav-stacked">
                        <?php $default = true; ?>
                        @foreach($settings['settingSections'] as $settingSection)
                            <?php
                                $current_route = is_current_route('admin-settings.index', 'active', ['admin_setting_type'=>$settingSection]);
                                if($default){
                                    $current_route = is_current_route('admin-settings.index', 'active', null,['admin_setting_type'=>$settingSection]);
                                }
                            ?>
                            <li class="{{$current_route}}">
                                <a href="{{route('admin-settings.index',['admin_setting_type'=>$settingSection])}}">{{ucwords(str_replace_last('_',' ',$settingSection))}}</a>
                            </li>
                            <?php $default = false; ?>
                        @endforeach
                    </ul>
                </div>
                <div class="col-sm-8 col-md-9">
                    <table class="table table-bordered">
                        {!! $settings['html'] !!}
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection