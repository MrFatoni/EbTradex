<input type="hidden" name="base_key" value="{{base_key()}}">
    <div class="form-group {{ $errors->has('role_name') ? 'has-error' : '' }}">
        <div class="row">
        <label for="" class="col-md-3 control-label required">{{ __('Role Name') }}</label>
            <div class="col-md-9">
                {{ Form::text(fake_field('role_name'),old('role_name', isset($userRoleManagement) ? $userRoleManagement->role_name : null),['class'=>'form-control','data-cval-name' => 'The role name field','data-cval-rules' => 'required|escapeInput']) }}
                <span class="validation-message cval-error" data-cval-error="{{ fake_field('role_name') }}">{{ $errors->first('role_name') }}</span>
            </div>
        </div>
    </div>
<div class="form-group has-error">
    <span class="help-block">{{ $errors->first('roles') }}</span>
</div>
<?php
$ModuleClasses = [];
?>
@foreach($routes as $name => $routeGroups)
    <div class="route-group">
        <div class="checkbox checkbox-success checkbox-compact" style="margin-bottom: 20px !important; clear:both">
            {{ Form::checkbox("module",1,false,["class"=>"flat-red module module_$name","id"=>"role-$name", "data-id"=>"$name"]) }}
            <label class="disable-text-select" for="role-{{$name}}">{{ title_case(str_replace('_',' ',$name)) }}</label>
        </div>
        <div class="col-md-12">
            <div class="row dc-clear">
            <?php $allSubModules = true; ?>
            @foreach($routeGroups as $groupName=>$permissionLists)
                <div class="route-subgroup">
                    <div class="col-lg-3 col-md-12" style="margin-bottom: 20px !important;">
                    <div class="checkbox checkbox-success checkbox-compact">
                        {{ Form::checkbox("task",1,false,["class"=>"sub-module flat-red task module_action_$name module_action_{$name}_{$groupName}","id"=>"task-$name-$groupName", "data-id"=>"{$name}_$groupName"]) }}
                        <label class="disable-text-select" for="task-{{$name}}-{{$groupName}}">{{ title_case(str_replace('_',' ',$groupName)) }}</label>
                    </div>
                </div>
                    <div class="col-lg-9 col-md-12" style="margin-bottom:20px; border-bottom:1px solid #efefef; padding-bottom: 10px">
                    <div class="row dc-clear">
                        <?php $allItems = true; ?>
                        @foreach($permissionLists as $permissionName => $routeList)
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="checkbox checkbox-success checkbox-inline checkbox-compact">
                                {{ Form::checkbox("roles[$name][$groupName][]",$permissionName, isset($userRoleManagement->route_group[$name][$groupName]) ? (in_array($permissionName, $userRoleManagement->route_group[$name][$groupName]) ? true :false) : false ,["class"=>"route-item flat-red module_action_$name task_action_{$name}_$groupName", "id"=>"list-$name-$groupName-$permissionName"]) }}
                                <label class="disable-text-select" for="list-{{$name}}-{{$groupName}}-{{ $permissionName }}">{{ title_case(str_replace('_',' ',$permissionName)) }}</label>
                            </div>
                        </div>
                            <?php
                                if(!isset($userRoleManagement->route_group[$name][$groupName]) || !in_array($permissionName, $userRoleManagement->route_group[$name][$groupName])){
                                    $allSubModules = false;
                                    $allItems = false;
                                }
                            ?>
                        @endforeach
                            <?php
                                if($allItems){
                                    $ModuleClasses[] = "module_action_{$name}_{$groupName}";
                                }
                            ?>
                    </div>
                </div>
                </div>
            @endforeach
                <?php
                if($allSubModules){
                    $ModuleClasses[] = "module_$name";
                }
                ?>
            </div>
        </div>
    </div>
@endforeach
<div class="pull-right m-t-15">{{ Form::submit($buttonText,['class'=>'btn btn-success form-submission-button']) }}</div>

@section('extraScript')
    <script>
        (function($){
            var selecteModules = {!! json_encode($ModuleClasses) !!};
            for(var i=0; i < selecteModules.length; i++){
                $('.'+ selecteModules[i]).attr('checked', 'checked')
                console.log(selecteModules[i])
            }
        }(jQuery))

    </script>
@endsection